/**
 * ParentingHub.pk — AI Pediatric & Milestone Chatbot
 * Modern floating AI Chat Assistant with Email Activation
 * Powered by n8n Webhook & Gemini Pediatric Engine
 */

(function () {
  'use strict';

  // Check if already injected
  if (document.getElementById('phAiChatWidget')) return;

  const styles = `
    /* Floating Chat Bubble Button */
    .ph-chat-bubble-btn {
      position: fixed;
      bottom: 92px;
      right: 20px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #B63866 0%, #8E6FC4 100%);
      color: #ffffff !important;
      border: none;
      cursor: pointer;
      box-shadow: 0 8px 25px rgba(182, 56, 102, 0.38);
      z-index: 9995;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
      outline: none;
    }
    .ph-chat-bubble-btn:hover {
      transform: scale(1.08) translateY(-2px);
      box-shadow: 0 12px 30px rgba(182, 56, 102, 0.48);
    }
    .ph-chat-bubble-btn svg {
      width: 28px;
      height: 28px;
      transition: transform 0.25s ease;
    }
    .ph-chat-bubble-btn.active svg.ph-icon-chat {
      display: none;
    }
    .ph-chat-bubble-btn:not(.active) svg.ph-icon-close {
      display: none;
    }
    .ph-chat-badge-pulse {
      position: absolute;
      top: -3px;
      right: -3px;
      width: 15px;
      height: 15px;
      background: #10B981;
      border: 2.5px solid #ffffff;
      border-radius: 50%;
      box-shadow: 0 0 10px #10B981;
    }

    /* Tooltip Callout */
    .ph-chat-callout {
      position: fixed;
      bottom: 163px;
      right: 20px;
      background: #ffffff;
      color: #2E2333;
      padding: 10px 16px;
      border-radius: 14px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px;
      font-weight: 600;
      box-shadow: 0 10px 25px rgba(46, 35, 51, 0.12);
      border: 1px solid #F3E8EE;
      z-index: 9994;
      display: flex;
      align-items: center;
      gap: 8px;
      animation: phBounce 2s infinite ease-in-out;
      cursor: pointer;
      transition: opacity 0.3s;
    }
    @keyframes phBounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-4px); }
    }

    /* Chat Container Window */
    .ph-chat-window {
      position: fixed;
      bottom: 162px;
      right: 20px;
      width: 380px;
      max-width: calc(100vw - 32px);
      height: 540px;
      max-height: calc(100vh - 120px);
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 20px 50px rgba(46, 35, 51, 0.22);
      border: 1px solid #EEE4EB;
      z-index: 9996;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      opacity: 0;
      transform: translateY(20px) scale(0.95);
      pointer-events: none;
      transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .ph-chat-window.open {
      opacity: 1;
      transform: translateY(0) scale(1);
      pointer-events: auto;
    }

    /* Header */
    .ph-chat-header {
      background: linear-gradient(135deg, #B63866 0%, #8E6FC4 100%);
      color: #ffffff;
      padding: 16px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-radius: 20px 20px 0 0;
    }
    .ph-chat-header-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .ph-chat-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.4);
    }
    .ph-chat-header-title h4 {
      margin: 0;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: -0.2px;
    }
    .ph-chat-header-title span {
      font-size: 11.5px;
      opacity: 0.88;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .ph-chat-header-title span::before {
      content: '';
      width: 7px;
      height: 7px;
      background: #34D399;
      border-radius: 50%;
      display: inline-block;
    }
    .ph-chat-close-btn {
      background: transparent;
      border: none;
      color: #ffffff;
      opacity: 0.8;
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      transition: opacity 0.2s;
    }
    .ph-chat-close-btn:hover {
      opacity: 1;
    }

    /* Messages Area */
    .ph-chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      background: #FAF6F4;
    }

    /* Chat Bubbles */
    .ph-msg {
      max-width: 82%;
      font-size: 13.5px;
      line-height: 1.48;
      word-break: break-word;
      animation: phMsgIn 0.25s ease forwards;
    }
    @keyframes phMsgIn {
      from { opacity: 0; transform: translateY(6px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .ph-msg.bot {
      align-self: flex-start;
      background: #ffffff;
      color: #2E2333;
      padding: 12px 15px;
      border-radius: 16px 16px 16px 4px;
      border: 1px solid #EEE4EB;
      box-shadow: 0 2px 8px rgba(46, 35, 51, 0.04);
    }
    .ph-msg.user {
      align-self: flex-end;
      background: linear-gradient(135deg, #B63866 0%, #972B53 100%);
      color: #ffffff;
      padding: 10px 14px;
      border-radius: 16px 16px 4px 16px;
      box-shadow: 0 4px 12px rgba(182, 56, 102, 0.25);
    }

    /* Email Gate Card */
    .ph-email-gate-card {
      background: #ffffff;
      border: 1.5px solid #F9D2DF;
      border-radius: 14px;
      padding: 16px;
      margin-top: 6px;
      box-shadow: 0 4px 14px rgba(182, 56, 102, 0.08);
    }
    .ph-email-gate-card h5 {
      margin: 0 0 6px;
      color: #B63866;
      font-size: 13.5px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .ph-email-gate-card p {
      margin: 0 0 10px;
      font-size: 12px;
      color: #6A5B6F;
      line-height: 1.4;
    }
    .ph-email-gate-input {
      width: 100%;
      padding: 9px 12px;
      border: 1.5px solid #EEE4EB;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 8px;
      box-sizing: border-box;
      outline: none;
      font-family: inherit;
    }
    .ph-email-gate-input:focus {
      border-color: #B63866;
    }
    .ph-email-gate-btn {
      width: 100%;
      background: #B63866;
      color: #ffffff;
      border: none;
      border-radius: 8px;
      padding: 9px 12px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: background 0.2s;
    }
    .ph-email-gate-btn:hover {
      background: #972B53;
    }

    /* Quick Action Chips */
    .ph-chat-chips {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 6px;
    }
    .ph-chip-btn {
      background: #FDF5F8;
      border: 1px solid #F9D2DF;
      color: #B63866;
      font-size: 11.5px;
      font-weight: 600;
      padding: 5px 10px;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.15s;
    }
    .ph-chip-btn:hover {
      background: #B63866;
      color: #ffffff;
      border-color: #B63866;
    }

    /* Product recommendation pill in message */
    .ph-rec-card {
      margin-top: 10px;
      padding: 10px 12px;
      background: #FAF6F4;
      border: 1px solid #EEE4EB;
      border-radius: 10px;
      font-size: 12.5px;
    }
    .ph-rec-card b {
      color: #B63866;
      display: block;
      margin-bottom: 4px;
    }
    .ph-rec-card a {
      display: inline-block;
      margin-top: 6px;
      color: #B63866;
      font-weight: 700;
      text-decoration: none;
    }
    .ph-rec-card a:hover {
      text-decoration: underline;
    }

    /* Typing Dots */
    .ph-typing {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 10px 14px;
      background: #ffffff;
      border-radius: 14px;
      border: 1px solid #EEE4EB;
      width: fit-content;
    }
    .ph-typing-dot {
      width: 6px;
      height: 6px;
      background: #B63866;
      border-radius: 50%;
      animation: phTyping 1.4s infinite ease-in-out;
    }
    .ph-typing-dot:nth-child(1) { animation-delay: 0s; }
    .ph-typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .ph-typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes phTyping {
      0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
      40% { transform: scale(1.1); opacity: 1; }
    }

    /* Bottom Input Bar */
    .ph-chat-input-bar {
      padding: 12px 14px;
      background: #ffffff;
      border-top: 1px solid #EEE4EB;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .ph-chat-input {
      flex: 1;
      padding: 10px 14px;
      border: 1.5px solid #EEE4EB;
      border-radius: 24px;
      font-family: inherit;
      font-size: 13.5px;
      outline: none;
      transition: border-color 0.2s;
    }
    .ph-chat-input:focus {
      border-color: #B63866;
    }
    .ph-chat-send-btn {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: #B63866;
      color: #ffffff;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
      flex-shrink: 0;
    }
    .ph-chat-send-btn:hover {
      background: #972B53;
      transform: scale(1.05);
    }
    .ph-chat-send-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
    }

    @media(max-width: 480px) {
      .ph-chat-window {
        bottom: 0;
        right: 0;
        width: 100vw;
        height: 100vh;
        max-width: 100vw;
        max-height: 100vh;
        border-radius: 0;
      }
      .ph-chat-header {
        border-radius: 0;
        padding-top: calc(16px + env(safe-area-inset-top, 0px));
      }
      .ph-chat-bubble-btn {
        bottom: calc(82px + env(safe-area-inset-bottom, 0px));
        right: 14px;
      }
      .ph-chat-callout {
        display: none;
      }
    }
  `;

  // Inject CSS
  const styleEl = document.createElement('style');
  styleEl.textContent = styles;
  document.head.appendChild(styleEl);

  // Widget HTML
  const widgetHtml = `
    <div id="phAiChatWidget">
      <!-- Callout badge preview -->
      <div class="ph-chat-callout" id="phChatCallout">
        <span>✨ <strong>AI Assistant</strong> • Need parenting help?</span>
      </div>

      <!-- Floating Button -->
      <button class="ph-chat-bubble-btn" id="phChatToggleBtn" aria-label="Open ParentingHub AI Chat">
        <div class="ph-chat-badge-pulse"></div>
        <!-- Chat Icon -->
        <svg class="ph-icon-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <!-- Close Icon -->
        <svg class="ph-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <!-- Chat Window -->
      <div class="ph-chat-window" id="phChatWindow">
        <!-- Header -->
        <div class="ph-chat-header">
          <div class="ph-chat-header-info">
            <div class="ph-chat-avatar">👶</div>
            <div class="ph-chat-header-title">
              <h4>ParentingHub AI</h4>
              <span>Online • Parenting & Milestone Guide</span>
            </div>
          </div>
          <button class="ph-chat-close-btn" id="phChatCloseBtn" aria-label="Minimize Chat">&times;</button>
        </div>

        <!-- Messages Area -->
        <div class="ph-chat-messages" id="phChatMessages">
          <!-- Initial bot welcome -->
          <div class="ph-msg bot">
            Assalam-o-Alaikum! 🌸 Welcome to <strong>ParentingHub AI</strong>.
            I can share general parenting and milestone information, feeding guidance, and relevant baby essentials. I do not diagnose medical conditions or replace a pediatrician. For urgent symptoms, contact local emergency services or a qualified clinician.
          </div>

          <!-- Email Activation Gate Area (injected dynamically if not verified) -->
          <div id="phEmailGateSlot"></div>
        </div>

        <!-- Input Bar -->
        <form class="ph-chat-input-bar" id="phChatInputForm">
          <input type="text" class="ph-chat-input" id="phChatInput" placeholder="Ask about milestones, feeding, colic..." autocomplete="off" />
          <button type="submit" class="ph-chat-send-btn" id="phChatSendBtn" aria-label="Send Message">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="22" y1="2" x2="11" y2="13"></line>
              <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
          </button>
        </form>
      </div>
    </div>
  `;

  const container = document.createElement('div');
  container.innerHTML = widgetHtml;
  document.body.appendChild(container.firstElementChild);

  // State Management
  const toggleBtn = document.getElementById('phChatToggleBtn');
  const chatWindow = document.getElementById('phChatWindow');
  const closeBtn = document.getElementById('phChatCloseBtn');
  const callout = document.getElementById('phChatCallout');
  const messagesBox = document.getElementById('phChatMessages');
  const inputForm = document.getElementById('phChatInputForm');
  const inputEl = document.getElementById('phChatInput');
  const sendBtn = document.getElementById('phChatSendBtn');
  const emailGateSlot = document.getElementById('phEmailGateSlot');

  let activeEmail = localStorage.getItem('ph_ai_email') || '';

  // Keep the floating assistant away from product-card CTAs and other bottom-right helpers.
  // The position is recalculated on scroll/resize because product buttons move through the viewport.
  let phFloatFrame = 0;
  function positionFloatingAssistant() {
    if (!toggleBtn || !chatWindow) return;
    const mobile = window.matchMedia('(max-width: 480px)').matches;
    const baseBottom = mobile ? 82 : 92;
    const right = mobile ? 14 : 20;

    toggleBtn.style.right = right + 'px';
    toggleBtn.style.bottom = `calc(${baseBottom}px + env(safe-area-inset-bottom, 0px))`;

    // Let layout settle before measuring the prospective bubble rectangle.
    const bubble = toggleBtn.getBoundingClientRect();
    let desiredBottom = baseBottom;

    document.querySelectorAll('.product-add-btn').forEach((btn) => {
      const r = btn.getBoundingClientRect();
      if (r.bottom <= 0 || r.top >= window.innerHeight || r.right <= 0 || r.left >= window.innerWidth) return;
      const horizontalOverlap = bubble.left < r.right + 8 && bubble.right > r.left - 8;
      const verticalOverlap = bubble.top < r.bottom + 8 && bubble.bottom > r.top - 8;
      if (horizontalOverlap && verticalOverlap) {
        desiredBottom = Math.max(desiredBottom, Math.ceil(window.innerHeight - r.top + 14));
      }
    });

    // Avoid stacking directly on top of the back-to-top helper.
    const backTop = document.querySelector('.ph-back-to-top.visible');
    if (backTop) {
      const r = backTop.getBoundingClientRect();
      const nextTop = window.innerHeight - desiredBottom - toggleBtn.offsetHeight;
      const nextBottom = nextTop + toggleBtn.offsetHeight;
      const horizontalOverlap = (window.innerWidth - right - toggleBtn.offsetWidth) < r.right + 8 &&
        (window.innerWidth - right) > r.left - 8;
      if (horizontalOverlap && nextTop < r.bottom + 8 && nextBottom > r.top - 8) {
        desiredBottom = Math.max(desiredBottom, Math.ceil(window.innerHeight - r.top + 14));
      }
    }

    // Prevent an excessive jump if a very tall card button happens to be near the top.
    desiredBottom = Math.min(desiredBottom, mobile ? 190 : 230);
    toggleBtn.style.bottom = `calc(${desiredBottom}px + env(safe-area-inset-bottom, 0px))`;

    if (callout) {
      callout.style.right = right + 'px';
      callout.style.bottom = `calc(${desiredBottom + 71}px + env(safe-area-inset-bottom, 0px))`;
    }
    if (!mobile) {
      chatWindow.style.right = right + 'px';
      chatWindow.style.bottom = `calc(${desiredBottom + 70}px + env(safe-area-inset-bottom, 0px))`;
    } else {
      // Mobile CSS owns the full-screen chat window position.
      chatWindow.style.right = '';
      chatWindow.style.bottom = '';
    }
  }

  function scheduleFloatingAssistantPosition() {
    if (phFloatFrame) cancelAnimationFrame(phFloatFrame);
    phFloatFrame = requestAnimationFrame(() => {
      phFloatFrame = 0;
      positionFloatingAssistant();
    });
  }

  window.addEventListener('scroll', scheduleFloatingAssistantPosition, { passive: true });
  window.addEventListener('resize', scheduleFloatingAssistantPosition, { passive: true });
  document.addEventListener('DOMContentLoaded', scheduleFloatingAssistantPosition, { once: true });
  setTimeout(scheduleFloatingAssistantPosition, 50);
  setTimeout(scheduleFloatingAssistantPosition, 500);

  // Check if parent is logged in
  try {
    const rawUser = localStorage.getItem('ph_user') || localStorage.getItem('user');
    if (rawUser) {
      const u = JSON.parse(rawUser);
      if (u.email) {
        activeEmail = u.email;
        localStorage.setItem('ph_ai_email', u.email);
      }
    }
  } catch (e) {}

  function toggleChat(forceOpen = false) {
    scheduleFloatingAssistantPosition();
    const isOpen = forceOpen ? true : !chatWindow.classList.contains('open');
    if (isOpen) {
      chatWindow.classList.add('open');
      toggleBtn.classList.add('active');
      if (callout) callout.style.display = 'none';
      if (activeEmail) {
        inputEl.focus();
      }
    } else {
      chatWindow.classList.remove('open');
      toggleBtn.classList.remove('active');
    }
  }

  toggleBtn.addEventListener('click', () => toggleChat());
  closeBtn.addEventListener('click', () => toggleChat(false));
  if (callout) callout.addEventListener('click', () => toggleChat(true));

  // Global window function so homepage banner button can open the chatbot!
  window.openAiMilestoneAdvisor = () => toggleChat(true);

  // Render Email Gate or Quick Chips
  function renderActivationState() {
    if (!activeEmail) {
      emailGateSlot.innerHTML = `
        <div class="ph-email-gate-card" id="phGateCard">
          <h5>🔒 Activate AI Assistant</h5>
          <p>Please enter your email to activate the ParentingHub AI information assistant:</p>
          <input type="email" class="ph-email-gate-input" id="phGateEmail" placeholder="your-email@example.com" required />
          <button type="button" class="ph-email-gate-btn" id="phGateActivateBtn">
            <span>✨ Activate Chatbot</span>
          </button>
        </div>
      `;
      inputEl.disabled = true;
      inputEl.placeholder = "Please enter your email above to activate...";
      sendBtn.disabled = true;

      const actBtn = document.getElementById('phGateActivateBtn');
      const actInput = document.getElementById('phGateEmail');

      actBtn.addEventListener('click', () => {
        const emailVal = (actInput.value || '').trim();
        if (!emailVal || !emailVal.includes('@') || !emailVal.includes('.')) {
          alert('Please enter a valid email address.');
          return;
        }
        activeEmail = emailVal;
        localStorage.setItem('ph_ai_email', emailVal);
        renderActivationState();
        appendBotMessage(`Thank you! 💖 Chatbot activated for <strong>${activeEmail}</strong>. How can I help you and your baby today?`);
      });
    } else {
      emailGateSlot.innerHTML = `
        <div class="ph-chat-chips">
          <button class="ph-chip-btn" data-query="Bacha 6 mahine ka ho gaya hai, food diet plan suggest karein.">🥣 6-Month Diet Plan</button>
          <button class="ph-chip-btn" data-query="Best anti-colic feeding bottles and teats for newborn">🍼 Anti-Colic Bottles</button>
          <button class="ph-chip-btn" data-query="Baby sleep routine and night waking guidance">💤 Baby Sleep Routine</button>
          <button class="ph-chip-btn" data-query="Baby teething symptoms and safe soothing essentials">🦷 Teething Guidance</button>
        </div>
      `;
      inputEl.disabled = false;
      inputEl.placeholder = "Ask about milestones, feeding, colic...";
      sendBtn.disabled = false;

      // Chip click listeners
      document.querySelectorAll('.ph-chip-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          const q = btn.dataset.query;
          sendUserQuery(q);
        });
      });
    }
  }

  renderActivationState();

  function appendUserMessage(text) {
    const el = document.createElement('div');
    el.className = 'ph-msg user';
    el.textContent = text;
    messagesBox.appendChild(el);
    messagesBox.scrollTop = messagesBox.scrollHeight;
  }

  function appendBotMessage(htmlContent) {
    const el = document.createElement('div');
    el.className = 'ph-msg bot';
    el.innerHTML = htmlContent;
    messagesBox.appendChild(el);
    messagesBox.scrollTop = messagesBox.scrollHeight;
  }

  function showTypingIndicator() {
    const el = document.createElement('div');
    el.className = 'ph-typing';
    el.id = 'phTypingIndicator';
    el.innerHTML = `
      <div class="ph-typing-dot"></div>
      <div class="ph-typing-dot"></div>
      <div class="ph-typing-dot"></div>
    `;
    messagesBox.appendChild(el);
    messagesBox.scrollTop = messagesBox.scrollHeight;
  }

  function removeTypingIndicator() {
    const el = document.getElementById('phTypingIndicator');
    if (el) el.remove();
  }

  function safeAssistantHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#039;')
      .replace(/\n/g, '<br>');
  }

  async function sendUserQuery(query) {
    if (!query.trim()) return;
    if (!activeEmail) {
      appendBotMessage("Please enter your email above to activate the AI assistant first! 🔒");
      return;
    }

    appendUserMessage(query);
    inputEl.value = '';
    showTypingIndicator();

    const payload = { parent_email: activeEmail, query: query };

    let data = null;
    let requestError = '';

    try {
      const res = await fetch('/api/milestone', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
      });
      const parsed = await res.json().catch(() => null);
      if (!res.ok || !parsed || parsed.success === false) {
        requestError = (parsed && (parsed.message || parsed.error)) || 'The advisor is temporarily unavailable.';
      } else {
        data = parsed;
      }
    } catch (e) {
      requestError = 'The advisor could not connect. Please try again later.';
    }

    removeTypingIndicator();

    if (data && (data.advice || data.store_recommendation)) {
      let botHtml = `<div>${safeAssistantHtml(data.advice || 'Here is your guidance.')}</div>`;
      if (data.store_recommendation) {
        botHtml += `
          <div class="ph-rec-card">
            <b>🛍️ Recommended Gear & Essentials:</b>
            <span>${safeAssistantHtml(data.store_recommendation)}</span>
            <br>
            <a href="/shop">Browse Authentic Store Collection →</a>
          </div>
        `;
      }
      botHtml += `<div style="margin-top:10px;padding-top:8px;border-top:1px solid #EEE4EB;font-size:11.5px;color:#6A5B6F"><b>Safety note:</b> General information only — not a diagnosis or substitute for professional medical care. For breathing difficulty, severe dehydration, seizures, unresponsiveness, serious injury, or other emergencies, seek urgent medical help.</div>`;
      appendBotMessage(botHtml);
    } else {
      appendBotMessage(`<div>${requestError || 'The advisor is temporarily unavailable.'}</div><div style="margin-top:8px;font-size:12px;color:#6A5B6F">For urgent or concerning symptoms, contact a qualified pediatrician or local emergency service. Do not delay medical care while waiting for this assistant.</div>`);
    }
  }

  inputForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const val = inputEl.value;
    sendUserQuery(val);
  });

})();
