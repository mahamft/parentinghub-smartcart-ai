<?php
declare(strict_types=1);

/**
 * ParentingHub PayPal Orders v2 Business Service
 * 
 * Manages full end-to-end payment lifecycle:
 * 1. Server-side authoritative pricing & stock re-verification.
 * 2. High-entropy Order ID generation and pending order insertion.
 * 3. PayPal Order v2 creation & ph_payments ledger recording with unguessable access tokens.
 * 4. Idempotent payment capture with strict currency (USD), payee merchant ID, and amount verification.
 * 5. Payment status privacy protection (IDOR defense).
 * 6. Webhook listener with signature verification & deduplication via ph_payment_events.
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/lib/pricing.php';
require_once __DIR__ . '/paypal_client.php';

date_default_timezone_set('UTC');

class PhPayPalService {

    /**
     * Check whether the required payment and reservation tables and columns exist
     * without executing any DDL statements (safe to call inside active transactions).
     */
    public static function hasRequiredTables(PDO $pdo): bool {
        try {
            $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'sqlite') {
                $st = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name IN ('ph_payments', 'ph_payment_events', 'ph_inventory_reservations')");
                $tables = $st ? $st->fetchAll(PDO::FETCH_COLUMN) : [];
                if (count($tables) < 3) {
                    return false;
                }
                $colSt = $pdo->query("PRAGMA table_info(ph_inventory_reservations)");
                $cols = $colSt ? $colSt->fetchAll(PDO::FETCH_ASSOC) : [];
                $colNames = array_map(function($c) { return strtolower((string)$c['name']); }, $cols);
                return in_array('active_slot', $colNames, true);
            } else {
                $st = $pdo->query("SHOW TABLES");
                $tables = $st ? $st->fetchAll(PDO::FETCH_COLUMN) : [];
                $lower = array_map('strtolower', $tables);
                if (!in_array('ph_payments', $lower, true) || 
                    !in_array('ph_payment_events', $lower, true) || 
                    !in_array('ph_inventory_reservations', $lower, true)) {
                    return false;
                }
                $colSt = $pdo->query("SHOW COLUMNS FROM ph_inventory_reservations LIKE 'active_slot'");
                return ($colSt && $colSt->fetch() !== false);
            }
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Ensure required payment, webhook event, and inventory reservation tables exist.
     * Independently verifies:
     *  1. active_slot column existence
     *  2. uq_active_slot unique index (with duplicate reconciliation before index creation)
     *  3. uq_order_product composite unique index
     * Returns an audit array of actions taken and surfaces errors via error_log.
     */
    public static function ensureTables(PDO $pdo): array {
        if ($pdo->inTransaction()) {
            if (self::hasRequiredTables($pdo)) {
                return ['schema_already_verified'];
            }
            throw new LogicException("Cannot execute schema migration DDL inside an active transaction. Call PhPayPalService::ensureTables(\$pdo) before beginTransaction().");
        }
        $actions = [];
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        try {
            if ($driver === 'sqlite') {
                $pdo->exec("CREATE TABLE IF NOT EXISTS ph_payments (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    order_id TEXT NOT NULL,
                    provider TEXT NOT NULL DEFAULT 'paypal',
                    paypal_order_id TEXT UNIQUE NOT NULL,
                    payment_token TEXT,
                    capture_id TEXT UNIQUE,
                    currency TEXT NOT NULL DEFAULT 'USD',
                    amount_usd REAL NOT NULL,
                    amount_pkr REAL NOT NULL,
                    fx_rate REAL NOT NULL,
                    status TEXT NOT NULL DEFAULT 'CREATED',
                    payer_email TEXT,
                    raw_response TEXT,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                )");
                $pdo->exec("CREATE TABLE IF NOT EXISTS ph_payment_events (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    event_id TEXT NOT NULL UNIQUE,
                    event_type TEXT NOT NULL,
                    order_id TEXT,
                    verified INTEGER NOT NULL DEFAULT 1,
                    payload TEXT NOT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                )");
                $pdo->exec("CREATE TABLE IF NOT EXISTS ph_inventory_reservations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    order_id TEXT NOT NULL,
                    product_id TEXT NOT NULL,
                    active_slot TEXT UNIQUE,
                    qty INTEGER NOT NULL DEFAULT 1,
                    status TEXT NOT NULL DEFAULT 'RESERVED',
                    expires_at DATETIME NOT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE (order_id, product_id)
                )");
            } else {
                $pdo->exec("CREATE TABLE IF NOT EXISTS ph_payments (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    order_id VARCHAR(32) NOT NULL,
                    provider VARCHAR(20) NOT NULL DEFAULT 'paypal',
                    paypal_order_id VARCHAR(64) NOT NULL UNIQUE,
                    payment_token VARCHAR(64) DEFAULT NULL,
                    capture_id VARCHAR(64) DEFAULT NULL UNIQUE,
                    currency VARCHAR(3) NOT NULL DEFAULT 'USD',
                    amount_usd DECIMAL(10,2) NOT NULL,
                    amount_pkr DECIMAL(12,2) NOT NULL,
                    fx_rate DECIMAL(10,4) NOT NULL,
                    status VARCHAR(30) NOT NULL DEFAULT 'CREATED',
                    payer_email VARCHAR(190) DEFAULT NULL,
                    raw_response LONGTEXT DEFAULT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_pay_order (order_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                $pdo->exec("CREATE TABLE IF NOT EXISTS ph_payment_events (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    event_id VARCHAR(100) NOT NULL UNIQUE,
                    event_type VARCHAR(80) NOT NULL,
                    order_id VARCHAR(32) DEFAULT NULL,
                    verified TINYINT(1) NOT NULL DEFAULT 1,
                    payload LONGTEXT NOT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_ev_order (order_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

                $pdo->exec("CREATE TABLE IF NOT EXISTS ph_inventory_reservations (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    order_id VARCHAR(32) NOT NULL,
                    product_id VARCHAR(64) NOT NULL,
                    active_slot VARCHAR(64) DEFAULT NULL,
                    qty INT NOT NULL DEFAULT 1,
                    status VARCHAR(20) NOT NULL DEFAULT 'RESERVED',
                    expires_at DATETIME NOT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_active_slot (active_slot),
                    UNIQUE KEY uq_order_product (order_id, product_id),
                    INDEX idx_res_order (order_id),
                    INDEX idx_res_prod (product_id, status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            }

            // Independent check 1: Column active_slot existence
            $hasActiveSlotCol = false;
            if ($driver === 'sqlite') {
                $colsStmt = $pdo->query("PRAGMA table_info(ph_inventory_reservations)");
                while ($c = $colsStmt->fetch(PDO::FETCH_ASSOC)) {
                    if (strcasecmp((string)$c['name'], 'active_slot') === 0) {
                        $hasActiveSlotCol = true;
                        break;
                    }
                }
            } else {
                $colsStmt = $pdo->query("SHOW COLUMNS FROM ph_inventory_reservations LIKE 'active_slot'");
                $hasActiveSlotCol = ($colsStmt && $colsStmt->fetch() !== false);
            }

            if (!$hasActiveSlotCol) {
                try {
                    if ($driver === 'sqlite') {
                        $pdo->exec("ALTER TABLE ph_inventory_reservations ADD COLUMN active_slot TEXT DEFAULT NULL");
                    } else {
                        $pdo->exec("ALTER TABLE ph_inventory_reservations ADD COLUMN active_slot VARCHAR(64) DEFAULT NULL");
                    }
                    $actions[] = 'added_column_active_slot';
                } catch (Throwable $colEx) {
                    error_log("PhPayPalService migration error (add active_slot): " . $colEx->getMessage());
                    $actions[] = 'error_add_column_active_slot: ' . $colEx->getMessage();
                }
            }

            // Independent check 2: Unique index on active_slot
            $hasActiveSlotIdx = false;
            if ($driver === 'sqlite') {
                $idxList = $pdo->query("PRAGMA index_list(ph_inventory_reservations)")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($idxList as $idx) {
                    if (empty($idx['unique'])) continue;
                    $idxName = (string)$idx['name'];
                    $info = $pdo->query("PRAGMA index_info(" . $pdo->quote($idxName) . ")")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($info as $col) {
                        if (strcasecmp((string)$col['name'], 'active_slot') === 0) {
                            $hasActiveSlotIdx = true;
                            break 2;
                        }
                    }
                }
            } else {
                $idxList = $pdo->query("SHOW INDEX FROM ph_inventory_reservations WHERE Non_unique = 0")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($idxList as $idx) {
                    if (strcasecmp((string)($idx['Column_name'] ?? ''), 'active_slot') === 0) {
                        $hasActiveSlotIdx = true;
                        break;
                    }
                }
            }

            if (!$hasActiveSlotIdx) {
                // Reconcile existing duplicate active_slot values before unique index creation (Scenario 5)
                try {
                    $dupStmt = $pdo->query("SELECT active_slot, COUNT(*) as cnt 
                        FROM ph_inventory_reservations 
                        WHERE active_slot IS NOT NULL AND active_slot != '' 
                        GROUP BY active_slot 
                        HAVING cnt > 1");
                    $dups = $dupStmt ? $dupStmt->fetchAll(PDO::FETCH_ASSOC) : [];
                    if (!empty($dups)) {
                        error_log("PhPayPalService migration warning: Duplicate active_slot values detected (" . count($dups) . " slots with duplicates). Reconciling duplicates before unique index creation.");
                        foreach ($dups as $dup) {
                            $slotVal = (string)$dup['active_slot'];
                            $rowsStmt = $pdo->prepare("SELECT id, status FROM ph_inventory_reservations 
                                WHERE active_slot = ? 
                                ORDER BY CASE WHEN status = 'RESERVED' THEN 1 ELSE 0 END DESC, id DESC");
                            $rowsStmt->execute([$slotVal]);
                            $slotRows = $rowsStmt->fetchAll(PDO::FETCH_ASSOC);
                            // Keep first (active/newest), set remainder active_slot to NULL
                            $first = true;
                            foreach ($slotRows as $sr) {
                                if ($first) {
                                    $first = false;
                                    continue;
                                }
                                $pdo->prepare("UPDATE ph_inventory_reservations 
                                    SET active_slot = NULL, status = 'RELEASED', updated_at = CURRENT_TIMESTAMP 
                                    WHERE id = ?")
                                    ->execute([$sr['id']]);
                            }
                        }
                        $actions[] = 'reconciled_duplicate_active_slots';
                    }

                    if ($driver === 'sqlite') {
                        $pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS uq_inv_active_slot ON ph_inventory_reservations (active_slot)");
                    } else {
                        $pdo->exec("ALTER TABLE ph_inventory_reservations ADD UNIQUE KEY uq_active_slot (active_slot)");
                    }
                    $actions[] = 'created_index_uq_active_slot';
                } catch (Throwable $idxEx) {
                    error_log("PhPayPalService migration error (create uq_active_slot): " . $idxEx->getMessage());
                    $actions[] = 'error_create_index_uq_active_slot: ' . $idxEx->getMessage();
                }
            }

            // Independent check 3: Unique constraint on (order_id, product_id)
            $hasOrderProdIdx = false;
            if ($driver === 'sqlite') {
                $idxList = $pdo->query("PRAGMA index_list(ph_inventory_reservations)")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($idxList as $idx) {
                    if (empty($idx['unique'])) continue;
                    $idxName = (string)$idx['name'];
                    $info = $pdo->query("PRAGMA index_info(" . $pdo->quote($idxName) . ")")->fetchAll(PDO::FETCH_ASSOC);
                    $cols = array_map(function($c) { return strtolower((string)$c['name']); }, $info);
                    if (in_array('order_id', $cols, true) && in_array('product_id', $cols, true)) {
                        $hasOrderProdIdx = true;
                        break;
                    }
                }
            } else {
                $idxList = $pdo->query("SHOW INDEX FROM ph_inventory_reservations WHERE Key_name = 'uq_order_product'")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($idxList)) {
                    $hasOrderProdIdx = true;
                }
            }

            if (!$hasOrderProdIdx) {
                try {
                    $dupSt = $pdo->query("SELECT order_id, product_id, COUNT(*) as cnt 
                        FROM ph_inventory_reservations 
                        GROUP BY order_id, product_id 
                        HAVING cnt > 1");
                    $dups = $dupSt ? $dupSt->fetchAll(PDO::FETCH_ASSOC) : [];
                    if (!empty($dups)) {
                        error_log("PhPayPalService migration warning: Duplicate (order_id, product_id) detected. Reconciling before unique index creation.");
                        foreach ($dups as $dup) {
                            $rStmt = $pdo->prepare("SELECT id FROM ph_inventory_reservations 
                                WHERE order_id = ? AND product_id = ? ORDER BY id DESC");
                            $rStmt->execute([$dup['order_id'], $dup['product_id']]);
                            $rows = $rStmt->fetchAll(PDO::FETCH_ASSOC);
                            array_shift($rows);
                            foreach ($rows as $oldRow) {
                                $pdo->prepare("DELETE FROM ph_inventory_reservations WHERE id = ?")->execute([$oldRow['id']]);
                            }
                        }
                        $actions[] = 'reconciled_duplicate_order_products';
                    }

                    if ($driver === 'sqlite') {
                        $pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS uq_inv_order_prod ON ph_inventory_reservations (order_id, product_id)");
                    } else {
                        $pdo->exec("ALTER TABLE ph_inventory_reservations ADD UNIQUE KEY uq_order_product (order_id, product_id)");
                    }
                    $actions[] = 'created_index_uq_order_product';
                } catch (Throwable $idxEx) {
                    error_log("PhPayPalService migration error (create uq_order_product): " . $idxEx->getMessage());
                    $actions[] = 'error_create_index_uq_order_product: ' . $idxEx->getMessage();
                }
            }
        } catch (Throwable $e) {
            error_log("PhPayPalService ensureTables fatal error: " . $e->getMessage());
            $actions[] = 'fatal_error: ' . $e->getMessage();
        }
        return $actions;
    }

    /**
     * Safely reclaim expired active reservations whose checkout window passed.
     */
    public static function reclaimExpiredReservations(PDO $pdo): int {
        if (!$pdo->inTransaction()) {
            self::ensureTables($pdo);
        } elseif (!self::hasRequiredTables($pdo)) {
            throw new LogicException("Database schema must be initialized via PhPayPalService::ensureTables(\$pdo) before opening a transaction.");
        }
        $now = gmdate('Y-m-d H:i:s');
        $st = $pdo->prepare("UPDATE ph_inventory_reservations 
            SET status = 'RELEASED', active_slot = NULL, updated_at = CURRENT_TIMESTAMP 
            WHERE status = 'RESERVED' AND expires_at < ?");
        $st->execute([$now]);
        return $st->rowCount();
    }

    /**
     * Check if a product has an active, unexpired reservation held by another order.
     */
    public static function checkActiveReservation(PDO $pdo, string $productId, ?string $excludeOrderId = null): ?array {
        if (!$pdo->inTransaction()) {
            self::ensureTables($pdo);
        } elseif (!self::hasRequiredTables($pdo)) {
            throw new LogicException("Database schema must be initialized via PhPayPalService::ensureTables(\$pdo) before opening a transaction.");
        }
        self::reclaimExpiredReservations($pdo);

        $now = gmdate('Y-m-d H:i:s');
        $sql = "SELECT r.*, p.name as product_name 
                FROM ph_inventory_reservations r
                LEFT JOIN products p ON r.product_id = p.id
                WHERE r.active_slot = ? 
                  AND r.status = 'RESERVED'
                  AND r.expires_at >= ?";
        $params = [$productId, $now];
        if ($excludeOrderId !== null && $excludeOrderId !== '') {
            $sql .= " AND r.order_id != ?";
            $params[] = $excludeOrderId;
        }
        $sql .= " LIMIT 1";

        $st = $pdo->prepare($sql);
        $st->execute($params);
        $row = $st->fetch();
        return $row ?: null;
    }

    /**
     * Reserve inventory for an in-flight checkout with TTL and strict unique slot enforcement.
     */
    public static function reserveInventory(PDO $pdo, string $orderId, array $items, int $ttlSeconds = 900): void {
        if (!$pdo->inTransaction()) {
            self::ensureTables($pdo);
        } elseif (!self::hasRequiredTables($pdo)) {
            throw new LogicException("Database schema must be initialized via PhPayPalService::ensureTables(\$pdo) before opening a transaction.");
        }
        self::reclaimExpiredReservations($pdo);
        $expiresAt = gmdate('Y-m-d H:i:s', time() + $ttlSeconds);

        // 1. Aggregate quantities by product ID using addition (combining multiple order lines for same product)
        $aggregated = [];
        foreach ($items as $it) {
            $pid = trim((string)($it['id'] ?? ''));
            $qty = max(1, (int)($it['qty'] ?? 1));
            if ($pid === '') continue;
            if (!isset($aggregated[$pid])) {
                $aggregated[$pid] = 0;
            }
            $aggregated[$pid] += $qty;
        }

        foreach ($aggregated as $pid => $qty) {
            // Check if already actively reserved by another order
            $conflict = self::checkActiveReservation($pdo, $pid, $orderId);
            if ($conflict !== null) {
                $prodName = $conflict['product_name'] ?? $pid;
                throw new DomainException("Product '{$prodName}' is currently reserved by another customer in checkout.");
            }

            // Check if this order already holds the reservation (safe retry/recovery)
            $stMine = $pdo->prepare("SELECT id, status, qty FROM ph_inventory_reservations WHERE order_id = ? AND product_id = ? LIMIT 1");
            $stMine->execute([$orderId, $pid]);
            $myRes = $stMine->fetch();

            if ($myRes) {
                if ($myRes['status'] === 'RESERVED') {
                    $pdo->prepare("UPDATE ph_inventory_reservations SET qty = ?, expires_at = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                        ->execute([$qty, $expiresAt, $myRes['id']]);
                }
                continue;
            }

            // Atomically claim reservation slot
            // Database-level UNIQUE constraint on active_slot prevents simultaneous double reservation
            try {
                $st = $pdo->prepare("INSERT INTO ph_inventory_reservations 
                    (order_id, product_id, active_slot, qty, status, expires_at, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, 'RESERVED', ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
                $st->execute([$orderId, $pid, $pid, $qty, $expiresAt]);
            } catch (PDOException $e) {
                // Code 23000 = Integrity constraint violation (Duplicate key on active_slot or order_id+product_id)
                if ((string)$e->getCode() === '23000' || str_contains($e->getMessage(), 'UNIQUE') || str_contains($e->getMessage(), 'Duplicate')) {
                    // Check if it was an idempotent repeat of our own reservation
                    $stMine->execute([$orderId, $pid]);
                    if ($stMine->fetch()) {
                        continue;
                    }
                    throw new DomainException("Product '{$pid}' was just claimed by another customer in checkout. Reservation failed.");
                }
                throw $e;
            }
        }
    }

    /**
     * Commit reserved inventory upon verified payment capture.
     * Transitions status to COMMITTED and frees in-flight active_slot lock.
     * The row permanently remains status = 'COMMITTED' and cannot be released.
     */
    public static function commitInventoryReservation(PDO $pdo, string $orderId): void {
        if (!$pdo->inTransaction()) {
            self::ensureTables($pdo);
        } elseif (!self::hasRequiredTables($pdo)) {
            throw new LogicException("Database schema must be initialized via PhPayPalService::ensureTables(\$pdo) before opening a transaction.");
        }
        $st = $pdo->prepare("UPDATE ph_inventory_reservations 
            SET status = 'COMMITTED', active_slot = NULL, updated_at = CURRENT_TIMESTAMP 
            WHERE order_id = ? AND status = 'RESERVED'");
        $st->execute([$orderId]);
    }

    /**
     * Release reserved inventory upon cancellation, failure, or expiration.
     * Frees active_slot to NULL so other shoppers can reserve the product.
     * Crucially: COMMITTED rows are NEVER released by this method!
     */
    public static function releaseInventoryReservation(PDO $pdo, string $orderId): void {
        if (!$pdo->inTransaction()) {
            self::ensureTables($pdo);
        } elseif (!self::hasRequiredTables($pdo)) {
            throw new LogicException("Database schema must be initialized via PhPayPalService::ensureTables(\$pdo) before opening a transaction.");
        }
        $st = $pdo->prepare("UPDATE ph_inventory_reservations 
            SET status = 'RELEASED', active_slot = NULL, updated_at = CURRENT_TIMESTAMP 
            WHERE order_id = ? AND status = 'RESERVED'");
        $st->execute([$orderId]);
    }

    /**
     * Retrieve inventory reservations for an order.
     */
    public static function getReservations(PDO $pdo, string $orderId): array {
        if (!$pdo->inTransaction()) {
            self::ensureTables($pdo);
        } elseif (!self::hasRequiredTables($pdo)) {
            throw new LogicException("Database schema must be initialized via PhPayPalService::ensureTables(\$pdo) before opening a transaction.");
        }
        $st = $pdo->prepare("SELECT * FROM ph_inventory_reservations WHERE order_id = ? ORDER BY id ASC");
        $st->execute([$orderId]);
        return $st->fetchAll();
    }

    /**
     * Initiate a PayPal Order for checkout.
     * Re-calculates and verifies all prices, quantities, stock, and shipping on the server.
     */
    public static function initiateOrder(PDO $pdo, array $body, ?int $userId = null, ?PhPayPalClient $client = null): array {
        $client = $client ?? new PhPayPalClient();
        self::ensureTables($pdo);

        // 1. Validate Delivery Address
        $address = !empty($body['address']) && is_array($body['address'])
            ? $body['address']
            : (!empty($body['customer']) && is_array($body['customer']) ? $body['customer'] : []);

        if (empty($address)) {
            throw new InvalidArgumentException("Delivery address details are required.");
        }

        foreach (['name', 'phone', 'address', 'city'] as $field) {
            if (trim((string)($address[$field] ?? '')) === '') {
                throw new InvalidArgumentException("Please complete all required delivery address fields: {$field}.");
            }
        }

        if (strcasecmp(trim((string)$address['city']), 'Other') === 0) {
            throw new InvalidArgumentException("Please select your actual delivery city.");
        }

        $phone = preg_replace('/[^\d+]/', '', (string)$address['phone']);
        if (str_starts_with($phone, '+92')) $phone = '0' . substr($phone, 3);
        elseif (str_starts_with($phone, '0092')) $phone = '0' . substr($phone, 4);
        elseif (str_starts_with($phone, '92') && strlen($phone) === 12) $phone = '0' . substr($phone, 2);
        elseif (preg_match('/^3\d{9}$/', $phone)) $phone = '0' . $phone;

        if (!preg_match('/^03\d{9}$/', $phone) && !preg_match('/^\d{10,12}$/', $phone)) {
            throw new InvalidArgumentException("Enter a valid Pakistani mobile number (03XXXXXXXXX).");
        }

        if (!empty($address['email']) && !filter_var($address['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Enter a valid receipt email address.");
        }

        // 2. Validate Items & Compute Authoritative Pricing
        $requestedItems = $body['items'] ?? [];
        if (!is_array($requestedItems) || empty($requestedItems)) {
            throw new InvalidArgumentException("Shopping bag is empty.");
        }

        $couponCode = trim((string)($body['coupon'] ?? ''));
        $calculation = ph_calculate_cart_totals($pdo, $requestedItems, $couponCode !== '' ? $couponCode : null);

        $subtotalPkr = (float)$calculation['subtotal'];
        $shippingPkr = (float)$calculation['shipping'];
        $discountPkr = (float)$calculation['discount'];
        $totalPkr = (float)$calculation['total'];
        $appliedCoupon = $calculation['coupon'];
        $items = $calculation['items'];

        // 2b. Check active inventory reservations before initiating external PayPal checkout
        self::reclaimExpiredReservations($pdo);
        foreach ($items as $it) {
            $pid = (string)($it['id'] ?? '');
            if ($pid !== '') {
                $existing = self::checkActiveReservation($pdo, $pid);
                if ($existing !== null) {
                    $prodName = $existing['product_name'] ?? $it['name'] ?? $pid;
                    throw new DomainException("Product '{$prodName}' is currently reserved by another customer in checkout.");
                }
            }
        }

        // 3. FX Conversion & Dual-Currency Amounts
        $fxRate = $client->getFxRate();
        $subtotalUsd = ph_pkr_to_usd($subtotalPkr, $fxRate);
        $shippingUsd = ph_pkr_to_usd($shippingPkr, $fxRate);
        $discountUsd = ph_pkr_to_usd($discountPkr, $fxRate);
        $totalUsd = ph_pkr_to_usd($totalPkr, $fxRate);

        // Adjust item total breakdown if rounding discrepancy occurs
        $expectedSum = round(max(0.0, $subtotalUsd - $discountUsd) + $shippingUsd, 2);
        if ($expectedSum !== $totalUsd) {
            $subtotalUsd = round($totalUsd - $shippingUsd, 2);
        }

        // 4. Generate High-Entropy Order ID & Unguessable Payment Token
        $orderId = ph_generate_order_id();
        $paymentToken = bin2hex(random_bytes(32)); // 64 hex characters, 256 bits of entropy

        // 5. Prepare PayPal Line Items
        $paypalLineItems = [];
        foreach ($items as $it) {
            $unitUsd = ph_pkr_to_usd((float)$it['price'], $fxRate);
            $paypalLineItems[] = [
                'name' => (string)$it['name'],
                'unitPriceUsd' => $unitUsd,
                'qty' => (int)$it['qty']
            ];
        }

        // 6. Create PayPal Order v2 via API / Mock (executed outside database transaction to avoid open locks)
        $paypalOrder = $client->createOrder($orderId, $totalUsd, $subtotalUsd, $shippingUsd, $paypalLineItems);
        $paypalOrderId = (string)$paypalOrder['id'];

        $safeAddress = [
            'name' => mb_substr(trim((string)$address['name']), 0, 160),
            'phone' => $phone,
            'email' => mb_substr(trim((string)($address['email'] ?? '')), 0, 190),
            'address' => mb_substr(trim((string)$address['address']), 0, 500),
            'city' => mb_substr(trim((string)$address['city']), 0, 100),
            'notes' => mb_substr(trim((string)($address['notes'] ?? '')), 0, 1000)
        ];

        // 7. Atomic Transaction: Insert Order, Payment Record & Inventory Reservations
        $pdo->beginTransaction();
        try {
            $stOrder = $pdo->prepare("INSERT INTO ph_orders 
                (id, user_id, status, payment, subtotal, shipping, discount, total, coupon, address_json, items_json, created_at)
                VALUES (?, ?, 'Pending Payment', 'paypal', ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
            
            $stOrder->execute([
                $orderId,
                $userId,
                $subtotalPkr,
                $shippingPkr,
                $discountPkr,
                $totalPkr,
                $appliedCoupon,
                json_encode($safeAddress, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ]);

            $stPay = $pdo->prepare("INSERT INTO ph_payments
                (order_id, provider, paypal_order_id, payment_token, currency, amount_usd, amount_pkr, fx_rate, status, raw_response, created_at)
                VALUES (?, 'paypal', ?, ?, 'USD', ?, ?, ?, 'CREATED', ?, CURRENT_TIMESTAMP)");
            
            $stPay->execute([
                $orderId,
                $paypalOrderId,
                $paymentToken,
                $totalUsd,
                $totalPkr,
                $fxRate,
                json_encode($paypalOrder, JSON_UNESCAPED_SLASHES)
            ]);

            // Create inventory reservations with 15-minute TTL
            self::reserveInventory($pdo, $orderId, $items, 900);

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            // Orphan PayPal Order Mitigation:
            // If the PayPal order was successfully created at PayPal but local reservation failed,
            // persist an explicit CANCELLED audit trail so this PayPal Order ID can never proceed to capture.
            try {
                $pdo->prepare("INSERT INTO ph_orders 
                    (id, user_id, status, payment, subtotal, shipping, discount, total, coupon, address_json, items_json, created_at, updated_at)
                    VALUES (?, ?, 'Reservation Failed', 'paypal', ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)")
                    ->execute([
                        $orderId,
                        $userId,
                        $subtotalPkr,
                        $shippingPkr,
                        $discountPkr,
                        $totalPkr,
                        $appliedCoupon,
                        json_encode($safeAddress, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    ]);

                $pdo->prepare("INSERT INTO ph_payments
                    (order_id, provider, paypal_order_id, payment_token, currency, amount_usd, amount_pkr, fx_rate, status, raw_response, created_at, updated_at)
                    VALUES (?, 'paypal', ?, ?, 'USD', ?, ?, ?, 'CANCELLED', ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)")
                    ->execute([
                        $orderId,
                        $paypalOrderId,
                        $paymentToken,
                        $totalUsd,
                        $totalPkr,
                        $fxRate,
                        json_encode(['error' => $e->getMessage(), 'mitigated' => 'reservation_failure'], JSON_UNESCAPED_SLASHES)
                    ]);
            } catch (Throwable $auditEx) {
                error_log("Failed to record orphan mitigation record: " . $auditEx->getMessage());
            }

            throw $e;
        }

        return [
            'success' => true,
            'orderId' => $orderId,
            'paypalOrderId' => $paypalOrderId,
            'paymentToken' => $paymentToken,
            'status' => 'Pending Payment',
            'currency' => 'USD',
            'amountUsd' => $totalUsd,
            'amountPkr' => $totalPkr,
            'fxRate' => $fxRate,
            'items' => $items,
            'links' => $paypalOrder['links'] ?? [],
            'isMock' => $client->isMockMode(),
            'message' => 'PayPal sandbox order prepared successfully.'
        ];
    }

    /**
     * Capture an approved PayPal order and confirm the store order.
     * Enforces strict integrity checks and capture recovery reconciliation:
     * - Status must be 'COMPLETED'
     * - Currency must be 'USD'
     * - Captured amount must match expected amount in ph_payments
     * - Payee merchant ID must match configured merchant (if set)
     * - Never blindly issue duplicate capture: inspects remote status first
     * - Automatically reconciles if network response was previously lost after external capture
     * - Temporary network failures do not mark orders cancelled or failed prematurely
     * - Prevents duplicate captures & replay attacks
     */
    public static function captureOrder(PDO $pdo, string $orderId, string $paypalOrderId, ?PhPayPalClient $client = null): array {
        $cleanOrderId = trim($orderId);
        $cleanPaypalOrderId = trim($paypalOrderId);

        if ($cleanOrderId === '' || $cleanPaypalOrderId === '') {
            throw new InvalidArgumentException("Both order reference and PayPal order ID are required for capture.");
        }

        $client = $client ?? new PhPayPalClient();
        self::ensureTables($pdo);

        // 1. Fetch Order and Payment record from database
        $stOrder = $pdo->prepare("SELECT * FROM ph_orders WHERE id = ? LIMIT 1");
        $stOrder->execute([$cleanOrderId]);
        $order = $stOrder->fetch();

        if (!$order) {
            throw new DomainException("Order '{$cleanOrderId}' was not found.");
        }

        $stPay = $pdo->prepare("SELECT * FROM ph_payments WHERE order_id = ? AND paypal_order_id = ? LIMIT 1");
        $stPay->execute([$cleanOrderId, $cleanPaypalOrderId]);
        $payment = $stPay->fetch();

        if (!$payment) {
            throw new DomainException("No matching PayPal payment found for order '{$cleanOrderId}'.");
        }

        // 2. Local Idempotency Check: Already captured / completed locally
        if ($payment['status'] === 'COMPLETED' && $order['status'] === 'Confirmed') {
            return [
                'success' => true,
                'orderId' => $cleanOrderId,
                'paypalOrderId' => $cleanPaypalOrderId,
                'status' => 'Confirmed',
                'captureId' => (string)($payment['capture_id'] ?? ''),
                'amountUsd' => (float)$payment['amount_usd'],
                'amountPkr' => (float)$payment['amount_pkr'],
                'isDuplicate' => true,
                'reconciled' => false,
                'message' => 'Payment has already been captured and order is confirmed.'
            ];
        }

        if ($order['status'] === 'Cancelled' || $order['status'] === 'Reservation Failed' || $order['status'] === 'Reservation Expired' || $payment['status'] === 'CANCELLED' || $payment['status'] === 'DENIED' || $payment['status'] === 'EXPIRED') {
            throw new DomainException("This order has been cancelled or expired and cannot be paid.");
        }

        // 2b. Strict Per-Product Reservation, Quantity & Active Slot Validation:
        // Automatically reclaim any reservations that passed their expiry window
        self::reclaimExpiredReservations($pdo);

        $items = json_decode((string)$order['items_json'], true) ?: [];
        $requiredProductQuantities = [];
        foreach ($items as $it) {
            $pid = trim((string)($it['id'] ?? ''));
            $qty = max(1, (int)($it['qty'] ?? 1));
            if ($pid !== '') {
                if (!isset($requiredProductQuantities[$pid])) {
                    $requiredProductQuantities[$pid] = 0;
                }
                $requiredProductQuantities[$pid] += $qty;
            }
        }

        if (empty($requiredProductQuantities)) {
            throw new DomainException("Order '{$cleanOrderId}' contains no purchasable products.");
        }

        $now = gmdate('Y-m-d H:i:s');
        $stRes = $pdo->prepare("SELECT * FROM ph_inventory_reservations WHERE order_id = ?");
        $stRes->execute([$cleanOrderId]);
        $resRows = $stRes->fetchAll(PDO::FETCH_ASSOC);

        $reservationsByProduct = [];
        foreach ($resRows as $r) {
            $pid = (string)$r['product_id'];
            if (isset($reservationsByProduct[$pid])) {
                self::releaseInventoryReservation($pdo, $cleanOrderId);
                $pdo->prepare("UPDATE ph_orders SET status = 'Reservation Expired', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanOrderId]);
                $pdo->prepare("UPDATE ph_payments SET status = 'EXPIRED', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                throw new DomainException("Order '{$cleanOrderId}' has corrupt/duplicate reservation rows for product '{$pid}'. Payment capture aborted.");
            }
            $reservationsByProduct[$pid] = $r;
        }

        // Check if all lines are already COMMITTED (safe idempotent recovery path)
        $allCommitted = true;
        foreach ($requiredProductQuantities as $pid => $reqQty) {
            $r = $reservationsByProduct[$pid] ?? null;
            if (!$r || strtoupper((string)($r['status'] ?? '')) !== 'COMMITTED') {
                $allCommitted = false;
                break;
            }
        }

        if (!$allCommitted) {
            // Every required product line MUST have a valid, unexpired RESERVED row owning the correct active_slot and sufficient qty
            foreach ($requiredProductQuantities as $pid => $reqQty) {
                $r = $reservationsByProduct[$pid] ?? null;
                if (!$r) {
                    self::releaseInventoryReservation($pdo, $cleanOrderId);
                    $pdo->prepare("UPDATE ph_orders SET status = 'Reservation Expired', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanOrderId]);
                    $pdo->prepare("UPDATE ph_payments SET status = 'EXPIRED', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                    throw new DomainException("Order '{$cleanOrderId}' is missing an inventory reservation for product '{$pid}'. Payment capture aborted.");
                }

                $rStatus = strtoupper((string)($r['status'] ?? ''));
                if ($rStatus === 'RELEASED') {
                    self::releaseInventoryReservation($pdo, $cleanOrderId);
                    $pdo->prepare("UPDATE ph_orders SET status = 'Reservation Expired', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanOrderId]);
                    $pdo->prepare("UPDATE ph_payments SET status = 'EXPIRED', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                    throw new DomainException("Order '{$cleanOrderId}' product '{$pid}' reservation has been RELEASED. Payment capture aborted.");
                }

                if ($rStatus !== 'RESERVED' && $rStatus !== 'COMMITTED') {
                    self::releaseInventoryReservation($pdo, $cleanOrderId);
                    $pdo->prepare("UPDATE ph_orders SET status = 'Reservation Expired', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanOrderId]);
                    $pdo->prepare("UPDATE ph_payments SET status = 'EXPIRED', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                    throw new DomainException("Order '{$cleanOrderId}' product '{$pid}' reservation has invalid status '{$rStatus}'. Payment capture aborted.");
                }

                if ($rStatus === 'RESERVED') {
                    // 1. Must own the exact active_slot matching product_id
                    $activeSlot = (string)($r['active_slot'] ?? '');
                    if ($activeSlot === '' || $activeSlot !== $pid) {
                        self::releaseInventoryReservation($pdo, $cleanOrderId);
                        $pdo->prepare("UPDATE ph_orders SET status = 'Reservation Expired', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanOrderId]);
                        $pdo->prepare("UPDATE ph_payments SET status = 'EXPIRED', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                        throw new DomainException("Order '{$cleanOrderId}' product '{$pid}' reservation has lost its active slot lock (active_slot is NULL or mismatched). Payment capture aborted.");
                    }

                    // 2. Must be unexpired
                    if ((string)$r['expires_at'] < $now) {
                        self::releaseInventoryReservation($pdo, $cleanOrderId);
                        $pdo->prepare("UPDATE ph_orders SET status = 'Reservation Expired', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanOrderId]);
                        $pdo->prepare("UPDATE ph_payments SET status = 'EXPIRED', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                        throw new DomainException("Inventory reservation for order '{$cleanOrderId}' product '{$pid}' has expired. Payment capture aborted.");
                    }

                    // 3. Strict Quantity Match: Reservation qty must cover required order quantity
                    $resQty = (int)($r['qty'] ?? 0);
                    if ($resQty < $reqQty) {
                        self::releaseInventoryReservation($pdo, $cleanOrderId);
                        $pdo->prepare("UPDATE ph_orders SET status = 'Reservation Expired', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanOrderId]);
                        $pdo->prepare("UPDATE ph_payments SET status = 'EXPIRED', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                        throw new DomainException("Order '{$cleanOrderId}' product '{$pid}' reservation quantity ({$resQty}) is insufficient for required order quantity ({$reqQty}). Payment capture aborted.");
                    }
                }
            }
        }

        // 3. Stock Check: Verify all products remain in stock
        $items = json_decode((string)$order['items_json'], true) ?: [];
        foreach ($items as $it) {
            $pid = (string)($it['id'] ?? '');
            if ($pid !== '') {
                $stStock = $pdo->prepare("SELECT in_stock, name FROM products WHERE id = ? LIMIT 1");
                $stStock->execute([$pid]);
                $prod = $stStock->fetch();
                if ($prod && empty($prod['in_stock'])) {
                    self::releaseInventoryReservation($pdo, $cleanOrderId);
                    throw new DomainException("Product '{$prod['name']}' became out of stock during checkout.");
                }
            }
        }

        // 4. Remote Status Check & Capture Recovery:
        // Never blindly issue another capture without verifying remote state.
        $paypalRemoteOrder = null;
        try {
            $paypalRemoteOrder = $client->getOrder($cleanPaypalOrderId);
        } catch (Throwable $e) {
            // Note: temporary network issue during getOrder will be handled gracefully below
            error_log("Notice: PayPal getOrder status check encountered: " . $e->getMessage());
        }

        $captureData = null;
        $isReconciled = false;

        if (is_array($paypalRemoteOrder) && strtoupper((string)($paypalRemoteOrder['status'] ?? '')) === 'COMPLETED') {
            // Scenario: PayPal already completed this order (e.g. from a prior dropped response).
            // Reconcile directly from the verified PayPal order details without issuing another capture!
            $captureData = $paypalRemoteOrder;
            $isReconciled = true;
        } else {
            // Order has not yet been completed on PayPal (e.g. status APPROVED). Execute capture call.
            try {
                $expectedAmountUsd = (string)$payment['amount_usd'];
                $captureData = $client->captureOrder($cleanPaypalOrderId, $expectedAmountUsd);
            } catch (Throwable $capEx) {
                $capMsg = $capEx->getMessage();
                // Check if PayPal reports order already captured
                if (str_contains($capMsg, 'ORDER_ALREADY_CAPTURED') || str_contains($capMsg, 'already been captured')) {
                    $recheck = $client->getOrder($cleanPaypalOrderId);
                    if (is_array($recheck) && strtoupper((string)($recheck['status'] ?? '')) === 'COMPLETED') {
                        $captureData = $recheck;
                        $isReconciled = true;
                    } else {
                        throw $capEx;
                    }
                } elseif ($capEx instanceof DomainException) {
                    // Actual business domain rejection (e.g. card declined, invalid state)
                    $pdo->prepare("UPDATE ph_payments SET status = 'FAILED', raw_response = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                        ->execute([$capMsg, $payment['id']]);
                    $pdo->prepare("UPDATE ph_orders SET status = 'Payment Failed', updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                        ->execute([$cleanOrderId]);
                    self::releaseInventoryReservation($pdo, $cleanOrderId);
                    throw $capEx;
                } else {
                    // Network timeout or temporary connection failure:
                    // DO NOT mark order or payment as failed! Retain Pending Payment so subsequent retry can reconcile.
                    throw new RuntimeException("Temporary communication failure with PayPal: {$capMsg}. Your order status is preserved. Please retry to finalize your payment.", 0, $capEx);
                }
            }
        }

        $captureStatus = strtoupper((string)($captureData['status'] ?? ''));
        if ($captureStatus !== 'COMPLETED') {
            $pdo->prepare("UPDATE ph_payments SET status = ?, raw_response = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                ->execute([$captureStatus ?: 'FAILED', json_encode($captureData), $payment['id']]);
            $pdo->prepare("UPDATE ph_orders SET status = 'Payment Failed', updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                ->execute([$cleanOrderId]);
            self::releaseInventoryReservation($pdo, $cleanOrderId);
            throw new DomainException("PayPal payment was not completed (Status: {$captureStatus}).");
        }

        // 5. Extract and Validate Capture Details
        $captures = $captureData['purchase_units'][0]['payments']['captures'] ?? [];
        if (empty($captures)) {
            throw new RuntimeException("PayPal capture response is missing transaction captures.");
        }

        $firstCapture = $captures[0];
        $captureId = (string)($firstCapture['id'] ?? '');
        $capturedStatus = strtoupper((string)($firstCapture['status'] ?? ''));
        $capturedCurrency = strtoupper((string)($firstCapture['amount']['currency_code'] ?? ''));
        $capturedAmount = (float)($firstCapture['amount']['value'] ?? 0);
        $expectedAmount = (float)$payment['amount_usd'];

        if ($capturedStatus !== 'COMPLETED') {
            $pdo->prepare("UPDATE ph_payments SET status = ?, raw_response = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                ->execute([$capturedStatus ?: 'FAILED', json_encode($captureData), $payment['id']]);
            $pdo->prepare("UPDATE ph_orders SET status = 'Payment Failed', updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                ->execute([$cleanOrderId]);
            self::releaseInventoryReservation($pdo, $cleanOrderId);
            throw new DomainException("PayPal transaction capture status is '{$capturedStatus}', expected COMPLETED.");
        }

        // Strict Currency Check: Must be USD
        if ($capturedCurrency !== 'USD') {
            $pdo->prepare("UPDATE ph_payments SET status = 'CURRENCY_MISMATCH', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
            $pdo->prepare("UPDATE ph_orders SET status = 'Payment Failed', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanOrderId]);
            self::releaseInventoryReservation($pdo, $cleanOrderId);
            throw new DomainException("Payment currency mismatch: expected USD, received {$capturedCurrency}.");
        }

        // Strict Amount Check: Must match within 0.01 tolerance
        if (abs($capturedAmount - $expectedAmount) > 0.01) {
            $pdo->prepare("UPDATE ph_payments SET status = 'AMOUNT_MISMATCH', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
            $pdo->prepare("UPDATE ph_orders SET status = 'Payment Failed', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanOrderId]);
            self::releaseInventoryReservation($pdo, $cleanOrderId);
            throw new DomainException("Captured amount \${$capturedAmount} does not match expected order total \${$expectedAmount}.");
        }

        // Payee Merchant ID Verification (if configured)
        $expectedMerchantId = $client->getMerchantId();
        if ($expectedMerchantId !== '') {
            $actualPayeeMerchant = (string)($captureData['purchase_units'][0]['payee']['merchant_id'] 
                ?? $firstCapture['payee']['merchant_id'] 
                ?? '');
            if ($actualPayeeMerchant !== '' && strcasecmp($actualPayeeMerchant, $expectedMerchantId) !== 0) {
                $pdo->prepare("UPDATE ph_payments SET status = 'MERCHANT_MISMATCH', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                $pdo->prepare("UPDATE ph_orders SET status = 'Payment Failed', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanOrderId]);
                self::releaseInventoryReservation($pdo, $cleanOrderId);
                throw new DomainException("Security violation: Payment captured for unexpected payee merchant ID '{$actualPayeeMerchant}'. Expected '{$expectedMerchantId}'.");
            }
        }

        $payerEmail = (string)($captureData['payer']['email_address'] 
            ?? $captureData['payment_source']['paypal']['email_address'] 
            ?? '');

        // 6. Atomically update ph_payments, ph_orders, and commit inventory reservation
        $pdo->beginTransaction();
        try {
            $pdo->prepare("UPDATE ph_payments SET 
                status = 'COMPLETED',
                capture_id = ?,
                payer_email = ?,
                raw_response = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?")
                ->execute([
                    $captureId,
                    $payerEmail,
                    json_encode($captureData, JSON_UNESCAPED_SLASHES),
                    $payment['id']
                ]);

            $pdo->prepare("UPDATE ph_orders SET 
                status = 'Confirmed',
                payment = 'paypal',
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?")
                ->execute([$cleanOrderId]);

            self::commitInventoryReservation($pdo, $cleanOrderId);

            $pdo->commit();
        } catch (Throwable $dbEx) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $dbEx;
        }

        return [
            'success' => true,
            'orderId' => $cleanOrderId,
            'paypalOrderId' => $cleanPaypalOrderId,
            'status' => 'Confirmed',
            'captureId' => $captureId,
            'amountUsd' => $capturedAmount,
            'amountPkr' => (float)$order['total'],
            'fxRate' => (float)$payment['fx_rate'],
            'isDuplicate' => false,
            'reconciled' => $isReconciled,
            'message' => $isReconciled
                ? 'Payment capture recovered and reconciled successfully from PayPal.'
                : 'Payment successfully captured and order confirmed.'
        ];
    }

    /**
     * Retrieve payment status with strict IDOR / authorization defense.
     * Requires either session user ownership OR a valid order-specific payment access token.
     */
    public static function getPaymentStatus(PDO $pdo, string $orderId, ?string $token = null, ?int $sessionUserId = null): array {
        $cleanId = trim($orderId);
        $cleanToken = $token !== null ? trim($token) : '';

        $st = $pdo->prepare("SELECT o.id, o.user_id, o.status as order_status, o.total, o.payment as payment_method,
            p.paypal_order_id, p.payment_token, p.capture_id, p.currency, p.amount_usd, p.amount_pkr, p.fx_rate, p.status as payment_status, p.created_at
            FROM ph_orders o
            LEFT JOIN ph_payments p ON o.id = p.order_id
            WHERE o.id = ? LIMIT 1");
        $st->execute([$cleanId]);
        $row = $st->fetch();

        if (!$row) {
            throw new DomainException("Order '{$cleanId}' not found.", 404);
        }

        // Authorization check: User must own the order OR supply the unguessable payment access token
        $isOwner = ($sessionUserId !== null && isset($row['user_id']) && (int)$row['user_id'] === $sessionUserId);
        $hasValidToken = ($cleanToken !== '' && !empty($row['payment_token']) && hash_equals((string)$row['payment_token'], $cleanToken));

        if (!$isOwner && !$hasValidToken) {
            throw new DomainException("Unauthorized access to payment status. Please provide a valid order access token.", 403);
        }

        // Safe projection: Never expose customer personal info or credentials
        return [
            'success' => true,
            'orderId' => (string)$row['id'],
            'orderStatus' => (string)$row['order_status'],
            'paymentMethod' => (string)$row['payment_method'],
            'totalPkr' => (float)$row['total'],
            'payment' => $row['paypal_order_id'] ? [
                'paypalOrderId' => (string)$row['paypal_order_id'],
                'captureId' => (string)($row['capture_id'] ?? ''),
                'currency' => (string)($row['currency'] ?? 'USD'),
                'amountUsd' => (float)($row['amount_usd'] ?? 0),
                'amountPkr' => (float)($row['amount_pkr'] ?? 0),
                'fxRate' => (float)($row['fx_rate'] ?? 280.0),
                'status' => (string)($row['payment_status'] ?? 'UNKNOWN'),
                'createdAt' => (string)($row['created_at'] ?? '')
            ] : null
        ];
    }

    /**
     * Cancel an uncaptured PayPal checkout order (e.g. customer cancelled at PayPal checkout).
     * Enforces ownership check (session user or payment token).
     */
    public static function cancelOrder(PDO $pdo, string $orderId, ?string $token = null, ?int $sessionUserId = null): array {
        $cleanId = trim($orderId);
        $cleanToken = $token !== null ? trim($token) : '';

        $st = $pdo->prepare("SELECT o.id, o.user_id, o.status as order_status, p.payment_token, p.status as payment_status
            FROM ph_orders o
            LEFT JOIN ph_payments p ON o.id = p.order_id
            WHERE o.id = ? LIMIT 1");
        $st->execute([$cleanId]);
        $order = $st->fetch();

        if (!$order) {
            throw new DomainException("Order '{$cleanId}' not found.", 404);
        }

        // Ownership verification
        $isOwner = false;
        if ($sessionUserId !== null && $order['user_id'] !== null && (int)$order['user_id'] === $sessionUserId) {
            $isOwner = true;
        } elseif ($cleanToken !== '' && !empty($order['payment_token']) && hash_equals((string)$order['payment_token'], $cleanToken)) {
            $isOwner = true;
        }

        if (!$isOwner) {
            throw new DomainException("Unauthorized: Valid payment access token or authenticated user ownership is required.", 403);
        }

        if ($order['order_status'] === 'Confirmed' || $order['payment_status'] === 'COMPLETED') {
            throw new DomainException("Completed or confirmed orders cannot be cancelled.");
        }

        $pdo->prepare("UPDATE ph_orders SET status = 'Cancelled', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$cleanId]);
        $pdo->prepare("UPDATE ph_payments SET status = 'CANCELLED', updated_at = CURRENT_TIMESTAMP WHERE order_id = ?")->execute([$cleanId]);
        self::releaseInventoryReservation($pdo, $cleanId);

        return [
            'success' => true,
            'orderId' => $cleanId,
            'status' => 'Cancelled',
            'message' => 'Order checkout has been successfully cancelled.'
        ];
    }

    /**
     * Webhook event handler with cryptographic signature verification, deduplication,
     * and strict payment verification before order state changes.
     */
    public static function handleWebhook(PDO $pdo, array $headers, string $rawBody, ?PhPayPalClient $client = null): array {
        $client = $client ?? new PhPayPalClient();

        // 1. Signature Verification precedes all database reads and state changes
        $isValid = $client->verifyWebhookSignature($headers, $rawBody);
        if (!$isValid) {
            throw new InvalidArgumentException("Invalid PayPal webhook signature or forged transmission.");
        }

        $payload = json_decode($rawBody, true);
        if (!is_array($payload) || empty($payload['id']) || empty($payload['event_type'])) {
            throw new InvalidArgumentException("Invalid webhook JSON payload.");
        }

        $eventId = (string)$payload['id'];
        $eventType = (string)$payload['event_type'];
        $resource = $payload['resource'] ?? [];

        self::ensureTables($pdo);

        // 2. Deduplication via ph_payment_events unique constraint
        $localOrderId = (string)($resource['custom_id'] ?? $resource['reference_id'] ?? '');

        // Check if event already exists
        $stCheck = $pdo->prepare("SELECT id FROM ph_payment_events WHERE event_id = ? LIMIT 1");
        $stCheck->execute([$eventId]);
        if ($stCheck->fetch()) {
            return [
                'success' => true,
                'eventId' => $eventId,
                'duplicate' => true,
                'message' => 'Webhook event already processed.'
            ];
        }

        // Record event into ph_payment_events
        $stIns = $pdo->prepare("INSERT INTO ph_payment_events 
            (event_id, event_type, order_id, verified, payload, created_at)
            VALUES (?, ?, ?, 1, ?, CURRENT_TIMESTAMP)");
        try {
            $stIns->execute([$eventId, $eventType, $localOrderId ?: null, $rawBody]);
        } catch (PDOException $e) {
            if ((string)$e->getCode() === '23000') {
                return [
                    'success' => true,
                    'eventId' => $eventId,
                    'duplicate' => true,
                    'message' => 'Webhook event already processed (concurrent).'
                ];
            }
            throw $e;
        }

        // 3. Process Events with Strict Payment Validation
        if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
            $captureId = (string)($resource['id'] ?? '');
            if ($localOrderId !== '') {
                // Fetch payment record
                $stPay = $pdo->prepare("SELECT * FROM ph_payments WHERE order_id = ? LIMIT 1");
                $stPay->execute([$localOrderId]);
                $payment = $stPay->fetch();

                if (!$payment) {
                    throw new DomainException("Webhook received for unknown order '{$localOrderId}'.");
                }

                // Strict validation: currency, amount, and payee
                $capturedCurrency = strtoupper((string)($resource['amount']['currency_code'] ?? ''));
                $capturedAmount = (float)($resource['amount']['value'] ?? 0);
                $expectedAmount = (float)$payment['amount_usd'];

                if ($capturedCurrency !== 'USD') {
                    $pdo->prepare("UPDATE ph_payments SET status = 'CURRENCY_MISMATCH', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                    $pdo->prepare("UPDATE ph_orders SET status = 'Payment Failed', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$localOrderId]);
                    self::releaseInventoryReservation($pdo, $localOrderId);
                    throw new DomainException("Webhook payment currency mismatch: expected USD, received {$capturedCurrency}.");
                }

                if (abs($capturedAmount - $expectedAmount) > 0.01) {
                    $pdo->prepare("UPDATE ph_payments SET status = 'AMOUNT_MISMATCH', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                    $pdo->prepare("UPDATE ph_orders SET status = 'Payment Failed', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$localOrderId]);
                    self::releaseInventoryReservation($pdo, $localOrderId);
                    throw new DomainException("Webhook payment amount mismatch: expected \${$expectedAmount}, received \${$capturedAmount}.");
                }

                $expectedMerchant = $client->getMerchantId();
                if ($expectedMerchant !== '') {
                    $webhookPayee = (string)($resource['payee']['merchant_id'] ?? '');
                    if ($webhookPayee !== '' && strcasecmp($webhookPayee, $expectedMerchant) !== 0) {
                        $pdo->prepare("UPDATE ph_payments SET status = 'MERCHANT_MISMATCH', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$payment['id']]);
                        $pdo->prepare("UPDATE ph_orders SET status = 'Payment Failed', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$localOrderId]);
                        self::releaseInventoryReservation($pdo, $localOrderId);
                        throw new DomainException("Webhook payee merchant mismatch.");
                    }
                }

                // Payment verified! Atomically confirm order and commit inventory reservation
                $pdo->beginTransaction();
                try {
                    $pdo->prepare("UPDATE ph_payments SET status = 'COMPLETED', capture_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                        ->execute([$captureId, $payment['id']]);
                    $pdo->prepare("UPDATE ph_orders SET status = 'Confirmed', payment = 'paypal', updated_at = CURRENT_TIMESTAMP WHERE id = ? AND status = 'Pending Payment'")
                        ->execute([$localOrderId]);
                    self::commitInventoryReservation($pdo, $localOrderId);
                    $pdo->commit();
                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    throw $e;
                }
            }
        } elseif ($eventType === 'PAYMENT.CAPTURE.DENIED' || $eventType === 'PAYMENT.CAPTURE.DECLINED') {
            if ($localOrderId !== '') {
                $pdo->prepare("UPDATE ph_payments SET status = 'DENIED', updated_at = CURRENT_TIMESTAMP WHERE order_id = ?")->execute([$localOrderId]);
                $pdo->prepare("UPDATE ph_orders SET status = 'Payment Failed', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$localOrderId]);
                self::releaseInventoryReservation($pdo, $localOrderId);
            }
        } elseif ($eventType === 'PAYMENT.CAPTURE.REFUNDED') {
            if ($localOrderId !== '') {
                $pdo->prepare("UPDATE ph_payments SET status = 'REFUNDED', updated_at = CURRENT_TIMESTAMP WHERE order_id = ?")->execute([$localOrderId]);
                $pdo->prepare("UPDATE ph_orders SET status = 'Refunded', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$localOrderId]);
            }
        }

        return [
            'success' => true,
            'eventId' => $eventId,
            'eventType' => $eventType,
            'processed' => true
        ];
    }
}
