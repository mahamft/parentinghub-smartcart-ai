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
<title>Ride-On &amp; Scooters in Pakistan | ParentingHub</title>
<meta name="description" content="Shop Ride-On &amp; Scooters in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<link rel="canonical" href="https://www.parentinghub.pk/shop/categories/ride-on-scooters">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="Ride-On &amp; Scooters in Pakistan | ParentingHub">
<meta property="og:description" content="Shop Ride-On &amp; Scooters in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta property="og:url" content="https://www.parentinghub.pk/shop/categories/ride-on-scooters">
<meta property="og:image" content="https://www.parentinghub.pk/assets/products/gear/kdl_ts430.webp">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Ride-On &amp; Scooters in Pakistan | ParentingHub">
<meta name="twitter:description" content="Shop Ride-On &amp; Scooters in Pakistan. Compare available products, prices and specifications with nationwide delivery and Cash on Delivery.">
<meta name="twitter:image" content="https://www.parentinghub.pk/assets/products/gear/kdl_ts430.webp">

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
<body data-page="shop" data-category="Ride-On &amp; Scooters">
<div id="chrome-top"></div>

<main class="shop-main-wrap">
  <div class="container wide">
    <!-- Slim Mall Assurance Bar (Daraz Style) -->
    <div class="dz-top-nav">
      <nav class="crumbs" style="margin:0"><a href="index.html">Home</a> <span>/</span> <span style="color:var(--ink)">Official Shop</span></nav>
      <h1 class="shop-page-title" style="font-size:20px;margin:8px 0 0;font-weight:700;color:var(--ink)">Ride-On &amp; Scooters in Pakistan</h1>
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
        <div class="grid-products" id="shopGrid"><article class="product-card card rv" data-id="kdl_ts430" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kdl_ts430');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kdl_ts430" aria-label="View Kidilo TS430 Deluxe Baby Tricycle with Parent Push Bar &amp; LED Headlight" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/kdl_ts430.webp" alt="Kidilo TS430 Deluxe Baby Tricycle with Parent Push Bar &amp; LED Headlight" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 18%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kdl_ts430', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="Kidilo TS430 Deluxe Baby Tricycle with Parent Push Bar &amp; LED Headlight">
        <a href="/product/kdl_ts430" class="product-name-link">Kidilo TS430 Deluxe Baby Tricycle with Parent Push Bar &amp; LED Headlight</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(25 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 7,499</b></span>
        <span class="old-price"><s>Rs 9,200</s></span><span class="discount-pill off">-18%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo TS430 Deluxe Baby Tricycle with Parent Push Bar &amp; LED Headlight to bag" onclick="event.stopPropagation();addToCart('kdl_ts430', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kdl_ts871" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kdl_ts871');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kdl_ts871" aria-label="View Kidilo TS871 Musical Kids Tricycle with Flashing Disco Lights" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/kdl_ts871.webp" alt="Kidilo TS871 Musical Kids Tricycle with Flashing Disco Lights" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kdl_ts871', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="Kidilo TS871 Musical Kids Tricycle with Flashing Disco Lights">
        <a href="/product/kdl_ts871" class="product-name-link">Kidilo TS871 Musical Kids Tricycle with Flashing Disco Lights</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(22 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 6,999</b></span>
        <span class="old-price"><s>Rs 8,800</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo TS871 Musical Kids Tricycle with Flashing Disco Lights to bag" onclick="event.stopPropagation();addToCart('kdl_ts871', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kdl_688" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kdl_688');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kdl_688" aria-label="View Kidilo 688 Luxury Baby Canopy Tricycle with Parent Steer Bar" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/kdl_688.webp" alt="Kidilo 688 Luxury Baby Canopy Tricycle with Parent Steer Bar" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 19%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kdl_688', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="Kidilo 688 Luxury Baby Canopy Tricycle with Parent Steer Bar">
        <a href="/product/kdl_688" class="product-name-link">Kidilo 688 Luxury Baby Canopy Tricycle with Parent Steer Bar</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(22 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 10,999</b></span>
        <span class="old-price"><s>Rs 13,500</s></span><span class="discount-pill off">-19%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo 688 Luxury Baby Canopy Tricycle with Parent Steer Bar to bag" onclick="event.stopPropagation();addToCart('kdl_688', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="kdl_ts428" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('kdl_ts428');}">
    <div class="product-card-thumb thumb">
      <a href="/product/kdl_ts428" aria-label="View Kidilo TS428 Classic Sport Kids Tricycle with LED Light" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/kdl_ts428.webp" alt="Kidilo TS428 Classic Sport Kids Tricycle with LED Light" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 20%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('kdl_ts428', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Kidilo</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="Kidilo TS428 Classic Sport Kids Tricycle with LED Light">
        <a href="/product/kdl_ts428" class="product-name-link">Kidilo TS428 Classic Sport Kids Tricycle with LED Light</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(28 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 6,800</b></span>
        <span class="old-price"><s>Rs 8,500</s></span><span class="discount-pill off">-20%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Kidilo TS428 Classic Sport Kids Tricycle with LED Light to bag" onclick="event.stopPropagation();addToCart('kdl_ts428', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="spm_8806c" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('spm_8806c');}">
    <div class="product-card-thumb thumb">
      <a href="/product/spm_8806c" aria-label="View SpeedMaster 8806C Adjustable Inline Roller Skates with Flashing Wheels" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/spm_8806c.webp" alt="SpeedMaster 8806C Adjustable Inline Roller Skates with Flashing Wheels" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 22%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('spm_8806c', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">ParentingHub Gear</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="SpeedMaster 8806C Adjustable Inline Roller Skates with Flashing Wheels">
        <a href="/product/spm_8806c" class="product-name-link">SpeedMaster 8806C Adjustable Inline Roller Skates with Flashing Wheels</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(35 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 4,499</b></span>
        <span class="old-price"><s>Rs 5,800</s></span><span class="discount-pill off">-22%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add SpeedMaster 8806C Adjustable Inline Roller Skates with Flashing Wheels to bag" onclick="event.stopPropagation();addToCart('spm_8806c', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="pwz_m23" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('pwz_m23');}">
    <div class="product-card-thumb thumb">
      <a href="/product/pwz_m23" aria-label="View Power Weelz M23 12V 2-Motor Off-Road Police Edition Electric Ride-On Truck" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/pwz_m23_1.webp" alt="Power Weelz M23 12V 2-Motor Off-Road Police Edition Electric Ride-On Truck" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 17%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('pwz_m23', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Power Weelz</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="Power Weelz M23 12V 2-Motor Off-Road Police Edition Electric Ride-On Truck">
        <a href="/product/pwz_m23" class="product-name-link">Power Weelz M23 12V 2-Motor Off-Road Police Edition Electric Ride-On Truck</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(31 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 39,999</b></span>
        <span class="old-price"><s>Rs 48,000</s></span><span class="discount-pill off">-17%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Power Weelz M23 12V 2-Motor Off-Road Police Edition Electric Ride-On Truck to bag" onclick="event.stopPropagation();addToCart('pwz_m23', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="pwz_m16" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('pwz_m16');}">
    <div class="product-card-thumb thumb">
      <a href="/product/pwz_m16" aria-label="View Power Weelz M16 12V Mercedes G-Wagon Style Electric Luxury SUV" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/pwz_m16_1.webp" alt="Power Weelz M16 12V Mercedes G-Wagon Style Electric Luxury SUV" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 17%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('pwz_m16', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">Power Weelz</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="Power Weelz M16 12V Mercedes G-Wagon Style Electric Luxury SUV">
        <a href="/product/pwz_m16" class="product-name-link">Power Weelz M16 12V Mercedes G-Wagon Style Electric Luxury SUV</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(30 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 42,999</b></span>
        <span class="old-price"><s>Rs 52,000</s></span><span class="discount-pill off">-17%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add Power Weelz M16 12V Mercedes G-Wagon Style Electric Luxury SUV to bag" onclick="event.stopPropagation();addToCart('pwz_m16', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="glk_sw618" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('glk_sw618');}">
    <div class="product-card-thumb thumb">
      <a href="/product/glk_sw618" aria-label="View GlideKid SW618 3-in-1 Foldable Toddler Scooter with Seat &amp; Light Wheels" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/glk_sw618.webp" alt="GlideKid SW618 3-in-1 Foldable Toddler Scooter with Seat &amp; Light Wheels" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 24%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('glk_sw618', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">ParentingHub Gear</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="GlideKid SW618 3-in-1 Foldable Toddler Scooter with Seat &amp; Light Wheels">
        <a href="/product/glk_sw618" class="product-name-link">GlideKid SW618 3-in-1 Foldable Toddler Scooter with Seat &amp; Light Wheels</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(36 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 5,200</b></span>
        <span class="old-price"><s>Rs 6,800</s></span><span class="discount-pill off">-24%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add GlideKid SW618 3-in-1 Foldable Toddler Scooter with Seat &amp; Light Wheels to bag" onclick="event.stopPropagation();addToCart('glk_sw618', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="glk_2396" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('glk_2396');}">
    <div class="product-card-thumb thumb">
      <a href="/product/glk_2396" aria-label="View GlideKid 2396 3-in-1 Adjustable Kids Kick Scooter with Foldable Seat" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/glk_2396.webp" alt="GlideKid 2396 3-in-1 Adjustable Kids Kick Scooter with Foldable Seat" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 22%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('glk_2396', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">ParentingHub Gear</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="GlideKid 2396 3-in-1 Adjustable Kids Kick Scooter with Foldable Seat">
        <a href="/product/glk_2396" class="product-name-link">GlideKid 2396 3-in-1 Adjustable Kids Kick Scooter with Foldable Seat</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(36 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 4,600</b></span>
        <span class="old-price"><s>Rs 5,900</s></span><span class="discount-pill off">-22%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add GlideKid 2396 3-in-1 Adjustable Kids Kick Scooter with Foldable Seat to bag" onclick="event.stopPropagation();addToCart('glk_2396', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="zoo_6181" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('zoo_6181');}">
    <div class="product-card-thumb thumb">
      <a href="/product/zoo_6181" aria-label="View ZooRider 6181 Cartoon Animal 3-in-1 Kids Scooter with Foldable Seat" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/zoo_6181.webp" alt="ZooRider 6181 Cartoon Animal 3-in-1 Kids Scooter with Foldable Seat" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 22%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('zoo_6181', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">ParentingHub Gear</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="ZooRider 6181 Cartoon Animal 3-in-1 Kids Scooter with Foldable Seat">
        <a href="/product/zoo_6181" class="product-name-link">ZooRider 6181 Cartoon Animal 3-in-1 Kids Scooter with Foldable Seat</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(14 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 5,400</b></span>
        <span class="old-price"><s>Rs 6,900</s></span><span class="discount-pill off">-22%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add ZooRider 6181 Cartoon Animal 3-in-1 Kids Scooter with Foldable Seat to bag" onclick="event.stopPropagation();addToCart('zoo_6181', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="glk_6191" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('glk_6191');}">
    <div class="product-card-thumb thumb">
      <a href="/product/glk_6191" aria-label="View GlideKid 6191 Deluxe 3-in-1 Light-Up Kids Kick Scooter with Seat" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/glk_6191.webp" alt="GlideKid 6191 Deluxe 3-in-1 Light-Up Kids Kick Scooter with Seat" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 21%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('glk_6191', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">ParentingHub Gear</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="GlideKid 6191 Deluxe 3-in-1 Light-Up Kids Kick Scooter with Seat">
        <a href="/product/glk_6191" class="product-name-link">GlideKid 6191 Deluxe 3-in-1 Light-Up Kids Kick Scooter with Seat</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(13 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 5,500</b></span>
        <span class="old-price"><s>Rs 7,000</s></span><span class="discount-pill off">-21%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add GlideKid 6191 Deluxe 3-in-1 Light-Up Kids Kick Scooter with Seat to bag" onclick="event.stopPropagation();addToCart('glk_6191', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="jsp_601" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('jsp_601');}">
    <div class="product-card-thumb thumb">
      <a href="/product/jsp_601" aria-label="View JuniorSprint 601 3-Wheel Kids Lean-To-Steer Kick Scooter" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/jsp_601.webp" alt="JuniorSprint 601 3-Wheel Kids Lean-To-Steer Kick Scooter" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 25%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('jsp_601', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">ParentingHub Gear</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="JuniorSprint 601 3-Wheel Kids Lean-To-Steer Kick Scooter">
        <a href="/product/jsp_601" class="product-name-link">JuniorSprint 601 3-Wheel Kids Lean-To-Steer Kick Scooter</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(28 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 1,950</b></span>
        <span class="old-price"><s>Rs 2,600</s></span><span class="discount-pill off">-25%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add JuniorSprint 601 3-Wheel Kids Lean-To-Steer Kick Scooter to bag" onclick="event.stopPropagation();addToCart('jsp_601', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="zoo_6691" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('zoo_6691');}">
    <div class="product-card-thumb thumb">
      <a href="/product/zoo_6691" aria-label="View ZooRider 6691 Cute Frog &amp; Crab 3-in-1 Foldable Toddler Scooter" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/zoo_6691.webp" alt="ZooRider 6691 Cute Frog &amp; Crab 3-in-1 Foldable Toddler Scooter" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 23%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('zoo_6691', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">ParentingHub Gear</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="ZooRider 6691 Cute Frog &amp; Crab 3-in-1 Foldable Toddler Scooter">
        <a href="/product/zoo_6691" class="product-name-link">ZooRider 6691 Cute Frog &amp; Crab 3-in-1 Foldable Toddler Scooter</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(33 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 4,999</b></span>
        <span class="old-price"><s>Rs 6,500</s></span><span class="discount-pill off">-23%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add ZooRider 6691 Cute Frog &amp; Crab 3-in-1 Foldable Toddler Scooter to bag" onclick="event.stopPropagation();addToCart('zoo_6691', 1)">
         Add to Bag
      </button>
    </div>
  </article><article class="product-card card rv" data-id="spr_sw2029" tabindex="0" onkeydown="if(event.key==='Enter'&&event.target===this){openProductModal('spr_sw2029');}">
    <div class="product-card-thumb thumb">
      <a href="/product/spr_sw2029" aria-label="View SpaceRider SW2029 Kids Space UFO Foldable Kick Scooter with Wide Wheels" style="display:block;width:100%;height:100%">
        <img src="assets/products/gear/spr_sw2029.webp" alt="SpaceRider SW2029 Kids Space UFO Foldable Kick Scooter with Wide Wheels" loading="lazy" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='art/placeholder.jpg'">
      </a>
      <div class="product-badges tags">
        
        
        <span class="p-badge p-badge-sale tag tag-sale">Save 22%</span>
        
      </div>
      <button class="product-wish-btn wish " aria-label="Save to wishlist" title="Save to wishlist" onclick="event.stopPropagation();toggleWish('spr_sw2029', this)">
        
      </button>
    </div>
    <div class="product-card-body body">
      <div class="product-card-meta">
        <span class="product-brand">ParentingHub Gear</span>
        <span class="product-sep">•</span>
        <span class="product-cat cat">Ride-On &amp; Scooters</span>
      </div>
      <h3 class="product-name name" title="SpaceRider SW2029 Kids Space UFO Foldable Kick Scooter with Wide Wheels">
        <a href="/product/spr_sw2029" class="product-name-link">SpaceRider SW2029 Kids Space UFO Foldable Kick Scooter with Wide Wheels</a>
      </h3>
      
      <div class="product-rating rate"><span class="stars st">★★★★★</span><span class="score">4.9</span><span class="count muted">(29 reviews)</span></div>
      <div class="product-pricing price">
        <span class="curr-price"><b>Rs 5,400</b></span>
        <span class="old-price"><s>Rs 6,900</s></span><span class="discount-pill off">-22%</span>
      </div>
      <button class="product-add-btn btn" type="button" aria-label="Add SpaceRider SW2029 Kids Space UFO Foldable Kick Scooter with Wide Wheels to bag" onclick="event.stopPropagation();addToCart('spr_sw2029', 1)">
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
