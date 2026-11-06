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
<title>Baby Swings &amp; Bouncers in Pakistan | ParentingHub</title>
<meta name="description" content="Shop Baby Swings &amp; Bouncers in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<link rel="canonical" href="https://www.parentinghub.pk/shop/categories/baby-swings-bouncers">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="Baby Swings &amp; Bouncers in Pakistan | ParentingHub">
<meta property="og:description" content="Shop Baby Swings &amp; Bouncers in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta property="og:url" content="https://www.parentinghub.pk/shop/categories/baby-swings-bouncers">
<meta property="og:image" content="https://www.parentinghub.pk/assets/products/kidilo/kidilo-b01.webp">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Baby Swings &amp; Bouncers in Pakistan | ParentingHub">
<meta name="twitter:description" content="Shop Baby Swings &amp; Bouncers in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta name="twitter:image" content="https://www.parentinghub.pk/assets/products/kidilo/kidilo-b01.webp">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/style.css?v=5.5-product-visual">
  <!-- Favicons -->
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="icon" type="image/png" sizes="64x64" href="assets/favicon-64.png">
  <link rel="icon" type="image/png" sizes="192x192" href="icon-192.png">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png">
  <link rel="shortcut icon" href="favicon.ico">
</head>
<body data-page="shop" data-category="Baby Swings &amp; Bouncers">
<div id="chrome-top"></div>

<main class="shop-main-wrap">
  <div class="container wide">
    <!-- Slim Mall Assurance Bar (Daraz Style) -->
    <div class="dz-top-nav">
      <nav class="crumbs" style="margin:0"><a href="index.html">Home</a> <span>/</span> <span style="color:var(--ink)">Official Shop</span></nav>
      <h1 class="shop-page-title" style="font-size:20px;margin:8px 0 0;font-weight:700;color:var(--ink)">Baby Swings &amp; Bouncers in Pakistan</h1>
      <div class="dz-mall-strip" style="margin-top:8px">
        <span class="dz-mall-tag"><span class="ico" data-ico="shield"></span> ParentingHub Mall</span>
        <span class="dz-mall-item"><span class="ico" data-ico="check"></span> 100% Authentic Authorized Brands</span>
        <span class="dz-mall-item"><span class="ico" data-ico="card"></span> Cash on Delivery Across Pakistan</span>
        <span class="dz-mall-item"><span class="ico" data-ico="truck"></span> Fixed PKR 200 Delivery Per Order</span>
        <span class="dz-mall-item"><span class="ico" data-ico="refresh"></span> 7-Day Easy Returns</span>
      </div>
    </div>

    <!-- Daraz Marketplace 2-Column Layout -->
    <div class="daraz-layout">
      <!-- Left Filter Sidebar -->
      <aside class="daraz-sidebar" id="shopSidebar">
        <div class="dz-sidebar-head">
          <h3>Filters</h3>
          <button class="dz-sidebar-clear" onclick="window.resetShop()">Reset All</button>
          <button class="dz-sidebar-close" id="closeSidebarBtn" aria-label="Close filters"><span class="ico" data-ico="x"></span></button>
        </div>

        <!-- Categories with counts -->
        <div class="dz-filter-group">
          <h4 class="dz-filter-title">Category</h4>
          <div class="dz-cat-list" id="sidebarCatList">
            <!-- Injected dynamically by app.js -->
          </div>
        </div>

        <!-- Brands -->
        <div class="dz-filter-group">
          <h4 class="dz-filter-title">Brand</h4>
          <div class="dz-brand-list" id="sidebarBrandList">
            <!-- Injected dynamically by app.js -->
          </div>
        </div>

        <!-- Price Range (PKR) -->
        <div class="dz-filter-group">
          <h4 class="dz-filter-title">Price (PKR)</h4>
          <div class="dz-price-inputs">
            <input type="number" id="minPriceInput" placeholder="Min" min="0" step="100" aria-label="Minimum price">
            <span>–</span>
            <input type="number" id="maxPriceInput" placeholder="Max" min="0" step="100" aria-label="Maximum price">
            <button id="applyPriceBtn" aria-label="Apply price filter">→</button>
          </div>
        </div>

        <!-- Service & Promotion -->
        <div class="dz-filter-group">
          <h4 class="dz-filter-title">Service & Promotion</h4>
          <div class="dz-options-list">
            <label class="dz-checkbox-label">
              <input type="checkbox" id="chkInStock">
              <span>In Stock Only</span>
            </label>
            <label class="dz-checkbox-label">
              <input type="checkbox" id="chkOnSale">
              <span>Discount Deals</span>
            </label>
          </div>
        </div>

        <!-- Customer Rating -->
        <div class="dz-filter-group" style="border-bottom:none">
          <h4 class="dz-filter-title">Rating</h4>
          <label class="dz-checkbox-label">
            <input type="checkbox" id="chkRating4">
            <span class="dz-stars" style="color:#F59E0B">★ ★ ★ ★ ☆</span>
            <span>4★ & above</span>
          </label>
        </div>
      </aside>

      <!-- Right Catalog Section -->
      <div class="daraz-main">
        <!-- Daraz Control Bar: Counter + Inline Search + Sort + Mobile Filter Button -->
        <div class="dz-controls">
          <div class="dz-res-count" id="resLine">
            <span>Loading catalog products…</span>
          </div>

          <div class="dz-controls-right">
            <div class="dz-search-inline">
              <span class="ico" data-ico="search"></span>
              <input id="shopSearch" placeholder="Search in catalog…" aria-label="Search catalog">
            </div>

            <div class="dz-sort-wrap">
              <span>Sort:</span>
              <select class="dz-sort-select" id="shopSort" aria-label="Sort products">
                <option value="featured">Best Match</option>
                <option value="low">Price: Low to High</option>
                <option value="high">Price: High to Low</option>
                <option value="rating">Top Rated</option>
                <option value="newest">New Arrivals</option>
              </select>
            </div>

            <button class="dz-mobile-filter-btn" id="openSidebarBtn">
              <span class="ico" data-ico="grid"></span> Filters
            </button>
          </div>
        </div>

        <!-- Active Filter Pills -->
        <div class="dz-active-pills" id="activeFilterTags" style="display:none"></div>

        <!-- Product Grid -->
        <div class="grid-products" id="shopGrid"><article class="product-card card rv" data-id="kidilo-b01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kidilo-b01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kidilo-b01" aria-label="View Kidilo B01 Smart Bluetooth Electric Baby Swing &amp; Bassinet Crib" style="display:block;width:100%;height:100%">
        <img src="assets/products/kidilo/kidilo-b01.webp" alt="Kidilo B01 Smart Bluetooth Electric Baby Swing &amp; Bassinet Crib" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 15%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kidilo-b01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo B01 Smart Bluetooth Electric Baby Swing &amp; Bassinet Crib">
        <a href="/product/kidilo-b01" class="product-name-link">Kidilo B01 Smart Bluetooth Electric Baby Swing &amp; Bassinet Crib</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(18 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 29,800</b></span>
        <span class="old-price"><s>Rs 35,000</s></span><span class="discount-pill off">-15%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo B01 Smart Bluetooth Electric Baby Swing &amp; Bassinet Crib to bag" onclick="event.stopPropagation();addToCart('kidilo-b01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kidilo-e04d" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kidilo-e04d');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kidilo-e04d" aria-label="View Kidilo E04D Leaf-Style Ergonomic Electric Baby Bouncer &amp; Swing" style="display:block;width:100%;height:100%">
        <img src="assets/products/kidilo/kidilo-e04d.webp" alt="Kidilo E04D Leaf-Style Ergonomic Electric Baby Bouncer &amp; Swing" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 13%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kidilo-e04d', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo E04D Leaf-Style Ergonomic Electric Baby Bouncer &amp; Swing">
        <a href="/product/kidilo-e04d" class="product-name-link">Kidilo E04D Leaf-Style Ergonomic Electric Baby Bouncer &amp; Swing</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.7</span><span class="count muted">(14 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 14,300</b></span>
        <span class="old-price"><s>Rs 16,500</s></span><span class="discount-pill off">-13%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo E04D Leaf-Style Ergonomic Electric Baby Bouncer &amp; Swing to bag" onclick="event.stopPropagation();addToCart('kidilo-e04d', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kidilo-rc002" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kidilo-rc002');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kidilo-rc002" aria-label="View Kidilo RC002 Comfort Gliding Baby Rocker &amp; Bassinet Seat with Toy Bar" style="display:block;width:100%;height:100%">
        <img src="assets/products/kidilo/kidilo-rc002.webp" alt="Kidilo RC002 Comfort Gliding Baby Rocker &amp; Bassinet Seat with Toy Bar" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 15%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kidilo-rc002', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo RC002 Comfort Gliding Baby Rocker &amp; Bassinet Seat with Toy Bar">
        <a href="/product/kidilo-rc002" class="product-name-link">Kidilo RC002 Comfort Gliding Baby Rocker &amp; Bassinet Seat with Toy Bar</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.8</span><span class="count muted">(9 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 16,499</b></span>
        <span class="old-price"><s>Rs 19,500</s></span><span class="discount-pill off">-15%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo RC002 Comfort Gliding Baby Rocker &amp; Bassinet Seat with Toy Bar to bag" onclick="event.stopPropagation();addToCart('kidilo-rc002', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kidilo-rc003" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kidilo-rc003');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kidilo-rc003" aria-label="View Kidilo RC003 Multi-Position Deluxe Baby Rocker &amp; Bouncer Seat" style="display:block;width:100%;height:100%">
        <img src="assets/products/kidilo/kidilo-rc003.webp" alt="Kidilo RC003 Multi-Position Deluxe Baby Rocker &amp; Bouncer Seat" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 11%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kidilo-rc003', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo RC003 Multi-Position Deluxe Baby Rocker &amp; Bouncer Seat">
        <a href="/product/kidilo-rc003" class="product-name-link">Kidilo RC003 Multi-Position Deluxe Baby Rocker &amp; Bouncer Seat</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.7</span><span class="count muted">(11 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 15,999</b></span>
        <span class="old-price"><s>Rs 18,000</s></span><span class="discount-pill off">-11%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo RC003 Multi-Position Deluxe Baby Rocker &amp; Bouncer Seat to bag" onclick="event.stopPropagation();addToCart('kidilo-rc003', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kidilo-rc001" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kidilo-rc001');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kidilo-rc001" aria-label="View Kidilo RC001 Premium Soothing Bassinet Seat &amp; Baby Swing" style="display:block;width:100%;height:100%">
        <img src="assets/products/kidilo/kidilo-rc001.webp" alt="Kidilo RC001 Premium Soothing Bassinet Seat &amp; Baby Swing" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kidilo-rc001', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo RC001 Premium Soothing Bassinet Seat &amp; Baby Swing">
        <a href="/product/kidilo-rc001" class="product-name-link">Kidilo RC001 Premium Soothing Bassinet Seat &amp; Baby Swing</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.8</span><span class="count muted">(14 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 13,200</b></span>
        <span class="old-price"><s>Rs 16,500</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo RC001 Premium Soothing Bassinet Seat &amp; Baby Swing to bag" onclick="event.stopPropagation();addToCart('kidilo-rc001', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kidilo-y12" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kidilo-y12');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kidilo-y12" aria-label="View Kidilo Y12 Multi-Functional Electric Bedside Crib &amp; Auto Swing" style="display:block;width:100%;height:100%">
        <img src="assets/products/kidilo/kidilo-y12.webp" alt="Kidilo Y12 Multi-Functional Electric Bedside Crib &amp; Auto Swing" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 19%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kidilo-y12', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo Y12 Multi-Functional Electric Bedside Crib &amp; Auto Swing">
        <a href="/product/kidilo-y12" class="product-name-link">Kidilo Y12 Multi-Functional Electric Bedside Crib &amp; Auto Swing</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(19 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 27,500</b></span>
        <span class="old-price"><s>Rs 34,000</s></span><span class="discount-pill off">-19%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo Y12 Multi-Functional Electric Bedside Crib &amp; Auto Swing to bag" onclick="event.stopPropagation();addToCart('kidilo-y12', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kidilo-08016" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kidilo-08016');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kidilo-08016" aria-label="View Kidilo 08016 Ergonomic Natural Bouncer &amp; Rocking Sleeper Seat" style="display:block;width:100%;height:100%">
        <img src="assets/products/kidilo/kidilo-08016.webp" alt="Kidilo 08016 Ergonomic Natural Bouncer &amp; Rocking Sleeper Seat" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 19%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kidilo-08016', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo 08016 Ergonomic Natural Bouncer &amp; Rocking Sleeper Seat">
        <a href="/product/kidilo-08016" class="product-name-link">Kidilo 08016 Ergonomic Natural Bouncer &amp; Rocking Sleeper Seat</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.7</span><span class="count muted">(10 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 12,200</b></span>
        <span class="old-price"><s>Rs 15,000</s></span><span class="discount-pill off">-19%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo 08016 Ergonomic Natural Bouncer &amp; Rocking Sleeper Seat to bag" onclick="event.stopPropagation();addToCart('kidilo-08016', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kidilo-08018" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kidilo-08018');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kidilo-08018" aria-label="View Kidilo 08018 Deluxe Breathable Mesh Baby Rocker &amp; Bouncer" style="display:block;width:100%;height:100%">
        <img src="assets/products/kidilo/kidilo-08018.webp" alt="Kidilo 08018 Deluxe Breathable Mesh Baby Rocker &amp; Bouncer" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 19%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kidilo-08018', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo 08018 Deluxe Breathable Mesh Baby Rocker &amp; Bouncer">
        <a href="/product/kidilo-08018" class="product-name-link">Kidilo 08018 Deluxe Breathable Mesh Baby Rocker &amp; Bouncer</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.8</span><span class="count muted">(12 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 12,200</b></span>
        <span class="old-price"><s>Rs 15,000</s></span><span class="discount-pill off">-19%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo 08018 Deluxe Breathable Mesh Baby Rocker &amp; Bouncer to bag" onclick="event.stopPropagation();addToCart('kidilo-08018', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kidilo-08015" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kidilo-08015');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kidilo-08015" aria-label="View Kidilo 08015 Natural Balance Baby Bouncer &amp; Bassinet Seat" style="display:block;width:100%;height:100%">
        <img src="assets/products/kidilo/kidilo-08015.webp" alt="Kidilo 08015 Natural Balance Baby Bouncer &amp; Bassinet Seat" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 19%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kidilo-08015', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo 08015 Natural Balance Baby Bouncer &amp; Bassinet Seat">
        <a href="/product/kidilo-08015" class="product-name-link">Kidilo 08015 Natural Balance Baby Bouncer &amp; Bassinet Seat</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.8</span><span class="count muted">(15 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 12,200</b></span>
        <span class="old-price"><s>Rs 15,000</s></span><span class="discount-pill off">-19%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo 08015 Natural Balance Baby Bouncer &amp; Bassinet Seat to bag" onclick="event.stopPropagation();addToCart('kidilo-08015', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kdl_rc003" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kdl_rc003');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kdl_rc003" aria-label="View Kidilo RC003 3-in-1 Electric Baby Bouncer &amp; Swing" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/kdl_rc003.webp" alt="Kidilo RC003 3-in-1 Electric Baby Bouncer &amp; Swing" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 21%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kdl_rc003', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo RC003 3-in-1 Electric Baby Bouncer &amp; Swing">
        <a href="/product/kdl_rc003" class="product-name-link">Kidilo RC003 3-in-1 Electric Baby Bouncer &amp; Swing</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(30 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 15,500</b></span>
        <span class="old-price"><s>Rs 19,500</s></span><span class="discount-pill off">-21%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo RC003 3-in-1 Electric Baby Bouncer &amp; Swing to bag" onclick="event.stopPropagation();addToCart('kdl_rc003', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="mst_08087" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('mst_08087');}">
    <div class="product-card-thumb thumb">
      <a href="/product/mst_08087" aria-label="View Mastela 08087 4-in-1 Deluxe Multi-Functional Bassinet &amp; Bouncer" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/mst_08087.webp" alt="Mastela 08087 4-in-1 Deluxe Multi-Functional Bassinet &amp; Bouncer" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 19%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('mst_08087', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Mastela</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Mastela 08087 4-in-1 Deluxe Multi-Functional Bassinet &amp; Bouncer">
        <a href="/product/mst_08087" class="product-name-link">Mastela 08087 4-in-1 Deluxe Multi-Functional Bassinet &amp; Bouncer</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(22 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 19,500</b></span>
        <span class="old-price"><s>Rs 24,000</s></span><span class="discount-pill off">-19%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Mastela 08087 4-in-1 Deluxe Multi-Functional Bassinet &amp; Bouncer to bag" onclick="event.stopPropagation();addToCart('mst_08087', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="mst_08105" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('mst_08105');}">
    <div class="product-card-thumb thumb">
      <a href="/product/mst_08105" aria-label="View Mastela 08105 3-in-1 Deluxe Electric Bassinet &amp; Swing" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/mst_08105.webp" alt="Mastela 08105 3-in-1 Deluxe Electric Bassinet &amp; Swing" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('mst_08105', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Mastela</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Mastela 08105 3-in-1 Deluxe Electric Bassinet &amp; Swing">
        <a href="/product/mst_08105" class="product-name-link">Mastela 08105 3-in-1 Deluxe Electric Bassinet &amp; Swing</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(21 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 17,200</b></span>
        <span class="old-price"><s>Rs 21,500</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Mastela 08105 3-in-1 Deluxe Electric Bassinet &amp; Swing to bag" onclick="event.stopPropagation();addToCart('mst_08105', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="mst_08104" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('mst_08104');}">
    <div class="product-card-thumb thumb">
      <a href="/product/mst_08104" aria-label="View Mastela 08104 3-in-1 Deluxe Multi-Functional Bassinet (Light Grey)" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/mst_08104.webp" alt="Mastela 08104 3-in-1 Deluxe Multi-Functional Bassinet (Light Grey)" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('mst_08104', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Mastela</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Mastela 08104 3-in-1 Deluxe Multi-Functional Bassinet (Light Grey)">
        <a href="/product/mst_08104" class="product-name-link">Mastela 08104 3-in-1 Deluxe Multi-Functional Bassinet (Light Grey)</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(12 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 17,200</b></span>
        <span class="old-price"><s>Rs 21,500</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Mastela 08104 3-in-1 Deluxe Multi-Functional Bassinet (Light Grey) to bag" onclick="event.stopPropagation();addToCart('mst_08104', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="mst_6599" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('mst_6599');}">
    <div class="product-card-thumb thumb">
      <a href="/product/mst_6599" aria-label="View Mastela 6599 Deluxe Portable Automatic Baby Swing (Cool Grey)" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/mst_6599.webp" alt="Mastela 6599 Deluxe Portable Automatic Baby Swing (Cool Grey)" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('mst_6599', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Mastela</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Mastela 6599 Deluxe Portable Automatic Baby Swing (Cool Grey)">
        <a href="/product/mst_6599" class="product-name-link">Mastela 6599 Deluxe Portable Automatic Baby Swing (Cool Grey)</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(19 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 15,200</b></span>
        <span class="old-price"><s>Rs 18,900</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Mastela 6599 Deluxe Portable Automatic Baby Swing (Cool Grey) to bag" onclick="event.stopPropagation();addToCart('mst_6599', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="mst_08037" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('mst_08037');}">
    <div class="product-card-thumb thumb">
      <a href="/product/mst_08037" aria-label="View Mastela 08037 6-in-1 Deluxe Multi-Functional Bassinet &amp; Rocker" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/mst_08037.webp" alt="Mastela 08037 6-in-1 Deluxe Multi-Functional Bassinet &amp; Rocker" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 19%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('mst_08037', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Mastela</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Mastela 08037 6-in-1 Deluxe Multi-Functional Bassinet &amp; Rocker">
        <a href="/product/mst_08037" class="product-name-link">Mastela 08037 6-in-1 Deluxe Multi-Functional Bassinet &amp; Rocker</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(34 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 18,999</b></span>
        <span class="old-price"><s>Rs 23,500</s></span><span class="discount-pill off">-19%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Mastela 08037 6-in-1 Deluxe Multi-Functional Bassinet &amp; Rocker to bag" onclick="event.stopPropagation();addToCart('mst_08037', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="mst_08039" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('mst_08039');}">
    <div class="product-card-thumb thumb">
      <a href="/product/mst_08039" aria-label="View Mastela 08039 6-in-1 Deluxe Bassinet &amp; Rocker (Safari Edition)" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/mst_08039.webp" alt="Mastela 08039 6-in-1 Deluxe Bassinet &amp; Rocker (Safari Edition)" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 19%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('mst_08039', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Mastela</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Mastela 08039 6-in-1 Deluxe Bassinet &amp; Rocker (Safari Edition)">
        <a href="/product/mst_08039" class="product-name-link">Mastela 08039 6-in-1 Deluxe Bassinet &amp; Rocker (Safari Edition)</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(27 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 18,999</b></span>
        <span class="old-price"><s>Rs 23,500</s></span><span class="discount-pill off">-19%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Mastela 08039 6-in-1 Deluxe Bassinet &amp; Rocker (Safari Edition) to bag" onclick="event.stopPropagation();addToCart('mst_08039', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="mst_6511" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('mst_6511');}">
    <div class="product-card-thumb thumb">
      <a href="/product/mst_6511" aria-label="View Mastela 6511 Deluxe Portable Baby Swing (Vanilla Cream)" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/mst_6511.webp" alt="Mastela 6511 Deluxe Portable Baby Swing (Vanilla Cream)" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('mst_6511', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Mastela</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Mastela 6511 Deluxe Portable Baby Swing (Vanilla Cream)">
        <a href="/product/mst_6511" class="product-name-link">Mastela 6511 Deluxe Portable Baby Swing (Vanilla Cream)</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(33 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 15,200</b></span>
        <span class="old-price"><s>Rs 18,900</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Mastela 6511 Deluxe Portable Baby Swing (Vanilla Cream) to bag" onclick="event.stopPropagation();addToCart('mst_6511', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kdl_e04d" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kdl_e04d');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kdl_e04d" aria-label="View Kidilo E04D Smart Electric Baby Rocker &amp; Swing with Touch Panel" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/kdl_e04d.webp" alt="Kidilo E04D Smart Electric Baby Rocker &amp; Swing with Touch Panel" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kdl_e04d', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Baby Swings &amp; Bouncers</span>
      </div>
      <h3 class="product-name name" title="Kidilo E04D Smart Electric Baby Rocker &amp; Swing with Touch Panel">
        <a href="/product/kdl_e04d" class="product-name-link">Kidilo E04D Smart Electric Baby Rocker &amp; Swing with Touch Panel</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(30 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 13,999</b></span>
        <span class="old-price"><s>Rs 17,500</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo E04D Smart Electric Baby Rocker &amp; Swing with Touch Panel to bag" onclick="event.stopPropagation();addToCart('kdl_e04d', 1)">
         Add to Bag
      </button>
    </div>
  </article></div>
      </div>
    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div class="dz-sidebar-scrim" id="sidebarScrim"></div>
  </div>
</main>

<div id="chrome-bottom"></div>
<script src="js/data.js?v=5.5-product-visual"></script>
<script src="js/app.js?v=5.5-product-visual"></script>
<script src="js/milestone-ai.js?v=5.5-product-visual"></script>
</body>
</html>
