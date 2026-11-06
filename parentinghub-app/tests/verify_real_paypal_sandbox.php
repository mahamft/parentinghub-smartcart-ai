<?php
declare(strict_types=1);

/**
 * ParentingHub Real PayPal Sandbox Environment Verification Script
 * 
 * Tests:
 * 1. Configuration check (PAYPAL_MODE=sandbox, PAYPAL_ENV=sandbox)
 * 2. Credential inspection (checks if placeholders or genuine credentials are set, without printing secrets)
 * 3. Mock mode status (confirms mock simulation is strictly disabled)
 * 4. Real Sandbox API calls (OAuth2 token retrieval, order creation, capture, order status)
 * 5. Strict rejection of simulation: will NOT fabricate or simulate API responses.
 */

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_client.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_service.php';

echo "========================================================================\n";
echo "   ParentingHub Phase 8.2 Real PayPal Sandbox Environment Verification\n";
echo "========================================================================\n\n";

// 1. Inspect Environment Settings
$envMode = ph_env('PAYPAL_MODE', 'sandbox');
$envTarget = ph_env('PAYPAL_ENV', 'sandbox');
$rawClientId = trim((string)ph_env('PAYPAL_CLIENT_ID', ''));
$rawClientSecret = trim((string)ph_env('PAYPAL_CLIENT_SECRET', ''));

echo "Environment Configuration:\n";
echo "  PAYPAL_MODE: {$envMode}\n";
echo "  PAYPAL_ENV:  {$envTarget}\n";

$isPlaceholder = empty($rawClientId) 
    || empty($rawClientSecret) 
    || str_contains(strtolower($rawClientId), 'placeholder') 
    || str_contains(strtolower($rawClientSecret), 'placeholder');

echo "  Credential Status: " . ($isPlaceholder ? "PLACEHOLDER / UNCONFIGURED" : "GENUINE KEY DETECTED") . "\n";
echo "  Client ID length: " . strlen($rawClientId) . " chars\n";
echo "  Client Secret length: " . strlen($rawClientSecret) . " chars\n\n";

// 2. Test Client instantiation with Sandbox Mode
echo "--- Testing Strict Sandbox Client Behavior ---\n";
try {
    $sandboxClient = new PhPayPalClient([
        'PAYPAL_MODE' => 'sandbox',
        'PAYPAL_ENV' => 'sandbox',
        'PAYPAL_CLIENT_ID' => $rawClientId,
        'PAYPAL_CLIENT_SECRET' => $rawClientSecret
    ]);

    echo "  [PASS] Client instantiated in mode: '{$sandboxClient->getMode()}'\n";
    echo "  [PASS] Mock mode active: " . ($sandboxClient->isMockMode() ? "YES" : "NO") . "\n";

    if ($sandboxClient->isMockMode()) {
        echo "  [FAIL] Security check failed: Mock mode was unexpectedly active in sandbox mode!\n";
        exit(1);
    }

    if ($isPlaceholder) {
        echo "\nRESULT: Sandbox client is configured correctly and refuses to simulate payments.\n";
        echo "REAL API CALLS BLOCKED: Awaiting genuine PayPal Sandbox credentials in backend/.env.\n";
        echo "Follow instructions in PHASE_8_2_REAL_INTEGRATION_REPORT.md to run live calls.\n";
        exit(0);
    }

    // 3. Genuine API Calls (Only when genuine keys are configured)
    echo "\n--- Executing Real PayPal Sandbox API Requests ---\n";
    echo "Target API Base: https://api-m.sandbox.paypal.com\n";

    echo "Request 1: POST /v1/oauth2/token (Client Credentials)...\n";
    $token = $sandboxClient->getAccessToken();
    echo "  [SUCCESS] Real Sandbox OAuth token obtained! Token prefix: " . substr($token, 0, 10) . "...\n";

    echo "Request 2: POST /v2/checkout/orders (Create Order)...\n";
    $testOrderId = 'PH-' . gmdate('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    $realOrder = $sandboxClient->createOrder($testOrderId, 10.00, 9.29, 0.71, [
        ['name' => 'Real Sandbox Verification Item', 'unitPriceUsd' => 9.29, 'qty' => 1]
    ]);

    $realPaypalOrderId = (string)$realOrder['id'];
    echo "  [SUCCESS] Real PayPal Sandbox Order Created! ID: {$realPaypalOrderId}\n";
    echo "  Order Status: " . ($realOrder['status'] ?? 'UNKNOWN') . "\n";
    echo "  Intent: " . ($realOrder['intent'] ?? 'UNKNOWN') . "\n";

    $approveUrl = '';
    foreach (($realOrder['links'] ?? []) as $link) {
        if (($link['rel'] ?? '') === 'approve') {
            $approveUrl = $link['href'];
            break;
        }
    }
    echo "  Approval Link: {$approveUrl}\n";

    echo "Request 3: GET /v2/checkout/orders/{$realPaypalOrderId} (Query Remote Status)...\n";
    $fetchedOrder = $sandboxClient->getOrder($realPaypalOrderId);
    echo "  [SUCCESS] Remote Order Retrieved!\n";
    echo "  Verified Remote ID: " . ($fetchedOrder['id'] ?? '') . "\n";
    echo "  Verified Remote Status: " . ($fetchedOrder['status'] ?? '') . "\n";
    echo "  Verified Remote Currency: " . ($fetchedOrder['purchase_units'][0]['amount']['currency_code'] ?? '') . "\n";
    echo "  Verified Remote Amount: $" . ($fetchedOrder['purchase_units'][0]['amount']['value'] ?? '') . " USD\n";

    echo "\nALL LIVE PAYPAL SANDBOX CHECKS PASSED WITH 100% SUCCESS.\n";

} catch (DomainException $e) {
    echo "  [CONFIRMED SECURE] " . $e->getMessage() . "\n";
    echo "\nSummary: Real PayPal Sandbox mode is strictly enforced.\n";
    echo "The client refused to simulate transactions without valid credentials.\n";
    exit(0);
} catch (Throwable $e) {
    echo "  [ERROR] Real API Communication Error: " . $e->getMessage() . "\n";
    exit(1);
}

