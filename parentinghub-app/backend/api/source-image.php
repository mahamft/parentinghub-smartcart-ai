<?php
declare(strict_types=1);

// ParentingHub trusted image proxy for authorized supplier catalogs.
// Supported sources are hard-coded to avoid arbitrary URL fetching / SSRF.

function ph_source_config(string $source): array {
    return match ($source) {
        'tommee' => [
            'origin' => 'https://tommeetippee.pk',
            'page_hosts' => ['tommeetippee.pk', 'www.tommeetippee.pk'],
            'image_hosts' => ['tommeetippee.pk', 'www.tommeetippee.pk', 'cdn.shopify.com', 'cdn.shopifycdn.net'],
        ],
        default => [
            'origin' => 'https://www.philipsaventpakistan.com',
            'page_hosts' => ['www.philipsaventpakistan.com', 'philipsaventpakistan.com'],
            'image_hosts' => ['www.philipsaventpakistan.com', 'philipsaventpakistan.com'],
        ],
    };
}

function ph_fail_placeholder(): never {
    $placeholder = dirname(__DIR__, 2) . '/frontend/art/placeholder.jpg';
    if (is_file($placeholder)) {
        header('Content-Type: image/jpeg');
        header('Cache-Control: public, max-age=3600');
        header('X-Content-Type-Options: nosniff');
        readfile($placeholder);
        exit;
    }
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Image unavailable';
    exit;
}

function ph_allowed_host(string $host, array $allowed): bool {
    return in_array(strtolower($host), array_map('strtolower', $allowed), true);
}

function ph_normalize_remote_url(string $url, array $cfg): ?string {
    $url = html_entity_decode(trim($url), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    if ($url === '') return null;
    if (str_starts_with($url, '//')) $url = 'https:' . $url;
    if (str_starts_with($url, '/')) $url = rtrim($cfg['origin'], '/') . $url;
    if (!str_starts_with($url, 'https://')) return null;
    return $url;
}

function ph_fetch(string $url, array $cfg, bool $binary = false): array {
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    $allowed = $binary ? $cfg['image_hosts'] : $cfg['page_hosts'];
    if (!ph_allowed_host($host, $allowed)) {
        return ['ok' => false, 'body' => '', 'type' => '', 'url' => ''];
    }

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 4,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'ParentingHubCatalog/2.0 (+https://www.parentinghub.pk)',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => '',
        ]);
        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $type = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $final = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        if ($errno !== 0 || $body === false || $status < 200 || $status >= 400) {
            return ['ok' => false, 'body' => '', 'type' => '', 'url' => ''];
        }
        $finalHost = strtolower((string) parse_url($final, PHP_URL_HOST));
        if (!ph_allowed_host($finalHost, $allowed)) {
            return ['ok' => false, 'body' => '', 'type' => '', 'url' => ''];
        }
        return ['ok' => true, 'body' => $body, 'type' => $type, 'url' => $final];
    }

    if (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create(['http' => [
            'timeout' => 20,
            'user_agent' => 'ParentingHubCatalog/2.0 (+https://www.parentinghub.pk)',
            'follow_location' => 1,
            'max_redirects' => 4,
        ]]);
        $body = @file_get_contents($url, false, $ctx);
        if ($body !== false) {
            $type = $binary ? 'image/jpeg' : 'text/html';
            if (isset($http_response_header) && is_array($http_response_header)) {
                foreach ($http_response_header as $headerLine) {
                    if (stripos($headerLine, 'Content-Type:') === 0) {
                        $type = trim(substr($headerLine, strlen('Content-Type:')));
                        break;
                    }
                }
            }
            return ['ok' => true, 'body' => $body, 'type' => $type, 'url' => $url];
        }
    }
    return ['ok' => false, 'body' => '', 'type' => '', 'url' => ''];
}

function ph_first_product_url(string $html, string $source, array $cfg): ?string {
    $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    if ($source === 'tommee') {
        if (preg_match_all('~href=["\'](/products/[a-z0-9][a-z0-9\-]*)(?:\?[^"\']*)?["\']~i', $html, $m)) {
            foreach ($m[1] as $path) return rtrim($cfg['origin'], '/') . $path;
        }
        if (preg_match_all('~https://(?:www\.)?tommeetippee\.pk/products/[a-z0-9][a-z0-9\-]*~i', $html, $m)) {
            foreach ($m[0] as $url) return $url;
        }
        return null;
    }

    if (preg_match_all('~https://(?:www\.)?philipsaventpakistan\.com/product/[a-z0-9\-]+/?~i', $html, $m)) {
        foreach ($m[0] as $url) {
            if (!str_contains($url, '/product-category/')) return rtrim($url, '/') . '/';
        }
    }
    return null;
}

function ph_extract_images(string $html, string $source, array $cfg): array {
    $out = [];
    $push = function(?string $url) use (&$out, $cfg): void {
        if (!$url) return;
        $url = ph_normalize_remote_url($url, $cfg);
        if (!$url) return;
        $low = strtolower($url);
        if (str_contains($low, 'logo') || str_contains($low, 'icon') || str_contains($low, 'background') ||
            str_contains($low, 'banner') || str_contains($low, 'cropped-') || str_contains($low, 'favicon') ||
            str_contains($low, 'track-order') || str_contains($low, '7c2123e3') || str_contains($low, 'group-172') ||
            str_contains($low, 'width=1&') || str_contains($low, 'width=32&') || str_contains($low, 'width=150') ||
            str_contains($low, 'width=170') || preg_match('/-\d+x\d+\.(?:png|jpe?g|webp)/i', $low)) {
            return;
        }
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if (!ph_allowed_host($host, $cfg['image_hosts'])) return;
        if (!preg_match('~\.(?:jpe?g|png|webp|avif)(?:\?.*)?$~i', $url)) return;
        if (!in_array($url, $out, true)) $out[] = $url;
    };

    if (class_exists('DOMDocument')) {
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        @$doc->loadHTML($html);
        libxml_clear_errors();
        $xp = new DOMXPath($doc);

        foreach ($xp->query('//meta[@property="og:image"]/@content') ?: [] as $node) $push($node->nodeValue);
        foreach ($xp->query('//meta[@name="twitter:image"]/@content') ?: [] as $node) $push($node->nodeValue);

        if ($source === 'tommee') {
            foreach ($xp->query('//img[contains(@src,"/cdn/shop/")]/@src') ?: [] as $node) $push($node->nodeValue);
            foreach ($xp->query('//img[contains(@data-src,"/cdn/shop/")]/@data-src') ?: [] as $node) $push($node->nodeValue);
            foreach ($xp->query('//source[contains(@srcset,"/cdn/shop/")]/@srcset') ?: [] as $node) {
                foreach (explode(',', $node->nodeValue) as $candidate) {
                    $piece = trim(preg_split('/\s+/', trim($candidate))[0] ?? '');
                    $push($piece);
                }
            }
        } else {
            foreach ($xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " woocommerce-product-gallery__image ")]//a/@href') ?: [] as $node) $push($node->nodeValue);
            foreach ($xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " woocommerce-product-gallery__image ")]//img/@data-large_image') ?: [] as $node) $push($node->nodeValue);
            foreach ($xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " woocommerce-product-gallery__image ")]//img/@src') ?: [] as $node) $push($node->nodeValue);
        }
    }

    $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    if ($source === 'tommee') {
        if (preg_match_all('~(?:https:)?//(?:www\.)?tommeetippee\.pk/cdn/shop/[^"\'<> ]+?\.(?:jpe?g|png|webp|avif)(?:\?[^"\'<> ]*)?~i', $decoded, $m)) {
            foreach ($m[0] as $url) $push($url);
        }
        if (preg_match_all('~https://cdn\.shopify\.com/[^"\'<> ]+?\.(?:jpe?g|png|webp|avif)(?:\?[^"\'<> ]*)?~i', $decoded, $m)) {
            foreach ($m[0] as $url) $push($url);
        }
    } else {
        if (preg_match_all('~https://(?:www\.)?philipsaventpakistan\.com/wp-content/uploads/[^"\'<> ]+?\.(?:jpe?g|png|webp)(?:\?[^"\'<> ]*)?~i', $decoded, $m)) {
            foreach ($m[0] as $url) $push($url);
        }
    }

    return array_slice($out, 0, 12);
}

function ph_cache_file(): string {
    $dir = __DIR__ . '/cache';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    return $dir . '/source-images.json';
}

function ph_cache_read(): array {
    $file = ph_cache_file();
    if (!is_file($file)) return [];
    $json = @file_get_contents($file);
    $data = $json ? json_decode($json, true) : null;
    return is_array($data) ? $data : [];
}

function ph_cache_write(array $cache): void {
    @file_put_contents(ph_cache_file(), json_encode($cache, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), LOCK_EX);
}

$source = strtolower(trim((string)($_GET['source'] ?? 'philips')));
if (!in_array($source, ['philips', 'tommee'], true)) $source = 'philips';
$cfg = ph_source_config($source);

$sku = trim((string)($_GET['sku'] ?? ''));
$name = trim((string)($_GET['name'] ?? ''));
$slot = max(1, min(12, (int)($_GET['slot'] ?? 1)));
if ($sku === '' && $name === '') ph_fail_placeholder();

$key = $source . ':' . strtolower($sku !== '' ? $sku : $name);
$baseName = preg_replace('/\s*—\s*Variant\s*\d+/i', '', $name);
$cache = ph_cache_read();
$entry = is_array($cache[$key] ?? null) ? $cache[$key] : null;

if (!$entry && $sku !== '') {
    $entry = is_array($cache[$source . ':' . strtolower($sku)] ?? null) ? $cache[$source . ':' . strtolower($sku)] : null;
}
if (!$entry && $name !== '') {
    $entry = is_array($cache[$source . ':' . strtolower($name)] ?? null) ? $cache[$source . ':' . strtolower($name)] : null;
}
if (!$entry && $baseName !== '') {
    $entry = is_array($cache[$source . ':' . strtolower($baseName)] ?? null) ? $cache[$source . ':' . strtolower($baseName)] : null;
}

// Fast path: if found in cache, immediately redirect (302) to direct CDN image
if ($entry && isset($entry['images']) && is_array($entry['images']) && count($entry['images']) > 0) {
    $images = array_values(array_filter($entry['images'], 'is_string'));
    if (!empty($images)) {
        $imageUrl = $images[$slot - 1] ?? $images[0];
        if (str_starts_with($imageUrl, 'http')) {
            header('Location: ' . $imageUrl, true, 302);
            header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
            exit;
        }
    }
}

if (!$entry || !isset($entry['images']) || !is_array($entry['images']) || count($entry['images']) === 0) {
    $term = $sku !== '' ? $sku : $baseName;
    $searchUrl = $source === 'tommee'
        ? $cfg['origin'] . '/search?q=' . rawurlencode($term) . '&type=product'
        : $cfg['origin'] . '/?s=' . rawurlencode($term) . '&post_type=product';

    $search = ph_fetch($searchUrl, $cfg, false);
    if (!$search['ok']) ph_fail_placeholder();

    $productUrl = null;
    $productBody = null;

    if (str_contains((string)($search['url'] ?? ''), '/product/') || str_contains((string)($search['url'] ?? ''), '/products/')) {
        $productUrl = $search['url'];
        $productBody = (string)$search['body'];
    } else {
        $productUrl = ph_first_product_url((string)$search['body'], $source, $cfg);
        if (!$productUrl && $sku !== '' && $baseName !== '') {
            $searchUrl = $source === 'tommee'
                ? $cfg['origin'] . '/search?q=' . rawurlencode($baseName) . '&type=product'
                : $cfg['origin'] . '/?s=' . rawurlencode($baseName) . '&post_type=product';
            $search = ph_fetch($searchUrl, $cfg, false);
            if ($search['ok']) {
                if (str_contains((string)($search['url'] ?? ''), '/product/') || str_contains((string)($search['url'] ?? ''), '/products/')) {
                    $productUrl = $search['url'];
                    $productBody = (string)$search['body'];
                } else {
                    $productUrl = ph_first_product_url((string)$search['body'], $source, $cfg);
                }
            }
        }
        if (!$productUrl) ph_fail_placeholder();
    }

    if ($productBody === null) {
        $product = ph_fetch($productUrl, $cfg, false);
        if (!$product['ok']) ph_fail_placeholder();
        $productBody = (string)$product['body'];
    }

    $images = ph_extract_images($productBody, $source, $cfg);
    if (!$images) ph_fail_placeholder();

    $entry = [
        'source' => $source,
        'product_url' => $productUrl,
        'images' => $images,
        'updated_at' => gmdate('c'),
    ];
    $cache[$key] = $entry;
    ph_cache_write($cache);
}

$images = array_values(array_filter($entry['images'] ?? [], 'is_string'));
if (!$images) ph_fail_placeholder();
$imageUrl = $images[$slot - 1] ?? $images[0];

// Instant fast 302 redirect directly to supplier CDN (prevents server-side cURL timeouts/blocking)
if (isset($imageUrl) && is_string($imageUrl) && str_starts_with($imageUrl, 'http')) {
    header('Location: ' . $imageUrl, true, 302);
    header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
    exit;
}
if (!is_dir($imgCacheDir)) @mkdir($imgCacheDir, 0755, true);

$ext = 'jpg';
if (preg_match('~\.(jpe?g|png|webp|avif)(?:\?.*)?$~i', $imageUrl, $extMatch)) {
    $ext = strtolower($extMatch[1]);
}
$imgCacheFile = $imgCacheDir . '/' . md5($imageUrl) . '.' . $ext;
$mimeCacheFile = $imgCacheDir . '/' . md5($imageUrl) . '.mime';

if (is_file($imgCacheFile) && filesize($imgCacheFile) > 0) {
    $mime = is_file($mimeCacheFile) ? trim((string)@file_get_contents($mimeCacheFile)) : ('image/' . ($ext === 'jpg' ? 'jpeg' : $ext));
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
    header('X-Content-Type-Options: nosniff');
    header('Content-Disposition: inline');
    readfile($imgCacheFile);
    exit;
}

$image = ph_fetch($imageUrl, $cfg, true);
if (!$image['ok'] || !str_starts_with(strtolower((string)$image['type']), 'image/')) ph_fail_placeholder();

$cleanMime = preg_replace('/;.*$/', '', (string)$image['type']);
@file_put_contents($imgCacheFile, $image['body'], LOCK_EX);
@file_put_contents($mimeCacheFile, $cleanMime, LOCK_EX);

header('Content-Type: ' . $cleanMime);
header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: inline');
echo $image['body'];
