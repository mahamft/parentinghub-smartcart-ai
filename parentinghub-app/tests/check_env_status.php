<?php
declare(strict_types=1);

$mysqlStatus = 'UNAVAILABLE';
$mysqlError = '';
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '', [
        PDO::ATTR_TIMEOUT => 2,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $mysqlStatus = 'CONNECTED';
} catch (Throwable $e) {
    $mysqlStatus = 'UNAVAILABLE';
    $mysqlError = $e->getMessage();
}

$envFile = dirname(__DIR__) . '/backend/.env';
$hasGemini = false;
$hasPayPalId = false;
$hasPayPalSecret = false;

if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v, " \t\n\r\0\x0B\"'");
        if ($k === 'GEMINI_API_KEY' && $v !== '' && !str_starts_with($v, '__') && !str_contains($v, 'placeholder')) {
            $hasGemini = true;
        }
        if ($k === 'PAYPAL_CLIENT_ID' && $v !== '' && !str_starts_with($v, '__') && !str_contains($v, 'placeholder')) {
            $hasPayPalId = true;
        }
        if ($k === 'PAYPAL_CLIENT_SECRET' && $v !== '' && !str_starts_with($v, '__') && !str_contains($v, 'placeholder')) {
            $hasPayPalSecret = true;
        }
    }
}

echo "MYSQL_STATUS: " . $mysqlStatus . ($mysqlError ? " (" . $mysqlError . ")" : "") . "\n";
echo "GEMINI_KEY_CONFIGURED: " . ($hasGemini ? "YES" : "NO") . "\n";
echo "PAYPAL_CLIENT_ID_CONFIGURED: " . ($hasPayPalId ? "YES" : "NO") . "\n";
echo "PAYPAL_CLIENT_SECRET_CONFIGURED: " . ($hasPayPalSecret ? "YES" : "NO") . "\n";

