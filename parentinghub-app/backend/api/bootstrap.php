<?php
declare(strict_types=1);

const PH_SESSION_COOKIE = 'ph_session';
const PH_API_VERSION = '2026.10.30';

require_once __DIR__ . '/lib/pricing.php';

function ph_json(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, private');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function ph_error(string $message, int $status = 400, array $extra = []): never {
    ph_json(array_merge(['success' => false, 'message' => $message], $extra), $status);
}

function ph_load_env_file(string $path): void {
    if (!is_file($path) || !is_readable($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$lines) return;
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($key === '') continue;
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        if (getenv($key) === false && !array_key_exists($key, $_ENV)) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

function ph_env(string $key, ?string $default = null): ?string {
    $v = getenv($key);
    if ($v === false || $v === '') $v = $_ENV[$key] ?? null;
    if ($v === null || $v === '') return $default;
    return (string)$v;
}

// Prefer server environment, then private backend configuration.
ph_load_env_file(dirname(__DIR__) . '/.env');
ph_load_env_file(dirname(__DIR__, 2) . '/.env');

function ph_is_https(): bool {
    if (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') return true;
    if (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) return true;
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') return true;
    return false;
}

function ph_allowed_origins(): array {
    $raw = ph_env('CORS_ALLOWED_ORIGINS', 'https://parentinghub.pk,https://www.parentinghub.pk') ?? '';
    $allowed = array_values(array_filter(array_map('trim', explode(',', $raw))));

    // Always trust the exact same origin currently serving the application.
    // This keeps localhost/staging/cPanel preview working without opening CORS broadly.
    $host = trim((string)($_SERVER['HTTP_HOST'] ?? ''));
    if ($host !== '') {
        $scheme = ph_is_https() ? 'https' : 'http';
        $allowed[] = $scheme . '://' . $host;
    }

    $appEnv = ph_env('APP_ENV', 'production');
    $appDebug = ph_env('APP_DEBUG', '0');
    if ($appEnv !== 'production' || $appDebug === '1') {
        $allowed = array_merge($allowed, [
            'http://localhost',
            'http://localhost:5173',
            'http://localhost:3000',
            'http://127.0.0.1',
            'http://127.0.0.1:5173',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:8080',
            'http://localhost:8080',
        ]);
    }
    return array_unique($allowed);
}

function ph_security_headers(): void {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), camera=(), microphone=()');

    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = ph_allowed_origins();
    if ($origin !== '' && in_array($origin, $allowed, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Vary: Origin');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, X-Session-ID');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    }
    if ($method === 'OPTIONS') {
        if ($origin !== '' && !in_array($origin, $allowed, true)) {
            ph_error('Origin not allowed.', 403, ['code' => 'ORIGIN_BLOCKED']);
        }
        http_response_code(204);
        exit;
    }

    // Browser mutation requests must come through our JS client. This blocks
    // cross-site HTML form posts and adds a lightweight CSRF barrier on top of
    // SameSite cookies and the strict origin allow-list.
    if (in_array($method, ['POST','PUT','PATCH','DELETE'], true)) {
        if ($origin !== '' && !in_array($origin, $allowed, true)) {
            ph_error('Origin not allowed.', 403, ['code' => 'ORIGIN_BLOCKED']);
        }
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
            ph_error('Invalid request.', 403, ['code' => 'CSRF_CHECK_FAILED']);
        }
    }
}
ph_security_headers();



function ph_safe_next(?string $next, string $fallback = '/parent'): string {
    $next = is_string($next) ? trim($next) : '';
    if ($next === '' || !str_starts_with($next, '/') || str_starts_with($next, '//')) return $fallback;
    if (strpbrk($next, "\r\n\\") !== false) return $fallback;
    return $next;
}

function ph_redirect(string $url, int $status = 302): never {
    header('Cache-Control: no-store, private');
    header('Location: ' . $url, true, $status);
    exit;
}

function ph_oauth_cookie(string $name, string $value, int $ttlSeconds = 600): void {
    setcookie($name, $value, [
        'expires' => time() + $ttlSeconds,
        'path' => '/api/auth',
        'secure' => ph_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function ph_clear_oauth_cookies(): void {
    foreach (['ph_oauth_state','ph_oauth_provider','ph_oauth_next'] as $name) {
        setcookie($name, '', [
            'expires' => time() - 3600,
            'path' => '/api/auth',
            'secure' => ph_is_https(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

function ph_http_json(string $url, string $method = 'GET', array $headers = [], ?string $body = null): array {
    $method = strtoupper($method);
    $headers[] = 'Accept: application/json';
    $status = 0;
    $raw = false;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
        ]);
        if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $raw = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        if ($raw === false) throw new RuntimeException($err ?: 'OAuth provider request failed.');
    } else {
        $headerText = implode("\r\n", $headers);
        $opts = [
            'http' => [
                'method' => $method,
                'header' => $headerText,
                'ignore_errors' => true,
                'timeout' => 15,
            ],
        ];
        if ($body !== null) $opts['http']['content'] = $body;
        $ctx = stream_context_create($opts);
        $raw = @file_get_contents($url, false, $ctx);
        $statusLine = $http_response_header[0] ?? '';
        if (preg_match('/\s(\d{3})\s/', $statusLine, $m)) $status = (int)$m[1];
        if ($raw === false) throw new RuntimeException('OAuth provider request failed.');
    }

    $data = json_decode((string)$raw, true);
    if (!is_array($data)) throw new RuntimeException('OAuth provider returned an invalid response.');
    if ($status >= 400) {
        $msg = (string)($data['error_description'] ?? $data['error']['message'] ?? 'OAuth provider rejected the request.');
        throw new RuntimeException($msg);
    }
    return $data;
}

function ph_oauth_login_user(PDO $pdo, string $provider, string $providerId, string $email, string $name, string $avatar = ''): array {
    $provider = strtolower(trim($provider));
    $providerId = trim($providerId);
    $email = ph_email($email);
    $name = trim($name);
    if ($provider !== 'google' || $providerId === '') throw new RuntimeException('Invalid social login identity.');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Your social account did not provide a usable email address.');
    if ($name === '') $name = strstr($email, '@', true) ?: 'ParentingHub Parent';

    $st = $pdo->prepare('SELECT * FROM ph_users WHERE email=? LIMIT 1');
    $st->execute([$email]);
    $user = $st->fetch();
    if ($user && (int)$user['is_banned'] === 1) throw new RuntimeException('This account is unavailable.');
    if ($user && (string)($user['role'] ?? '') === 'admin') throw new RuntimeException('Administrator accounts must use the private admin login.');

    $profile = [];
    if ($user && !empty($user['profile_json'])) {
        $tmp = json_decode((string)$user['profile_json'], true);
        if (is_array($tmp)) $profile = $tmp;
    }
    $providers = is_array($profile['oauthProviders'] ?? null) ? $profile['oauthProviders'] : [];
    if (isset($providers[$provider]['id']) && !hash_equals((string)$providers[$provider]['id'], $providerId)) {
        throw new RuntimeException('This email is already linked to a different social account.');
    }
    $providers[$provider] = ['id' => $providerId, 'linkedAt' => gmdate('c')];
    $profile['oauthProviders'] = $providers;

    if ($user) {
        $newAvatar = trim((string)($user['avatar'] ?? '')) !== '' ? (string)$user['avatar'] : $avatar;
        $pdo->prepare('UPDATE ph_users SET email_verified=1, avatar=?, profile_json=? WHERE id=?')
            ->execute([$newAvatar, json_encode($profile, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), (int)$user['id']]);
        $st = $pdo->prepare('SELECT * FROM ph_users WHERE id=?');
        $st->execute([(int)$user['id']]);
        return $st->fetch();
    }

    $publicId = 'u-' . bin2hex(random_bytes(8));
    $baseHandle = preg_replace('/[^a-z0-9]+/', '', strtolower($name));
    if ($baseHandle === '') $baseHandle = 'parent';
    $handle = '@' . $baseHandle . substr(bin2hex(random_bytes(2)), 0, 4);
    if ($avatar === '') $avatar = 'https://api.dicebear.com/9.x/avataaars/svg?seed=' . rawurlencode($name);
    $hash = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
    $profile = array_merge(['children'=>[], 'growth'=>[], 'milestones'=>[], 'vaccines'=>[]], $profile);
    $ins = $pdo->prepare("INSERT INTO ph_users (public_id,name,email,password_hash,role,phone,city,avatar,handle,bio,profile_json,email_verified) VALUES (?,?,?,?, 'parent','','Karachi',?,?,?,?,1)");
    $ins->execute([$publicId,$name,$email,$hash,$avatar,$handle,'Parent • Pakistan • ParentingHub member',json_encode($profile,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)]);
    $id = (int)$pdo->lastInsertId();
    $st = $pdo->prepare('SELECT * FROM ph_users WHERE id=?');
    $st->execute([$id]);
    return $st->fetch();
}

function ph_db_config(): array {
    $host = ph_env('DB_HOST', '127.0.0.1');
    $port = ph_env('DB_PORT', '3306');
    $name = ph_env('DB_NAME');
    $user = ph_env('DB_USER');
    $pass = ph_env('DB_PASS', '');
    if (!$name || !$user) {
        ph_error('Database configuration is missing. Add DB_NAME, DB_USER and DB_PASS to the server .env file.', 503, ['code' => 'DB_CONFIG_MISSING']);
    }
    return compact('host', 'port', 'name', 'user', 'pass');
}

function ph_pdo(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $c = ph_db_config();
    try {
        $pdo = new PDO(
            "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4",
            $c['user'],
            $c['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (Throwable $e) {
        error_log('ParentingHub DB connection failed: ' . $e->getMessage());
        ph_error('Database connection failed.', 503, ['code' => 'DB_CONNECTION_FAILED']);
    }
    ph_ensure_schema($pdo);
    return $pdo;
}

function ph_migrate_platform_integrity_20261030(PDO $pdo): void {
    // Safe, additive one-time migration for account-backed community/profile features.
    // The marker prevents the counter reconciliation from running on every public API request.
    $markerKey = 'migration_20261030_platform_integrity_v2';
    try {
        $marker = (int)$pdo->query("SELECT COUNT(*) FROM ph_catalog_state WHERE state_key='" . $markerKey . "'")->fetchColumn();
        if ($marker > 0) return;
    } catch (Throwable) {
        // Fresh installs reach this function only after ph_catalog_state is created; continue safely if a legacy DB differs.
    }
    $migrationOk = true;
    // CREATE IF NOT EXISTS is intentionally used so existing production data is preserved.
    $sql = [
        "CREATE TABLE IF NOT EXISTS ph_community_categories (
            community_key VARCHAR(40) PRIMARY KEY,
            label VARCHAR(120) NOT NULL,
            icon VARCHAR(32) NOT NULL DEFAULT '♡',
            members_label VARCHAR(40) NOT NULL DEFAULT '',
            active TINYINT(1) NOT NULL DEFAULT 1,
            sort_order INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_ph_ccat_active_sort (active, sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS ph_community_reels (
            id VARCHAR(64) PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            tag VARCHAR(80) NOT NULL DEFAULT 'Care',
            author_name VARCHAR(120) NOT NULL DEFAULT '@parentinghub.pk',
            caption TEXT NULL,
            media_url TEXT NOT NULL,
            thumbnail_url TEXT NULL,
            views INT UNSIGNED NOT NULL DEFAULT 0,
            likes INT UNSIGNED NOT NULL DEFAULT 0,
            status ENUM('published','draft') NOT NULL DEFAULT 'published',
            sort_order INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_ph_reels_status_sort (status, sort_order),
            INDEX idx_ph_reels_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS ph_saved_items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            item_type ENUM('post','answer','reel') NOT NULL,
            item_id VARCHAR(64) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_ph_saved_user_item (user_id,item_type,item_id),
            INDEX idx_ph_saved_user_created (user_id,created_at),
            CONSTRAINT fk_ph_saved_user FOREIGN KEY (user_id) REFERENCES ph_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS ph_product_wishlist (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            product_id VARCHAR(190) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_ph_wishlist_user_product (user_id,product_id),
            INDEX idx_ph_wishlist_user_created (user_id,created_at),
            CONSTRAINT fk_ph_wishlist_user FOREIGN KEY (user_id) REFERENCES ph_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS ph_post_votes (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            post_id VARCHAR(64) NOT NULL,
            vote TINYINT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_ph_vote_user_post (user_id,post_id),
            INDEX idx_ph_vote_post (post_id),
            CONSTRAINT fk_ph_vote_user FOREIGN KEY (user_id) REFERENCES ph_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    foreach ($sql as $stmt) {
        try { $pdo->exec($stmt); }
        catch (Throwable $e) { $migrationOk = false; error_log('ParentingHub integrity migration: ' . $e->getMessage()); }
    }
    // These are labels only; public counts are calculated from real posts/users.
    try {
        $seed = $pdo->prepare("INSERT IGNORE INTO ph_community_categories (community_key,label,icon,members_label,active,sort_order) VALUES (?,?,?,?,1,?)");
        $rows = [
            ['feeding','Feeding & Bottles','🍼','',10],
            ['newborn','Newborn Essentials','👶','',20],
            ['weaning','Cups & Weaning','🥣','',30],
            ['sleep','Sleep & Routine','🌙','',40],
            ['general','All Parents','♡','',50],
        ];
        foreach ($rows as $r) $seed->execute($r);
    } catch (Throwable $e) { $migrationOk = false; error_log('ParentingHub category seed: ' . $e->getMessage()); }
    // Public engagement counters must be derived from actual relational data, not seed/demo values.
    try {
        $pdo->exec("UPDATE ph_community_posts p SET votes=(SELECT COALESCE(SUM(v.vote),0) FROM ph_post_votes v WHERE v.post_id=p.id), comments=(SELECT COUNT(*) FROM ph_community_answers a WHERE a.post_id=p.id)");
    } catch (Throwable $e) { $migrationOk = false; error_log('ParentingHub community counter reconciliation: ' . $e->getMessage()); }

    // Mark only after all additive objects and the counter reconciliation succeeded.
    if ($migrationOk) {
        try {
            $payload = json_encode(['appliedAt'=>gmdate('c'),'version'=>2], JSON_UNESCAPED_SLASHES) ?: '{}';
            $st = $pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES (?,?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),updated_at=NOW()");
            $st->execute([$markerKey, $payload]);
        } catch (Throwable $e) { error_log('ParentingHub integrity migration marker: ' . $e->getMessage()); }
    }
}

/** Restore only known, previously published Learn guides that are missing from the live articles table. */
function ph_migrate_articles_repair_20261030(PDO $pdo): void {
    $markerKey = 'migration_20261030_article_repair_v1';
    try {
        $marker = (int)$pdo->query("SELECT COUNT(*) FROM ph_catalog_state WHERE state_key='" . $markerKey . "'")->fetchColumn();
        if ($marker > 0) return;
    } catch (Throwable) {}

    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS articles (
            id VARCHAR(64) NOT NULL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            excerpt TEXT NOT NULL,
            cover VARCHAR(255) NOT NULL,
            author VARCHAR(150) NOT NULL,
            date VARCHAR(64) NOT NULL,
            read_time VARCHAR(50) NOT NULL,
            category VARCHAR(100) NOT NULL,
            body_json LONGTEXT NOT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_articles_category (category),
            INDEX idx_articles_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $seedFile = __DIR__ . '/seed/article-repair.json';
        $raw = is_file($seedFile) ? file_get_contents($seedFile) : false;
        $rows = $raw !== false ? json_decode($raw, true) : null;
        if (!is_array($rows)) throw new RuntimeException('Article repair seed is unavailable.');

        $insert = $pdo->prepare("INSERT IGNORE INTO articles (id,title,excerpt,cover,author,date,read_time,category,body_json) VALUES (?,?,?,?,?,?,?,?,?)");
        $added = 0;
        foreach ($rows as $r) {
            if (!is_array($r) || empty($r['id']) || empty($r['title'])) continue;
            $insert->execute([
                (string)$r['id'], (string)$r['title'], (string)($r['excerpt'] ?? ''), (string)($r['cover'] ?? ''),
                (string)($r['author'] ?? 'ParentingHub Editorial'), (string)($r['date'] ?? ''), (string)($r['read_time'] ?? ''),
                (string)($r['category'] ?? 'Parenting Tips'), (string)($r['body_json'] ?? '[]')
            ]);
            $added += $insert->rowCount();
        }

        $payload = json_encode(['appliedAt'=>gmdate('c'),'insertedMissingGuides'=>$added], JSON_UNESCAPED_SLASHES) ?: '{}';
        $mark = $pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES (?,?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),updated_at=NOW()");
        $mark->execute([$markerKey, $payload]);
    } catch (Throwable $e) {
        error_log('ParentingHub article repair migration: ' . $e->getMessage());
    }
}


function ph_migrate_release_tasks_20261030(PDO $pdo): void {
    $markerKey = 'migration_20261030_release_tasks_v2';
    try {
        $marker=(int)$pdo->query("SELECT COUNT(*) FROM ph_catalog_state WHERE state_key='".$markerKey."'")->fetchColumn();
        if($marker>0)return;
    } catch(Throwable) {}
    $ok=true;
    $sql=[
        "CREATE TABLE IF NOT EXISTS ph_reel_likes (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            reel_id VARCHAR(64) NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_ph_reel_like (reel_id,user_id),
            INDEX idx_ph_reel_likes_reel (reel_id),
            CONSTRAINT fk_ph_reel_like_user FOREIGN KEY (user_id) REFERENCES ph_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS ph_reel_comments (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            reel_id VARCHAR(64) NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            author_name VARCHAR(160) NOT NULL,
            body TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ph_reel_comments_reel (reel_id,created_at),
            CONSTRAINT fk_ph_reel_comment_user FOREIGN KEY (user_id) REFERENCES ph_users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    foreach($sql as $stmt){try{$pdo->exec($stmt);}catch(Throwable $e){$ok=false;error_log('ParentingHub release migration: '.$e->getMessage());}}
    // Threaded community replies: additive column only; existing answers remain top-level.
    try {
        $col=$pdo->query("SHOW COLUMNS FROM ph_community_answers LIKE 'parent_answer_id'")->fetch();
        if(!$col){
            $pdo->exec("ALTER TABLE ph_community_answers ADD COLUMN parent_answer_id VARCHAR(64) NULL AFTER post_id, ADD INDEX idx_ph_canswers_parent (parent_answer_id)");
        }
    } catch(Throwable $e) { $ok=false; error_log('ParentingHub threaded replies migration: '.$e->getMessage()); }
    try {
        $up=$pdo->prepare("INSERT INTO site_settings (key_name,value_text) VALUES (?,?) ON DUPLICATE KEY UPDATE value_text=VALUES(value_text)");
        $up->execute(['shippingFee','200']);
        $up->execute(['deliveryPolicy','fixed']);
        $pdo->exec("DELETE FROM site_settings WHERE key_name IN ('deliveryRatePerKg','defaultProductWeightKg','freeShippingAbove')");
    } catch(Throwable $e) { /* site_settings may not exist on very old installs; runtime still enforces PKR 200. */ }
    if($ok){try{$payload=json_encode(['appliedAt'=>gmdate('c'),'version'=>2])?:'{}';$st=$pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES (?,?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),updated_at=NOW()");$st->execute([$markerKey,$payload]);}catch(Throwable $e){}}
}


/**
 * Targeted product-image integrity repair.
 * Only repairs records with a known wrong/missing image; never overwrites a valid admin image blindly.
 */
function ph_migrate_product_image_quality_20261030(PDO $pdo): void {
    // v3 adds visually-reviewed card images and removes confirmed wrong SKU/photo pairings.
    $markerKey = 'migration_20261030_product_image_quality_v3';
    try {
        $marker=(int)$pdo->query("SELECT COUNT(*) FROM ph_catalog_state WHERE state_key='".$markerKey."'")->fetchColumn();
        if($marker>0)return;
    } catch(Throwable) {}

    $fixed=[];
    try {
        // Apply only when the live row still uses the exact legacy image we audited.
        // This preserves any image an administrator has already corrected manually.
        $mapFile=__DIR__ . '/seed/product-card-image-map.json';
        $mapRaw=is_file($mapFile) ? file_get_contents($mapFile) : false;
        $mapRows=$mapRaw!==false ? json_decode($mapRaw,true) : null;
        if(is_array($mapRows)){
            $sel=$pdo->prepare('SELECT image FROM products WHERE id=? LIMIT 1');
            $up=$pdo->prepare('UPDATE products SET image=?, images=? WHERE id=? AND image=?');
            foreach($mapRows as $m){
                if(!is_array($m))continue;
                $id=(string)($m['id']??'');
                $old=(string)($m['old_image']??'');
                $new=(string)($m['new_image']??'');
                $imgs=$m['new_images']??[];
                if($id===''||$old===''||$new===''||!is_array($imgs))continue;
                $sel->execute([$id]);
                $current=(string)($sel->fetchColumn()?:'');
                if($current!==$old)continue;
                $up->execute([$new,json_encode(array_values($imgs),JSON_UNESCAPED_SLASHES),$id,$old]);
                if($up->rowCount()>0)$fixed[]=$id . ':' . (string)($m['reason']??'card');
            }
        }

        // Upgrade low-resolution Tommee Tippee official CDN requests without changing SKU mapping.
        $st=$pdo->query("SELECT id,image,images FROM products WHERE image LIKE 'https://tommeetippee.pk/cdn/shop/%' AND (image LIKE '%width=120%' OR image LIKE '%width=360%' OR image LIKE '%width=600%' OR image LIKE '%width=720%')");
        foreach($st->fetchAll() as $row){
            $id=(string)$row['id'];
            $image=(string)($row['image']??'');
            $newImage=preg_replace('/([?&]width=)(120|360|600|720)(?=&|$)/','${1}900',$image) ?: $image;
            $rawImages=json_decode((string)($row['images']??'[]'),true);
            if(!is_array($rawImages))$rawImages=[];
            $newImages=[];
            foreach($rawImages as $u){
                $u=(string)$u;
                $newImages[]=preg_replace('/([?&]width=)(120|360|600|720)(?=&|$)/','${1}900',$u) ?: $u;
            }
            $upRemote=$pdo->prepare('UPDATE products SET image=?, images=? WHERE id=? AND image=?');
            $upRemote->execute([$newImage,json_encode($newImages,JSON_UNESCAPED_SLASHES),$id,$image]);
            if($upRemote->rowCount()>0)$fixed[]=$id . ':official-900w';
        }

        $payload=json_encode(['appliedAt'=>gmdate('c'),'version'=>3,'fixed'=>$fixed],JSON_UNESCAPED_SLASHES)?:'{}';
        $mark=$pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES (?,?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),updated_at=NOW()");
        $mark->execute([$markerKey,$payload]);
    } catch(Throwable $e){
        error_log('ParentingHub product image quality migration: '.$e->getMessage());
    }
}

function ph_migrate_knowledge_foundation_20261105(PDO $pdo): void {
    $markerKey = 'migration_20261105_knowledge_foundation_v1';
    try {
        $marker = (int)$pdo->query("SELECT COUNT(*) FROM ph_catalog_state WHERE state_key='" . $markerKey . "'")->fetchColumn();
        if ($marker > 0) return;
    } catch (Throwable) {}

    $sql = [
        "CREATE TABLE IF NOT EXISTS ph_ai_knowledge_documents (
            id VARCHAR(80) PRIMARY KEY,
            source_type VARCHAR(40) NOT NULL,
            source_id VARCHAR(190) NOT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(190) NOT NULL DEFAULT '',
            category VARCHAR(150) NOT NULL DEFAULT 'General',
            author VARCHAR(150) NOT NULL DEFAULT 'ParentingHub Care Editorial',
            author_credentials VARCHAR(255) NULL,
            is_medically_verified TINYINT(1) NOT NULL DEFAULT 0,
            publication_status VARCHAR(20) NOT NULL DEFAULT 'published',
            content_hash CHAR(64) NOT NULL,
            version INT UNSIGNED NOT NULL DEFAULT 1,
            metadata_json LONGTEXT NULL,
            indexed_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_source (source_type, source_id),
            KEY idx_category (category),
            KEY idx_status (publication_status),
            KEY idx_hash (content_hash)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_ai_knowledge_chunks (
            chunk_id VARCHAR(96) PRIMARY KEY,
            document_id VARCHAR(80) NOT NULL,
            chunk_index INT UNSIGNED NOT NULL,
            heading VARCHAR(255) NULL,
            content TEXT NOT NULL,
            token_count INT UNSIGNED NOT NULL DEFAULT 0,
            content_hash CHAR(64) NOT NULL,
            source_type VARCHAR(40) NOT NULL,
            category VARCHAR(150) NOT NULL DEFAULT 'General',
            metadata_json LONGTEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_doc (document_id),
            KEY idx_source_cat (source_type, category),
            KEY idx_chunk_hash (content_hash)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_ai_knowledge_ingestion_jobs (
            job_id VARCHAR(64) PRIMARY KEY,
            job_type VARCHAR(50) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            total_sources INT UNSIGNED NOT NULL DEFAULT 0,
            processed_sources INT UNSIGNED NOT NULL DEFAULT 0,
            documents_created INT UNSIGNED NOT NULL DEFAULT 0,
            documents_updated INT UNSIGNED NOT NULL DEFAULT 0,
            documents_unchanged INT UNSIGNED NOT NULL DEFAULT 0,
            documents_deindexed INT UNSIGNED NOT NULL DEFAULT 0,
            chunks_created INT UNSIGNED NOT NULL DEFAULT 0,
            retry_count INT UNSIGNED NOT NULL DEFAULT 0,
            errors_json LONGTEXT NULL,
            started_at DATETIME NULL,
            completed_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_job_status (status),
            KEY idx_job_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];

    foreach ($sql as $q) {
        try {
            $pdo->exec($q);
        } catch (Throwable $e) {
            error_log('ph_migrate_knowledge_foundation error: ' . $e->getMessage());
        }
    }

    try {
        $ins = $pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE payload=VALUES(payload), updated_at=NOW()");
        $ins->execute([$markerKey, json_encode(['applied_at' => date('Y-m-d H:i:s'), 'tables' => ['ph_ai_knowledge_documents', 'ph_ai_knowledge_chunks', 'ph_ai_knowledge_ingestion_jobs']])]);
    } catch (Throwable) {}
}

function ph_ensure_schema(PDO $pdo): void {
    static $done = false;
    if ($done) return;
    $done = true;

    $cacheDir = __DIR__ . '/cache';
    if (!is_dir($cacheDir)) @mkdir($cacheDir, 0755, true);
    $cacheFile = $cacheDir . '/.schema_installed';

    if (is_file($cacheFile) && empty($_GET['migrate'])) {
        // Keep the live DB catalog in sync with newly deployed seed products even when
        // the schema-install cache is already present from an older deployment.
        try { ph_migrate_catalog_20261023_full_sync($pdo); } catch (Throwable $e) {
            error_log('ParentingHub catalog full sync failed: ' . $e->getMessage());
        }
        try { ph_migrate_platform_integrity_20261030($pdo); } catch (Throwable $e) {
            error_log('ParentingHub integrity migration failed: ' . $e->getMessage());
        }
        try { ph_migrate_articles_repair_20261030($pdo); } catch (Throwable $e) {
            error_log('ParentingHub article repair migration failed: ' . $e->getMessage());
        }
        try { ph_migrate_release_tasks_20261030($pdo); } catch (Throwable $e) { error_log('ParentingHub release task migration failed: ' . $e->getMessage()); }
        try { ph_migrate_product_image_quality_20261030($pdo); } catch (Throwable $e) { error_log('ParentingHub product image migration failed: ' . $e->getMessage()); }
        try { ph_migrate_knowledge_foundation_20261105($pdo); } catch (Throwable $e) { error_log('ParentingHub knowledge foundation migration failed: ' . $e->getMessage()); }
        if (random_int(1, 50) === 1) {
            try {
                $pdo->exec("DELETE FROM ph_sessions WHERE expires_at < NOW()");
            } catch (Throwable) {}
        }
        return;
    }

    $sql = [
        "CREATE TABLE IF NOT EXISTS ph_users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            public_id VARCHAR(64) NOT NULL UNIQUE,
            name VARCHAR(160) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('parent','admin') NOT NULL DEFAULT 'parent',
            phone VARCHAR(40) DEFAULT '',
            city VARCHAR(100) DEFAULT '',
            avatar TEXT NULL,
            handle VARCHAR(120) DEFAULT '',
            bio TEXT NULL,
            profile_json MEDIUMTEXT NULL,
            email_verified TINYINT(1) NOT NULL DEFAULT 0,
            is_banned TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_ph_users_role (role),
            INDEX idx_ph_users_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_sessions (
            token_hash CHAR(64) PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            last_seen_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_ph_sessions_user FOREIGN KEY (user_id) REFERENCES ph_users(id) ON DELETE CASCADE,
            INDEX idx_ph_sessions_user (user_id),
            INDEX idx_ph_sessions_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_catalog_state (
            state_key VARCHAR(64) PRIMARY KEY,
            payload LONGTEXT NOT NULL,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_orders (
            id VARCHAR(32) PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            status VARCHAR(40) NOT NULL,
            payment VARCHAR(20) NOT NULL,
            subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
            shipping DECIMAL(12,2) NOT NULL DEFAULT 0,
            discount DECIMAL(12,2) NOT NULL DEFAULT 0,
            total DECIMAL(12,2) NOT NULL DEFAULT 0,
            coupon VARCHAR(64) NULL,
            address_json MEDIUMTEXT NOT NULL,
            items_json LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_ph_orders_user FOREIGN KEY (user_id) REFERENCES ph_users(id) ON DELETE SET NULL,
            INDEX idx_ph_orders_user (user_id),
            INDEX idx_ph_orders_status (status),
            INDEX idx_ph_orders_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_inquiries (
            id VARCHAR(40) PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            type VARCHAR(30) NOT NULL,
            name VARCHAR(160) NOT NULL,
            contact VARCHAR(190) NOT NULL,
            topic VARCHAR(160) NULL,
            order_number VARCHAR(40) NULL,
            message TEXT NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'new',
            admin_notes TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_ph_inquiries_user FOREIGN KEY (user_id) REFERENCES ph_users(id) ON DELETE SET NULL,
            INDEX idx_ph_inquiries_status (status),
            INDEX idx_ph_inquiries_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_password_resets (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(190) NOT NULL,
            token VARCHAR(64) NOT NULL UNIQUE,
            expires_at DATETIME NOT NULL,
            used TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ph_resets_token (token),
            INDEX idx_ph_resets_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_community_posts (
            id VARCHAR(64) PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            community VARCHAR(40) NOT NULL,
            author_name VARCHAR(120) NOT NULL,
            author_avatar VARCHAR(40) NOT NULL DEFAULT 'g',
            flair VARCHAR(40) NOT NULL DEFAULT 'Question',
            title VARCHAR(255) NOT NULL,
            body TEXT NOT NULL,
            votes INT NOT NULL DEFAULT 0,
            comments INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ph_cposts_comm (community),
            INDEX idx_ph_cposts_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_community_answers (
            id VARCHAR(64) PRIMARY KEY,
            post_id VARCHAR(64) NOT NULL,
            parent_answer_id VARCHAR(64) NULL,
            user_id BIGINT UNSIGNED NULL,
            author_name VARCHAR(120) NOT NULL,
            author_avatar VARCHAR(40) NOT NULL DEFAULT 't',
            text TEXT NOT NULL,
            votes INT NOT NULL DEFAULT 0,
            is_best TINYINT(1) NOT NULL DEFAULT 0,
            is_verified TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ph_canswers_post (post_id),
            INDEX idx_ph_canswers_parent (parent_answer_id),
            INDEX idx_ph_canswers_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_newsletter_subscribers (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(190) NOT NULL UNIQUE,
            stage VARCHAR(80) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ph_subscribers_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_payments (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id VARCHAR(32) NOT NULL,
            provider VARCHAR(20) NOT NULL DEFAULT 'paypal',
            paypal_order_id VARCHAR(64) NOT NULL UNIQUE,
            capture_id VARCHAR(64) NULL UNIQUE,
            currency VARCHAR(3) NOT NULL DEFAULT 'USD',
            amount_usd DECIMAL(10,2) NOT NULL,
            amount_pkr DECIMAL(12,2) NOT NULL,
            fx_rate DECIMAL(10,4) NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'CREATED',
            payer_email VARCHAR(190) NULL,
            raw_response LONGTEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_ph_payments_order (order_id),
            INDEX idx_ph_payments_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_payment_events (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            event_id VARCHAR(100) NOT NULL UNIQUE,
            event_type VARCHAR(80) NOT NULL,
            order_id VARCHAR(32) NULL,
            verified TINYINT(1) NOT NULL DEFAULT 0,
            payload LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_ai_cart_proposals (
            proposal_id VARCHAR(64) PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            session_token_hash CHAR(64) NULL,
            items_json LONGTEXT NOT NULL,
            subtotal_pkr DECIMAL(12,2) NOT NULL,
            status ENUM('proposed','approved','rejected','expired') NOT NULL DEFAULT 'proposed',
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_cart_proposals_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ph_rate_limits (
            bucket_key VARCHAR(120) PRIMARY KEY,
            hits INT UNSIGNED NOT NULL DEFAULT 1,
            reset_at INT UNSIGNED NOT NULL,
            INDEX idx_rate_reset (reset_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];

    foreach ($sql as $stmt) $pdo->exec($stmt);
    ph_migrate_platform_integrity_20261030($pdo);
    ph_migrate_articles_repair_20261030($pdo);
    ph_migrate_release_tasks_20261030($pdo);
    ph_migrate_product_image_quality_20261030($pdo);
    ph_seed_catalog($pdo);
    ph_migrate_catalog_20260926($pdo);
    ph_migrate_catalog_20261017_kidilo($pdo);
    ph_migrate_catalog_20261017_whatsapp_gear($pdo);
    ph_migrate_catalog_20261023_full_sync($pdo);
    ph_bootstrap_admin($pdo);
    $pdo->exec("DELETE FROM ph_sessions WHERE expires_at < NOW()");
    @file_put_contents($cacheFile, date('c'), LOCK_EX);
}

function ph_seed_catalog(PDO $pdo): void {
    $count = (int)$pdo->query("SELECT COUNT(*) FROM ph_catalog_state WHERE state_key='catalog'")->fetchColumn();
    if ($count > 0) return;
    $seed = __DIR__ . '/seed/catalog.json';
    if (!is_file($seed)) return;
    $payload = file_get_contents($seed);
    if ($payload === false || json_decode($payload, true) === null) return;
    $st = $pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload) VALUES ('catalog', ?)");
    $st->execute([$payload]);
}


/** One-time catalog/content integrity cleanup for the 2026-09-26 production build. */
function ph_migrate_catalog_20260926(PDO $pdo): void {
    $marker = (int)$pdo->query("SELECT COUNT(*) FROM ph_catalog_state WHERE state_key='migration_20260926_catalog_cleanup_v2'")->fetchColumn();
    if ($marker > 0) return;
    $seedFile = __DIR__ . '/seed/catalog.json';
    $seedRaw = is_file($seedFile) ? file_get_contents($seedFile) : false;
    $seed = $seedRaw !== false ? json_decode($seedRaw, true) : null;
    if (!is_array($seed) || !isset($seed['products']) || !is_array($seed['products'])) return;

    $st = $pdo->prepare("SELECT payload FROM ph_catalog_state WHERE state_key='catalog' LIMIT 1");
    $st->execute();
    $row = $st->fetch();
    $current = $row ? json_decode((string)$row['payload'], true) : null;
    if (!is_array($current)) $current = [];

    $seedById = [];
    foreach ($seed['products'] as $p) if (is_array($p) && isset($p['id'])) $seedById[(string)$p['id']] = $p;
    $allowed = ['philips avent' => true, 'tommee tippee' => true, 'kidilo' => true];
    $kept = [];
    foreach (($current['products'] ?? []) as $p) {
        if (!is_array($p)) continue;
        $brand = strtolower(trim((string)($p['brand'] ?? '')));
        if (!isset($allowed[$brand])) continue;
        $id = (string)($p['id'] ?? '');
        if ($id !== '' && isset($seedById[$id])) {
            // Keep live pricing/stock/admin edits, but use the cleaned display name and taxonomy.
            $p['name'] = $seedById[$id]['name'];
            $p['brand'] = $seedById[$id]['brand'];
            $p['category'] = $seedById[$id]['category'];
            if (empty($p['image'])) $p['image'] = $seedById[$id]['image'];
            if (empty($p['images'])) $p['images'] = $seedById[$id]['images'];
        }
        // No production review system exists yet; old numeric ratings were seed/demo values.
        $p['rating'] = 0;
        $p['reviews'] = 0;
        $kept[] = $p;
    }

    $clean = $seed;
    // A healthy current catalog is preserved instead of being overwritten; an old/empty catalog falls back to the clean seed.
    if (count($kept) >= 80) $clean['products'] = $kept;
    if (isset($current['settings']) && is_array($current['settings'])) {
        $clean['settings'] = array_merge($seed['settings'] ?? [], $current['settings']);
        $clean['settings']['siteName'] = 'ParentingHub';
        $clean['settings']['tagline'] = 'Shop • Learn • Connect • Grow';
        $clean['settings']['announcement'] = 'Everything parenting. One trusted hub.';
    }
    if (isset($current['coupons']) && is_array($current['coupons'])) $clean['coupons'] = $current['coupons'];
    if (isset($current['faqs']) && is_array($current['faqs']) && count($current['faqs']) > 0) $clean['faqs'] = $current['faqs'];

    $payload = json_encode($clean, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($payload === false) return;
    $save = $pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES ('catalog',?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),updated_at=NOW()");
    $save->execute([$payload]);
    $markPayload = json_encode(['appliedAt'=>gmdate('c'),'keptLiveProducts'=>count($kept),'seedProducts'=>count($seed['products'])], JSON_UNESCAPED_SLASHES);
    $mark = $pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES ('migration_20260926_catalog_cleanup_v2',?) ON DUPLICATE KEY UPDATE payload=VALUES(payload)");
    $mark->execute([$markPayload ?: '{}']);
}

/** Migration to merge new Kidilo products & categories into the live database catalog state */
function ph_migrate_catalog_20261017_kidilo(PDO $pdo): void {
    $marker = (int)$pdo->query("SELECT COUNT(*) FROM ph_catalog_state WHERE state_key='migration_20261017_kidilo_v1'")->fetchColumn();
    if ($marker > 0) return;
    $seedFile = __DIR__ . '/seed/catalog.json';
    $seedRaw = is_file($seedFile) ? file_get_contents($seedFile) : false;
    $seed = $seedRaw !== false ? json_decode($seedRaw, true) : null;
    if (!is_array($seed) || !isset($seed['products']) || !is_array($seed['products'])) return;

    $st = $pdo->prepare("SELECT payload FROM ph_catalog_state WHERE state_key='catalog' LIMIT 1");
    $st->execute();
    $row = $st->fetch();
    $current = $row ? json_decode((string)$row['payload'], true) : null;
    if (!is_array($current)) $current = $seed;

    $existingIds = [];
    foreach (($current['products'] ?? []) as $p) {
        if (is_array($p) && isset($p['id'])) $existingIds[(string)$p['id']] = true;
    }

    $added = 0;
    foreach ($seed['products'] as $p) {
        if (is_array($p) && isset($p['id']) && !isset($existingIds[(string)$p['id']])) {
            $current['products'][] = $p;
            $added++;
        }
    }

    $current['brands'] = $seed['brands'] ?? ($current['brands'] ?? []);
    $current['categories'] = $seed['categories'] ?? ($current['categories'] ?? []);
    if (isset($seed['homeData'])) {
        $current['homeData'] = $seed['homeData'];
    }

    $payload = json_encode($current, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($payload !== false) {
        $save = $pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES ('catalog',?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),updated_at=NOW()");
        $save->execute([$payload]);
        $mark = $pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES ('migration_20261017_kidilo_v1',?) ON DUPLICATE KEY UPDATE payload=VALUES(payload)");
        $mark->execute([json_encode(['appliedAt' => gmdate('c'), 'addedKidiloProducts' => $added])]);
    }
}

/** Migration to merge WhatsApp Gear & Nursery products (54 products) into the live database catalog state */
function ph_migrate_catalog_20261017_whatsapp_gear(PDO $pdo): void {
    $marker = (int)$pdo->query("SELECT COUNT(*) FROM ph_catalog_state WHERE state_key='migration_20261017_whatsapp_gear_v1'")->fetchColumn();
    if ($marker > 0) return;
    $seedFile = __DIR__ . '/seed/catalog.json';
    $seedRaw = is_file($seedFile) ? file_get_contents($seedFile) : false;
    $seed = $seedRaw !== false ? json_decode($seedRaw, true) : null;
    if (!is_array($seed) || !isset($seed['products']) || !is_array($seed['products'])) return;

    $st = $pdo->prepare("SELECT payload FROM ph_catalog_state WHERE state_key='catalog' LIMIT 1");
    $st->execute();
    $row = $st->fetch();
    $current = $row ? json_decode((string)$row['payload'], true) : null;
    if (!is_array($current)) $current = $seed;

    $existingIds = [];
    foreach (($current['products'] ?? []) as $p) {
        if (is_array($p) && isset($p['id'])) $existingIds[(string)$p['id']] = true;
    }

    $added = 0;
    foreach ($seed['products'] as $p) {
        if (is_array($p) && isset($p['id']) && !isset($existingIds[(string)$p['id']])) {
            $current['products'][] = $p;
            $added++;
        }
    }

    $current['brands'] = $seed['brands'] ?? ($current['brands'] ?? []);
    $current['categories'] = $seed['categories'] ?? ($current['categories'] ?? []);

    $payload = json_encode($current, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($payload !== false) {
        $save = $pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES ('catalog',?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),updated_at=NOW()");
        $save->execute([$payload]);
        $mark = $pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES ('migration_20261017_whatsapp_gear_v1',?) ON DUPLICATE KEY UPDATE payload=VALUES(payload)");
        $mark->execute([json_encode(['appliedAt' => gmdate('c'), 'addedGearProducts' => $added])]);
    }
}


/**
 * One-time full catalog reconciliation for the 2026-10-23 build.
 * Adds any products present in api/seed/catalog.json but missing from the live DB,
 * while preserving existing live price, stock, descriptions and admin edits.
 */
function ph_migrate_catalog_20261023_full_sync(PDO $pdo): void {
    $markerKey = 'migration_20261023_catalog_full_sync_v1';
    $marker = (int)$pdo->query("SELECT COUNT(*) FROM ph_catalog_state WHERE state_key='" . $markerKey . "'")->fetchColumn();
    if ($marker > 0) return;

    $seedFile = __DIR__ . '/seed/catalog.json';
    $seedRaw = is_file($seedFile) ? file_get_contents($seedFile) : false;
    $seed = $seedRaw !== false ? json_decode($seedRaw, true) : null;
    if (!is_array($seed) || !isset($seed['products']) || !is_array($seed['products'])) return;

    $st = $pdo->prepare("SELECT payload FROM ph_catalog_state WHERE state_key='catalog' LIMIT 1");
    $st->execute();
    $row = $st->fetch();
    $current = $row ? json_decode((string)$row['payload'], true) : null;
    if (!is_array($current)) $current = $seed;
    if (!isset($current['products']) || !is_array($current['products'])) $current['products'] = [];

    $existingIds = [];
    foreach ($current['products'] as $p) {
        if (is_array($p) && isset($p['id'])) $existingIds[(string)$p['id']] = true;
    }

    $added = 0;
    foreach ($seed['products'] as $p) {
        if (!is_array($p) || !isset($p['id'])) continue;
        $id = (string)$p['id'];
        if ($id !== '' && !isset($existingIds[$id])) {
            $current['products'][] = $p;
            $existingIds[$id] = true;
            $added++;
        }
    }

    // Taxonomy should reflect the deployed catalog; live product edits remain untouched.
    if (isset($seed['brands']) && is_array($seed['brands'])) $current['brands'] = $seed['brands'];
    if (isset($seed['categories']) && is_array($seed['categories'])) $current['categories'] = $seed['categories'];

    $payload = json_encode($current, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($payload === false) return;
    $save = $pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES ('catalog',?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),updated_at=NOW()");
    $save->execute([$payload]);

    $markPayload = json_encode([
        'appliedAt' => gmdate('c'),
        'seedProducts' => count($seed['products']),
        'liveProductsAfterSync' => count($current['products']),
        'addedProducts' => $added
    ], JSON_UNESCAPED_SLASHES);
    $mark = $pdo->prepare("INSERT INTO ph_catalog_state (state_key,payload) VALUES (?,?) ON DUPLICATE KEY UPDATE payload=VALUES(payload)");
    $mark->execute([$markerKey, $markPayload ?: '{}']);
}

function ph_bootstrap_admin(PDO $pdo): void {
    $email = strtolower(trim((string)ph_env('ADMIN_BOOTSTRAP_EMAIL', 'admin@parentinghub.pk')));
    $password = trim((string)ph_env('ADMIN_BOOTSTRAP_PASSWORD', ''));
    if ($email === '' || $password === '' || str_starts_with($password, '__SET_')) return;
    if (strlen($password) < 12) return;

    $st = $pdo->prepare('SELECT id, role FROM ph_users WHERE email = ? LIMIT 1');
    $st->execute([$email]);
    $existing = $st->fetch();

    // Never overwrite an existing account automatically. The environment password
    // is only for bootstrapping a missing administrator account.
    if ($existing) return;

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $publicId = 'adm-' . bin2hex(random_bytes(6));
    $name = ph_env('ADMIN_BOOTSTRAP_NAME', 'ParentingHub Administrator');
    $ins = $pdo->prepare("INSERT INTO ph_users (public_id,name,email,password_hash,role,email_verified,handle,bio) VALUES (?,?,?,?, 'admin',1,'@parentinghub.admin','ParentingHub Administrator')");
    $ins->execute([$publicId, $name, $email, $hash]);
}

function ph_body(int $maxBytes = 2097152): array {
    $len = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($len > $maxBytes) ph_error('Request body is too large.', 413);
    $raw = file_get_contents('php://input') ?: '';
    if ($raw === '') return [];
    $data = json_decode($raw, true);
    if (!is_array($data)) ph_error('Invalid JSON request body.', 400);
    return $data;
}

function ph_fixed_shipping_fee(float $subtotal): float {
    return $subtotal > 0 ? 200.0 : 0.0;
}

function ph_email(string $email): string {
    return strtolower(trim($email));
}

function ph_user_public(array $row): array {
    $profile = [];
    if (!empty($row['profile_json'])) {
        $tmp = json_decode((string)$row['profile_json'], true);
        if (is_array($tmp)) $profile = $tmp;
    }
    return array_merge($profile, [
        'id' => (string)$row['public_id'],
        'name' => (string)$row['name'],
        'email' => (string)$row['email'],
        'avatar' => (string)($row['avatar'] ?: ($profile['avatar'] ?? '')),
        'handle' => (string)($row['handle'] ?: ($profile['handle'] ?? '')),
        'bio' => (string)($row['bio'] ?: ($profile['bio'] ?? '')),
        'phone' => (string)($row['phone'] ?? ''),
        'city' => (string)($row['city'] ?? ''),
        'isBanned' => (bool)$row['is_banned'],
        'registeredAt' => (string)$row['created_at'],
        'role' => (string)$row['role'],
        'emailVerified' => (bool)$row['email_verified'],
    ]);
}

function ph_session_user(PDO $pdo): ?array {
    $token = $_COOKIE[PH_SESSION_COOKIE] ?? '';
    if (!is_string($token) || strlen($token) < 32) return null;
    $hash = hash('sha256', $token);
    $st = $pdo->prepare("SELECT u.* FROM ph_sessions s JOIN ph_users u ON u.id=s.user_id WHERE s.token_hash=? AND s.expires_at>NOW() LIMIT 1");
    $st->execute([$hash]);
    $user = $st->fetch();
    if (!$user) return null;
    if ((int)$user['is_banned'] === 1) return null;
    $pdo->prepare('UPDATE ph_sessions SET last_seen_at=NOW() WHERE token_hash=?')->execute([$hash]);
    return $user;
}

function ph_create_session(PDO $pdo, int $userId): void {
    $token = bin2hex(random_bytes(32));
    $hash = hash('sha256', $token);
    $days = max(1, min(90, (int)(ph_env('SESSION_DAYS', '30') ?? '30')));
    $expires = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->modify("+{$days} days");
    $st = $pdo->prepare('INSERT INTO ph_sessions (token_hash,user_id,expires_at) VALUES (?,?,?)');
    $st->execute([$hash, $userId, $expires->format('Y-m-d H:i:s')]);
    setcookie(PH_SESSION_COOKIE, $token, [
        'expires' => $expires->getTimestamp(),
        'path' => '/',
        'secure' => ph_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function ph_destroy_session(PDO $pdo): void {
    $token = $_COOKIE[PH_SESSION_COOKIE] ?? '';
    if (is_string($token) && $token !== '') {
        $pdo->prepare('DELETE FROM ph_sessions WHERE token_hash=?')->execute([hash('sha256', $token)]);
    }
    setcookie(PH_SESSION_COOKIE, '', ['expires' => time() - 3600, 'path' => '/', 'secure' => ph_is_https(), 'httponly' => true, 'samesite' => 'Lax']);
}

function ph_require_user(PDO $pdo): array {
    $u = ph_session_user($pdo);
    if (!$u) ph_error('Authentication required.', 401, ['code' => 'AUTH_REQUIRED']);
    return $u;
}

function ph_require_admin(PDO $pdo): array {
    $u = ph_require_user($pdo);
    if (($u['role'] ?? '') !== 'admin') ph_error('Administrator access required.', 403, ['code' => 'ADMIN_REQUIRED']);
    return $u;
}

function ph_order_from_row(array $r): array {
    $addr = json_decode((string)$r['address_json'], true) ?: [];
    $createdIso = str_replace(' ', 'T', (string)$r['created_at']) . 'Z';
    return [
        'id' => (string)$r['id'],
        'date' => $createdIso,
        'createdAt' => $createdIso,
        'items' => json_decode((string)$r['items_json'], true) ?: [],
        'subtotal' => (float)$r['subtotal'],
        'shipping' => (float)$r['shipping'],
        'discount' => (float)$r['discount'],
        'total' => (float)$r['total'],
        'coupon' => $r['coupon'] !== null ? (string)$r['coupon'] : null,
        'address' => $addr,
        'customer' => $addr,
        'customerName' => (string)($addr['name'] ?? 'Customer'),
        'customerPhone' => (string)($addr['phone'] ?? ''),
        'city' => (string)($addr['city'] ?? 'Pakistan'),
        'payment' => (string)$r['payment'],
        'paymentMethod' => (string)$r['payment'] === 'paypal' ? 'PayPal' : ((string)$r['payment'] === 'bank' ? 'Bank Transfer' : 'Cash on Delivery'),
        'status' => (string)$r['status'],
    ];
}

function ph_inquiry_from_row(array $r): array {
    return [
        'id' => (string)$r['id'],
        'type' => (string)$r['type'],
        'name' => (string)$r['name'],
        'contact' => (string)$r['contact'],
        'topic' => $r['topic'] !== null ? (string)$r['topic'] : null,
        'orderNumber' => $r['order_number'] !== null ? (string)$r['order_number'] : null,
        'message' => (string)$r['message'],
        'createdAt' => str_replace(' ', 'T', (string)$r['created_at']) . 'Z',
        'status' => (string)$r['status'],
        'adminNotes' => $r['admin_notes'] !== null ? (string)$r['admin_notes'] : null,
    ];
}
