<?php
declare(strict_types=1);

if (!defined('PH_ENTRY')) {
    http_response_code(404);
    exit;
}

/*
 * ParentingHub Shop
 * Database-driven version.
 * Products are loaded from the MySQL/MariaDB `products` table.
 * No product cards are hard-coded in this file.
 */
require_once __DIR__ . '/../../backend/api/bootstrap.php';

$pdo = ph_pdo();

function ph_shop_slug(string $value): string
{
    $value = trim(mb_strtolower($value, 'UTF-8'));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';
    return trim($value, '-');
}

function ph_shop_json_array(?string $value): array
{
    if ($value === null || trim($value) === '') {
        return [];
    }
    $decoded = json_decode($value, true);
    return is_array($decoded) ? array_values($decoded) : [];
}

/* ---------------- Products ---------------- */
// Support both the newer MariaDB schema (updated_at) and the older schema (created_at).
$productColumns = $pdo->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_COLUMN, 0);
$timeColumn = null;
if (in_array('updated_at', $productColumns, true)) {
    $timeColumn = 'updated_at';
} elseif (in_array('created_at', $productColumns, true)) {
    $timeColumn = 'created_at';
}

$select = "SELECT id, sku, name, price, compare_at, image, images, category, brand, description, source_json, video_url, weight_kg,
                  has_options, rating, reviews, in_stock, tags";
if ($timeColumn !== null) {
    $select .= ", `{$timeColumn}` AS catalog_timestamp";
}
$select .= " FROM products";
$select .= $timeColumn !== null
    ? " ORDER BY `{$timeColumn}` DESC, name ASC"
    : " ORDER BY name ASC";

$productRows = $pdo->query($select)->fetchAll();

$products = [];
$categoryCounts = [];
$brandCounts = [];

foreach ($productRows as $row) {
    $tags = ph_shop_json_array($row['tags'] ?? null);
    $images = ph_shop_json_array($row['images'] ?? null);

    $mainImage = trim((string)($row['image'] ?? ''));
    if ($mainImage !== '' && !in_array($mainImage, $images, true)) {
        array_unshift($images, $mainImage);
    }

    $images = array_values(array_unique(array_filter($images, 'is_string')));
    $source = json_decode($row['source_json'] ?? '{}', true) ?: [];
    $tag = '';
    $lowerTags = array_map(static fn($t) => mb_strtolower((string)$t, 'UTF-8'), $tags);
    if (in_array('bestseller', $lowerTags, true) || in_array('best', $lowerTags, true)) {
        $tag = 'best';
    } elseif (in_array('new', $lowerTags, true) || in_array('new-arrival', $lowerTags, true)) {
        $tag = 'new';
    }

    $category = trim((string)($row['category'] ?? 'Baby Care')) ?: 'Baby Care';
    $brand = trim((string)($row['brand'] ?? 'ParentingHub')) ?: 'ParentingHub';

    $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
    $brandCounts[$brand] = ($brandCounts[$brand] ?? 0) + 1;

    $products[] = [
        'id' => (string)$row['id'],
        'sku' => (string)($row['sku'] ?? $row['id']),
        'name' => (string)$row['name'],
        'price' => (float)$row['price'],
        'old' => $row['compare_at'] !== null ? (float)$row['compare_at'] : null,
        'compareAt' => $row['compare_at'] !== null ? (float)$row['compare_at'] : null,
        'img' => $mainImage !== '' ? $mainImage : 'art/placeholder.jpg',
        'image' => $mainImage !== '' ? $mainImage : 'art/placeholder.jpg',
        'images' => $images,
        'cat' => $category,
        'category' => $category,
        'brand' => $brand,
        'hasOptions' => (bool)$row['has_options'],
        'rating' => (float)$row['rating'],
        'reviews' => (int)$row['reviews'],
        'inStock' => (bool)$row['in_stock'],
        'tags' => $tags,
        'tag' => $tag,
        'desc' => (string)($row['description'] ?? ''),
        'specs' => is_array($source['specs'] ?? null) ? $source['specs'] : [],
        'video' => (string)($row['video_url'] ?? ''),
        'weightKg' => isset($row['weight_kg']) ? (float)$row['weight_kg'] : null,
        'createdAt' => (string)($row['catalog_timestamp'] ?? ''),
    ];
}

ksort($categoryCounts, SORT_NATURAL | SORT_FLAG_CASE);
ksort($brandCounts, SORT_NATURAL | SORT_FLAG_CASE);

$categories = [];
foreach (array_keys($categoryCounts) as $category) {
    $categories[] = [
        'name' => $category,
        'handle' => ph_shop_slug($category),
    ];
}
$brands = array_keys($brandCounts);

/* Optional category/brand landing filters come from the router and are resolved against live DB taxonomy. */
$activeCategory = '';
$activeBrand = '';
$categorySlug = trim((string)($_GET['category_slug'] ?? ''));
$brandSlug = trim((string)($_GET['brand_slug'] ?? ''));
if ($categorySlug !== '') {
    foreach (array_keys($categoryCounts) as $name) {
        if (ph_shop_slug($name) === $categorySlug) { $activeCategory = $name; break; }
    }
}
if ($brandSlug !== '') {
    foreach ($brands as $name) {
        if (ph_shop_slug($name) === $brandSlug) { $activeBrand = $name; break; }
    }
}

/* ---------------- Site settings ---------------- */
$settings = [
    'siteName' => 'ParentingHub',
    'tagline' => 'Shop • Learn • Connect • Grow',
    'announcement' => "ParentingHub: Pakistan's Trusted Baby & Mother Care Hub",
    'announcementLink' => '/shop',
    'phone' => '0329 3007360',
    'email' => 'support@parentinghub.pk',
    'whatsapp' => '',
    'shippingFee' => 200,
    'codEnabled' => true,
    'bankEnabled' => true,
];

try {
    $settingRows = $pdo->query("SELECT key_name, value_text FROM site_settings")->fetchAll();
    foreach ($settingRows as $row) {
        $key = (string)$row['key_name'];
        $value = (string)$row['value_text'];
        if (in_array($key, ['codEnabled', 'bankEnabled'], true)) {
            $settings[$key] = in_array(mb_strtolower($value), ['1', 'true', 'yes', 'on'], true);
        } elseif ($key === 'shippingFee') {
            $settings[$key] = 200.0;
        } else {
            $settings[$key] = $value;
        }
    }
} catch (Throwable $e) {
    error_log('Shop settings load failed: ' . $e->getMessage());
}
$settings['shippingFee'] = 200.0;
unset($settings['deliveryRatePerKg'],$settings['defaultProductWeightKg'],$settings['freeShippingAbove']);

/* ---------------- Coupons used by existing cart UI ---------------- */
$coupons = [];
try {
    $couponRows = $pdo->query(
        "SELECT code, type, value, min_order, active
         FROM coupons
         WHERE active = 1
         ORDER BY code ASC"
    )->fetchAll();

    foreach ($couponRows as $row) {
        $coupon = [
            'code' => strtoupper((string)$row['code']),
            'type' => (string)$row['type'],
            'value' => (float)$row['value'],
            'minOrder' => (float)$row['min_order'],
            'active' => (bool)$row['active'],
        ];
        if ($coupon['type'] === 'percent') {
            $coupon['discountPercent'] = $coupon['value'];
            $coupon['desc'] = rtrim(rtrim(number_format($coupon['value'], 2, '.', ''), '0'), '.') . '% off';
        } else {
            $coupon['discountAmount'] = $coupon['value'];
            $coupon['desc'] = 'Rs ' . number_format($coupon['value'], 0) . ' off';
        }
        $coupons[] = $coupon;
    }
} catch (Throwable $e) {
    error_log('Shop coupons load failed: ' . $e->getMessage());
}

$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$shopCanonical = 'https://www.parentinghub.pk/shop';
if ($activeCategory !== '') $shopCanonical .= '/categories/' . rawurlencode($categorySlug);
elseif ($activeBrand !== '') $shopCanonical .= '/brands/' . rawurlencode($brandSlug);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <base href="/">
  <meta name="theme-color" content="#bd3d6b">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($activeCategory ?: ($activeBrand ?: 'Official Baby Essentials Shop'), ENT_QUOTES, 'UTF-8') ?> | ParentingHub Pakistan</title>
  <meta name="description" content="Shop authentic baby and mother care essentials from ParentingHub Pakistan.">
  <link rel="canonical" href="<?= htmlspecialchars($shopCanonical, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

  <meta property="og:type" content="website">
  <meta property="og:locale" content="en_PK">
  <meta property="og:site_name" content="ParentingHub Pakistan">
  <meta property="og:title" content="Official Baby Essentials Shop | ParentingHub Pakistan">
  <meta property="og:description" content="Explore baby and mother care essentials with nationwide delivery.">
  <meta property="og:url" content="<?= htmlspecialchars($shopCanonical, ENT_QUOTES, 'UTF-8') ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/css/style.css?v=6.1-card-layout-fix">
  <link rel="stylesheet" href="/css/smartcart-ai.css?v=1.0">
  <link rel="icon" type="image/png" href="/favicon.png">
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  <link rel="icon" type="image/png" sizes="64x64" href="/assets/favicon-64.png">
  <link rel="icon" type="image/png" sizes="192x192" href="/icon-192.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">
</head>
<body data-page="shop"<?= $activeCategory!=='' ? ' data-category="'.htmlspecialchars($activeCategory,ENT_QUOTES,'UTF-8').'"' : '' ?><?= $activeBrand!=='' ? ' data-brand="'.htmlspecialchars($activeBrand,ENT_QUOTES,'UTF-8').'"' : '' ?>>
<div id="chrome-top"></div>

<main class="shop-main-wrap">
  <div class="container wide">
    <div class="dz-top-nav">
      <nav class="crumbs" style="margin:0"><a href="/">Home</a> <span>/</span> <span style="color:var(--ink)">Official Shop</span></nav>
      <h1 class="shop-page-title" style="font-size:20px;margin:8px 0 0;font-weight:700;color:var(--ink)">Baby Care &amp; Feeding Essentials</h1>
      <div class="dz-mall-strip" style="margin-top:8px">
        <span class="dz-mall-tag"><span class="ico" data-ico="shield"></span> ParentingHub Mall</span>
        <span class="dz-mall-item"><span class="ico" data-ico="check"></span> Authentic Products</span>
        <span class="dz-mall-item"><span class="ico" data-ico="card"></span> Cash on Delivery Across Pakistan</span>
        <span class="dz-mall-item"><span class="ico" data-ico="truck"></span> Fixed PKR 200 Delivery Per Order</span>
        <span class="dz-mall-item"><span class="ico" data-ico="refresh"></span> 7-Day Easy Returns</span>
      </div>
    </div>

    <div class="daraz-layout">
      <aside class="daraz-sidebar" id="shopSidebar">
        <div class="dz-sidebar-head">
          <h3>Filters</h3>
          <button class="dz-sidebar-clear" type="button" onclick="window.resetShop && window.resetShop()">Reset All</button>
          <button class="dz-sidebar-close" id="closeSidebarBtn" type="button" aria-label="Close filters"><span class="ico" data-ico="x"></span></button>
        </div>

        <div class="dz-filter-group">
          <h4 class="dz-filter-title">Category</h4>
          <div class="dz-cat-list" id="sidebarCatList"></div>
        </div>

        <div class="dz-filter-group">
          <h4 class="dz-filter-title">Brand</h4>
          <div class="dz-brand-list" id="sidebarBrandList"></div>
        </div>

        <div class="dz-filter-group">
          <h4 class="dz-filter-title">Price (PKR)</h4>
          <div class="dz-price-inputs">
            <input type="number" id="minPriceInput" placeholder="Min" min="0" step="100" aria-label="Minimum price">
            <span>–</span>
            <input type="number" id="maxPriceInput" placeholder="Max" min="0" step="100" aria-label="Maximum price">
            <button id="applyPriceBtn" type="button" aria-label="Apply price filter">→</button>
          </div>
        </div>

        <div class="dz-filter-group">
          <h4 class="dz-filter-title">Service &amp; Promotion</h4>
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

        <div class="dz-filter-group" style="border-bottom:none">
          <h4 class="dz-filter-title">Rating</h4>
          <label class="dz-checkbox-label">
            <input type="checkbox" id="chkRating4">
            <span class="dz-stars" style="color:#F59E0B">★ ★ ★ ★ ☆</span>
            <span>4★ &amp; above</span>
          </label>
        </div>
      </aside>

      <div class="daraz-main">
        <div class="dz-controls">
          <div class="dz-res-count" id="resLine">
            <span>Loading <?= count($products) ?> database products…</span>
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

            <button class="dz-mobile-filter-btn" id="openSidebarBtn" type="button">
              <span class="ico" data-ico="grid"></span> Filters
            </button>
          </div>
        </div>

        <div class="dz-active-pills" id="activeFilterTags" style="display:none"></div>

        <!-- IMPORTANT: intentionally empty. app.js renders DB products here. -->
        <div class="grid-products" id="shopGrid"></div>
      </div>
    </div>

    <div class="dz-sidebar-scrim" id="sidebarScrim"></div>
  </div>
</main>

<div id="chrome-bottom"></div>

<script>
/* Database values injected by PHP. No static product catalog is loaded. */
const PRODUCTS = <?= json_encode($products, $jsonFlags) ?>;
const CATEGORIES = <?= json_encode($categories, $jsonFlags) ?>;
const BRANDS = <?= json_encode($brands, $jsonFlags) ?>;
const SETTINGS = <?= json_encode($settings, $jsonFlags) ?>;
const COUPONS = <?= json_encode($coupons, $jsonFlags) ?>;

/* Helpers that were previously supplied by static data.js. */
const money = n => "Rs " + Math.round(Number(n) || 0).toLocaleString("en-PK");
const star = r => {
  const f = Math.max(0, Math.min(5, Math.round(Number(r) || 0)));
  return "★★★★★".slice(0, f) + "☆☆☆☆☆".slice(0, 5 - f);
};

function mixProducts(arr) {
  if (!Array.isArray(arr) || arr.length <= 1) return arr || [];
  const groups = {};
  arr.forEach(p => {
    const b = p.brand || "Other";
    (groups[b] ||= []).push(p);
  });
  const brands = Object.keys(groups).sort((a, b) => groups[b].length - groups[a].length);
  const mixed = [];
  const total = arr.length;
  while (mixed.length < total) {
    for (const b of brands) {
      if (groups[b].length) mixed.push(groups[b].shift());
    }
  }
  return mixed;
}

function filterProducts({
  cat = "all",
  brand = "all",
  stage = "all",
  inStockOnly = false,
  minPrice = 0,
  maxPrice = Infinity,
  onSaleOnly = false,
  q = "",
  sort = "featured"
} = {}) {
  let list = PRODUCTS.slice();

  if (cat && cat !== "all") {
    const cLow = String(cat).toLowerCase();
    list = list.filter(p => {
      const pCat = String(p.cat || "").toLowerCase();
      const pHandle = pCat.replace(/\s+&\s+/g, "-").replace(/\s+/g, "-");
      return pCat === cLow || pHandle === cLow || pCat.includes(cLow);
    });
  }

  if (brand && brand !== "all") {
    const bLow = String(brand).toLowerCase();
    list = list.filter(p => String(p.brand || "").toLowerCase() === bLow);
  }

  if (stage && stage !== "all") {
    const sLow = String(stage).toLowerCase();
    list = list.filter(p => {
      const text = [p.name, p.cat, p.brand, p.desc, ...(p.tags || [])].join(" ").toLowerCase();
      if (sLow.includes("newborn") || sLow === "0-3m") {
        return /newborn|0m|0-2m|0-6m|125ml|60ml|anti-colic|sterilizer|breast pump|soothie|pacifier/.test(text);
      }
      if (sLow.includes("infant") || sLow === "3-6m") {
        return /infant|3m|6m|260ml|330ml|teether|sippy|high chair|bouncer|swing|stroller/.test(text);
      }
      if (sLow.includes("toddler") || sLow === "12m+") {
        return /toddler|12m|18m|straw cup|weaning|walker|tricycle|car seat|ride-on/.test(text);
      }
      if (sLow.includes("expecting")) {
        return /breast pump|pump|maternity|storage cups|sterilizer|warmer|starter set|hospital/.test(text);
      }
      return text.includes(sLow);
    });
  }

  if (inStockOnly) list = list.filter(p => !!p.inStock);
  if (onSaleOnly) list = list.filter(p => Number(p.old || 0) > Number(p.price || 0));

  const min = Math.max(0, Number(minPrice) || 0);
  const max = Number.isFinite(Number(maxPrice)) ? Number(maxPrice) : Infinity;
  list = list.filter(p => Number(p.price || 0) >= min && Number(p.price || 0) <= max);

  if (q && String(q).trim()) {
    const terms = String(q).trim().toLowerCase().split(/\s+/).filter(Boolean);
    list = list.filter(p => {
      const full = [p.name, p.sku, p.id, p.brand, p.cat, p.desc, ...(p.tags || [])].join(" ").toLowerCase();
      return terms.every(term => full.includes(term));
    });
  }

  if (sort === "low") list.sort((a, b) => Number(a.price) - Number(b.price));
  else if (sort === "high") list.sort((a, b) => Number(b.price) - Number(a.price));
  else if (sort === "rating") list.sort((a, b) => Number(b.rating || 0) - Number(a.rating || 0));
  else if (sort === "newest") list.sort((a, b) => String(b.createdAt || "").localeCompare(String(a.createdAt || "")));
  else if ((!sort || sort === "featured") && (!brand || brand === "all")) list = mixProducts(list);

  return list;
}
</script>

<!-- data.js is deliberately NOT loaded on the shop page. -->
<script src="/js/app.js?v=6.0-card-hover-v22"></script>
<script src="/js/milestone-ai.js?v=5.5-product-visual"></script>
<script src="/js/smartcart-ai.js?v=1.0"></script>
<script>
/* Product links currently use the site's product modal instead of a dead /product route. */
document.addEventListener('click', function (event) {
  const link = event.target.closest('#shopGrid a[href^="/product/"]');
  if (!link) return;
  const card = link.closest('[data-id]');
  if (!card || typeof window.openProductModal !== 'function') return;
  event.preventDefault();
  window.openProductModal(card.dataset.id);
});
</script>
</body>
</html>
