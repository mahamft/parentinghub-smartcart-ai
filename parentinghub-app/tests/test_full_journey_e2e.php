<?php
declare(strict_types=1);

/**
 * ParentingHub Full End-to-End E-Commerce Journey Test
 * 
 * Flow:
 * 1. AI Recommendation: Parent asks for baby bottles / essentials
 * 2. SmartCart Proposal: AI proposes a starter pack (ticket PROP-XXXX)
 * 3. Customer Review & Approval: Customer modifies quantities and approves bundle
 * 4. Checkout Initiation: Server re-verifies pricing, address, dual currency USD
 * 5. PayPal Sandbox Lifecycle: Creates order, simulates customer approval, captures payment
 * 6. Order Confirmation & Ledger Audit: Order becomes 'Confirmed', ph_payments becomes 'COMPLETED'
 * 7. Status Query & Privacy Check: Queries status with secure payment token
 */

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';
require_once __DIR__ . '/../backend/api/lib/pricing.php';
require_once __DIR__ . '/../backend/api/ai/catalog_retriever.php';
require_once __DIR__ . '/../backend/api/ai/ai_service.php';
require_once __DIR__ . '/../backend/api/ai/cart_proposals.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_client.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_service.php';

echo "========================================================================\n";
echo "   ParentingHub Full End-to-End SmartCart AI to PayPal Journey\n";
echo "   [SIMULATED MOCK TEST DRIVER - FOR LOCAL VERIFICATION ONLY]\n";
echo "========================================================================\n\n";

// Set up in-memory database with 287 catalog products
$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

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
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE ph_catalog_state (
    state_key VARCHAR(64) PRIMARY KEY,
    payload TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

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

$pdo->exec("CREATE TABLE ph_payment_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_id VARCHAR(100) NOT NULL UNIQUE,
    event_type VARCHAR(80) NOT NULL,
    order_id VARCHAR(32) DEFAULT NULL,
    verified INTEGER NOT NULL DEFAULT 1,
    payload TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$catalogPath = __DIR__ . '/../backend/api/seed/catalog.json';
$catalogData = json_decode((string)file_get_contents($catalogPath), true);
$seedProducts = $catalogData['products'] ?? [];

$st = $pdo->prepare("INSERT INTO products (id, sku, name, price, compare_at, image, images, category, brand, description, has_options, options_json, rating, reviews, in_stock, weight_kg, tags, source_url)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($seedProducts as $p) {
    $st->execute([
        $p['id'],
        $p['sku'] ?? '',
        $p['name'],
        (float)($p['price'] ?? 0),
        isset($p['compareAt']) && $p['compareAt'] !== null ? (float)$p['compareAt'] : null,
        $p['img'] ?? ($p['images'][0] ?? ($p['image'] ?? '')),
        json_encode($p['images'] ?? []),
        $p['category'] ?? $p['cat'] ?? 'General',
        $p['brand'] ?? '',
        $p['description'] ?? $p['desc'] ?? '',
        empty($p['options']) ? 0 : 1,
        json_encode($p['options'] ?? []),
        (float)($p['rating'] ?? 0),
        (int)($p['reviews'] ?? 0),
        ($p['inStock'] ?? true) ? 1 : 0,
        isset($p['weightKg']) ? (float)$p['weightKg'] : null,
        json_encode($p['tags'] ?? []),
        $p['sourceUrl'] ?? ''
    ]);
}

$pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload) VALUES ('catalog', ?)")
    ->execute([json_encode($catalogData)]);

echo "Catalog Initialized: " . count($seedProducts) . " genuine products in database.\n\n";

$step = 1;
function logStep(string $desc) {
    global $step;
    echo "--- Step {$step}: {$desc} ---\n";
    $step++;
}

// ----------------------------------------------------------------------------
// STEP 1: AI SHOPPING ASSISTANT RECOMMENDATION
// ----------------------------------------------------------------------------
logStep("Parent consults AI for 3-month-old infant feeding essentials");
$aiQuery = "I need feeding bottles and teats for my 3 month old baby under PKR 10,000";
$recs = PhAiService::runRecommendations($pdo, 3, 10000.0, null, $aiQuery);

echo "AI Response Status: " . ($recs['count'] > 0 ? "SUCCESS" : "EMPTY") . "\n";
echo "Recommended Products: {$recs['count']}\n";
foreach (array_slice($recs['recommendations'], 0, 2) as $r) {
    echo "  - {$r['name']} (PKR " . number_format($r['price']) . ") | Reason: {$r['reason']}\n";
}

if ($recs['count'] < 2) {
    echo "FATAL: Did not get enough product recommendations.\n";
    exit(1);
}

$recItem1 = $recs['recommendations'][0];
$recItem2 = $recs['recommendations'][1];

function ph_test_default_option(array $product): ?string {
    $options = $product['options'] ?? [];
    if (empty($options) || !is_array($options)) return null;
    $parts = [];
    foreach ($options as $group) {
        if (!empty($group['name']) && !empty($group['values'][0])) {
            $parts[] = trim((string)$group['name']) . ': ' . trim((string)$group['values'][0]);
        }
    }
    return !empty($parts) ? implode(' / ', $parts) : null;
}

$opt1 = ph_test_default_option($recItem1);
$opt2 = ph_test_default_option($recItem2);

// ----------------------------------------------------------------------------
// STEP 2: AI PROPOSES SMARTCART BUNDLE TICKET
// ----------------------------------------------------------------------------
logStep("AI generates tokenized proposal ticket for review");
$proposalItems = [
    ['id' => $recItem1['id'], 'qty' => 2] + ($opt1 ? ['option' => $opt1] : []),
    ['id' => $recItem2['id'], 'qty' => 1] + ($opt2 ? ['option' => $opt2] : [])
];
$proposal = PhCartProposalEngine::createProposal($pdo, $proposalItems);

echo "Proposal ID:      {$proposal['proposalId']}\n";
echo "Proposal Status:  {$proposal['status']}\n";
echo "Proposal Subtotal: PKR " . number_format($proposal['subtotal'], 2) . "\n";
echo "Nationwide Delivery: PKR " . number_format($proposal['shipping'], 2) . "\n";
echo "Proposal Total:   PKR " . number_format($proposal['total'], 2) . "\n";
echo "Expires At:       {$proposal['expiresAt']}\n";

if (!str_starts_with($proposal['proposalId'], 'PROP-') || $proposal['shipping'] !== 200.0) {
    echo "FATAL: Proposal structure invalid.\n";
    exit(1);
}

// ----------------------------------------------------------------------------
// STEP 3: CUSTOMER REVIEW & MODIFIED APPROVAL
// ----------------------------------------------------------------------------
logStep("Customer modifies quantities (reduces item 1 to qty 1) and explicitly approves");
$approvalItems = [
    ['id' => $recItem1['id'], 'qty' => 1] + ($opt1 ? ['option' => $opt1] : []),
    ['id' => $recItem2['id'], 'qty' => 1] + ($opt2 ? ['option' => $opt2] : [])
];
$approvedBundle = PhCartProposalEngine::approveProposal($pdo, $proposal['proposalId'], $approvalItems);

echo "Approval Result:  " . ($approvedBundle['success'] ? "APPROVED" : "FAILED") . "\n";
echo "Approved Items:   {$approvedBundle['itemCount']}\n";
echo "Updated Subtotal: PKR " . number_format($approvedBundle['subtotal'], 2) . "\n";
echo "Updated Total:    PKR " . number_format($approvedBundle['total'], 2) . "\n";

$expectedSub = round($recItem1['price'] + $recItem2['price'], 2);
$expectedTot = round($expectedSub + 200.0, 2);

if ($approvedBundle['subtotal'] !== $expectedSub || $approvedBundle['total'] !== $expectedTot) {
    echo "FATAL: Approved bundle price recalculation mismatch.\n";
    exit(1);
}

// ----------------------------------------------------------------------------
// STEP 4: CHECKOUT INITIATION & DUAL-CURRENCY CALCULATION
// ----------------------------------------------------------------------------
logStep("Customer fills delivery address and selects PayPal checkout");
$customerAddress = [
    'name' => 'Fatima Ali',
    'phone' => '03001234567',
    'email' => 'fatima.ali@example.com',
    'address' => 'House 14, Street 9, Sector F-7/1',
    'city' => 'Islamabad',
    'notes' => 'Please call upon arrival.'
];

$checkoutItems = [
    ['id' => $recItem1['id'], 'qty' => 1] + ($opt1 ? ['option' => $opt1] : []),
    ['id' => $recItem2['id'], 'qty' => 1] + ($opt2 ? ['option' => $opt2] : [])
];

$testClient = new PhPayPalClient([
    'PAYPAL_MODE' => 'mock',
    'PAYPAL_FX_PKR_USD' => '280.0000'
]);

$checkoutOrder = PhPayPalService::initiateOrder($pdo, [
    'address' => $customerAddress,
    'items' => $checkoutItems
], 101, $testClient);

echo "ParentingHub Order ID: {$checkoutOrder['orderId']}\n";
echo "PayPal Order ID:       {$checkoutOrder['paypalOrderId']}\n";
echo "Payment Access Token:  " . substr($checkoutOrder['paymentToken'], 0, 16) . "... (64 hex chars)\n";
echo "Order Status:          {$checkoutOrder['status']}\n";
echo "PKR Total:             PKR " . number_format($checkoutOrder['amountPkr'], 2) . "\n";
echo "Fixed Demo FX:         {$checkoutOrder['fxRate']} PKR/USD\n";
echo "USD PayPal Charge:     \${$checkoutOrder['amountUsd']} USD\n";

$expectedUsd = round($expectedTot / 280.0, 2);
if ($checkoutOrder['amountUsd'] !== $expectedUsd || $checkoutOrder['status'] !== 'Pending Payment') {
    echo "FATAL: Order creation values invalid.\n";
    exit(1);
}

// ----------------------------------------------------------------------------
// STEP 5: PAYPAL AUTHORIZATION & SECURE BACKEND CAPTURE
// ----------------------------------------------------------------------------
logStep("Customer approves in PayPal Sandbox; server captures funds and confirms order");
$captureResult = PhPayPalService::captureOrder($pdo, $checkoutOrder['orderId'], $checkoutOrder['paypalOrderId'], $testClient);

echo "Capture Success:       " . ($captureResult['success'] ? "YES" : "NO") . "\n";
echo "Capture ID:            {$captureResult['captureId']}\n";
echo "Final Order Status:    {$captureResult['status']}\n";
echo "Captured Amount USD:   \${$captureResult['amountUsd']} USD\n";
echo "Captured Amount PKR:   PKR " . number_format($captureResult['amountPkr'], 2) . "\n";

if ($captureResult['status'] !== 'Confirmed' || empty($captureResult['captureId'])) {
    echo "FATAL: Capture did not confirm the order.\n";
    exit(1);
}

// ----------------------------------------------------------------------------
// STEP 6: DATABASE LEDGER AUDIT
// ----------------------------------------------------------------------------
logStep("Verifying ph_orders and ph_payments database records");
$stOrder = $pdo->prepare("SELECT id, status, payment, total, subtotal, shipping FROM ph_orders WHERE id = ?");
$stOrder->execute([$checkoutOrder['orderId']]);
$dbOrder = $stOrder->fetch();

$stPayment = $pdo->prepare("SELECT order_id, paypal_order_id, capture_id, amount_usd, amount_pkr, status FROM ph_payments WHERE order_id = ?");
$stPayment->execute([$checkoutOrder['orderId']]);
$dbPayment = $stPayment->fetch();

echo "Database ph_orders:   ID: {$dbOrder['id']} | Status: {$dbOrder['status']} | Total: PKR {$dbOrder['total']}\n";
echo "Database ph_payments: Order: {$dbPayment['order_id']} | Status: {$dbPayment['status']} | Capture ID: {$dbPayment['capture_id']} | USD: \${$dbPayment['amount_usd']}\n";

if ($dbOrder['status'] !== 'Confirmed' || $dbPayment['status'] !== 'COMPLETED') {
    echo "FATAL: Database persistence status desynchronized.\n";
    exit(1);
}

// ----------------------------------------------------------------------------
// STEP 7: PAYMENT STATUS PRIVACY VERIFICATION
// ----------------------------------------------------------------------------
logStep("Testing payment status privacy (authorized token query vs unauthorized query)");

// Unauthorized query (no token)
$blocked = false;
try {
    PhPayPalService::getPaymentStatus($pdo, $checkoutOrder['orderId'], null, null);
} catch (DomainException $e) {
    $blocked = ($e->getCode() === 403);
}
echo "Unauthorized query blocked with 403: " . ($blocked ? "YES" : "NO") . "\n";

// Authorized query (with secret token)
$statusWithToken = PhPayPalService::getPaymentStatus($pdo, $checkoutOrder['orderId'], $checkoutOrder['paymentToken'], null);
echo "Authorized query with token succeeded: " . ($statusWithToken['success'] ? "YES" : "NO") . "\n";
echo "Status returned: {$statusWithToken['orderStatus']} | Payment: {$statusWithToken['payment']['status']}\n";

if (!$blocked || !$statusWithToken['success']) {
    echo "FATAL: Privacy status authorization check failed.\n";
    exit(1);
}

echo "\n========================================================================\n";
echo "FULL END-TO-END JOURNEY TEST COMPLETED WITH 100% SUCCESS!\n";
echo "All 7 stages from AI consultation to PayPal capture executed cleanly.\n";
echo "========================================================================\n";
exit(0);
