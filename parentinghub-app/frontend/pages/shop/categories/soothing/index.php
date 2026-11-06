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
<title>Soothing in Pakistan | ParentingHub</title>
<meta name="description" content="Shop Soothing in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<link rel="canonical" href="https://www.parentinghub.pk/shop/categories/soothing">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="Soothing in Pakistan | ParentingHub">
<meta property="og:description" content="Shop Soothing in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta property="og:url" content="https://www.parentinghub.pk/shop/categories/soothing">
<meta property="og:image" content="https://tommeetippee.pk/cdn/shop/files/TT4_jpg.jpg?v=1784265898&amp;width=720">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Soothing in Pakistan | ParentingHub">
<meta name="twitter:description" content="Shop Soothing in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta name="twitter:image" content="https://tommeetippee.pk/cdn/shop/files/TT4_jpg.jpg?v=1784265898&amp;width=720">

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
<body data-page="shop" data-category="Soothing">
<div id="chrome-top"></div>

<main class="shop-main-wrap">
  <div class="container wide">
    <!-- Slim Mall Assurance Bar (Daraz Style) -->
    <div class="dz-top-nav">
      <nav class="crumbs" style="margin:0"><a href="index.html">Home</a> <span>/</span> <span style="color:var(--ink)">Official Shop</span></nav>
      <h1 class="shop-page-title" style="font-size:20px;margin:8px 0 0;font-weight:700;color:var(--ink)">Soothing in Pakistan</h1>
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
        <div class="grid-products" id="shopGrid"><article class="product-card card rv" data-id="tt-tommee-tippee-newborn-soother-0-2m-pack-of-2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-tommee-tippee-newborn-soother-0-2m-pack-of-2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-tommee-tippee-newborn-soother-0-2m-pack-of-2" aria-label="View Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 1" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/TT4_jpg.jpg?v=1784265898&width=720" alt="Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-tommee-tippee-newborn-soother-0-2m-pack-of-2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 1">
        <a href="/product/tt-tommee-tippee-newborn-soother-0-2m-pack-of-2" class="product-name-link">Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 1</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 2,540</b></span>
        <span class="old-price"><s>Rs 3,175</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 1 to bag" onclick="event.stopPropagation();addToCart('tt-tommee-tippee-newborn-soother-0-2m-pack-of-2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v2" aria-label="View Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/TT4_jpg.jpg?v=1784265898&width=720" alt="Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 2">
        <a href="/product/tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v2" class="product-name-link">Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 2,540</b></span>
        <span class="old-price"><s>Rs 3,175</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 2 to bag" onclick="event.stopPropagation();addToCart('tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v3" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v3');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v3" aria-label="View Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 3" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/TT4_jpg.jpg?v=1784265898&width=720" alt="Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 3" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v3', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 3">
        <a href="/product/tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v3" class="product-name-link">Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 3</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 2,540</b></span>
        <span class="old-price"><s>Rs 3,175</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Tommee Tippee Newborn Soother 0-2M Pack Of 2 — Variant 3 to bag" onclick="event.stopPropagation();addToCart('tt-tommee-tippee-newborn-soother-0-2m-pack-of-2-v3', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-newborn-silicone-soothers-0-2m-pack-of-2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-newborn-silicone-soothers-0-2m-pack-of-2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-newborn-silicone-soothers-0-2m-pack-of-2" aria-label="View Newborn Silicone Soothers 0-2M Pack Of 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458818_1.jpg?v=1784186139&width=720" alt="Newborn Silicone Soothers 0-2M Pack Of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-newborn-silicone-soothers-0-2m-pack-of-2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Newborn Silicone Soothers 0-2M Pack Of 2">
        <a href="/product/tt-newborn-silicone-soothers-0-2m-pack-of-2" class="product-name-link">Newborn Silicone Soothers 0-2M Pack Of 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 2,540</b></span>
        <span class="old-price"><s>Rs 3,175</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Newborn Silicone Soothers 0-2M Pack Of 2 to bag" onclick="event.stopPropagation();addToCart('tt-newborn-silicone-soothers-0-2m-pack-of-2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultra-light-silicone-soother-6-18m-pack-of-2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultra-light-silicone-soother-6-18m-pack-of-2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultra-light-silicone-soother-6-18m-pack-of-2" aria-label="View Ultra Light Silicone Soother 6-18M Pack Of 2 — Variant 1" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458812_2.jpg?v=1784185857&width=720" alt="Ultra Light Silicone Soother 6-18M Pack Of 2 — Variant 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultra-light-silicone-soother-6-18m-pack-of-2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultra Light Silicone Soother 6-18M Pack Of 2 — Variant 1">
        <a href="/product/tt-ultra-light-silicone-soother-6-18m-pack-of-2" class="product-name-link">Ultra Light Silicone Soother 6-18M Pack Of 2 — Variant 1</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,100</b></span>
        <span class="old-price"><s>Rs 3,875</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultra Light Silicone Soother 6-18M Pack Of 2 — Variant 1 to bag" onclick="event.stopPropagation();addToCart('tt-ultra-light-silicone-soother-6-18m-pack-of-2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultra-light-silicone-soother-6-18m-pack-of-2-v2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultra-light-silicone-soother-6-18m-pack-of-2-v2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultra-light-silicone-soother-6-18m-pack-of-2-v2" aria-label="View Ultra Light Silicone Soother 6-18M Pack Of 2 — Variant 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458812_2.jpg?v=1784185857&width=720" alt="Ultra Light Silicone Soother 6-18M Pack Of 2 — Variant 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultra-light-silicone-soother-6-18m-pack-of-2-v2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultra Light Silicone Soother 6-18M Pack Of 2 — Variant 2">
        <a href="/product/tt-ultra-light-silicone-soother-6-18m-pack-of-2-v2" class="product-name-link">Ultra Light Silicone Soother 6-18M Pack Of 2 — Variant 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,100</b></span>
        <span class="old-price"><s>Rs 3,875</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultra Light Silicone Soother 6-18M Pack Of 2 — Variant 2 to bag" onclick="event.stopPropagation();addToCart('tt-ultra-light-silicone-soother-6-18m-pack-of-2-v2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultra-light-silicone-soother-0-6m-pack-of-2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultra-light-silicone-soother-0-6m-pack-of-2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultra-light-silicone-soother-0-6m-pack-of-2" aria-label="View Ultra Light Silicone Soother 0-6M Pack Of 2 — Variant 1" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458810_1.jpg?v=1784182502&width=720" alt="Ultra Light Silicone Soother 0-6M Pack Of 2 — Variant 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultra-light-silicone-soother-0-6m-pack-of-2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultra Light Silicone Soother 0-6M Pack Of 2 — Variant 1">
        <a href="/product/tt-ultra-light-silicone-soother-0-6m-pack-of-2" class="product-name-link">Ultra Light Silicone Soother 0-6M Pack Of 2 — Variant 1</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,100</b></span>
        <span class="old-price"><s>Rs 3,875</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultra Light Silicone Soother 0-6M Pack Of 2 — Variant 1 to bag" onclick="event.stopPropagation();addToCart('tt-ultra-light-silicone-soother-0-6m-pack-of-2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-decorated-ultimate-fit-soother-18-36m-pack-of-2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-decorated-ultimate-fit-soother-18-36m-pack-of-2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-decorated-ultimate-fit-soother-18-36m-pack-of-2" aria-label="View Decorated Ultimate Fit Soother 18-36M Pack Of 2 — Variant 1" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458808_TT2X18-36MULFTSILSTRGLDECUNAS.jpg?v=1784184845&width=720" alt="Decorated Ultimate Fit Soother 18-36M Pack Of 2 — Variant 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-decorated-ultimate-fit-soother-18-36m-pack-of-2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Decorated Ultimate Fit Soother 18-36M Pack Of 2 — Variant 1">
        <a href="/product/tt-decorated-ultimate-fit-soother-18-36m-pack-of-2" class="product-name-link">Decorated Ultimate Fit Soother 18-36M Pack Of 2 — Variant 1</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,072</b></span>
        <span class="old-price"><s>Rs 3,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Decorated Ultimate Fit Soother 18-36M Pack Of 2 — Variant 1 to bag" onclick="event.stopPropagation();addToCart('tt-decorated-ultimate-fit-soother-18-36m-pack-of-2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-decorated-ultimate-fit-soother-18-36m-pack-of-2-v2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-decorated-ultimate-fit-soother-18-36m-pack-of-2-v2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-decorated-ultimate-fit-soother-18-36m-pack-of-2-v2" aria-label="View Decorated Ultimate Fit Soother 18-36M Pack Of 2 — Variant 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458808_TT2X18-36MULFTSILSTRGLDECUNAS.jpg?v=1784184845&width=720" alt="Decorated Ultimate Fit Soother 18-36M Pack Of 2 — Variant 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-decorated-ultimate-fit-soother-18-36m-pack-of-2-v2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Decorated Ultimate Fit Soother 18-36M Pack Of 2 — Variant 2">
        <a href="/product/tt-decorated-ultimate-fit-soother-18-36m-pack-of-2-v2" class="product-name-link">Decorated Ultimate Fit Soother 18-36M Pack Of 2 — Variant 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,072</b></span>
        <span class="old-price"><s>Rs 3,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Decorated Ultimate Fit Soother 18-36M Pack Of 2 — Variant 2 to bag" onclick="event.stopPropagation();addToCart('tt-decorated-ultimate-fit-soother-18-36m-pack-of-2-v2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultimate-fit-latex-soother-6-18m-pack-of-2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultimate-fit-latex-soother-6-18m-pack-of-2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultimate-fit-latex-soother-6-18m-pack-of-2" aria-label="View Ultimate Fit Latex Soother 6-18M Pack Of 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458805_2pk6-18mSoothers_Boy_02.jpg?v=1784183718&width=720" alt="Ultimate Fit Latex Soother 6-18M Pack Of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultimate-fit-latex-soother-6-18m-pack-of-2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultimate Fit Latex Soother 6-18M Pack Of 2">
        <a href="/product/tt-ultimate-fit-latex-soother-6-18m-pack-of-2" class="product-name-link">Ultimate Fit Latex Soother 6-18M Pack Of 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,072</b></span>
        <span class="old-price"><s>Rs 3,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultimate Fit Latex Soother 6-18M Pack Of 2 to bag" onclick="event.stopPropagation();addToCart('tt-ultimate-fit-latex-soother-6-18m-pack-of-2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultimate-fit-latex-soother-0-6m-pack-of-2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultimate-fit-latex-soother-0-6m-pack-of-2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultimate-fit-latex-soother-0-6m-pack-of-2" aria-label="View Ultimate Fit Latex Soother 0-6M Pack Of 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458804_2pk0-6mSoothers_Boy_01.jpg?v=1784183551&width=720" alt="Ultimate Fit Latex Soother 0-6M Pack Of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultimate-fit-latex-soother-0-6m-pack-of-2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultimate Fit Latex Soother 0-6M Pack Of 2">
        <a href="/product/tt-ultimate-fit-latex-soother-0-6m-pack-of-2" class="product-name-link">Ultimate Fit Latex Soother 0-6M Pack Of 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,072</b></span>
        <span class="old-price"><s>Rs 3,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultimate Fit Latex Soother 0-6M Pack Of 2 to bag" onclick="event.stopPropagation();addToCart('tt-ultimate-fit-latex-soother-0-6m-pack-of-2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-night-glow-ultralight-soother-6-18m-pack-of-2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-night-glow-ultralight-soother-6-18m-pack-of-2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-night-glow-ultralight-soother-6-18m-pack-of-2" aria-label="View Night Glow Ultralight Soother 6-18M Pack Of 2 — Variant 1" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/433805_TT2X6-18MULNIGHTGLOWSTHRUNI.jpg?v=1784183018&width=720" alt="Night Glow Ultralight Soother 6-18M Pack Of 2 — Variant 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-night-glow-ultralight-soother-6-18m-pack-of-2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Night Glow Ultralight Soother 6-18M Pack Of 2 — Variant 1">
        <a href="/product/tt-night-glow-ultralight-soother-6-18m-pack-of-2" class="product-name-link">Night Glow Ultralight Soother 6-18M Pack Of 2 — Variant 1</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,100</b></span>
        <span class="old-price"><s>Rs 3,875</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Night Glow Ultralight Soother 6-18M Pack Of 2 — Variant 1 to bag" onclick="event.stopPropagation();addToCart('tt-night-glow-ultralight-soother-6-18m-pack-of-2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-night-glow-ultralight-soother-6-18m-pack-of-2-v2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-night-glow-ultralight-soother-6-18m-pack-of-2-v2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-night-glow-ultralight-soother-6-18m-pack-of-2-v2" aria-label="View Night Glow Ultralight Soother 6-18M Pack Of 2 — Variant 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/433805_TT2X6-18MULNIGHTGLOWSTHRUNI.jpg?v=1784183018&width=720" alt="Night Glow Ultralight Soother 6-18M Pack Of 2 — Variant 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-night-glow-ultralight-soother-6-18m-pack-of-2-v2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Night Glow Ultralight Soother 6-18M Pack Of 2 — Variant 2">
        <a href="/product/tt-night-glow-ultralight-soother-6-18m-pack-of-2-v2" class="product-name-link">Night Glow Ultralight Soother 6-18M Pack Of 2 — Variant 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,100</b></span>
        <span class="old-price"><s>Rs 3,875</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Night Glow Ultralight Soother 6-18M Pack Of 2 — Variant 2 to bag" onclick="event.stopPropagation();addToCart('tt-night-glow-ultralight-soother-6-18m-pack-of-2-v2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultra-light-silicone-soother-0-6m-pack-of-2-v2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultra-light-silicone-soother-0-6m-pack-of-2-v2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultra-light-silicone-soother-0-6m-pack-of-2-v2" aria-label="View Ultra Light Silicone Soother 0-6M Pack Of 2 — Variant 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458810_1.jpg?v=1784182502&width=720" alt="Ultra Light Silicone Soother 0-6M Pack Of 2 — Variant 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultra-light-silicone-soother-0-6m-pack-of-2-v2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultra Light Silicone Soother 0-6M Pack Of 2 — Variant 2">
        <a href="/product/tt-ultra-light-silicone-soother-0-6m-pack-of-2-v2" class="product-name-link">Ultra Light Silicone Soother 0-6M Pack Of 2 — Variant 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,076</b></span>
        <span class="old-price"><s>Rs 3,845</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultra Light Silicone Soother 0-6M Pack Of 2 — Variant 2 to bag" onclick="event.stopPropagation();addToCart('tt-ultra-light-silicone-soother-0-6m-pack-of-2-v2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-tommee-tippee-soother-comfort-clip-0m" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-tommee-tippee-soother-comfort-clip-0m');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-tommee-tippee-soother-comfort-clip-0m" aria-label="View Tommee Tippee Soother Comfort Clip 0m+" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/423780_423785_TT_Comforter-Clip-Cream_02.jpg?v=1784181598&width=720" alt="Tommee Tippee Soother Comfort Clip 0m+" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-tommee-tippee-soother-comfort-clip-0m', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Tommee Tippee Soother Comfort Clip 0m+">
        <a href="/product/tt-tommee-tippee-soother-comfort-clip-0m" class="product-name-link">Tommee Tippee Soother Comfort Clip 0m+</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 5,500</b></span>
        <span class="old-price"><s>Rs 6,875</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Tommee Tippee Soother Comfort Clip 0m+ to bag" onclick="event.stopPropagation();addToCart('tt-tommee-tippee-soother-comfort-clip-0m', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultra-fit-silicone-soother-6-18m-pack-of-2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultra-fit-silicone-soother-6-18m-pack-of-2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultra-fit-silicone-soother-6-18m-pack-of-2" aria-label="View Ultra Fit Silicone Soother 6-18M Pack Of 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458828_1.jpg?v=1784180959&width=720" alt="Ultra Fit Silicone Soother 6-18M Pack Of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultra-fit-silicone-soother-6-18m-pack-of-2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultra Fit Silicone Soother 6-18M Pack Of 2">
        <a href="/product/tt-ultra-fit-silicone-soother-6-18m-pack-of-2" class="product-name-link">Ultra Fit Silicone Soother 6-18M Pack Of 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,072</b></span>
        <span class="old-price"><s>Rs 3,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultra Fit Silicone Soother 6-18M Pack Of 2 to bag" onclick="event.stopPropagation();addToCart('tt-ultra-fit-silicone-soother-6-18m-pack-of-2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultra-fit-silicone-soother-0-6m-pack-of-2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultra-fit-silicone-soother-0-6m-pack-of-2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultra-fit-silicone-soother-0-6m-pack-of-2" aria-label="View Ultra Fit Silicone Soother 0-6M Pack Of 2 — Variant 1" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458826_1.jpg?v=1784180076&width=720" alt="Ultra Fit Silicone Soother 0-6M Pack Of 2 — Variant 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultra-fit-silicone-soother-0-6m-pack-of-2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultra Fit Silicone Soother 0-6M Pack Of 2 — Variant 1">
        <a href="/product/tt-ultra-fit-silicone-soother-0-6m-pack-of-2" class="product-name-link">Ultra Fit Silicone Soother 0-6M Pack Of 2 — Variant 1</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,072</b></span>
        <span class="old-price"><s>Rs 3,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultra Fit Silicone Soother 0-6M Pack Of 2 — Variant 1 to bag" onclick="event.stopPropagation();addToCart('tt-ultra-fit-silicone-soother-0-6m-pack-of-2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultra-fit-silicone-soother-0-6m-pack-of-2-v2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultra-fit-silicone-soother-0-6m-pack-of-2-v2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultra-fit-silicone-soother-0-6m-pack-of-2-v2" aria-label="View Ultra Fit Silicone Soother 0-6M Pack Of 2 — Variant 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/458826_1.jpg?v=1784180076&width=720" alt="Ultra Fit Silicone Soother 0-6M Pack Of 2 — Variant 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultra-fit-silicone-soother-0-6m-pack-of-2-v2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultra Fit Silicone Soother 0-6M Pack Of 2 — Variant 2">
        <a href="/product/tt-ultra-fit-silicone-soother-0-6m-pack-of-2-v2" class="product-name-link">Ultra Fit Silicone Soother 0-6M Pack Of 2 — Variant 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,072</b></span>
        <span class="old-price"><s>Rs 3,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultra Fit Silicone Soother 0-6M Pack Of 2 — Variant 2 to bag" onclick="event.stopPropagation();addToCart('tt-ultra-fit-silicone-soother-0-6m-pack-of-2-v2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultra-fit-decorated-soother-6-18m-433652" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultra-fit-decorated-soother-6-18m-433652');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultra-fit-decorated-soother-6-18m-433652" aria-label="View Ultra Fit Decorated Soother 6-18M - 433652" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/72_59d197d6-04b6-4789-912e-96f31f5f43ea.jpg?v=1765867440&width=720" alt="Ultra Fit Decorated Soother 6-18M - 433652" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultra-fit-decorated-soother-6-18m-433652', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultra Fit Decorated Soother 6-18M - 433652">
        <a href="/product/tt-ultra-fit-decorated-soother-6-18m-433652" class="product-name-link">Ultra Fit Decorated Soother 6-18M - 433652</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,032</b></span>
        <span class="old-price"><s>Rs 3,790</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultra Fit Decorated Soother 6-18M - 433652 to bag" onclick="event.stopPropagation();addToCart('tt-ultra-fit-decorated-soother-6-18m-433652', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultra-fit-decorated-soother-18-36m-433657" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultra-fit-decorated-soother-18-36m-433657');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultra-fit-decorated-soother-18-36m-433657" aria-label="View Ultra Fit Decorated Soother 18-36M - 433657" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/34_e2941846-6c38-457c-9392-371fbc3edec0.jpg?v=1765866953&width=720" alt="Ultra Fit Decorated Soother 18-36M - 433657" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultra-fit-decorated-soother-18-36m-433657', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultra Fit Decorated Soother 18-36M - 433657">
        <a href="/product/tt-ultra-fit-decorated-soother-18-36m-433657" class="product-name-link">Ultra Fit Decorated Soother 18-36M - 433657</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,032</b></span>
        <span class="old-price"><s>Rs 3,790</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultra Fit Decorated Soother 18-36M - 433657 to bag" onclick="event.stopPropagation();addToCart('tt-ultra-fit-decorated-soother-18-36m-433657', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultimate-fit-soother-6-18m-433651" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultimate-fit-soother-6-18m-433651');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultimate-fit-soother-6-18m-433651" aria-label="View Ultimate Fit Soother 6-18M - 433651 — Variant 1" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/60_b0ccac74-fd77-4542-babe-fa20a078a711.jpg?v=1765866302&width=720" alt="Ultimate Fit Soother 6-18M - 433651 — Variant 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultimate-fit-soother-6-18m-433651', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultimate Fit Soother 6-18M - 433651 — Variant 1">
        <a href="/product/tt-ultimate-fit-soother-6-18m-433651" class="product-name-link">Ultimate Fit Soother 6-18M - 433651 — Variant 1</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,072</b></span>
        <span class="old-price"><s>Rs 3,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultimate Fit Soother 6-18M - 433651 — Variant 1 to bag" onclick="event.stopPropagation();addToCart('tt-ultimate-fit-soother-6-18m-433651', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-tommee-tippee-ultimate-fit-soother-6-18m-433651" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-tommee-tippee-ultimate-fit-soother-6-18m-433651');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-tommee-tippee-ultimate-fit-soother-6-18m-433651" aria-label="View Tommee Tippee Ultimate Fit Soother 6-18M - 433651" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/60_b0ccac74-fd77-4542-babe-fa20a078a711.jpg?v=1765866302&width=720" alt="Tommee Tippee Ultimate Fit Soother 6-18M - 433651" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-tommee-tippee-ultimate-fit-soother-6-18m-433651', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Tommee Tippee Ultimate Fit Soother 6-18M - 433651">
        <a href="/product/tt-tommee-tippee-ultimate-fit-soother-6-18m-433651" class="product-name-link">Tommee Tippee Ultimate Fit Soother 6-18M - 433651</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,072</b></span>
        <span class="old-price"><s>Rs 3,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Tommee Tippee Ultimate Fit Soother 6-18M - 433651 to bag" onclick="event.stopPropagation();addToCart('tt-tommee-tippee-ultimate-fit-soother-6-18m-433651', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultimate-fit-soother-6-18m-433651-v2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultimate-fit-soother-6-18m-433651-v2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultimate-fit-soother-6-18m-433651-v2" aria-label="View Ultimate Fit Soother 6-18M - 433651 — Variant 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/60_b0ccac74-fd77-4542-babe-fa20a078a711.jpg?v=1765866302&width=720" alt="Ultimate Fit Soother 6-18M - 433651 — Variant 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultimate-fit-soother-6-18m-433651-v2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultimate Fit Soother 6-18M - 433651 — Variant 2">
        <a href="/product/tt-ultimate-fit-soother-6-18m-433651-v2" class="product-name-link">Ultimate Fit Soother 6-18M - 433651 — Variant 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,072</b></span>
        <span class="old-price"><s>Rs 3,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultimate Fit Soother 6-18M - 433651 — Variant 2 to bag" onclick="event.stopPropagation();addToCart('tt-ultimate-fit-soother-6-18m-433651-v2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultimate-fit-soother-0-6m" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultimate-fit-soother-0-6m');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultimate-fit-soother-0-6m" aria-label="View Ultimate Fit Soother 0-6M" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/68_fc2cebf5-1cf8-4f78-b2d9-febd86a52d3e.jpg?v=1765865910&width=720" alt="Ultimate Fit Soother 0-6M" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultimate-fit-soother-0-6m', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Soothing</span>
      </div>
      <h3 class="product-name name" title="Ultimate Fit Soother 0-6M">
        <a href="/product/tt-ultimate-fit-soother-0-6m" class="product-name-link">Ultimate Fit Soother 0-6M</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,032</b></span>
        <span class="old-price"><s>Rs 3,790</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultimate Fit Soother 0-6M to bag" onclick="event.stopPropagation();addToCart('tt-ultimate-fit-soother-0-6m', 1)">
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
