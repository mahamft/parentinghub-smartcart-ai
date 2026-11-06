<?php
declare(strict_types=1);

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_client.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_service.php';

date_default_timezone_set('UTC');

echo "========================================================================\n";
echo "   ParentingHub First Complete Application-Integrated PayPal Checkout\n";
echo "========================================================================\n\n";

try {
    $pdo = ph_pdo();

    // 1. Target Product Selection & Pre-Check
    $targetProductId = 'philips-avent-citric-acid-10g';
    $stProd = $pdo->prepare("SELECT id, name, price, in_stock FROM products WHERE id = ?");
    $stProd->execute([$targetProductId]);
    $product = $stProd->fetch();

    if (!$product || empty($product['in_stock'])) {
        throw new RuntimeException("Target product '{$targetProductId}' is missing or out of stock.");
    }

    echo "--- 1. Target Product Verification ---\n";
    echo "Product ID:    {$product['id']}\n";
    echo "Product Name:  {$product['name']}\n";
    echo "Catalog Price: PKR " . number_format((float)$product['price'], 2) . "\n";
    echo "In Stock Flag: " . ($product['in_stock'] ? 'YES' : 'NO') . "\n\n";

    // 2. Prepare Standard Application Customer Payload (matches frontend/js/app.js)
    $payload = [
        'address' => [
            'name' => 'Ayesha Khan',
            'phone' => '03001234567',
            'email' => 'ayesha.khan@example.com',
            'address' => 'House 12, Street 4, Sector F-7/2',
            'city' => 'Islamabad',
            'notes' => 'Please deliver between 10am and 4pm'
        ],
        'items' => [
            [
                'id' => $targetProductId,
                'qty' => 1
            ]
        ],
        'coupon' => null
    ];

    echo "--- 2. Executing Real Application Flow (POST /api/paypal/create-order) ---\n";
    // Initialize real Sandbox PayPal Client
    $client = new PhPayPalClient();

    if ($client->isMockMode()) {
        throw new RuntimeException("FATAL: Client is in mock mode! Real Sandbox checkout requires live client.");
    }

    echo "Client Mode:       " . $client->getMode() . " (Strict Sandbox)\n";
    echo "PayPal API Target: https://api-m.sandbox.paypal.com\n";

    // Call PhPayPalService::initiateOrder (the exact logic behind POST /api/paypal/create-order)
    $result = PhPayPalService::initiateOrder($pdo, $payload, null, $client);

    echo "\n>>> Order Initiation Succeeded!\n";
    echo "Internal Order ID:  " . $result['orderId'] . "\n";
    echo "PayPal Order ID:    " . $result['paypalOrderId'] . "\n";
    echo "Server Total PKR:   Rs " . number_format((float)$result['amountPkr'], 2) . "\n";
    echo "Server Total USD:   $" . number_format((float)$result['amountUsd'], 2) . " USD\n";
    echo "FX Rate Applied:    " . $result['fxRate'] . " PKR/USD\n";
    echo "Buyer Approval URL: " . ($result['approvalUrl'] ?? 'N/A') . "\n\n";

    // 3. Database Verification: ph_orders
    echo "--- 3. Local Database Ledger Audit (ph_orders) ---\n";
    $stOrd = $pdo->prepare("SELECT id, status, payment, subtotal, shipping, discount, total, address_json, items_json, created_at FROM ph_orders WHERE id = ?");
    $stOrd->execute([$result['orderId']]);
    $dbOrder = $stOrd->fetch();

    if (!$dbOrder) {
        throw new RuntimeException("Database record missing in ph_orders for {$result['orderId']}");
    }

    echo "  Order ID:     {$dbOrder['id']}\n";
    echo "  Status:       {$dbOrder['status']}\n";
    echo "  Payment Type: {$dbOrder['payment']}\n";
    echo "  Subtotal:     PKR " . number_format((float)$dbOrder['subtotal'], 2) . "\n";
    echo "  Shipping:     PKR " . number_format((float)$dbOrder['shipping'], 2) . " (Fixed Delivery Fee)\n";
    echo "  Total:        PKR " . number_format((float)$dbOrder['total'], 2) . "\n";
    echo "  Items:        {$dbOrder['items_json']}\n\n";

    // 4. Database Verification: ph_payments
    echo "--- 4. Local Database Ledger Audit (ph_payments) ---\n";
    $stPay = $pdo->prepare("SELECT order_id, provider, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status, created_at FROM ph_payments WHERE order_id = ?");
    $stPay->execute([$result['orderId']]);
    $dbPayment = $stPay->fetch();

    if (!$dbPayment) {
        throw new RuntimeException("Database record missing in ph_payments for {$result['orderId']}");
    }

    echo "  Order ID:        {$dbPayment['order_id']}\n";
    echo "  Provider:        {$dbPayment['provider']}\n";
    echo "  PayPal Order ID: {$dbPayment['paypal_order_id']}\n";
    echo "  Currency:        {$dbPayment['currency']}\n";
    echo "  Amount USD:      \${$dbPayment['amount_usd']}\n";
    echo "  Amount PKR:      Rs {$dbPayment['amount_pkr']}\n";
    echo "  Payment Status:  {$dbPayment['status']}\n\n";

    // 5. Database Verification: ph_inventory_reservations
    echo "--- 5. Inventory Reservation Audit (ph_inventory_reservations) ---\n";
    $stRes = $pdo->prepare("SELECT id, order_id, product_id, active_slot, qty, status, expires_at, created_at FROM ph_inventory_reservations WHERE order_id = ?");
    $stRes->execute([$result['orderId']]);
    $reservations = $stRes->fetchAll();

    if (empty($reservations)) {
        throw new RuntimeException("Reservation record missing in ph_inventory_reservations for {$result['orderId']}");
    }

    foreach ($reservations as $r) {
        echo "  Reservation ID: {$r['id']}\n";
        echo "  Product ID:     {$r['product_id']}\n";
        echo "  Active Slot:    {$r['active_slot']}\n";
        echo "  Reserved Qty:   {$r['qty']}\n";
        echo "  Status:         {$r['status']}\n";
        echo "  Expires At:     {$r['expires_at']} (UTC)\n";
    }

    // 6. Remote PayPal Sanity Check
    echo "\n--- 6. Remote PayPal Sandbox Sanity Check (GET /v2/checkout/orders) ---\n";
    $remoteCheck = $client->getOrder($result['paypalOrderId']);
    echo "  Remote PayPal ID:     {$remoteCheck['id']}\n";
    echo "  Remote PayPal Status: {$remoteCheck['status']}\n";
    echo "  Remote Currency:      " . ($remoteCheck['purchase_units'][0]['amount']['currency_code'] ?? 'N/A') . "\n";
    echo "  Remote Amount:        $" . ($remoteCheck['purchase_units'][0]['amount']['value'] ?? 'N/A') . " USD\n";
    echo "  Remote Custom ID:     " . ($remoteCheck['purchase_units'][0]['custom_id'] ?? 'N/A') . "\n";

    echo "\n========================================================================\n";
    echo "   APPLICATION-INTEGRATED PAYPAL CHECKOUT TEST PREPARED SUCCESSFULLY!\n";
    echo "========================================================================\n";

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
