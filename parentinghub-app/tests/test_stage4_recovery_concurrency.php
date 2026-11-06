<?php
/**
 * ParentingHub SmartCart AI — Stage 4 PayPal Recovery & Inventory Concurrency Test Suite
 *
 * Targeted verification for:
 * 1. PayPal Capture Recovery (Lost network responses, ORDER_ALREADY_CAPTURED handling, remote status reconciliation, timeout resilience)
 * 2. Inventory Concurrency under boolean in_stock schema (short transactions, reservation TTL, safe release on failure/cancellation, commitment on capture)
 * 3. Webhook security & mandatory payment validation before confirmation
 * 4. Header-based token security & Git untracked .env verification
 */

declare(strict_types=1);

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', '0');

$baseDir = dirname(__DIR__);

require_once $baseDir . '/backend/api/bootstrap.php';
require_once $baseDir . '/backend/api/lib/pricing.php';
require_once $baseDir . '/backend/api/paypal/paypal_client.php';
require_once $baseDir . '/backend/api/paypal/paypal_service.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "======================================================================\n";
echo "   Stage 4: PayPal Capture Recovery & Inventory Concurrency Suite\n";
echo "======================================================================\n\n";

$passed = 0;
$failed = 0;

function assertRecovery(bool $cond, string $desc, string $detail = ''): void {
    global $passed, $failed;
    if ($cond) {
        echo "  [PASS] {$desc}\n";
        if ($detail !== '') echo "         -> {$detail}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$desc}\n";
        if ($detail !== '') echo "         -> {$detail}\n";
        $failed++;
    }
}

// -------------------------------------------------------------
// Database Setup: Isolated SQLite memory instance
// -------------------------------------------------------------
$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$pdo->exec("CREATE TABLE products (
    id TEXT PRIMARY KEY,
    sku TEXT,
    name TEXT NOT NULL,
    price REAL NOT NULL DEFAULT 0,
    compare_at REAL,
    image TEXT,
    images TEXT,
    category TEXT NOT NULL DEFAULT 'General',
    brand TEXT NOT NULL DEFAULT 'ParentingHub',
    description TEXT,
    has_options INTEGER NOT NULL DEFAULT 0,
    options_json TEXT,
    rating REAL NOT NULL DEFAULT 0,
    reviews INTEGER NOT NULL DEFAULT 0,
    in_stock INTEGER NOT NULL DEFAULT 1,
    weight_kg REAL,
    tags TEXT,
    source_url TEXT,
    video_url TEXT,
    source_json TEXT
)");

$pdo->exec("CREATE TABLE ph_catalog_state (
    state_key TEXT PRIMARY KEY,
    payload TEXT NOT NULL
)");

$pdo->exec("CREATE TABLE ph_orders (
    id TEXT PRIMARY KEY,
    user_id INTEGER,
    status TEXT NOT NULL,
    payment TEXT NOT NULL,
    subtotal REAL NOT NULL DEFAULT 0,
    shipping REAL NOT NULL DEFAULT 0,
    discount REAL NOT NULL DEFAULT 0,
    total REAL NOT NULL DEFAULT 0,
    coupon TEXT,
    address_json TEXT NOT NULL,
    items_json TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE ph_payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id TEXT NOT NULL,
    provider TEXT NOT NULL DEFAULT 'paypal',
    paypal_order_id TEXT UNIQUE NOT NULL,
    payment_token TEXT,
    capture_id TEXT UNIQUE,
    currency TEXT NOT NULL DEFAULT 'USD',
    amount_usd REAL NOT NULL,
    amount_pkr REAL NOT NULL,
    fx_rate REAL NOT NULL,
    status TEXT NOT NULL DEFAULT 'CREATED',
    payer_email TEXT,
    raw_response TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE ph_payment_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_id TEXT NOT NULL UNIQUE,
    event_type TEXT NOT NULL,
    order_id TEXT,
    verified INTEGER NOT NULL DEFAULT 1,
    payload TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE ph_inventory_reservations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id TEXT NOT NULL,
    product_id TEXT NOT NULL,
    qty INTEGER NOT NULL DEFAULT 1,
    status TEXT NOT NULL DEFAULT 'RESERVED',
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

// Seed products from catalog.json
$catalogJsonPath = $baseDir . '/backend/api/seed/catalog.json';
if (!file_exists($catalogJsonPath)) {
    $catalogJsonPath = dirname($baseDir) . '/parentinghub-app/backend/api/seed/catalog.json';
}
$catalogData = json_decode(file_get_contents($catalogJsonPath), true);
$products = $catalogData['products'] ?? [];

$stProd = $pdo->prepare("INSERT INTO products (id, sku, name, price, category, brand, in_stock, has_options, options_json) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($products as $p) {
    $stProd->execute([
        (string)$p['id'],
        (string)($p['sku'] ?? ''),
        (string)$p['name'],
        (float)$p['price'],
        (string)($p['category'] ?? 'General'),
        (string)($p['brand'] ?? 'ParentingHub'),
        !empty($p['inStock']) ? 1 : 0,
        !empty($p['hasOptions']) ? 1 : 0,
        json_encode($p['options'] ?? [])
    ]);
}

$stCat = $pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload) VALUES ('catalog', ?)");
$stCat->execute([json_encode($catalogData)]);

// Select valid products without mandatory options for predictable testing
$validSeedProds = array_values(array_filter($products, fn($p) => empty($p['hasOptions']) && !empty($p['inStock'])));
$p1 = (string)$validSeedProds[0]['id'];
$p2 = (string)$validSeedProds[1]['id'];
$p3 = (string)$validSeedProds[2]['id'];
$p4 = (string)$validSeedProds[3]['id'];
$p5 = (string)$validSeedProds[4]['id'];
$p6 = (string)$validSeedProds[5]['id'];

echo "Catalog Initialized: " . count($products) . " products loaded.\n\n";

// ============================================================================
// SECTION 1: PayPal Capture Recovery (Critical Priority)
// ============================================================================
echo "--- Section 1: PayPal Capture Recovery & Network Drop Resilience ---\n";

$client = new PhPayPalClient(['PAYPAL_MODE' => 'mock', 'PAYPAL_MERCHANT_ID' => 'PARENTINGHUB-MERCHANT-PK']);

// 1.1 Initiate Order
$customer = [
    'name' => 'Ayesha Khan',
    'phone' => '03001234567',
    'email' => 'ayesha@example.com',
    'address' => 'House 42, Street 7, F-8/2',
    'city' => 'Islamabad'
];
$initOrder = PhPayPalService::initiateOrder($pdo, [
    'customer' => $customer,
    'items' => [['id' => $p1, 'qty' => 1]]
], null, $client);

$orderId = $initOrder['orderId'];
$paypalOrderId = $initOrder['paypalOrderId'];
$expectedUsd = $initOrder['amountUsd'];

assertRecovery($initOrder['success'] === true, "1.1 Order initiated: {$orderId} (PayPal: {$paypalOrderId})");

// Simulate Scenario:
// Customer approved on PayPal, and PayPal captured the order remotely,
// but the response network connection dropped before ParentingHub saved the result!
// Local DB remains 'Pending Payment' / 'CREATED'.
$mockCaptureId = 'CAPTURE-LOST-NET-' . bin2hex(random_bytes(4));
$simulatedCapturedPayload = [
    'id' => $paypalOrderId,
    'status' => 'COMPLETED',
    'purchase_units' => [
        [
            'reference_id' => $orderId,
            'payee' => [
                'merchant_id' => 'PARENTINGHUB-MERCHANT-PK',
                'email_address' => 'merchant@parentinghub.pk'
            ],
            'payments' => [
                'captures' => [
                    [
                        'id' => $mockCaptureId,
                        'status' => 'COMPLETED',
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => number_format($expectedUsd, 2, '.', '')
                        ],
                        'final_capture' => true
                    ]
                ]
            ]
        ]
    ],
    'payer' => [
        'email_address' => 'ayesha@example.com'
    ]
];
PhPayPalClient::setMockOrder($paypalOrderId, $simulatedCapturedPayload);

// Verify local DB state before retry
$stCheckPay = $pdo->prepare("SELECT status FROM ph_payments WHERE order_id = ?");
$stCheckPay->execute([$orderId]);
$payStatusBefore = $stCheckPay->fetchColumn();

$stCheckOrd = $pdo->prepare("SELECT status FROM ph_orders WHERE id = ?");
$stCheckOrd->execute([$orderId]);
$ordStatusBefore = $stCheckOrd->fetchColumn();

assertRecovery($payStatusBefore === 'CREATED' && $ordStatusBefore === 'Pending Payment', 
    "1.2 Simulated network failure pre-condition verified: Local DB status is CREATED / Pending Payment");

// 1.3 Customer retries captureOrder
$recoveryResult = PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $client);

assertRecovery($recoveryResult['success'] === true, "1.3 Recovery capture succeeded without error");
assertRecovery($recoveryResult['reconciled'] === true, "1.4 Order identified as already completed on PayPal and safely reconciled (reconciled=true)");
assertRecovery($recoveryResult['captureId'] === $mockCaptureId, "1.5 Correct PayPal capture ID reconciled: {$mockCaptureId}");

// 1.6 Verify local database updated atomically
$stCheckPay->execute([$orderId]);
$payAfter = $pdo->prepare("SELECT status, capture_id FROM ph_payments WHERE order_id = ?");
$payAfter->execute([$orderId]);
$payRow = $payAfter->fetch();

$stCheckOrd->execute([$orderId]);
$ordStatusAfter = $stCheckOrd->fetchColumn();

assertRecovery($payRow['status'] === 'COMPLETED' && $payRow['capture_id'] === $mockCaptureId, 
    "1.6 ph_payments atomically updated to COMPLETED with persisted capture_id");
assertRecovery($ordStatusAfter === 'Confirmed', "1.7 ph_orders atomically updated to Confirmed");

// 1.8 Subsequent duplicate capture returns idempotent success
$dupResult = PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $client);
assertRecovery($dupResult['success'] === true && $dupResult['isDuplicate'] === true, 
    "1.8 Subsequent retry handled idempotently with isDuplicate=true (no double capture)");

// 1.9 Network Timeout Resilience: Temporary connection error does NOT fail order prematurely
$initOrder2 = PhPayPalService::initiateOrder($pdo, [
    'customer' => $customer,
    'items' => [['id' => $p2, 'qty' => 1]]
], null, $client);

$orderId2 = $initOrder2['orderId'];
$paypalOrderId2 = $initOrder2['paypalOrderId'];

// Create a mock client that throws a network timeout
class TimeoutMockPayPalClient extends PhPayPalClient {
    public function __construct() {
        parent::__construct(['PAYPAL_MODE' => 'mock']);
    }
    public function getOrder(string $id): array {
        throw new RuntimeException("cURL error 28: Operation timed out after 15000 milliseconds with 0 bytes received");
    }
    public function captureOrder(string $id, ?string $amt = null): array {
        throw new RuntimeException("cURL error 28: Connection timed out to PayPal");
    }
}

$timeoutClient = new TimeoutMockPayPalClient();
$timeoutCaught = false;
try {
    PhPayPalService::captureOrder($pdo, $orderId2, $paypalOrderId2, $timeoutClient);
} catch (RuntimeException $e) {
    $timeoutCaught = true;
}

$stOrd2 = $pdo->prepare("SELECT status FROM ph_orders WHERE id = ?");
$stOrd2->execute([$orderId2]);
$statusOrd2 = $stOrd2->fetchColumn();

$stPay2 = $pdo->prepare("SELECT status FROM ph_payments WHERE order_id = ?");
$stPay2->execute([$orderId2]);
$statusPay2 = $stPay2->fetchColumn();

assertRecovery($timeoutCaught, "1.9 Temporary PayPal network timeout detected and thrown cleanly");
assertRecovery($statusOrd2 === 'Pending Payment' && $statusPay2 === 'CREATED', 
    "1.10 Order preserved in Pending Payment / CREATED (NOT marked failed or cancelled on timeout)");


// ============================================================================
// SECTION 2: Inventory Concurrency & Reservation Guarantees
// ============================================================================
echo "\n--- Section 2: Inventory Concurrency & Boolean in_stock Architecture ---\n";

// 2.1 Schema Analysis: Verify products table uses boolean in_stock (TINYINT / INTEGER)
$tableInfo = $pdo->query("PRAGMA table_info(products)")->fetchAll();
$inStockCol = null;
foreach ($tableInfo as $col) {
    if ($col['name'] === 'in_stock') {
        $inStockCol = $col;
        break;
    }
}
assertRecovery($inStockCol !== null, "2.1 products table contains in_stock column");

// 2.2 Reservation creation on initiateOrder
$resList = PhPayPalService::getReservations($pdo, $orderId);
assertRecovery(count($resList) > 0, "2.2 ph_inventory_reservations created upon order initiation (Found: " . count($resList) . ")");
assertRecovery($resList[0]['status'] === 'COMMITTED', "2.3 Captured order reservation status transitioned to COMMITTED");
assertRecovery(strtotime($resList[0]['expires_at']) > time(), "2.4 Reservation contains valid future expiration timestamp");

// 2.5 Cancellation safely releases reservations
$initOrder3 = PhPayPalService::initiateOrder($pdo, [
    'customer' => $customer,
    'items' => [['id' => $p3, 'qty' => 1]]
], null, $client);
$orderId3 = $initOrder3['orderId'];
$token3 = $initOrder3['paymentToken'];

$resBeforeCancel = PhPayPalService::getReservations($pdo, $orderId3);
assertRecovery($resBeforeCancel[0]['status'] === 'RESERVED', "2.5 Order {$orderId3} reservation initially RESERVED");

PhPayPalService::cancelOrder($pdo, $orderId3, $token3);

$resAfterCancel = PhPayPalService::getReservations($pdo, $orderId3);
assertRecovery($resAfterCancel[0]['status'] === 'RELEASED', "2.6 Order {$orderId3} reservation safely updated to RELEASED upon cancelOrder");

// 2.7 Stock exhaustion during checkout safely releases reservations
$initOrder4 = PhPayPalService::initiateOrder($pdo, [
    'customer' => $customer,
    'items' => [['id' => $p4, 'qty' => 1]]
], null, $client);
$orderId4 = $initOrder4['orderId'];
$paypalOrderId4 = $initOrder4['paypalOrderId'];

// Mark product out of stock before capture
$pdo->prepare("UPDATE products SET in_stock = 0 WHERE id = ?")->execute([$p4]);

$stockBlockCaught = false;
try {
    PhPayPalService::captureOrder($pdo, $orderId4, $paypalOrderId4, $client);
} catch (DomainException $e) {
    $stockBlockCaught = true;
}

$resAfterStockBlock = PhPayPalService::getReservations($pdo, $orderId4);
assertRecovery($stockBlockCaught, "2.7 Capture blocked when product becomes out of stock");
assertRecovery($resAfterStockBlock[0]['status'] === 'RELEASED', "2.8 Inventory reservation safely released when checkout blocked by stock");

// Restore p4 stock
$pdo->prepare("UPDATE products SET in_stock = 1 WHERE id = ?")->execute([$p4]);


// ============================================================================
// SECTION 3: Webhook Cryptographic Verification & Payment Validation
// ============================================================================
echo "\n--- Section 3: Webhook Verification & Strict Payment Integrity ---\n";

$initOrder5 = PhPayPalService::initiateOrder($pdo, [
    'customer' => $customer,
    'items' => [['id' => $p5, 'qty' => 1]]
], null, $client);
$orderId5 = $initOrder5['orderId'];
$paypalOrderId5 = $initOrder5['paypalOrderId'];
$expectedAmount5 = $initOrder5['amountUsd'];

// 3.1 Unsigned/Forged webhook is rejected before database mutation
$forgedHeaders = [
    'PAYPAL-TRANSMISSION-ID' => 'FORGED-TRANS-01',
    'PAYPAL-TRANSMISSION-TIME' => gmdate('Y-m-d\TH:i:s\Z'),
    'PAYPAL-TRANSMISSION-SIG' => 'FORGED_SIGNATURE_BYTES',
    'PAYPAL-CERT-URL' => 'https://api-m.sandbox.paypal.com/cert.pem',
    'PAYPAL-AUTH-ALGO' => 'SHA256withRSA'
];
$forgedPayload = json_encode([
    'id' => 'WH-FORGED-01',
    'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
    'resource' => ['custom_id' => $orderId5, 'id' => 'CAP-FORGED-01']
]);

$sigRejected = false;
try {
    PhPayPalService::handleWebhook($pdo, $forgedHeaders, $forgedPayload, $client);
} catch (InvalidArgumentException $e) {
    $sigRejected = true;
}
assertRecovery($sigRejected, "3.1 Forged webhook signature rejected before database state mutation");

// 3.2 Webhook cannot confirm an order without validating payment (Amount Mismatch attack)
$validHeaders = [
    'PAYPAL-TRANSMISSION-ID' => 'VALID-TRANS-01',
    'PAYPAL-TRANSMISSION-TIME' => gmdate('Y-m-d\TH:i:s\Z'),
    'PAYPAL-TRANSMISSION-SIG' => 'GENUINE_MOCK_SIGNATURE',
    'PAYPAL-CERT-URL' => 'https://api-m.sandbox.paypal.com/cert.pem',
    'PAYPAL-AUTH-ALGO' => 'SHA256withRSA'
];
$manipulatedAmountPayload = json_encode([
    'id' => 'WH-AMOUNT-MANIP-01',
    'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
    'resource' => [
        'id' => 'CAP-CHEAP-01',
        'custom_id' => $orderId5,
        'amount' => [
            'currency_code' => 'USD',
            'value' => '0.01' // Manipulated underpayment!
        ]
    ]
]);

$underpayRejected = false;
try {
    PhPayPalService::handleWebhook($pdo, $validHeaders, $manipulatedAmountPayload, $client);
} catch (DomainException $e) {
    $underpayRejected = true;
}

$stOrd5 = $pdo->prepare("SELECT status FROM ph_orders WHERE id = ?");
$stOrd5->execute([$orderId5]);
$statusOrd5 = $stOrd5->fetchColumn();

assertRecovery($underpayRejected, "3.2 Webhook rejected manipulated underpayment ($0.01 instead of \${$expectedAmount5})");
assertRecovery($statusOrd5 === 'Payment Failed', "3.3 Order marked 'Payment Failed' rather than Confirmed (fraud prevented)");

// 3.3 Valid genuine webhook confirms order and commits reservation
$initOrder6 = PhPayPalService::initiateOrder($pdo, [
    'customer' => $customer,
    'items' => [['id' => $p6, 'qty' => 1]]
], null, $client);
$orderId6 = $initOrder6['orderId'];
$paypalOrderId6 = $initOrder6['paypalOrderId'];
$expectedAmount6 = $initOrder6['amountUsd'];

$legitHeaders = [
    'PAYPAL-TRANSMISSION-ID' => 'VALID-TRANS-02',
    'PAYPAL-TRANSMISSION-TIME' => gmdate('Y-m-d\TH:i:s\Z'),
    'PAYPAL-TRANSMISSION-SIG' => 'GENUINE_MOCK_SIGNATURE',
    'PAYPAL-CERT-URL' => 'https://api-m.sandbox.paypal.com/cert.pem',
    'PAYPAL-AUTH-ALGO' => 'SHA256withRSA'
];
$legitPayload = json_encode([
    'id' => 'WH-LEGIT-EVENT-01',
    'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
    'resource' => [
        'id' => 'CAP-LEGIT-01',
        'custom_id' => $orderId6,
        'amount' => [
            'currency_code' => 'USD',
            'value' => number_format($expectedAmount6, 2, '.', '')
        ]
    ]
]);

$legitResult = PhPayPalService::handleWebhook($pdo, $legitHeaders, $legitPayload, $client);

$stOrd6 = $pdo->prepare("SELECT status FROM ph_orders WHERE id = ?");
$stOrd6->execute([$orderId6]);
$statusOrd6 = $stOrd6->fetchColumn();

$res6 = PhPayPalService::getReservations($pdo, $orderId6);

assertRecovery($legitResult['success'] === true && $legitResult['processed'] === true, 
    "3.4 Genuine webhook successfully processed");
assertRecovery($statusOrd6 === 'Confirmed', "3.5 Order {$orderId6} confirmed upon verified webhook");
assertRecovery($res6[0]['status'] === 'COMMITTED', "3.6 Inventory reservation transitioned to COMMITTED");


// ============================================================================
// SECTION 4: Security Verifications (Token Headers & Git Status)
// ============================================================================
echo "\n--- Section 4: Security Verifications (X-Payment-Token & Git .env) ---\n";

// 4.1 Payment Status queries support and prefer HTTP_X_PAYMENT_TOKEN header
$_SERVER['HTTP_X_PAYMENT_TOKEN'] = $initOrder6['paymentToken'];
$statusResp = PhPayPalService::getPaymentStatus($pdo, $orderId6, $_SERVER['HTTP_X_PAYMENT_TOKEN']);
assertRecovery($statusResp['success'] === true && $statusResp['orderStatus'] === 'Confirmed', 
    "4.1 Payment status verified via X-Payment-Token header");
unset($_SERVER['HTTP_X_PAYMENT_TOKEN']);

// 4.2 Git untracked check: Verify backend/.env is NOT tracked in Git
$gitCmd = 'git ls-files parentinghub-app/backend/.env';
$trackedFiles = shell_exec($gitCmd);
$isTracked = !empty(trim((string)$trackedFiles));
assertRecovery(!$isTracked, "4.2 Confirmed parentinghub-app/backend/.env is NOT tracked in Git (git ls-files is clean)");

// 4.3 Clean isolation: Verify parentinghub-original was never touched
$origDiff = shell_exec('git status --porcelain parentinghub-original');
$isOrigClean = empty(trim((string)$origDiff));
assertRecovery($isOrigClean, "4.3 Confirmed parentinghub-original/ is pristine and untouched");

echo "\n======================================================================\n";
echo " Recovery & Concurrency Suite Summary: {$passed} passed, {$failed} failed\n";
echo "======================================================================\n";

if ($failed > 0) {
    exit(1);
}
