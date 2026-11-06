<?php
declare(strict_types=1);
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
require_once __DIR__ . '/bootstrap.php';
header('Content-Type: application/javascript; charset=utf-8');
header('Cache-Control: public, max-age=60, stale-while-revalidate=300');
$pdo = ph_pdo();

function phfd_table_exists(PDO $pdo, string $table): bool {
    try {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $st = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name = ?");
            $st->execute([$table]);
            return (bool)$st->fetchColumn();
        }
        $st = $pdo->prepare("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
        $st->execute([$table]);
        return (bool)$st->fetchColumn();
    } catch (Throwable $e) { return false; }
}
function phfd_json_array($v): array {
    if (is_array($v)) return array_values($v);
    if (!is_string($v) || trim($v)==='') return [];
    $d=json_decode($v,true); return is_array($d)?array_values($d):[];
}
function phfd_json_obj($v): array {
    if (is_array($v)) return $v;
    if (!is_string($v) || trim($v)==='') return [];
    $d=json_decode($v,true); return is_array($d)?$d:[];
}
function phfd_emit(string $name, $value): void {
    echo 'const '.$name.' = '.json_encode($value, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE).";\n";
}

$state=[];
try {
    if (phfd_table_exists($pdo,'ph_catalog_state')) {
        $st=$pdo->prepare("SELECT payload FROM ph_catalog_state WHERE state_key='catalog' LIMIT 1"); $st->execute();
        $raw=$st->fetchColumn(); if (is_string($raw)) { $tmp=json_decode($raw,true); if(is_array($tmp))$state=$tmp; }
    }
} catch(Throwable $e) { error_log('frontend-data catalog state: '.$e->getMessage()); }

$products=[];
if (phfd_table_exists($pdo,'products')) {
    $cols=$pdo->query('SHOW COLUMNS FROM products')->fetchAll(PDO::FETCH_COLUMN,0);
    $rows=$pdo->query('SELECT * FROM products ORDER BY name ASC')->fetchAll();
    foreach($rows as $r){
        $images=phfd_json_array($r['images']??'[]');
        $img=trim((string)($r['image']??'')); if($img!==''&&!in_array($img,$images,true))array_unshift($images,$img);
        $source=phfd_json_obj($r['source_json']??'{}');
        $options=phfd_json_array($r['options_json']??'[]');
        $tags=phfd_json_array($r['tags']??'[]');
        $products[]=[
            'id'=>(string)($r['id']??''),'sku'=>(string)($r['sku']??''),'name'=>(string)($r['name']??''),
            'price'=>(float)($r['price']??0),'compareAt'=>isset($r['compare_at'])&&$r['compare_at']!==null?(float)$r['compare_at']:null,
            'old'=>isset($r['compare_at'])&&$r['compare_at']!==null?(float)$r['compare_at']:null,
            // Keep the global browser payload compact: cards/cart need only one primary image.
            // Full galleries, video and source metadata are loaded server-side on the product detail page.
            'image'=>$img,'img'=>$img,'images'=>$img!==''?[$img]:[],'category'=>(string)($r['category']??''),'cat'=>(string)($r['category']??''),
            'brand'=>(string)($r['brand']??''),'description'=>(string)($r['description']??($source['description']??'')),'desc'=>(string)($r['description']??($source['description']??'')),
            'hasOptions'=>(bool)($r['has_options']??false),'options'=>$options,'rating'=>(float)($r['rating']??0),'reviews'=>(int)($r['reviews']??0),
            'inStock'=>(bool)($r['in_stock']??true),'weightKg'=>isset($r['weight_kg'])&&$r['weight_kg']!==null?(float)$r['weight_kg']:null
        ];
    }
}

// Taxonomy is derived from the live products table. Optional DB metadata from catalog state enriches it.
$metaCats=[]; foreach(($state['categories']??[]) as $c){ if(is_array($c)&&isset($c['name']))$metaCats[strtolower((string)$c['name'])]=$c; }
$catNames=[]; $brandNames=[];
foreach(($state['categories']??[]) as $c){ if(is_array($c)&&trim((string)($c['name']??''))!=='')$catNames[trim((string)$c['name'])]=true; }
foreach(($state['brands']??[]) as $b){ $bn=is_array($b)?trim((string)($b['name']??'')):trim((string)$b); if($bn!=='')$brandNames[$bn]=true; }
foreach($products as $p){ if(trim($p['category'])!=='')$catNames[$p['category']]=true; if(trim($p['brand'])!=='')$brandNames[$p['brand']]=true; }
ksort($catNames,SORT_NATURAL|SORT_FLAG_CASE); ksort($brandNames,SORT_NATURAL|SORT_FLAG_CASE);
$categories=[];
foreach(array_keys($catNames) as $name){
    $m=$metaCats[strtolower($name)]??[]; $handle=$m['handle']??strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$name),'-'));
    $sample=''; foreach($products as $p){if($p['category']===$name&&$p['image']!==''){$sample=$p['image'];break;}}
    $categories[]=['handle'=>$handle,'name'=>$name,'image'=>$m['image']??$sample,'desc'=>$m['desc']??('Browse '.$name.' products available at ParentingHub.')];
}
$brands=array_keys($brandNames);

$articles=[];
if (phfd_table_exists($pdo,'articles')) {
    foreach($pdo->query('SELECT * FROM articles ORDER BY created_at DESC, id DESC')->fetchAll() as $r){
        // Public JS only needs card metadata; article bodies are rendered server-side on /articles/{slug}.
        $excerpt=(string)($r['excerpt']??''); $cover=(string)($r['cover']??''); $author=(string)($r['author']??''); $read=(string)($r['read_time']??''); $category=(string)($r['category']??'');
        $articles[]=['id'=>(string)$r['id'],'title'=>(string)$r['title'],'excerpt'=>$excerpt,'ex'=>$excerpt,'cover'=>$cover,'img'=>$cover,'author'=>$author,'by'=>$author,'date'=>(string)($r['date']??''),'readTime'=>$read,'read'=>$read,'category'=>$category,'cat'=>$category,'body'=>[]];
    }
}

$communities=[]; if(phfd_table_exists($pdo,'ph_community_categories')) {
    $communityRows=$pdo->query("SELECT c.community_key AS k,c.label,c.icon AS em,COUNT(p.id) AS posts FROM ph_community_categories c LEFT JOIN ph_community_posts p ON p.community=c.community_key WHERE c.active=1 GROUP BY c.community_key,c.label,c.icon,c.sort_order ORDER BY c.sort_order,c.label")->fetchAll();
    $communities=array_map(static fn($r)=>['k'=>(string)$r['k'],'label'=>(string)$r['label'],'em'=>(string)$r['em'],'posts'=>(int)$r['posts']],$communityRows);
}
$posts=[]; if(phfd_table_exists($pdo,'ph_community_posts')) foreach($pdo->query('SELECT p.*,(SELECT COUNT(*) FROM ph_community_answers a WHERE a.post_id=p.id) AS comments FROM ph_community_posts p ORDER BY p.created_at DESC LIMIT 30')->fetchAll() as $r){
    $ts=strtotime((string)$r['created_at']); $posts[]=['id'=>(string)$r['id'],'community'=>(string)$r['community'],'au'=>(string)$r['author_name'],'av'=>(string)($r['author_avatar']?:'g'),'time'=>$ts?date('d M Y H:i',$ts):'','flair'=>(string)$r['flair'],'title'=>(string)$r['title'],'body'=>(string)$r['body'],'votes'=>(int)$r['votes'],'my'=>0,'comments'=>(int)$r['comments'],'createdAt'=>$ts?$ts*1000:0];
}
$reels=[]; if(phfd_table_exists($pdo,'ph_community_reels')) foreach($pdo->query("SELECT r.*,(SELECT COUNT(*) FROM ph_reel_comments c WHERE c.reel_id=r.id) AS comments FROM ph_community_reels r WHERE r.status='published' ORDER BY r.sort_order,r.created_at DESC LIMIT 24")->fetchAll() as $r){
    $reels[]=['id'=>(string)$r['id'],'title'=>(string)$r['title'],'views'=>(int)$r['views'],'likes'=>(int)$r['likes'],'img'=>(string)($r['thumbnail_url']?:$r['media_url']),'media'=>(string)$r['media_url'],'by'=>(string)$r['author_name'],'tag'=>(string)$r['tag'],'caption'=>(string)$r['caption'],'comments'=>(int)($r['comments']??0)];
}

$settings=is_array($state['settings']??null)?$state['settings']:[];
try {
    foreach($pdo->query('SELECT key_name,value_text FROM site_settings')->fetchAll() as $r){
        $k=(string)($r['key_name']??''); if($k==='') continue; $v=(string)($r['value_text']??'');
        if($k==='shippingFee')$settings[$k]=200.0;
        elseif(in_array($k,['codEnabled','bankEnabled'],true))$settings[$k]=in_array(strtolower($v),['1','true','yes','on'],true);
        else $settings[$k]=$v;
    }
} catch(Throwable $e) {}
$settings['shippingFee']=200.0;
unset($settings['deliveryRatePerKg'],$settings['defaultProductWeightKg'],$settings['freeShippingAbove']);
foreach(['navigationItems'=>'path','navItems'=>'url'] as $listKey=>$urlKey){
    if(!isset($settings[$listKey])||!is_array($settings[$listKey]))continue;
    $seen=[];$clean=[];
    foreach($settings[$listKey] as $item){
        if(!is_array($item))continue;$url=trim((string)($item[$urlKey]??''));
        if($url==='/parent'||$url==='/parent/')$url='/profile';if(strtolower($url)==='/home')$url='/';
        $item[$urlKey]=$url;$dedupe=rtrim($url,'/')?:'/';if(isset($seen[$dedupe]))continue;$seen[$dedupe]=true;$clean[]=$item;
    }
    $settings[$listKey]=$clean;
}
$home=is_array($state['homeData']??null)?$state['homeData']:['slides'=>[],'brands'=>[],'collections'=>[],'banners'=>[],'sections'=>[]];
// Ensure homepage brand/category displays reflect live DB taxonomy, not stale seed arrays.
$home['brands']=array_map(fn($b)=>['handle'=>strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$b),'-')),'name'=>$b,'image'=>''],$brands);
$home['collections']=array_map(fn($c)=>['handle'=>$c['handle'],'title'=>$c['name'],'image'=>$c['image']],$categories);
$faqs=is_array($state['faqs']??null)?$state['faqs']:[];
foreach($faqs as &$faq){
    if(!is_array($faq))continue;
    $q=strtolower(trim((string)($faq['q']??'')));
    if(str_contains($q,'free shipping') || str_contains($q,'delivery charges') || str_contains($q,'shipping cost')){
        $faq['q']='What are the delivery charges?';
        $faq['a']='A fixed PKR 200 standard delivery charge applies to every order across Pakistan. There is no free-delivery threshold and the fee does not vary by product weight.';
        $faq['category']='Shipping';
    }
}
unset($faq);
$coupons=is_array($state['coupons']??null)?$state['coupons']:[];

phfd_emit('SETTINGS',$settings); phfd_emit('HOMEDATA',$home); phfd_emit('BRANDS',$brands); phfd_emit('CATEGORIES',$categories); phfd_emit('PRODUCTS',$products); phfd_emit('ARTICLES',$articles); phfd_emit('FAQS',$faqs); phfd_emit('COUPONS',$coupons); phfd_emit('COMMUNITIES',$communities); phfd_emit('REELS',$reels); phfd_emit('POSTS',$posts);
echo "const money = n => 'Rs ' + Math.round(Number(n) || 0).toLocaleString('en-PK');\n";
echo "const star = r => { const f=Math.max(0,Math.min(5,Math.round(Number(r)||0))); return '★★★★★'.slice(0,f)+'☆☆☆☆☆'.slice(0,5-f); };\n";
?>
