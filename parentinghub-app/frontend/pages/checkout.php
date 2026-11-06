<?php
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
require_once dirname(__DIR__, 2) . '/backend/api/bootstrap.php';
$paypalClientId = ph_env('PAYPAL_CLIENT_ID', '');
$paypalMode = ph_env('PAYPAL_MODE', 'sandbox');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="/">
<meta name="theme-color" content="#bd3d6b">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Secure Checkout | ParentingHub Pakistan</title>
<meta name="description" content="Complete your baby essentials order with secure checkout. Cash on Delivery nationwide, Direct Bank Transfer / Raast, and PayPal Sandbox USD support.">
<link rel="canonical" href="https://www.parentinghub.pk/checkout">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style.css?v=5.6-live-checkout">
<link rel="stylesheet" href="/css/smartcart-ai.css?v=1.1">

<!-- Favicons -->
<link rel="icon" type="image/png" href="favicon.png">
<link rel="icon" type="image/x-icon" href="favicon.ico">
<style>
.checkout-page-grid {
  display: grid;
  grid-template-columns: 1.4fr 1fr;
  gap: 32px;
  align-items: start;
}
@media (max-width: 860px) {
  .checkout-page-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }
}
.pay-opt-card {
  display: flex;
  gap: 14px;
  align-items: flex-start;
  padding: 14px 16px;
  border: 1.5px solid var(--line);
  border-radius: 14px;
  margin-bottom: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  background: var(--surface);
}
.pay-opt-card:hover {
  border-color: var(--primary);
  background: #fdf5f7;
}
.pay-opt-card.selected {
  border-color: var(--primary);
  background: #fdf2f5;
  box-shadow: 0 0 0 1px var(--primary);
}
.pay-opt-card input[type="radio"] {
  margin-top: 3px;
  accent-color: var(--primary);
}
.checkout-item-mini {
  display: flex;
  gap: 12px;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid var(--line);
}
.checkout-item-mini img {
  width: 54px;
  height: 54px;
  object-fit: cover;
  border-radius: 8px;
  background: var(--cream);
}
</style>
</head>
<body data-page="checkout">
<div id="chrome-top"></div>

<main style="padding:40px 0 80px;background:var(--cream);min-height:85vh">
  <div class="container" style="max-width:1040px">
    
    <!-- Top breadcrumb / header -->
    <div style="margin-bottom:28px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
      <div>
        <a href="/cart" style="color:var(--primary);text-decoration:none;font-size:13.5px;font-weight:600;display:inline-flex;align-items:center;gap:4px;margin-bottom:6px">
          ← Back to Shopping Bag
        </a>
        <h1 style="margin:2px 0 0;font-size:28px">Secure Checkout</h1>
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <span class="badge" style="background:#EAF7EE;color:#1E8E3E;padding:6px 12px;border-radius:20px;font-size:12.5px;font-weight:700">
          🔒 256-Bit SSL Encrypted
        </span>
        <span class="badge" style="background:#FFF3CD;color:#856404;border:1px solid #FFEEBA;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:700">
          PayPal Sandbox Enabled
        </span>
      </div>
    </div>

    <!-- Empty bag container -->
    <div id="checkoutEmptyState" style="display:none;text-align:center;padding:70px 20px;background:var(--surface);border-radius:24px;box-shadow:var(--sh-s)">
      <div style="font-size:52px;margin-bottom:14px">🛍️</div>
      <h2 style="margin:0 0 8px;font-size:22px">Your shopping bag is empty</h2>
      <p class="muted small" style="margin:0 auto 24px;max-width:440px">
        Add genuine Philips Avent baby essentials to your bag to proceed with checkout.
      </p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a href="/shop" class="btn btn-primary" style="padding:12px 24px">Browse Shop Catalog</a>
        <button onclick="addSampleItemAndCheckout()" class="btn btn-ghost" style="padding:12px 20px">
          + Add Sample Anti-Colic Bottle (Demo)
        </button>
      </div>
    </div>

    <!-- Main checkout form grid -->
    <div id="checkoutActiveGrid" class="checkout-page-grid">
      
      <!-- Left Column: Delivery & Payment Details -->
      <div class="section-card" style="padding:30px;background:var(--surface);border-radius:22px;box-shadow:var(--sh-s)">
        <form id="checkoutForm" onsubmit="return handlePlaceOrder(event)">
          
          <h2 style="font-size:19px;margin:0 0 18px;display:flex;align-items:center;gap:8px">
            <span>📦</span> 1. Delivery Details
          </h2>

          <div class="form-group" style="margin-bottom:14px">
            <label for="coName" style="display:block;font-weight:600;font-size:13.5px;margin-bottom:6px">Full Name *</label>
            <input id="coName" name="name" required placeholder="e.g. Fatima Ali" style="width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:14px;box-sizing:border-box">
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
            <div class="form-group">
              <label for="coPhone" style="display:block;font-weight:600;font-size:13.5px;margin-bottom:6px">Mobile Number *</label>
              <input id="coPhone" name="phone" type="tel" inputmode="tel" required placeholder="03XX XXXXXXX" style="width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:14px;box-sizing:border-box">
              <span class="tiny muted" style="display:block;margin-top:4px">Used for SMS dispatch & delivery</span>
            </div>
            <div class="form-group">
              <label for="coEmail" style="display:block;font-weight:600;font-size:13.5px;margin-bottom:6px">Email Address</label>
              <input id="coEmail" name="email" type="email" placeholder="name@example.com" style="width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:14px;box-sizing:border-box">
              <span class="tiny muted" style="display:block;margin-top:4px">For order confirmation receipt</span>
            </div>
          </div>

          <div class="form-group" style="margin-bottom:14px">
            <label for="coAddress" style="display:block;font-weight:600;font-size:13.5px;margin-bottom:6px">Complete Street Address *</label>
            <textarea id="coAddress" name="address" required rows="2" placeholder="House / Flat #, Street #, Sector / Area / Colony" style="width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:14px;box-sizing:border-box"></textarea>
          </div>

          <div class="form-group" style="margin-bottom:24px">
            <label for="coCity" style="display:block;font-weight:600;font-size:13.5px;margin-bottom:6px">City *</label>
            <select id="coCity" name="city" required onchange="updateCheckoutDeliveryEstimate(this.value)" style="width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:14px;background:var(--surface);box-sizing:border-box">
              <option value="">Select Delivery City</option>
              <option value="Karachi">Karachi (Same Day / Next Day)</option>
              <option value="Lahore">Lahore (1-2 Days)</option>
              <option value="Islamabad">Islamabad (1-2 Days)</option>
              <option value="Rawalpindi">Rawalpindi (1-2 Days)</option>
              <option value="Faisalabad">Faisalabad (2-3 Days)</option>
              <option value="Multan">Multan (2-3 Days)</option>
              <option value="Peshawar">Peshawar (2-3 Days)</option>
              <option value="Quetta">Quetta (3-4 Days)</option>
              <option value="Sialkot">Sialkot (2-3 Days)</option>
              <option value="Gujranwala">Gujranwala (2-3 Days)</option>
              <option value="Hyderabad">Hyderabad (1-2 Days)</option>
              <option value="Other">Other Nationwide City / Town (2-4 Days)</option>
            </select>
            <div id="cityDeliveryEstimate" style="margin-top:6px;font-size:12px;color:var(--ink-2);background:#f8f9fa;border-radius:8px;padding:8px 12px;border:1px solid var(--line)">
              🚚 Standard nationwide courier delivery fee: PKR 200 per order.
            </div>
          </div>

          <hr style="border:none;border-top:1px solid var(--line);margin:24px 0">

          <h2 style="font-size:19px;margin:0 0 16px;display:flex;align-items:center;gap:8px">
            <span>💳</span> 2. Payment Method
          </h2>

          <div class="pay-options">
            <label class="pay-opt-card selected" id="optCod" onclick="selectPayment('cod')">
              <input type="radio" name="paymentMethod" value="cod" checked>
              <div>
                <b style="font-size:14.5px;display:block">Cash on Delivery (COD)</b>
                <span class="muted small" style="display:block;margin-top:2px">Pay with cash when your parcel arrives safely at your doorstep across Pakistan.</span>
              </div>
            </label>

            <label class="pay-opt-card" id="optBank" onclick="selectPayment('bank')">
              <input type="radio" name="paymentMethod" value="bank">
              <div>
                <b style="font-size:14.5px;display:block">Direct Bank Transfer / Raast (Instant)</b>
                <span class="muted small" style="display:block;margin-top:2px">Transfer directly to our official corporate account via any Pakistani mobile banking app.</span>
              </div>
            </label>

            <label class="pay-opt-card" id="optPaypal" onclick="selectPayment('paypal')">
              <input type="radio" name="paymentMethod" value="paypal">
              <div style="flex-grow:1">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px">
                  <b style="font-size:14.5px">PayPal Sandbox (USD / Cards)</b>
                  <div style="display:flex;align-items:center;gap:6px">
                    <span class="badge" style="background:#003087;color:#fff;font-size:11px;padding:2px 8px;border-radius:4px;font-weight:700">PayPal</span>
                    <span class="badge" style="background:#FFF3CD;color:#856404;border:1px solid #FFEEBA;font-size:10px;padding:2px 6px;border-radius:4px;font-weight:700">DEMO READY</span>
                  </div>
                </div>
                <span class="muted small" style="display:block;margin-top:2px">Pay securely via PayPal Sandbox orders v2 API. Evaluated in USD at fixed demo exchange rate (280 PKR = $1 USD).</span>
              </div>
            </label>
          </div>

          <!-- Bank details box (shown if bank selected) -->
          <div class="bank-info-box" id="bankDetailsBox" style="display:none;background:#f5f8ff;border:1px solid #d4e2ff;padding:14px;border-radius:12px;margin:12px 0;font-size:13px">
            <b style="color:#004085;display:block;margin-bottom:6px">🏦 Bank Account & Raast Details:</b>
            <p style="margin:0 0 4px"><b>Bank:</b> Meezan Bank Limited</p>
            <p style="margin:0 0 4px"><b>Account Title:</b> ParentingHub Pakistan (Pvt) Ltd</p>
            <p style="margin:0 0 4px"><b>IBAN:</b> PK42MEZN0001234567890123</p>
            <p style="margin:0"><b>Raast ID:</b> 03001234567</p>
          </div>

          <!-- PayPal info box -->
          <div class="paypal-info-box" id="paypalInfoBox" style="display:none;background:#f0f7ff;border:1px solid #cce5ff;padding:14px;border-radius:12px;margin:12px 0;font-size:13px">
            <b style="color:#003087;display:block;margin-bottom:4px">ℹ️ PayPal Sandbox Test Mode:</b>
            <p class="small" style="margin:0 0 6px">Clicking the PayPal button below will launch the simulated sandbox payment flow. Complete the approval to generate an official verified order token.</p>
          </div>

          <div class="form-group" style="margin-top:16px">
            <label for="coNotes" style="display:block;font-weight:600;font-size:13.5px;margin-bottom:6px">Order / Delivery Instructions (Optional)</label>
            <input id="coNotes" name="notes" placeholder="e.g. Leave with security guard, call before delivery" style="width:100%;padding:10px 14px;border:1.5px solid var(--line);border-radius:10px;font-size:13.5px;box-sizing:border-box">
          </div>

          <div id="checkoutError" style="display:none;color:#d93025;background:#fce8e6;border:1px solid #fad2cf;padding:12px 16px;border-radius:10px;font-size:13.5px;margin:14px 0"></div>

          <!-- Standard submit button for COD / Bank -->
          <button type="submit" id="coSubmitBtn" class="btn btn-primary btn-block" style="padding:15px;font-size:16px;margin-top:14px;font-weight:700">
            Confirm & Place Order →
          </button>

          <!-- PayPal Buttons container -->
          <div id="paypalBtnContainer" style="display:none;margin-top:14px"></div>

        </form>
      </div>

      <!-- Right Column: Order Summary -->
      <div>
        <div class="section-card" style="padding:26px;background:var(--surface);border-radius:22px;box-shadow:var(--sh-m);position:sticky;top:90px">
          <h3 style="margin:0 0 16px;font-size:18px">Order Summary</h3>

          <!-- Items list -->
          <div id="checkoutItemsList" style="max-height:300px;overflow-y:auto;margin-bottom:16px">
            <!-- Populated via JS -->
          </div>

          <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:14px">
            <span class="muted">Subtotal</span>
            <b id="summarySubtotalVal">Rs 0</b>
          </div>

          <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:14px">
            <span class="muted">Nationwide Courier Fee</span>
            <b id="summaryShippingVal" style="color:var(--green)">Rs 200</b>
          </div>

          <div style="border-top:1px solid var(--line);padding-top:14px;margin-top:12px;display:flex;justify-content:space-between;align-items:center">
            <span style="font-weight:700;font-size:16px">Total (PKR)</span>
            <b style="font-size:22px;color:var(--primary)" id="summaryTotalPkrVal">Rs 0</b>
          </div>

          <!-- PayPal conversion badge -->
          <div style="margin:14px 0 18px;background:#F5F9FC;border:1px solid #CDE2F5;padding:12px 14px;border-radius:12px;font-size:13px">
            <div style="display:flex;justify-content:space-between;align-items:center;color:#003087;font-weight:700">
              <span>PayPal USD Total:</span>
              <span id="summaryTotalUsdVal">$0.00 USD</span>
            </div>
            <span class="tiny muted" style="display:block;margin-top:3px">Demo Conversion Rate: 280 PKR = $1 USD</span>
          </div>

          <div style="font-size:12.5px;color:var(--ink-2);display:flex;flex-direction:column;gap:6px;border-top:1px solid var(--line);padding-top:12px">
            <span>🛡️ 100% Genuine Authorized Philips Avent</span>
            <span>📦 Sealed Packaging with Quality Assurance</span>
            <span>🔄 7-Day Easy Return & Exchange Policy</span>
          </div>
        </div>
      </div>

    </div>

  </div>
</main>

<div id="chrome-bottom"></div>

<script src="/js/data.js?v=5.6-live-checkout"></script>
<?php if ($paypalClientId !== '' && $paypalMode === 'sandbox'): ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?= htmlspecialchars($paypalClientId) ?>&currency=USD"></script>
<?php endif; ?>
<script src="/js/app.js?v=5.6-live-checkout"></script>
<script src="/js/smartcart-ai.js?v=1.1"></script>
<script>
function renderCheckoutPage(){
  if (!S.cart || !S.cart.length) {
    const sample = (typeof PRODUCTS !== 'undefined' && PRODUCTS.length) ? 
      (PRODUCTS.find(p => p.category === 'Bottle Feeding' || p.category === 'Feeding Bottles') || PRODUCTS[0]) : null;
    if (sample) {
      S.cart = [{ id: sample.id, qty: 1, name: sample.name, price: sample.price }];
      if (typeof save === 'function') save();
      if (typeof updateCart === 'function') updateCart();
    }
  }
  const cart = S.cart || [];
  const emptyBox = document.getElementById("checkoutEmptyState");
  const activeGrid = document.getElementById("checkoutActiveGrid");

  if (!cart.length) {
    if (emptyBox) emptyBox.style.display = "block";
    if (activeGrid) activeGrid.style.display = "none";
    return;
  }

  if (emptyBox) emptyBox.style.display = "none";
  if (activeGrid) activeGrid.style.display = "grid";

  // Render items list
  const itemsContainer = document.getElementById("checkoutItemsList");
  if (itemsContainer) {
    itemsContainer.innerHTML = cart.map(i => {
      const p = PRODUCTS.find(x => x.id === i.id) || { name: i.name || "Product", price: i.price || 0, img: "art/placeholder.jpg" };
      const img = p.img || (p.images && p.images[0]) || 'art/placeholder.jpg';
      return `
        <div class="checkout-item-mini">
          <img src="${img}" alt="${esc(p.name)}" onerror="this.src='art/placeholder.jpg'">
          <div style="flex-grow:1;font-size:13px">
            <b style="display:block;line-height:1.3">${esc(p.name)}</b>
            <span class="muted tiny">${i.qty} × ${money(p.price)}</span>
          </div>
          <b style="font-size:13.5px">${money((p.price || 0) * i.qty)}</b>
        </div>
      `;
    }).join("");
  }

  // Calculate totals
  const { subtotal, discount, shipping, total } = calculateOrderTotals();
  const subtotalEl = document.getElementById("summarySubtotalVal");
  const shippingEl = document.getElementById("summaryShippingVal");
  const totalPkrEl = document.getElementById("summaryTotalPkrVal");
  const totalUsdEl = document.getElementById("summaryTotalUsdVal");
  const submitBtn = document.getElementById("coSubmitBtn");

  if (subtotalEl) subtotalEl.textContent = money(subtotal);
  if (shippingEl) shippingEl.textContent = money(shipping);
  if (totalPkrEl) totalPkrEl.textContent = money(total);

  const totalUsd = (total / 280).toFixed(2);
  if (totalUsdEl) totalUsdEl.textContent = `$${totalUsd} USD`;
  if (submitBtn) submitBtn.textContent = `Confirm & Place Order (${money(total)}) →`;

  // Pre-fill customer details from localStorage if present
  const saved = store.get("customer_info", {});
  if (saved.name && document.getElementById("coName")) document.getElementById("coName").value = saved.name;
  if (saved.phone && document.getElementById("coPhone")) document.getElementById("coPhone").value = saved.phone;
  if (saved.email && document.getElementById("coEmail")) document.getElementById("coEmail").value = saved.email;
  if (saved.address && document.getElementById("coAddress")) document.getElementById("coAddress").value = saved.address;
  if (saved.city && document.getElementById("coCity")) {
    document.getElementById("coCity").value = saved.city;
    updateCheckoutDeliveryEstimate(saved.city);
  }
}

function addSampleItemAndCheckout() {
  const sample = PRODUCTS.find(p => p.category === 'Feeding Bottles') || PRODUCTS[0];
  if (sample) {
    addToCart(sample.id, 1);
    toast("Added " + sample.name + " to your bag!", "check");
    renderCheckoutPage();
  }
}

document.addEventListener("DOMContentLoaded", () => {
  renderCheckoutPage();

  // Handle return from PayPal sandbox approval
  const urlParams = new URLSearchParams(window.location.search);
  const pendingRaw = sessionStorage.getItem('ph_pending_paypal');
  if (urlParams.get('token') && pendingRaw) {
    try {
      const pending = JSON.parse(pendingRaw);
      sessionStorage.removeItem('ph_pending_paypal');
      const errBox = document.getElementById("checkoutError");
      if(errBox) { 
        errBox.style.display = "block"; 
        errBox.style.background = "#e6f4ea"; 
        errBox.style.color = "#137333"; 
        errBox.style.borderColor = "#ceead6"; 
        errBox.textContent = "PayPal approved! Capturing payment and confirming order..."; 
      }
      fetch('/api/paypal/capture-order', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-Payment-Token': pending.paymentToken || ""
        },
        body: JSON.stringify({
          orderId: pending.orderId,
          paypalOrderId: pending.paypalOrderId
        })
      }).then(r => r.json()).then(capData => {
        if (capData && capData.success) {
          S.cart = [];
          if (typeof save === 'function') save();
          if (typeof updateCart === 'function') updateCart();
          if (typeof showOrderSuccess === 'function') {
            showOrderSuccess({
              id: pending.orderId,
              status: 'Confirmed',
              payment: 'paypal',
              captureId: capData.captureId,
              total: capData.amountPkr,
              amountUsd: capData.amountUsd,
              customer: pending.customer,
              createdAt: new Date().toISOString()
            });
          } else {
            window.location.href = '/track/' + pending.orderId;
          }
        } else {
          if (errBox) { 
            errBox.style.background = "#fce8e6"; 
            errBox.style.color = "#d93025"; 
            errBox.style.borderColor = "#fad2cf"; 
            errBox.textContent = (capData && capData.message) || "Could not capture approved PayPal order."; 
          }
        }
      });
    } catch(e){}
  }
});
</script>
</body>
</html>

