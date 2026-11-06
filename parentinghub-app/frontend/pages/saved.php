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
<title>Saved Items & Baby Wishlist | ParentingHub Pakistan</title>
<meta name="description" content="View your saved baby feeding bottles, pacifiers, pumps and bookmarked parent community discussions on ParentingHub Pakistan.">
<link rel="canonical" href="https://www.parentinghub.pk/saved">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style.css?v=6.1-card-layout-fix">

<!-- Favicons -->
<link rel="icon" type="image/png" href="favicon.png">
<link rel="icon" type="image/x-icon" href="favicon.ico">
<style>
.saved-hero {
  padding: 44px 0 24px;
  background: radial-gradient(100% 120% at 50% 0%, #fdf5f8 0%, var(--surface) 100%);
  border-bottom: 1px solid var(--line);
}
.saved-tabs-row {
  display: flex;
  gap: 12px;
  margin-top: 24px;
  border-bottom: 2px solid var(--line);
}
.saved-tab-btn {
  padding: 12px 20px;
  font-size: 15px;
  font-weight: 700;
  color: var(--ink-2);
  background: none;
  border: none;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  margin-bottom: -2px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
}
.saved-tab-btn:hover {
  color: var(--primary);
}
.saved-tab-btn.active {
  color: var(--primary);
  border-bottom-color: var(--primary);
}
.saved-tab-badge {
  background: var(--cream);
  color: var(--ink);
  font-size: 11px;
  padding: 2px 8px;
  border-radius: 12px;
  font-weight: 700;
}
.saved-tab-btn.active .saved-tab-badge {
  background: var(--primary-soft);
  color: var(--primary);
}
</style>
</head>
<body data-page="saved">
<div id="chrome-top"></div>

<main>
  <!-- Saved Hero Section -->
  <section class="saved-hero">
    <div class="container">
      <span class="eyebrow dotted">Personal Parenting Archive</span>
      <h1 style="margin-top:12px">Saved Items & Baby Wishlist</h1>
      <p style="max-width:560px">Everything you have shortlisted for your baby — feeding essentials, care items, and helpful community advice saved for quick reference.</p>
      
      <!-- Interactive Tabs -->
      <div class="saved-tabs-row" id="savedTabNav">
        <button class="saved-tab-btn active" data-view="products" onclick="switchSavedTab('products')">
          <span class="ico" data-ico="heart" aria-hidden="true"></span>
          <span>Saved Products (Wishlist)</span>
          <span class="saved-tab-badge" id="badgeProdCount">0</span>
        </button>
        <button class="saved-tab-btn" data-view="community" onclick="switchSavedTab('community')">
          <span class="ico" data-ico="save" aria-hidden="true"></span>
          <span>Saved Discussions & Answers</span>
          <span class="saved-tab-badge" id="badgeCommCount">0</span>
        </button>
      </div>
    </div>
  </section>

  <!-- Container for Tab Contents -->
  <section class="container" style="padding:36px 0 70px">
    
    <!-- Tab 1: Products View -->
    <div id="viewProducts">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <div>
          <h2 style="font-size:18px;margin:0" id="prodSummaryTitle">Saved Baby Essentials</h2>
          <span class="tiny muted">Items you saved with the heart icon while browsing our store</span>
        </div>
        <a href="/shop" class="btn btn-soft btn-s">Continue Shopping →</a>
      </div>

      <div class="grid-products g3" id="savedProductsGrid" style="gap:20px"></div>
    </div>

    <!-- Tab 2: Community Discussions View -->
    <div id="viewCommunity" style="display:none">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <div>
          <h2 style="font-size:18px;margin:0">Bookmarked Discussions & Questions</h2>
          <span class="tiny muted">Advice and answers saved from fellow Pakistani parents</span>
        </div>
        <a href="/community" class="btn btn-soft btn-s">Explore Discussions →</a>
      </div>

      <div id="savedDiscussionsList" style="display:flex;flex-direction:column;gap:14px;max-width:820px"></div>
    </div>

  </section>
</main>

<div id="chrome-bottom"></div>
<script src="js/data.js?v=5.5-product-visual"></script>
<script src="js/app.js?v=5.5-product-visual"></script>
<script src="js/community-db.js?v=1.0"></script>
</body>
</html>
