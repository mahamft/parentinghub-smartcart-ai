<?php
/**
 * Standalone Worker Process for Genuine Database Concurrency Testing.
 * 
 * Invoked as an independent OS process:
 * php worker_reserve.php --db=<dbPath> --worker=<id> --product=<productId> --sync=<syncFile>
 */

declare(strict_types=1);

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', '0');

$opts = getopt('', ['db:', 'worker:', 'product:', 'sync:']);

$dbPath = $opts['db'] ?? '';
$workerId = $opts['worker'] ?? 'worker-1';
$productId = $opts['product'] ?? '';
$syncFile = $opts['sync'] ?? '';

if (empty($dbPath) || empty($productId)) {
    echo json_encode(['success' => false, 'error' => 'Missing arguments']);
    exit(2);
}

$baseDir = dirname(__DIR__);
require_once $baseDir . '/backend/api/bootstrap.php';
require_once $baseDir . '/backend/api/lib/pricing.php';
require_once $baseDir . '/backend/api/paypal/paypal_client.php';
require_once $baseDir . '/backend/api/paypal/paypal_service.php';

// Each worker opens its OWN independent PDO database connection
$pdo = new PDO("sqlite:" . $dbPath, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_TIMEOUT => 10
]);
$pdo->exec("PRAGMA busy_timeout = 10000;");

$client = new PhPayPalClient(['PAYPAL_MODE' => 'mock']);

$customer = [
    'name' => "Customer {$workerId}",
    'phone' => '0300' . sprintf('%07d', crc32($workerId) % 10000000),
    'email' => "{$workerId}@example.com",
    'address' => "Address of {$workerId}",
    'city' => 'Lahore'
];

// Synchronization Barrier: Wait until master signals go
if (!empty($syncFile)) {
    $waited = 0;
    while (!file_exists($syncFile) && $waited < 500) {
        usleep(5000); // 5ms sleep
        $waited++;
    }
}

// Record start time at microsecond precision
$startTime = microtime(true);

$result = [
    'worker' => $workerId,
    'pid' => getmypid(),
    'product' => $productId,
    'startTime' => $startTime,
    'success' => false,
    'orderId' => null,
    'paypalOrderId' => null,
    'error' => null,
    'exceptionType' => null
];

try {
    $order = PhPayPalService::initiateOrder($pdo, [
        'customer' => $customer,
        'items' => [['id' => $productId, 'qty' => 1]]
    ], null, $client);

    $result['success'] = true;
    $result['orderId'] = $order['orderId'];
    $result['paypalOrderId'] = $order['paypalOrderId'];
} catch (DomainException $e) {
    $result['success'] = false;
    $result['error'] = $e->getMessage();
    $result['exceptionType'] = 'DomainException';
} catch (PDOException $e) {
    $result['success'] = false;
    $result['error'] = $e->getMessage();
    $result['exceptionType'] = 'PDOException';
} catch (Throwable $e) {
    $result['success'] = false;
    $result['error'] = $e->getMessage();
    $result['exceptionType'] = get_class($e);
}

$result['endTime'] = microtime(true);
$result['durationMs'] = round(($result['endTime'] - $startTime) * 1000, 2);

echo json_encode($result);
exit($result['success'] ? 0 : 1);

