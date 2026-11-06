/**
 * ParentingHub SmartCart AI — Client Assistant & Recommendation Engine
 * Integrates directly with existing app.js cart, catalog & modal systems.
 */

(function () {
  'use strict';

  // Config & State
  const API_BASE = '/api/ai';
  const STORAGE_KEY = 'ph_smartcart_session_v1';
  let isOpen = false;
  let isLoading = false;
  let conversation = [];

  function getClientSessionId() {
    let sid = sessionStorage.getItem('ph_smartcart_client_sid');
    if (!sid) {
      sid = 'ph_sid_' + Math.random().toString(36).substring(2) + Date.now().toString(36);
      try {
        sessionStorage.setItem('ph_smartcart_client_sid', sid);
      } catch (e) {}
    }
    return sid;
  }

  // Helper: Format Money in PKR
  function formatPKR(amount) {
    return 'PKR ' + Number(amount || 0).toLocaleString('en-PK', { maximumFractionDigits: 0 });
  }

  // Helper: HTML escape
  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Load session conversation
  function loadHistory() {
    try {
      const saved = sessionStorage.getItem(STORAGE_KEY);
      if (saved) {
        conversation = JSON.parse(saved);
      }
    } catch (e) {
      conversation = [];
    }
  }

  // Save session conversation
  function saveHistory() {
    try {
      sessionStorage.setItem(STORAGE_KEY, JSON.stringify(conversation.slice(-20)));
    } catch (e) {}
  }

  // 1. Inject DOM Elements
  function injectInterface() {
    if (document.getElementById('smartcart-ai-root')) return;

    const root = document.createElement('div');
    root.id = 'smartcart-ai-root';
    root.innerHTML = `
      <!-- Floating Action Button -->
      <div id="smartcart-ai-fab" role="button" tabindex="0" aria-label="Open ParentingHub SmartCart AI Shopping Assistant" title="Open AI Shopping Assistant">
        <div class="sc-fab-icon">✨</div>
        <div class="sc-fab-label">
          <span class="sc-fab-title">SmartCart AI</span>
          <span class="sc-fab-sub">Parenting Advisor</span>
        </div>
      </div>

      <!-- Main Assistant Drawer / Modal -->
      <div id="smartcart-ai-modal" role="dialog" aria-modal="true" aria-labelledby="sc-title">
        <div class="sc-header">
          <div class="sc-header-brand">
            <div class="sc-header-avatar">🍼</div>
            <div class="sc-header-text">
              <h3 id="sc-title">SmartCart AI <span class="sc-header-badge">Advisor</span></h3>
              <p class="sc-header-sub">ParentingHub Pakistan Verified Essentials</p>
            </div>
          </div>
          <div class="sc-header-actions">
            <button class="sc-btn-icon" id="sc-clear-btn" title="Clear Conversation">🔄</button>
            <button class="sc-btn-icon" id="sc-close-btn" title="Close Assistant">✕</button>
          </div>
        </div>

        <!-- Chat Thread -->
        <div class="sc-thread" id="sc-thread"></div>

        <!-- Footer & Input -->
        <div class="sc-footer">
          <form class="sc-input-form" id="sc-form">
            <input 
              type="text" 
              class="sc-input-field" 
              id="sc-input" 
              placeholder="Ask for baby bottles, strollers, weaning gear..." 
              autocomplete="off" 
              maxlength="400"
            />
            <button type="submit" class="sc-send-btn" id="sc-send-btn" title="Send Question">➤</button>
          </form>
          <div class="sc-disclaimer-text">
            Standard delivery fee is PKR 200 nationwide. Medical concerns should be referred to a pediatrician.
          </div>
        </div>
      </div>

      <!-- Product Comparison Overlay -->
      <div id="smartcart-comparison-overlay">
        <div class="sc-compare-modal">
          <div class="sc-compare-header">
            <h3>Factual Product Comparison</h3>
            <button class="sc-btn-icon" style="color:var(--ph-ink);background:var(--ph-line)" id="sc-compare-close">✕</button>
          </div>
          <div class="sc-compare-body" id="sc-compare-content">
            <!-- Populated dynamically -->
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(root);
    bindEvents();
    loadHistory();

    if (conversation.length === 0) {
      renderWelcome();
    } else {
      renderThread();
    }
  }

  // 2. Event Binding
  function bindEvents() {
    const fab = document.getElementById('smartcart-ai-fab');
    const closeBtn = document.getElementById('sc-close-btn');
    const clearBtn = document.getElementById('sc-clear-btn');
    const form = document.getElementById('sc-form');
    const compareClose = document.getElementById('sc-compare-close');
    const compareOverlay = document.getElementById('smartcart-comparison-overlay');

    fab.addEventListener('click', () => toggleChat());
    fab.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        toggleChat();
      }
    });

    closeBtn.addEventListener('click', () => toggleChat(false));
    clearBtn.addEventListener('click', () => {
      conversation = [];
      saveHistory();
      renderWelcome();
    });

    // Keyboard support: Escape closes assistant or comparison overlay
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const compareOverlay = document.getElementById('smartcart-comparison-overlay');
        if (compareOverlay && compareOverlay.classList.contains('open')) {
          compareOverlay.classList.remove('open');
          return;
        }
        if (isOpen) {
          toggleChat(false);
        }
      }
    });

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      handleSend();
    });

    compareClose.addEventListener('click', () => {
      compareOverlay.classList.remove('open');
    });

    compareOverlay.addEventListener('click', (e) => {
      if (e.target === compareOverlay) {
        compareOverlay.classList.remove('open');
      }
    });
  }

  function toggleChat(forceState) {
    const modal = document.getElementById('smartcart-ai-modal');
    isOpen = forceState !== undefined ? forceState : !isOpen;
    if (isOpen) {
      modal.classList.add('open');
      document.getElementById('sc-input').focus();
      scrollToBottom();
    } else {
      modal.classList.remove('open');
    }
  }

  function scrollToBottom() {
    const thread = document.getElementById('sc-thread');
    if (thread) {
      thread.scrollTop = thread.scrollHeight;
    }
  }

  // 3. Render Welcome Message
  function renderWelcome() {
    const thread = document.getElementById('sc-thread');
    if (!thread) return;

    thread.innerHTML = `
      <div class="sc-msg bot">
        <div class="sc-bubble">
          👋 <b>Assalam-o-Alaikum! Welcome to ParentingHub SmartCart AI.</b><br>
          I can help you discover genuine baby essentials, match products to your baby's age, compare gear, and prepare shopping carts within your budget.
          
          <div style="margin-top:14px;font-weight:700;font-size:12px;color:var(--ph-ink-2, #64748B);text-transform:uppercase;letter-spacing:0.5px">Suggested Inquiries:</div>
          <div class="sc-prompt-pills">
            <button class="sc-pill" data-query="Recommend anti-colic feeding bottles under Rs 6,000">
              🍼 Anti-colic bottles under Rs 6,000
            </button>
            <button class="sc-pill" data-query="Compare Philips Avent vs Tommee Tippee feeding bottles">
              ⚖️ Compare Avent vs Tommee Tippee
            </button>
            <button class="sc-pill" data-query="Build a newborn starter pack under Rs 15,000">
              🛍️ Newborn Starter Pack under Rs 15,000
            </button>
            <button class="sc-pill" data-query="Recommend training cups for 6 months baby">
              🥣 Weaning cups for 6 months
            </button>
            <button class="sc-pill" data-query="What feeding products do I need for a newborn?">
              👶 Newborn Feeding Essentials
            </button>
          </div>
        </div>
        <div class="sc-msg-meta">ParentingHub AI • Verified Store Data • Fixed Rate 280 PKR/USD</div>
      </div>
    `;

    // Bind prompt pills
    thread.querySelectorAll('.sc-pill').forEach(btn => {
      btn.addEventListener('click', () => {
        const query = btn.getAttribute('data-query');
        document.getElementById('sc-input').value = query;
        handleSend();
      });
    });
  }

  // 4. Render Conversation Thread
  function renderThread() {
    const thread = document.getElementById('sc-thread');
    if (!thread) return;

    thread.innerHTML = '';
    conversation.forEach(msg => {
      if (msg.role === 'user') {
        appendUserMessageToDOM(msg.content);
      } else {
        appendBotMessageToDOM(msg);
      }
    });
    scrollToBottom();
  }

  // 5. Send User Message & Call API
  async function handleSend() {
    const input = document.getElementById('sc-input');
    const sendBtn = document.getElementById('sc-send-btn');
    const text = input.value.trim();
    if (!text || isLoading) return;

    input.value = '';
    isLoading = true;
    sendBtn.disabled = true;

    // Append User Message
    conversation.push({ role: 'user', content: text });
    appendUserMessageToDOM(text);
    saveHistory();
    scrollToBottom();

    // Show Typing Skeleton
    const typingId = 'sc-typing-' + Date.now();
    const thread = document.getElementById('sc-thread');
    const typingEl = document.createElement('div');
    typingEl.id = typingId;
    typingEl.className = 'sc-msg bot';
    typingEl.innerHTML = `
      <div class="sc-bubble">
        <div class="sc-typing-dots">
          <span class="sc-dot"></span>
          <span class="sc-dot"></span>
          <span class="sc-dot"></span>
        </div>
        <span style="font-size:12px;color:var(--ph-ink-muted);margin-left:6px">Analyzing catalog...</span>
      </div>
    `;
    thread.appendChild(typingEl);
    scrollToBottom();

    try {
      // Check if message is a direct bundle request
      const isBundleRequest = /bundle|starter pack|starter cart|build a cart/i.test(text);

      const endpoint = isBundleRequest ? `${API_BASE}/recommend` : `${API_BASE}/assistant`;
      const payload = isBundleRequest 
        ? { requirement: text }
        : {
            message: text,
            conversationHistory: conversation.slice(-6).map(m => ({ role: m.role, content: m.content }))
          };

      const res = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json().catch(() => null);
      const typingNode = document.getElementById(typingId);
      if (typingNode) typingNode.remove();

      if (!res.ok || !data || !data.success) {
        throw new Error((data && data.message) || 'Could not connect to ParentingHub AI. Please retry.');
      }

      const botMessage = {
        role: 'assistant',
        reply: data.reply || (data.count ? `Found ${data.count} matching products in the catalog:` : 'Here are the matching verified essentials:'),
        stage: data.stage,
        isFallback: data.isFallback,
        recommendations: data.recommendations || [],
        suggestedActions: data.suggestedActions || []
      };

      conversation.push(botMessage);
      appendBotMessageToDOM(botMessage);
      saveHistory();

    } catch (err) {
      const typingNode = document.getElementById(typingId);
      if (typingNode) typingNode.remove();

      appendBotMessageToDOM({
        role: 'assistant',
        reply: `⚠️ ${escapeHtml(err.message || 'An error occurred.')} Please try asking again.`,
        recommendations: []
      });
    } finally {
      isLoading = false;
      sendBtn.disabled = false;
      scrollToBottom();
    }
  }

  function appendUserMessageToDOM(text) {
    const thread = document.getElementById('sc-thread');
    const msg = document.createElement('div');
    msg.className = 'sc-msg user';
    msg.innerHTML = `
      <div class="sc-bubble">${escapeHtml(text)}</div>
      <div class="sc-msg-meta">You</div>
    `;
    thread.appendChild(msg);
  }

  function appendBotMessageToDOM(msg) {
    const thread = document.getElementById('sc-thread');
    const msgEl = document.createElement('div');
    msgEl.className = 'sc-msg bot';

    let cardsHtml = '';
    if (msg.recommendations && msg.recommendations.length > 0) {
      cardsHtml = `
        <div class="sc-cards-container">
          ${msg.recommendations.map(p => `
            <div class="sc-card" data-product-id="${escapeHtml(p.id)}">
              <div class="sc-card-img-wrap">
                <span class="sc-card-brand-badge">${escapeHtml(p.brand)}</span>
                <img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}" class="sc-card-img" loading="lazy" onerror="this.onerror=null;this.src='/assets/products/image-verification-pending.webp';" />
              </div>
              <div class="sc-card-title" title="${escapeHtml(p.name)}">${escapeHtml(p.name)}</div>
              <div class="sc-card-price-row">
                <span class="sc-card-price">${formatPKR(p.price)}</span>
                ${p.compareAt ? `<span class="sc-card-old-price">${formatPKR(p.compareAt)}</span>` : ''}
              </div>
              ${p.reason ? `<div class="sc-card-reason">💡 ${escapeHtml(p.reason)}</div>` : ''}
              <div class="sc-card-actions">
                <a href="${escapeHtml(p.url)}" class="sc-btn-sm sc-btn-primary" target="_blank" title="View details">
                  View Details
                </a>
                <button class="sc-btn-sm sc-btn-secondary sc-add-cart-btn" data-id="${escapeHtml(p.id)}" data-name="${escapeHtml(p.name)}" disabled title="Cart proposals activate in Stage 3">
                  + Add to Bag (Stage 3)
                </button>
              </div>
            </div>
          `).join('')}
        </div>
      `;
    }

    let actionsHtml = '';
    if (msg.suggestedActions && msg.suggestedActions.length > 0) {
      actionsHtml = `
        <div class="sc-prompt-pills" style="margin-top:8px">
          ${msg.suggestedActions.map(act => `
            <button class="sc-pill sc-action-pill" data-action="${escapeHtml(act.action)}" data-ids='${JSON.stringify(act.productIds || [])}'>
              ${escapeHtml(act.label)}
            </button>
          `).join('')}
        </div>
      `;
    }

    const badgeLabel = msg.isFallback ? 'Catalog Engine' : 'Gemini AI';
    msgEl.innerHTML = `
      <div class="sc-bubble">
        <div>${escapeHtml(msg.reply)}</div>
        ${cardsHtml}
        ${actionsHtml}
      </div>
      <div class="sc-msg-meta">
        <span>SmartCart • ${badgeLabel}</span>
        ${msg.stage ? `<span>• ${escapeHtml(msg.stage)}</span>` : ''}
      </div>
    `;

    thread.appendChild(msgEl);
    bindCardActions(msgEl);
  }

  // 6. Bind Card Buttons (Add to Bag, Compare)
  function bindCardActions(container) {
    // Add to Cart Buttons
    // STAGE 2 SAFETY: Stage 2 is strictly read-only for product discovery and comparison.
    // Direct cart mutation (window.addToCart, localStorage, sessions, DB) is disabled in Stage 2.
    // Full cart proposal workflows activate in Stage 3.
    container.querySelectorAll('.sc-add-cart-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const pid = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        
        btn.setAttribute('disabled', 'disabled');
        btn.textContent = 'Stage 2 Read-Only';
        
        if (typeof window.toast === 'function') {
          window.toast('Stage 2 is read-only. Cart proposals activate in Stage 3.');
        } else {
          console.info('[SmartCart Stage 2] Cart actions are read-only; no state changed.');
        }
      });
    });

    // Action Pills
    container.querySelectorAll('.sc-action-pill').forEach(btn => {
      btn.addEventListener('click', async () => {
        const action = btn.getAttribute('data-action');
        const ids = JSON.parse(btn.getAttribute('data-ids') || '[]');

        if (action === 'compare' && ids.length >= 2) {
          openComparisonModal(ids);
        } else if (action === 'propose_cart' && ids.length > 0) {
          triggerCartProposal(ids);
        } else if (action === 'view' && ids[0]) {
          window.open('/product/' + encodeURIComponent(ids[0]), '_blank');
        }
      });
    });
  }

  // 7. Smart Cart Proposal Action
  async function triggerCartProposal(productIds) {
    const thread = document.getElementById('sc-thread');
    const loadingMsg = document.createElement('div');
    loadingMsg.className = 'sc-msg bot';
    loadingMsg.innerHTML = `<div class="sc-bubble">⏳ Preparing customized cart bundle for review...</div>`;
    thread.appendChild(loadingMsg);
    scrollToBottom();

    try {
      const res = await fetch(`${API_BASE}/cart/propose`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-Session-ID': getClientSessionId()
        },
        body: JSON.stringify({ productIds })
      });
      const data = await res.json().catch(() => null);
      loadingMsg.remove();

      if (!res.ok || !data || !data.success) {
        throw new Error((data && data.message) || 'Could not prepare proposed cart bundle.');
      }

      // Render Proposal Review Card with interactive checkboxes & quantity controls
      const proposalEl = document.createElement('div');
      proposalEl.className = 'sc-msg bot';
      proposalEl.innerHTML = `
        <div class="sc-bubble" style="background:#FFFFFF">
          <div class="sc-proposal-box">
            <div class="sc-proposal-header">
              <span class="sc-proposal-title">🛍️ Proposed SmartCart Bundle</span>
              <span style="font-size:11px;color:var(--ph-ink-muted)">Ticket #${escapeHtml(data.proposalId)}</span>
            </div>
            <div style="font-size:11px;color:var(--ph-ink-muted);margin:2px 0 6px">
              ⏱️ <strong>Valid for 30 minutes</strong> • Server-verified prices & stock
            </div>
            <p style="font-size:12px;color:var(--ph-ink-muted);margin:0 0 8px">
              ${escapeHtml(data.approvalNote)}
            </p>
            <div class="sc-proposal-items-list" id="prop-items-${escapeHtml(data.proposalId)}">
              ${data.items.map(item => `
                <div class="sc-proposal-item" data-item-id="${escapeHtml(item.id)}">
                  <input type="checkbox" checked class="sc-prop-check" data-id="${escapeHtml(item.id)}" data-price="${item.price}" aria-label="Include ${escapeHtml(item.name)} in bundle" />
                  <img src="${escapeHtml((item.product && item.product.image) || item.image || '/assets/products/image-verification-pending.webp')}" alt="${escapeHtml(item.name)}" onerror="this.onerror=null;this.src='/assets/products/image-verification-pending.webp';" />
                  <div class="sc-proposal-item-details">
                    <div style="font-size:10.5px;color:var(--ph-blue-dark);font-weight:700;text-transform:uppercase;margin-bottom:2px">${escapeHtml((item.product && item.product.brand) || item.brand || 'ParentingHub')}</div>
                    <div style="font-weight:700">${escapeHtml(item.name)}</div>
                    <div style="color:var(--ph-ink-muted);font-size:11px">Unit: ${formatPKR(item.price)}</div>
                    <div class="sc-prop-qty-ctrl">
                      <button type="button" class="sc-prop-qty-btn sc-qty-dec" data-id="${escapeHtml(item.id)}" aria-label="Decrease quantity for ${escapeHtml(item.name)}">−</button>
                      <span class="sc-prop-qty-val" id="qty-val-${escapeHtml(item.id)}">${item.qty}</span>
                      <button type="button" class="sc-prop-qty-btn sc-qty-inc" data-id="${escapeHtml(item.id)}" aria-label="Increase quantity for ${escapeHtml(item.name)}">+</button>
                    </div>
                  </div>
                  <div style="font-weight:800;color:var(--ph-rose)" class="sc-item-linetotal" id="linetot-${escapeHtml(item.id)}">${formatPKR(item.lineTotal)}</div>
                </div>
              `).join('')}
            </div>
            <div class="sc-proposal-totals">
              <div class="sc-totals-row">
                <span>Subtotal</span>
                <span id="prop-subtot-${escapeHtml(data.proposalId)}">${formatPKR(data.subtotal)}</span>
              </div>
              <div class="sc-totals-row">
                <span>Nationwide Courier Delivery</span>
                <span id="prop-ship-${escapeHtml(data.proposalId)}">${formatPKR(data.shipping)}</span>
              </div>
              <div class="sc-totals-row final">
                <span>Total (PKR & USD @ 280)</span>
                <span id="prop-tot-${escapeHtml(data.proposalId)}">${formatPKR(data.total)} <span style="font-size:11px;color:var(--ph-blue-dark);font-weight:700">($${(data.total / 280).toFixed(2)} USD)</span></span>
              </div>
            </div>
            <button class="sc-btn-sm sc-btn-primary sc-approve-proposal-btn" data-proposal-id="${escapeHtml(data.proposalId)}" style="width:100%;margin-top:12px;padding:10px">
              ✓ Approve & Add ${data.itemCount} Items to Bag
            </button>
            <div id="prop-action-area-${escapeHtml(data.proposalId)}"></div>
          </div>
        </div>
        <div class="sc-msg-meta">Customer Permission Required • Server Price Verified</div>
      `;

      thread.appendChild(proposalEl);
      scrollToBottom();

      // State tracking for this proposal card
      const proposalItemsState = {};
      data.items.forEach(it => {
        proposalItemsState[it.id] = {
          id: it.id,
          name: it.name,
          price: Number(it.price || 0),
          qty: Number(it.qty || 1),
          checked: true
        };
      });

      const updateProposalTotals = () => {
        let sub = 0;
        let count = 0;
        Object.values(proposalItemsState).forEach(st => {
          if (st.checked) {
            sub += st.price * st.qty;
            count += st.qty;
          }
        });
        const ship = count > 0 ? 200 : 0;
        const tot = sub + ship;

        const subEl = document.getElementById(`prop-subtot-${data.proposalId}`);
        const shipEl = document.getElementById(`prop-ship-${data.proposalId}`);
        const totEl = document.getElementById(`prop-tot-${data.proposalId}`);
        const appBtn = proposalEl.querySelector('.sc-approve-proposal-btn');

        if (subEl) subEl.textContent = formatPKR(sub);
        if (shipEl) shipEl.textContent = formatPKR(ship);
        if (totEl) totEl.innerHTML = `${formatPKR(tot)} <span style="font-size:11px;color:var(--ph-blue-dark);font-weight:700">($${(tot / 280).toFixed(2)} USD)</span>`;

        if (appBtn && !appBtn.disabled) {
          appBtn.textContent = count > 0 
            ? `✓ Approve & Add ${count} Items to Bag (${formatPKR(tot)})`
            : 'Select items to approve';
          appBtn.style.opacity = count > 0 ? '1' : '0.5';
          appBtn.style.pointerEvents = count > 0 ? 'auto' : 'none';
        }
      };

      // Bind Checkbox events
      proposalEl.querySelectorAll('.sc-prop-check').forEach(chk => {
        chk.addEventListener('change', () => {
          const pid = chk.getAttribute('data-id');
          if (proposalItemsState[pid]) {
            proposalItemsState[pid].checked = chk.checked;
            updateProposalTotals();
          }
        });
      });

      // Bind Quantity [+] and [-]
      proposalEl.querySelectorAll('.sc-qty-inc').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const pid = btn.getAttribute('data-id');
          if (proposalItemsState[pid] && proposalItemsState[pid].qty < 99) {
            proposalItemsState[pid].qty++;
            const valEl = document.getElementById(`qty-val-${pid}`);
            const lineTotEl = document.getElementById(`linetot-${pid}`);
            if (valEl) valEl.textContent = proposalItemsState[pid].qty;
            if (lineTotEl) lineTotEl.textContent = formatPKR(proposalItemsState[pid].price * proposalItemsState[pid].qty);
            updateProposalTotals();
          }
        });
      });

      proposalEl.querySelectorAll('.sc-qty-dec').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const pid = btn.getAttribute('data-id');
          if (proposalItemsState[pid] && proposalItemsState[pid].qty > 1) {
            proposalItemsState[pid].qty--;
            const valEl = document.getElementById(`qty-val-${pid}`);
            const lineTotEl = document.getElementById(`linetot-${pid}`);
            if (valEl) valEl.textContent = proposalItemsState[pid].qty;
            if (lineTotEl) lineTotEl.textContent = formatPKR(proposalItemsState[pid].price * proposalItemsState[pid].qty);
            updateProposalTotals();
          }
        });
      });

      // Bind Approval Button
      const approveBtn = proposalEl.querySelector('.sc-approve-proposal-btn');
      approveBtn.addEventListener('click', async () => {
        const approvedItemsList = Object.values(proposalItemsState)
          .filter(st => st.checked && st.qty > 0)
          .map(st => ({ id: st.id, qty: st.qty }));

        if (approvedItemsList.length === 0) {
          alert('Please select at least one product to add.');
          return;
        }

        approveBtn.disabled = true;
        approveBtn.textContent = 'Verifying with server...';

        try {
          const appRes = await fetch(`${API_BASE}/cart/approve`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-Session-ID': getClientSessionId()
            },
            body: JSON.stringify({
              proposalId: data.proposalId,
              approvedItems: approvedItemsList,
              idempotentRecovery: true
            })
          });
          const appData = await appRes.json().catch(() => null);

          if (!appRes.ok || !appData || !appData.success) {
            throw new Error((appData && appData.message) || 'Approval failed.');
          }

          // Idempotent Cart Application Guard
          // Prevents duplicate additions if user retries or reloads after approval
          const appliedKey = 'ph_applied_proposals';
          let appliedMap = {};
          try {
            appliedMap = JSON.parse(localStorage.getItem(appliedKey) || '{}');
          } catch (e) {
            appliedMap = {};
          }

          if (!appliedMap[data.proposalId]) {
            if (appData.approvedItems && typeof window.addToCart === 'function') {
              appData.approvedItems.forEach(it => {
                window.addToCart(it.id, it.qty, true, it.option || '');
              });
              if (typeof window.updateCart === 'function') {
                window.updateCart();
              }
            }
            appliedMap[data.proposalId] = Date.now();
            try {
              localStorage.setItem(appliedKey, JSON.stringify(appliedMap));
            } catch (e) {}
          }

          approveBtn.textContent = '✓ Approved & In Your Shopping Bag!';
          approveBtn.style.background = '#2F9E5B';

          // Render direct Proceed to Checkout button
          const actionArea = document.getElementById(`prop-action-area-${data.proposalId}`);
          if (actionArea) {
            actionArea.innerHTML = `
              <a href="/checkout" class="sc-proceed-checkout-btn" style="display:block;text-align:center;text-decoration:none" id="sc-checkout-btn-${escapeHtml(data.proposalId)}">
                Proceed to Checkout (${formatPKR(appData.total)}) →
              </a>
            `;
            const coBtn = document.getElementById(`sc-checkout-btn-${data.proposalId}`);
            if (coBtn) {
              coBtn.addEventListener('click', (e) => {
                e.preventDefault();
                toggleChat(false);
                window.location.href = '/checkout';
              });
            }
          }

        } catch (appErr) {
          alert('Error: ' + appErr.message);
          approveBtn.disabled = false;
          approveBtn.textContent = 'Approve & Add to Bag';
        }
      });

    } catch (err) {
      alert('Could not propose bundle: ' + err.message);
    }
  }

  // 8. Factual Product Comparison Modal
  async function openComparisonModal(productIds) {
    const overlay = document.getElementById('smartcart-comparison-overlay');
    const content = document.getElementById('sc-compare-content');
    content.innerHTML = `<div style="text-align:center;padding:40px">Analyzing specifications from database...</div>`;
    overlay.classList.add('open');

    try {
      const res = await fetch(`${API_BASE}/compare`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ productIds })
      });
      const data = await res.json().catch(() => null);

      if (!res.ok || !data || !data.success) {
        throw new Error((data && data.message) || 'Comparison failed.');
      }

      const products = data.products || [];
      const comp = data.comparison || {};
      const verdict = data.verdict || {};

      content.innerHTML = `
        <div class="sc-compare-grid">
          ${products.map(p => `
            <div class="sc-compare-col">
              <img src="${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}" onerror="this.onerror=null;this.src='/assets/products/image-verification-pending.webp';" />
              <div style="font-weight:800;font-size:14px;color:var(--ph-ink);height:36px;overflow:hidden">${escapeHtml(p.name)}</div>
              <div style="font-weight:800;font-size:16px;color:var(--ph-rose);margin:6px 0">${formatPKR(p.price)}</div>
              <div style="font-size:11px;color:var(--ph-blue);font-weight:700">Brand: ${escapeHtml(p.brand)}</div>
              
              <div class="sc-compare-specs">
                <div class="sc-spec-row">
                  <span class="sc-spec-label">Category:</span>
                  <span class="sc-spec-value">${escapeHtml(p.category)}</span>
                </div>
                <div class="sc-spec-row">
                  <span class="sc-spec-label">Rating:</span>
                  <span class="sc-spec-value">${p.rating > 0 ? p.rating + '★' : 'New'}</span>
                </div>
                <div class="sc-spec-row">
                  <span class="sc-spec-label">Availability:</span>
                  <span class="sc-spec-value" style="color:#2F9E5B">${p.inStock ? 'In Stock' : 'Out of Stock'}</span>
                </div>
                <div class="sc-spec-row">
                  <span class="sc-spec-label">Options:</span>
                  <span class="sc-spec-value">${p.hasOptions ? p.options.length + ' variants' : 'Standard'}</span>
                </div>
              </div>

              <button class="sc-btn-sm sc-btn-primary sc-add-cart-btn" data-id="${escapeHtml(p.id)}" style="margin-top:14px;width:100%">
                + Add This to Bag
              </button>
            </div>
          `).join('')}
        </div>

        <div class="sc-verdict-box">
          <div style="font-weight:800;margin-bottom:4px;color:var(--ph-blue-dark)">💡 Factual Comparison Summary:</div>
          <div>${escapeHtml(verdict.summary || 'Both products are verified genuine and available with standard PKR 200 delivery.')}</div>
          ${comp.priceDifference > 0 ? `<div style="margin-top:6px;font-weight:700">Price difference: ${formatPKR(comp.priceDifference)}</div>` : ''}
        </div>
      `;

      bindCardActions(content);

    } catch (err) {
      content.innerHTML = `<div style="color:#dc2626;padding:30px;text-align:center">${escapeHtml(err.message)}</div>`;
    }
  }

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', injectInterface);
  } else {
    injectInterface();
  }

  // Expose API for existing buttons on product and shop pages
  window.SmartCartAI = {
    open: () => toggleChat(true),
    close: () => toggleChat(false),
    ask: (q) => {
      toggleChat(true);
      document.getElementById('sc-input').value = q;
      handleSend();
    },
    compare: (ids) => openComparisonModal(ids)
  };

})();

