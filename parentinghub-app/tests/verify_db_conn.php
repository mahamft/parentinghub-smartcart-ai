<?php
define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';

$pdo = ph_pdo();
$dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
echo "Active Connected Database: {$dbName}\n";

$cfg = ph_db_config();
echo "Configured Host: {$cfg['host']}\n";
echo "Configured Port: {$cfg['port']}\n";
echo "Configured DB Name: {$cfg['name']}\n";
echo "Configured User: {$cfg['user']}\n";
echo "Password Configured: " . (strlen($cfg['pass']) > 0 ? "YES (hidden)" : "NO (empty)") . "\n";

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Total Tables: " . count($tables) . "\n";
foreach ($tables as $t) {
    echo "  - {$t}\n";
}

