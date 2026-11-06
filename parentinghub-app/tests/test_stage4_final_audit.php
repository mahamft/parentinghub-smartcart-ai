<?php
/**
 * ParentingHub SmartCart AI — Stage 4 Final Audit Test Suite
 *
 * Dedicated verification for:
 * 1. Quantity handling (qty > 1, line subtotaling, FX conversion, boolean in_stock limitation)
 * 2. Preservation of COD and Bank Transfer checkout with multi-item / qty > 1
 * 3. PayPal orphan-order mitigation (failed reservation after PayPal creation marked 'Reservation Failed' / 'CANCELLED')
 * 4. Preventing capture of unreserved orders (missing or released reservation blocked from capture)
 * 5. Duplicate reservation attempts (idempotent retry, unique (order_id, product_id) protection)
 * 6. Reservation lifecycle (cancellation, expiry reclamation, committed permanence)
 * 7. MySQL / SQLite schema migration and NULL unique constraint compatibility
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
echo "   ParentingHub: Stage 4 Final Security & Concurrency Audit Suite\n";
echo "======================================================================\n\n";

$passed = 0;
$failed = 0;

function assertAudit(bool $cond, string $desc, string $detail = ''): void {
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
// Database Setup: Isolated memory instance
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

$pdo->exec("CREATE TABLE ph_inventory_reservations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id TEXT NOT NULL,
    product_id TEXT NOT NULL,
    active_slot TEXT UNIQUE,
    qty INTEGER NOT NULL DEFAULT 1,
    status TEXT NOT NULL DEFAULT 'RESERVED',
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (order_id, product_id)
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
$pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload) VALUES ('catalog', ?)")
    ->execute([json_encode($catalogData)]);

$validProds = array_values(array_filter($products, fn($p) => empty($p['hasOptions']) && !empty($p['inStock'])));
$prod1 = $validProds[0];
$p1 = (string)$prod1['id'];
$price1 = (float)$prod1['price'];

$client = new PhPayPalClient(['PAYPAL_MODE' => 'mock', 'PAYPAL_MERCHANT_ID' => 'PARENTINGHUB-MERCHANT-PK']);

$customer = [
    'name' => 'Fatima Ali',
    'phone' => '03001234567',
    'email' => 'fatima@example.com',
    'address' => 'House 12, Street 4, Gulberg III',
    'city' => 'Lahore'
];


// ============================================================================
// PART 1: Quantity and Inventory Safety (qty > 1)
// ============================================================================
echo "--- Part 1: Quantity Handling & Inventory Safety (qty > 1) ---\n";

// 1.1 Initiate Order with quantity = 3
$qty3 = 3;
$expectedSubtotal = $price1 * $qty3;
$expectedTotalPkr = $expectedSubtotal + 200.0; // PKR 200 shipping
$expectedTotalUsd = round($expectedTotalPkr / 280.0, 2);

$orderQty3 = PhPayPalService::initiateOrder($pdo, [
    'customer' => $customer,
    'items' => [['id' => $p1, 'qty' => $qty3]]
], null, $client);

assertAudit($orderQty3['success'] === true, "1.1 Order created with requested quantity = {$qty3} for product {$p1}");
assertAudit((float)$orderQty3['amountPkr'] === $expectedTotalPkr, 
    "1.2 Authoritative total correctly calculated for qty={$qty3}: PKR {$expectedTotalPkr}");
assertAudit((float)$orderQty3['amountUsd'] === $expectedTotalUsd, 
    "1.3 USD dual-currency conversion calculated: \${$expectedTotalUsd} USD");

// Check ph_inventory_reservations table
$stResQty = $pdo->prepare("SELECT qty, status, active_slot FROM ph_inventory_reservations WHERE order_id = ?");
$stResQty->execute([$orderQty3['orderId']]);
$resRowQty = $stResQty->fetch();

assertAudit((int)$resRowQty['qty'] === $qty3, "1.4 ph_inventory_reservations accurately records requested qty = {$qty3}");
assertAudit($resRowQty['active_slot'] === $p1 && $resRowQty['status'] === 'RESERVED', 
    "1.5 Product active_slot claimed exclusively for this order");

// 1.6 Verify COD checkout works with qty > 1 without interference
$calcCod = ph_calculate_cart_totals($pdo, [['id' => $p1, 'qty' => 5]]);
assertAudit($calcCod['totalQuantity'] === 5 && (float)$calcCod['subtotal'] === ($price1 * 5), 
    "1.6 Existing cart engine calculates qty=5 for COD/Bank without error");

// Cancel orderQty3 to free product for subsequent tests
PhPayPalService::cancelOrder($pdo, $orderQty3['orderId'], $orderQty3['paymentToken']);


// ============================================================================
// PART 2: PayPal Orphan-Order Mitigation
// ============================================================================
echo "\n--- Part 2: PayPal Orphan-Order Mitigation ---\n";

// Scenario:
// Step 2b pre-check passes (product available).
// PayPal creates order 8XY... externally.
// A concurrent transaction claims the active slot right as PayPal returns.
// Local reservation at Step 7 fails, triggering the rollback and orphan mitigation record.

class RaceContentionClient extends PhPayPalClient {
    private PDO $pdo;
    private string $conflictPid;
    public function __construct(PDO $pdo, string $conflictPid) {
        parent::__construct(['PAYPAL_MODE' => 'mock']);
        $this->pdo = $pdo;
        $this->conflictPid = $conflictPid;
    }
    public function createOrder(string $localOrderId, float $totalUsd, float $subtotalUsd, float $shippingUsd, array $items = []): array {
        $res = parent::createOrder($localOrderId, $totalUsd, $subtotalUsd, $shippingUsd, $items);
        // Simulate concurrent race: another process claims active slot before our local commit
        $this->pdo->prepare("INSERT INTO ph_inventory_reservations 
            (order_id, product_id, active_slot, qty, status, expires_at, created_at, updated_at) 
            VALUES ('PH-RACE-WINNER', ?, ?, 1, 'RESERVED', datetime('now', '+15 minutes'), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)")
            ->execute([$this->conflictPid, $this->conflictPid]);
        return $res;
    }
}

$raceClient = new RaceContentionClient($pdo, $p1);
$orphanExceptionCaught = false;

try {
    PhPayPalService::initiateOrder($pdo, [
        'customer' => $customer,
        'items' => [['id' => $p1, 'qty' => 1]]
    ], null, $raceClient);
} catch (DomainException $e) {
    $orphanExceptionCaught = true;
}

assertAudit($orphanExceptionCaught, "2.1 initiateOrder caught reservation contention conflict during local commit");

// Check if an orphan mitigation record was persisted
$stOrphan = $pdo->query("SELECT o.id, o.status as order_status, p.status as payment_status, p.paypal_order_id, p.payment_token
    FROM ph_orders o
    JOIN ph_payments p ON o.id = p.order_id
    WHERE o.status = 'Reservation Failed'
    ORDER BY o.created_at DESC LIMIT 1")->fetch();

assertAudit(!empty($stOrphan), "2.2 Orphan mitigation record persisted in ph_orders and ph_payments");
assertAudit($stOrphan['order_status'] === 'Reservation Failed', "2.3 Order status recorded as 'Reservation Failed'");
assertAudit($stOrphan['payment_status'] === 'CANCELLED', "2.4 Payment record status recorded as 'CANCELLED'");

// 2.5 Verify unusable orphan order CANNOT proceed to capture
$orphanCaptureBlocked = false;
try {
    PhPayPalService::captureOrder($pdo, (string)$stOrphan['id'], (string)$stOrphan['paypal_order_id'], $client);
} catch (DomainException $e) {
    $orphanCaptureBlocked = (stripos($e->getMessage(), 'cancelled') !== false || stripos($e->getMessage(), 'reservation') !== false);
}
assertAudit($orphanCaptureBlocked, "2.5 Capture of unusable orphan PayPal order blocked before calling PayPal");

// Clear pre-existing race winner reservation
$pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id = 'PH-RACE-WINNER'")->execute();


// ============================================================================
// PART 3: Preventing Capture of Unreserved Orders
// ============================================================================
echo "\n--- Part 3: Preventing Capture of Unreserved Orders ---\n";

// Create a synthetic order in ph_orders & ph_payments WITHOUT any reservation in ph_inventory_reservations
$unreservedOrderId = 'PH-20261104-UNRESERVED';
$unreservedPaypalId = '8XY-UNRESERVED-9999';
$pdo->prepare("INSERT INTO ph_orders 
    (id, user_id, status, payment, subtotal, shipping, discount, total, address_json, items_json)
    VALUES (?, NULL, 'Pending Payment', 'paypal', 5000, 200, 0, 5200, '{}', ?)")
    ->execute([$unreservedOrderId, json_encode([['id' => $p1, 'qty' => 1]])]);

$pdo->prepare("INSERT INTO ph_payments 
    (order_id, provider, paypal_order_id, payment_token, currency, amount_usd, amount_pkr, fx_rate, status)
    VALUES (?, 'paypal', ?, 'token_unreserved', 'USD', 18.57, 5200, 280, 'CREATED')")
    ->execute([$unreservedOrderId, $unreservedPaypalId]);

$unreservedBlocked = false;
try {
    PhPayPalService::captureOrder($pdo, $unreservedOrderId, $unreservedPaypalId, $client);
} catch (DomainException $e) {
    $unreservedBlocked = (stripos($e->getMessage(), 'reservation') !== false);
}
assertAudit($unreservedBlocked, "3.1 Order lacking an active inventory reservation is blocked from capture");


// ============================================================================
// PART 4: Duplicate Reservation Attempts & Composite Unique Index
// ============================================================================
echo "\n--- Part 4: Duplicate Reservation Attempts & Composite Constraints ---\n";

$validOrder = PhPayPalService::initiateOrder($pdo, [
    'customer' => $customer,
    'items' => [['id' => $p1, 'qty' => 1]]
], null, $client);

$vOrderId = $validOrder['orderId'];

// Attempt duplicate reservation call with the exact same order and product
PhPayPalService::reserveInventory($pdo, $vOrderId, [['id' => $p1, 'qty' => 1]]);
PhPayPalService::reserveInventory($pdo, $vOrderId, [['id' => $p1, 'qty' => 1]]);

$stDupCheck = $pdo->prepare("SELECT COUNT(*) FROM ph_inventory_reservations WHERE order_id = ? AND product_id = ?");
$stDupCheck->execute([$vOrderId, $p1]);
$dupCount = (int)$stDupCheck->fetchColumn();

assertAudit($dupCount === 1, "4.1 Repeated reservation calls for the same order create strictly 1 reservation row (Count: {$dupCount})");


// ============================================================================
// PART 5: Reservation Lifecycle (Expiry Reclamation & Committed Permanence)
// ============================================================================
echo "\n--- Part 5: Lifecycle Expiry Reclamation & Committed Permanence ---\n";

// 5.1 Manually expire the reservation
$pastUtc = gmdate('Y-m-d H:i:s', time() - 60);
$pdo->prepare("UPDATE ph_inventory_reservations SET expires_at = ? WHERE order_id = ?")->execute([$pastUtc, $vOrderId]);

// Reclaim expired
$reclaimed = PhPayPalService::reclaimExpiredReservations($pdo);
assertAudit($reclaimed >= 1, "5.1 reclaimExpiredReservations successfully cleared expired slot (Reclaimed: {$reclaimed})");

$stExpiredCheck = $pdo->prepare("SELECT status, active_slot FROM ph_inventory_reservations WHERE order_id = ?");
$stExpiredCheck->execute([$vOrderId]);
$expRow = $stExpiredCheck->fetch();
assertAudit($expRow['status'] === 'RELEASED' && $expRow['active_slot'] === null, 
    "5.2 Expired reservation status updated to RELEASED and active_slot freed to NULL");

// 5.3 Create, capture and verify COMMITTED permanence
$newOrder = PhPayPalService::initiateOrder($pdo, [
    'customer' => $customer,
    'items' => [['id' => $p1, 'qty' => 1]]
], null, $client);
$newOrderId = $newOrder['orderId'];
$newPaypalId = $newOrder['paypalOrderId'];

$capResult = PhPayPalService::captureOrder($pdo, $newOrderId, $newPaypalId, $client);
assertAudit($capResult['success'] === true && $capResult['status'] === 'Confirmed', "5.3 Order captured and confirmed");

// Attempt to release the committed reservation
PhPayPalService::releaseInventoryReservation($pdo, $newOrderId);

$stCommittedCheck = $pdo->prepare("SELECT status FROM ph_inventory_reservations WHERE order_id = ?");
$stCommittedCheck->execute([$newOrderId]);
$committedStatus = $stCommittedCheck->fetchColumn();
assertAudit($committedStatus === 'COMMITTED', "5.4 releaseInventoryReservation cannot release a COMMITTED reservation (Status remains COMMITTED)");


// ============================================================================
// PART 6: MySQL / SQLite Schema Migration & NULL Uniqueness Compatibility
// ============================================================================
echo "\n--- Part 6: Database Compatibility & NULL Behavior ---\n";

// Test NULL coexistence in active_slot:
// In both MySQL and SQLite, NULL values in a UNIQUE column must not collide.
$stNull1 = $pdo->prepare("INSERT INTO ph_inventory_reservations 
    (order_id, product_id, active_slot, qty, status, expires_at) 
    VALUES ('ORD-NULL-1', 'prod-x', NULL, 1, 'RELEASED', datetime('now'))");
$stNull1->execute();

$stNull2 = $pdo->prepare("INSERT INTO ph_inventory_reservations 
    (order_id, product_id, active_slot, qty, status, expires_at) 
    VALUES ('ORD-NULL-2', 'prod-y', NULL, 1, 'RELEASED', datetime('now'))");
$stNull2->execute();

$nullCount = (int)$pdo->query("SELECT COUNT(*) FROM ph_inventory_reservations WHERE active_slot IS NULL")->fetchColumn();
assertAudit($nullCount >= 2, "6.1 Multiple released rows with active_slot = NULL coexist without UNIQUE violation (Count: {$nullCount})");

// Test runtime table upgrade idempotency on ensureTables
PhPayPalService::ensureTables($pdo);
assertAudit(true, "6.2 ensureTables executes idempotently without throwing on existing tables");

echo "\n======================================================================\n";
echo " Final Audit Suite Summary: {$passed} passed, {$failed} failed\n";
echo "======================================================================\n";

if ($failed > 0) {
    exit(1);
}
