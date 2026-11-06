<?php
declare(strict_types=1);

/**
 * ParentingHub AI Router
 * 
 * Dispatches:
 * POST /api/ai/assistant
 * POST /api/ai/recommend
 * POST /api/ai/compare
 * POST /api/ai/cart/propose
 * POST /api/ai/cart/approve
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/rate_limiter.php';
require_once __DIR__ . '/ai_service.php';
require_once __DIR__ . '/cart_proposals.php';

// Retrieve clean path relative to /ai/
$subPath = substr($path, 3); // strip '/ai'
$subPath = '/' . ltrim($subPath, '/');

if ($method !== 'POST') {
    ph_error('Method not allowed. AI endpoints accept POST requests only.', 405);
}

// Global AI rate limit: max 40 calls per minute per IP
ph_require_rate_limit($pdo, 'ai_global', 40, 60);

// ----------------------------------------------------------------------------
// 1. POST /api/ai/assistant
// ----------------------------------------------------------------------------
if ($subPath === '/assistant') {
    ph_require_rate_limit($pdo, 'ai_assistant', 25, 60);
    $body = ph_body();
    
    $message = trim((string)($body['message'] ?? $body['query'] ?? ''));
    if ($message === '') {
        ph_error('Please enter a shopping question or product request.', 422);
    }

    $history = is_array($body['conversationHistory'] ?? null) ? $body['conversationHistory'] : [];
    $context = is_array($body['context'] ?? null) ? $body['context'] : [];

    try {
        $response = PhAiService::runAssistant($pdo, $message, $history, $context);
        ph_json($response);
    } catch (InvalidArgumentException $e) {
        ph_error($e->getMessage(), 422);
    } catch (DomainException $e) {
        ph_error($e->getMessage(), 409);
    } catch (Throwable $e) {
        error_log('AI Assistant error: ' . $e->getMessage());
        ph_error('Unable to complete AI assistant request. Please try again.', 500);
    }
}

// ----------------------------------------------------------------------------
// 2. POST /api/ai/recommend
// ----------------------------------------------------------------------------
if ($subPath === '/recommend') {
    ph_require_rate_limit($pdo, 'ai_recommend', 30, 60);
    $body = ph_body();

    $ageMonths = isset($body['childAgeMonths']) && $body['childAgeMonths'] !== '' ? (int)$body['childAgeMonths'] : (isset($body['ageMonths']) ? (int)$body['ageMonths'] : null);
    $budget = isset($body['maxBudget']) && (float)$body['maxBudget'] > 0 ? (float)$body['maxBudget'] : (isset($body['budget']) ? (float)$body['budget'] : null);
    $category = !empty($body['category']) ? trim((string)$body['category']) : null;
    $requirement = trim((string)($body['requirement'] ?? $body['message'] ?? ''));

    try {
        $response = PhAiService::runRecommendations($pdo, $ageMonths, $budget, $category, $requirement);
        ph_json($response);
    } catch (InvalidArgumentException $e) {
        ph_error($e->getMessage(), 422);
    } catch (Throwable $e) {
        error_log('AI Recommendation error: ' . $e->getMessage());
        ph_error('Unable to generate recommendations. Please try again.', 500);
    }
}

// ----------------------------------------------------------------------------
// 3. POST /api/ai/compare
// ----------------------------------------------------------------------------
if ($subPath === '/compare') {
    ph_require_rate_limit($pdo, 'ai_compare', 30, 60);
    $body = ph_body();

    $productIds = $body['productIds'] ?? $body['ids'] ?? [];
    if (!is_array($productIds) || count($productIds) < 2) {
        ph_error('Please select at least two product IDs to compare.', 422);
    }

    try {
        $response = PhAiService::runComparison($pdo, $productIds);
        ph_json($response);
    } catch (InvalidArgumentException $e) {
        ph_error($e->getMessage(), 422);
    } catch (DomainException $e) {
        ph_error($e->getMessage(), 409);
    } catch (Throwable $e) {
        error_log('AI Comparison error: ' . $e->getMessage());
        ph_error('Unable to generate comparison. Please check product IDs.', 500);
    }
}

// ----------------------------------------------------------------------------
// 4. POST /api/ai/cart/propose
// ----------------------------------------------------------------------------
if ($subPath === '/cart/propose') {
    ph_require_rate_limit($pdo, 'ai_cart_propose', 20, 60);
    $body = ph_body();
    $items = $body['items'] ?? [];

    if (!is_array($items) || empty($items)) {
        // Also support passing an array of product IDs shorthand [{id: "..."}]
        $ids = $body['productIds'] ?? [];
        if (is_array($ids) && !empty($ids)) {
            $items = array_map(fn($id) => ['id' => $id, 'qty' => 1], $ids);
        }
    }

    if (empty($items)) {
        ph_error('One or more product items are required to propose a cart.', 422);
    }

    $token = $_COOKIE[PH_SESSION_COOKIE] ?? '';
    $sessionTokenHash = !empty($token) ? hash('sha256', $token) : (!empty($_SERVER['HTTP_X_SESSION_ID']) ? hash('sha256', (string)$_SERVER['HTTP_X_SESSION_ID']) : null);
    $u = ph_session_user($pdo);
    $userId = $u ? (int)$u['id'] : null;

    try {
        $proposal = PhCartProposalEngine::createProposal($pdo, $items, $userId, $sessionTokenHash);
        ph_json(['success' => true] + $proposal, 201);
    } catch (InvalidArgumentException $e) {
        ph_error($e->getMessage(), 422);
    } catch (DomainException $e) {
        ph_error($e->getMessage(), 409);
    } catch (Throwable $e) {
        error_log('AI Cart Propose error: ' . $e->getMessage());
        ph_error('Could not generate cart proposal.', 500);
    }
}

// ----------------------------------------------------------------------------
// 5. POST /api/ai/cart/approve
// ----------------------------------------------------------------------------
if ($subPath === '/cart/approve') {
    ph_require_rate_limit($pdo, 'ai_cart_approve', 20, 60);
    $body = ph_body();

    $proposalId = trim((string)($body['proposalId'] ?? ''));
    if ($proposalId === '') {
        ph_error('Proposal reference is required.', 422);
    }

    $customerApprovedItems = isset($body['approvedItems']) && is_array($body['approvedItems']) 
        ? $body['approvedItems'] 
        : null;

    $allowPriceUpdate = !empty($body['allowPriceUpdate']);
    $idempotentRecovery = !empty($body['idempotentRecovery']) || !empty($body['retry']);

    $token = $_COOKIE[PH_SESSION_COOKIE] ?? '';
    $sessionTokenHash = !empty($token) ? hash('sha256', $token) : (!empty($_SERVER['HTTP_X_SESSION_ID']) ? hash('sha256', (string)$_SERVER['HTTP_X_SESSION_ID']) : null);
    $u = ph_session_user($pdo);
    $userId = $u ? (int)$u['id'] : null;

    try {
        $result = PhCartProposalEngine::approveProposal($pdo, $proposalId, $customerApprovedItems, $userId, $sessionTokenHash, $allowPriceUpdate, $idempotentRecovery);
        ph_json($result);
    } catch (InvalidArgumentException $e) {
        ph_error($e->getMessage(), 422);
    } catch (DomainException $e) {
        $status = str_starts_with($e->getMessage(), 'Unauthorized') ? 403 : 409;
        ph_error($e->getMessage(), $status);
    } catch (Throwable $e) {
        error_log('AI Cart Approve error: ' . $e->getMessage());
        ph_error('Could not approve cart proposal.', 500);
    }
}

ph_error('Unknown AI endpoint.', 404);

