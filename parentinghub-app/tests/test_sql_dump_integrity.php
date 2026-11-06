<?php
declare(strict_types=1);

/**
 * Validates database/init_local_dev.sql structure and statement validity
 */

$sqlFile = dirname(__DIR__) . '/database/init_local_dev.sql';
if (!is_file($sqlFile)) {
    die("init_local_dev.sql missing\n");
}

$contents = file_get_contents($sqlFile);
echo "SQL file size: " . number_format(strlen($contents)) . " bytes\n";

$checks = [
    'DROP TABLE IF EXISTS `products`' => false,
    'CREATE TABLE `products`' => false,
    'CREATE TABLE `legacy_products_archive`' => false,
    'CREATE TABLE `ph_users`' => false,
    'CREATE TABLE `ph_sessions`' => false,
    'CREATE TABLE `ph_catalog_state`' => false,
    'CREATE TABLE `ph_orders`' => false,
    'CREATE TABLE `ph_payments`' => false,
    'CREATE TABLE `ph_payment_events`' => false,
    'CREATE TABLE `ph_ai_cart_proposals`' => false,
    'CREATE TABLE `ph_rate_limits`' => false,
    'CREATE TRIGGER `ph_catalog_products_after_insert`' => false,
    'CREATE TRIGGER `ph_catalog_products_after_update`' => false,
    'CREATE TRIGGER `ph_catalog_products_after_delete`' => false,
    'INSERT INTO `products`' => false,
    'INSERT INTO `ph_catalog_state`' => false
];

foreach ($checks as $pattern => &$found) {
    if (str_contains($contents, $pattern)) {
        $found = true;
        echo "  [FOUND] $pattern\n";
    } else {
        echo "  [MISSING] $pattern\n";
    }
}

// Count product rows in INSERT INTO `products`
preg_match_all("/\('([^']+)',\s*(NULL|'[^']*'),\s*'([^']*)',\s*([0-9.]+)/", $contents, $matches);
$productInsertCount = count($matches[0]);
echo "Product insert statements counted: $productInsertCount\n";

if ($productInsertCount !== 287) {
    echo "ERROR: Expected 287 products in init_local_dev.sql, found $productInsertCount\n";
    exit(1);
}

// Verify legacy_products_archive rows counted
preg_match_all("/\('archive-item-\d+',\s*'[^']+',\s*1000\.00\)/", $contents, $archiveMatches);
$archiveCount = count($archiveMatches[0]);
echo "Legacy archive count: $archiveCount (expected 140)\n";
if ($archiveCount !== 140) {
    echo "ERROR: Expected 140 archived products, found $archiveCount\n";
    exit(1);
}

echo "SQL dump structural validation PASSED.\n";

