<?php
declare(strict_types=1);

/**
 * ParentingHub AI Rate Limiter
 * 
 * Protects AI and computation-heavy endpoints against brute force, scraping,
 * and cost abuse. Uses ph_rate_limits table (compatible with MySQL and SQLite).
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

/**
 * Get client IP address securely, handling common proxy headers with sanitization.
 */
function ph_get_client_ip(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $candidate = trim($parts[0]);
        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            $ip = $candidate;
        }
    }
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
}

/**
 * Check and record a rate limit hit.
 * 
 * @param PDO $pdo Active database connection
 * @param string $action Endpoint identifier (e.g. 'ai_assistant', 'ai_compare')
 * @param int $maxHits Maximum allowed calls in the given window
 * @param int $windowSeconds Window duration in seconds (default: 60s)
 * @param string|null $identifier Optional custom identifier (falls back to client IP)
 * @return array ['allowed' => bool, 'remaining' => int, 'reset' => int]
 */
function ph_rate_limit_check(
    PDO $pdo,
    string $action,
    int $maxHits = 30,
    int $windowSeconds = 60,
    ?string $identifier = null
): array {
    $now = time();
    $id = $identifier ?? ph_get_client_ip();
    $bucketKey = hash('sha256', "rl:{$action}:{$id}");

    // Ensure ph_rate_limits table exists in current PDO session
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS ph_rate_limits (
            bucket_key VARCHAR(120) PRIMARY KEY,
            hits INT UNSIGNED NOT NULL DEFAULT 1,
            reset_at INT UNSIGNED NOT NULL
        )");
    } catch (Throwable $e) {
        // Table already exists or handled by migrations
    }

    // Clean up expired buckets opportunistically (1% chance per call)
    if (random_int(1, 100) === 1) {
        try {
            $pdo->prepare("DELETE FROM ph_rate_limits WHERE reset_at < ?")->execute([$now]);
        } catch (Throwable $e) {}
    }

    // Check existing record
    $st = $pdo->prepare("SELECT hits, reset_at FROM ph_rate_limits WHERE bucket_key = ? LIMIT 1");
    $st->execute([$bucketKey]);
    $row = $st->fetch();

    if ($row && (int)$row['reset_at'] > $now) {
        $hits = (int)$row['hits'];
        $resetAt = (int)$row['reset_at'];

        if ($hits >= $maxHits) {
            return [
                'allowed' => false,
                'remaining' => 0,
                'reset' => $resetAt - $now
            ];
        }

        // Increment hit
        $upd = $pdo->prepare("UPDATE ph_rate_limits SET hits = hits + 1 WHERE bucket_key = ?");
        $upd->execute([$bucketKey]);

        return [
            'allowed' => true,
            'remaining' => max(0, $maxHits - ($hits + 1)),
            'reset' => $resetAt - $now
        ];
    }

    // New or expired window - cross-compatible with MariaDB/MySQL and SQLite
    $resetAt = $now + $windowSeconds;
    $driver = strtolower((string)$pdo->getAttribute(PDO::ATTR_DRIVER_NAME));

    if ($driver === 'sqlite') {
        $stmt = $pdo->prepare("INSERT INTO ph_rate_limits (bucket_key, hits, reset_at) VALUES (?, 1, ?)
            ON CONFLICT(bucket_key) DO UPDATE SET hits = 1, reset_at = excluded.reset_at");
    } else {
        $stmt = $pdo->prepare("INSERT INTO ph_rate_limits (bucket_key, hits, reset_at) VALUES (?, 1, ?)
            ON DUPLICATE KEY UPDATE hits = 1, reset_at = VALUES(reset_at)");
    }
    $stmt->execute([$bucketKey, $resetAt]);

    return [
        'allowed' => true,
        'remaining' => $maxHits - 1,
        'reset' => $windowSeconds
    ];
}

/**
 * Enforce rate limit directly, issuing 429 response if breached.
 */
function ph_require_rate_limit(PDO $pdo, string $action, int $maxHits = 30, int $windowSeconds = 60): void {
    $status = ph_rate_limit_check($pdo, $action, $maxHits, $windowSeconds);
    if (!$status['allowed']) {
        if (function_exists('ph_error')) {
            ph_error(
                "Too many requests. Please wait {$status['reset']} seconds before asking again.",
                429,
                ['code' => 'RATE_LIMIT_EXCEEDED', 'retryAfter' => $status['reset']]
            );
        } else {
            http_response_code(429);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => "Too many requests. Please wait {$status['reset']} seconds.",
                'code' => 'RATE_LIMIT_EXCEEDED',
                'retryAfter' => $status['reset']
            ]);
            exit;
        }
    }
}
