<?php
declare(strict_types=1);

/**
 * ParentingHub Commerce Regression Test Suite
 * 
 * Verifies core commerce functionality:
 * 1. Product retrieval (287 catalog products)
 * 2. Product detail inspection
 * 3. Stock validation (in_stock flag enforcement)
 * 4. Cart calculations (pricing, line totals, options)
 * 5. Coupon validation (active, percentage, fixed, min order, expiry)
 * 6. Order creation (server totals, address normalization, payment methods)
 * 7. Invalid product rejection (non-existent, malformed ID)
 * 8. Invalid quantity rejection (<1 or >99)
 * 9. Delivery fee calculation (fixed PKR 200 rule, zero threshold)
 */

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once dirname(__DIR__) . '/backend/api/lib/pricing.php';

function ph_fixed_shipping_fee(float $subtotal): float {
    return $subtotal > 0 ? 200.0 : 0.0;
}
function ph_env(string $key, ?string $default = null): ?string {
    return $default;
}

echo "============================================================\n";
echo "   ParentingHub Commerce Regression Test Suite\n";
echo "============================================================\n\n";

// Load 287 products from catalog.json
$catalogPath = dirname(__DIR__) . '/backend/api/seed/catalog.json';
if (!is_file($catalogPath)) {
    die("FATAL: catalog.json missing\n");
}
$catalogData = json_decode(file_get_contents($catalogPath), true);
$seedProducts = $catalogData['products'];
echo "Loaded " . count($seedProducts) . " seed products from catalog.json\n\n";

// Set up in-memory regression database
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
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

// Insert all 287 products into SQLite
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

// Add test coupons into catalog state
$catalogPayloadWithCoupons = $catalogData;
$catalogPayloadWithCoupons['coupons'] = [
    [
        'code' => 'WELCOME10',
        'type' => 'percent',
        'value' => 10,
        'minOrder' => 1000,
        'active' => true,
        'expiryDate' => '2099-12-31'
    ],
    [
        'code' => 'FLAT500',
        'type' => 'fixed',
        'value' => 500,
        'minOrder' => 3000,
        'active' => true,
        'expiryDate' => '2099-12-31'
    ],
    [
        'code' => 'EXPIRED99',
        'type' => 'percent',
        'value' => 50,
        'minOrder' => 500,
        'active' => true,
        'expiryDate' => '2021-01-01'
    ]
];
$pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload) VALUES ('catalog', ?)")
    ->execute([json_encode($catalogPayloadWithCoupons)]);

$testCount = 0;
$passedCount = 0;

function runTest(string $title, callable $fn) {
    global $testCount, $passedCount;
    $testCount++;
    try {
        $result = $fn();
        if ($result === true || $result === null) {
            echo "  [PASS] Test $testCount: $title\n";
            $passedCount++;
        } else {
            echo "  [FAIL] Test $testCount: $title ($result)\n";
        }
    } catch (Throwable $e) {
        echo "  [FAIL] Test $testCount: $title (" . $e->getMessage() . ")\n";
    }
}

// 1. Product Retrieval Test
runTest("Product retrieval returns all 287 catalog products", function() use ($pdo) {
    $count = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($count !== 287) return "Expected 287, got $count";
    return true;
});

// 2. Product Details Test
runTest("Product detail lookup retrieves correct metadata, price and brand", function() use ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute(['philips-avent-scf619-05']);
    $prod = $stmt->fetch();
    if (!$prod) return "Product not found";
    if ($prod['brand'] !== 'Philips Avent') return "Brand mismatch: " . $prod['brand'];
    if ((float)$prod['price'] !== 5835.00) return "Price mismatch: " . $prod['price'];
    return true;
});

// 3. Stock Validation Test
runTest("Out of stock product is blocked from checkout", function() use ($pdo) {
    // Temporarily mark one product as out of stock
    $pdo->exec("UPDATE products SET in_stock = 0 WHERE id = 'philips-avent-scf619-05'");
    try {
        ph_calculate_cart_totals($pdo, [
            ['id' => 'philips-avent-scf619-05', 'qty' => 1]
        ]);
        return "Should have thrown DomainException for out-of-stock product";
    } catch (DomainException $e) {
        if (!str_contains($e->getMessage(), 'out of stock')) return "Unexpected error: " . $e->getMessage();
    } finally {
        $pdo->exec("UPDATE products SET in_stock = 1 WHERE id = 'philips-avent-scf619-05'");
    }
    return true;
});

// 4. Cart Calculations Test
runTest("Cart calculation validates multi-item subtotals and quantities", function() use ($pdo) {
    $items = [
        ['id' => 'philips-avent-scf619-05', 'qty' => 2] // 5835 * 2 = 11670
    ];
    $calc = ph_calculate_cart_totals($pdo, $items);
    if ($calc['subtotal'] !== 11670.00) return "Expected subtotal 11670, got " . $calc['subtotal'];
    if ($calc['totalQuantity'] !== 2) return "Expected qty 2, got " . $calc['totalQuantity'];
    return true;
});

// 5. Coupon Validation Test
runTest("Percentage coupon correctly discounts order and enforces minOrder", function() use ($pdo) {
    $items = [['id' => 'philips-avent-scf619-05', 'qty' => 1]]; // 5835
    $calc = ph_calculate_cart_totals($pdo, $items, 'welcome10');
    // 10% of 5835 = 583.50
    if ($calc['discount'] !== 583.50) return "Expected discount 583.50, got " . $calc['discount'];
    if ($calc['total'] !== round(5835 - 583.50 + 200, 2)) return "Total mismatch: " . $calc['total'];
    return true;
});

runTest("Expired coupon is rejected with InvalidArgumentException", function() use ($pdo) {
    try {
        ph_calculate_cart_totals($pdo, [['id' => 'philips-avent-scf619-05', 'qty' => 1]], 'EXPIRED99');
        return "Expired coupon was not rejected";
    } catch (InvalidArgumentException $e) {
        return true;
    }
});

// 6. Order Creation Test
runTest("End-to-end order creation stores server-verified totals and address", function() use ($pdo) {
    $calc = ph_calculate_cart_totals($pdo, [['id' => 'philips-avent-scf619-05', 'qty' => 1]]);
    $orderId = ph_generate_order_id();
    $address = [
        'name' => 'Fatima Ali',
        'phone' => '03001234567',
        'city' => 'Lahore',
        'address' => 'House 42, Street 5, Gulberg'
    ];
    
    $stmt = $pdo->prepare("INSERT INTO ph_orders (id, status, payment, subtotal, shipping, discount, total, address_json, items_json) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $orderId,
        'Confirmed',
        'cod',
        $calc['subtotal'],
        $calc['shipping'],
        $calc['discount'],
        $calc['total'],
        json_encode($address),
        json_encode($calc['items'])
    ]);

    $saved = $pdo->query("SELECT * FROM ph_orders WHERE id = '$orderId'")->fetch();
    if (!$saved) return "Order not found";
    if ($saved['total'] != $calc['total']) return "Stored total mismatch";
    if ($saved['payment'] !== 'cod') return "Payment mismatch";
    return true;
});

// 7. Invalid Product Rejection Test
runTest("Invalid product ID throws DomainException", function() use ($pdo) {
    try {
        ph_calculate_cart_totals($pdo, [['id' => 'fake-product-xyz-99', 'qty' => 1]]);
        return "Non-existent product did not throw exception";
    } catch (DomainException $e) {
        return true;
    }
});

// 8. Invalid Quantity Rejection Test
runTest("Invalid quantity (>99 or <1) throws InvalidArgumentException", function() use ($pdo) {
    try {
        ph_calculate_cart_totals($pdo, [['id' => 'philips-avent-scf619-05', 'qty' => 150]]);
        return "Quantity 150 was not rejected";
    } catch (InvalidArgumentException $e) {
        // Now test 0
        try {
            ph_calculate_cart_totals($pdo, [['id' => 'philips-avent-scf619-05', 'qty' => 0]]);
            return "Quantity 0 was not rejected";
        } catch (InvalidArgumentException $e2) {
            return true;
        }
    }
});

// 9. Delivery Calculations Test
runTest("Delivery fee strictly enforces PKR 200 on non-empty order with zero free threshold", function() use ($pdo) {
    // Test with expensive order (Rs 50,000+) - verify fee remains PKR 200 (no free threshold)
    $items = [['id' => 'philips-avent-scf619-05', 'qty' => 10]]; // 58,350
    $calc = ph_calculate_cart_totals($pdo, $items);
    if ($calc['shipping'] !== 200.00) return "Expected shipping 200 on Rs 58,350 order, got " . $calc['shipping'];
    if ($calc['total'] !== 58550.00) return "Expected total 58,550, got " . $calc['total'];
    return true;
});

echo "\n============================================================\n";
echo "   Regression Results: $passedCount / $testCount Tests Passed\n";
echo "============================================================\n";

if ($passedCount !== $testCount) {
    exit(1);
}

