<?php
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
require_once dirname(__DIR__, 2) . '/backend/api/bootstrap.php';
$homeArticles = [];
$homeCommunityParents = null;
$homeCommunityAnswersMonth = null;
$homeCommunityAnswersTotal = null;
try {
    $c = ph_db_config();
    $homeDb = new PDO(
        "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4",
        $c['user'], $c['pass'],
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]
    );
    $homeArticles = $homeDb->query('SELECT id,title,excerpt,cover,author,date,read_time,category FROM articles ORDER BY created_at DESC, id DESC LIMIT 3')->fetchAll();
    $homeCommunityParents = (int)$homeDb->query("SELECT COUNT(*) FROM ph_users WHERE role='parent' AND is_banned=0")->fetchColumn();
    $homeCommunityAnswersMonth = (int)$homeDb->query("SELECT COUNT(*) FROM ph_community_answers WHERE created_at >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')")->fetchColumn();
    $homeCommunityAnswersTotal = (int)$homeDb->query("SELECT COUNT(*) FROM ph_community_answers")->fetchColumn();
} catch (Throwable $e) {
    error_log('Homepage article load failed: ' . $e->getMessage());
}
function ph_home_h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function ph_home_cover($v): string {
    $v=trim((string)$v);
    if($v==='') return '/art/placeholder.jpg';
    return preg_match('~^(?:https?:)?//~i',$v) ? $v : '/' . ltrim($v,'/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="/">
<meta name="theme-color" content="#bd3d6b">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ParentingHub Pakistan | Authentic Baby Essentials & Parenting Care</title>
<meta name="description" content="Shop 100% authentic Philips Avent & Tommee Tippee baby feeding essentials, breast pumps, sterilizers, and newborn care with nationwide Cash on Delivery across Pakistan.">
<meta name="keywords" content="baby shop pakistan, philips avent pakistan, tommee tippee pakistan, baby bottles karachi, breast pump lahore, newborn care pakistan">
<link rel="canonical" href="https://www.parentinghub.pk/">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- Open Graph / Facebook / email -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="ParentingHub Pakistan | Authentic Baby Essentials & Parenting Care">
<meta property="og:description" content="Official authorized baby care shop in Pakistan. 100% genuine Philips Avent & Tommee Tippee feeding bottles, breast pumps, and newborn sets with Cash on Delivery.">
<meta property="og:url" content="https://www.parentinghub.pk/">
<meta property="og:image" content="https://www.parentinghub.pk/assets/visual/banner-bottles.webp">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="ParentingHub Pakistan | Authentic Baby Essentials & Parenting Care">
<meta name="twitter:description" content="Official authorized baby care shop in Pakistan. 100% genuine Philips Avent & Tommee Tippee feeding essentials with Cash on Delivery.">
<meta name="twitter:image" content="https://www.parentinghub.pk/assets/visual/banner-bottles.webp">

<!-- Structured Data (JSON-LD): Organization & WebSite Searchbox -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "https://www.parentinghub.pk/#organization",
      "name": "ParentingHub Pakistan",
      "url": "https://www.parentinghub.pk",
      "logo": "https://www.parentinghub.pk/assets/visual/parentinghub-logo.png",
      "description": "Authorized retailer of genuine Philips Avent and Tommee Tippee baby care and maternal essentials in Pakistan.",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+92-329-3007360",
        "contactType": "customer service",
        "areaServed": "PK",
        "availableLanguage": ["English", "Urdu"]
      }
    },
    {
      "@type": "WebSite",
      "@id": "https://www.parentinghub.pk/#website",
      "url": "https://www.parentinghub.pk",
      "name": "ParentingHub Pakistan",
      "publisher": { "@id": "https://www.parentinghub.pk/#organization" },
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://www.parentinghub.pk/shop?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
  ]
}
</script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/css/style.css?v=5.5-product-visual">
  <link rel="stylesheet" href="/css/smartcart-ai.css?v=1.0">
  <!-- Favicons -->
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="icon" type="image/png" sizes="64x64" href="assets/favicon-64.png">
  <link rel="icon" type="image/png" sizes="192x192" href="icon-192.png">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png">
  <link rel="shortcut icon" href="favicon.ico">
</head>
<body data-page="home">
<div id="chrome-top"></div>

<main>
  <!-- ============ HERO PRODUCT SLIDER (Pure E-Commerce Banner Slider) ============ -->
  <section class="hero-vibe-section">
    <div class="container wide">
      <div class="luxury-carousel" id="heroProductCarousel" aria-label="Featured Baby Care Products">
        <div class="carousel-track">
          <!-- Slide 1: Feeding Bottles Collection -->
          <div class="carousel-slide active" data-index="0">
            <a class="carousel-slide-inner" href="/shop/categories/feeding-bottles" aria-label="Shop Feeding Bottles Collection">
              <img width="860" height="480" src="assets/visual/banner-bottles.webp" alt="Philips Avent Natural & Anti-Colic Feeding Bottles" loading="eager" fetchpriority="high">
            </a>
          </div>

          <!-- Slide 2: Digital Bottle Warmer & Sterilizer -->
          <div class="carousel-slide" data-index="1">
            <a class="carousel-slide-inner" href="/shop/categories/electricals" aria-label="Shop Bottle Warmers & Sterilizers">
              <img width="860" height="480" src="assets/visual/banner-warmer.webp" alt="Advance Fast Bottle Warmer & Steam Sterilizer" loading="lazy">
            </a>
          </div>

          <!-- Slide 3: Maternal Care & Breast Pumps -->
          <div class="carousel-slide" data-index="2">
            <a class="carousel-slide-inner" href="/shop/categories/breast-feeding" aria-label="Shop Breast Pumps & Maternal Care">
              <img width="860" height="480" src="assets/visual/banner-pump.webp" alt="Tommee Tippee Maternal Breast Pump & Milk Storage" loading="lazy">
            </a>
          </div>

          <!-- Slide 4: Starter Bundles & Gift Sets -->
          <div class="carousel-slide" data-index="3">
            <a class="carousel-slide-inner" href="/shop/categories/gift-sets" aria-label="Shop Newborn Starter Gift Sets">
              <img width="1774" height="887" src="assets/img/slider2.jpg" alt="Newborn Hospital Starter & Gift Sets" loading="lazy">
            </a>
          </div>

          <!-- Slide 5: Natural Feeding Essentials -->
          <div class="carousel-slide" data-index="4">
            <a class="carousel-slide-inner" href="/shop/categories/feeding-bottles" aria-label="Shop Natural Feeding Essentials">
              <img width="1694" height="929" src="assets/img/slider1.jpg" alt="Baby Feeding Essentials & Teats" loading="lazy">
            </a>
          </div>

          <!-- Slide 6: Baby Feeders & Non-Spill Cups -->
          <div class="carousel-slide" data-index="5">
            <a class="carousel-slide-inner" href="/shop/categories/cup-weaning" aria-label="Shop Non-Spill Cups & Feeders">
              <img width="600" height="600" src="assets/feeders-collection-568404a2.jpg" alt="Baby Non-Spill Cups & Feeding Essentials" loading="lazy">
            </a>
          </div>
        </div>

        <!-- Hero Value Proposition Overlay Card -->
        <div class="hero-overlay-card">
          <span class="eyebrow" style="color:var(--primary);font-weight:700;letter-spacing:0.05em">Official Authorized Store</span>
          <h1>Baby &amp; Mother Care Essentials, Delivered Across Pakistan</h1>
          <p>100% genuine Philips Avent, Tommee Tippee &amp; certified baby essentials. Nationwide delivery is a fixed PKR 200 per order, with Cash on Delivery available.</p>
          <div class="hero-overlay-actions">
            <a href="/shop" class="btn btn-primary btn-m" style="font-weight:700">
              Shop Baby Essentials <span class="ico" data-ico="arrowUpRight"></span>
            </a>
            <a href="/shop?stage=newborn" class="btn btn-ghost btn-m" style="background:rgba(255,255,255,0.88);font-weight:600">
              Newborn (0–3m)
            </a>
          </div>
        </div>

        <!-- Navigation Controls -->
        <button class="carousel-arrow prev" type="button" aria-label="Previous slide">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <button class="carousel-arrow next" type="button" aria-label="Next slide">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>

        <!-- Pagination Dots -->
        <div class="carousel-dots" role="tablist" aria-label="Slide dots"></div>
      </div>

      <!-- Trust Bar below banners -->
      <div class="banner-trust-bar">
        <div class="trust-item"><span class="ico" data-ico="shield"></span> 100% Genuine Authorized</div>
        <div class="trust-item"><span class="ico" data-ico="truck"></span> Nationwide Cash on Delivery</div>
        <div class="trust-item"><span class="ico" data-ico="refresh"></span> 7-Day Easy Returns</div>
        <div class="trust-item"><span class="ico" data-ico="check"></span> Practical Care Guides</div>
      </div>
    </div>
  </section>

  <!-- ============ VISUAL CATEGORIES STRIP ============ -->
  <section class="sec-sm" style="padding-top:12px;padding-bottom:8px">
    <div class="container wide">
      <div class="sec-head" style="margin-bottom:12px">
        <div><h2 style="font-size:1.35rem">Shop by Category</h2></div>
        <a class="btn btn-ghost btn-s" href="/shop">View All <span class="ico" data-ico="arrowUpRight" aria-hidden="true"></span></a>
      </div>
      <div class="circles" id="collStrip"></div>
    </div>
  </section>

  <!-- ============ BRAND MARQUEE ============ -->
  <div class="marquee"><div class="track" id="brandStrip"></div></div>

  <!-- ============ VISUAL PROMO BANNERS ============ -->
  <section class="sec-sm" style="padding-top:8px;padding-bottom:8px">
    <div class="container wide">
      <div class="promo-grid">
        <a class="promo-banner" href="/shop/categories/feeding-bottles" style="background-image:url('assets/img/slider1.jpg')">
          <div class="pb-content">
            <span class="pb-tag">Anti-Colic Feeding</span>
            <h3>Natural & Anti-Colic Bottles</h3>
            <span class="pb-link">Shop Bottles <span class="ico" data-ico="arrowUpRight"></span></span>
          </div>
        </a>
        <a class="promo-banner" href="/shop/categories/breast-feeding" style="background-image:url('assets/visual/reading-together.webp')">
          <div class="pb-content">
            <span class="pb-tag">Maternal Comfort</span>
            <h3>Gentle Breast Pumps & Care</h3>
            <span class="pb-link">Shop Breast Care <span class="ico" data-ico="arrowUpRight"></span></span>
          </div>
        </a>
        <a class="promo-banner" href="/shop/categories/gift-sets" style="background-image:url('assets/img/slider2.jpg')">
          <div class="pb-content">
            <span class="pb-tag">Starter Bundles</span>
            <h3>Newborn Hospital & Starter Sets</h3>
            <span class="pb-link">Shop Starter Sets <span class="ico" data-ico="arrowUpRight"></span></span>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- ============ BESTSELLERS ============ -->
  <section class="sec" style="padding-top:10px;padding-bottom:20px">
    <div class="container">
      <div class="sec-head" style="margin-bottom:16px">
        <div>
          <span class="eyebrow dotted">Popular In Pakistan</span>
          <h2 style="margin-top:6px">Bestselling Essentials</h2>
        </div>
        <a class="btn btn-ghost btn-s" href="/shop">See all products <span class="ico" data-ico="arrowUpRight" aria-hidden="true"></span></a>
      </div>
      <div class="grid-products" id="homeGrid"></div>
    </div>
  </section>

  <!-- ============ SHOP BY STAGE ============ -->
  <section class="sec-sm" style="padding-top:10px;padding-bottom:20px">
    <div class="container">
      <div class="sec-head" style="margin-bottom:16px">
        <div>
          <span class="eyebrow dotted">Growth Milestones</span>
          <h2 style="margin-top:6px">Shop by Baby Stage</h2>
        </div>
        <a class="btn btn-ghost btn-s" href="/shop">All Stages <span class="ico" data-ico="arrowUpRight" aria-hidden="true"></span></a>
      </div>
      <div class="stages" id="miniStages"></div>
    </div>
  </section>

  <!-- ============ AI MILESTONE & GUIDANCE BANNER ============ -->
  <section class="sec-sm" style="padding: 20px 0 10px;">
    <div class="container">
      <div style="background: linear-gradient(135deg, #FFF5F8 0%, #FAF0F4 50%, #F5EEF8 100%); border: 1.5px solid #F9D2DF; border-radius: 20px; padding: 28px 32px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; box-shadow: 0 8px 24px rgba(182, 56, 102, 0.08);">
        <div style="max-width: 580px;">
          <span class="eyebrow" style="color: #B63866; display: inline-flex; align-items: center; gap: 6px;">
            <span>✨</span> Pediatric Development &amp; Smart Gear Guidance
          </span>
          <h2 style="font-family: 'Playfair Display', serif; font-size: 26px; color: #2E2333; margin: 8px 0 6px;">
            AI Baby Milestone &amp; Parenting Advisor
          </h2>
          <p style="color: #6A5B6F; font-size: 14px; margin: 0; line-height: 1.5;">
            Get instant guidance on infant growth stages, feeding transitions, and brand-authorized gear. Advisory insights follow international pediatric developmental references (non-clinical informational support).
          </p>
        </div>
        <div>
          <button class="btn btn-primary btn-m" onclick="if(window.openAiMilestoneAdvisor) window.openAiMilestoneAdvisor();" style="border-radius: 12px; padding: 12px 24px; font-weight: 700; box-shadow: 0 6px 18px rgba(182, 56, 102, 0.25); cursor: pointer;">
            <span>💬 Chat with AI Assistant</span>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ COMMUNITY TEASER ============ -->
  <section class="sec" style="background:var(--cream);padding:36px 0">
    <div class="container">
      <div class="sec-head" style="margin-bottom:18px">
        <div>
          <span class="eyebrow dotted">Parent Network</span>
          <h2 style="margin-top:6px">Real Parent Discussions</h2>
        </div>
        <a class="btn btn-primary btn-s" href="/community">Open Community <span class="ico" data-ico="arrow" aria-hidden="true"></span></a>
      </div>
      <div class="row wrap gap-m tiny muted" id="homeCommunityStats" data-state="<?= ($homeCommunityParents !== null && $homeCommunityAnswersMonth !== null && $homeCommunityAnswersTotal !== null) ? 'ready' : 'error' ?>" style="margin:-4px 0 16px" aria-live="polite">
        <span id="homeCommunityParents"><?= $homeCommunityParents !== null ? number_format($homeCommunityParents) . ' parents' : 'Community stats unavailable' ?></span>
        <span id="homeCommunityAnswersMonth"><?= $homeCommunityAnswersMonth !== null ? number_format($homeCommunityAnswersMonth) . ' answers this month' : 'Please refresh shortly' ?></span>
        <span id="homeCommunityAnswersTotal"><?= $homeCommunityAnswersTotal !== null ? number_format($homeCommunityAnswersTotal) . ' total answers' : 'Database reconnecting' ?></span>
      </div>
      <div class="mini-feed" id="homeFeed"></div>
    </div>
  </section>

  <!-- ============ ARTICLES ============ -->
  <section class="sec" style="padding-top:24px;padding-bottom:20px">
    <div class="container">
      <div class="sec-head" style="margin-bottom:18px">
        <div>
          <span class="eyebrow dotted">Care & Buying Guides</span>
          <h2 style="margin-top:6px">Feeding & Care Guides</h2>
        </div>
        <a class="btn btn-ghost btn-s" href="/articles">All Guides <span class="ico" data-ico="arrowUpRight" aria-hidden="true"></span></a>
      </div>
      <div class="grid-articles" id="homeArticles">
        <?php if ($homeArticles): foreach ($homeArticles as $a): ?>
          <a class="article rv" href="/articles/<?= rawurlencode((string)$a['id']) ?>">
            <span class="im"><img src="<?= ph_home_h(ph_home_cover($a['cover'] ?? '')) ?>" alt="<?= ph_home_h($a['title'] ?? '') ?>" loading="lazy" onerror="this.onerror=null;this.src='/art/placeholder.jpg'"></span>
            <div class="bd">
              <span class="tag tag-new" style="align-self:flex-start"><?= ph_home_h($a['category'] ?? '') ?></span>
              <h3><?= ph_home_h($a['title'] ?? '') ?></h3>
              <p><?= ph_home_h($a['excerpt'] ?? '') ?></p>
              <span class="meta"><?= ph_home_h($a['author'] ?? '') ?> · <?= ph_home_h($a['date'] ?? '') ?> · <?= ph_home_h($a['read_time'] ?? '') ?></span>
            </div>
          </a>
        <?php endforeach; else: ?>
          <div class="empty"><b>Guides are being updated</b><p class="muted">Browse the Learn section for published guides.</p></div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- ============ CTA BAND ============ -->
  <section class="sec-sm" style="padding-top:10px;padding-bottom:28px">
    <div class="container">
      <div class="band rv">
        <div>
          <span class="eyebrow" style="color:#fce7f0">Official Store</span>
          <h2 style="margin-top:8px">Order Online with Confidence</h2>
          <p>Cash on Delivery nationwide · 100% Genuine Authorized Brands</p>
        </div>
        <div>
          <form class="form" action="/shop" method="get">
            <input
              type="search"
              name="q"
              placeholder="Search bottles, teats, pumps…"
              required
              aria-label="Search products"
            >
            <button class="btn btn-primary" type="submit">
              Shop Now
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>

<div id="chrome-bottom"></div>
<script src="/js/data.js?v=5.5-product-visual"></script>
<script src="/js/app.js?v=5.5-product-visual"></script>
<script src="/js/milestone-ai.js?v=5.5-product-visual"></script>
<script src="/js/smartcart-ai.js?v=1.0"></script>
</body>
</html>
