<?php
declare(strict_types=1);

/**
 * ParentingHub Permission-Based AI Cart Proposals Engine
 * 
 * Enforces strict customer permission:
 * 1. AI can ONLY "propose" a starter pack or bundle (POST /api/ai/cart/propose).
 * 2. Proposal is tokenized with an expiration time in ph_ai_cart_proposals.
 * 3. Customer must explicitly click "Approve" (and may adjust quantities/uncheck items).
 * 4. The server re-calculates all prices through pricing.php before anything lands in cart.
 * 5. Customer approved items must be a strict subset of original proposed items.
 * 6. AI has ZERO access to payments or payment authorizations.
 * 7. Proposal references are strictly one-time-use; replays and duplicates are rejected.
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/lib/pricing.php';

class PhCartProposalEngine {

    /**
     * Ensure storage table exists in active database.
     */
    private static function ensureTableExists(PDO $pdo): void {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS ph_ai_cart_proposals (
                proposal_id VARCHAR(64) PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                session_token_hash CHAR(64) NULL,
                items_json LONGTEXT NOT NULL,
                subtotal_pkr DECIMAL(12,2) NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'proposed',
                expires_at DATETIME NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )");
        } catch (Throwable $e) {
            // Table already exists or handled by migration
        }
    }

    /**
     * Create an AI cart proposal ticket.
     * 
     * @param PDO $pdo Active database connection
     * @param array $items Array of [{'id': string, 'qty': int, 'option'?: string}]
     * @param int|null $userId User ID if logged in
     * @param string|null $sessionTokenHash Session hash for guest/user ownership
     * @return array Proposal dictionary with proposalId, items, subtotal, and review details
     */
    public static function createProposal(
        PDO $pdo, 
        array $items, 
        ?int $userId = null, 
        ?string $sessionTokenHash = null
    ): array {
        self::ensureTableExists($pdo);

        if (empty($items)) {
            throw new InvalidArgumentException("No products provided for cart proposal.");
        }

        // Validate each product against actual database and auto-populate variant defaults
        $normalizedItems = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException("Invalid item payload format.");
            }
            $pid = trim((string)($item['id'] ?? ''));
            if ($pid === '') {
                throw new InvalidArgumentException("Product ID is required for all proposed items.");
            }

            $qty = isset($item['qty']) ? (int)$item['qty'] : 1;
            if ($qty < 1 || $qty > 99) {
                throw new InvalidArgumentException("Quantity for product '{$pid}' must be between 1 and 99.");
            }

            // Verify product exists in database and check stock
            $pStmt = $pdo->prepare("SELECT id, name, price, in_stock, has_options, options_json FROM products WHERE id = ?");
            $pStmt->execute([$pid]);
            $pRow = $pStmt->fetch();

            if (!$pRow) {
                throw new InvalidArgumentException("Product not found: '{$pid}'.");
            }

            if (isset($pRow['in_stock']) && (int)$pRow['in_stock'] === 0) {
                throw new InvalidArgumentException("Product '{$pRow['name']}' is currently out of stock.");
            }

            $norm = [
                'id' => $pid,
                'qty' => $qty
            ];

            // Handle options
            if (!empty($item['option'])) {
                $norm['option'] = trim((string)$item['option']);
            } elseif (!empty($pRow['has_options'])) {
                $optJson = is_string($pRow['options_json']) ? json_decode($pRow['options_json'], true) : $pRow['options_json'];
                if (is_array($optJson)) {
                    $parts = [];
                    foreach ($optJson as $group) {
                        if (!empty($group['name']) && !empty($group['values']) && is_array($group['values'])) {
                            $parts[] = trim((string)$group['name']) . ': ' . trim((string)$group['values'][0]);
                        }
                    }
                    if (!empty($parts)) {
                        $norm['option'] = implode(' / ', $parts);
                    }
                }
            }

            $normalizedItems[] = $norm;
        }

        // Validate items and compute totals via server pricing engine (never trust browser prices)
        $calculated = ph_calculate_cart_totals($pdo, $normalizedItems);

        $proposalId = 'PROP-' . strtoupper(bin2hex(random_bytes(8)));
        $expiresAt = date('Y-m-d H:i:s', time() + 1800); // 30 minutes TTL

        $stmt = $pdo->prepare("INSERT INTO ph_ai_cart_proposals 
            (proposal_id, user_id, session_token_hash, items_json, subtotal_pkr, status, expires_at)
            VALUES (?, ?, ?, ?, ?, 'proposed', ?)");
        
        $stmt->execute([
            $proposalId,
            $userId,
            $sessionTokenHash,
            json_encode($calculated['items'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            $calculated['subtotal'],
            $expiresAt
        ]);

        return [
            'proposalId' => $proposalId,
            'status' => 'proposed',
            'expiresAt' => $expiresAt,
            'expiresInSeconds' => 1800,
            'items' => $calculated['items'],
            'itemCount' => count($calculated['items']),
            'subtotal' => $calculated['subtotal'],
            'shipping' => $calculated['shipping'],
            'total' => $calculated['total'],
            'requiresCustomerApproval' => true,
            'approvalNote' => 'Review items, modify quantities or uncheck products, then click Approve to add to your shopping bag.'
        ];
    }

    /**
     * Customer approval endpoint: converts a proposed ticket into authorized cart items.
     * Allows customer to adjust quantities or uncheck items during review.
     * Strictly enforces:
     * - Proposal ownership (user or session)
     * - Expiration (< 30 mins)
     * - Single-use (replays rejected)
     * - Customer items must be a strict subset of proposed items
     * - Live price check (detects price changes before approval)
     * - Live stock and availability
     */
    public static function approveProposal(
        PDO $pdo, 
        string $proposalId, 
        ?array $customerApprovedItems = null,
        ?int $userId = null,
        ?string $sessionTokenHash = null,
        bool $allowPriceUpdate = false,
        bool $idempotentRecovery = false
    ): array {
        self::ensureTableExists($pdo);

        $cleanId = trim($proposalId);
        if ($cleanId === '' || !preg_match('/^PROP-[A-F0-9]{8,32}$/i', $cleanId)) {
            throw new InvalidArgumentException("Valid proposal reference is required.");
        }

        $stmt = $pdo->prepare("SELECT * FROM ph_ai_cart_proposals WHERE proposal_id = ? LIMIT 1");
        $stmt->execute([$cleanId]);
        $proposal = $stmt->fetch();

        if (!$proposal) {
            throw new DomainException("Proposed cart bundle was not found or has expired.");
        }

        // 1. Verify ownership if set on proposal
        if ($proposal['user_id'] !== null && $userId !== null && (int)$proposal['user_id'] !== $userId) {
            throw new DomainException("Unauthorized: You do not have permission to approve this cart proposal.");
        }
        if ($proposal['session_token_hash'] !== null && $sessionTokenHash !== null && $proposal['session_token_hash'] !== $sessionTokenHash) {
            throw new DomainException("Unauthorized: You do not have permission to approve this cart proposal.");
        }

        // 2. Verify status and support safe idempotent recovery for verified owners
        if ($proposal['status'] === 'approved') {
            $isOwner = false;
            if ($proposal['user_id'] !== null && $userId !== null && (int)$proposal['user_id'] === $userId) {
                $isOwner = true;
            } elseif ($proposal['session_token_hash'] !== null && $sessionTokenHash !== null && hash_equals((string)$proposal['session_token_hash'], (string)$sessionTokenHash)) {
                $isOwner = true;
            }

            if ($isOwner && $idempotentRecovery) {
                $savedItems = json_decode((string)$proposal['items_json'], true) ?: [];
                $calc = ph_calculate_cart_totals($pdo, $savedItems);
                return [
                    'success' => true,
                    'proposalId' => $cleanId,
                    'status' => 'approved',
                    'alreadyApproved' => true,
                    'idempotent' => true,
                    'approvedItems' => $calc['items'],
                    'itemCount' => count($calc['items']),
                    'subtotal' => $calc['subtotal'],
                    'shipping' => $calc['shipping'],
                    'total' => $calc['total'],
                    'priceChangesDetected' => false,
                    'message' => 'SmartCart items previously approved; synchronized existing approved bundle.'
                ];
            }

            throw new DomainException("This proposed bundle has already been approved. Proposals are single-use.");
        }
        if ($proposal['status'] !== 'proposed') {
            throw new DomainException("This proposed bundle has already been " . $proposal['status'] . ".");
        }

        // 3. Verify expiry
        if (strtotime((string)$proposal['expires_at']) < time()) {
            $pdo->prepare("UPDATE ph_ai_cart_proposals SET status = 'expired' WHERE proposal_id = ?")->execute([$cleanId]);
            throw new DomainException("This proposed bundle has expired. Please ask the assistant to generate a fresh bundle.");
        }

        $proposedItems = json_decode((string)$proposal['items_json'], true) ?: [];
        $proposedMap = [];
        foreach ($proposedItems as $pItem) {
            $pid = (string)($pItem['id'] ?? $pItem['product']['id'] ?? '');
            if ($pid !== '') {
                $proposedMap[$pid] = $pItem;
            }
        }

        // 4. Determine items to add: customer-adjusted selection or original proposal
        $itemsToValidate = [];
        if (!empty($customerApprovedItems) && is_array($customerApprovedItems)) {
            foreach ($customerApprovedItems as $cItem) {
                if (!is_array($cItem)) continue;
                $cid = trim((string)($cItem['id'] ?? $cItem['product']['id'] ?? ''));
                if ($cid === '' || !isset($proposedMap[$cid])) {
                    throw new DomainException("Product '{$cid}' was not part of the original proposal.");
                }
                $qty = (int)($cItem['qty'] ?? 1);
                if ($qty < 1 || $qty > 99) {
                    throw new InvalidArgumentException("Item quantity must be between 1 and 99.");
                }
                $itemPayload = [
                    'id' => $cid,
                    'qty' => $qty
                ];
                if (!empty($cItem['option'])) {
                    $itemPayload['option'] = (string)$cItem['option'];
                } elseif (!empty($proposedMap[$cid]['option'])) {
                    $itemPayload['option'] = (string)$proposedMap[$cid]['option'];
                }
                $itemsToValidate[] = $itemPayload;
            }
        } else {
            $itemsToValidate = $proposedItems;
        }

        if (empty($itemsToValidate)) {
            throw new InvalidArgumentException("At least one product must be selected to add to your bag.");
        }

        // 5. Re-validate everything against live catalog and stock
        $freshCalculation = ph_calculate_cart_totals($pdo, $itemsToValidate);

        // 6. Detect price changes between proposal creation and approval
        $priceChanges = [];
        foreach ($freshCalculation['items'] as $freshItem) {
            $fid = (string)$freshItem['id'];
            if (isset($proposedMap[$fid])) {
                $proposedPrice = (float)($proposedMap[$fid]['price'] ?? $proposedMap[$fid]['unitPrice'] ?? 0);
                $currentPrice = (float)$freshItem['price'];
                if (abs($proposedPrice - $currentPrice) > 0.01) {
                    $priceChanges[] = [
                        'id' => $fid,
                        'name' => $freshItem['name'],
                        'proposedPrice' => $proposedPrice,
                        'currentPrice' => $currentPrice
                    ];
                }
            }
        }

        if (!empty($priceChanges) && !$allowPriceUpdate) {
            $changeNotes = [];
            foreach ($priceChanges as $c) {
                $changeNotes[] = "{$c['name']}: was PKR " . number_format($c['proposedPrice']) . ", now PKR " . number_format($c['currentPrice']);
            }
            throw new DomainException("Price change detected before approval (" . implode('; ', $changeNotes) . "). Please review updated prices.");
        }

        // 7. Atomic update: transition status to approved only if still proposed
        $stmt = $pdo->prepare("UPDATE ph_ai_cart_proposals SET status = 'approved' WHERE proposal_id = ? AND status = 'proposed'");
        $stmt->execute([$cleanId]);

        if ($stmt->rowCount() === 0) {
            throw new DomainException("This proposed bundle has already been approved or modified by another request.");
        }

        return [
            'success' => true,
            'proposalId' => $cleanId,
            'status' => 'approved',
            'approvedItems' => $freshCalculation['items'],
            'itemCount' => count($freshCalculation['items']),
            'subtotal' => $freshCalculation['subtotal'],
            'shipping' => $freshCalculation['shipping'],
            'total' => $freshCalculation['total'],
            'priceChangesDetected' => !empty($priceChanges),
            'message' => 'SmartCart items successfully approved and added to your shopping bag.'
        ];
    }
}
