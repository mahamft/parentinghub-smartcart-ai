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
<title>Track Your Order | ParentingHub Pakistan</title>
<meta name="description" content="Track your ParentingHub baby essentials parcel in real-time across Pakistan. Enter Order ID or phone number.">
<link rel="canonical" href="https://www.parentinghub.pk/track">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css?v=5.5-product-visual">

<!-- Favicons -->
<link rel="icon" type="image/png" href="favicon.png">
<link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body data-page="track">
<div id="chrome-top"></div>

<main style="padding:44px 0 70px;background:var(--cream);min-height:80vh">
  <div class="container" style="max-width:760px">
    
    <div style="text-align:center;margin-bottom:32px">
      <span class="eyebrow dotted">Dispatch & Parcel Updates</span>
      <h1 style="margin:10px 0 6px;font-size:30px">Track Your Order</h1>
      <p class="muted small" style="max-width:480px;margin:0 auto">Enter your Order ID (from SMS or receipt) or phone number to check current dispatch and courier progress.</p>
    </div>

    <!-- Search Box -->
    <div class="section-card" style="padding:28px;background:var(--surface);border-radius:22px;box-shadow:var(--sh-m);margin-bottom:28px">
      <form onsubmit="event.preventDefault();searchOrder();" style="display:flex;gap:12px;flex-wrap:wrap">
        <input type="text" id="trackInput" placeholder="e.g. PH-1042 or 03293007360" required style="flex-grow:1;padding:12px 18px;border:1px solid var(--line);border-radius:12px;font-size:15px">
        <button type="submit" class="btn btn-primary" style="padding:12px 24px;font-size:15px">
          Track Order
        </button>
      </form>
    </div>

    <!-- Results Section -->
    <div id="trackingResultArea"></div>

    <!-- Recent Orders from Session / Storage -->
    <div id="recentOrdersSection" style="margin-top:28px">
      <h3 style="font-size:18px;margin:0 0 14px">Your Placed Orders</h3>
      <div id="recentOrdersList"></div>
    </div>

  </div>
</main>

<div id="chrome-bottom"></div>
<script src="js/data.js?v=5.5-product-visual"></script>
<script src="js/app.js?v=5.5-product-visual"></script>
<script>
function renderRecentOrders(){
  const listEl = document.getElementById("recentOrdersList");
  const orders = store.get("orders", []);

  if(!orders.length){
    listEl.innerHTML = `
      <div style="text-align:center;padding:36px 16px;background:var(--surface);border-radius:18px;border:1px dashed var(--line)">
        <p class="muted small" style="margin:0">No recent orders found on this device. Place your first order with Cash on Delivery!</p>
        <a href="/shop" class="btn btn-soft btn-xs" style="margin-top:12px">Explore Shop</a>
      </div>
    `;
    return;
  }

  listEl.innerHTML = orders.map(ord => renderOrderCard(ord)).join("");
}

function renderOrderCard(ord){
  const d = new Date(ord.date).toLocaleDateString("en-PK", { day: 'numeric', month: 'short', year: 'numeric' });
  
  return `
    <div class="panel" style="background:var(--surface);margin-bottom:16px;border:1px solid var(--line);border-radius:18px;padding:20px;box-shadow:var(--sh-s)">
      <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--line);padding-bottom:12px;margin-bottom:12px;flex-wrap:wrap;gap:8px">
        <div>
          <b style="font-size:17px;color:var(--primary)">#${ord.id}</b>
          <span class="tiny muted" style="margin-left:8px">${d}</span>
        </div>
        <span class="badge" style="background:#EAF7EE;color:#1E8E3E;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700">
          ✓ ${ord.status || 'Confirmed & Dispatched'}
        </span>
      </div>

      <div style="font-size:13.5px;line-height:1.6;color:var(--ink-2);margin-bottom:14px">
        ${ord.items.map(it => `<div>• <b>${it.name}</b> × ${it.qty} (${money(it.price * it.qty)})</div>`).join("")}
      </div>

      <!-- Timeline progress -->
      <div style="margin:16px 0 14px;background:var(--cream);padding:14px;border-radius:14px">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);text-align:center;gap:6px;font-size:11px;font-weight:700">
          <div style="color:var(--green)">✓ Confirmed</div>
          <div style="color:var(--green)">✓ Packed</div>
          <div style="color:var(--primary)">🚚 Dispatched</div>
          <div style="color:var(--ink-3)">○ Delivery</div>
        </div>
      </div>

      <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--line);padding-top:12px;flex-wrap:wrap;gap:10px">
        <div style="font-size:14px">
          Total: <b style="font-size:16px;color:var(--ink)">${money(ord.total)}</b> 
          <span class="tiny muted">(${ord.paymentMethod})</span>
        </div>
        <a href="mailto:support@parentinghub.pk" class="btn btn-ghost btn-xs">Email Support ↗</a>
      </div>
    </div>
  `;
}

function searchOrder(){
  const query = document.getElementById("trackInput").value.trim().toLowerCase().replace("#", "");
  const orders = store.get("orders", []);
  const area = document.getElementById("trackingResultArea");

  const match = orders.find(o => String(o.id).toLowerCase().includes(query) || (o.customer && (String(o.customer.phone).includes(query) || String(o.customer.email).toLowerCase().includes(query))));

  if(match){
    area.innerHTML = `
      <div style="margin-bottom:20px">
        <span class="badge" style="background:var(--green-soft);color:var(--green);font-size:12px;padding:4px 12px;border-radius:20px;font-weight:700;margin-bottom:8px;display:inline-block">Found 1 matching parcel</span>
        ${renderOrderCard(match)}
      </div>
    `;
    area.scrollIntoView({ behavior: "smooth", block: "start" });
  } else {
    area.innerHTML = `
      <div class="panel" style="background:var(--surface);border:1px solid var(--line);border-radius:18px;padding:24px;text-align:center;margin-bottom:20px">
        <b style="font-size:16px;display:block;margin-bottom:6px">No order found for "${query}"</b>
        <p class="muted small" style="margin:0 0 14px">Please verify the Order ID from your confirmation SMS or check with our team by email.</p>
        <a href="mailto:support@parentinghub.pk" target="_blank" class="btn btn-primary btn-xs">Email Support</a>
      </div>
    `;
  }
}

document.addEventListener("DOMContentLoaded", () => {
  renderRecentOrders();
  const q = new URLSearchParams(location.search).get("id");
  if(q){
    document.getElementById("trackInput").value = q;
    searchOrder();
  }
});
</script>
</body>
</html>
