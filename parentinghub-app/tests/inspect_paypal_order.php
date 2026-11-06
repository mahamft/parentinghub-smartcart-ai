<?php
declare(strict_types=1);

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_client.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_service.php';

$paypalOrderId = '2W847706AD834392E';

echo "========================================================================\n";
echo "   PayPal Sandbox Order Inspection: {$paypalOrderId}\n";
echo "========================================================================\n\n";

try {
    // 1. Fetch Remote PayPal Order
    $client = new PhPayPalClient();
    $remote = $client->getOrder($paypalOrderId);

    echo "--- 1. Remote PayPal Sandbox Order Details ---\n";
    echo "ID:             " . ($remote['id'] ?? 'N/A') . "\n";
    echo "Status:         " . ($remote['status'] ?? 'UNKNOWN') . "\n";
    echo "Intent:         " . ($remote['intent'] ?? 'N/A') . "\n";
    echo "Create Time:    " . ($remote['create_time'] ?? 'N/A') . "\n";
    echo "Update Time:    " . ($remote['update_time'] ?? 'N/A') . "\n";

    // Purchase Unit
    $pu = $remote['purchase_units'][0] ?? [];
    $amount = $pu['amount'] ?? [];
    echo "Currency:       " . ($amount['currency_code'] ?? 'N/A') . "\n";
    echo "Value:          " . ($amount['value'] ?? 'N/A') . "\n";
    echo "Custom ID:      " . ($pu['custom_id'] ?? 'N/A') . "\n";
    echo "Invoice ID:     " . ($pu['invoice_id'] ?? 'N/A') . "\n";

    // Payer Info
    $payer = $remote['payer'] ?? null;
    if ($payer !== null && !empty($payer)) {
        echo "\nPayer Attached: YES\n";
        echo "  Payer ID:     " . ($payer['payer_id'] ?? 'N/A') . "\n";
        echo "  Payer Email:  " . ($payer['email_address'] ?? 'N/A') . "\n";
        $givenName = $payer['name']['given_name'] ?? '';
        $surname = $payer['name']['surname'] ?? '';
        echo "  Payer Name:   " . trim("{$givenName} {$surname}") . "\n";
        $country = $payer['address']['country_code'] ?? '';
        echo "  Country:      " . ($country ?: 'N/A') . "\n";
    } else {
        echo "\nPayer Attached: NO (Buyer has not approved the checkout session yet)\n";
    }

    // 2. Query Local Database
    echo "\n--- 2. Local Database Ledger Check (parentinghub_db) ---\n";
    $pdo = ph_pdo();

    // Check ph_payments
    $stPay = $pdo->prepare("SELECT * FROM ph_payments WHERE paypal_order_id = ?");
    $stPay->execute([$paypalOrderId]);
    $localPayment = $stPay->fetch();

    if ($localPayment) {
        echo "ph_payments Record Found:\n";
        echo "  Order ID:     " . $localPayment['order_id'] . "\n";
        echo "  Status:       " . $localPayment['status'] . "\n";
        echo "  Capture ID:   " . ($localPayment['capture_id'] ?? 'NULL') . "\n";
        echo "  Amount USD:   $" . $localPayment['amount_usd'] . "\n";
        echo "  Amount PKR:   Rs " . $localPayment['amount_pkr'] . "\n";

        // Check corresponding ph_orders
        $stOrd = $pdo->prepare("SELECT id, status, payment, total, subtotal, shipping FROM ph_orders WHERE id = ?");
        $stOrd->execute([$localPayment['order_id']]);
        $localOrder = $stOrd->fetch();
        if ($localOrder) {
            echo "ph_orders Record Found:\n";
            echo "  Order ID:     " . $localOrder['id'] . "\n";
            echo "  Status:       " . $localOrder['status'] . "\n";
            echo "  Payment Type: " . $localOrder['payment'] . "\n";
            echo "  Total:        PKR " . $localOrder['total'] . "\n";
        } else {
            echo "ph_orders: No matching row found for order_id: " . $localPayment['order_id'] . "\n";
        }
    } else {
        echo "ph_payments: No matching record found for paypal_order_id = '{$paypalOrderId}'\n";
        echo "(Note: Order was generated directly via verify_real_paypal_sandbox.php verification probe)\n";
    }

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

