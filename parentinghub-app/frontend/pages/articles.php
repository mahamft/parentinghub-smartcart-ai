<?php
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
require_once dirname(__DIR__, 2) . '/backend/api/bootstrap.php';

$pdo = ph_pdo();
$reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/articles', PHP_URL_PATH) ?: '/articles';
$guideId = null;
if (preg_match('~^/articles/([^/]+)/?$~', $reqPath, $m)) {
    $guideId = rawurldecode((string)$m[1]);
}

function learn_h(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function learn_body(array $row): array {
    $body = json_decode((string)($row['body_json'] ?? '[]'), true);
    if (!is_array($body)) return [];
    return array_values(array_filter(array_map('strval', $body), static fn($v) => trim($v) !== ''));
}
function learn_cover(string $cover): string {
    $cover = trim($cover);
    if ($cover === '') return '/art/placeholder.jpg';
    if (preg_match('~^(?:https?:)?//~i', $cover)) return $cover;
    return '/' . ltrim($cover, '/');
}

$article = null;
$related = [];
$articles = [];
$categories = [];
$selectedCat = trim((string)($_GET['cat'] ?? ''));

if ($guideId !== null) {
    $st = $pdo->prepare('SELECT * FROM articles WHERE id=? LIMIT 1');
    $st->execute([$guideId]);
    $article = $st->fetch();
    if (!$article) {
        http_response_code(404);
    } else {
        $st = $pdo->prepare('SELECT * FROM articles WHERE id<>? ORDER BY (category=? ) DESC, created_at DESC LIMIT 3');
        $st->execute([$guideId, (string)$article['category']]);
        $related = $st->fetchAll();
    }
} else {
    if ($selectedCat !== '') {
        $st = $pdo->prepare('SELECT * FROM articles WHERE category=? ORDER BY created_at DESC, id DESC');
        $st->execute([$selectedCat]);
        $articles = $st->fetchAll();
    } else {
        $articles = $pdo->query('SELECT * FROM articles ORDER BY created_at DESC, id DESC')->fetchAll();
    }
    $categories = $pdo->query("SELECT DISTINCT category FROM articles WHERE TRIM(category)<>'' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
}

$pageTitle = $article ? ((string)$article['title'] . ' | ParentingHub') : 'Practical Parenting Guides | ParentingHub Pakistan';
$pageDesc = $article ? (string)$article['excerpt'] : 'Clear, useful parenting guides for Pakistani families — newborn feeding, starting solids, vaccination records and honest buying guides.';
$canonical = $article ? ('https://www.parentinghub.pk/articles/' . rawurlencode((string)$article['id'])) : 'https://www.parentinghub.pk/articles';
$ogImage = $article ? learn_cover((string)$article['cover']) : '/assets/visual/banner-bottles.jpg';
if (!preg_match('~^https?://~i', $ogImage)) $ogImage = 'https://www.parentinghub.pk' . $ogImage;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="/">
<meta name="theme-color" content="#bd3d6b">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= learn_h($pageTitle) ?></title>
<meta name="description" content="<?= learn_h($pageDesc) ?>">
<link rel="canonical" href="<?= learn_h($canonical) ?>">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta property="og:type" content="<?= $article ? 'article' : 'website' ?>">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="<?= learn_h($pageTitle) ?>">
<meta property="og:description" content="<?= learn_h($pageDesc) ?>">
<meta property="og:url" content="<?= learn_h($canonical) ?>">
<meta property="og:image" content="<?= learn_h($ogImage) ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css?v=5.5-product-visual">
<link rel="icon" type="image/png" href="favicon.png">
<link rel="icon" type="image/x-icon" href="favicon.ico">
<?php if ($article): ?>
<script type="application/ld+json"><?= json_encode([
  '@context'=>'https://schema.org', '@type'=>'Article',
  'headline'=>(string)$article['title'], 'description'=>(string)$article['excerpt'],
  'image'=>[$ogImage], 'author'=>['@type'=>'Person','name'=>(string)$article['author']],
  'mainEntityOfPage'=>$canonical, 'publisher'=>['@type'=>'Organization','name'=>'ParentingHub Pakistan']
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
</head>
<body data-page="<?= $article ? 'article' : 'articles' ?>" data-learn-db="1">
<div id="chrome-top"></div>

<main>
<?php if ($guideId !== null): ?>
  <div class="container">
    <nav class="crumbs"><a href="/">Home</a> <span>/</span> <a href="/articles">Learn</a><?php if ($article): ?> <span>/</span> <span><?= learn_h((string)$article['title']) ?></span><?php endif; ?></nav>
    <?php if (!$article): ?>
      <div class="empty" style="max-width:700px;margin:50px auto;padding:40px;text-align:center">
        <div class="box">📚</div><b>Guide not found</b><p class="muted">This guide may have been removed from the Learn database.</p>
        <a class="btn btn-primary" href="/articles">Back to Learn</a>
      </div>
    <?php else: $body = learn_body($article); ?>
      <div class="article-reader" style="max-width:780px;margin:0 auto;padding-bottom:40px">
        <div style="margin-bottom:16px"><a href="/articles" class="btn btn-ghost btn-xs" style="margin-bottom:14px">← Back to all guides</a></div>
        <div class="row gap-s" style="margin-bottom:10px">
          <span class="tag tag-new"><?= learn_h((string)$article['category']) ?></span>
          <span class="tiny muted" style="margin-left:8px"><?= learn_h((string)$article['read_time']) ?> read · Parenting guide</span>
        </div>
        <h1 style="font-size:32px;line-height:1.25;margin-bottom:12px"><?= learn_h((string)$article['title']) ?></h1>
        <div class="meta" style="color:var(--ink-3);font-size:13px;margin-bottom:20px">By <b><?= learn_h((string)$article['author']) ?></b> · Published <?= learn_h((string)$article['date']) ?></div>
        <div style="border-radius:18px;overflow:hidden;margin-bottom:24px;border:1px solid var(--line);max-height:420px">
          <img src="<?= learn_h(learn_cover((string)$article['cover'])) ?>" alt="<?= learn_h((string)$article['title']) ?>" style="width:100%;height:100%;object-fit:cover" onerror="this.onerror=null;this.src='/art/placeholder.jpg'">
        </div>
        <div class="article-summary-box" style="background:var(--surface-2);border-left:4px solid var(--primary);padding:16px 20px;border-radius:0 14px 14px 0;margin-bottom:26px;font-size:15px;color:var(--ink)">
          <b>Executive Takeaway:</b> <?= learn_h((string)$article['excerpt']) ?>
        </div>
        <div class="article-content">
          <?php if ($body): foreach ($body as $p): ?>
            <p style="font-size:16px;line-height:1.8;color:var(--ink-2);margin-bottom:18px"><?= nl2br(learn_h($p)) ?></p>
          <?php endforeach; else: ?>
            <p style="font-size:16px;line-height:1.8;color:var(--ink-2);margin-bottom:18px"><?= learn_h((string)$article['excerpt']) ?></p>
          <?php endif; ?>
        </div>
        <div style="background:#FAF5EE;border:1px solid #EADCC8;border-radius:16px;padding:20px;margin:32px 0;font-size:13.5px;color:#5D4428">
          <b>Parenting Guidance Note:</b> This guide provides practical organisation and product comparisons for Pakistani families. For individual pediatric, feeding, or medical advice, consult your qualified pediatrician.
        </div>
        <?php if ($related): ?>
        <div style="border-top:1px solid var(--line);padding-top:24px;margin-top:30px">
          <h3 style="margin-bottom:16px">More Parenting Guides</h3>
          <div class="grid-articles">
            <?php foreach ($related as $a): ?>
              <a class="article rv" href="/articles/<?= rawurlencode((string)$a['id']) ?>">
                <span class="im"><img src="<?= learn_h(learn_cover((string)$a['cover'])) ?>" alt="<?= learn_h((string)$a['title']) ?>" loading="lazy" onerror="this.onerror=null;this.src='/art/placeholder.jpg'"></span>
                <div class="bd"><span class="tag tag-new" style="align-self:flex-start"><?= learn_h((string)$a['category']) ?></span><h3><?= learn_h((string)$a['title']) ?></h3><p><?= learn_h((string)$a['excerpt']) ?></p><span class="meta"><?= learn_h((string)$a['author']) ?> · <?= learn_h((string)$a['date']) ?> · <?= learn_h((string)$a['read_time']) ?></span></div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="container">
    <nav class="crumbs"><a href="/">Home</a> <span>/</span> <a href="/articles">Learn</a></nav>
    <div id="artListingView">
      <div class="page-head">
        <span class="eyebrow dotted">Learn</span>
        <h1 style="margin-top:12px">Guides for the week you're in</h1>
        <p>Clear, useful parenting guides for Pakistani families — organised to help you understand the next step, not overwhelm you with everything at once.</p>
        <div class="row wrap gap-s" style="margin-top:22px">
          <a class="chip <?= $selectedCat === '' ? 'on' : '' ?>" href="/articles">All</a>
          <?php foreach ($categories as $cat): ?>
            <a class="chip <?= strcasecmp($selectedCat,(string)$cat)===0 ? 'on' : '' ?>" href="/articles?cat=<?= rawurlencode((string)$cat) ?>"><?= learn_h((string)$cat) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="grid-articles" id="artGrid" style="padding-bottom:20px">
        <?php if (!$articles): ?>
          <div class="empty" style="grid-column:1/-1"><div class="box">📚</div><b>No guides in this category yet</b></div>
        <?php else: foreach ($articles as $a): ?>
          <a class="article rv" href="/articles/<?= rawurlencode((string)$a['id']) ?>">
            <span class="im"><img src="<?= learn_h(learn_cover((string)$a['cover'])) ?>" alt="<?= learn_h((string)$a['title']) ?>" loading="lazy" onerror="this.onerror=null;this.src='/art/placeholder.jpg'"></span>
            <div class="bd">
              <span class="tag tag-new" style="align-self:flex-start"><?= learn_h((string)$a['category']) ?></span>
              <h3><?= learn_h((string)$a['title']) ?></h3>
              <p><?= learn_h((string)$a['excerpt']) ?></p>
              <span class="meta"><?= learn_h((string)$a['author']) ?> · <?= learn_h((string)$a['date']) ?> · <?= learn_h((string)$a['read_time']) ?></span>
            </div>
          </a>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>

  <section class="sec">
    <div class="container">
      <div class="band rv">
        <div>
          <span class="eyebrow" style="color:#fce7f0">One gentle nudge a week</span>
          <h2 style="margin-top:12px">Guides matched to your baby's age</h2>
          <p>Tell us your baby's birth month or stage and we'll send exactly the right guide each week — nothing more.</p>
          <p class="tiny" style="color:#a399a8;margin-top:14px"><span class="ico" data-ico="heart" aria-hidden="true"></span> Practical parenting guidance · Review individual health questions with a qualified pediatrician.</p>
        </div>
        <div>
          <form class="form" id="newsletterForm" onsubmit="return handleNewsletterSubmit(event)" style="display:flex;flex-direction:column;gap:10px">
            <div style="display:flex;gap:8px;flex-wrap:wrap">
              <input type="email" id="nlEmail" placeholder="you@example.com" required aria-label="Email Address" style="flex:1;min-width:200px;padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,0.25);background:rgba(255,255,255,0.12);color:#fff">
              <select id="nlStage" required aria-label="Baby's birth month or stage" style="padding:12px 14px;border-radius:12px;border:1px solid rgba(255,255,255,0.25);background:#2d1b28;color:#fff">
                <option value="">Select Baby Stage / Month</option><option value="Expecting Mom">Expecting (Due soon)</option><option value="Newborn (0–3m)">Newborn (0–3 months)</option><option value="Infant (4–6m)">Infant (4–6 months)</option><option value="Infant (7–12m)">Infant (7–12 months)</option><option value="Toddler (1–2y)">Toddler (1–2 years)</option><option value="Toddler (2–3y)">Toddler (2–3 years)</option>
              </select>
            </div>
            <button type="submit" id="nlSubmitBtn" class="btn btn-primary" style="padding:12px 20px;border-radius:12px;font-weight:700">Send me personalized guides <span class="ico" data-ico="arrowUpRight" aria-hidden="true"></span></button>
          </form>
          <div id="nlFeedback" style="display:none;margin-top:12px;padding:12px;border-radius:12px;font-size:13.5px"></div>
        </div>
      </div>
    </div>
  </section>
  <section class="sec" style="padding-top:0"><div class="container"><div class="sec-head"><div><span class="eyebrow dotted">Also worth reading</span><h2 style="margin-top:12px">Answers the community loved</h2></div><a class="btn btn-ghost btn-s hide-s" href="/community">Open community <span class="ico" data-ico="arrowUpRight" aria-hidden="true"></span></a></div><div class="mini-feed" id="homeFeed"></div></div></section>
<?php endif; ?>
</main>

<div id="chrome-bottom"></div>
<script src="js/data.js?v=5.5-product-visual"></script>
<script src="js/app.js?v=5.5-product-visual"></script>
</body>
</html>
