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
<title>Electricals in Pakistan | ParentingHub</title>
<meta name="description" content="Shop Electricals in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<link rel="canonical" href="https://www.parentinghub.pk/shop/categories/electricals">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="Electricals in Pakistan | ParentingHub">
<meta property="og:description" content="Shop Electricals in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta property="og:url" content="https://www.parentinghub.pk/shop/categories/electricals">
<meta property="og:image" content="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF355-09.webp">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Electricals in Pakistan | ParentingHub">
<meta name="twitter:description" content="Shop Electricals in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta name="twitter:image" content="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF355-09.webp">

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
<body data-page="shop" data-category="Electricals">
<div id="chrome-top"></div>

<main class="shop-main-wrap">
  <div class="container wide">
    <!-- Slim Mall Assurance Bar (Daraz Style) -->
    <div class="dz-top-nav">
      <nav class="crumbs" style="margin:0"><a href="index.html">Home</a> <span>/</span> <span style="color:var(--ink)">Official Shop</span></nav>
      <h1 class="shop-page-title" style="font-size:20px;margin:8px 0 0;font-weight:700;color:var(--ink)">Electricals in Pakistan</h1>
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
        <div class="grid-products" id="shopGrid"><article class="product-card card rv" data-id="philips-avent-scf355-09" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf355-09');}">
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
  </article><article class="product-card card rv" data-id="philips-avent-scf291-00" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf291-00');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf291-00" aria-label="View Philips Avent Sterilizer" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF291-00-5.jpg" alt="Philips Avent Sterilizer" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf291-00', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="Philips Avent Sterilizer">
        <a href="/product/philips-avent-scf291-00" class="product-name-link">Philips Avent Sterilizer</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF291/00</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 50,100</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Philips Avent Sterilizer to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf291-00', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="philips-avent-scf293-00" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('philips-avent-scf293-00');}">
    <div class="product-card-thumb thumb">
      <a href="/product/philips-avent-scf293-00" aria-label="View Sterilizer and Dryer" style="display:block;width:100%;height:100%">
        <img src="https://www.philipsaventpakistan.com/wp-content/uploads/2026/04/SCF293-00-1.webp" alt="Sterilizer and Dryer" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('philips-avent-scf293-00', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Philips Avent</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="Sterilizer and Dryer">
        <a href="/product/philips-avent-scf293-00" class="product-name-link">Sterilizer and Dryer</a>
      </h3>
      <span class="product-model muted tiny" style="display:block;margin-top:-2px;font-size:11px;color:var(--ink-3)">Model: SCF293/00</span>
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 62,630</b></span>
        
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Sterilizer and Dryer to bag" onclick="event.stopPropagation();addToCart('philips-avent-scf293-00', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-perfect-prep-pro-baby-bottle-maker-426776" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-perfect-prep-pro-baby-bottle-maker-426776');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-perfect-prep-pro-baby-bottle-maker-426776" aria-label="View Perfect Prep Pro Baby Bottle Maker - 426776 — Variant 1" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/3_f160a2b6-9f14-498f-9fb4-5f5868046e0b.jpg?v=1765536330&width=720" alt="Perfect Prep Pro Baby Bottle Maker - 426776 — Variant 1" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-perfect-prep-pro-baby-bottle-maker-426776', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="Perfect Prep Pro Baby Bottle Maker - 426776 — Variant 1">
        <a href="/product/tt-perfect-prep-pro-baby-bottle-maker-426776" class="product-name-link">Perfect Prep Pro Baby Bottle Maker - 426776 — Variant 1</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 160,000</b></span>
        <span class="old-price"><s>Rs 200,000</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Perfect Prep Pro Baby Bottle Maker - 426776 — Variant 1 to bag" onclick="event.stopPropagation();addToCart('tt-perfect-prep-pro-baby-bottle-maker-426776', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-perfect-prep-pro-baby-bottle-maker-426776-v2" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-perfect-prep-pro-baby-bottle-maker-426776-v2');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-perfect-prep-pro-baby-bottle-maker-426776-v2" aria-label="View Perfect Prep Pro Baby Bottle Maker - 426776 — Variant 2" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/3_f160a2b6-9f14-498f-9fb4-5f5868046e0b.jpg?v=1765536330&width=720" alt="Perfect Prep Pro Baby Bottle Maker - 426776 — Variant 2" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-perfect-prep-pro-baby-bottle-maker-426776-v2', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="Perfect Prep Pro Baby Bottle Maker - 426776 — Variant 2">
        <a href="/product/tt-perfect-prep-pro-baby-bottle-maker-426776-v2" class="product-name-link">Perfect Prep Pro Baby Bottle Maker - 426776 — Variant 2</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 160,000</b></span>
        <span class="old-price"><s>Rs 200,000</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Perfect Prep Pro Baby Bottle Maker - 426776 — Variant 2 to bag" onclick="event.stopPropagation();addToCart('tt-perfect-prep-pro-baby-bottle-maker-426776-v2', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-ultra-uv-steriliser-dryer-storage-white-423110" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-ultra-uv-steriliser-dryer-storage-white-423110');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-ultra-uv-steriliser-dryer-storage-white-423110" aria-label="View Ultra UV Steriliser Dryer &amp; Storage White - 423110" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/178.jpg?v=1756705972&width=720" alt="Ultra UV Steriliser Dryer &amp; Storage White - 423110" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-ultra-uv-steriliser-dryer-storage-white-423110', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="Ultra UV Steriliser Dryer &amp; Storage White - 423110">
        <a href="/product/tt-ultra-uv-steriliser-dryer-storage-white-423110" class="product-name-link">Ultra UV Steriliser Dryer &amp; Storage White - 423110</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 71,920</b></span>
        <span class="old-price"><s>Rs 89,900</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Ultra UV Steriliser Dryer &amp; Storage White - 423110 to bag" onclick="event.stopPropagation();addToCart('tt-ultra-uv-steriliser-dryer-storage-white-423110', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-closer-to-nature-digital-monitor-damage-packaging" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-closer-to-nature-digital-monitor-damage-packaging');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-closer-to-nature-digital-monitor-damage-packaging" aria-label="View Closer to Nature Digital Monitor - Damage Packaging" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/35_c27bf517-a0e3-44f8-a1f2-9f5c767fbacc.jpg?v=1756706002&width=720" alt="Closer to Nature Digital Monitor - Damage Packaging" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 40%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-closer-to-nature-digital-monitor-damage-packaging', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="Closer to Nature Digital Monitor - Damage Packaging">
        <a href="/product/tt-closer-to-nature-digital-monitor-damage-packaging" class="product-name-link">Closer to Nature Digital Monitor - Damage Packaging</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 16,650</b></span>
        <span class="old-price"><s>Rs 27,750</s></span><span class="discount-pill off">-40%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Closer to Nature Digital Monitor - Damage Packaging to bag" onclick="event.stopPropagation();addToCart('tt-closer-to-nature-digital-monitor-damage-packaging', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-baby-nail-file-423772" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-baby-nail-file-423772');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-baby-nail-file-423772" aria-label="View Baby Nail File - 423772" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/130.jpg?v=1756706011&width=720" alt="Baby Nail File - 423772" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-baby-nail-file-423772', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="Baby Nail File - 423772">
        <a href="/product/tt-baby-nail-file-423772" class="product-name-link">Baby Nail File - 423772</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 12,332</b></span>
        <span class="old-price"><s>Rs 15,415</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Baby Nail File - 423772 to bag" onclick="event.stopPropagation();addToCart('tt-baby-nail-file-423772', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-on-the-go-bottle-warmer-tommee-tippee-423770" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-on-the-go-bottle-warmer-tommee-tippee-423770');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-on-the-go-bottle-warmer-tommee-tippee-423770" aria-label="View On The Go Bottle Warmer Tommee Tippee 423770" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/products/423770UKLETSGOPORTABLEBOTTLEWARMERmerged.jpg?v=1756706076&width=720" alt="On The Go Bottle Warmer Tommee Tippee 423770" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-on-the-go-bottle-warmer-tommee-tippee-423770', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="On The Go Bottle Warmer Tommee Tippee 423770">
        <a href="/product/tt-on-the-go-bottle-warmer-tommee-tippee-423770" class="product-name-link">On The Go Bottle Warmer Tommee Tippee 423770</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 24,220</b></span>
        <span class="old-price"><s>Rs 30,275</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add On The Go Bottle Warmer Tommee Tippee 423770 to bag" onclick="event.stopPropagation();addToCart('tt-on-the-go-bottle-warmer-tommee-tippee-423770', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="tt-forehead-thermometer-tommee-tippee-423035" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('tt-forehead-thermometer-tommee-tippee-423035');}">
    <div class="product-card-thumb thumb">
      <a href="/product/tt-forehead-thermometer-tommee-tippee-423035" aria-label="View Forehead Thermometer Tommee Tippee 423035" style="display:block;width:100%;height:100%">
        <img src="https://tommeetippee.pk/cdn/shop/files/tt_423035.png?v=1756706108&width=720" alt="Forehead Thermometer Tommee Tippee 423035" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('tt-forehead-thermometer-tommee-tippee-423035', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Tommee Tippee</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Electricals</span>
      </div>
      <h3 class="product-name name" title="Forehead Thermometer Tommee Tippee 423035">
        <a href="/product/tt-forehead-thermometer-tommee-tippee-423035" class="product-name-link">Forehead Thermometer Tommee Tippee 423035</a>
      </h3>
      
      <div class="product-rating rate"><span class="count muted">No reviews yet</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 22,272</b></span>
        <span class="old-price"><s>Rs 27,840</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Forehead Thermometer Tommee Tippee 423035 to bag" onclick="event.stopPropagation();addToCart('tt-forehead-thermometer-tommee-tippee-423035', 1)">
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
