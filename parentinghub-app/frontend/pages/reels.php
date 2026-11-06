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
<title>Parenting Reels & Practical Baby Video Guides | ParentingHub Pakistan</title>
<meta name="description" content="Short, practical parenting video guides. Real techniques for winding gassy babies, bath time calm, nursery setup and weaning tips from Pakistani parents & pediatric specialists.">
<link rel="canonical" href="https://www.parentinghub.pk/reels">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css?v=5.5-product-visual">

<!-- Favicons -->
<link rel="icon" type="image/png" href="favicon.png">
<link rel="icon" type="image/x-icon" href="favicon.ico">
<style>
body[data-page="reels"]{background:#fff}
.reels-hero{
  padding:28px 0 12px;
  background:linear-gradient(180deg,#fff8fb 0%,#fff 100%);
  border-bottom:1px solid #f2e5eb;
}
.reels-hero .reels-head{
  display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap;
}
.reels-hero h1{margin:6px 0 0;font-size:clamp(1.8rem,3vw,2.55rem);line-height:1.08;letter-spacing:-.035em}
.reels-hero p{margin:8px 0 0;max-width:620px;color:var(--ink-2);font-size:14px;line-height:1.6}
.reels-filter-row{
  display:flex;gap:8px;overflow-x:auto;padding:16px 0 10px;scrollbar-width:none;-webkit-overflow-scrolling:touch;
}
.reels-filter-row::-webkit-scrollbar{display:none}
.reels-filter-row .chip{flex:0 0 auto;white-space:nowrap;min-height:38px;padding:8px 13px;border-radius:999px}
.reels-grid-full{
  display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;padding:22px 0 64px;
}
.reel-item-card{
  appearance:none;border:0;padding:0;text-align:left;position:relative;overflow:hidden;aspect-ratio:9/14;
  border-radius:18px;background:#171019;box-shadow:0 8px 24px rgba(47,27,39,.10);cursor:pointer;
  transition:transform .22s ease,box-shadow .22s ease;width:100%;
}
.reel-item-card:hover{transform:translateY(-3px);box-shadow:0 15px 34px rgba(182,56,102,.18)}
.reel-item-card img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .35s ease}
.reel-item-card:hover img{transform:scale(1.025)}
.reel-item-overlay{
  position:absolute;inset:0;display:flex;flex-direction:column;justify-content:flex-end;gap:7px;padding:16px;
  color:#fff;background:linear-gradient(180deg,rgba(12,8,14,.03) 35%,rgba(12,8,14,.88) 100%);
}
.reel-item-overlay b{font-size:14px!important;line-height:1.35!important;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.reel-item-overlay .tiny{font-size:10.5px!important;color:#f7edf2!important;gap:8px}
.reel-play-float{
  position:absolute;top:50%;left:50%;right:auto;transform:translate(-50%,-50%);width:48px;height:48px;border-radius:50%;
  background:rgba(255,255,255,.92);color:#b63866;display:grid;place-items:center;font-size:16px;box-shadow:0 8px 22px rgba(0,0,0,.15);
  backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);
}
@media(max-width:1050px){.reels-grid-full{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:760px){
  .reels-hero{padding:18px 0 8px}
  .reels-hero .container,.container:has(> .reels-grid-full){padding-inline:14px}
  .reels-hero h1{font-size:1.8rem}
  .reels-hero p{font-size:13px;line-height:1.5}
  .reels-filter-row{margin-inline:-14px;padding-inline:14px;padding-bottom:9px}
  .reels-grid-full{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;padding-top:14px}
  .reel-item-card{border-radius:15px;aspect-ratio:9/14.3}
  .reel-item-overlay{padding:12px;gap:5px}
  .reel-item-overlay b{font-size:12.8px!important}
  .reel-item-overlay .tiny{font-size:9.7px!important}
  .reel-play-float{width:40px;height:40px;font-size:14px}
}
@media(max-width:420px){
  .reels-grid-full{grid-template-columns:1fr;max-width:390px;margin-inline:auto;gap:14px}
  .reel-item-card{aspect-ratio:9/14;border-radius:18px}
  .reel-item-overlay{padding:16px}
  .reel-item-overlay b{font-size:14px!important}
  .reel-play-float{width:46px;height:46px}
}
</style>
</head>
<body data-page="reels">
<div id="chrome-top"></div>

<main>
  <!-- Reels Hero -->
  <section class="reels-hero">
    <div class="container">
      <div class="reels-head">
        <div>
          <span class="eyebrow dotted">ParentingHub Reels</span>
          <h1>Quick Parenting Tips</h1>
          <p>Short, practical baby-care videos you can watch in seconds.</p>
        </div>
      </div>
      
      <!-- Filter Chips -->
      <div class="reels-filter-row" id="reelFilterBar">
        <button class="chip on" data-tag="all">✨ All Reels</button>
        <button class="chip" data-tag="Feeding">🍼 Feeding & Bottles</button>
        <button class="chip" data-tag="Care">🛁 Baby Care & Bath</button>
        <button class="chip" data-tag="Nursery">🛏️ Nursery Setup</button>
        <button class="chip" data-tag="Weaning">🥣 Weaning & Meals</button>
        <button class="chip" data-tag="Travel">🚗 Diaper Bag & Travel</button>
        <button class="chip" data-tag="Newborn">👶 Newborn Hospital Bag</button>
      </div>
    </div>
  </section>

  <!-- Reels Grid Container -->
  <section class="container">
    <div class="reels-grid-full" id="reelsGrid"></div>
  </section>
</main>

<div id="chrome-bottom"></div>
<script src="js/data.js?v=5.5-product-visual"></script>
<script src="js/app.js?v=5.5-product-visual"></script>
<script src="js/community-db.js?v=1.0"></script>
</body>
</html>
