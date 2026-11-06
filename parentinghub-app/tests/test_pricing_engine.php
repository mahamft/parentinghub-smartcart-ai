<?php
declare(strict_types=1);

define('PH_TEST_SUITE', true);
define('PH_ENTRY', true);

require_once dirname(__DIR__) . '/backend/api/lib/pricing.php';

// Mock helper functions needed by pricing engine if not loaded
if (!function_exists('ph_fixed_shipping_fee')) {
    function ph_fixed_shipping_fee(float $subtotal): float {
        return $subtotal > 0 ? 200.0 : 0.0;
    }
}
if (!function_exists('ph_env')) {
    function ph_env(string $k, ?string $d = null): ?string {
        return $d;
    }
}

echo "=== Running Pricing & Validation Engine Unit Tests ===\n";

// 1. Initialize SQLite in-memory database with products table
$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$pdo->exec("CREATE TABLE products (
    id TEXT PRIMARY KEY,
    sku TEXT,
    name TEXT,
    price REAL DEFAULT 0,
    compare_at REAL,
    image TEXT,
    images TEXT,
    category TEXT,
    brand TEXT,
    description TEXT,
    has_options INTEGER DEFAULT 0,
    options_json TEXT,
    rating REAL DEFAULT 0,
    reviews INTEGER DEFAULT 0,
    in_stock INTEGER DEFAULT 1,
    weight_kg REAL,
    tags TEXT,
    source_url TEXT,
    video_url TEXT,
    source_json TEXT
)");

$pdo->exec("CREATE TABLE ph_catalog_state (
    state_key TEXT PRIMARY KEY,
    payload TEXT
)");

// Insert sample products:
// Product 1: Basic in-stock bottle
$pdo->exec("INSERT INTO products (id, sku, name, price, in_stock, category, brand) 
    VALUES ('p-bottle-1', 'SKU-001', 'Philips Avent Anti-Colic 260ml', 2500.00, 1, 'Feeding Bottles', 'Philips Avent')");

// Product 2: Out of stock item
$pdo->exec("INSERT INTO products (id, sku, name, price, in_stock, category, brand) 
    VALUES ('p-out-stock', 'SKU-002', 'Discontinued Teat Pack', 1200.00, 0, 'Teats', 'Philips Avent')");

// Product 3: Product with options
$optionsData = json_encode([
    ['name' => 'Color', 'values' => ['Blue', 'Pink', 'White']],
    ['name' => 'Size', 'values' => ['125ml', '260ml']]
]);
$pdo->exec("INSERT INTO products (id, sku, name, price, in_stock, has_options, options_json, category, brand) 
    VALUES ('p-option-bottle', 'SKU-003', 'Tommee Tippee Closer to Nature Bottle', 3000.00, 1, 1, '$optionsData', 'Feeding Bottles', 'Tommee Tippee')");

// Insert catalog coupons
$catalogPayload = json_encode([
    'coupons' => [
        [
            'code' => 'WELCOME10',
            'type' => 'percent',
            'value' => 10,
            'minOrder' => 2000,
            'active' => true,
            'expiryDate' => '2099-12-31'
        ],
        [
            'code' => 'FLAT500',
            'type' => 'fixed',
            'value' => 500,
            'minOrder' => 4000,
            'active' => true,
            'expiryDate' => '2099-12-31'
        ],
        [
            'code' => 'EXPIRED20',
            'type' => 'percent',
            'value' => 20,
            'minOrder' => 1000,
            'active' => true,
            'expiryDate' => '2020-01-01'
        ]
    ]
]);
$pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload) VALUES ('catalog', ?)")->execute([$catalogPayload]);

$passed = 0;
$failed = 0;

function assertTest(bool $condition, string $testName) {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] $testName\n";
        $passed++;
    } else {
        echo "  [FAIL] $testName\n";
        $failed++;
    }
}

// Test 1: Order ID Generation
$orderId = ph_generate_order_id();
assertTest(preg_match('/^PH-\d{8}-[0-9A-F]{8}$/', $orderId) === 1, "Order ID format matches PH-YYYYMMDD-HEX8 ($orderId)");

// Test 2: Standard Cart Calculation with 1 item
$result1 = ph_calculate_cart_totals($pdo, [
    ['id' => 'p-bottle-1', 'qty' => 2]
]);
assertTest($result1['subtotal'] === 5000.00, "Subtotal is 5000 (2 * 2500)");
assertTest($result1['shipping'] === 200.00, "Fixed shipping fee is PKR 200");
assertTest($result1['total'] === 5200.00, "Total is PKR 5200 (5000 + 200)");
assertTest($result1['discount'] === 0.00, "Discount is 0 when no coupon provided");

// Test 3: Dual Currency FX conversion
assertTest($result1['fxRate'] === 280.0, "Default FX rate is 280 PKR/USD");
$expectedUsd = round(5200.00 / 280.0, 2);
assertTest($result1['totalUsd'] === $expectedUsd, "Total USD correctly converted to $expectedUsd");

// Test 4: Percentage Coupon (10% off on 5000 = 500 discount)
$resultCoupon = ph_calculate_cart_totals($pdo, [
    ['id' => 'p-bottle-1', 'qty' => 2]
], 'welcome10');
assertTest($resultCoupon['discount'] === 500.00, "10% coupon provides PKR 500 discount");
assertTest($resultCoupon['total'] === 4700.00, "Total with discount and shipping is PKR 4700 (5000 - 500 + 200)");
assertTest($resultCoupon['coupon'] === 'WELCOME10', "Coupon code is normalized to uppercase");

// Test 5: Out of stock product rejected
try {
    ph_calculate_cart_totals($pdo, [
        ['id' => 'p-out-stock', 'qty' => 1]
    ]);
    assertTest(false, "Out of stock item was not rejected");
} catch (DomainException $e) {
    assertTest(str_contains($e->getMessage(), 'out of stock'), "Out of stock item correctly thrown DomainException");
}

// Test 6: Non-existent product rejected
try {
    ph_calculate_cart_totals($pdo, [
        ['id' => 'non-existent-id', 'qty' => 1]
    ]);
    assertTest(false, "Non-existent item was not rejected");
} catch (DomainException $e) {
    assertTest(str_contains($e->getMessage(), 'no longer available'), "Non-existent item throws DomainException");
}

// Test 7: Valid product option accepted
$resultOpt = ph_calculate_cart_totals($pdo, [
    ['id' => 'p-option-bottle', 'qty' => 1, 'option' => 'Color: Blue / Size: 260ml']
]);
assertTest($resultOpt['subtotal'] === 3000.00, "Product with options calculated subtotal 3000");
assertTest($resultOpt['items'][0]['option'] === 'Color: Blue / Size: 260ml', "Normalized options saved on line item");

// Test 8: Invalid product option rejected
try {
    ph_calculate_cart_totals($pdo, [
        ['id' => 'p-option-bottle', 'qty' => 1, 'option' => 'Color: NeonGreen / Size: 260ml']
    ]);
    assertTest(false, "Invalid option value was not rejected");
} catch (InvalidArgumentException $e) {
    assertTest(str_contains($e->getMessage(), 'Invalid option'), "Invalid option correctly thrown InvalidArgumentException");
}

// Test 9: Expired coupon rejected
try {
    ph_calculate_cart_totals($pdo, [
        ['id' => 'p-bottle-1', 'qty' => 2]
    ], 'EXPIRED20');
    assertTest(false, "Expired coupon was not rejected");
} catch (InvalidArgumentException $e) {
    assertTest(str_contains($e->getMessage(), 'invalid, inactive'), "Expired coupon correctly rejected");
}

// Test 10: Empty cart rejected
try {
    ph_calculate_cart_totals($pdo, []);
    assertTest(false, "Empty cart was not rejected");
} catch (InvalidArgumentException $e) {
    assertTest(str_contains($e->getMessage(), 'empty'), "Empty cart throws InvalidArgumentException");
}

echo "\nSummary: $passed passed, $failed failed.\n";
if ($failed > 0) exit(1);
echo "ALL TESTS PASSED.\n";

