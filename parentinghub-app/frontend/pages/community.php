<?php
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
require_once dirname(__DIR__, 2) . '/backend/api/bootstrap.php';
$communityParents = null;
$communityAnswersMonth = null;
$communityAnswersTotal = null;
try {
    $c=ph_db_config();
    $db=new PDO(
        "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4",
        $c['user'],$c['pass'],
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]
    );
    $communityParents=(int)$db->query("SELECT COUNT(*) FROM ph_users WHERE role='parent' AND is_banned=0")->fetchColumn();
    $communityAnswersMonth=(int)$db->query("SELECT COUNT(*) FROM ph_community_answers WHERE created_at >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')")->fetchColumn();
    $communityAnswersTotal=(int)$db->query("SELECT COUNT(*) FROM ph_community_answers")->fetchColumn();
} catch(Throwable $e){ error_log('Community stats load failed: '.$e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="/">
<meta name="theme-color" content="#bd3d6b">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Parent Community Pakistan | ParentingHub</title>
<meta name="description" content="Useful conversations, not vanity metrics. Ask questions, share lived experience and save the answers that genuinely help Pakistani parents.">
<link rel="canonical" href="https://www.parentinghub.pk/community">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="en_PK">
<meta property="og:site_name" content="ParentingHub Pakistan">
<meta property="og:title" content="Parent Community Pakistan | ParentingHub">
<meta property="og:description" content="Useful conversations, not vanity metrics. Ask questions, share lived experience and save the answers that genuinely help Pakistani parents.">
<meta property="og:url" content="https://www.parentinghub.pk/community">
<meta property="og:image" content="https://www.parentinghub.pk/assets/visual/banner-bottles.jpg">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Parent Community Pakistan | ParentingHub">
<meta name="twitter:description" content="Useful conversations, not vanity metrics. Ask questions, share lived experience and save the answers that genuinely help Pakistani parents.">
<meta name="twitter:image" content="https://www.parentinghub.pk/assets/visual/banner-bottles.jpg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/style.css?v=6.0-community-mobile-v20">
  <!-- Favicons -->
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="icon" type="image/png" sizes="64x64" href="assets/favicon-64.png">
  <link rel="icon" type="image/png" sizes="192x192" href="icon-192.png">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png">
  <link rel="shortcut icon" href="favicon.ico">
</head>
<body data-page="community">
<div id="chrome-top"></div>

<main>
  <!-- ===== mobile-first community header ===== -->
  <section class="comm-hero">
    <div class="container">
      <div class="comm-hero-shell">
        <div class="comm-hero-copy">
          <span class="eyebrow dotted">ParentingHub Community</span>
          <h1>Ask parents. Share experience. <em>Find what helps.</em></h1>
          <p>A practical, moderated space for Pakistani parents — real questions, useful answers and no pressure to be perfect.</p>
          <div class="comm-hero-actions">
            <button class="btn btn-primary" onclick="focusAsk()"><span class="ico" data-ico="pencil" aria-hidden="true"></span> Ask a question</button>
            <a class="btn btn-ghost" href="/reels"><span class="ico" data-ico="play" aria-hidden="true"></span> Parent reels</a>
          </div>
        </div>

        <div class="comm-quick-stats" aria-label="Community overview">
          <span class="comm-stat"><b><?= $communityParents !== null ? number_format($communityParents) : 'Live' ?></b><small>Parents</small></span>
          <span class="comm-stat"><b><?= $communityAnswersMonth !== null ? number_format($communityAnswersMonth) : 'Recent' ?></b><small>Answers this month</small></span>
          <span class="comm-stat"><b><?= $communityAnswersTotal !== null ? number_format($communityAnswersTotal) : 'Live' ?></b><small>Total answers</small></span>
        </div>
      </div>

      <div class="comm-nav-wrap">
        <div class="tabs comm-tabs">
          <a class="tab active" href="#" data-tab="foryou"><span class="ico" data-ico="sparkle" aria-hidden="true"></span> For you</a>
          <a class="tab" href="#questions" data-tab="questions"><span class="ico" data-ico="pencil" aria-hidden="true"></span> Questions</a>
          <a class="tab" href="/reels"><span class="ico" data-ico="play" aria-hidden="true"></span> Reels</a>
          <a class="tab" href="/saved" data-tab="saved"><span class="ico" data-ico="save" aria-hidden="true"></span> Saved</a>
          <a class="tab" href="/profile"><span class="ico" data-ico="user" aria-hidden="true"></span> My profile</a>
        </div>
        <div class="comm-mobile-filter-wrap">
          <label for="mobileCommunityFilter">Community</label>
          <select id="mobileCommunityFilter" class="comm-mobile-filter" aria-label="Filter community"><option value="all">All parents</option></select>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== 3-column layout ===== -->
  <div class="container wide layout-3 community-layout">
    <!-- left rail -->
    <aside class="rail rail-l" id="commRail"></aside>

    <!-- feed -->
    <section class="community-feed">
      <!-- composer -->
      <div class="composer">
        <span class="avatar t">Y</span>
        <div class="grow">
          <input id="askTitle" placeholder="Ask a question or share something useful…" aria-label="Post title">
          <div class="composer-expand" id="composerExpand">
            <textarea id="askBody" placeholder="Add the details — what have you tried, what happened, what are you wondering about?"></textarea>
            <div class="row wrap gap-m" style="margin-top:10px">
              <select class="sort" id="askCommunity" aria-label="Choose community"></select>
              <select class="sort" id="askFlair" aria-label="Choose flair">
                <option>Question</option><option>Win</option><option>Guide</option><option>Recipe</option>
              </select>
              <span class="row gap-s tiny muted"><span class="ico" data-ico="camera" aria-hidden="true"></span> Photo · <span class="ico" data-ico="link" aria-hidden="true"></span> Link</span>
              <button class="btn btn-primary btn-s" style="margin-inline-start:auto" onclick="submitPost()">Post</button>
            </div>
          </div>
        </div>
      </div>

      <!-- feed toolbar -->
      <div class="feed-top">
        <span class="tiny" style="font-weight:700;color:var(--ink-3)">SORT BY</span>
        <button class="chip on" data-sort="top"><span class="ico" data-ico="sparkle" aria-hidden="true"></span> Best</button>
        <button class="chip" data-sort="new"><span class="ico" data-ico="clock" aria-hidden="true"></span> New</button>
        <span class="spacer"></span>
        <span class="tiny muted" id="feedMeta"></span>
      </div>

      <div id="feed"></div>
    </section>

    <!-- right rail -->
    <aside class="rail rail-r" id="commRailR"></aside>
  </div>
</main>

<!-- ===== post modal ===== -->
<div class="modal" id="postModal">
  <div class="scrim" onclick="closeAll()" style="position:fixed;inset:0;opacity:1;pointer-events:auto"></div>
  <div class="box">
    <header>
      <b class="serif" style="font-size:18px">Conversation</b>
      <button class="icon-btn" aria-label="Close" onclick="closeAll()"><span class="ico" data-ico="x" aria-hidden="true"></span></button>
    </header>
    <div class="pad" id="modalBody"></div>
  </div>
</div>

<div id="chrome-bottom"></div>
<script src="js/data.js?v=5.5-product-visual"></script>
<script src="js/app.js?v=5.5-product-visual"></script>
<script src="js/community-db.js?v=1.0"></script>
</body>
</html>
