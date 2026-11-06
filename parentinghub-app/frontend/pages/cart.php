<?php
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="/">
<meta name="theme-color" content="#bd3d6b">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Shopping Bag | ParentingHub Pakistan</title>
<meta name="description" content="Review items in your shopping bag. 100% authentic Philips Avent baby essentials with Cash on Delivery nationwide.">
<link rel="canonical" href="https://www.parentinghub.pk/cart">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style.css?v=5.6-live-checkout">
<link rel="stylesheet" href="/css/smartcart-ai.css?v=1.1">

<!-- Favicons -->
<link rel="icon" type="image/png" href="favicon.png">
<link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body data-page="cart">
<div id="chrome-top"></div>

<main style="padding:44px 0 70px;background:var(--cream);min-height:80vh">
  <div class="container" style="max-width:960px">
    <div style="margin-bottom:24px">
      <span class="eyebrow dotted">Order Review</span>
      <h1 style="margin:8px 0 4px;font-size:28px">Shopping Bag</h1>
      <p class="muted small" style="margin:0">Standard delivery fee: PKR 200 per order · Cash on Delivery available.</p>
    </div>

    <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:28px" id="cartLayoutGrid">
      
      <!-- Left: Cart Items -->
      <div>
        <div class="section-card" style="padding:24px;background:var(--surface);border-radius:22px;box-shadow:var(--sh-s)" id="cartItemsContainer">
          <!-- Items will be populated by JS -->
        </div>
      </div>

      <!-- Right: Summary Card -->
      <div>
        <div class="section-card" style="padding:26px;background:var(--surface);border-radius:22px;box-shadow:var(--sh-m);position:sticky;top:90px">
          <h3 style="margin:0 0 16px;font-size:18px">Order Summary</h3>

          <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:14px">
            <span class="muted">Subtotal</span>
            <b id="cartSubtotalVal">Rs 0</b>
          </div>
          <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:14px">
            <span class="muted">Shipping</span>
            <span id="cartShippingVal" style="color:var(--green);font-weight:700">Calculated</span>
          </div>
          <div style="display:flex;justify-content:space-between;margin-bottom:16px;font-size:14px;display:none" id="cartDiscountRow">
            <span class="muted">Promo Discount</span>
            <span id="cartDiscountVal" style="color:var(--primary);font-weight:700">-Rs 0</span>
          </div>

          <div style="border-top:1px solid var(--line);padding-top:14px;margin-top:10px;display:flex;justify-content:space-between;align-items:center">
            <span style="font-weight:700;font-size:16px">Total</span>
            <b style="font-size:20px;color:var(--primary)" id="cartFinalTotalVal">Rs 0</b>
          </div>

          <!-- Dual-currency PayPal Sandbox preview -->
          <div style="margin:10px 0;background:#F5F9FC;border:1px solid #CDE2F5;padding:10px 14px;border-radius:12px;font-size:12.5px" id="paypalCartConversionBox">
            <div style="display:flex;justify-content:space-between;align-items:center;color:#003087;font-weight:700">
              <span>PayPal Sandbox Total:</span>
              <span id="cartTotalUsdVal">$0.00 USD</span>
            </div>
            <span class="tiny muted" style="display:block;margin-top:2px">Fixed demo rate: 280 PKR = $1 USD</span>
          </div>

          <!-- Fixed delivery summary -->
          <div style="margin:14px 0 18px;background:var(--cream);padding:12px 14px;border-radius:12px" id="deliveryFeeBox">
            <span class="tiny" style="font-weight:600;display:block" id="deliveryFeeLabel">Standard delivery fee: PKR 200 per order.</span>
          </div>

          <a href="/checkout" class="btn btn-primary btn-block" style="padding:14px;font-size:15px;text-align:center;text-decoration:none;display:block">
            Proceed to Checkout →
          </a>

          <a href="/shop" class="btn btn-ghost btn-block" style="margin-top:10px;text-decoration:none;text-align:center">
            Continue Shopping
          </a>

          <div style="margin-top:20px;display:flex;align-items:center;gap:12px;font-size:12px;color:var(--ink-2)">
            <span>🛡️ 100% Genuine</span>
            <span>💵 Cash on Delivery</span>
            <span>⚡ 2–4 Day Delivery</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<div id="chrome-bottom"></div>
<script src="/js/data.js?v=5.6-live-checkout"></script>
<script src="/js/app.js?v=5.6-live-checkout"></script>
<script src="/js/smartcart-ai.js?v=1.1"></script>
<script>
function renderCartPage(){
  const container = document.getElementById("cartItemsContainer");
  if(!container) return;

  const cart = S.cart || [];
  if(!cart.length){
    document.getElementById("cartLayoutGrid").innerHTML = `
      <div style="grid-column:1/-1;text-align:center;padding:70px 16px;background:var(--surface);border-radius:24px;box-shadow:var(--sh-s)">
        <div style="font-size:48px;color:var(--primary);margin-bottom:12px">🛍️</div>
        <h2 style="margin:0 0 8px;font-size:22px">Your shopping bag is empty</h2>
        <p class="muted small" style="margin:0 auto 20px;max-width:440px">
          Discover our verified selection of Philips Avent anti-colic feeding bottles, natural response teats and breast pumps.
        </p>
        <a href="/shop" class="btn btn-primary">Start Shopping Now</a>
      </div>
    `;
    return;
  }

  container.innerHTML = cart.map(it => {
    const p = PRODUCTS.find(x => x.id === it.id) || { name: it.name, price: it.price, image: "art/placeholder.jpg" };
    const lineTotal = (p.price || 0) * it.qty;
    return `
      <div style="display:flex;gap:16px;padding:16px 0;border-bottom:1px solid var(--line);align-items:center">
        <img src="${p.image || 'art/placeholder.jpg'}" alt="${esc(p.name)}" style="width:76px;height:76px;object-fit:cover;border-radius:12px;background:var(--cream)">
        <div style="flex-grow:1">
          <a href="/shop" style="color:var(--ink);text-decoration:none;font-weight:700;font-size:15px;display:block;line-height:1.35">${esc(p.name)}</a>
          <span class="tiny muted">${money(p.price || 0)} each</span>
          <div style="display:flex;align-items:center;gap:12px;margin-top:8px">
            <div class="stepper" style="display:inline-flex;align-items:center;border:1px solid var(--line);border-radius:8px;overflow:hidden">
              <button onclick="changeQty('${it.id}', -1)" style="border:none;background:var(--cream);padding:4px 10px;cursor:pointer;font-weight:700">-</button>
              <span style="padding:4px 12px;font-size:13px;font-weight:700">${it.qty}</span>
              <button onclick="changeQty('${it.id}', 1)" style="border:none;background:var(--cream);padding:4px 10px;cursor:pointer;font-weight:700">+</button>
            </div>
            <button onclick="removeFromCart('${it.id}')" style="background:none;border:none;color:#dc2626;font-size:12px;cursor:pointer;padding:0">Remove</button>
          </div>
        </div>
        <div style="text-align:right">
          <b style="font-size:16px;color:var(--ink)">${money(lineTotal)}</b>
        </div>
      </div>
    `;
  }).join("");

  const { subtotal, discount, shipping, total } = calculateOrderTotals();
  document.getElementById("cartSubtotalVal").textContent = money(subtotal);
  document.getElementById("cartShippingVal").textContent = money(shipping);
  document.getElementById("cartFinalTotalVal").textContent = money(total);

  const totalUsd = (total / 280).toFixed(2);
  const usdEl = document.getElementById("cartTotalUsdVal");
  if(usdEl) usdEl.textContent = `$${totalUsd} USD`;

  if(discount > 0){
    document.getElementById("cartDiscountRow").style.display = "flex";
    document.getElementById("cartDiscountVal").textContent = "-" + money(discount);
  }

  const feeLabel = document.getElementById("deliveryFeeLabel");
  if(feeLabel) feeLabel.textContent = "Standard delivery fee: PKR 200 per order. No free-delivery threshold.";
}

document.addEventListener("DOMContentLoaded", () => {
  renderCartPage();
});
</script>
</body>
</html>
