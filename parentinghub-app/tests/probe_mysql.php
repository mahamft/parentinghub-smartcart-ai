<?php
$host = '127.0.0.1';
$port = 3306;
$errno = 0;
$errstr = '';

echo "=== Probing MySQL/MariaDB Connection ===\n";
echo "Testing TCP port {$port} on {$host}...\n";

$sock = @fsockopen($host, $port, $errno, $errstr, 3);
if ($sock) {
    echo "SUCCESS: Port {$port} is open and responding.\n";
    fclose($sock);
} else {
    echo "RESULT: Port {$port} connection failed. Error {$errno}: {$errstr}\n";
}

// Check PHP PDO MySQL driver availability
$drivers = PDO::getAvailableDrivers();
echo "Available PDO drivers: " . implode(', ', $drivers) . "\n";
$hasMysql = in_array('mysql', $drivers, true);
echo "PDO MySQL Driver installed: " . ($hasMysql ? "YES" : "NO") . "\n";

if ($hasMysql && $sock) {
    try {
        $pdo = new PDO("mysql:host={$host};port={$port}", 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 3
        ]);
        echo "SUCCESS: Connected to MySQL server via PDO!\n";
        $version = $pdo->query("SELECT VERSION()")->fetchColumn();
        echo "MySQL/MariaDB Server Version: {$version}\n";
    } catch (PDOException $e) {
        echo "PDO Connection Error: " . $e->getMessage() . "\n";
    }
}

