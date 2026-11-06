<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$apiPos = strpos($uriPath, '/api');
$path = $apiPos !== false ? substr($uriPath, $apiPos + 4) : $uriPath;
$path = '/' . trim($path, '/');
if ($path === '//') $path = '/';

// The parenting advisor does not require a database connection. Keep it usable even
// during temporary catalog/DB maintenance, while still loading server-only env config.
if ($path === '/milestone') {
    require __DIR__ . '/milestone.php';
    exit;
}

$pdo = ph_pdo();

// SmartCart AI Endpoints
if (str_starts_with($path, '/ai/') || $path === '/ai') {
    require __DIR__ . '/ai/router.php';
    exit;
}
if ($path === '/cart/propose' || $path === '/cart/approve') {
    $path = '/ai' . $path;
    require __DIR__ . '/ai/router.php';
    exit;
}

// PayPal Sandbox Checkout Endpoints
if (str_starts_with($path, '/paypal/') || $path === '/paypal') {
    require __DIR__ . '/paypal/router.php';
    exit;
}


$phLoadSiteSettings = static function(PDO $pdo, array $base = []): array {
    $settings = $base;
    try {
        $rows = $pdo->query('SELECT key_name,value_text FROM site_settings')->fetchAll();
        foreach ($rows as $row) {
            $key = (string)($row['key_name'] ?? '');
            if ($key === '') continue;
            $value = (string)($row['value_text'] ?? '');
            if ($key === 'shippingFee') {
                $settings[$key] = 200.0;
            } elseif (in_array($key, ['codEnabled','bankEnabled'], true)) {
                $settings[$key] = in_array(strtolower($value), ['1','true','yes','on'], true);
            } else {
                $settings[$key] = $value;
            }
        }
    } catch (Throwable $e) {
        // site_settings is optional on older databases; catalog settings remain the fallback.
    }
    // Official store policy: one fixed PKR 200 delivery fee per non-empty order.
    // Legacy weight/free-shipping settings may remain in the DB for backward compatibility,
    // but public APIs and checkout must not use them.
    $settings['shippingFee'] = 200.0;
    $settings['deliveryPolicy'] = 'fixed';
    unset($settings['deliveryRatePerKg'], $settings['defaultProductWeightKg'], $settings['freeShippingAbove']);
    foreach (['navigationItems'=>'path','navItems'=>'url'] as $listKey=>$urlKey) {
        if (!isset($settings[$listKey]) || !is_array($settings[$listKey])) continue;
        $seen = []; $clean = [];
        foreach ($settings[$listKey] as $item) {
            if (!is_array($item)) continue;
            $url = trim((string)($item[$urlKey] ?? ''));
            if ($url === '/parent' || $url === '/parent/') $url = '/profile';
            if (strtolower($url) === '/home') $url = '/';
            $item[$urlKey] = $url;
            $dedupe = rtrim($url, '/') ?: '/';
            if (isset($seen[$dedupe])) continue;
            $seen[$dedupe] = true; $clean[] = $item;
        }
        $settings[$listKey] = $clean;
    }
    return $settings;
};

if ($method === 'GET' && $path === '/health') {
    // Public health check exposes only non-sensitive catalog/content totals.
    $counts = ['products'=>0,'articles'=>0,'communityPosts'=>0];
    foreach (['products'=>'products','articles'=>'articles','ph_community_posts'=>'communityPosts'] as $table=>$key) {
        try { $counts[$key] = (int)$pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn(); } catch (Throwable) {}
    }
    ph_json(['success'=>true,'message'=>'Database connected successfully','apiVersion'=>PH_API_VERSION,'databaseConnected'=>true,'counts'=>$counts]);
}


// Lightweight public bridge used by the WordPress homepage.
// Returns only a small product subset so WordPress never has to download the full catalog.
if ($method === 'GET' && $path === '/wp-home-products') {
    $limit = (int)($_GET['limit'] ?? 8);
    $limit = max(1, min(12, $limit));
    try {
        $cols = $pdo->query('SHOW COLUMNS FROM products')->fetchAll(PDO::FETCH_COLUMN, 0);
        $timeCol = in_array('updated_at', $cols, true) ? 'updated_at' : (in_array('created_at', $cols, true) ? 'created_at' : null);
        $order = $timeCol ? "`{$timeCol}` DESC, name ASC" : 'name ASC';
        $rows = $pdo->query("SELECT id,name,price,compare_at,image,category,brand,in_stock,rating,reviews FROM products WHERE in_stock=1 ORDER BY {$order} LIMIT {$limit}")->fetchAll();
        $products = [];
        foreach ($rows as $r) {
            $image = trim((string)($r['image'] ?? ''));
            if ($image !== '' && !preg_match('~^(?:https?://|data:|blob:)~i', $image)) $image = '/' . ltrim($image, '/');
            $id = (string)($r['id'] ?? '');
            $products[] = [
                'id' => $id,
                'name' => (string)($r['name'] ?? ''),
                'price' => (float)($r['price'] ?? 0),
                'compareAt' => isset($r['compare_at']) && $r['compare_at'] !== null ? (float)$r['compare_at'] : null,
                'image' => $image,
                'category' => (string)($r['category'] ?? ''),
                'brand' => (string)($r['brand'] ?? ''),
                'inStock' => (bool)($r['in_stock'] ?? true),
                'rating' => (float)($r['rating'] ?? 0),
                'reviews' => (int)($r['reviews'] ?? 0),
                'url' => '/product/' . rawurlencode($id),
            ];
        }
        header('Cache-Control: public, max-age=120');
        ph_json(['success'=>true,'products'=>$products,'count'=>count($products)]);
    } catch (Throwable $e) {
        error_log('WP home product bridge failed: ' . $e->getMessage());
        ph_json(['success'=>false,'products'=>[],'count'=>0], 200);
    }
}


// Lightweight read-only bridge for the WordPress Learn page during migration.
if ($method === 'GET' && $path === '/wp-learn-articles') {
    $limit = (int)($_GET['limit'] ?? 12);
    $limit = max(1, min(24, $limit));
    try {
        $rows = $pdo->query("SELECT id,title,excerpt,cover,author,date,read_time,category,created_at FROM articles ORDER BY created_at DESC, id DESC LIMIT {$limit}")->fetchAll();
        $articles = [];
        foreach ($rows as $r) {
            $cover = trim((string)($r['cover'] ?? ''));
            if ($cover !== '' && !preg_match('~^(?:https?://|data:|blob:)~i', $cover)) $cover = '/' . ltrim($cover, '/');
            $id = (string)($r['id'] ?? '');
            $articles[] = [
                'id' => $id,
                'title' => (string)($r['title'] ?? ''),
                'excerpt' => (string)($r['excerpt'] ?? ''),
                'cover' => $cover,
                'author' => (string)($r['author'] ?? ''),
                'date' => (string)($r['date'] ?? ''),
                'readTime' => (string)($r['read_time'] ?? ''),
                'category' => (string)($r['category'] ?? ''),
                'url' => '/articles/' . rawurlencode($id),
            ];
        }
        header('Cache-Control: public, max-age=120');
        ph_json(['success'=>true,'articles'=>$articles,'count'=>count($articles)]);
    } catch (Throwable $e) {
        error_log('WP learn article bridge failed: ' . $e->getMessage());
        ph_json(['success'=>false,'articles'=>[],'count'=>0], 200);
    }
}

if ($path === '/catalog') {
    $catalogProductFromRow = static function(array $r): array {
        $arr = static function($v): array { $d=is_string($v)?json_decode($v,true):$v; return is_array($d)?array_values($d):[]; };
        $images=$arr($r['images']??'[]'); $image=trim((string)($r['image']??''));
        if($image!==''&&!in_array($image,$images,true))array_unshift($images,$image);
        return [
            'id'=>(string)($r['id']??''),'sku'=>(string)($r['sku']??''),'name'=>(string)($r['name']??''),
            'price'=>(float)($r['price']??0),'compareAt'=>isset($r['compare_at'])&&$r['compare_at']!==null?(float)$r['compare_at']:null,
            'old'=>isset($r['compare_at'])&&$r['compare_at']!==null?(float)$r['compare_at']:null,
            'image'=>$image,'img'=>$image,'images'=>$images,'category'=>(string)($r['category']??''),'cat'=>(string)($r['category']??''),
            'brand'=>(string)($r['brand']??''),'description'=>(string)($r['description']??''),'desc'=>(string)($r['description']??''),
            'hasOptions'=>(bool)($r['has_options']??false),'options'=>$arr($r['options_json']??'[]'),
            'rating'=>(float)($r['rating']??0),'reviews'=>(int)($r['reviews']??0),'inStock'=>(bool)($r['in_stock']??true),
            'weightKg'=>isset($r['weight_kg'])&&$r['weight_kg']!==null?(float)$r['weight_kg']:null,'tags'=>$arr($r['tags']??'[]'),
            'sourceUrl'=>(string)($r['source_url']??''),'video'=>(string)($r['video_url']??'')
        ];
    };
    $loadCatalogState = static function(PDO $pdo): array {
        $st=$pdo->prepare("SELECT payload FROM ph_catalog_state WHERE state_key='catalog' LIMIT 1"); $st->execute();
        $raw=$st->fetchColumn(); $d=is_string($raw)?json_decode($raw,true):null; return is_array($d)?$d:[];
    };
    $deriveTaxonomy = static function(array $products, array $state): array {
        $catMeta=[]; foreach(($state['categories']??[]) as $c)if(is_array($c)&&isset($c['name']))$catMeta[strtolower((string)$c['name'])]=$c;
        $cats=[];$brands=[];foreach(($state['categories']??[]) as $c){if(is_array($c)){ $cn=trim((string)($c['name']??'')); if($cn!=='')$cats[$cn]=true; }}foreach(($state['brands']??[]) as $b){$bn=is_array($b)?trim((string)($b['name']??'')):trim((string)$b);if($bn!=='')$brands[$bn]=true;}foreach($products as $p){$cn=trim((string)($p['category']??''));$bn=trim((string)($p['brand']??''));if($cn!=='')$cats[$cn]=true;if($bn!=='')$brands[$bn]=true;}
        ksort($cats,SORT_NATURAL|SORT_FLAG_CASE);ksort($brands,SORT_NATURAL|SORT_FLAG_CASE);
        $outCats=[];foreach(array_keys($cats) as $name){$m=$catMeta[strtolower($name)]??[];$handle=$m['handle']??strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$name),'-'));$sample='';foreach($products as $p){if(($p['category']??'')===$name&&!empty($p['image'])){$sample=(string)$p['image'];break;}}$outCats[]=['handle'=>$handle,'name'=>$name,'image'=>$m['image']??$sample,'desc'=>$m['desc']??('Browse '.$name.' products available at ParentingHub.')];}
        return [$outCats,array_keys($brands)];
    };

    if ($method === 'GET') {
        $state=$loadCatalogState($pdo);
        $rows=$pdo->query('SELECT * FROM products ORDER BY name ASC')->fetchAll();
        $products=array_map($catalogProductFromRow,$rows);
        [$categories,$brands]=$deriveTaxonomy($products,$state);
        $catalog=[
            'products'=>$products,'categories'=>$categories,'brands'=>$brands,
            'settings'=>$phLoadSiteSettings($pdo, is_array($state['settings']??null)?$state['settings']:[]),
            'homeData'=>is_array($state['homeData']??null)?$state['homeData']:['slides'=>[],'brands'=>[],'collections'=>[],'banners'=>[],'sections'=>[]],
            'articles'=>[],'faqs'=>is_array($state['faqs']??null)?$state['faqs']:[],
            'coupons'=>is_array($state['coupons']??null)?$state['coupons']:[]
        ];
        foreach($catalog['faqs'] as &$faq){
            if(!is_array($faq))continue;
            $q=strtolower(trim((string)($faq['q']??'')));
            if(str_contains($q,'free shipping') || str_contains($q,'delivery charges') || str_contains($q,'shipping cost')){
                $faq['q']='What are the delivery charges?';
                $faq['a']='A fixed PKR 200 delivery charge applies to every order across Pakistan. There is no free-delivery threshold and the fee does not vary by product weight.';
                $faq['category']='Shipping';
            }
        }
        unset($faq);
        try{
            $arts=$pdo->query('SELECT * FROM articles ORDER BY created_at DESC,id DESC')->fetchAll();
            foreach($arts as $r){$body=json_decode((string)($r['body_json']??'[]'),true);$catalog['articles'][]=['id'=>(string)$r['id'],'title'=>(string)$r['title'],'excerpt'=>(string)($r['excerpt']??''),'cover'=>(string)($r['cover']??''),'author'=>(string)($r['author']??''),'date'=>(string)($r['date']??''),'readTime'=>(string)($r['read_time']??''),'category'=>(string)($r['category']??''),'body'=>is_array($body)?array_values($body):[]];}
        }catch(Throwable $e){}
        ph_json(['success'=>true,'catalog'=>$catalog]);
    }
    if ($method === 'PUT') {
        ph_require_admin($pdo);
        $body=ph_body(40*1024*1024);
        if(!isset($body['products'])||!is_array($body['products']))ph_error('products array is required.',422);
        $products=$body['products'];
        $pdo->beginTransaction();
        try{
            $up=$pdo->prepare("INSERT INTO products (id,sku,name,price,compare_at,image,images,category,brand,description,has_options,options_json,rating,reviews,in_stock,weight_kg,tags,source_url,video_url,source_json,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW()) ON DUPLICATE KEY UPDATE sku=VALUES(sku),name=VALUES(name),price=VALUES(price),compare_at=VALUES(compare_at),image=VALUES(image),images=VALUES(images),category=VALUES(category),brand=VALUES(brand),description=VALUES(description),has_options=VALUES(has_options),options_json=VALUES(options_json),rating=VALUES(rating),reviews=VALUES(reviews),in_stock=VALUES(in_stock),weight_kg=VALUES(weight_kg),tags=VALUES(tags),source_url=VALUES(source_url),video_url=VALUES(video_url),source_json=VALUES(source_json),updated_at=NOW()");
            $ids=[];
            foreach($products as $p){
                if(!is_array($p))continue;$id=trim((string)($p['id']??''));$name=trim((string)($p['name']??''));if($id===''||$name==='')continue;$ids[]=$id;
                $images=$p['images']??[];if(!is_array($images))$images=[];$options=$p['options']??[];if(!is_array($options))$options=[];$tags=$p['tags']??[];if(!is_array($tags))$tags=[];
                $image=trim((string)($p['image']??$p['img']??($images[0]??'')));$category=trim((string)($p['category']??$p['cat']??'General'));$brand=trim((string)($p['brand']??'ParentingHub'));
                $desc=(string)($p['description']??$p['desc']??'');$compare=$p['compareAt']??$p['old']??null;$compare=($compare===null||$compare==='')?null:(float)$compare;
                $source=$p;$source['image']=$image;$source['img']=$image;$source['category']=$category;$source['cat']=$category;
                $up->execute([$id,trim((string)($p['sku']??''))?:null,$name,(float)($p['price']??0),$compare,$image,json_encode(array_values($images),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),$category,$brand,$desc,!empty($p['hasOptions'])?1:0,json_encode(array_values($options),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),(float)($p['rating']??0),(int)($p['reviews']??0),array_key_exists('inStock',$p)?(!empty($p['inStock'])?1:0):1,isset($p['weightKg'])&&$p['weightKg']!==''?(float)$p['weightKg']:null,json_encode(array_values($tags),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),trim((string)($p['sourceUrl']??''))?:null,trim((string)($p['video']??$p['videoUrl']??''))?:null,json_encode($source,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)]);
            }
            // Admin sends the complete catalog; deleted rows in the UI are deleted from MySQL too.
            if($ids){$placeholders=implode(',',array_fill(0,count($ids),'?'));$del=$pdo->prepare("DELETE FROM products WHERE id NOT IN ($placeholders)");$del->execute($ids);}
            elseif(count($products)===0){$pdo->exec('DELETE FROM products');}

            $state=$loadCatalogState($pdo);
            foreach(['settings','homeData','faqs','coupons'] as $k){if(isset($body[$k])&&is_array($body[$k]))$state[$k]=$body[$k];}
            // Categories and brands are stored as metadata but product truth remains the products table.
            if(isset($body['categories'])&&is_array($body['categories']))$state['categories']=$body['categories'];
            if(isset($body['brands'])&&is_array($body['brands']))$state['brands']=$body['brands'];
            unset($state['products'],$state['articles']);
            $payload=json_encode($state,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);if($payload===false)throw new RuntimeException('Catalog state could not be encoded.');
            $st=$pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES ('catalog',?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),updated_at=NOW()");$st->execute([$payload]);
            $pdo->commit();
            ph_json(['success'=>true,'message'=>'Products and catalog settings saved directly to MySQL.','productCount'=>count($ids)]);
        }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();error_log('Catalog save failed: '.$e->getMessage());ph_error('Catalog database save failed.',500);}
    }
    ph_error('Method not allowed.',405);
}


/* Learn / Articles - database backed */
$phLearnArticleFromRow = static function(array $r): array {
    $body = json_decode((string)($r['body_json'] ?? '[]'), true);
    if (!is_array($body)) $body = [];
    return [
        'id' => (string)$r['id'],
        'title' => (string)$r['title'],
        'excerpt' => (string)$r['excerpt'],
        'cover' => (string)$r['cover'],
        'author' => (string)$r['author'],
        'date' => (string)$r['date'],
        'readTime' => (string)$r['read_time'],
        'category' => (string)$r['category'],
        'body' => array_values(array_map('strval', $body)),
        'createdAt' => (string)($r['created_at'] ?? ''),
    ];
};

if ($path === '/learn/articles') {
    if ($method !== 'GET') ph_error('Method not allowed.', 405);
    $cat = trim((string)($_GET['cat'] ?? ''));
    if ($cat !== '') {
        $st = $pdo->prepare('SELECT * FROM articles WHERE category=? ORDER BY created_at DESC, id DESC');
        $st->execute([$cat]);
    } else {
        $st = $pdo->query('SELECT * FROM articles ORDER BY created_at DESC, id DESC');
    }
    ph_json(['success'=>true,'articles'=>array_map($phLearnArticleFromRow,$st->fetchAll())]);
}

if (preg_match('#^/learn/articles/([^/]+)$#i', $path, $m)) {
    if ($method !== 'GET') ph_error('Method not allowed.', 405);
    $id = rawurldecode((string)$m[1]);
    $st = $pdo->prepare('SELECT * FROM articles WHERE id=? LIMIT 1');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) ph_error('Guide not found.', 404);
    ph_json(['success'=>true,'article'=>$phLearnArticleFromRow($row)]);
}

if ($path === '/admin/learn/articles') {
    ph_require_admin($pdo);
    if ($method === 'GET') {
        $st = $pdo->query('SELECT * FROM articles ORDER BY created_at DESC, id DESC');
        ph_json(['success'=>true,'articles'=>array_map($phLearnArticleFromRow,$st->fetchAll())]);
    }
    if ($method === 'POST') {
        $b = ph_body(5 * 1024 * 1024);
        $action = strtolower(trim((string)($b['_action'] ?? 'create')));

        if ($action === 'delete') {
            $id = trim((string)($b['id'] ?? ''));
            if ($id === '') ph_error('Guide id is required.', 422);
            $pdo->prepare('DELETE FROM articles WHERE id=?')->execute([$id]);
            ph_json(['success'=>true]);
        }

        $title = trim((string)($b['title'] ?? ''));
        $excerpt = trim((string)($b['excerpt'] ?? ''));
        $cover = trim((string)($b['cover'] ?? 'art/placeholder.jpg'));
        $author = trim((string)($b['author'] ?? 'ParentingHub Editorial'));
        $date = trim((string)($b['date'] ?? date('d M Y')));
        $readTime = trim((string)($b['readTime'] ?? '5 min'));
        $category = trim((string)($b['category'] ?? 'Guide'));
        $body = $b['body'] ?? [];
        if (is_string($body)) $body = preg_split('/\r?\n\s*\r?\n/', trim($body)) ?: [];
        if (!is_array($body)) $body = [];
        $body = array_values(array_filter(array_map(static fn($v)=>trim((string)$v), $body), static fn($v)=>$v!==''));
        if ($title === '') ph_error('Guide title is required.', 422);
        if ($excerpt === '') $excerpt = mb_substr($body[0] ?? $title, 0, 500);
        if ($cover === '') $cover = 'art/placeholder.jpg';
        if ($author === '') $author = 'ParentingHub Editorial';
        if ($category === '') $category = 'Guide';
        if ($readTime === '') $readTime = '5 min';
        $bodyJson = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($bodyJson === false) ph_error('Guide content could not be encoded.', 422);

        if ($action === 'update') {
            $id = trim((string)($b['id'] ?? ''));
            if ($id === '') ph_error('Guide id is required.', 422);
            $st = $pdo->prepare('SELECT id FROM articles WHERE id=?');
            $st->execute([$id]);
            if (!$st->fetchColumn()) ph_error('Guide not found.', 404);
            $pdo->prepare('UPDATE articles SET title=?,excerpt=?,cover=?,author=?,date=?,read_time=?,category=?,body_json=? WHERE id=?')
                ->execute([$title,$excerpt,$cover,$author,$date,$readTime,$category,$bodyJson,$id]);
            $st = $pdo->prepare('SELECT * FROM articles WHERE id=?');
            $st->execute([$id]);
            ph_json(['success'=>true,'article'=>$phLearnArticleFromRow($st->fetch())]);
        }

        // Create: generate a readable unique id/slug when none is supplied.
        $id = trim((string)($b['id'] ?? ''));
        if ($id === '') {
            $id = strtolower(trim((string)preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));
            if ($id === '') $id = 'guide-' . bin2hex(random_bytes(5));
        }
        $id = strtolower(trim((string)preg_replace('/[^a-zA-Z0-9_-]+/', '-', $id), '-'));
        if ($id === '') $id = 'guide-' . bin2hex(random_bytes(5));
        $baseId = mb_substr($id, 0, 55);
        $id = $baseId;
        $n = 2;
        $chk = $pdo->prepare('SELECT 1 FROM articles WHERE id=?');
        while (true) {
            $chk->execute([$id]);
            if (!$chk->fetchColumn()) break;
            $id = mb_substr($baseId,0,50) . '-' . $n++;
        }
        $pdo->prepare('INSERT INTO articles (id,title,excerpt,cover,author,date,read_time,category,body_json,created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())')
            ->execute([$id,$title,$excerpt,$cover,$author,$date,$readTime,$category,$bodyJson]);
        $st = $pdo->prepare('SELECT * FROM articles WHERE id=?');
        $st->execute([$id]);
        ph_json(['success'=>true,'article'=>$phLearnArticleFromRow($st->fetch())], 201);
    }
    ph_error('Method not allowed.',405);
}

if ($path === '/upload' && $method === 'POST') {
    ph_require_admin($pdo);

    $targetDir = dirname(__DIR__, 2) . '/frontend/assets/uploads';
    if (!is_dir($targetDir) && !@mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
        ph_error('Upload folder could not be created.', 500);
    }

    $saveUpload = static function(string $originalName, string $mime, string $rawOrTmp, bool $isTmp) use ($targetDir): array {
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/quicktime' => 'mov',
        ];
        if (!isset($allowed[$mime])) ph_error('Unsupported file type. Use JPG, PNG, WEBP, GIF, MP4, WebM or MOV.', 422);

        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        if (!$baseName) $baseName = 'upload';
        $safeName = substr($baseName, 0, 70) . '_' . date('Ymd_His') . '_' . substr(bin2hex(random_bytes(5)), 0, 8) . '.' . $allowed[$mime];
        $dest = $targetDir . DIRECTORY_SEPARATOR . $safeName;

        $ok = $isTmp ? move_uploaded_file($rawOrTmp, $dest) : (file_put_contents($dest, $rawOrTmp, LOCK_EX) !== false);
        if (!$ok) ph_error('Could not save uploaded file. Check folder permissions.', 500);
        @chmod($dest, 0644);

        return [
            'url' => '/assets/uploads/' . $safeName,
            'filename' => $safeName,
            'mime' => $mime,
            'size' => (int)filesize($dest),
        ];
    };

    if (isset($_FILES['file'])) {
        $f = $_FILES['file'];
        $err = (int)($f['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($err !== UPLOAD_ERR_OK) {
            $messages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds the hosting upload_max_filesize limit.',
                UPLOAD_ERR_FORM_SIZE => 'File is too large.',
                UPLOAD_ERR_PARTIAL => 'Upload was interrupted. Please retry.',
                UPLOAD_ERR_NO_FILE => 'No file was selected.',
            ];
            ph_error($messages[$err] ?? 'File upload failed.', 422, ['uploadError'=>$err]);
        }
        $size = (int)($f['size'] ?? 0);
        if ($size < 1) ph_error('Uploaded file is empty.', 422);
        if ($size > 80 * 1024 * 1024) ph_error('File exceeds the 80MB application limit.', 422);

        $tmp = (string)($f['tmp_name'] ?? '');
        $mime = '';
        if (class_exists('finfo')) {
            $fi = new finfo(FILEINFO_MIME_TYPE);
            $mime = (string)$fi->file($tmp);
        }
        if ($mime === '') $mime = strtolower((string)($f['type'] ?? ''));
        $saved = $saveUpload((string)($f['name'] ?? 'media'), $mime, $tmp, true);
        ph_json(['success'=>true] + $saved, 201);
    }

    // Backward-compatible JSON/base64 upload used by the existing product editor.
    $b = ph_body(90 * 1024 * 1024);
    $dataUri = (string)($b['data'] ?? '');
    $filename = trim((string)($b['filename'] ?? 'media'));
    if (!preg_match('#^data:(image/(?:jpeg|png|webp|gif)|video/(?:mp4|webm|quicktime));base64,(.+)$#is', $dataUri, $m)) {
        ph_error('Invalid or unsupported media format.', 422);
    }
    $raw = base64_decode($m[2], true);
    if ($raw === false || $raw === '') ph_error('Uploaded media could not be decoded.', 422);
    if (strlen($raw) > 80 * 1024 * 1024) ph_error('File exceeds the 80MB application limit.', 422);
    $saved = $saveUpload($filename, strtolower($m[1]), $raw, false);
    ph_json(['success'=>true] + $saved, 201);
}



// Social sign-in / registration (Google OAuth 2.0 authorization-code flow).
if ($method === 'GET' && $path === '/auth/google/start') {
    $next = ph_safe_next($_GET['next'] ?? '/profile');
    $state = bin2hex(random_bytes(24));
    ph_oauth_cookie('ph_oauth_state', $state);
    ph_oauth_cookie('ph_oauth_provider', 'google');
    ph_oauth_cookie('ph_oauth_next', rawurlencode($next));

    $clientId = ph_env('GOOGLE_CLIENT_ID', '');
    $redirect = ph_env('GOOGLE_REDIRECT_URI', 'https://www.parentinghub.pk/api/auth/google/callback');
    if (!$clientId || str_starts_with($clientId, '__SET_')) ph_redirect('/login?oauth_error=' . rawurlencode('Google sign-in is not configured yet.'));
    $params = [
        'client_id' => $clientId,
        'redirect_uri' => $redirect,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'state' => $state,
        'prompt' => 'select_account',
    ];
    ph_redirect('https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986));
}

if ($method === 'GET' && $path === '/auth/google/callback') {
    $expectedState = (string)($_COOKIE['ph_oauth_state'] ?? '');
    $expectedProvider = (string)($_COOKIE['ph_oauth_provider'] ?? '');
    $next = ph_safe_next(rawurldecode((string)($_COOKIE['ph_oauth_next'] ?? '')), '/profile');
    $state = (string)($_GET['state'] ?? '');
    $code = (string)($_GET['code'] ?? '');
    $providerError = trim((string)($_GET['error_description'] ?? $_GET['error'] ?? ''));

    if ($providerError !== '') {
        ph_clear_oauth_cookies();
        ph_redirect('/login?oauth_error=' . rawurlencode('Google sign-in was cancelled.'));
    }
    if ($expectedState === '' || !hash_equals($expectedState, $state) || $expectedProvider !== 'google' || $code === '') {
        ph_clear_oauth_cookies();
        ph_redirect('/login?oauth_error=' . rawurlencode('Google sign-in session expired. Please try again.'));
    }

    try {
        $clientId = ph_env('GOOGLE_CLIENT_ID', '');
        $clientSecret = ph_env('GOOGLE_CLIENT_SECRET', '');
        $redirect = ph_env('GOOGLE_REDIRECT_URI', 'https://www.parentinghub.pk/api/auth/google/callback');
        if (!$clientId || !$clientSecret || str_starts_with($clientId, '__SET_') || str_starts_with($clientSecret, '__SET_')) throw new RuntimeException('Google sign-in is not configured yet.');
        $token = ph_http_json('https://oauth2.googleapis.com/token', 'POST', ['Content-Type: application/x-www-form-urlencoded'], http_build_query([
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirect,
            'grant_type' => 'authorization_code',
        ], '', '&', PHP_QUERY_RFC3986));
        $accessToken = (string)($token['access_token'] ?? '');
        if ($accessToken === '') throw new RuntimeException('Google did not return an access token.');
        $info = ph_http_json('https://openidconnect.googleapis.com/v1/userinfo', 'GET', ['Authorization: Bearer ' . $accessToken]);
        if (($info['email_verified'] ?? false) !== true) throw new RuntimeException('Google email address is not verified.');
        $user = ph_oauth_login_user($pdo, 'google', (string)($info['sub'] ?? ''), (string)($info['email'] ?? ''), (string)($info['name'] ?? ''), (string)($info['picture'] ?? ''));
        ph_create_session($pdo, (int)$user['id']);
        ph_clear_oauth_cookies();
        ph_redirect($next);
    } catch (Throwable $e) {
        error_log('ParentingHub OAuth Google failed: ' . $e->getMessage());
        ph_clear_oauth_cookies();
        ph_redirect('/login?oauth_error=' . rawurlencode($e->getMessage()));
    }
}

// Google Identity Services (GSI) One Tap / Popup token authentication
if ($method === 'POST' && $path === '/auth/google-token') {
    $b = ph_body();
    $idToken = trim((string)($b['credential'] ?? $b['id_token'] ?? ''));
    if ($idToken === '') ph_error('Google credential token is required.', 400);

    try {
        $info = ph_http_json('https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($idToken), 'GET');
        $expectedClientId = trim((string)ph_env('GOOGLE_CLIENT_ID', ''));
        if ($expectedClientId === '' || str_starts_with($expectedClientId, '__SET_')) {
            throw new RuntimeException('Google sign-in is not configured yet.');
        }
        $aud = (string)($info['aud'] ?? '');
        if (!hash_equals($expectedClientId, $aud)) {
            throw new RuntimeException('Google token audience mismatch.');
        }
        $emailVerified = ($info['email_verified'] ?? '') === 'true' || ($info['email_verified'] ?? false) === true;
        if (!$emailVerified) {
            throw new RuntimeException('Google email address is not verified.');
        }
        $email = ph_email((string)($info['email'] ?? ''));
        $sub = (string)($info['sub'] ?? '');
        $name = trim((string)($info['name'] ?? ''));
        $avatar = (string)($info['picture'] ?? '');

        $user = ph_oauth_login_user($pdo, 'google', $sub, $email, $name, $avatar);
        ph_create_session($pdo, (int)$user['id']);
        ph_json(['success' => true, 'user' => ph_user_public($user), 'isAdmin' => false]);
    } catch (Throwable $e) {
        error_log('ParentingHub Google Token verification failed: ' . $e->getMessage());
        ph_error($e->getMessage(), 401);
    }
}

if ($path === '/auth/register' && $method === 'POST') {
    $b = ph_body();
    $name = trim((string)($b['name'] ?? ''));
    $email = ph_email((string)($b['email'] ?? ''));
    $password = (string)($b['password'] ?? '');
    $phone = trim((string)($b['phone'] ?? ''));
    $city = trim((string)($b['city'] ?? ''));
    if ($name === '' || mb_strlen($name) > 160) ph_error('Please enter a valid full name.', 422);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) ph_error('Please enter a valid email address.', 422);
    if (strlen($password) < 8) ph_error('Password must be at least 8 characters.', 422);
    $st = $pdo->prepare('SELECT id FROM ph_users WHERE email=? LIMIT 1');
    $st->execute([$email]);
    if ($st->fetch()) ph_error('An account with this email already exists.', 409);
    $publicId = 'u-' . bin2hex(random_bytes(8));
    $handle = '@' . preg_replace('/[^a-z0-9]+/', '', strtolower($name));
    $avatar = 'https://api.dicebear.com/9.x/avataaars/svg?seed=' . rawurlencode($name);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $profile = ['children'=>[], 'growth'=>[], 'milestones'=>[], 'vaccines'=>[]];
    $ins = $pdo->prepare("INSERT INTO ph_users (public_id,name,email,password_hash,role,phone,city,avatar,handle,bio,profile_json,email_verified) VALUES (?,?,?,?, 'parent',?,?,?,?,?,?,1)");
    $ins->execute([$publicId,$name,$email,$hash,$phone,$city ?: 'Karachi',$avatar,$handle,'Parent • Pakistan • ParentingHub member',json_encode($profile)]);
    $id = (int)$pdo->lastInsertId();
    ph_create_session($pdo, $id);
    $st = $pdo->prepare('SELECT * FROM ph_users WHERE id=?'); $st->execute([$id]);
    ph_json(['success'=>true,'user'=>ph_user_public($st->fetch())], 201);
}

if (($path === '/auth/login' || $path === '/auth/admin-login') && $method === 'POST') {
    $b = ph_body();
    $email = ph_email((string)($b['email'] ?? ''));
    $password = (string)($b['password'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') ph_error('Invalid email or password.', 401);
    $st = $pdo->prepare('SELECT * FROM ph_users WHERE email=? LIMIT 1');
    $st->execute([$email]);
    $u = $st->fetch();

    $bootstrapEmail = ph_email(ph_env('ADMIN_BOOTSTRAP_EMAIL', 'admin@parentinghub.pk'));
    $bootstrapPass = trim((string)ph_env('ADMIN_BOOTSTRAP_PASSWORD', ''));
    $bootstrapPassEnabled = $bootstrapPass !== '' && !str_starts_with($bootstrapPass, '__SET_') && strlen($bootstrapPass) >= 12;

    // If admin doesn't exist yet, bootstrap it now
    if (!$u && $email === $bootstrapEmail) {
        ph_bootstrap_admin($pdo);
        $st = $pdo->prepare('SELECT * FROM ph_users WHERE email=? LIMIT 1');
        $st->execute([$email]);
        $u = $st->fetch();
    }

    $valid = false;
    if ($u) {
        if (password_verify($password, (string)$u['password_hash'])) {
            $valid = true;
        } else {
            // Admin fallback check for environment bootstrap credentials
            $isAdminUser = ($email === $bootstrapEmail || (string)($u['role'] ?? '') === 'admin');
            $passMatches = $bootstrapPassEnabled && hash_equals($bootstrapPass, $password);
            if ($isAdminUser && $passMatches) {
                $valid = true;
                // Automatically re-hash and update the DB so future logins match directly
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE ph_users SET password_hash=? WHERE id=?')->execute([$newHash, (int)$u['id']]);
                $u['password_hash'] = $newHash;
            }
        }
    }

    if (!$u || !$valid || (int)$u['is_banned'] === 1) ph_error('Invalid email or password.', 401);
    if ($path === '/auth/admin-login' && $u['role'] !== 'admin') ph_error('Administrator access required.', 403);
    ph_create_session($pdo, (int)$u['id']);
    $isAdmin = ($u['role'] === 'admin');
    ph_json([
        'success' => true,
        'user' => ph_user_public($u),
        'isAdmin' => $isAdmin,
        'redirect' => $isAdmin ? '/admin-panel' : '/profile'
    ]);
}

if ($path === '/auth/me' && $method === 'GET') {
    $u = ph_session_user($pdo);
    ph_json(['success'=>true,'authenticated'=>(bool)$u,'user'=>$u ? ph_user_public($u) : null,'isAdmin'=>$u ? $u['role']==='admin' : false]);
}

if ($path === '/auth/logout' && $method === 'POST') {
    ph_destroy_session($pdo);
    ph_json(['success'=>true]);
}

if ($path === '/auth/profile' && $method === 'PUT') {
    $u = ph_require_user($pdo);
    $b = ph_body();
    $allowedProfile = ['children','growth','milestones','vaccines','cover','category','website','followersCount','followingCount','isVerified','highlights','headline','babyName','babyDob','stage','skills'];
    $existing = [];
    if (!empty($u['profile_json'])) $existing = json_decode((string)$u['profile_json'], true) ?: [];
    foreach ($allowedProfile as $k) if (array_key_exists($k,$b)) $existing[$k]=$b[$k];
    if (isset($existing['milestones']) && is_array($existing['milestones'])) {
        $clean = [];
        foreach (array_slice($existing['milestones'], 0, 100) as $m) {
            if (!is_array($m)) continue;
            $title = mb_substr(trim((string)($m['title'] ?? '')), 0, 160);
            if ($title === '') continue;
            $clean[] = [
                'title'=>$title,
                'description'=>mb_substr(trim((string)($m['description'] ?? $m['desc'] ?? '')),0,500),
                'when'=>mb_substr(trim((string)($m['when'] ?? 'Recently')),0,80),
                'createdAt'=>mb_substr(trim((string)($m['createdAt'] ?? gmdate('c'))),0,40),
            ];
        }
        $existing['milestones'] = $clean;
    }
    $name = array_key_exists('name',$b) ? mb_substr(trim((string)$b['name']),0,160) : (string)$u['name'];
    $phone = array_key_exists('phone',$b) ? trim((string)$b['phone']) : (string)$u['phone'];
    $city = array_key_exists('city',$b) ? trim((string)$b['city']) : (string)$u['city'];
    $avatar = array_key_exists('avatar',$b) ? trim((string)$b['avatar']) : (string)$u['avatar'];
    if ($avatar !== '') {
        $isDataImage = (bool)preg_match('#^data:image/(?:jpeg|png|webp);base64,[A-Za-z0-9+/=]+$#', $avatar);
        $isHttpsImage = (bool)preg_match('#^https://#i', $avatar);
        if ((!$isDataImage && !$isHttpsImage) || strlen($avatar) >= 700000) ph_error('Profile image is invalid or too large.', 422);
    }
    $handle = array_key_exists('handle',$b) ? trim((string)$b['handle']) : (string)$u['handle'];
    $bio = array_key_exists('bio',$b) ? trim((string)$b['bio']) : (string)$u['bio'];
    $st=$pdo->prepare('UPDATE ph_users SET name=?,phone=?,city=?,avatar=?,handle=?,bio=?,profile_json=? WHERE id=?');
    $st->execute([$name,$phone,$city,$avatar,$handle,$bio,json_encode($existing,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),(int)$u['id']]);
    $st=$pdo->prepare('SELECT * FROM ph_users WHERE id=?');$st->execute([(int)$u['id']]);
    ph_json(['success'=>true,'user'=>ph_user_public($st->fetch())]);
}

if ($path === '/auth/change-password' && $method === 'POST') {
    $u = ph_require_user($pdo);
    $b = ph_body();
    $old=(string)($b['oldPassword']??''); $new=(string)($b['newPassword']??'');
    if (!password_verify($old,(string)$u['password_hash'])) ph_error('Current password is incorrect.',422);
    if (strlen($new)<8) ph_error('New password must be at least 8 characters.',422);
    $pdo->prepare('UPDATE ph_users SET password_hash=? WHERE id=?')->execute([password_hash($new,PASSWORD_DEFAULT),(int)$u['id']]);
    ph_json(['success'=>true]);
}

if ($path === '/auth/forgot-password' && $method === 'POST') {
    $b = ph_body();
    $email = ph_email((string)($b['email'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) ph_error('Please enter a valid email address.', 422);

    // Always return the same public response so this endpoint cannot be used to enumerate accounts.
    $publicMessage = 'If an account exists for this email, password reset instructions have been sent.';
    $st = $pdo->prepare('SELECT id, name FROM ph_users WHERE email=? LIMIT 1');
    $st->execute([$email]);
    $u = $st->fetch();

    if ($u) {
        // Avoid repeated email/token generation in a short window.
        $recent=$pdo->prepare('SELECT created_at FROM ph_password_resets WHERE email=? AND used=0 ORDER BY id DESC LIMIT 1');
        $recent->execute([$email]);
        $last=$recent->fetchColumn();
        $canSend=!$last || (time()-(strtotime((string)$last)?:0) >= 60);
        if($canSend){
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);
            $pdo->prepare('UPDATE ph_password_resets SET used=1 WHERE email=? AND used=0')->execute([$email]);
            $ins = $pdo->prepare('INSERT INTO ph_password_resets (email, token, expires_at, used) VALUES (?, ?, ?, 0)');
            $ins->execute([$email, $token, $expires]);

            $baseUrl=rtrim((string)ph_env('APP_URL','https://www.parentinghub.pk'),'/');
            $resetUrl=$baseUrl.'/login?token='.rawurlencode($token).'#reset';
            $from=trim((string)ph_env('MAIL_FROM','noreply@parentinghub.pk'));
            if(!filter_var($from,FILTER_VALIDATE_EMAIL))$from='noreply@parentinghub.pk';
            $subject='Reset your ParentingHub password';
            $bodyText="Hello ".trim((string)($u['name']??'Parent')).",\n\nUse the secure link below to reset your ParentingHub password. This link expires in 1 hour:\n\n".$resetUrl."\n\nIf you did not request this reset, you can ignore this email.\n\nParentingHub Pakistan";
            $headers="From: ParentingHub <{$from}>\r\nReply-To: {$from}\r\nContent-Type: text/plain; charset=UTF-8\r\n";
            $sent=@mail($email,$subject,$bodyText,$headers);
            if(!$sent) error_log('ParentingHub password reset email could not be handed to the server mail transport for user '.(int)$u['id']);
        }
    }

    ph_json(['success' => true, 'message' => $publicMessage]);
}

if ($path === '/auth/reset-password' && $method === 'POST') {
    $b = ph_body();
    $token = trim((string)($b['token'] ?? ''));
    $password = (string)($b['newPassword'] ?? $b['password'] ?? '');
    if ($token === '') ph_error('Reset token is required.', 422);
    if (strlen($password) < 8) ph_error('Password must be at least 8 characters.', 422);

    $st = $pdo->prepare('SELECT * FROM ph_password_resets WHERE token=? AND used=0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
    $st->execute([$token]);
    $reset = $st->fetch();
    if (!$reset) ph_error('This password reset link is invalid or has expired.', 422);

    $email = $reset['email'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $upd = $pdo->prepare('UPDATE ph_users SET password_hash=? WHERE email=?');
    $upd->execute([$hash, $email]);

    $pdo->prepare('UPDATE ph_password_resets SET used=1 WHERE token=?')->execute([$token]);
    $pdo->prepare('DELETE FROM ph_sessions WHERE user_id = (SELECT id FROM ph_users WHERE email=? LIMIT 1)')->execute([$email]);

    ph_json(['success' => true, 'message' => 'Your password has been successfully reset. Please sign in with your new password.']);
}


if ($path === '/profile/dashboard' && $method === 'GET') {
    $u = ph_require_user($pdo);
    $uid = (int)$u['id'];
    $ost = $pdo->prepare('SELECT * FROM ph_orders WHERE user_id=? ORDER BY created_at DESC LIMIT 200');
    $ost->execute([$uid]);
    $pst = $pdo->prepare('SELECT * FROM ph_community_posts WHERE user_id=? ORDER BY created_at DESC LIMIT 100');
    $pst->execute([$uid]);
    $wst = $pdo->prepare('SELECT product_id FROM ph_product_wishlist WHERE user_id=? ORDER BY created_at DESC');
    $wst->execute([$uid]);
    $orders = array_map('ph_order_from_row', $ost->fetchAll());
    $posts = array_map(static function(array $r): array {
        return [
            'id' => (string)$r['id'],
            'community' => (string)$r['community'],
            'au' => (string)$r['author_name'],
            'av' => (string)($r['author_avatar'] ?? 'g'),
            'flair' => (string)$r['flair'],
            'title' => (string)$r['title'],
            'body' => (string)$r['body'],
            'votes' => (int)$r['votes'],
            'comments' => (int)$r['comments'],
            'time' => date('M j, Y', strtotime((string)$r['created_at'])),
            'createdAt' => strtotime((string)$r['created_at']) * 1000,
        ];
    }, $pst->fetchAll());
    $wishlist = array_map('strval', $wst->fetchAll(PDO::FETCH_COLUMN));
    ph_json([
        'success'=>true,
        'user'=>ph_user_public($u),
        'orders'=>$orders,
        'posts'=>$posts,
        'wishlist'=>$wishlist,
        'stats'=>['orders'=>count($orders),'wishlist'=>count($wishlist),'questions'=>count($posts)],
    ]);
}

if ($path === '/wishlist') {
    $u = ph_require_user($pdo);
    $uid = (int)$u['id'];
    if ($method === 'GET') {
        $st=$pdo->prepare('SELECT product_id FROM ph_product_wishlist WHERE user_id=? ORDER BY created_at DESC');
        $st->execute([$uid]);
        ph_json(['success'=>true,'productIds'=>array_map('strval',$st->fetchAll(PDO::FETCH_COLUMN))]);
    }
    if ($method === 'POST') {
        $b=ph_body();
        $pid=trim((string)($b['productId']??''));
        if($pid==='') ph_error('productId is required.',422);
        $chk=$pdo->prepare('SELECT 1 FROM products WHERE id=? LIMIT 1');$chk->execute([$pid]);
        if(!$chk->fetchColumn()) ph_error('Product not found.',404);
        $pdo->prepare('INSERT IGNORE INTO ph_product_wishlist (user_id,product_id,created_at) VALUES (?,?,NOW())')->execute([$uid,$pid]);
        ph_json(['success'=>true,'saved'=>true]);
    }
    if ($method === 'PUT') {
        $b=ph_body();$ids=$b['productIds']??[];if(!is_array($ids))ph_error('productIds must be an array.',422);
        $ids=array_values(array_unique(array_filter(array_map(static fn($x)=>trim((string)$x),$ids),static fn($x)=>$x!=='')));
        if(count($ids)>500)ph_error('Too many wishlist items.',422);
        $pdo->beginTransaction();
        try{
            $ins=$pdo->prepare('INSERT IGNORE INTO ph_product_wishlist (user_id,product_id,created_at) SELECT ?,id,NOW() FROM products WHERE id=?');
            foreach($ids as $pid)$ins->execute([$uid,$pid]);
            $pdo->commit();
        }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
        $st=$pdo->prepare('SELECT product_id FROM ph_product_wishlist WHERE user_id=? ORDER BY created_at DESC');$st->execute([$uid]);
        ph_json(['success'=>true,'productIds'=>array_map('strval',$st->fetchAll(PDO::FETCH_COLUMN))]);
    }
    ph_error('Method not allowed.',405);
}
if (preg_match('#^/wishlist/([^/]+)$#i',$path,$m) && $method==='DELETE') {
    $u=ph_require_user($pdo);$pdo->prepare('DELETE FROM ph_product_wishlist WHERE user_id=? AND product_id=?')->execute([(int)$u['id'],$m[1]]);
    ph_json(['success'=>true,'saved'=>false]);
}

if ($path === '/users' && $method === 'GET') {
    ph_require_admin($pdo);
    $rows=$pdo->query("SELECT * FROM ph_users WHERE role='parent' ORDER BY created_at DESC LIMIT 2000")->fetchAll();
    ph_json(['success'=>true,'users'=>array_map('ph_user_public',$rows)]);
}

if ($path === '/orders') {
    if ($method === 'GET') {
        $u = ph_require_user($pdo);
        if ($u['role']==='admin') {
            $st=$pdo->query('SELECT * FROM ph_orders ORDER BY created_at DESC LIMIT 5000');
        } else {
            $st=$pdo->prepare('SELECT * FROM ph_orders WHERE user_id=? ORDER BY created_at DESC LIMIT 500');
            $st->execute([(int)$u['id']]);
        }
        ph_json(['success'=>true,'orders'=>array_map('ph_order_from_row',$st->fetchAll())]);
    }
    if ($method === 'POST') {
        $b=ph_body(); $u=ph_session_user($pdo);
        $id=strtoupper(trim((string)($b['id']??'')));
        if ($id === '' || !preg_match('/^PH-?[0-9A-Z]{4,24}$/', $id)) {
            $id = ph_generate_order_id();
        }

        $requestedItems=$b['items']??[];
        $address = !empty($b['address']) && is_array($b['address'])
            ? $b['address']
            : (!empty($b['customer']) && is_array($b['customer']) ? $b['customer'] : []);

        if (!is_array($requestedItems)||count($requestedItems)<1||count($requestedItems)>50||!is_array($address)) ph_error('Order items and delivery address are required.',422);
        foreach (['name','phone','address','city'] as $field) {
            if (trim((string)($address[$field]??''))==='') ph_error('Please complete the delivery address.',422);
        }

        if (strcasecmp(trim((string)$address['city']), 'Other') === 0) ph_error('Please enter your actual city.', 422);
        $phone = preg_replace('/[^\d+]/', '', (string)$address['phone']);
        if (str_starts_with($phone, '+92')) $phone = '0' . substr($phone, 3);
        elseif (str_starts_with($phone, '0092')) $phone = '0' . substr($phone, 4);
        elseif (str_starts_with($phone, '92') && strlen($phone) === 12) $phone = '0' . substr($phone, 2);
        elseif (preg_match('/^3\d{9}$/', $phone)) $phone = '0' . $phone;
        if (!preg_match('/^03\d{9}$/', $phone) && !preg_match('/^\d{10,12}$/', $phone)) ph_error('Enter a valid Pakistani mobile number (03XXXXXXXXX).', 422);
        if (!empty($address['email']) && !filter_var($address['email'], FILTER_VALIDATE_EMAIL)) ph_error('Enter a valid receipt email address.', 422);

        // Never trust browser-submitted prices, discounts, stock, shipping or totals.
        // Product truth and pricing calculations are verified via the centralized engine.
        $couponCode = trim((string)($b['coupon'] ?? ''));
        try {
            $calculation = ph_calculate_cart_totals($pdo, $requestedItems, $couponCode !== '' ? $couponCode : null);
        } catch (DomainException $e) {
            ph_error($e->getMessage(), 409);
        } catch (InvalidArgumentException $e) {
            ph_error($e->getMessage(), 422);
        }

        $items = $calculation['items'];
        $subtotal = $calculation['subtotal'];
        $shipping = $calculation['shipping'];
        $discount = $calculation['discount'];
        $total = $calculation['total'];
        $appliedCoupon = $calculation['coupon'];

        $catSt=$pdo->prepare("SELECT payload FROM ph_catalog_state WHERE state_key='catalog' LIMIT 1");
        $catSt->execute();$catRow=$catSt->fetch();$catalog=$catRow?json_decode((string)$catRow['payload'],true):[];
        if(!is_array($catalog))$catalog=[];
        $settings=$phLoadSiteSettings($pdo, is_array($catalog['settings']??null)?$catalog['settings']:[]);

        $payRaw=strtolower((string)($b['payment']??$b['paymentMethod']??''));
        if (str_contains($payRaw, 'paypal')) {
            $payment = 'paypal';
            $status = 'Pending Payment';
        } elseif (str_contains($payRaw, 'bank')) {
            $payment = 'bank';
            $bankEnabled = !array_key_exists('bankEnabled', $settings) || !empty($settings['bankEnabled']);
            if (!$bankEnabled) ph_error('Bank transfer is currently unavailable.', 422);
            $status = 'Pending Payment';
        } else {
            $payment = 'cod';
            $codEnabled = !array_key_exists('codEnabled', $settings) || !empty($settings['codEnabled']);
            if (!$codEnabled) ph_error('Cash on Delivery is currently unavailable.', 422);
            $status = 'Confirmed';
        }

        $safeAddress=[
            'name'=>mb_substr(trim((string)$address['name']),0,160),
            'phone'=>mb_substr(trim((string)$address['phone']),0,40),
            'email'=>mb_substr(trim((string)($address['email']??'')),0,190),
            'address'=>mb_substr(trim((string)$address['address']),0,500),
            'city'=>mb_substr(trim((string)$address['city']),0,100),
            'notes'=>mb_substr(trim((string)($address['notes']??'')),0,1000),
        ];

        $st=$pdo->prepare('INSERT INTO ph_orders (id,user_id,status,payment,subtotal,shipping,discount,total,coupon,address_json,items_json,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())');
        try {
            $st->execute([$id,$u?(int)$u['id']:null,$status,$payment,$subtotal,$shipping,$discount,$total,$appliedCoupon,json_encode($safeAddress,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),json_encode($items,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)]);
        } catch (PDOException $e) {
            if ((string)$e->getCode()==='23000') ph_error('Order reference already exists. Please retry.',409);
            throw $e;
        }
        $st=$pdo->prepare('SELECT * FROM ph_orders WHERE id=?');$st->execute([$id]);
        $orderRow=$st->fetch();
        $orderPublic=ph_order_from_row($orderRow);

        // Order confirmation notifications use the server-verified totals only.
        // Notification failure must never invalidate a successfully created order.
        $notifyEmail=ph_email((string)($safeAddress['email']??''));
        $shippingText='PKR '.number_format((float)$shipping,0);
        $totalText='PKR '.number_format((float)$total,0);
        $paymentText=$payment==='bank'?'Bank Transfer':'Cash on Delivery';
        if(filter_var($notifyEmail,FILTER_VALIDATE_EMAIL)){
            $subject='ParentingHub Order '.$id.' Confirmation';
            $bodyText="Hello ".$safeAddress['name'].",

Your ParentingHub order has been received.

Order ID: ".$id."
Subtotal: PKR ".number_format((float)$subtotal,0)."
Delivery: ".$shippingText." (fixed standard fee per order)
Discount: PKR ".number_format((float)$discount,0)."
Total: ".$totalText."
Payment: ".$paymentText."

Delivery address: ".$safeAddress['address'].", ".$safeAddress['city']."

Thank you for shopping with ParentingHub Pakistan.";
            $headers="From: ParentingHub Pakistan <support@parentinghub.pk>
Reply-To: support@parentinghub.pk
Content-Type: text/plain; charset=UTF-8";
            if(!@mail($notifyEmail,$subject,$bodyText,$headers)) error_log('ParentingHub order confirmation email could not be handed to server mail transport for '.$id);
        }
        $smsWebhook=trim((string)ph_env('ORDER_SMS_WEBHOOK_URL',''));
        if($smsWebhook!=='' && filter_var($smsWebhook,FILTER_VALIDATE_URL)){
            $smsPayload=json_encode([
                'event'=>'order.confirmed','orderId'=>$id,'phone'=>$safeAddress['phone'],'customer'=>$safeAddress['name'],
                'shipping'=>$shipping,'total'=>$total,'payment'=>$payment,
                'message'=>'ParentingHub '.$id.': Delivery '.$shippingText.', total '.$totalText.'. Payment: '.$paymentText.'.'
            ],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            try{ph_http_json($smsWebhook,'POST',['Content-Type: application/json'],(string)$smsPayload);}catch(Throwable $e){error_log('ParentingHub order SMS webhook failed for '.$id.': '.$e->getMessage());}
        }
        ph_json(['success'=>true,'order'=>$orderPublic],201);
    }
    ph_error('Method not allowed.',405);
}

if (preg_match('#^/orders/([A-Z0-9-]+)$#i',$path,$m)) {
    $id=strtoupper($m[1]);
    if ($method==='PATCH') {
        ph_require_admin($pdo); $b=ph_body();
        $allowed=['Pending Payment','Confirmed','Packed','Shipped','Out for Delivery','Delivered','Cancelled'];
        if (isset($b['status']) && in_array($b['status'],$allowed,true)) $pdo->prepare('UPDATE ph_orders SET status=? WHERE id=?')->execute([$b['status'],$id]);
        $st=$pdo->prepare('SELECT * FROM ph_orders WHERE id=?');$st->execute([$id]);$row=$st->fetch();
        if(!$row) ph_error('Order not found.',404);
        ph_json(['success'=>true,'order'=>ph_order_from_row($row)]);
    }
    if ($method==='DELETE') {
        ph_require_admin($pdo);$pdo->prepare('DELETE FROM ph_orders WHERE id=?')->execute([$id]);ph_json(['success'=>true]);
    }
}

if ($path === '/inquiries') {
    if ($method==='GET') {
        ph_require_admin($pdo);
        $rows=$pdo->query('SELECT * FROM ph_inquiries ORDER BY created_at DESC LIMIT 5000')->fetchAll();
        ph_json(['success'=>true,'inquiries'=>array_map('ph_inquiry_from_row',$rows)]);
    }
    if ($method==='POST') {
        $b=ph_body();$u=ph_session_user($pdo);
        $name=trim((string)($b['name']??''));$contact=trim((string)($b['contact']??''));$message=trim((string)($b['message']??''));
        if($name===''||$contact===''||$message==='') ph_error('Name, contact and message are required.',422);
        $id='INQ-'.strtoupper(bin2hex(random_bytes(4)));
        $type=(string)($b['type']??'contact'); if(!in_array($type,['contact','return','support','other'],true))$type='other';
        $st=$pdo->prepare('INSERT INTO ph_inquiries (id,user_id,type,name,contact,topic,order_number,message,status) VALUES (?,?,?,?,?,?,?,?,\'new\')');
        $st->execute([$id,$u?(int)$u['id']:null,$type,$name,$contact,$b['topic']??null,$b['orderNumber']??null,$message]);
        $st=$pdo->prepare('SELECT * FROM ph_inquiries WHERE id=?');$st->execute([$id]);
        ph_json(['success'=>true,'inquiry'=>ph_inquiry_from_row($st->fetch())],201);
    }
    ph_error('Method not allowed.',405);
}

if (preg_match('#^/inquiries/([A-Z0-9-]+)$#i',$path,$m)) {
    ph_require_admin($pdo);$id=$m[1];
    if($method==='PATCH'){
        $b=ph_body();$status=(string)($b['status']??'');
        if(!in_array($status,['new','in-progress','resolved'],true)) ph_error('Invalid inquiry status.',422);
        $pdo->prepare('UPDATE ph_inquiries SET status=?,admin_notes=? WHERE id=?')->execute([$status,$b['adminNotes']??null,$id]);
        $st=$pdo->prepare('SELECT * FROM ph_inquiries WHERE id=?');$st->execute([$id]);$row=$st->fetch(); if(!$row)ph_error('Inquiry not found.',404);
        ph_json(['success'=>true,'inquiry'=>ph_inquiry_from_row($row)]);
    }
    if($method==='DELETE'){$pdo->prepare('DELETE FROM ph_inquiries WHERE id=?')->execute([$id]);ph_json(['success'=>true]);}
}

/* =========================================================
   COMMUNITY DATABASE API
   ========================================================= */
$phCommunityPostFromRow = static function(array $r): array {
    return [
        'id' => (string)$r['id'],
        'community' => (string)$r['community'],
        'au' => (string)$r['author_name'],
        'av' => (string)($r['author_avatar'] ?? 'g'),
        'flair' => (string)$r['flair'],
        'title' => (string)$r['title'],
        'body' => (string)$r['body'],
        'votes' => (int)$r['votes'],
        'comments' => (int)$r['comments'],
        'time' => date('M j, Y', strtotime((string)$r['created_at'])),
        'createdAt' => strtotime((string)$r['created_at']) * 1000,
    ];
};
$phCommunityAnswerFromRow = static function(array $r): array {
    return [
        'id' => (string)$r['id'],
        'postId' => (string)$r['post_id'],
        'parentId' => isset($r['parent_answer_id']) && $r['parent_answer_id'] !== null ? (string)$r['parent_answer_id'] : '',
        'au' => (string)$r['author_name'],
        'av' => (string)($r['author_avatar'] ?? 't'),
        'text' => (string)$r['text'],
        'votes' => (int)$r['votes'],
        'best' => (bool)$r['is_best'],
        'verified' => (bool)$r['is_verified'],
        'time' => date('M j, Y', strtotime((string)$r['created_at'])),
        'createdAt' => strtotime((string)$r['created_at']) * 1000,
    ];
};
$phCommunityReelFromRow = static function(array $r): array {
    return [
        'id' => (string)$r['id'],
        'title' => (string)$r['title'],
        'tag' => (string)$r['tag'],
        'by' => (string)$r['author_name'],
        'caption' => (string)$r['caption'],
        'img' => (string)$r['thumbnail_url'],
        'media' => (string)$r['media_url'],
        'views' => (int)$r['views'],
        'likes' => (int)$r['likes'],
        'comments' => (int)($r['comments'] ?? 0),
        'sortOrder' => (int)$r['sort_order'],
        'status' => (string)($r['status'] ?? 'published'),
        'createdAt' => strtotime((string)$r['created_at']) * 1000,
    ];
};

if ($path === '/community/bootstrap' && $method === 'GET') {
    $categoryRows = $pdo->query("SELECT c.community_key AS k,c.label,c.icon AS em,COUNT(p.id) AS posts FROM ph_community_categories c LEFT JOIN ph_community_posts p ON p.community=c.community_key WHERE c.active=1 GROUP BY c.community_key,c.label,c.icon,c.sort_order ORDER BY c.sort_order,c.label")->fetchAll();
    $categories = array_map(static fn($r) => ['k'=>(string)$r['k'],'label'=>(string)$r['label'],'em'=>(string)$r['em'],'posts'=>(int)$r['posts']], $categoryRows);
    $rows = $pdo->query('SELECT p.*,(SELECT COUNT(*) FROM ph_community_answers a WHERE a.post_id=p.id) AS comments FROM ph_community_posts p ORDER BY p.created_at DESC LIMIT 200')->fetchAll();
    $posts = array_map($phCommunityPostFromRow, $rows);
    $reelRows = $pdo->query("SELECT r.*,(SELECT COUNT(*) FROM ph_reel_comments c WHERE c.reel_id=r.id) AS comments FROM ph_community_reels r WHERE r.status='published' ORDER BY r.sort_order ASC,r.created_at DESC LIMIT 100")->fetchAll();
    $reels = array_map($phCommunityReelFromRow, $reelRows);

    $trendRows = $pdo->query('SELECT p.id,p.community,p.title,p.votes,(SELECT COUNT(*) FROM ph_community_answers a WHERE a.post_id=p.id) AS comments FROM ph_community_posts p ORDER BY (p.votes + ((SELECT COUNT(*) FROM ph_community_answers a2 WHERE a2.post_id=p.id) * 2)) DESC, p.created_at DESC LIMIT 5')->fetchAll();
    $trending = array_map(static fn($r) => [
        'p'=>(string)$r['id'],'c'=>(string)$r['community'],'t'=>(string)$r['title'],'v'=>(int)$r['votes']
    ], $trendRows);

    $contribRows = $pdo->query("SELECT author_name,author_avatar,COUNT(*) AS post_count,SUM(votes) AS score FROM ph_community_posts GROUP BY author_name,author_avatar ORDER BY score DESC,post_count DESC LIMIT 5")->fetchAll();
    $contributors = array_map(static fn($r) => [
        'n'=>(string)$r['author_name'],'av'=>(string)($r['author_avatar'] ?: 'g'),'r'=>((int)$r['post_count']).' helpful posts'
    ], $contribRows);

    $topicRows = $pdo->query("SELECT flair,COUNT(*) AS n FROM ph_community_posts GROUP BY flair ORDER BY n DESC,flair LIMIT 12")->fetchAll();
    $topics = array_map(static fn($r) => ['label'=>(string)$r['flair'],'count'=>(int)$r['n']], $topicRows);

    $parents = (int)$pdo->query("SELECT COUNT(*) FROM ph_users WHERE role='parent' AND is_banned=0")->fetchColumn();
    $answersMonth = (int)$pdo->query("SELECT COUNT(*) FROM ph_community_answers WHERE created_at >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')")->fetchColumn();
    $answersTotal = (int)$pdo->query("SELECT COUNT(*) FROM ph_community_answers")->fetchColumn();

    $saved = [];
    $u = ph_session_user($pdo);
    if ($u) {
        $st = $pdo->prepare('SELECT item_type,item_id FROM ph_saved_items WHERE user_id=? ORDER BY created_at DESC');
        $st->execute([(int)$u['id']]);
        foreach ($st->fetchAll() as $s) $saved[] = ['type'=>(string)$s['item_type'],'id'=>(string)$s['item_id']];
    }

    ph_json([
        'success'=>true,
        'categories'=>$categories,
        'posts'=>$posts,
        'reels'=>$reels,
        'trending'=>$trending,
        'contributors'=>$contributors,
        'topics'=>$topics,
        'stats'=>['parents'=>$parents,'answersThisMonth'=>$answersMonth,'answersTotal'=>$answersTotal,'posts'=>count($posts)],
        'saved'=>$saved,
        'authenticated'=>(bool)$u,
    ]);
}

if ($path === '/community/posts') {
    if ($method === 'GET') {
        $st = $pdo->query('SELECT p.*,(SELECT COUNT(*) FROM ph_community_answers a WHERE a.post_id=p.id) AS comments FROM ph_community_posts p ORDER BY p.created_at DESC LIMIT 200');
        ph_json(['success'=>true,'posts'=>array_map($phCommunityPostFromRow,$st->fetchAll())]);
    }
    if ($method === 'POST') {
        $b = ph_body();
        $u = ph_require_user($pdo);
        $title = trim((string)($b['title'] ?? ''));
        $body = trim((string)($b['body'] ?? ''));
        $comm = trim((string)($b['community'] ?? 'feeding'));
        $flair = trim((string)($b['flair'] ?? 'Question'));
        if ($title === '') ph_error('Post title is required.',422);
        if ($body === '') ph_error('Post details are required.',422);
        $cst=$pdo->prepare('SELECT COUNT(*) FROM ph_community_categories WHERE community_key=? AND active=1');$cst->execute([$comm]);
        if((int)$cst->fetchColumn()!==1) ph_error('Choose a valid community category.',422);
        $id='post-'.bin2hex(random_bytes(6));
        $authorName=(string)$u['name'];
        $authorAvatar=trim((string)($u['avatar']??''))!==''?'t':'g';
        $st=$pdo->prepare('INSERT INTO ph_community_posts (id,user_id,community,author_name,author_avatar,flair,title,body,votes,comments,created_at) VALUES (?,?,?,?,?,?,?,?,0,0,NOW())');
        $st->execute([$id,(int)$u['id'],$comm,$authorName,$authorAvatar,$flair,$title,$body]);
        $st=$pdo->prepare('SELECT * FROM ph_community_posts WHERE id=?');$st->execute([$id]);
        ph_json(['success'=>true,'post'=>$phCommunityPostFromRow($st->fetch())],201);
    }
    ph_error('Method not allowed.',405);
}

if (preg_match('#^/community/posts/([^/]+)$#i',$path,$m)) {
    $id=(string)$m[1];
    if($method==='GET'){
        $st=$pdo->prepare('SELECT p.*,(SELECT COUNT(*) FROM ph_community_answers a WHERE a.post_id=p.id) AS comments FROM ph_community_posts p WHERE p.id=? LIMIT 1');$st->execute([$id]);$row=$st->fetch();
        if(!$row)ph_error('Discussion not found.',404);
        ph_json(['success'=>true,'post'=>$phCommunityPostFromRow($row)]);
    }
    if($method==='PATCH'){
        ph_require_admin($pdo);$b=ph_body();
        $st=$pdo->prepare('SELECT * FROM ph_community_posts WHERE id=?');$st->execute([$id]);$row=$st->fetch();if(!$row)ph_error('Discussion not found.',404);
        $pdo->prepare('UPDATE ph_community_posts SET community=?,author_name=?,author_avatar=?,flair=?,title=?,body=? WHERE id=?')->execute([
            trim((string)($b['community']??$row['community'])),trim((string)($b['authorName']??$row['author_name'])),trim((string)($b['authorAvatar']??$row['author_avatar'])),trim((string)($b['flair']??$row['flair'])),trim((string)($b['title']??$row['title'])),trim((string)($b['body']??$row['body'])),$id
        ]);
        $st->execute([$id]);ph_json(['success'=>true,'post'=>$phCommunityPostFromRow($st->fetch())]);
    }
    if($method==='DELETE'){
        ph_require_admin($pdo);
        $pdo->prepare('DELETE FROM ph_community_answers WHERE post_id=?')->execute([$id]);
        $pdo->prepare('DELETE FROM ph_post_votes WHERE post_id=?')->execute([$id]);
        $pdo->prepare("DELETE FROM ph_saved_items WHERE item_type='post' AND item_id=?")->execute([$id]);
        $pdo->prepare('DELETE FROM ph_community_posts WHERE id=?')->execute([$id]);
        ph_json(['success'=>true]);
    }
    ph_error('Method not allowed.',405);
}

if (preg_match('#^/community/posts/([^/]+)/vote$#i',$path,$m) && $method==='POST') {
    $u=ph_require_user($pdo);$postId=(string)$m[1];
    $b=ph_body();$delta=(int)($b['delta']??0);if(!in_array($delta,[-1,1],true))ph_error('Vote must be -1 or 1.',422);
    $chk=$pdo->prepare('SELECT 1 FROM ph_community_posts WHERE id=?');$chk->execute([$postId]);if(!$chk->fetchColumn())ph_error('Discussion not found.',404);
    $pdo->beginTransaction();
    try{
        $st=$pdo->prepare('SELECT vote FROM ph_post_votes WHERE user_id=? AND post_id=? FOR UPDATE');$st->execute([(int)$u['id'],$postId]);$oldVote=$st->fetchColumn();
        if($oldVote===false){
            $pdo->prepare('INSERT INTO ph_post_votes (user_id,post_id,vote,created_at,updated_at) VALUES (?,?,?,NOW(),NOW())')->execute([(int)$u['id'],$postId,$delta]);
        }elseif((int)$oldVote===$delta){
            $pdo->prepare('DELETE FROM ph_post_votes WHERE user_id=? AND post_id=?')->execute([(int)$u['id'],$postId]);
        }else{
            $pdo->prepare('UPDATE ph_post_votes SET vote=?,updated_at=NOW() WHERE user_id=? AND post_id=?')->execute([$delta,(int)$u['id'],$postId]);
        }
        $sum=$pdo->prepare('SELECT COALESCE(SUM(vote),0) FROM ph_post_votes WHERE post_id=?');$sum->execute([$postId]);$votes=(int)$sum->fetchColumn();
        $pdo->prepare('UPDATE ph_community_posts SET votes=? WHERE id=?')->execute([$votes,$postId]);
        $pdo->commit();
        ph_json(['success'=>true,'votes'=>$votes]);
    }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}

if (preg_match('#^/community/posts/([^/]+)/answers$#i',$path,$m)) {
    $postId=(string)$m[1];
    if($method==='GET'){
        $st=$pdo->prepare('SELECT * FROM ph_community_answers WHERE post_id=? ORDER BY is_best DESC,votes DESC,created_at ASC LIMIT 200');$st->execute([$postId]);
        ph_json(['success'=>true,'answers'=>array_map($phCommunityAnswerFromRow,$st->fetchAll())]);
    }
    if($method==='POST'){
        $b=ph_body();$u=ph_require_user($pdo);$txt=trim((string)($b['text']??''));if($txt==='')ph_error('Answer text cannot be empty.',422);if(mb_strlen($txt)>4000)ph_error('Answer is too long.',422);
        $postChk=$pdo->prepare('SELECT 1 FROM ph_community_posts WHERE id=?');$postChk->execute([$postId]);if(!$postChk->fetchColumn())ph_error('Discussion not found.',404);
        $parentId=trim((string)($b['parentId']??''));
        if($parentId!==''){
            $parentChk=$pdo->prepare('SELECT 1 FROM ph_community_answers WHERE id=? AND post_id=?');$parentChk->execute([$parentId,$postId]);if(!$parentChk->fetchColumn())ph_error('Reply target was not found in this discussion.',422);
        }
        $ansId='ans-'.bin2hex(random_bytes(6));$authorName=(string)$u['name'];
        $authorAvatar=trim((string)($u['avatar']??''))!==''?'t':'g';
        $st=$pdo->prepare('INSERT INTO ph_community_answers (id,post_id,parent_answer_id,user_id,author_name,author_avatar,text,votes,is_best,is_verified,created_at) VALUES (?,?,?,?,?,?,?,0,0,0,NOW())');
        $st->execute([$ansId,$postId,$parentId!==''?$parentId:null,(int)$u['id'],$authorName,$authorAvatar,$txt]);
        // Stored counter is kept for backwards compatibility; public counts use actual answer rows.
        $pdo->prepare('UPDATE ph_community_posts SET comments=(SELECT COUNT(*) FROM ph_community_answers WHERE post_id=?) WHERE id=?')->execute([$postId,$postId]);
        $st=$pdo->prepare('SELECT * FROM ph_community_answers WHERE id=?');$st->execute([$ansId]);
        ph_json(['success'=>true,'answer'=>$phCommunityAnswerFromRow($st->fetch())],201);
    }
    ph_error('Method not allowed.',405);
}


if ($path === '/community/reels/upload' && $method === 'POST') {
    $u=ph_require_user($pdo);
    if(!isset($_FILES['file'])) ph_error('Please choose a video file.',422);
    $f=$_FILES['file']; $err=(int)($f['error']??UPLOAD_ERR_NO_FILE);
    if($err!==UPLOAD_ERR_OK) ph_error('Video upload failed. Please retry.',422,['uploadError'=>$err]);
    $size=(int)($f['size']??0);
    if($size<1) ph_error('Uploaded video is empty.',422);
    if($size>30*1024*1024) ph_error('Video exceeds the 30MB upload limit.',422);
    $tmp=(string)($f['tmp_name']??'');
    $mime=''; if(class_exists('finfo')){$fi=new finfo(FILEINFO_MIME_TYPE);$mime=(string)$fi->file($tmp);} if($mime==='')$mime=strtolower((string)($f['type']??''));
    $allowed=['video/mp4'=>'mp4','video/webm'=>'webm','video/quicktime'=>'mov'];
    if(!isset($allowed[$mime])) ph_error('Unsupported reel format. Use MP4, WebM or MOV.',422);
    $title=trim((string)($_POST['title']??$_POST['caption']??''));
    $caption=trim((string)($_POST['caption']??$title));
    $tag=trim((string)($_POST['tag']??'Care')) ?: 'Care';
    if($title==='') ph_error('Reel caption is required.',422);
    $dir=dirname(__DIR__,2).'/frontend/assets/uploads/reels';
    if(!is_dir($dir)&&!@mkdir($dir,0755,true)&&!is_dir($dir)) ph_error('Reel upload folder could not be created.',500);
    $safe='reel_'.date('Ymd_His').'_'.substr(bin2hex(random_bytes(6)),0,10).'.'.$allowed[$mime];
    $dest=$dir.DIRECTORY_SEPARATOR.$safe;
    if(!move_uploaded_file($tmp,$dest)) ph_error('Uploaded reel could not be saved.',500);
    @chmod($dest,0644);
    $url='/assets/uploads/reels/'.$safe;
    $id='reel-'.bin2hex(random_bytes(6));
    $pdo->prepare("INSERT INTO ph_community_reels (id,title,tag,author_name,caption,media_url,thumbnail_url,views,likes,status,sort_order,created_at,updated_at) VALUES (?,?,?,?,?,?,?,0,0,'published',0,NOW(),NOW())")
        ->execute([$id,mb_substr($title,0,255),mb_substr($tag,0,80),(string)$u['name'],mb_substr($caption,0,2000),$url,'']);
    $st=$pdo->prepare("SELECT r.*,(SELECT COUNT(*) FROM ph_reel_comments c WHERE c.reel_id=r.id) AS comments FROM ph_community_reels r WHERE r.id=?");$st->execute([$id]);
    ph_json(['success'=>true,'reel'=>$phCommunityReelFromRow($st->fetch())],201);
}

if (preg_match('#^/community/reels/([^/]+)/like$#i',$path,$m) && $method==='POST') {
    $u=ph_require_user($pdo); $id=(string)$m[1];
    $chk=$pdo->prepare("SELECT 1 FROM ph_community_reels WHERE id=? AND status='published'");$chk->execute([$id]);if(!$chk->fetchColumn())ph_error('Reel not found.',404);
    $pdo->beginTransaction();
    try{
        $st=$pdo->prepare('SELECT id FROM ph_reel_likes WHERE reel_id=? AND user_id=? FOR UPDATE');$st->execute([$id,(int)$u['id']]);$existing=$st->fetchColumn();
        if($existing){$pdo->prepare('DELETE FROM ph_reel_likes WHERE reel_id=? AND user_id=?')->execute([$id,(int)$u['id']]);$liked=false;}
        else{$pdo->prepare('INSERT INTO ph_reel_likes (reel_id,user_id,created_at) VALUES (?,?,NOW())')->execute([$id,(int)$u['id']]);$liked=true;}
        $cnt=$pdo->prepare('SELECT COUNT(*) FROM ph_reel_likes WHERE reel_id=?');$cnt->execute([$id]);$likes=(int)$cnt->fetchColumn();
        $pdo->prepare('UPDATE ph_community_reels SET likes=? WHERE id=?')->execute([$likes,$id]);$pdo->commit();
        ph_json(['success'=>true,'liked'=>$liked,'likes'=>$likes]);
    }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
}

if (preg_match('#^/community/reels/([^/]+)/comments$#i',$path,$m)) {
    $id=(string)$m[1];
    if($method==='GET'){
        $st=$pdo->prepare('SELECT id,reel_id,user_id,author_name,body,created_at FROM ph_reel_comments WHERE reel_id=? ORDER BY created_at ASC LIMIT 200');$st->execute([$id]);
        $rows=array_map(static fn($r)=>['id'=>(int)$r['id'],'reelId'=>(string)$r['reel_id'],'userId'=>(int)$r['user_id'],'author'=>(string)$r['author_name'],'body'=>(string)$r['body'],'createdAt'=>(string)$r['created_at']],$st->fetchAll());
        ph_json(['success'=>true,'comments'=>$rows]);
    }
    if($method==='POST'){
        $u=ph_require_user($pdo);$b=ph_body();$body=trim((string)($b['body']??''));if($body==='')ph_error('Comment cannot be empty.',422);if(mb_strlen($body)>1000)ph_error('Comment is too long.',422);
        $chk=$pdo->prepare("SELECT 1 FROM ph_community_reels WHERE id=? AND status='published'");$chk->execute([$id]);if(!$chk->fetchColumn())ph_error('Reel not found.',404);
        $pdo->prepare('INSERT INTO ph_reel_comments (reel_id,user_id,author_name,body,created_at) VALUES (?,?,?,?,NOW())')->execute([$id,(int)$u['id'],(string)$u['name'],$body]);
        $commentId=(int)$pdo->lastInsertId();
        ph_json(['success'=>true,'comment'=>['id'=>$commentId,'reelId'=>$id,'userId'=>(int)$u['id'],'author'=>(string)$u['name'],'body'=>$body,'createdAt'=>date('Y-m-d H:i:s')]],201);
    }
    ph_error('Method not allowed.',405);
}

if ($path === '/community/reels') {
    if($method==='GET'){
        $st=$pdo->query("SELECT r.*,(SELECT COUNT(*) FROM ph_reel_comments c WHERE c.reel_id=r.id) AS comments FROM ph_community_reels r WHERE r.status='published' ORDER BY r.sort_order,r.created_at DESC LIMIT 100");
        ph_json(['success'=>true,'reels'=>array_map($phCommunityReelFromRow,$st->fetchAll())]);
    }
    ph_error('Method not allowed.',405);
}

if ($path === '/community/saved') {
    $u=ph_require_user($pdo);
    if($method==='GET'){
        $st=$pdo->prepare('SELECT * FROM ph_saved_items WHERE user_id=? ORDER BY created_at DESC');$st->execute([(int)$u['id']]);
        $items=$st->fetchAll();$details=[];
        foreach($items as $item){
            $type=(string)$item['item_type'];$iid=(string)$item['item_id'];$detail=null;
            if($type==='post'){$q=$pdo->prepare('SELECT * FROM ph_community_posts WHERE id=?');$q->execute([$iid]);$r=$q->fetch();if($r)$detail=$phCommunityPostFromRow($r);}
            elseif($type==='reel'){$q=$pdo->prepare('SELECT * FROM ph_community_reels WHERE id=?');$q->execute([$iid]);$r=$q->fetch();if($r)$detail=$phCommunityReelFromRow($r);}
            elseif($type==='answer'){$q=$pdo->prepare('SELECT * FROM ph_community_answers WHERE id=?');$q->execute([$iid]);$r=$q->fetch();if($r)$detail=$phCommunityAnswerFromRow($r);}
            $details[]=['id'=>(int)$item['id'],'type'=>$type,'itemId'=>$iid,'createdAt'=>(string)$item['created_at'],'detail'=>$detail];
        }
        ph_json(['success'=>true,'items'=>$details]);
    }
    if($method==='POST'){
        $b=ph_body();$type=strtolower(trim((string)($b['itemType']??'')));$iid=trim((string)($b['itemId']??''));
        if(!in_array($type,['post','answer','reel'],true)||$iid==='')ph_error('Valid itemType and itemId are required.',422);
        $st=$pdo->prepare('INSERT IGNORE INTO ph_saved_items (user_id,item_type,item_id,created_at) VALUES (?,?,?,NOW())');$st->execute([(int)$u['id'],$type,$iid]);
        ph_json(['success'=>true,'saved'=>true]);
    }
    ph_error('Method not allowed.',405);
}
if (preg_match('#^/community/saved/(post|answer|reel)/([^/]+)$#i',$path,$m) && $method==='DELETE') {
    $u=ph_require_user($pdo);$pdo->prepare('DELETE FROM ph_saved_items WHERE user_id=? AND item_type=? AND item_id=?')->execute([(int)$u['id'],strtolower($m[1]),$m[2]]);
    ph_json(['success'=>true,'saved'=>false]);
}

/* Admin community CRUD */
if ($path === '/admin/community/posts') {
    ph_require_admin($pdo);
    if($method==='POST'){
        $b=ph_body();
        $action=strtolower(trim((string)($b['_action']??'create')));
        if($action==='update'){
            $id=trim((string)($b['id']??''));if($id==='')ph_error('Post id is required.',422);
            $st=$pdo->prepare('SELECT * FROM ph_community_posts WHERE id=?');$st->execute([$id]);$r=$st->fetch();if(!$r)ph_error('Discussion not found.',404);
            $pdo->prepare('UPDATE ph_community_posts SET community=?,author_name=?,author_avatar=?,flair=?,title=?,body=? WHERE id=?')->execute([
                trim((string)($b['community']??$r['community'])),trim((string)($b['authorName']??$r['author_name'])),trim((string)($b['authorAvatar']??$r['author_avatar'])),trim((string)($b['flair']??$r['flair'])),trim((string)($b['title']??$r['title'])),trim((string)($b['body']??$r['body'])),$id
            ]);
            $st->execute([$id]);ph_json(['success'=>true,'post'=>$phCommunityPostFromRow($st->fetch())]);
        }
        if($action==='delete'){
            $id=trim((string)($b['id']??''));if($id==='')ph_error('Post id is required.',422);
            $pdo->prepare("DELETE FROM ph_saved_items WHERE item_type='answer' AND item_id IN (SELECT id FROM ph_community_answers WHERE post_id=?)")->execute([$id]);
            $pdo->prepare('DELETE FROM ph_community_answers WHERE post_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM ph_post_votes WHERE post_id=?')->execute([$id]);
            $pdo->prepare("DELETE FROM ph_saved_items WHERE item_type='post' AND item_id=?")->execute([$id]);
            $pdo->prepare('DELETE FROM ph_community_posts WHERE id=?')->execute([$id]);
            ph_json(['success'=>true]);
        }
    }
    if($method==='GET'){$st=$pdo->query('SELECT p.*,(SELECT COUNT(*) FROM ph_community_answers a WHERE a.post_id=p.id) AS comments FROM ph_community_posts p ORDER BY p.created_at DESC');ph_json(['success'=>true,'posts'=>array_map($phCommunityPostFromRow,$st->fetchAll())]);}
    if($method==='POST'){
        $b=ph_body();$id=trim((string)($b['id']??''));if($id==='')$id='post-'.bin2hex(random_bytes(6));
        $title=trim((string)($b['title']??''));$body=trim((string)($b['body']??''));if($title===''||$body==='')ph_error('Title and body are required.',422);
        $pdo->prepare('INSERT INTO ph_community_posts (id,user_id,community,author_name,author_avatar,flair,title,body,votes,comments,created_at) VALUES (?,NULL,?,?,?,?,?,?,0,0,NOW())')->execute([$id,trim((string)($b['community']??'feeding')),trim((string)($b['authorName']??'ParentingHub')),trim((string)($b['authorAvatar']??'g')),trim((string)($b['flair']??'Question')),$title,$body]);
        $st=$pdo->prepare('SELECT * FROM ph_community_posts WHERE id=?');$st->execute([$id]);ph_json(['success'=>true,'post'=>$phCommunityPostFromRow($st->fetch())],201);
    }
}
if ($path === '/admin/community/answers') {
    ph_require_admin($pdo);
    if($method==='POST'){
        $b=ph_body();$action=strtolower(trim((string)($b['_action']??'')));$id=trim((string)($b['id']??''));
        if($id==='')ph_error('Answer id is required.',422);
        if($action==='update'){
            $st=$pdo->prepare('SELECT * FROM ph_community_answers WHERE id=?');$st->execute([$id]);$r=$st->fetch();if(!$r)ph_error('Answer not found.',404);
            $pdo->prepare('UPDATE ph_community_answers SET author_name=?,text=?,votes=?,is_best=?,is_verified=? WHERE id=?')->execute([trim((string)($b['authorName']??$r['author_name'])),trim((string)($b['text']??$r['text'])),(int)($b['votes']??$r['votes']),(int)!empty($b['best']),(int)!empty($b['verified']),$id]);
            $st->execute([$id]);ph_json(['success'=>true,'answer'=>$phCommunityAnswerFromRow($st->fetch())]);
        }
        if($action==='delete'){
            $st=$pdo->prepare('SELECT post_id FROM ph_community_answers WHERE id=?');$st->execute([$id]);$postId=$st->fetchColumn();
            $pdo->prepare('UPDATE ph_community_answers SET parent_answer_id=NULL WHERE parent_answer_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM ph_community_answers WHERE id=?')->execute([$id]);
            if($postId)$pdo->prepare('UPDATE ph_community_posts SET comments=(SELECT COUNT(*) FROM ph_community_answers WHERE post_id=?) WHERE id=?')->execute([$postId,$postId]);
            $pdo->prepare("DELETE FROM ph_saved_items WHERE item_type='answer' AND item_id=?")->execute([$id]);
            ph_json(['success'=>true]);
        }
        ph_error('Invalid action.',422);
    }
    if($method==='GET'){$st=$pdo->query('SELECT * FROM ph_community_answers ORDER BY created_at DESC LIMIT 1000');ph_json(['success'=>true,'answers'=>array_map($phCommunityAnswerFromRow,$st->fetchAll())]);}
}
if (preg_match('#^/admin/community/answers/([^/]+)$#i',$path,$m)) {
    ph_require_admin($pdo);$id=$m[1];
    if($method==='PATCH'){$b=ph_body();$st=$pdo->prepare('SELECT * FROM ph_community_answers WHERE id=?');$st->execute([$id]);$r=$st->fetch();if(!$r)ph_error('Answer not found.',404);$pdo->prepare('UPDATE ph_community_answers SET author_name=?,text=?,votes=?,is_best=?,is_verified=? WHERE id=?')->execute([trim((string)($b['authorName']??$r['author_name'])),trim((string)($b['text']??$r['text'])),(int)($b['votes']??$r['votes']),(int)!empty($b['best']),(int)!empty($b['verified']),$id]);$st->execute([$id]);ph_json(['success'=>true,'answer'=>$phCommunityAnswerFromRow($st->fetch())]);}
    if($method==='DELETE'){$st=$pdo->prepare('SELECT post_id FROM ph_community_answers WHERE id=?');$st->execute([$id]);$postId=$st->fetchColumn();$pdo->prepare('UPDATE ph_community_answers SET parent_answer_id=NULL WHERE parent_answer_id=?')->execute([$id]);$pdo->prepare('DELETE FROM ph_community_answers WHERE id=?')->execute([$id]);if($postId)$pdo->prepare('UPDATE ph_community_posts SET comments=(SELECT COUNT(*) FROM ph_community_answers WHERE post_id=?) WHERE id=?')->execute([$postId,$postId]);$pdo->prepare("DELETE FROM ph_saved_items WHERE item_type='answer' AND item_id=?")->execute([$id]);ph_json(['success'=>true]);}
}

if ($path === '/admin/community/reels') {
    ph_require_admin($pdo);
    if($method==='GET'){$st=$pdo->query('SELECT * FROM ph_community_reels ORDER BY sort_order,created_at DESC');ph_json(['success'=>true,'reels'=>array_map($phCommunityReelFromRow,$st->fetchAll())]);}
    if($method==='POST'){
        $b=ph_body();$action=strtolower(trim((string)($b['_action']??'create')));
        if($action==='update'){
            $id=trim((string)($b['id']??''));if($id==='')ph_error('Reel id is required.',422);
            $st=$pdo->prepare('SELECT * FROM ph_community_reels WHERE id=?');$st->execute([$id]);$r=$st->fetch();if(!$r)ph_error('Reel not found.',404);
            $status=(string)($b['status']??$r['status']);if(!in_array($status,['published','draft'],true))$status='published';
            $pdo->prepare('UPDATE ph_community_reels SET title=?,tag=?,author_name=?,caption=?,media_url=?,thumbnail_url=?,views=?,likes=?,status=?,sort_order=?,updated_at=NOW() WHERE id=?')->execute([trim((string)($b['title']??$r['title'])),trim((string)($b['tag']??$r['tag'])),trim((string)($b['authorName']??$r['author_name'])),trim((string)($b['caption']??$r['caption'])),trim((string)($b['media']??$r['media_url'])),trim((string)($b['thumbnail']??$r['thumbnail_url'])),(int)($b['views']??$r['views']),(int)($b['likes']??$r['likes']),$status,(int)($b['sortOrder']??$r['sort_order']),$id]);
            $st->execute([$id]);ph_json(['success'=>true,'reel'=>$phCommunityReelFromRow($st->fetch())]);
        }
        if($action==='delete'){
            $id=trim((string)($b['id']??''));if($id==='')ph_error('Reel id is required.',422);
            $pdo->prepare("DELETE FROM ph_saved_items WHERE item_type='reel' AND item_id=?")->execute([$id]);
            $pdo->prepare('DELETE FROM ph_community_reels WHERE id=?')->execute([$id]);
            ph_json(['success'=>true]);
        }
        $id=trim((string)($b['id']??''));if($id==='')$id='reel-'.bin2hex(random_bytes(5));$title=trim((string)($b['title']??''));if($title==='')ph_error('Reel title is required.',422);$pdo->prepare('INSERT INTO ph_community_reels (id,title,tag,author_name,caption,media_url,thumbnail_url,views,likes,status,sort_order,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())')->execute([$id,$title,trim((string)($b['tag']??'Care')),trim((string)($b['authorName']??'@parentinghub.pk')),trim((string)($b['caption']??'')),trim((string)($b['media']??$b['thumbnail']??'')),trim((string)($b['thumbnail']??'')),(int)($b['views']??0),(int)($b['likes']??0),in_array(($b['status']??'published'),['published','draft'],true)?$b['status']:'published',(int)($b['sortOrder']??0)]);$st=$pdo->prepare('SELECT * FROM ph_community_reels WHERE id=?');$st->execute([$id]);ph_json(['success'=>true,'reel'=>$phCommunityReelFromRow($st->fetch())],201);
    }
}
if (preg_match('#^/admin/community/reels/([^/]+)$#i',$path,$m)) {
    ph_require_admin($pdo);$id=$m[1];
    if($method==='PATCH'){$b=ph_body();$st=$pdo->prepare('SELECT * FROM ph_community_reels WHERE id=?');$st->execute([$id]);$r=$st->fetch();if(!$r)ph_error('Reel not found.',404);$status=(string)($b['status']??$r['status']);if(!in_array($status,['published','draft'],true))$status='published';$pdo->prepare('UPDATE ph_community_reels SET title=?,tag=?,author_name=?,caption=?,media_url=?,thumbnail_url=?,views=?,likes=?,status=?,sort_order=?,updated_at=NOW() WHERE id=?')->execute([trim((string)($b['title']??$r['title'])),trim((string)($b['tag']??$r['tag'])),trim((string)($b['authorName']??$r['author_name'])),trim((string)($b['caption']??$r['caption'])),trim((string)($b['media']??$r['media_url'])),trim((string)($b['thumbnail']??$r['thumbnail_url'])),(int)($b['views']??$r['views']),(int)($b['likes']??$r['likes']),$status,(int)($b['sortOrder']??$r['sort_order']),$id]);$st->execute([$id]);ph_json(['success'=>true,'reel'=>$phCommunityReelFromRow($st->fetch())]);}
    if($method==='DELETE'){$pdo->prepare("DELETE FROM ph_saved_items WHERE item_type='reel' AND item_id=?")->execute([$id]);$pdo->prepare('DELETE FROM ph_community_reels WHERE id=?')->execute([$id]);ph_json(['success'=>true]);}
}

if ($path === '/admin/community/saved') {
    ph_require_admin($pdo);
    if($method==='GET'){$rows=$pdo->query('SELECT s.*,u.name,u.email FROM ph_saved_items s LEFT JOIN ph_users u ON u.id=s.user_id ORDER BY s.created_at DESC LIMIT 2000')->fetchAll();$items=array_map(static fn($r)=>['id'=>(int)$r['id'],'userId'=>(int)$r['user_id'],'userName'=>(string)($r['name']??''),'email'=>(string)($r['email']??''),'itemType'=>(string)$r['item_type'],'itemId'=>(string)$r['item_id'],'createdAt'=>(string)$r['created_at']],$rows);ph_json(['success'=>true,'items'=>$items]);}
    if($method==='POST'){
        $b=ph_body();$action=strtolower(trim((string)($b['_action']??'create')));
        if($action==='delete'){
            $id=(int)($b['id']??0);if($id<1)ph_error('Saved record id is required.',422);
            $pdo->prepare('DELETE FROM ph_saved_items WHERE id=?')->execute([$id]);ph_json(['success'=>true]);
        }
        if($action==='update'){
            $id=(int)($b['id']??0);if($id<1)ph_error('Saved record id is required.',422);
            $st=$pdo->prepare('SELECT * FROM ph_saved_items WHERE id=?');$st->execute([$id]);$r=$st->fetch();if(!$r)ph_error('Saved record not found.',404);
            $uid=(int)($b['userId']??$r['user_id']);$type=strtolower(trim((string)($b['itemType']??$r['item_type'])));$iid=trim((string)($b['itemId']??$r['item_id']));
            if($uid<1||!in_array($type,['post','answer','reel'],true)||$iid==='')ph_error('Invalid saved record.',422);
            $pdo->prepare('UPDATE ph_saved_items SET user_id=?,item_type=?,item_id=? WHERE id=?')->execute([$uid,$type,$iid,$id]);ph_json(['success'=>true]);
        }
        $uid=(int)($b['userId']??0);$type=strtolower(trim((string)($b['itemType']??'')));$iid=trim((string)($b['itemId']??''));
        if($uid<1||!in_array($type,['post','answer','reel'],true)||$iid==='')ph_error('userId, itemType and itemId are required.',422);
        $pdo->prepare('INSERT INTO ph_saved_items (user_id,item_type,item_id,created_at) VALUES (?,?,?,NOW())')->execute([$uid,$type,$iid]);ph_json(['success'=>true,'id'=>(int)$pdo->lastInsertId()],201);
    }
}
if (preg_match('#^/admin/community/saved/(\d+)$#',$path,$m)) {
    ph_require_admin($pdo);$id=(int)$m[1];
    if($method==='PATCH'){$b=ph_body();$st=$pdo->prepare('SELECT * FROM ph_saved_items WHERE id=?');$st->execute([$id]);$r=$st->fetch();if(!$r)ph_error('Saved record not found.',404);$uid=(int)($b['userId']??$r['user_id']);$type=strtolower(trim((string)($b['itemType']??$r['item_type'])));$iid=trim((string)($b['itemId']??$r['item_id']));if($uid<1||!in_array($type,['post','answer','reel'],true)||$iid==='')ph_error('Invalid saved record.',422);$pdo->prepare('UPDATE ph_saved_items SET user_id=?,item_type=?,item_id=? WHERE id=?')->execute([$uid,$type,$iid,$id]);ph_json(['success'=>true]);}
    if($method==='DELETE'){$pdo->prepare('DELETE FROM ph_saved_items WHERE id=?')->execute([$id]);ph_json(['success'=>true]);}
}

if ($path === '/newsletter/subscribe' && $method === 'POST') {
    $b = ph_body();
    $email = ph_email((string)($b['email'] ?? ''));
    $stage = trim((string)($b['stage'] ?? 'General'));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) ph_error('Please enter a valid email address.', 422);
    if ($stage === '') $stage = 'General';

    $st = $pdo->prepare('INSERT INTO ph_newsletter_subscribers (email, stage, created_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE stage=VALUES(stage)');
    $st->execute([$email, mb_substr($stage, 0, 80)]);

    ph_json([
        'success' => true,
        'message' => 'Subscribed successfully! Your first guide for ' . htmlspecialchars($stage) . ' arrives this Sunday.'
    ]);
}

ph_error('API endpoint not found.', 404, ['path' => $path]);
