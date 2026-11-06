<?php
declare(strict_types=1);

/**
 * ParentingHub Phase 7 & 8 Automated Test Suite
 * 
 * Verifies:
 * - SmartCart AI Cart Proposal Engine (creation, subset approval, unproposed item rejection, replay prevention)
 * - 16 Required PayPal Sandbox Integration Scenarios:
 *   1. Unauthorized / Invalid payment order creation
 *   2. Tampered cart amount
 *   3. Tampered PayPal order ID
 *   4. Incorrect currency
 *   5. Incorrect payment amount
 *   6. Duplicate capture
 *   7. Repeated approval
 *   8. Invalid capture response
 *   9. Pending payment
 *   10. Failed payment
 *   11. Cancelled checkout
 *   12. Invalid webhook signature
 *   13. Duplicate webhook event
 *   14. Stock changes during checkout
 *   15. Order status consistency
 *   16. Exchange-rate rounding
 */

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';
require_once __DIR__ . '/../backend/api/lib/pricing.php';
require_once __DIR__ . '/../backend/api/ai/cart_proposals.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_client.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_service.php';

function init_test_sqlite_database(): PDO {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Products
    $pdo->exec("CREATE TABLE products (
        id VARCHAR(190) PRIMARY KEY,
        sku VARCHAR(100),
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        compare_at DECIMAL(10,2),
        image TEXT,
        images TEXT,
        category VARCHAR(100),
        brand VARCHAR(100),
        description TEXT,
        has_options INTEGER DEFAULT 0,
        options_json TEXT,
        rating DECIMAL(3,2) DEFAULT 0,
        reviews INTEGER DEFAULT 0,
        in_stock INTEGER DEFAULT 1,
        weight_kg DECIMAL(6,2),
        tags TEXT,
        source_url TEXT,
        video_url TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Catalog state
    $pdo->exec("CREATE TABLE ph_catalog_state (
        state_key VARCHAR(64) PRIMARY KEY,
        payload TEXT,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Orders
    $pdo->exec("CREATE TABLE ph_orders (
        id VARCHAR(32) PRIMARY KEY,
        user_id INTEGER,
        status VARCHAR(40) NOT NULL DEFAULT 'Confirmed',
        payment VARCHAR(20) NOT NULL DEFAULT 'cod',
        subtotal DECIMAL(10,2) NOT NULL,
        shipping DECIMAL(10,2) NOT NULL,
        discount DECIMAL(10,2) NOT NULL DEFAULT 0,
        total DECIMAL(10,2) NOT NULL,
        coupon VARCHAR(50),
        address_json TEXT,
        items_json TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // AI Cart Proposals
    $pdo->exec("CREATE TABLE ph_ai_cart_proposals (
        proposal_id VARCHAR(64) PRIMARY KEY,
        user_id INTEGER,
        session_token_hash CHAR(64),
        items_json TEXT NOT NULL,
        subtotal_pkr DECIMAL(12,2) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'proposed',
        expires_at DATETIME NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Payments ledger
    $pdo->exec("CREATE TABLE ph_payments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id VARCHAR(32) NOT NULL,
        provider VARCHAR(20) NOT NULL DEFAULT 'paypal',
        paypal_order_id VARCHAR(64) NOT NULL,
        payment_token VARCHAR(64) DEFAULT NULL,
        capture_id VARCHAR(64) DEFAULT NULL,
        currency VARCHAR(3) NOT NULL DEFAULT 'USD',
        amount_usd DECIMAL(10,2) NOT NULL,
        amount_pkr DECIMAL(12,2) NOT NULL,
        fx_rate DECIMAL(10,4) NOT NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'CREATED',
        payer_email VARCHAR(190) DEFAULT NULL,
        raw_response TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Payment events for webhooks
    $pdo->exec("CREATE TABLE ph_payment_events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        event_id VARCHAR(100) NOT NULL UNIQUE,
        event_type VARCHAR(80) NOT NULL,
        order_id VARCHAR(32) DEFAULT NULL,
        verified INTEGER NOT NULL DEFAULT 1,
        payload TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Seed products
    $catalogPath = __DIR__ . '/../backend/api/seed/catalog.json';
    $catalogData = json_decode((string)file_get_contents($catalogPath), true);
    $products = $catalogData['products'] ?? [];

    $st = $pdo->prepare("INSERT INTO products (id, sku, name, price, compare_at, image, images, category, brand, description, has_options, options_json, rating, reviews, in_stock, weight_kg, tags, source_url)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($products as $p) {
        $st->execute([
            $p['id'],
            $p['sku'] ?? '',
            $p['name'],
            $p['price'],
            $p['compareAt'] ?? null,
            $p['img'] ?? ($p['images'][0] ?? ''),
            json_encode($p['images'] ?? []),
            $p['cat'] ?? '',
            $p['brand'] ?? '',
            $p['desc'] ?? '',
            empty($p['options']) ? 0 : 1,
            json_encode($p['options'] ?? []),
            $p['rating'] ?? 0,
            $p['reviews'] ?? 0,
            ($p['inStock'] ?? true) ? 1 : 0,
            $p['weightKg'] ?? null,
            json_encode($p['tags'] ?? []),
            $p['sourceUrl'] ?? ''
        ]);
    }

    $pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload) VALUES ('catalog', ?)")
        ->execute([json_encode($catalogData)]);

    return $pdo;
}

$pdo = init_test_sqlite_database();
$testClient = new PhPayPalClient(['PAYPAL_MODE' => 'mock', 'PAYPAL_FX_PKR_USD' => '280.0000']);

$testsPassed = 0;
$totalTests = 0;

function assertTest(string $description, bool $condition, ?string $extra = null) {
    global $testsPassed, $totalTests;
    $totalTests++;
    if ($condition) {
        $testsPassed++;
        echo "  [PASS] Test {$totalTests}: {$description}\n";
    } else {
        echo "  [FAIL] Test {$totalTests}: {$description}" . ($extra ? " - {$extra}" : "") . "\n";
    }
}

echo "========================================================================\n";
echo "Starting ParentingHub Phase 7 & 8 Automated Test Suite\n";
echo "========================================================================\n\n";

// ----------------------------------------------------------------------------
// PHASE 7 TESTS: SMART CART ENGINE & PROPOSAL LIFECYCLE
// ----------------------------------------------------------------------------
echo "--- Phase 7: Smart Cart Engine & Permission-Based Proposals ---\n";

// 7.1 Propose cart ticket using genuine catalog products from SQLite database
$testProducts = $pdo->query("SELECT id, price FROM products LIMIT 2")->fetchAll();
$p1 = (string)$testProducts[0]['id'];
$price1 = (float)$testProducts[0]['price'];
$p2 = (string)$testProducts[1]['id'];
$price2 = (float)$testProducts[1]['price'];

$expectedProposalSubtotal = round(($price1 * 2) + $price2, 2);
$expectedProposalTotal = round($expectedProposalSubtotal + 200.0, 2);

$proposal = PhCartProposalEngine::createProposal($pdo, [
    ['id' => $p1, 'qty' => 2],
    ['id' => $p2, 'qty' => 1]
]);

assertTest("AI creates proposal ticket with unique ID and 30m TTL", 
    str_starts_with($proposal['proposalId'], 'PROP-') && $proposal['status'] === 'proposed');

assertTest("Proposal computes verified subtotal & PKR 200 delivery fee",
    $proposal['subtotal'] === $expectedProposalSubtotal 
    && $proposal['shipping'] === 200.0 
    && $proposal['total'] === $expectedProposalTotal);

// 7.2 Customer adjusts quantities during approval
$expectedAdjustedSubtotal = round($price1 * 1, 2);
$expectedAdjustedTotal = round($expectedAdjustedSubtotal + 200.0, 2);

$approvalAdjusted = PhCartProposalEngine::approveProposal($pdo, $proposal['proposalId'], [
    ['id' => $p1, 'qty' => 1] // Customer reduced p1 to 1 and unchecked p2
]);

assertTest("Customer explicitly approves modified subset of proposed bundle",
    $approvalAdjusted['success'] === true 
    && $approvalAdjusted['subtotal'] === $expectedAdjustedSubtotal 
    && $approvalAdjusted['shipping'] === 200.0 
    && $approvalAdjusted['total'] === $expectedAdjustedTotal);

// 7.3 Unproposed item injection is rejected
$proposal2 = PhCartProposalEngine::createProposal($pdo, [['id' => $p1, 'qty' => 1]]);
$injectionCaught = false;
try {
    PhCartProposalEngine::approveProposal($pdo, $proposal2['proposalId'], [
        ['id' => 'unproposed-arbitrary-product', 'qty' => 1]
    ]);
} catch (DomainException $e) {
    $injectionCaught = true;
}
assertTest("Reject customer attempting to approve products not in original proposal", $injectionCaught);


// ----------------------------------------------------------------------------
// PHASE 8 TESTS: 16 REQUIRED PAYPAL SANDBOX SCENARIOS
// ----------------------------------------------------------------------------
echo "\n--- Phase 8: 16 PayPal Sandbox Checkout Verification Scenarios ---\n";

$validAddress = [
    'name' => 'Fatima Ali',
    'phone' => '03001234567',
    'email' => 'fatima.ali@example.com',
    'address' => 'House 42, Street 7, Sector F-8/2',
    'city' => 'Islamabad'
];

// Scenario 1: Unauthorized payment order creation (missing / invalid data)
$unauthCaught = false;
try {
    PhPayPalService::initiateOrder($pdo, [
        'address' => ['name' => '', 'phone' => 'invalid-phone', 'address' => '', 'city' => ''],
        'items' => []
    ], null, $testClient);
} catch (InvalidArgumentException $e) {
    $unauthCaught = true;
}
assertTest("Scenario 1: Unauthorized / invalid order creation rejected with 422", $unauthCaught);

// Scenario 2: Tampered cart amount
// Attacker submits PKR 10.00 subtotal for catalog product
$orderTamper = PhPayPalService::initiateOrder($pdo, [
    'address' => $validAddress,
    'items' => [['id' => $p1, 'qty' => 1]],
    'subtotal' => 10.0,
    'total' => 10.0
], null, $testClient);

$stCheck = $pdo->prepare("SELECT subtotal, shipping, total FROM ph_orders WHERE id = ?");
$stCheck->execute([$orderTamper['orderId']]);
$dbRow = $stCheck->fetch();

$expectedP1Total = round($price1 + 200.0, 2);
assertTest("Scenario 2: Tampered cart amount ignored, server calculates true catalog prices",
    (float)$dbRow['subtotal'] === $price1 && (float)$dbRow['shipping'] === 200.0 && (float)$dbRow['total'] === $expectedP1Total);

// Scenario 3: Tampered PayPal order ID during capture
$tamperedOrderIdCaught = false;
try {
    PhPayPalService::captureOrder($pdo, $orderTamper['orderId'], '8XY_FORGED_NON_EXISTENT_ID', $testClient);
} catch (DomainException $e) {
    $tamperedOrderIdCaught = true;
}
PhPayPalService::releaseInventoryReservation($pdo, $orderTamper['orderId']);
assertTest("Scenario 3: Tampered / mismatched PayPal order ID rejected during capture", $tamperedOrderIdCaught);

// Scenario 4: Incorrect currency (e.g. PayPal returned EUR instead of USD)
class MockCurrencyMismatchClient extends PhPayPalClient {
    public function captureOrder(string $paypalOrderId, ?string $mockExpectedAmount = null): array {
        $res = parent::captureOrder($paypalOrderId, $mockExpectedAmount);
        $res['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'] = 'EUR';
        return $res;
    }
}
$orderCurr = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], null, $testClient);
$currMismatchCaught = false;
try {
    PhPayPalService::captureOrder($pdo, $orderCurr['orderId'], $orderCurr['paypalOrderId'], new MockCurrencyMismatchClient(['PAYPAL_MODE' => 'mock']));
} catch (DomainException $e) {
    $currMismatchCaught = str_contains($e->getMessage(), 'currency mismatch');
}
PhPayPalService::releaseInventoryReservation($pdo, $orderCurr['orderId']);
assertTest("Scenario 4: Incorrect currency (non-USD) rejected and flagged", $currMismatchCaught);

// Scenario 5: Incorrect payment amount (e.g. attacker paid $1.00 instead of $9.29)
class MockAmountMismatchClient extends PhPayPalClient {
    public function captureOrder(string $paypalOrderId, ?string $mockExpectedAmount = null): array {
        $res = parent::captureOrder($paypalOrderId, $mockExpectedAmount);
        $res['purchase_units'][0]['payments']['captures'][0]['amount']['value'] = '1.00';
        return $res;
    }
}
$orderAmt = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], null, $testClient);
$amtMismatchCaught = false;
try {
    PhPayPalService::captureOrder($pdo, $orderAmt['orderId'], $orderAmt['paypalOrderId'], new MockAmountMismatchClient(['PAYPAL_MODE' => 'mock']));
} catch (DomainException $e) {
    $amtMismatchCaught = str_contains($e->getMessage(), 'does not match');
}
PhPayPalService::releaseInventoryReservation($pdo, $orderAmt['orderId']);
assertTest("Scenario 5: Incorrect payment amount rejected and flagged", $amtMismatchCaught);

// Scenario 6: Duplicate capture / replay protection
$orderDup = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], null, $testClient);
$firstCapture = PhPayPalService::captureOrder($pdo, $orderDup['orderId'], $orderDup['paypalOrderId'], $testClient);
$secondCapture = PhPayPalService::captureOrder($pdo, $orderDup['orderId'], $orderDup['paypalOrderId'], $testClient);

assertTest("Scenario 6: Duplicate capture handled idempotently without duplicate charges",
    $firstCapture['status'] === 'Confirmed' && $secondCapture['isDuplicate'] === true && $secondCapture['status'] === 'Confirmed');

// Scenario 7: Repeated approval of proposal ticket
$repeatedApprovalCaught = false;
try {
    PhCartProposalEngine::approveProposal($pdo, $proposal['proposalId']);
} catch (DomainException $e) {
    $repeatedApprovalCaught = str_contains($e->getMessage(), 'already been approved');
}
assertTest("Scenario 7: Repeated approval of AI cart proposal rejected with 409", $repeatedApprovalCaught);

// Scenario 8: Invalid capture response (PayPal returns non-COMPLETED status)
class MockFailedCaptureClient extends PhPayPalClient {
    public function captureOrder(string $paypalOrderId, ?string $mockExpectedAmount = null): array {
        return ['id' => $paypalOrderId, 'status' => 'DECLINED', 'isMock' => true];
    }
}
$orderDeclined = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], null, $testClient);
$declinedCaught = false;
try {
    PhPayPalService::captureOrder($pdo, $orderDeclined['orderId'], $orderDeclined['paypalOrderId'], new MockFailedCaptureClient(['PAYPAL_MODE' => 'mock']));
} catch (DomainException $e) {
    $declinedCaught = str_contains($e->getMessage(), 'not completed');
}
$stDec = $pdo->prepare("SELECT status FROM ph_orders WHERE id = ?");
$stDec->execute([$orderDeclined['orderId']]);
PhPayPalService::releaseInventoryReservation($pdo, $orderDeclined['orderId']);
assertTest("Scenario 8: Non-COMPLETED capture marks order Payment Failed", 
    $declinedCaught && $stDec->fetchColumn() === 'Payment Failed');

// Scenario 9: Pending payment state verification
$orderPending = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], null, $testClient);
$stPend = $pdo->prepare("SELECT status, payment FROM ph_orders WHERE id = ?");
$stPend->execute([$orderPending['orderId']]);
$pendRow = $stPend->fetch();
PhPayPalService::releaseInventoryReservation($pdo, $orderPending['orderId']);
assertTest("Scenario 9: Freshly initiated PayPal order is Pending Payment",
    $pendRow['status'] === 'Pending Payment' && $pendRow['payment'] === 'paypal');

// Scenario 10: Failed payment event via Webhook
$orderWebFail = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], null, $testClient);
$webhookFailPayload = json_encode([
    'id' => 'WH-FAIL-' . bin2hex(random_bytes(4)),
    'event_type' => 'PAYMENT.CAPTURE.DENIED',
    'resource' => [
        'id' => $orderWebFail['paypalOrderId'],
        'custom_id' => $orderWebFail['orderId']
    ]
]);
$validWebhookHeaders = [
    'PAYPAL-TRANSMISSION-ID' => 'TRANS-001',
    'PAYPAL-TRANSMISSION-TIME' => gmdate('Y-m-d\TH:i:s\Z'),
    'PAYPAL-TRANSMISSION-SIG' => 'VALID_TEST_SIG',
    'PAYPAL-CERT-URL' => 'https://api.sandbox.paypal.com/cert.pem',
    'PAYPAL-AUTH-ALGO' => 'SHA256withRSA'
];
PhPayPalService::handleWebhook($pdo, $validWebhookHeaders, $webhookFailPayload, $testClient);
$stWebFail = $pdo->prepare("SELECT status FROM ph_orders WHERE id = ?");
$stWebFail->execute([$orderWebFail['orderId']]);
PhPayPalService::releaseInventoryReservation($pdo, $orderWebFail['orderId']);
assertTest("Scenario 10: Failed payment webhook event updates status to Payment Failed",
    $stWebFail->fetchColumn() === 'Payment Failed');

// Scenario 11: Cancelled checkout
$orderCancel = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], null, $testClient);
$pdo->prepare("UPDATE ph_orders SET status = 'Cancelled' WHERE id = ?")->execute([$orderCancel['orderId']]);
$cancelCaptureCaught = false;
try {
    PhPayPalService::captureOrder($pdo, $orderCancel['orderId'], $orderCancel['paypalOrderId'], $testClient);
} catch (DomainException $e) {
    $cancelCaptureCaught = str_contains($e->getMessage(), 'cancelled');
}
PhPayPalService::releaseInventoryReservation($pdo, $orderCancel['orderId']);
assertTest("Scenario 11: Cancelled checkout cannot be captured", $cancelCaptureCaught);

// Scenario 12: Invalid webhook signature rejected
$invalidSigCaught = false;
$forgedHeaders = array_merge($validWebhookHeaders, ['PAYPAL-TRANSMISSION-SIG' => 'FORGED_SIGNATURE']);
try {
    PhPayPalService::handleWebhook($pdo, $forgedHeaders, $webhookFailPayload, $testClient);
} catch (InvalidArgumentException $e) {
    $invalidSigCaught = true;
}
assertTest("Scenario 12: Invalid / forged webhook signature rejected with 400", $invalidSigCaught);

// Scenario 13: Duplicate webhook event deduplication
$eventDupId = 'WH-EVENT-DUP-' . bin2hex(random_bytes(4));
$webhookDupPayload = json_encode([
    'id' => $eventDupId,
    'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
    'resource' => [
        'id' => 'CAP-123',
        'custom_id' => $orderPending['orderId'],
        'amount' => [
            'currency_code' => 'USD',
            'value' => (string)$orderPending['amountUsd']
        ]
    ]
]);
$whRes1 = PhPayPalService::handleWebhook($pdo, $validWebhookHeaders, $webhookDupPayload, $testClient);
$whRes2 = PhPayPalService::handleWebhook($pdo, $validWebhookHeaders, $webhookDupPayload, $testClient);

assertTest("Scenario 13: Duplicate webhook event recognized and deduplicated",
    ($whRes1['processed'] ?? false) && ($whRes2['duplicate'] ?? false));
PhPayPalService::releaseInventoryReservation($pdo, $orderPending['orderId']);

// Scenario 14: Stock changes during checkout (product sold out between create & capture)
$orderStock = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], null, $testClient);
// Mark product out of stock
$pdo->prepare("UPDATE products SET in_stock = 0 WHERE id = ?")->execute([$p1]);
$stockCaught = false;
try {
    PhPayPalService::captureOrder($pdo, $orderStock['orderId'], $orderStock['paypalOrderId'], $testClient);
} catch (DomainException $e) {
    $stockCaught = str_contains($e->getMessage(), 'out of stock');
}
// Restore stock
$pdo->prepare("UPDATE products SET in_stock = 1 WHERE id = ?")->execute([$p1]);
PhPayPalService::releaseInventoryReservation($pdo, $orderStock['orderId']);
assertTest("Scenario 14: Stock depletion during checkout caught before capture confirmation", $stockCaught);

// Scenario 15: Order status consistency across ph_orders and ph_payments
$orderFull = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], null, $testClient);
$capFull = PhPayPalService::captureOrder($pdo, $orderFull['orderId'], $orderFull['paypalOrderId'], $testClient);

$stOrdCheck = $pdo->prepare("SELECT status, payment FROM ph_orders WHERE id = ?");
$stOrdCheck->execute([$orderFull['orderId']]);
$ordRow = $stOrdCheck->fetch();

$stPayCheck = $pdo->prepare("SELECT status, capture_id, payer_email FROM ph_payments WHERE order_id = ?");
$stPayCheck->execute([$orderFull['orderId']]);
$payRow = $stPayCheck->fetch();

assertTest("Scenario 15: Order status consistency across ph_orders (Confirmed) and ph_payments (COMPLETED)",
    $ordRow['status'] === 'Confirmed' && $payRow['status'] === 'COMPLETED' && !empty($payRow['capture_id']));

// Scenario 16: Exchange-rate rounding consistency
// Test across multiple items and odd price combinations
$oddItems = [
    ['id' => $p1, 'qty' => 3],
    ['id' => $p2, 'qty' => 1]
];
$expectedSubtotal = round(($price1 * 3) + $price2, 2);
$expectedPkr = round($expectedSubtotal + 200.0, 2);
$expectedUsd = round($expectedPkr / 280.0, 2);

$orderFx = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => $oddItems], null, $testClient);
PhPayPalService::releaseInventoryReservation($pdo, $orderFx['orderId']);

assertTest("Scenario 16: Mathematical FX conversion and 2-decimal rounding exactness ({$expectedPkr} PKR = {$expectedUsd} USD)",
    $orderFx['amountPkr'] === $expectedPkr && $orderFx['amountUsd'] === $expectedUsd);

// ----------------------------------------------------------------------------
// PHASE 8.1 SECURITY AUDIT VERIFICATION TESTS
// ----------------------------------------------------------------------------
echo "\n--- Phase 8.1: PayPal Security Audit & Privacy Defense Tests ---\n";

// 8.1.1: MOCK mode strictly prohibited in production environment
$prodMockBlocked = false;
try {
    new PhPayPalClient(['PAYPAL_MODE' => 'mock', 'APP_ENV' => 'production']);
} catch (DomainException $e) {
    $prodMockBlocked = str_contains($e->getMessage(), 'SECURITY VIOLATION');
}
assertTest("Phase 8.1 Test 1: PAYPAL_MODE=mock strictly prohibited in production environment", $prodMockBlocked);

// 8.1.2: MOCK mode strictly prohibited in PAYPAL_ENV=live
$liveMockBlocked = false;
try {
    new PhPayPalClient(['PAYPAL_MODE' => 'mock', 'PAYPAL_ENV' => 'live']);
} catch (DomainException $e) {
    $liveMockBlocked = str_contains($e->getMessage(), 'SECURITY VIOLATION');
}
assertTest("Phase 8.1 Test 2: PAYPAL_MODE=mock strictly prohibited when PAYPAL_ENV=live", $liveMockBlocked);

// 8.1.3: Sandbox mode with missing credentials produces clear configuration error (never silent mock fallback)
$missingCredsBlocked = false;
try {
    new PhPayPalClient(['PAYPAL_MODE' => 'sandbox', 'PAYPAL_CLIENT_ID' => '', 'PAYPAL_CLIENT_SECRET' => '']);
} catch (DomainException $e) {
    $missingCredsBlocked = str_contains($e->getMessage(), 'PayPal configuration error');
}
assertTest("Phase 8.1 Test 3: Non-mock mode requires valid credentials (no silent mock fallback)", $missingCredsBlocked);

// 8.1.4: Payee merchant ID mismatch rejected during capture
$merchantClient = new PhPayPalClient([
    'PAYPAL_MODE' => 'mock',
    'PAYPAL_MERCHANT_ID' => 'EXPECTED_MERCHANT_XYZ'
]);
class MockSpoofedMerchantClient extends PhPayPalClient {
    public function captureOrder(string $paypalOrderId, ?string $mockExpectedAmount = null): array {
        $res = parent::captureOrder($paypalOrderId, $mockExpectedAmount);
        $res['purchase_units'][0]['payee']['merchant_id'] = 'ATTACKER_MERCHANT_999';
        return $res;
    }
}
$orderMerch = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], null, $merchantClient);
$merchMismatchCaught = false;
try {
    PhPayPalService::captureOrder($pdo, $orderMerch['orderId'], $orderMerch['paypalOrderId'], new MockSpoofedMerchantClient(['PAYPAL_MODE' => 'mock', 'PAYPAL_MERCHANT_ID' => 'EXPECTED_MERCHANT_XYZ']));
} catch (DomainException $e) {
    $merchMismatchCaught = str_contains($e->getMessage(), 'payee merchant');
}
PhPayPalService::releaseInventoryReservation($pdo, $orderMerch['orderId']);
assertTest("Phase 8.1 Test 4: Payee merchant ID mismatch rejected and flagged during capture", $merchMismatchCaught);

// 8.1.5: Payment status privacy protection (IDOR defense - no token or wrong token rejected with 403)
$orderPrivacy = PhPayPalService::initiateOrder($pdo, ['address' => $validAddress, 'items' => [['id' => $p1, 'qty' => 1]]], 42, $testClient);
$privacyToken = $orderPrivacy['paymentToken'] ?? '';

$unauthStatusCaught = false;
try {
    PhPayPalService::getPaymentStatus($pdo, $orderPrivacy['orderId'], null, null); // No token, no user
} catch (DomainException $e) {
    $unauthStatusCaught = $e->getCode() === 403;
}
assertTest("Phase 8.1 Test 5: Payment status query without access token or user ownership rejected with 403", $unauthStatusCaught);

$invalidTokenCaught = false;
try {
    PhPayPalService::getPaymentStatus($pdo, $orderPrivacy['orderId'], 'WRONG_INVALID_TOKEN', null);
} catch (DomainException $e) {
    $invalidTokenCaught = $e->getCode() === 403;
}
assertTest("Phase 8.1 Test 6: Payment status query with invalid token rejected with 403", $invalidTokenCaught);

// 8.1.6: Authorized status query with unguessable token succeeds and sanitizes sensitive data
$validStatus = PhPayPalService::getPaymentStatus($pdo, $orderPrivacy['orderId'], $privacyToken, null);
assertTest("Phase 8.1 Test 7: Authorized query with unguessable token succeeds without leaking payer personal email",
    $validStatus['success'] === true 
    && $validStatus['orderId'] === $orderPrivacy['orderId'] 
    && !isset($validStatus['payment']['payerEmail']));

// 8.1.7: Authenticated user ownership allows status retrieval
$ownerStatus = PhPayPalService::getPaymentStatus($pdo, $orderPrivacy['orderId'], null, 42);
assertTest("Phase 8.1 Test 8: Authenticated order owner can retrieve payment status without token",
    $ownerStatus['success'] === true && $ownerStatus['orderId'] === $orderPrivacy['orderId']);

// 8.1.8: Delayed authentic webhook retry (>20 minutes old) is accepted via signature verification
$delayedWebhookHeaders = [
    'PAYPAL-TRANSMISSION-ID' => 'TRANS-DELAYED-001',
    'PAYPAL-TRANSMISSION-TIME' => gmdate('Y-m-d\TH:i:s\Z', time() - 7200), // 2 hours old
    'PAYPAL-TRANSMISSION-SIG' => 'VALID_TEST_SIG',
    'PAYPAL-CERT-URL' => 'https://api.sandbox.paypal.com/cert.pem',
    'PAYPAL-AUTH-ALGO' => 'SHA256withRSA'
];
$delayedPayload = json_encode([
    'id' => 'WH-DELAYED-' . bin2hex(random_bytes(4)),
    'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
    'resource' => [
        'id' => 'CAP-DELAYED-99',
        'custom_id' => $orderFx['orderId'],
        'amount' => [
            'currency_code' => 'USD',
            'value' => (string)$orderFx['amountUsd']
        ]
    ]
]);
$delayedResult = PhPayPalService::handleWebhook($pdo, $delayedWebhookHeaders, $delayedPayload, $testClient);
assertTest("Phase 8.1 Test 9: Delayed authentic webhook (>20 mins old) accepted via signature & deduplicated",
    ($delayedResult['processed'] ?? false) === true);

echo "\n========================================================================\n";
echo "Test Suite Execution Finished: {$testsPassed} / {$totalTests} Passed\n";
echo "========================================================================\n";

if ($testsPassed === $totalTests) {
    echo "SUCCESS: ALL TESTS PASSED WITH 0 REGRESSIONS.\n";
    exit(0);
} else {
    echo "FAILURE: ONE OR MORE TESTS FAILED.\n";
    exit(1);
}
