<?php
declare(strict_types=1);
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600');

function phsm_x(string $v): string { return htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8'); }
function phsm_slug(string $v): string {
    $v = strtolower(trim($v));
    $v = preg_replace('/[^a-z0-9]+/i', '-', $v) ?? '';
    return trim($v, '-');
}
function phsm_abs(string $path): string { return 'https://www.parentinghub.pk' . ($path === '/' ? '/' : '/' . ltrim($path, '/')); }

$urls = [
    ['loc'=>phsm_abs('/'),'priority'=>'1.0','changefreq'=>'daily'],
    ['loc'=>phsm_abs('/shop'),'priority'=>'0.9','changefreq'=>'daily'],
    ['loc'=>phsm_abs('/articles'),'priority'=>'0.8','changefreq'=>'weekly'],
    ['loc'=>phsm_abs('/community'),'priority'=>'0.7','changefreq'=>'daily'],
    ['loc'=>phsm_abs('/about'),'priority'=>'0.5','changefreq'=>'monthly'],
    ['loc'=>phsm_abs('/support'),'priority'=>'0.4','changefreq'=>'monthly'],
];
$seen = array_fill_keys(array_column($urls,'loc'), true);

try {
    $c = ph_db_config();
    $pdo = new PDO(
        "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4",
        $c['user'], $c['pass'],
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]
    );

    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN, 0);
    if (in_array('products', $tables, true)) {
        $rows = $pdo->query("SELECT id, category, brand FROM products WHERE id<>'' ORDER BY id")->fetchAll();
        $cats=[]; $brands=[];
        foreach ($rows as $r) {
            $id=trim((string)$r['id']);
            if ($id!=='') {
                $loc=phsm_abs('/product/'.rawurlencode($id));
                if(!isset($seen[$loc])) { $urls[]=['loc'=>$loc,'priority'=>'0.7','changefreq'=>'weekly']; $seen[$loc]=true; }
            }
            $cat=trim((string)($r['category']??'')); if($cat!=='') $cats[$cat]=true;
            $brand=trim((string)($r['brand']??'')); if($brand!=='') $brands[$brand]=true;
        }
        foreach(array_keys($cats) as $cat){ $slug=phsm_slug($cat); if($slug==='')continue; $loc=phsm_abs('/shop/categories/'.$slug); if(!isset($seen[$loc])){$urls[]=['loc'=>$loc,'priority'=>'0.6','changefreq'=>'weekly'];$seen[$loc]=true;} }
        foreach(array_keys($brands) as $brand){ $slug=phsm_slug($brand); if($slug==='')continue; $loc=phsm_abs('/shop/brands/'.$slug); if(!isset($seen[$loc])){$urls[]=['loc'=>$loc,'priority'=>'0.5','changefreq'=>'weekly'];$seen[$loc]=true;} }
    }
    if (in_array('articles', $tables, true)) {
        foreach($pdo->query("SELECT id FROM articles WHERE id<>'' ORDER BY id")->fetchAll(PDO::FETCH_COLUMN,0) as $id){
            $loc=phsm_abs('/articles/'.rawurlencode((string)$id));
            if(!isset($seen[$loc])){$urls[]=['loc'=>$loc,'priority'=>'0.7','changefreq'=>'monthly'];$seen[$loc]=true;}
        }
    }
} catch (Throwable $e) {
    error_log('Dynamic sitemap generation failed: '.$e->getMessage());
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach($urls as $u){
    echo "  <url><loc>".phsm_x($u['loc'])."</loc><changefreq>".phsm_x($u['changefreq'])."</changefreq><priority>".phsm_x($u['priority'])."</priority></url>\n";
}
echo "</urlset>\n";
