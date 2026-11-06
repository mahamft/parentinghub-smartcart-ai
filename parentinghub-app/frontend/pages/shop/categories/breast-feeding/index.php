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
<title>Breast Feeding in Pakistan | ParentingHub</title>
<meta name="description" content="Shop Breast Feeding in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<link rel="canonical" href="https://www.parentinghub.pk/shop/categories/breast-feeding">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="Breast Feeding in Pakistan | ParentingHub">
<meta property="og:description" content="Shop Breast Feeding in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta property="og:url" content="https://www.parentinghub.pk/shop/categories/breast-feeding">
<meta property="og:image" content="https://tommeetippee.pk/cdn/shop/files/manual_breast_pump_main_700x778_1ee1eb2c-f1bb-4a9f-85ab-c51f2628747e.jpg?v=1784701566&amp;width=720">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Breast Feeding in Pakistan | ParentingHub">
<meta name="twitter:description" content="Shop Breast Feeding in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta name="twitter:image" content="https://tommeetippee.pk/cdn/shop/files/manual_breast_pump_main_700x778_1ee1eb2c-f1bb-4a9f-85ab-c51f2628747e.jpg?v=1784701566&amp;width=720">

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
<body data-page="shop" data-category="Breast Feeding">
<div id="chrome-top"></div>

<main class="shop-main-wrap">
  <div class="container wide">
    <!-- Slim Mall Assurance Bar (Daraz Style) -->
    <div class="dz-top-nav">
      <nav class="crumbs" style="margin:0"><a href="index.html">Home</a> <span>/</span> <span style="color:var(--ink)">Official Shop</span></nav>
      <h1 class="shop-page-title" style="font-size:20px;margin:8px 0 0;font-weight:700;color:var(--ink)">Breast Feeding in Pakistan</h1>
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
        <div class="grid-products" id="shopGrid"><article class="product-card card rv" data-id="tt-tommee-tippee-manual-breast-pump-portable-lightweight" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-tommee-tippee-manual-breast-pump-portable-lightweight');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-tommee-tippee-manual-breast-pump-portable-lightweight" aria-label="View Tommee Tippee Manual Breast Pump – Portable &amp; Lightweight" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/manual_breast_pump_main_700x778_1ee1eb2c-f1bb-4a9f-85ab-c51f2628747e.jpg?v=1784701566&width=720" alt="Tommee Tippee Manual Breast Pump – Portable &amp; Lightweight" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-tommee-tippee-manual-breast-pump-portable-lightweight', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Tommee Tippee Manual Breast Pump – Portable &amp; Lightweight">
        <a href="/product/tt-tommee-tippee-manual-breast-pump-portable-lightweight" class="product-name-link">Tommee Tippee Manual Breast Pump – Portable &amp; Lightweight</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 11,920</b></span>
        <span class="old-price"><s>Rs 14,900</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Tommee Tippee Manual Breast Pump – Portable &amp; Lightweight to bag" onclick="event.stopPropagation();addToCart('tt-tommee-tippee-manual-breast-pump-portable-lightweight', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-made-for-me-electric-breast-pump" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-made-for-me-electric-breast-pump');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-made-for-me-electric-breast-pump" aria-label="View Made for Me Electric Breast Pump" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/423691_2_fd4bd502-c6ae-4b6c-b97a-ae7c0ba33dd6.jpg?v=1775113408&width=720" alt="Made for Me Electric Breast Pump" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-made-for-me-electric-breast-pump', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Made for Me Electric Breast Pump">
        <a href="/product/tt-made-for-me-electric-breast-pump" class="product-name-link">Made for Me Electric Breast Pump</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 41,600</b></span>
        <span class="old-price"><s>Rs 52,000</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Made for Me Electric Breast Pump to bag" onclick="event.stopPropagation();addToCart('tt-made-for-me-electric-breast-pump', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-express-and-go-storage-case-white-damage-packaging" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-express-and-go-storage-case-white-damage-packaging');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-express-and-go-storage-case-white-damage-packaging" aria-label="View Express and Go Storage Case, White - Damage Packaging" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/21_8a749951-1e91-46be-9359-061069834e07.jpg?v=1756705980&width=720" alt="Express and Go Storage Case, White - Damage Packaging" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 40%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-express-and-go-storage-case-white-damage-packaging', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Express and Go Storage Case, White - Damage Packaging">
        <a href="/product/tt-express-and-go-storage-case-white-damage-packaging" class="product-name-link">Express and Go Storage Case, White - Damage Packaging</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,114</b></span>
        <span class="old-price"><s>Rs 5,190</s></span><span class="discount-pill off">-40%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Express and Go Storage Case, White - Damage Packaging to bag" onclick="event.stopPropagation();addToCart('tt-express-and-go-storage-case-white-damage-packaging', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-manual-breast-pump-tommee-tippee-423697" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-manual-breast-pump-tommee-tippee-423697');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-manual-breast-pump-tommee-tippee-423697" aria-label="View Manual Breast Pump Tommee Tippee 423697" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/135_a7f88c69-c2c7-48d5-9cc7-e73b194ab873.jpg?v=1756705988&width=720" alt="Manual Breast Pump Tommee Tippee 423697" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-manual-breast-pump-tommee-tippee-423697', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Manual Breast Pump Tommee Tippee 423697">
        <a href="/product/tt-manual-breast-pump-tommee-tippee-423697" class="product-name-link">Manual Breast Pump Tommee Tippee 423697</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 11,920</b></span>
        <span class="old-price"><s>Rs 14,900</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Manual Breast Pump Tommee Tippee 423697 to bag" onclick="event.stopPropagation();addToCart('tt-manual-breast-pump-tommee-tippee-423697', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-express-go-breast-pump-adapter-set-damage-packaging" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-express-go-breast-pump-adapter-set-damage-packaging');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-express-go-breast-pump-adapter-set-damage-packaging" aria-label="View Express &amp; Go Breast Pump Adapter Set - Damage Packaging" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/4_178e218e-c42a-40c0-9555-4c3328a331f1.jpg?v=1756705999&width=720" alt="Express &amp; Go Breast Pump Adapter Set - Damage Packaging" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 40%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-express-go-breast-pump-adapter-set-damage-packaging', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Express &amp; Go Breast Pump Adapter Set - Damage Packaging">
        <a href="/product/tt-express-go-breast-pump-adapter-set-damage-packaging" class="product-name-link">Express &amp; Go Breast Pump Adapter Set - Damage Packaging</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 510</b></span>
        <span class="old-price"><s>Rs 850</s></span><span class="discount-pill off">-40%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Express &amp; Go Breast Pump Adapter Set - Damage Packaging to bag" onclick="event.stopPropagation();addToCart('tt-express-go-breast-pump-adapter-set-damage-packaging', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-express-and-go-breast-milk-starter-kit-damage-packaging" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-express-and-go-breast-milk-starter-kit-damage-packaging');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-express-and-go-breast-milk-starter-kit-damage-packaging" aria-label="View Express and Go Breast Milk Starter Kit - Damage Packaging" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/32_f8c14672-10ff-47c0-a3cb-9affec841525.jpg?v=1756706000&width=720" alt="Express and Go Breast Milk Starter Kit - Damage Packaging" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 40%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-express-and-go-breast-milk-starter-kit-damage-packaging', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Express and Go Breast Milk Starter Kit - Damage Packaging">
        <a href="/product/tt-express-and-go-breast-milk-starter-kit-damage-packaging" class="product-name-link">Express and Go Breast Milk Starter Kit - Damage Packaging</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 5,400</b></span>
        <span class="old-price"><s>Rs 9,000</s></span><span class="discount-pill off">-40%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Express and Go Breast Milk Starter Kit - Damage Packaging to bag" onclick="event.stopPropagation();addToCart('tt-express-and-go-breast-milk-starter-kit-damage-packaging', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-made-for-metm-single-electric-breast-pump-223251" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-made-for-metm-single-electric-breast-pump-223251');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-made-for-metm-single-electric-breast-pump-223251" aria-label="View Made for Me™ Single Electric Breast Pump - 223251" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/423691_2_fd4bd502-c6ae-4b6c-b97a-ae7c0ba33dd6.jpg?v=1775113408&width=720" alt="Made for Me™ Single Electric Breast Pump - 223251" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-made-for-metm-single-electric-breast-pump-223251', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Made for Me™ Single Electric Breast Pump - 223251">
        <a href="/product/tt-made-for-metm-single-electric-breast-pump-223251" class="product-name-link">Made for Me™ Single Electric Breast Pump - 223251</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 47,120</b></span>
        <span class="old-price"><s>Rs 58,900</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Made for Me™ Single Electric Breast Pump - 223251 to bag" onclick="event.stopPropagation();addToCart('tt-made-for-metm-single-electric-breast-pump-223251', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-complete-breast-milk-starter-set-express-go-tommee-tippee-423570" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-complete-breast-milk-starter-set-express-go-tommee-tippee-423570');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-complete-breast-milk-starter-set-express-go-tommee-tippee-423570" aria-label="View Complete Breast Milk Starter Set Express &amp; Go Tommee Tippee 423570" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/156.jpg?v=1756706141&width=720" alt="Complete Breast Milk Starter Set Express &amp; Go Tommee Tippee 423570" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-complete-breast-milk-starter-set-express-go-tommee-tippee-423570', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Complete Breast Milk Starter Set Express &amp; Go Tommee Tippee 423570">
        <a href="/product/tt-complete-breast-milk-starter-set-express-go-tommee-tippee-423570" class="product-name-link">Complete Breast Milk Starter Set Express &amp; Go Tommee Tippee 423570</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 25,648</b></span>
        <span class="old-price"><s>Rs 32,060</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Complete Breast Milk Starter Set Express &amp; Go Tommee Tippee 423570 to bag" onclick="event.stopPropagation();addToCart('tt-complete-breast-milk-starter-set-express-go-tommee-tippee-423570', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-silicone-breast-pump-tommee-tippee-223230" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-silicone-breast-pump-tommee-tippee-223230');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-silicone-breast-pump-tommee-tippee-223230" aria-label="View Silicone Breast Pump Tommee Tippee 223230" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/tt_223230.png?v=1756706142&width=720" alt="Silicone Breast Pump Tommee Tippee 223230" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-silicone-breast-pump-tommee-tippee-223230', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Silicone Breast Pump Tommee Tippee 223230">
        <a href="/product/tt-silicone-breast-pump-tommee-tippee-223230" class="product-name-link">Silicone Breast Pump Tommee Tippee 223230</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 7,096</b></span>
        <span class="old-price"><s>Rs 8,870</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Silicone Breast Pump Tommee Tippee 223230 to bag" onclick="event.stopPropagation();addToCart('tt-silicone-breast-pump-tommee-tippee-223230', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-double-electric-breast-pump-tommee-tippee-423698" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-double-electric-breast-pump-tommee-tippee-423698');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-double-electric-breast-pump-tommee-tippee-423698" aria-label="View Double Electric Breast Pump Tommee Tippee 423698" style="display:block;width:100%;height:100%">
        <img src="/api/source-image.php?source=tommee&name=Double+Electric+Breast+Pump+Tommee+Tippee+423698&sku=423698&slot=1" alt="Double Electric Breast Pump Tommee Tippee 423698" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-double-electric-breast-pump-tommee-tippee-423698', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Double Electric Breast Pump Tommee Tippee 423698">
        <a href="/product/tt-double-electric-breast-pump-tommee-tippee-423698" class="product-name-link">Double Electric Breast Pump Tommee Tippee 423698</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 54,592</b></span>
        <span class="old-price"><s>Rs 68,240</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Double Electric Breast Pump Tommee Tippee 423698 to bag" onclick="event.stopPropagation();addToCart('tt-double-electric-breast-pump-tommee-tippee-423698', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-breast-milk-pouch-bottle-180ml-422470-damage-packaging" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-breast-milk-pouch-bottle-180ml-422470-damage-packaging');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-breast-milk-pouch-bottle-180ml-422470-damage-packaging" aria-label="View Breast Milk Pouch Bottle 180ml 422470 - Damage Packaging" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/12_ac45939e-2692-4228-8ca8-9ed1d12ce147.jpg?v=1756706148&width=720" alt="Breast Milk Pouch Bottle 180ml 422470 - Damage Packaging" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 40%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-breast-milk-pouch-bottle-180ml-422470-damage-packaging', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Feeding</span>
      </div>
      <h3 class="product-name name" title="Breast Milk Pouch Bottle 180ml 422470 - Damage Packaging">
        <a href="/product/tt-breast-milk-pouch-bottle-180ml-422470-damage-packaging" class="product-name-link">Breast Milk Pouch Bottle 180ml 422470 - Damage Packaging</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 1,794</b></span>
        <span class="old-price"><s>Rs 2,990</s></span><span class="discount-pill off">-40%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Breast Milk Pouch Bottle 180ml 422470 - Damage Packaging to bag" onclick="event.stopPropagation();addToCart('tt-breast-milk-pouch-bottle-180ml-422470-damage-packaging', 1)">
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
