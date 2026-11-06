<?php
declare(strict_types=1);

/**
 * ParentingHub PayPal Sandbox Router
 * 
 * Endpoints:
 * - GET  /api/paypal/config
 * - POST /api/paypal/create-order
 * - POST /api/paypal/capture-order
 * - GET  /api/paypal/payment-status
 * - POST /api/paypal/webhook
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/paypal_service.php';

$subPath = substr($path, strlen('/paypal'));
$subPath = '/' . trim($subPath, '/');
if ($subPath === '//') $subPath = '/';

// ----------------------------------------------------------------------------
// 1. GET /api/paypal/config
// ----------------------------------------------------------------------------
if ($method === 'GET' && ($subPath === '/config' || $subPath === '/')) {
    $client = new PhPayPalClient();
    ph_json([
        'success' => true,
        'clientId' => $client->getClientId(),
        'env' => $client->getEnv(),
        'currency' => 'USD',
        'fxRate' => $client->getFxRate(),
        'deliveryFee' => 200.0,
        'isMock' => $client->isMockMode(),
        'brand' => 'ParentingHub Pakistan'
    ]);
}

// ----------------------------------------------------------------------------
// 2. POST /api/paypal/create-order
// ----------------------------------------------------------------------------
if ($method === 'POST' && $subPath === '/create-order') {
    $body = ph_body();
    $u = ph_session_user($pdo);
    $userId = $u ? (int)$u['id'] : null;

    try {
        $result = PhPayPalService::initiateOrder($pdo, $body, $userId);
        ph_json($result, 201);
    } catch (InvalidArgumentException $e) {
        ph_error($e->getMessage(), 422);
    } catch (DomainException $e) {
        ph_error($e->getMessage(), 409);
    } catch (Throwable $e) {
        error_log('PayPal create-order error: ' . $e->getMessage());
        ph_error('Could not create PayPal checkout order: ' . $e->getMessage(), 500);
    }
}

// ----------------------------------------------------------------------------
// 3. POST /api/paypal/capture-order
// ----------------------------------------------------------------------------
if ($method === 'POST' && $subPath === '/capture-order') {
    $body = ph_body();
    $orderId = trim((string)($body['orderId'] ?? ''));
    $paypalOrderId = trim((string)($body['paypalOrderId'] ?? ''));

    if ($orderId === '' || $paypalOrderId === '') {
        ph_error('Both orderId and paypalOrderId are required for capture.', 422);
    }

    try {
        $result = PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId);
        ph_json($result, 200);
    } catch (InvalidArgumentException $e) {
        ph_error($e->getMessage(), 422);
    } catch (DomainException $e) {
        ph_error($e->getMessage(), 409);
    } catch (Throwable $e) {
        error_log('PayPal capture-order error: ' . $e->getMessage());
        ph_error('Could not capture PayPal order: ' . $e->getMessage(), 500);
    }
}

// ----------------------------------------------------------------------------
// 4. GET /api/paypal/payment-status
// ----------------------------------------------------------------------------
if ($method === 'GET' && $subPath === '/payment-status') {
    $orderId = trim((string)($_GET['orderId'] ?? ''));
    // Security Best Practice: Prefer X-Payment-Token request header over URL query parameters
    $token = trim((string)($_SERVER['HTTP_X_PAYMENT_TOKEN'] ?? $_GET['token'] ?? ''));
    if ($orderId === '') {
        ph_error('Order reference is required.', 422);
    }

    $u = ph_session_user($pdo);
    $userId = $u ? (int)$u['id'] : null;

    try {
        $result = PhPayPalService::getPaymentStatus($pdo, $orderId, $token !== '' ? $token : null, $userId);
        ph_json($result, 200);
    } catch (DomainException $e) {
        $code = $e->getCode() === 403 ? 403 : 404;
        ph_error($e->getMessage(), $code);
    } catch (Throwable $e) {
        error_log('PayPal payment-status error: ' . $e->getMessage());
        ph_error('Could not fetch payment status.', 500);
    }
}

// ----------------------------------------------------------------------------
// 5. POST /api/paypal/cancel-order
// ----------------------------------------------------------------------------
if ($method === 'POST' && ($subPath === '/cancel-order' || $subPath === '/cancel')) {
    $body = ph_body();
    $orderId = trim((string)($body['orderId'] ?? ''));
    $token = trim((string)($body['token'] ?? $_SERVER['HTTP_X_PAYMENT_TOKEN'] ?? ''));
    if ($orderId === '') {
        ph_error('Order reference is required.', 422);
    }

    $u = ph_session_user($pdo);
    $userId = $u ? (int)$u['id'] : null;

    try {
        $result = PhPayPalService::cancelOrder($pdo, $orderId, $token !== '' ? $token : null, $userId);
        ph_json($result, 200);
    } catch (DomainException $e) {
        $code = $e->getCode() === 403 ? 403 : 409;
        ph_error($e->getMessage(), $code);
    } catch (Throwable $e) {
        error_log('PayPal cancel-order error: ' . $e->getMessage());
        ph_error('Could not cancel order.', 500);
    }
}

// ----------------------------------------------------------------------------
// 6. POST /api/paypal/webhook
// ----------------------------------------------------------------------------
if ($method === 'POST' && $subPath === '/webhook') {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $rawBody = file_get_contents('php://input') ?: '';

    try {
        $result = PhPayPalService::handleWebhook($pdo, $headers, $rawBody);
        ph_json($result, 200);
    } catch (InvalidArgumentException $e) {
        ph_error($e->getMessage(), 400);
    } catch (Throwable $e) {
        error_log('PayPal webhook error: ' . $e->getMessage());
        ph_error('Webhook processing error.', 500);
    }
}

ph_error('Unknown PayPal endpoint.', 404);

