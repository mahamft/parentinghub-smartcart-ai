<?php
declare(strict_types=1);
if (!defined('PH_ENTRY')) { http_response_code(404); exit; }
$root = dirname(__DIR__);
$rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rawurldecode($rawPath);
function ph_route_error(int $status): never {
    http_response_code($status);
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store');
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>' . $status . '</title><h1>' . $status . '</h1><p>Page unavailable.</p><a href="/">Return home</a></html>';
    exit;
}
function ph_route_redirect(string $target): never {
    header('Cache-Control: no-cache, max-age=0, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Location: ' . $target, true, 301); exit;
}
function ph_route_temp_redirect(string $target): never {
    header('Cache-Control: no-store, private');
    header('Location: ' . $target, true, 302); exit;
}
if (preg_match('~[\x00-\x1f\x7f\\\\]~', $path) || in_array('..', explode('/', $path), true)) ph_route_error(400);
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
// Only the original production hostname is canonicalized. Local/staging hosts stay local.
$host = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? '')[0]);
if (in_array($host, ['parentinghub.pk', 'www.parentinghub.pk'], true)) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    if ($host !== 'www.parentinghub.pk' || !$https) ph_route_redirect('https://www.parentinghub.pk' . ($_SERVER['REQUEST_URI'] ?? '/'));
}
// Private implementation folders can never be fetched over HTTP.
if (preg_match('~^/(?:backend|frontend|tools|database)(?:/|$)|(?:^|/)\.(?!well-known(?:/|$))|\.sql$~i', $path)) ph_route_error(404);
// Canonical public URLs: legacy homepage and directory-index variants must never be crawlable duplicates.
if ($path === '/index.html' || $path === '/index.php') ph_route_redirect('/');
if ($path === '/parent' || $path === '/parent/') ph_route_redirect('/profile');
if (preg_match('~^/(.+)/index\.html$~', $path, $m)) ph_route_redirect('/' . trim($m[1], '/'));
$legacyTopLevel = [
    '/shop.html'=>'/shop', '/articles.html'=>'/articles', '/learn.html'=>'/articles',
    '/community.html'=>'/community', '/about.html'=>'/about', '/support.html'=>'/support',
    '/privacy.html'=>'/privacy', '/returns.html'=>'/returns', '/terms.html'=>'/terms',
    '/profile.html'=>'/profile', '/edit-profile.html'=>'/edit-profile', '/saved.html'=>'/saved',
    '/cart.html'=>'/cart', '/checkout.html'=>'/checkout', '/track.html'=>'/track', '/login.html'=>'/login'
];
if (isset($legacyTopLevel[$path])) ph_route_redirect($legacyTopLevel[$path]);

// Dynamic XML sitemap generated from the live database.
if ($path === '/sitemap.xml') {
    require $root . '/backend/api/sitemap.php';
    exit;
}

// Dynamic database-backed frontend data bundle. Never serve the legacy static data.js catalog.
if ($path === '/js/data.js') {
    require $root . '/backend/api/frontend-data.php';
    exit;
}

if (str_starts_with($path, '/api/') || $path === '/api') {
    $endpoint = substr($path, 4);
    if (in_array($endpoint, ['/source-image.php', '/db-status.php', '/milestone.php'], true)) {
        require $root . '/backend/api' . $endpoint; exit;
    }
    // Internal API PHP files, JSON data, seed and caches are never public.
    if (preg_match('~\.(?:php|json|log)$|^/(?:cache|seed)(?:/|$)~i', $endpoint)) ph_route_error(404);
    require $root . '/backend/api/index.php'; exit;
}
// Serve only known static media types. Uploaded scripts cannot be executed.
$publicDir = realpath($root . '/frontend');
$types = ['css'=>'text/css','js'=>'application/javascript','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp','gif'=>'image/gif','svg'=>'image/svg+xml','ico'=>'image/x-icon','woff'=>'font/woff','woff2'=>'font/woff2','webmanifest'=>'application/manifest+json','xml'=>'application/xml','txt'=>'text/plain','mp4'=>'video/mp4','webm'=>'video/webm','mov'=>'video/quicktime'];
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
if (isset($types[$ext])) {
    $file = realpath(($publicDir ?: ($root . '/frontend')) . $path);
    $normalizedFile = $file ? str_replace('\\', '/', $file) : '';
    $normalizedPublic = $publicDir ? str_replace('\\', '/', $publicDir) : '';
    if (!$file || !$publicDir || !str_starts_with($normalizedFile, $normalizedPublic . '/') || !is_file($file)) ph_route_error(404);
    header('Content-Type: ' . $types[$ext]);
    if ($path === '/robots.txt') header('Cache-Control: public, max-age=3600');
    else header('Cache-Control: public, max-age=2592000, immutable');
    header('Content-Length: ' . filesize($file));
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'HEAD') readfile($file);
    exit;
}
if (preg_match('~^/\.well-known/acme-challenge/([A-Za-z0-9_-]+)$~', $path, $m)) {
    $file = $root . '/.well-known/acme-challenge/' . $m[1];
    if (!is_file($file)) ph_route_error(404);
    header('Content-Type: text/plain'); readfile($file); exit;
}
$key = rtrim($path, '/') ?: '/';
if (in_array($key, ['/', '/index.html', '/shop', '/shop.html'], true) && isset($_GET['product']) && is_string($_GET['product']) && preg_match('/^[A-Za-z0-9_-]+$/', $_GET['product'])) ph_route_redirect('/product/' . $_GET['product']);
if ($key === '/product') ph_route_redirect('/shop');
$rules = json_decode(file_get_contents(__DIR__ . '/config/legacy-routes.json'), true, 512, JSON_THROW_ON_ERROR);
foreach ($rules as $rule) {
    if (preg_match('~' . $rule['pattern'] . '~', ltrim($path, '/'))) {
        if ($rule['status'] === 410) ph_route_error(410);
        ph_route_redirect($rule['target']);
    }
}
$routes = json_decode(file_get_contents(__DIR__ . '/config/routes.json'), true, 512, JSON_THROW_ON_ERROR);
if (preg_match('~^/(?:admin|admin-panel)(?:/.*)?$~', $key) || $key === '/admin.html') {
    $template = 'admin-panel/index.php';
} else {
    // Public .php aliases are legacy-only. Canonical public routes use extensionless URLs.
    if (str_ends_with($key, '.php')) ph_route_redirect(substr($key, 0, -4) ?: '/');
    // All Learn guide detail URLs are served from the database-backed template.
    if (preg_match('~^/product/([A-Za-z0-9_-]+)(?:/index\.html)?$~', $key, $productMatch)) {
        $productId = $productMatch[1];
        $template = 'frontend/pages/product.php';
    } elseif (preg_match('~^/articles/[^/]+(?:/index\.html)?$~', $key)) {
        $template = 'frontend/pages/articles.php';
    } elseif (preg_match('~^/shop/categories/([A-Za-z0-9_-]+)(?:/index\.html)?$~', $key, $shopCatMatch)) {
        $_GET['category_slug'] = $shopCatMatch[1];
        $template = 'frontend/pages/shop.php';
    } elseif (preg_match('~^/shop/brands/([A-Za-z0-9_-]+)(?:/index\.html)?$~', $key, $shopBrandMatch)) {
        $_GET['brand_slug'] = $shopBrandMatch[1];
        $template = 'frontend/pages/shop.php';
    } else {
        $template = $routes[$key] ?? null;
    }
}
if (!$template) {
    if (str_starts_with($key, '/product/')) ph_route_error(404);
    if (preg_match('~^/shop/(categories|brands)/[^/]+$~', $key)) ph_route_redirect('/shop');
    // Unknown URLs must be genuine 404s. Never render the homepage for an unknown path.
    ph_route_error(404);
}

// Private parent pages require a valid server-side session. Client localStorage alone is never trusted.
if (in_array($key, ['/profile','/edit-profile','/profile/manage','/saved','/profile/saved'], true)) {
    require_once $root . '/backend/api/bootstrap.php';
    try {
        $pdo = ph_pdo();
        $routeUser = ph_session_user($pdo);
        if (!$routeUser) ph_route_temp_redirect('/login?next=' . rawurlencode($key));
    } catch (Throwable $e) {
        error_log('Private route auth check failed: ' . $e->getMessage());
        ph_route_error(503);
    }
}

if (preg_match('~^/(admin|admin-panel|parent|checkout|cart|track|profile|messages|login|register|saved|edit)(?:[/.]|$)~', $path)) {
    header('X-Robots-Tag: noindex, nofollow');
    header('Cache-Control: no-store, private');
} else {
    header('Cache-Control: no-cache, max-age=0, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
}
header('Content-Type: text/html; charset=utf-8');
require $root . '/' . $template;
