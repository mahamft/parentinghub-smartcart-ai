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
<title>Feeding Bottles in Pakistan | ParentingHub</title>
<meta name="description" content="Shop Feeding Bottles in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<link rel="canonical" href="https://www.parentinghub.pk/shop/categories/feeding-bottles">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="Feeding Bottles in Pakistan | ParentingHub">
<meta property="og:description" content="Shop Feeding Bottles in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta property="og:url" content="https://www.parentinghub.pk/shop/categories/feeding-bottles">
<meta property="og:image" content="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY100-01-1.webp">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Feeding Bottles in Pakistan | ParentingHub">
<meta name="twitter:description" content="Shop Feeding Bottles in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta name="twitter:image" content="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY100-01-1.webp">

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
<body data-page="shop" data-category="Feeding Bottles">
<div id="chrome-top"></div>

<main class="shop-main-wrap">
  <div class="container wide">
    <!-- Slim Mall Assurance Bar (Daraz Style) -->
    <div class="dz-top-nav">
      <nav class="crumbs" style="margin:0"><a href="index.html">Home</a> <span>/</span> <span style="color:var(--ink)">Official Shop</span></nav>
      <h1 class="shop-page-title" style="font-size:20px;margin:8px 0 0;font-weight:700;color:var(--ink)">Feeding Bottles in Pakistan</h1>
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
        <div class="grid-products" id="shopGrid"><article class="product-card card rv" data-id="philips-avent-scy100-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy100-01');}">
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
  </article><article class="product-card card rv" data-id="philips-avent-scy670-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy670-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy670-01" aria-label="View Natural Bottle 125ml Pack of 1 (SCY670/01) with Airfree vent" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY670-01-1.webp" alt="Natural Bottle 125ml Pack of 1 (SCY670/01) with Airfree vent" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy670-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 125ml Pack of 1 (SCY670/01) with Airfree vent">
        <a href="/product/philips-avent-scy670-01" class="product-name-link">Natural Bottle 125ml Pack of 1 (SCY670/01) with Airfree vent</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 7,240</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 125ml Pack of 1 (SCY670/01) with Airfree vent to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy670-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy900-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy900-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy900-01" aria-label="View Natural Bottle 125ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY900-01-1.webp" alt="Natural Bottle 125ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy900-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 125ml Pack of 1">
        <a href="/product/philips-avent-scy900-01" class="product-name-link">Natural Bottle 125ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY900/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 6,080</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 125ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy900-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy900-02" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy900-02');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy900-02" aria-label="View Natural Bottle 125ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY900-02.webp" alt="Natural Bottle 125ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        <span class="p-badge p-badge-new tag tag-new">New</span>
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy900-02', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 125ml Pack of 1">
        <a href="/product/philips-avent-scy900-02" class="product-name-link">Natural Bottle 125ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY900/02</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 12,120</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 125ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy900-02', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy670-02" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy670-02');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy670-02" aria-label="View Natural Bottle 125ml Pack of 2 (SCY670/02) with Airfree vent" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/07/SCY670-02-1.jpg" alt="Natural Bottle 125ml Pack of 2 (SCY670/02) with Airfree vent" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy670-02', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 125ml Pack of 2 (SCY670/02) with Airfree vent">
        <a href="/product/philips-avent-scy670-02" class="product-name-link">Natural Bottle 125ml Pack of 2 (SCY670/02) with Airfree vent</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 14,280</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 125ml Pack of 2 (SCY670/02) with Airfree vent to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy670-02', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy673-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy673-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy673-01" aria-label="View Natural Bottle 260ml Pack of 1 (SCY673/01) with Airfree vent" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY673-01-1.webp" alt="Natural Bottle 260ml Pack of 1 (SCY673/01) with Airfree vent" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy673-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 260ml Pack of 1 (SCY673/01) with Airfree vent">
        <a href="/product/philips-avent-scy673-01" class="product-name-link">Natural Bottle 260ml Pack of 1 (SCY673/01) with Airfree vent</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 7,820</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 260ml Pack of 1 (SCY673/01) with Airfree vent to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy673-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy673-81" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy673-81');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy673-81" aria-label="View Natural Bottle 260ml Pack of 1 (SCY673/81) with Airfree vent" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY673-81-1.webp" alt="Natural Bottle 260ml Pack of 1 (SCY673/81) with Airfree vent" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy673-81', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 260ml Pack of 1 (SCY673/81) with Airfree vent">
        <a href="/product/philips-avent-scy673-81" class="product-name-link">Natural Bottle 260ml Pack of 1 (SCY673/81) with Airfree vent</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 7,760</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 260ml Pack of 1 (SCY673/81) with Airfree vent to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy673-81', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy673-82" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy673-82');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy673-82" aria-label="View Natural Bottle 260ml Pack of 1 (SCY673/82) with Airfree vent" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY673-82-1.webp" alt="Natural Bottle 260ml Pack of 1 (SCY673/82) with Airfree vent" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy673-82', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 260ml Pack of 1 (SCY673/82) with Airfree vent">
        <a href="/product/philips-avent-scy673-82" class="product-name-link">Natural Bottle 260ml Pack of 1 (SCY673/82) with Airfree vent</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 7,760</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 260ml Pack of 1 (SCY673/82) with Airfree vent to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy673-82', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy903-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy903-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy903-01" aria-label="View Natural Bottle 260ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY903-01-1.webp" alt="Natural Bottle 260ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy903-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 260ml Pack of 1">
        <a href="/product/philips-avent-scy903-01" class="product-name-link">Natural Bottle 260ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY903/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 6,550</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 260ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy903-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy903-11" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy903-11');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy903-11" aria-label="View Natural Bottle 260ml Pack of 1 (SCY903/11) Pink" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/03/SCY903-11-1.webp" alt="Natural Bottle 260ml Pack of 1 (SCY903/11) Pink" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        <span class="p-badge p-badge-bestseller tag tag-best">Bestseller</span>
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy903-11', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 260ml Pack of 1 (SCY903/11) Pink">
        <a href="/product/philips-avent-scy903-11" class="product-name-link">Natural Bottle 260ml Pack of 1 (SCY903/11) Pink</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 7,770</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 260ml Pack of 1 (SCY903/11) Pink to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy903-11', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy903-21" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy903-21');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy903-21" aria-label="View Natural Bottle 260ml Pack of 1 (SCY903/21) Blue" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY903-21-1.webp" alt="Natural Bottle 260ml Pack of 1 (SCY903/21) Blue" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy903-21', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 260ml Pack of 1 (SCY903/21) Blue">
        <a href="/product/philips-avent-scy903-21" class="product-name-link">Natural Bottle 260ml Pack of 1 (SCY903/21) Blue</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 7,770</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 260ml Pack of 1 (SCY903/21) Blue to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy903-21', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy903-66" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy903-66');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy903-66" aria-label="View Natural Bottle 260ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY903-66-1.webp" alt="Natural Bottle 260ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy903-66', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 260ml Pack of 1">
        <a href="/product/philips-avent-scy903-66" class="product-name-link">Natural Bottle 260ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY903/66</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 8,540</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 260ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy903-66', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy903-67" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy903-67');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy903-67" aria-label="View Natural Bottle 260ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY903-67-1.webp" alt="Natural Bottle 260ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy903-67', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 260ml Pack of 1">
        <a href="/product/philips-avent-scy903-67" class="product-name-link">Natural Bottle 260ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY903/67</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 8,540</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 260ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy903-67', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy903-02" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy903-02');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy903-02" aria-label="View Natural Bottle 260ml Pack of 2" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY903-02.webp" alt="Natural Bottle 260ml Pack of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy903-02', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 260ml Pack of 2">
        <a href="/product/philips-avent-scy903-02" class="product-name-link">Natural Bottle 260ml Pack of 2</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY903/02</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 13,020</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 260ml Pack of 2 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy903-02', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy906-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy906-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy906-01" aria-label="View Natural Bottle 330ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY906-01-1.webp" alt="Natural Bottle 330ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy906-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 330ml Pack of 1">
        <a href="/product/philips-avent-scy906-01" class="product-name-link">Natural Bottle 330ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY906/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 6,990</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 330ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy906-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy906-02" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy906-02');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy906-02" aria-label="View Natural Bottle 330ml Pack of 2" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY906-02.webp" alt="Natural Bottle 330ml Pack of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy906-02', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 330ml Pack of 2">
        <a href="/product/philips-avent-scy906-02" class="product-name-link">Natural Bottle 330ml Pack of 2</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY906/02</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 13,830</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 330ml Pack of 2 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy906-02', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf039-17" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf039-17');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf039-17" aria-label="View Natural Bottle 60ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF039-17-1.jpg" alt="Natural Bottle 60ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf039-17', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Bottle 60ml Pack of 1">
        <a href="/product/philips-avent-scf039-17" class="product-name-link">Natural Bottle 60ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF039/17</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 5,150</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Bottle 60ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf039-17', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy930-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy930-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy930-01" aria-label="View Natural Glass Bottle 120ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY930-01-1.webp" alt="Natural Glass Bottle 120ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy930-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Glass Bottle 120ml Pack of 1">
        <a href="/product/philips-avent-scy930-01" class="product-name-link">Natural Glass Bottle 120ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY930/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 9,700</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Glass Bottle 120ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy930-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy933-01" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy933-01');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy933-01" aria-label="View Natural Glass Bottle 240ml Pack of 1" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCY933-01-1.webp" alt="Natural Glass Bottle 240ml Pack of 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        <span class="p-badge p-badge-new tag tag-new">New</span>
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy933-01', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Natural Glass Bottle 240ml Pack of 1">
        <a href="/product/philips-avent-scy933-01" class="product-name-link">Natural Glass Bottle 240ml Pack of 1</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY933/01</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 10,140</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Natural Glass Bottle 240ml Pack of 1 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy933-01', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scy103-02" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scy103-02');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scy103-02" aria-label="View Anti-Colic Bottle 260ml Pack of 2" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/05/SCY103-02.webp" alt="Anti-Colic Bottle 260ml Pack of 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        <span class="p-badge p-badge-out tag" style="background:#4A3F50;color:#fff">Out of stock</span>
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scy103-02', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Feeding Bottles</span>
      </div>
      <h3 class="product-name name" title="Anti-Colic Bottle 260ml Pack of 2">
        <a href="/product/philips-avent-scy103-02" class="product-name-link">Anti-Colic Bottle 260ml Pack of 2</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCY103/02</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 10,880</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Anti-Colic Bottle 260ml Pack of 2 to bag" onclick="event.stopPropagation();addToCart('philips-avent-scy103-02', 1)">
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
