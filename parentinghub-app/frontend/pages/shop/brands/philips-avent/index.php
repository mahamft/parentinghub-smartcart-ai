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
<title>Philips Avent in Pakistan | ParentingHub</title>
<meta name="description" content="Shop Philips Avent in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<link rel="canonical" href="https://www.parentinghub.pk/shop/brands/philips-avent">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="Philips Avent in Pakistan | ParentingHub">
<meta property="og:description" content="Shop Philips Avent in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta property="og:url" content="https://www.parentinghub.pk/shop/brands/philips-avent">
<meta property="og:image" content="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF619-05.webp">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Philips Avent in Pakistan | ParentingHub">
<meta name="twitter:description" content="Shop Philips Avent in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta name="twitter:image" content="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF619-05.webp">

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
<body data-page="shop" data-brand="Philips Avent">
<div id="chrome-top"></div>

<main class="shop-main-wrap">
  <div class="container wide">
    <!-- Slim Mall Assurance Bar (Daraz Style) -->
    <div class="dz-top-nav">
      <nav class="crumbs" style="margin:0"><a href="index.html">Home</a> <span>/</span> <span style="color:var(--ink)">Official Shop</span></nav>
      <h1 class="shop-page-title" style="font-size:20px;margin:8px 0 0;font-weight:700;color:var(--ink)">Philips Avent in Pakistan</h1>
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
        <div class="grid-products" id="shopGrid"><article class="product-card card rv" data-id="philips-avent-scf619-05" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf619-05');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf619-05" aria-label="View 5 × 180 ml Breast Milk Storage Cups" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF619-05.webp" alt="5 × 180 ml Breast Milk Storage Cups" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        <span class="p-badge p-badge-bestseller tag tag-best">Bestseller</span>
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf619-05', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Accessories</span>
      </div>
      <h3 class="product-name name" title="5 × 180 ml Breast Milk Storage Cups">
        <a href="/product/philips-avent-scf619-05" class="product-name-link">5 × 180 ml Breast Milk Storage Cups</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF619/05</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 5,835</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add 5 × 180 ml Breast Milk Storage Cups to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf619-05', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf355-09" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf355-09');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf355-09" aria-label="View Advance Fast Bottle Warmer" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF355-09.webp" alt="Advance Fast Bottle Warmer" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf355-09', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="Advance Fast Bottle Warmer">
        <a href="/product/philips-avent-scf355-09" class="product-name-link">Advance Fast Bottle Warmer</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF355/09</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 32,380</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Advance Fast Bottle Warmer to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf355-09', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy100-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy100-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy100-01" aria-label="View Anti-Colic Bottle 125ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY100-01-1.webp" alt="Anti-Colic Bottle 125ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy100-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Anti-Colic Bottle 125ml Pack of 1">
        <a href="/product/philips-avent-scy100-01" class="product-name-link">Anti-Colic Bottle 125ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY100/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 5,220</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Anti-Colic Bottle 125ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy100-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy103-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy103-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy103-01" aria-label="View Anti-Colic Bottle 260ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY103-01-1.webp" alt="Anti-Colic Bottle 260ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy103-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Anti-Colic Bottle 260ml Pack of 1">
        <a href="/product/philips-avent-scy103-01" class="product-name-link">Anti-Colic Bottle 260ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY103/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 5,510</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Anti-Colic Bottle 260ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy103-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy106-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy106-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy106-01" aria-label="View Anti-Colic Bottle 330ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY106-01-1.webp" alt="Anti-Colic Bottle 330ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy106-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Anti-Colic Bottle 330ml Pack of 1">
        <a href="/product/philips-avent-scy106-01" class="product-name-link">Anti-Colic Bottle 330ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY106/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 6,380</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Anti-Colic Bottle 330ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy106-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy106-02" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy106-02');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy106-02" aria-label="View Anti-Colic Bottle 330ml Pack of 2" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/07/SCY106-02-1.jpg" alt="Anti-Colic Bottle 330ml Pack of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy106-02', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Anti-Colic Bottle 330ml Pack of 2">
        <a href="/product/philips-avent-scy106-02" class="product-name-link">Anti-Colic Bottle 330ml Pack of 2</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY106/02</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 12,615</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Anti-Colic Bottle 330ml Pack of 2 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy106-02', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scd180-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scd180-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scd180-01" aria-label="View Anti-Colic Gift Set" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/07/SCD180-01.jpg" alt="Anti-Colic Gift Set" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scd180-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Gift Sets</span>
      </div>
      <h3 class="product-name name" title="Anti-Colic Gift Set">
        <a href="/product/philips-avent-scd180-01" class="product-name-link">Anti-Colic Gift Set</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCD180/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 24,360</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Anti-Colic Gift Set to bag" onclick="event.stopPropagation();addToCart('philips-avent-scd180-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy761-02" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy761-02');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy761-02" aria-label="View Anti-Colic Teat 0m+ Pack of 2" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY761-02.webp" alt="Anti-Colic Teat 0m+ Pack of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy761-02', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Teats</span>
      </div>
      <h3 class="product-name name" title="Anti-Colic Teat 0m+ Pack of 2">
        <a href="/product/philips-avent-scy761-02" class="product-name-link">Anti-Colic Teat 0m+ Pack of 2</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY761/02</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,950</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Anti-Colic Teat 0m+ Pack of 2 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy761-02', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy762-02" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy762-02');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy762-02" aria-label="View Anti-Colic Teat 1m+ Pack of 2" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/05/SCY762-02.webp" alt="Anti-Colic Teat 1m+ Pack of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy762-02', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Teats</span>
      </div>
      <h3 class="product-name name" title="Anti-Colic Teat 1m+ Pack of 2">
        <a href="/product/philips-avent-scy762-02" class="product-name-link">Anti-Colic Teat 1m+ Pack of 2</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY762/02</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,950</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Anti-Colic Teat 1m+ Pack of 2 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy762-02', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy763-02" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy763-02');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy763-02" aria-label="View Anti-Colic Teat 3m+ Pack of 2" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY763-02.webp" alt="Anti-Colic Teat 3m+ Pack of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy763-02', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Teats</span>
      </div>
      <h3 class="product-name name" title="Anti-Colic Teat 3m+ Pack of 2">
        <a href="/product/philips-avent-scy763-02" class="product-name-link">Anti-Colic Teat 3m+ Pack of 2</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY763/02</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,950</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Anti-Colic Teat 3m+ Pack of 2 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy763-02', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy764-02" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy764-02');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy764-02" aria-label="View Anti-Colic Teat 6m+ Pack of 2" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY764-02.webp" alt="Anti-Colic Teat 6m+ Pack of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy764-02', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Teats</span>
      </div>
      <h3 class="product-name name" title="Anti-Colic Teat 6m+ Pack of 2">
        <a href="/product/philips-avent-scy764-02" class="product-name-link">Anti-Colic Teat 6m+ Pack of 2</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY764/02</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 3,950</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Anti-Colic Teat 6m+ Pack of 2 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy764-02', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf145-06" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf145-06');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf145-06" aria-label="View Bottle Brush 1pk (Blue)" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/03/SCF145-06.webp" alt="Bottle Brush 1pk (Blue)" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf145-06', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Accessories</span>
      </div>
      <h3 class="product-name name" title="Bottle Brush 1pk (Blue)">
        <a href="/product/philips-avent-scf145-06" class="product-name-link">Bottle Brush 1pk (Blue)</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF145/06</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 4,450</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Bottle Brush 1pk (Blue) to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf145-06', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf145-07" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf145-07');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf145-07" aria-label="View Bottle Brush 1pk (Pink)" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF145-07.webp" alt="Bottle Brush 1pk (Pink)" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        <span class="p-badge p-badge-bestseller tag tag-best">Bestseller</span>
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf145-07', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Accessories</span>
      </div>
      <h3 class="product-name name" title="Bottle Brush 1pk (Pink)">
        <a href="/product/philips-avent-scf145-07" class="product-name-link">Bottle Brush 1pk (Pink)</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF145/07</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 4,450</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Bottle Brush 1pk (Pink) to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf145-07', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-citric-acid-10g" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-citric-acid-10g');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-citric-acid-10g" aria-label="View Citric Acid 10g" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/Citric-Acid.webp" alt="Citric Acid 10g" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-citric-acid-10g', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Accessories</span>
      </div>
      <h3 class="product-name name" title="Citric Acid 10g">
        <a href="/product/philips-avent-citric-acid-10g" class="product-name-link">Citric Acid 10g</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 500</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Citric Acid 10g to bag" onclick="event.stopPropagation();addToCart('philips-avent-citric-acid-10g', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf551-05" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf551-05');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf551-05" aria-label="View Classic Spout Cup 7OZ Green" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF551-05-1.webp" alt="Classic Spout Cup 7OZ Green" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf551-05', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Non-Spill Cups</span>
      </div>
      <h3 class="product-name name" title="Classic Spout Cup 7OZ Green">
        <a href="/product/philips-avent-scf551-05" class="product-name-link">Classic Spout Cup 7OZ Green</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF551/05</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 4,860</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Classic Spout Cup 7OZ Green to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf551-05', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf551-03" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf551-03');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf551-03" aria-label="View Classic Spout Cup 7OZ Pink" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF551-03-1.webp" alt="Classic Spout Cup 7OZ Pink" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        <span class="p-badge p-badge-new tag tag-new">New</span>
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf551-03', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Non-Spill Cups</span>
      </div>
      <h3 class="product-name name" title="Classic Spout Cup 7OZ Pink">
        <a href="/product/philips-avent-scf551-03" class="product-name-link">Classic Spout Cup 7OZ Pink</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF551/03</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 4,860</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Classic Spout Cup 7OZ Pink to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf551-03', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf430-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf430-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf430-01" aria-label="View Comfort Manual Breast Pump" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF430-01-1.webp" alt="Comfort Manual Breast Pump" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf430-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Care</span>
      </div>
      <h3 class="product-name name" title="Comfort Manual Breast Pump">
        <a href="/product/philips-avent-scf430-01" class="product-name-link">Comfort Manual Breast Pump</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF430/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 25,930</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Comfort Manual Breast Pump to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf430-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf099-21" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf099-21');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf099-21" aria-label="View Curved Soothie 0-6M (SCF099/21) Boy" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF099-21.webp" alt="Curved Soothie 0-6M (SCF099/21) Boy" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf099-21', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Pacifiers</span>
      </div>
      <h3 class="product-name name" title="Curved Soothie 0-6M (SCF099/21) Boy">
        <a href="/product/philips-avent-scf099-21" class="product-name-link">Curved Soothie 0-6M (SCF099/21) Boy</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 6,265</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Curved Soothie 0-6M (SCF099/21) Boy to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf099-21', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf099-22" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf099-22');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf099-22" aria-label="View Curved Soothie 0-6M (SCF099/22) Girl" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF099-22.webp" alt="Curved Soothie 0-6M (SCF099/22) Girl" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf099-22', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Pacifiers</span>
      </div>
      <h3 class="product-name name" title="Curved Soothie 0-6M (SCF099/22) Girl">
        <a href="/product/philips-avent-scf099-22" class="product-name-link">Curved Soothie 0-6M (SCF099/22) Girl</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 6,265</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Curved Soothie 0-6M (SCF099/22) Girl to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf099-22', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf254-10" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf254-10');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf254-10" aria-label="View Disposable Breast Pads Pack of 100 (Day)" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF254-61.webp" alt="Disposable Breast Pads Pack of 100 (Day)" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf254-10', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Care</span>
      </div>
      <h3 class="product-name name" title="Disposable Breast Pads Pack of 100 (Day)">
        <a href="/product/philips-avent-scf254-10" class="product-name-link">Disposable Breast Pads Pack of 100 (Day)</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF254/10</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 10,610</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Disposable Breast Pads Pack of 100 (Day) to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf254-10', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf254-61" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf254-61');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf254-61" aria-label="View Disposable Breast Pads Pack of 60 (Day)" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF254-61.webp" alt="Disposable Breast Pads Pack of 60 (Day)" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf254-61', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Care</span>
      </div>
      <h3 class="product-name name" title="Disposable Breast Pads Pack of 60 (Day)">
        <a href="/product/philips-avent-scf254-61" class="product-name-link">Disposable Breast Pads Pack of 60 (Day)</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF254/61</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 7,970</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Disposable Breast Pads Pack of 60 (Day) to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf254-61', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf358-00" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf358-00');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf358-00" aria-label="View Fast Bottle Warmer" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF358-00-1.webp" alt="Fast Bottle Warmer" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf358-00', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="Fast Bottle Warmer">
        <a href="/product/philips-avent-scf358-00" class="product-name-link">Fast Bottle Warmer</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF358/00</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 39,590</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Fast Bottle Warmer to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf358-00', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf900-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf900-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf900-01" aria-label="View Manual Breast Pump" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF900-01.webp" alt="Manual Breast Pump" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf900-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Care</span>
      </div>
      <h3 class="product-name name" title="Manual Breast Pump">
        <a href="/product/philips-avent-scf900-01" class="product-name-link">Manual Breast Pump</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF900/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 23,745</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Manual Breast Pump to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf900-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf330-13" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf330-13');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf330-13" aria-label="View Manual breast pump with 3 cups" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF330-13.webp" alt="Manual breast pump with 3 cups" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf330-13', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Breast Care</span>
      </div>
      <h3 class="product-name name" title="Manual breast pump with 3 cups">
        <a href="/product/philips-avent-scf330-13" class="product-name-link">Manual breast pump with 3 cups</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF330/13</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 26,660</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Manual breast pump with 3 cups to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf330-13', 1)">
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
