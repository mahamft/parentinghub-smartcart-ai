<?php
declare(strict_types=1);
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
require_once __DIR__ . '/../../backend/api/bootstrap.php';
// Read-only connection: viewing a product must not run catalog seed/migrations.
try {
    $c = ph_db_config();
    $db = new PDO("mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4", $c['user'], $c['pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
    $query = $db->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $query->execute([$productId]);
    $row = $query->fetch();
    $shippingFee=200.0;
} catch (Throwable $e) {
    error_log('Product page database read failed: ' . $e->getMessage());
    ph_route_error(503);
}
if (!$row) ph_route_error(404);
function pd_escape($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function pd_json($s): array { $v=json_decode((string)$s,true); return is_array($v)?$v:[]; }
function pd_spec_value($v): ?string {
    if (!is_scalar($v)) return null;
    $v=trim((string)$v);
    if($v==='')return null;
    $generic=['n/a','na','none','not specified','not available','unknown','varies','please check','check packaging','ask us before ordering','contact us'];
    $low=strtolower($v);
    foreach($generic as $bad){if($low===$bad || str_contains($low,$bad))return null;}
    return $v;
}
function pd_media($v): string {
    if (!is_string($v)) return '';
    $v=trim($v);
    if ($v==='') return '';

    // Product images may be stored directly in MySQL as base64 data URIs.
    // Allow only common image MIME types with a base64 payload.
    if (preg_match('~^data:image/(?:png|jpe?g|webp|gif);base64,[A-Za-z0-9+/=\r\n]+$~i', $v)) {
        return $v;
    }

    if (preg_match('~[\x00-\x20<>"\x27\\\\]~',$v) || str_starts_with($v,'//')) return '';
    if (preg_match('~^https?://~i',$v)) return $v;
    if (preg_match('~^[a-z][a-z0-9+.-]*:~i',$v) || in_array('..',explode('/',$v),true)) return '';
    return '/' . ltrim($v,'/');
}
$images=[];
foreach (array_merge([$row['image'] ?? ''],pd_json($row['images'] ?? '[]')) as $url) {
    $url=pd_media($url);
    if ($url!=='' && !in_array($url,$images,true)) $images[]=$url;
}
if (!$images) $images=['/art/placeholder.jpg'];
$source=pd_json($row['source_json'] ?? '{}');
$specs=[];
$labels=['age'=>'Age Suitability','flow'=>'Teat / Flow Rate','material'=>'Material Safety','pack'=>'Pack Contents','compat'=>'Range Compatibility'];
foreach ($labels as $k=>$label) {
    $v=pd_spec_value($source['specs'][$k] ?? null);
    if ($v!==null) $specs[$k]=$v;
}
$p=['id'=>(string)$row['id'],'sku'=>(string)($row['sku']??''),'name'=>(string)$row['name'],'price'=>(float)$row['price'],'old'=>isset($row['compare_at'])?(float)$row['compare_at']:null,'img'=>$images[0],'image'=>$images[0],'images'=>$images,'brand'=>(string)$row['brand'],'cat'=>(string)$row['category'],'category'=>(string)$row['category'],'desc'=>(string)$row['description'],'inStock'=>(bool)$row['in_stock'],'hasOptions'=>(bool)$row['has_options'],'options'=>pd_json($row['options_json']??'[]'),'rating'=>(float)($row['rating']??0),'reviews'=>(int)($row['reviews']??0),'weightKg'=>isset($row['weight_kg'])?(float)$row['weight_kg']:null,'tags'=>pd_json($row['tags']??'[]'),'specs'=>$specs,'video'=>pd_media($row['video_url']??'')];
$title=preg_replace('/(\d+)\s*\*\s*(\d+)\s*ml/i','$1 × $2 ml',$p['name']);
$url='https://www.parentinghub.pk/product/'.rawurlencode($p['id']);
$jsonFlags=JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE;
$schemaImages=array_values(array_filter(array_map(static function($img){
    if (preg_match('~^https?://~i',$img)) return $img;
    if (str_starts_with($img,'data:image/')) return null;
    return 'https://www.parentinghub.pk/' . ltrim($img,'/');
}, $images)));
$shareImage=$schemaImages[0] ?? 'https://www.parentinghub.pk/logo.png';
$singleItemShipping=200.0;
$schema=['@context'=>'https://schema.org','@type'=>'Product','name'=>$p['name'],'description'=>$p['desc'],'image'=>($schemaImages ?: [$shareImage]),'sku'=>$p['sku'],'brand'=>['@type'=>'Brand','name'=>$p['brand']],'url'=>$url,'offers'=>['@type'=>'Offer','url'=>$url,'priceCurrency'=>'PKR','price'=>$p['price'],'availability'=>'https://schema.org/'.($p['inStock']?'InStock':'OutOfStock'),'itemCondition'=>'https://schema.org/NewCondition','seller'=>['@type'=>'Organization','name'=>'ParentingHub Pakistan'],'shippingDetails'=>['@type'=>'OfferShippingDetails','shippingDestination'=>['@type'=>'DefinedRegion','addressCountry'=>'PK'],'shippingRate'=>['@type'=>'MonetaryAmount','value'=>$singleItemShipping,'currency'=>'PKR']]]];
if($p['reviews']>0 && $p['rating']>0)$schema['aggregateRating']=['@type'=>'AggregateRating','ratingValue'=>$p['rating'],'reviewCount'=>$p['reviews']];
?>
<!doctype html><html lang="en"><head>
<base href="/"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=pd_escape($title)?> | ParentingHub Pakistan</title>
<meta name="description" content="<?=pd_escape($p['desc'])?>"><link rel="canonical" href="<?=pd_escape($url)?>">
<meta property="og:type" content="product"><meta property="og:title" content="<?=pd_escape($title)?>"><meta property="og:image" content="<?=pd_escape($shareImage)?>"><meta property="og:url" content="<?=pd_escape($url)?>"><meta property="og:description" content="<?=pd_escape($p['desc'])?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="/css/style.css?v=6.1-card-layout-fix">
<link rel="stylesheet" href="/css/smartcart-ai.css?v=1.0">
<link rel="icon" href="/favicon.png">
<script type="application/ld+json"><?=json_encode($schema,$jsonFlags)?></script>
</head><body data-page="product" data-product-id="<?=pd_escape($p['id'])?>">
<div id="chrome-top"></div>
<main class="container"><nav class="crumbs"><a href="/">Home</a> / <a href="/shop">Shop</a> / <?=pd_escape($title)?></nav>
<div class="product-modal-grid">
<div class="pm-gallery"><div class="pm-main-img" id="pmMainMediaBox"><img id="pmMainImg" src="<?=pd_escape($images[0])?>" alt="<?=pd_escape($title)?>" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='/art/placeholder.jpg'"></div>
<?php if(count($images)>1 || $p['video']): ?><div class="pm-thumbs">
<?php foreach($images as $i=>$img): ?><img class="pm-thumb <?=$i===0?'active':''?>" src="<?=pd_escape($img)?>" data-media="<?=pd_escape($img)?>" alt="<?=pd_escape($title)?> photo <?=$i+1?>" role="button" tabindex="0" onclick="setModalMedia('image',this.dataset.media,this)" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click()}" referrerpolicy="no-referrer"><?php endforeach; ?>
<?php if($p['video']): ?><button class="pm-thumb pm-thumb-video" data-media="<?=pd_escape($p['video'])?>" onclick="setModalMedia('video',this.dataset.media,this)">▶ Video</button><?php endif; ?>
</div><?php endif; ?></div>
<div class="pm-info"><div class="pm-badges"><span class="pm-badge-brand"><?=pd_escape($p['brand'])?></span><?php if($p['sku']): ?><span class="pm-badge-sku">Model: <?=pd_escape($p['sku'])?></span><?php endif; ?><span class="pm-badge-stock <?=$p['inStock']?'in':'out'?>"><?=$p['inStock']?'In Stock':'Out of Stock'?></span></div>
<h1 class="pm-title" id="pmModalTitle"><?=pd_escape($title)?></h1>
<div class="pm-price-wrap"><span class="pm-price">Rs <?=number_format($p['price'], $p['price']==floor($p['price'])?0:2)?></span><?php if($p['old']>$p['price']): ?><span class="pm-old-price">Rs <?=number_format($p['old'],2)?></span><?php endif; ?></div>
<div style="margin:10px 0 14px;padding:10px 12px;border:1px solid var(--line);border-radius:10px;background:var(--surface-2);font-size:13px;color:var(--ink-2)"><b>Delivery:</b> Fixed PKR 200 standard delivery charge per order across Pakistan.</div>
<div class="pm-desc"><p><?=nl2br(pd_escape($p['desc']))?></p>
<?php if($specs): ?><div class="pm-specs-wrap"><b>Product Specifications:</b><table><?php foreach($specs as $k=>$v): ?><tr><td class="k"><?=pd_escape($labels[$k])?>:</td><td class="v"><?=pd_escape($v)?></td></tr><?php endforeach; ?></table></div><?php endif; ?>
<div id="productPageOptions"></div>
<div class="pm-reviews-box"><b>Customer Reviews</b><p class="muted"><?=$p['reviews']>0 && $p['rating']>0 ? pd_escape($p['rating']).' / 5 ('.$p['reviews'].' reviews)' : 'No reviews yet.'?></p></div></div>
<div class="pm-actions"><div class="pm-qty-row"><span>Quantity:</span><div class="pm-qty-box"><button class="pm-qty-btn" aria-label="Decrease quantity" onclick="changeModalQty(-1)">−</button><span id="pmQtyVal" class="pm-qty-val">1</span><button class="pm-qty-btn" aria-label="Increase quantity" onclick="changeModalQty(1)">+</button></div><button class="btn btn-ghost btn-s" data-id="<?=pd_escape($p['id'])?>" onclick="toggleWish(this.dataset.id,this)">Wishlist</button></div>
<button class="btn btn-primary" onclick="addModalToCart()" <?=$p['inStock']?'':'disabled'?>><?=$p['inStock']?'Add to Bag':'Out of Stock'?></button></div>
</div></div></main><div id="chrome-bottom"></div>
<script src="/js/data.js?v=5.5-product-visual"></script>
<script>
// Override only this product before app boot so gallery, price, stock and cart agree.
(function(){const p=<?=json_encode($p,$jsonFlags)?>;const i=PRODUCTS.findIndex(x=>x.id===p.id);if(i<0)PRODUCTS.push(p);else PRODUCTS[i]=p;})();
</script>
<script src="/js/app.js?v=product-db-20261030"></script>
<script>
(function(){
  const pageProduct=PRODUCTS.find(x=>x.id===document.body.dataset.productId);
  if(!pageProduct) return;
  currentModalProduct=pageProduct; modalQty=1;
  const host=document.getElementById('productPageOptions');
  if(host) host.innerHTML=productOptionsHTML(pageProduct);
})();
</script>
<script src="/js/milestone-ai.js?v=5.5-product-visual"></script>
<script src="/js/smartcart-ai.js?v=1.0"></script>
</body></html>
