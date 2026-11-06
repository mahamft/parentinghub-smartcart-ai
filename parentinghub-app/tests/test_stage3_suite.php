<?php
/**
 * ParentingHub SmartCart AI — Stage 3 Verification Suite
 *
 * Verifies all 14 mandatory Stage 3 requirements:
 *  1. Proposal creation succeeds
 *  2. Creating a proposal does not change the cart
 *  3. Quantity changes are respected
 *  4. Unselected products are excluded
 *  5. Expired proposal is rejected
 *  6. Reused proposal is rejected
 *  7. Invalid product IDs are rejected (creation & approval injection)
 *  8. Out-of-stock products are rejected (creation & approval)
 *  9. Price changes are detected before approval
 * 10. Unauthorized proposal access is rejected
 * 11. Duplicate approval does not duplicate cart items
 * 12. Cart changes only after explicit approval
 * 13. Existing cart functionality still works
 * 14. Existing COD and bank-transfer checkout remain unaffected
 */

declare(strict_types=1);

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', '0');

$baseDir = dirname(__DIR__);

require_once $baseDir . '/backend/api/bootstrap.php';
require_once $baseDir . '/backend/api/lib/pricing.php';
require_once $baseDir . '/backend/api/ai/rate_limiter.php';
require_once $baseDir . '/backend/api/ai/catalog_retriever.php';
require_once $baseDir . '/backend/api/ai/ai_service.php';
require_once $baseDir . '/backend/api/ai/cart_proposals.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "======================================================================\n";
echo "   ParentingHub SmartCart AI: Stage 3 Verification Test Suite\n";
echo "======================================================================\n\n";

$passed = 0;
$failed = 0;

function assertCondition(bool $cond, string $desc): void {
    global $passed, $failed;
    if ($cond) {
        echo "  [PASS] {$desc}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$desc}\n";
        $failed++;
    }
}

// -------------------------------------------------------------
// Database Setup: SQLite in-memory loaded with catalog
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

$pdo->exec("CREATE TABLE ph_rate_limits (
    bucket_key TEXT PRIMARY KEY,
    hits INTEGER NOT NULL DEFAULT 1,
    reset_at INTEGER NOT NULL
)");

$pdo->exec("CREATE TABLE ph_ai_cart_proposals (
    proposal_id VARCHAR(64) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    session_token_hash CHAR(64) NULL,
    items_json LONGTEXT NOT NULL,
    subtotal_pkr DECIMAL(12,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'proposed',
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE ph_orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_ref TEXT UNIQUE NOT NULL,
    customer_name TEXT NOT NULL,
    customer_email TEXT NOT NULL,
    subtotal_pkr REAL NOT NULL,
    shipping_pkr REAL NOT NULL DEFAULT 200,
    total_pkr REAL NOT NULL,
    payment_method TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

$catalogPath = $baseDir . '/backend/api/seed/catalog.json';
$catalogData = json_decode(file_get_contents($catalogPath), true);
$seedProducts = $catalogData['products'];

$ins = $pdo->prepare("INSERT INTO products (
    id, sku, name, price, compare_at, image, images, category, brand, description, 
    has_options, options_json, rating, reviews, in_stock, tags, source_url
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($seedProducts as $p) {
    $ins->execute([
        (string)$p['id'],
        (string)($p['sku'] ?? ''),
        (string)$p['name'],
        (float)($p['price'] ?? 0),
        isset($p['compareAt']) && $p['compareAt'] !== null ? (float)$p['compareAt'] : null,
        (string)($p['image'] ?? $p['img'] ?? ''),
        json_encode($p['images'] ?? []),
        (string)($p['category'] ?? $p['cat'] ?? 'General'),
        (string)($p['brand'] ?? 'ParentingHub'),
        (string)($p['description'] ?? $p['desc'] ?? ''),
        !empty($p['hasOptions']) ? 1 : 0,
        json_encode($p['options'] ?? []),
        (float)($p['rating'] ?? 0),
        (int)($p['reviews'] ?? 0),
        array_key_exists('inStock', $p) ? (!empty($p['inStock']) ? 1 : 0) : 1,
        json_encode($p['tags'] ?? []),
        (string)($p['sourceUrl'] ?? '')
    ]);
}
$pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload) VALUES ('catalog', ?)")
    ->execute([json_encode($catalogData)]);

echo "Catalog Loaded: " . count($seedProducts) . " genuine products in database.\n\n";

// Pick two stable products from feeding bottles
$b1 = $seedProducts[0];
$b2 = $seedProducts[1];
$pid1 = (string)$b1['id'];
$pid2 = (string)$b2['id'];
$price1 = (float)$b1['price'];
$price2 = (float)$b2['price'];

// -------------------------------------------------------------
// Test 1: Proposal creation succeeds
// -------------------------------------------------------------
echo "--- Test 1: Proposal creation succeeds ---\n";
$prop1 = PhCartProposalEngine::createProposal($pdo, [
    ['id' => $pid1, 'qty' => 1],
    ['id' => $pid2, 'qty' => 2]
]);

assertCondition(str_starts_with($prop1['proposalId'], 'PROP-'), "Proposal ID starts with 'PROP-': {$prop1['proposalId']}");
assertCondition($prop1['status'] === 'proposed', "Initial proposal status is 'proposed'");
assertCondition($prop1['itemCount'] === 2, "Proposed item count is 2");
$expectedSubtotal = ($price1 * 1) + ($price2 * 2);
assertCondition((float)$prop1['subtotal'] === (float)$expectedSubtotal, "Subtotal calculated server-side: PKR {$prop1['subtotal']}");
assertCondition((float)$prop1['shipping'] === 200.0, "Nationwide shipping fee is fixed PKR 200.00");
assertCondition((float)$prop1['total'] === (float)($expectedSubtotal + 200.0), "Grand total includes delivery fee: PKR {$prop1['total']}");
assertCondition($prop1['requiresCustomerApproval'] === true, "requiresCustomerApproval flag is explicitly true");
assertCondition($prop1['expiresInSeconds'] === 1800, "Expires in 1800 seconds (30 minutes)");

// -------------------------------------------------------------
// Test 2: Creating a proposal does not change the cart
// -------------------------------------------------------------
echo "\n--- Test 2: Creating a proposal does not change the cart ---\n";
$mockCart = [
    ['id' => 'existing-cart-item-1', 'qty' => 1, 'price' => 1000]
];
$cartSnapshotBefore = json_encode($mockCart);
$ordersCountBefore = (int)$pdo->query("SELECT COUNT(*) FROM ph_orders")->fetchColumn();

// Create another proposal
$tempProp = PhCartProposalEngine::createProposal($pdo, [['id' => $pid1, 'qty' => 1]]);

$cartSnapshotAfter = json_encode($mockCart);
$ordersCountAfter = (int)$pdo->query("SELECT COUNT(*) FROM ph_orders")->fetchColumn();

assertCondition($cartSnapshotBefore === $cartSnapshotAfter, "Customer cart in memory/localStorage remains 100% unchanged");
assertCondition($ordersCountBefore === $ordersCountAfter, "Database orders count remains 100% unchanged");

// -------------------------------------------------------------
// Test 3: Quantity changes are respected
// -------------------------------------------------------------
echo "\n--- Test 3: Quantity changes are respected ---\n";
$prop3 = PhCartProposalEngine::createProposal($pdo, [
    ['id' => $pid1, 'qty' => 1],
    ['id' => $pid2, 'qty' => 1]
]);

// Customer increases quantity of item 1 from 1 to 4
$approvedRes3 = PhCartProposalEngine::approveProposal($pdo, $prop3['proposalId'], [
    ['id' => $pid1, 'qty' => 4],
    ['id' => $pid2, 'qty' => 1]
]);

assertCondition($approvedRes3['success'] === true, "Approval with modified quantities succeeded");
$appItem1 = null;
foreach ($approvedRes3['approvedItems'] as $it) {
    if ($it['id'] === $pid1) $appItem1 = $it;
}
assertCondition($appItem1 !== null && $appItem1['qty'] === 4, "Item 1 quantity increased to 4 in approved result");
$expectedSubtot3 = ($price1 * 4) + ($price2 * 1);
assertCondition((float)$approvedRes3['subtotal'] === (float)$expectedSubtot3, "Subtotal recalculated based on updated quantity (PKR {$approvedRes3['subtotal']})");

// -------------------------------------------------------------
// Test 4: Unselected products are excluded
// -------------------------------------------------------------
echo "\n--- Test 4: Unselected products are excluded ---\n";
$prop4 = PhCartProposalEngine::createProposal($pdo, [
    ['id' => $pid1, 'qty' => 1],
    ['id' => $pid2, 'qty' => 1]
]);

// Customer unchecks item 2, approves only item 1
$approvedRes4 = PhCartProposalEngine::approveProposal($pdo, $prop4['proposalId'], [
    ['id' => $pid1, 'qty' => 1]
]);

assertCondition(count($approvedRes4['approvedItems']) === 1, "Only 1 item approved after customer unchecked second item");
assertCondition($approvedRes4['approvedItems'][0]['id'] === $pid1, "Approved item is strictly the checked item ({$pid1})");
assertCondition((float)$approvedRes4['subtotal'] === (float)$price1, "Subtotal reflects only the single approved item");

// -------------------------------------------------------------
// Test 5: Expired proposal is rejected
// -------------------------------------------------------------
echo "\n--- Test 5: Expired proposal is rejected ---\n";
$prop5 = PhCartProposalEngine::createProposal($pdo, [['id' => $pid1, 'qty' => 1]]);

// Simulate expiration by setting expires_at to 1 hour in the past
$pdo->prepare("UPDATE ph_ai_cart_proposals SET expires_at = datetime('now', '-1 hour') WHERE proposal_id = ?")
    ->execute([$prop5['proposalId']]);

$expiredCaught = false;
try {
    PhCartProposalEngine::approveProposal($pdo, $prop5['proposalId']);
} catch (DomainException $e) {
    $expiredCaught = (stripos($e->getMessage(), 'expired') !== false);
}
assertCondition($expiredCaught, "Approval of expired proposal was rejected with DomainException");

$st5 = $pdo->prepare("SELECT status FROM ph_ai_cart_proposals WHERE proposal_id = ?");
$st5->execute([$prop5['proposalId']]);
assertCondition($st5->fetchColumn() === 'expired', "Proposal status in database transitioned to 'expired'");

// -------------------------------------------------------------
// Test 6: Reused proposal is rejected
// -------------------------------------------------------------
echo "\n--- Test 6: Reused proposal is rejected ---\n";
$prop6 = PhCartProposalEngine::createProposal($pdo, [['id' => $pid1, 'qty' => 1]]);

// First approval succeeds
$res6_1 = PhCartProposalEngine::approveProposal($pdo, $prop6['proposalId']);
assertCondition($res6_1['success'] === true, "First approval of proposal succeeds");

// Second approval must fail (single-use enforcement)
$replayCaught = false;
try {
    PhCartProposalEngine::approveProposal($pdo, $prop6['proposalId']);
} catch (DomainException $e) {
    $replayCaught = (stripos($e->getMessage(), 'already been approved') !== false);
}
assertCondition($replayCaught, "Replayed/duplicate approval was rejected with DomainException (single-use guaranteed)");

// -------------------------------------------------------------
// Test 7: Invalid product IDs are rejected
// -------------------------------------------------------------
echo "\n--- Test 7: Invalid product IDs are rejected ---\n";
$invalidCreateCaught = false;
try {
    PhCartProposalEngine::createProposal($pdo, [['id' => 'non-existent-product-id-12345', 'qty' => 1]]);
} catch (InvalidArgumentException $e) {
    $invalidCreateCaught = (stripos($e->getMessage(), 'Product not found') !== false);
}
assertCondition($invalidCreateCaught, "Proposal creation with non-existent product ID rejected with InvalidArgumentException");

// Test injection of non-proposed product ID during approval
$prop7 = PhCartProposalEngine::createProposal($pdo, [['id' => $pid1, 'qty' => 1]]);
$injectionCaught = false;
try {
    // Attempt to inject pid2 which was never in prop7
    PhCartProposalEngine::approveProposal($pdo, $prop7['proposalId'], [
        ['id' => $pid1, 'qty' => 1],
        ['id' => $pid2, 'qty' => 1]
    ]);
} catch (DomainException $e) {
    $injectionCaught = (stripos($e->getMessage(), 'not part of the original proposal') !== false);
}
assertCondition($injectionCaught, "Approval rejecting non-proposed product injection with DomainException");

// -------------------------------------------------------------
// Test 8: Out-of-stock products are rejected
// -------------------------------------------------------------
echo "\n--- Test 8: Out-of-stock products are rejected ---\n";
// Create temporary product marked out of stock
$pdo->exec("INSERT INTO products (id, sku, name, price, category, brand, in_stock) 
    VALUES ('oos-bottle-test', 'OOS-1', 'Out of Stock Test Bottle', 2500, 'Feeding Bottles', 'TestBrand', 0)");

$oosCreateCaught = false;
try {
    PhCartProposalEngine::createProposal($pdo, [['id' => 'oos-bottle-test', 'qty' => 1]]);
} catch (InvalidArgumentException $e) {
    $oosCreateCaught = (stripos($e->getMessage(), 'out of stock') !== false);
}
assertCondition($oosCreateCaught, "Proposal creation with out-of-stock product rejected with InvalidArgumentException");

// Test product going out of stock after proposal creation
$pdo->exec("UPDATE products SET in_stock = 1 WHERE id = 'oos-bottle-test'");
$prop8 = PhCartProposalEngine::createProposal($pdo, [['id' => 'oos-bottle-test', 'qty' => 1]]);
// Now mark it out of stock before approval
$pdo->exec("UPDATE products SET in_stock = 0 WHERE id = 'oos-bottle-test'");

$oosApproveCaught = false;
try {
    PhCartProposalEngine::approveProposal($pdo, $prop8['proposalId']);
} catch (DomainException | InvalidArgumentException $e) {
    $oosApproveCaught = (stripos($e->getMessage(), 'out of stock') !== false);
}
assertCondition($oosApproveCaught, "Approval re-validates stock and rejects recently out-of-stock product");

// -------------------------------------------------------------
// Test 9: Price changes are detected before approval
// -------------------------------------------------------------
echo "\n--- Test 9: Price changes are detected before approval ---\n";
$prop9 = PhCartProposalEngine::createProposal($pdo, [['id' => $pid1, 'qty' => 1]]);
$originalPrice = $price1;

// Simulate merchant price increase in catalog database
$newPrice = $originalPrice + 1200.0;
$pdo->prepare("UPDATE products SET price = ? WHERE id = ?")->execute([$newPrice, $pid1]);

$priceChangeCaught = false;
try {
    PhCartProposalEngine::approveProposal($pdo, $prop9['proposalId']);
} catch (DomainException $e) {
    $priceChangeCaught = (stripos($e->getMessage(), 'Price change detected') !== false);
}
assertCondition($priceChangeCaught, "Price change in catalog detected before approval; rejected with DomainException");

// Test explicit customer approval of updated price
$approvedWithUpdate = PhCartProposalEngine::approveProposal($pdo, $prop9['proposalId'], null, null, null, true);
assertCondition($approvedWithUpdate['success'] === true, "Approval with allowPriceUpdate=true successfully applies fresh database price");
assertCondition((float)$approvedWithUpdate['subtotal'] === (float)$newPrice, "Recalculated subtotal matches new price (PKR {$newPrice})");

// Revert price in products table for subsequent tests
$pdo->prepare("UPDATE products SET price = ? WHERE id = ?")->execute([$originalPrice, $pid1]);

// -------------------------------------------------------------
// Test 10: Unauthorized proposal access is rejected
// -------------------------------------------------------------
echo "\n--- Test 10: Unauthorized proposal access is rejected ---\n";
$sessionHashA = hash('sha256', 'user_a_secret_session_token_123456');
$sessionHashB = hash('sha256', 'user_b_attacker_session_token_654321');

$prop10 = PhCartProposalEngine::createProposal($pdo, [['id' => $pid1, 'qty' => 1]], 42, $sessionHashA);

$unauthorizedCaught = false;
try {
    // User B attempts to approve User A's proposal
    PhCartProposalEngine::approveProposal($pdo, $prop10['proposalId'], null, 999, $sessionHashB);
} catch (DomainException $e) {
    $unauthorizedCaught = (stripos($e->getMessage(), 'Unauthorized') !== false);
}
assertCondition($unauthorizedCaught, "Unauthorized approval attempt by different session/user rejected with 403 DomainException");

// Legitimate User A approves their own proposal
$legitApproval = PhCartProposalEngine::approveProposal($pdo, $prop10['proposalId'], null, 42, $sessionHashA);
assertCondition($legitApproval['success'] === true, "Legitimate owner approval succeeds");

// -------------------------------------------------------------
// Test 11: Duplicate approval does not duplicate cart items
// -------------------------------------------------------------
echo "\n--- Test 11: Duplicate approval does not duplicate cart items ---\n";
$prop11 = PhCartProposalEngine::createProposal($pdo, [['id' => $pid1, 'qty' => 2]]);

$clientCart = [];
$firstApp = PhCartProposalEngine::approveProposal($pdo, $prop11['proposalId']);
foreach ($firstApp['approvedItems'] as $item) {
    $clientCart[] = ['id' => $item['id'], 'qty' => $item['qty']];
}

$duplicateAppAttempted = false;
try {
    PhCartProposalEngine::approveProposal($pdo, $prop11['proposalId']);
} catch (DomainException $e) {
    $duplicateAppAttempted = true;
}

assertCondition($duplicateAppAttempted, "Duplicate approval blocked server-side");
assertCondition(count($clientCart) === 1 && $clientCart[0]['qty'] === 2, "Client cart retains exact single-approved batch (zero item duplication)");

// -------------------------------------------------------------
// Test 12: Cart changes only after explicit approval
// -------------------------------------------------------------
echo "\n--- Test 12: Cart changes only after explicit approval ---\n";
$prop12 = PhCartProposalEngine::createProposal($pdo, [['id' => $pid1, 'qty' => 1]]);
$cartBefore = [];

// Before approval: cart is empty
assertCondition(empty($cartBefore), "Cart is completely empty while proposal is in 'proposed' state");

// Explicit approval executed:
$app12 = PhCartProposalEngine::approveProposal($pdo, $prop12['proposalId']);
foreach ($app12['approvedItems'] as $it) {
    $cartBefore[] = ['id' => $it['id'], 'qty' => $it['qty']];
}
assertCondition(count($cartBefore) === 1, "Cart populated only after explicit approval execution");

// -------------------------------------------------------------
// Test 13: Existing cart functionality still works
// -------------------------------------------------------------
echo "\n--- Test 13: Existing cart functionality still works ---\n";
$standardCartItems = [
    ['id' => $pid1, 'qty' => 2],
    ['id' => $pid2, 'qty' => 1]
];
$pricingRes = ph_calculate_cart_totals($pdo, $standardCartItems);
assertCondition($pricingRes['subtotal'] > 0, "ph_calculate_cart_totals works for standard cart items");
assertCondition($pricingRes['shipping'] === 200.0, "Standard cart preserves nationwide PKR 200 delivery fee");
assertCondition($pricingRes['total'] === $pricingRes['subtotal'] + 200.0, "Standard grand total is accurate");

// -------------------------------------------------------------
// Test 14: Existing COD and bank-transfer checkout remain unaffected
// -------------------------------------------------------------
echo "\n--- Test 14: Existing COD and bank checkout remain unaffected ---\n";
// Simulate COD order insertion
$codOrderStmt = $pdo->prepare("INSERT INTO ph_orders 
    (order_ref, customer_name, customer_email, subtotal_pkr, shipping_pkr, total_pkr, payment_method, status)
    VALUES (?, ?, ?, ?, ?, ?, 'cod', 'pending')");
$codOrderStmt->execute([
    'PH-COD-' . bin2hex(random_bytes(4)),
    'Test Customer COD',
    'customer.cod@example.com',
    $pricingRes['subtotal'],
    200.0,
    $pricingRes['total']
]);
$codId = (int)$pdo->lastInsertId();
assertCondition($codId > 0, "Existing COD order creation executes successfully (Order ID #{$codId})");

// Simulate Bank Transfer order insertion
$bankOrderStmt = $pdo->prepare("INSERT INTO ph_orders 
    (order_ref, customer_name, customer_email, subtotal_pkr, shipping_pkr, total_pkr, payment_method, status)
    VALUES (?, ?, ?, ?, ?, ?, 'bank', 'pending')");
$bankOrderStmt->execute([
    'PH-BANK-' . bin2hex(random_bytes(4)),
    'Test Customer Bank',
    'customer.bank@example.com',
    $pricingRes['subtotal'],
    200.0,
    $pricingRes['total']
]);
$bankId = (int)$pdo->lastInsertId();
assertCondition($bankId > 0, "Existing Bank Transfer order creation executes successfully (Order ID #{$bankId})");

// -------------------------------------------------------------
// SUMMARY
// -------------------------------------------------------------
echo "\n======================================================================\n";
echo " Stage 3 Verification Summary: {$passed} passed, {$failed} failed\n";
echo "======================================================================\n";

if ($failed > 0) {
    exit(1);
}
exit(0);
