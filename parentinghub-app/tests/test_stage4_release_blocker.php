<?php
/**
 * ParentingHub SmartCart AI - Stage 4 Final Release-Blocker Edge Cases Test Suite
 *
 * Verifies:
 * 1. Migration resilience across 5 existing-table scenarios:
 *    - Scenario A: Table exists, active_slot column missing.
 *    - Scenario B: active_slot exists, uq_active_slot index missing.
 *    - Scenario C: active_slot and uq_active_slot exist, uq_order_product missing.
 *    - Scenario D: Both indexes already exist (idempotent run).
 *    - Scenario E: Existing duplicate active_slot values reconciled before unique index creation.
 * 2. PayPal remote orphan order behavior:
 *    - Rejection of capture on orphaned / reservation-failed / cancelled order.
 *    - Preservation of order state preventing payment.
 * 3. Capture & Reservation state transitions:
 *    - Rejection of RELEASED reservation capture.
 *    - Rejection of expired reservation capture.
 *    - Acceptance of valid RESERVED reservation within TTL.
 *    - Safe idempotent recovery for COMMITTED orders.
 *    - Network-loss capture recovery reconciliation without double-charging.
 */

declare(strict_types=1);

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_client.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_service.php';

date_default_timezone_set('UTC');

class Stage4ReleaseBlockerTest {
    private int $passed = 0;
    private int $failed = 0;
    private array $errors = [];

    private function assert(string $desc, bool $condition, string $details = ''): void {
        if ($condition) {
            $this->passed++;
            echo "  [PASS] {$desc}\n";
        } else {
            $this->failed++;
            $msg = "  [FAIL] {$desc}" . ($details !== '' ? " -> {$details}" : "");
            $this->errors[] = $msg;
            echo "{$msg}\n";
        }
    }

    public function runAll(): void {
        echo "====================================================================\n";
        echo "STAGE 4 FINAL RELEASE-BLOCKER CHECK: MIGRATION & CAPTURE RESILIENCE\n";
        echo "====================================================================\n\n";

        $this->testMigrationScenarioA_MissingColumn();
        $this->testMigrationScenarioB_MissingSlotIndex();
        $this->testMigrationScenarioC_MissingCompositeIndex();
        $this->testMigrationScenarioD_BothIndexesExist();
        $this->testMigrationScenarioE_DuplicateActiveSlotReconciliation();
        $this->testMigrationDeduplicationStatusTransition();
        $this->testPayPalRemoteOrphanOrderSafeguards();
        $this->testCaptureStateTransitions();
        $this->testCaptureReservedNullActiveSlotRejected();
        $this->testMultiProductPartialExpiryOrReleaseRejection();
        $this->testMultiProductAllValidCaptureAndIdempotence();
        $this->testCodAndBankTransferCompatibility();
        $this->testDuplicateOrderLinesQuantityAggregation();
        $this->testInsufficientReservationQuantityRejection();
        $this->testCorruptDuplicateReservationRowRejection();
        $this->testCaptureLostResponseReconciliation();

        echo "\n--------------------------------------------------------------------\n";
        echo "RELEASE-BLOCKER RESULTS: {$this->passed} passed, {$this->failed} failed\n";
        echo "--------------------------------------------------------------------\n";

        if ($this->failed > 0) {
            echo "\nFailures:\n" . implode("\n", $this->errors) . "\n";
            exit(1);
        }
    }

    private function createBasePdo(): PDO {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Base products and orders
        $pdo->exec("CREATE TABLE products (
            id TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            brand TEXT,
            category TEXT,
            price REAL NOT NULL,
            original_price REAL,
            in_stock INTEGER NOT NULL DEFAULT 1,
            stock_quantity INTEGER DEFAULT 10
        )");
        $pdo->exec("CREATE TABLE ph_orders (
            id TEXT PRIMARY KEY,
            user_id INTEGER,
            total REAL NOT NULL,
            subtotal REAL NOT NULL,
            shipping REAL NOT NULL DEFAULT 0,
            discount REAL NOT NULL DEFAULT 0,
            status TEXT NOT NULL DEFAULT 'Pending Payment',
            payment TEXT DEFAULT 'paypal',
            items_json TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");
        return $pdo;
    }

    // =========================================================================
    // SECTION 1: Migration Scenarios
    // =========================================================================

    private function testMigrationScenarioA_MissingColumn(): void {
        echo "--- Test 1: Migration Scenario A (Table exists, active_slot column missing) ---\n";
        $pdo = $this->createBasePdo();
        // Create table without active_slot column and without unique indexes
        $pdo->exec("CREATE TABLE ph_inventory_reservations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id TEXT NOT NULL,
            product_id TEXT NOT NULL,
            qty INTEGER NOT NULL DEFAULT 1,
            status TEXT NOT NULL DEFAULT 'RESERVED',
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");

        $actions = PhPayPalService::ensureTables($pdo);

        $this->assert("Scenario A: Detected missing active_slot and added column", in_array('added_column_active_slot', $actions));
        $this->assert("Scenario A: Created uq_active_slot index", in_array('created_index_uq_active_slot', $actions));
        $this->assert("Scenario A: Created uq_order_product index", in_array('created_index_uq_order_product', $actions));

        // Verify column is queryable
        $q = $pdo->query("SELECT active_slot FROM ph_inventory_reservations LIMIT 1");
        $this->assert("Scenario A: active_slot column is now queryable", $q !== false);
    }

    private function testMigrationScenarioB_MissingSlotIndex(): void {
        echo "\n--- Test 2: Migration Scenario B (active_slot exists, uq_active_slot index missing) ---\n";
        $pdo = $this->createBasePdo();
        // Create table with active_slot column, but NO unique index on active_slot
        $pdo->exec("CREATE TABLE ph_inventory_reservations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id TEXT NOT NULL,
            product_id TEXT NOT NULL,
            active_slot TEXT,
            qty INTEGER NOT NULL DEFAULT 1,
            status TEXT NOT NULL DEFAULT 'RESERVED',
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");

        $actions = PhPayPalService::ensureTables($pdo);

        $this->assert("Scenario B: Column active_slot was already present (not re-added)", !in_array('added_column_active_slot', $actions));
        $this->assert("Scenario B: Detected missing uq_active_slot and created it", in_array('created_index_uq_active_slot', $actions));
        $this->assert("Scenario B: Created uq_order_product index", in_array('created_index_uq_order_product', $actions));

        // Verify unique constraint is active: inserting duplicate non-null active_slot throws PDOException
        $pdo->exec("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, expires_at) VALUES ('ord-1', 'p-1', 'slot-1', '2099-01-01 00:00:00')");
        $caughtDup = false;
        try {
            $pdo->exec("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, expires_at) VALUES ('ord-2', 'p-1', 'slot-1', '2099-01-01 00:00:00')");
        } catch (PDOException $e) {
            $caughtDup = true;
        }
        $this->assert("Scenario B: Unique index on active_slot rejects duplicate slots", $caughtDup);
    }

    private function testMigrationScenarioC_MissingCompositeIndex(): void {
        echo "\n--- Test 3: Migration Scenario C (active_slot & uq_active_slot exist, uq_order_product missing) ---\n";
        $pdo = $this->createBasePdo();
        // Create table with active_slot column and its unique index, but WITHOUT uq_order_product
        $pdo->exec("CREATE TABLE ph_inventory_reservations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id TEXT NOT NULL,
            product_id TEXT NOT NULL,
            active_slot TEXT UNIQUE,
            qty INTEGER NOT NULL DEFAULT 1,
            status TEXT NOT NULL DEFAULT 'RESERVED',
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");

        $actions = PhPayPalService::ensureTables($pdo);

        $this->assert("Scenario C: active_slot column was already present", !in_array('added_column_active_slot', $actions));
        $this->assert("Scenario C: uq_active_slot was already present", !in_array('created_index_uq_active_slot', $actions));
        $this->assert("Scenario C: Detected missing uq_order_product and created it", in_array('created_index_uq_order_product', $actions));

        // Verify composite constraint rejects duplicate (order_id, product_id)
        $pdo->exec("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, expires_at) VALUES ('ord-c1', 'p-c1', NULL, '2099-01-01 00:00:00')");
        $caughtCompDup = false;
        try {
            $pdo->exec("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, expires_at) VALUES ('ord-c1', 'p-c1', NULL, '2099-01-01 00:00:00')");
        } catch (PDOException $e) {
            $caughtCompDup = true;
        }
        $this->assert("Scenario C: Composite unique index rejects duplicate (order_id, product_id)", $caughtCompDup);
    }

    private function testMigrationScenarioD_BothIndexesExist(): void {
        echo "\n--- Test 4: Migration Scenario D (Both indexes already exist - Idempotence) ---\n";
        $pdo = $this->createBasePdo();
        // Run ensureTables once to create full schema
        PhPayPalService::ensureTables($pdo);

        // Run ensureTables a second time
        $actions2 = PhPayPalService::ensureTables($pdo);

        $this->assert("Scenario D: No column added on second run", !in_array('added_column_active_slot', $actions2));
        $this->assert("Scenario D: No uq_active_slot created on second run", !in_array('created_index_uq_active_slot', $actions2));
        $this->assert("Scenario D: No uq_order_product created on second run", !in_array('created_index_uq_order_product', $actions2));
        $this->assert("Scenario D: ensureTables completed cleanly without errors", empty(array_filter($actions2, fn($a) => str_starts_with($a, 'error') || str_starts_with($a, 'fatal'))));
    }

    private function testMigrationScenarioE_DuplicateActiveSlotReconciliation(): void {
        echo "\n--- Test 5: Migration Scenario E (Duplicate active_slot values prevented unique index creation) ---\n";
        $pdo = $this->createBasePdo();
        // Table exists without index, and contains pre-existing duplicate active_slot values
        $pdo->exec("CREATE TABLE ph_inventory_reservations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id TEXT NOT NULL,
            product_id TEXT NOT NULL,
            active_slot TEXT,
            qty INTEGER NOT NULL DEFAULT 1,
            status TEXT NOT NULL DEFAULT 'RESERVED',
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");

        // Insert duplicate active_slot entries
        $pdo->exec("INSERT INTO ph_inventory_reservations (id, order_id, product_id, active_slot, status, expires_at) 
            VALUES (1, 'ord-old', 'prod-10', 'prod-10', 'RELEASED', '2026-01-01 00:00:00')");
        $pdo->exec("INSERT INTO ph_inventory_reservations (id, order_id, product_id, active_slot, status, expires_at) 
            VALUES (2, 'ord-new', 'prod-10', 'prod-10', 'RESERVED', '2099-01-01 00:00:00')");

        // Running ensureTables must detect the duplicate, reconcile it (set older/released slot to NULL),
        // and create the unique index successfully without throwing an error!
        $actions = PhPayPalService::ensureTables($pdo);

        $this->assert("Scenario E: Detected and reconciled duplicate active_slot values", in_array('reconciled_duplicate_active_slots', $actions));
        $this->assert("Scenario E: Successfully created uq_active_slot unique index after reconciliation", in_array('created_index_uq_active_slot', $actions));

        // Verify the active/new reservation preserved its slot while older one was nullified
        $st1 = $pdo->query("SELECT active_slot FROM ph_inventory_reservations WHERE id = 1")->fetchColumn();
        $st2 = $pdo->query("SELECT active_slot FROM ph_inventory_reservations WHERE id = 2")->fetchColumn();
        $this->assert("Scenario E: Older released reservation slot was nullified", $st1 === null);
        $this->assert("Scenario E: Active RESERVED reservation preserved its slot", $st2 === 'prod-10');
    }

    // =========================================================================
    // SECTION 2: PayPal Remote Orphan Order Safeguards
    // =========================================================================

    private function testPayPalRemoteOrphanOrderSafeguards(): void {
        echo "\n--- Test 6: PayPal Remote Orphan Order Safeguards ---\n";
        $pdo = $this->createBasePdo();
        PhPayPalService::ensureTables($pdo);

        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('prod-orphan', 'Orphan Item', 1000, 1)");

        // Simulate an order where remote PayPal order was created, but inventory reservation failed
        $orderId = 'ORD-ORPHAN-001';
        $paypalOrderId = 'PAYPAL-ORPHAN-XYZ';
        $items = [['id' => 'prod-orphan', 'name' => 'Orphan Item', 'qty' => 1, 'price' => 1000]];

        $pdo->prepare("INSERT INTO ph_orders (id, user_id, total, subtotal, shipping, discount, status, payment, items_json)
            VALUES (?, 1, 1000, 1000, 0, 0, 'Reservation Failed', 'paypal', ?)")
            ->execute([$orderId, json_encode($items)]);

        $pdo->prepare("INSERT INTO ph_payments (order_id, provider, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status)
            VALUES (?, 'paypal', ?, 'USD', 3.58, 1000, 279.0, 'CANCELLED')")
            ->execute([$orderId, $paypalOrderId]);

        // Attempting to capture this orphan order must be rejected immediately
        $mockClient = new class extends PhPayPalClient {
            public int $captureCalls = 0;
            public function __construct() {}
            public function captureOrder(string $id, ?string $exp = null): array {
                $this->captureCalls++;
                return ['status' => 'COMPLETED', 'purchase_units' => [['payments' => ['captures' => [['id' => 'CAP-1', 'status' => 'COMPLETED', 'amount' => ['value' => '3.58', 'currency_code' => 'USD']]]]]]];
            }
        };

        $caught = false;
        $caughtMsg = '';
        try {
            PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $mockClient);
        } catch (DomainException $e) {
            $caught = true;
            $caughtMsg = $e->getMessage();
        }

        $this->assert("Orphan order capture rejected by ParentingHub backend", $caught);
        $this->assert("Orphan order capture error mentions cancelled/expired order", str_contains($caughtMsg, 'cancelled') || str_contains($caughtMsg, 'reservation'));
        $this->assert("ParentingHub backend made 0 capture HTTP calls to PayPal for orphan order", $mockClient->captureCalls === 0);
    }

    // =========================================================================
    // SECTION 3: Capture & Reservation State Transitions
    // =========================================================================

    private function testCaptureStateTransitions(): void {
        echo "\n--- Test 7: Capture State Transitions (RELEASED, EXPIRED, RESERVED, COMMITTED) ---\n";
        $pdo = $this->createBasePdo();
        PhPayPalService::ensureTables($pdo);

        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('prod-bottle', 'Feeding Bottle', 2000, 1)");

        $mockClient = new class extends PhPayPalClient {
            public function __construct() {}
            public function getOrder(string $id): array {
                return ['status' => 'APPROVED'];
            }
            public function captureOrder(string $id, ?string $exp = null): array {
                return [
                    'status' => 'COMPLETED',
                    'purchase_units' => [[
                        'payments' => [
                            'captures' => [[
                                'id' => 'CAP-SUCCESS',
                                'status' => 'COMPLETED',
                                'amount' => ['value' => '7.17', 'currency_code' => 'USD']
                            ]]
                        ]
                    ]]
                ];
            }
        };

        // 7A: RELEASED reservation cannot be captured
        $ordReleased = 'ORD-RELEASED-1';
        $payReleased = 'PAY-REL-1';
        $items = [['id' => 'prod-bottle', 'name' => 'Feeding Bottle', 'qty' => 1, 'price' => 2000]];
        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, 2000, 2000, 0, 'Pending Payment', 'paypal', ?)")
            ->execute([$ordReleased, json_encode($items)]);
        $pdo->prepare("INSERT INTO ph_payments (order_id, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status) VALUES (?, ?, 'USD', 7.17, 2000, 279.0, 'CREATED')")
            ->execute([$ordReleased, $payReleased]);
        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, status, expires_at) VALUES (?, 'prod-bottle', NULL, 'RELEASED', '2099-01-01 00:00:00')")
            ->execute([$ordReleased]);

        $relCaught = false;
        try {
            PhPayPalService::captureOrder($pdo, $ordReleased, $payReleased, $mockClient);
        } catch (DomainException $e) {
            $relCaught = true;
        }
        $this->assert("7A: Order with RELEASED reservation is rejected from capture", $relCaught);

        // 7B: EXPIRED reservation cannot be captured
        $ordExpired = 'ORD-EXPIRED-1';
        $payExpired = 'PAY-EXP-1';
        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, 2000, 2000, 0, 'Pending Payment', 'paypal', ?)")
            ->execute([$ordExpired, json_encode($items)]);
        $pdo->prepare("INSERT INTO ph_payments (order_id, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status) VALUES (?, ?, 'USD', 7.17, 2000, 279.0, 'CREATED')")
            ->execute([$ordExpired, $payExpired]);
        // Reservation expired 10 minutes ago
        $pastTime = gmdate('Y-m-d H:i:s', time() - 600);
        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, status, expires_at) VALUES (?, 'prod-bottle', 'prod-bottle', 'RESERVED', ?)")
            ->execute([$ordExpired, $pastTime]);

        $expCaught = false;
        try {
            PhPayPalService::captureOrder($pdo, $ordExpired, $payExpired, $mockClient);
        } catch (DomainException $e) {
            $expCaught = true;
        }
        $this->assert("7B: Order with EXPIRED reservation is rejected from capture", $expCaught);

        // Verify order and payment were marked expired, and slot was freed
        $orderSt = $pdo->query("SELECT status FROM ph_orders WHERE id = '{$ordExpired}'")->fetchColumn();
        $paySt = $pdo->query("SELECT status FROM ph_payments WHERE order_id = '{$ordExpired}'")->fetchColumn();
        $this->assert("7B: Expired order status updated to 'Reservation Expired'", $orderSt === 'Reservation Expired');
        $this->assert("7B: Expired payment status updated to 'EXPIRED'", $paySt === 'EXPIRED');

        // 7C: Valid RESERVED order can be captured
        $ordValid = 'ORD-VALID-1';
        $payValid = 'PAY-VAL-1';
        $futureTime = gmdate('Y-m-d H:i:s', time() + 900);
        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, 2000, 2000, 0, 'Pending Payment', 'paypal', ?)")
            ->execute([$ordValid, json_encode($items)]);
        $pdo->prepare("INSERT INTO ph_payments (order_id, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status) VALUES (?, ?, 'USD', 7.17, 2000, 279.0, 'CREATED')")
            ->execute([$ordValid, $payValid]);
        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, status, expires_at) VALUES (?, 'prod-bottle', 'prod-bottle', 'RESERVED', ?)")
            ->execute([$ordValid, $futureTime]);

        $capRes = PhPayPalService::captureOrder($pdo, $ordValid, $payValid, $mockClient);
        $this->assert("7C: Valid RESERVED reservation successfully captured", $capRes['success'] === true && $capRes['status'] === 'Confirmed');

        $resRow = $pdo->query("SELECT status, active_slot FROM ph_inventory_reservations WHERE order_id = '{$ordValid}'")->fetch(PDO::FETCH_ASSOC);
        $this->assert("7C: Reservation transitioned to COMMITTED upon capture", $resRow['status'] === 'COMMITTED');
        $this->assert("7C: Active slot freed to NULL upon COMMITTED status", $resRow['active_slot'] === null);

        // 7D: COMMITTED order provides safe idempotent recovery
        $capIdempotent = PhPayPalService::captureOrder($pdo, $ordValid, $payValid, $mockClient);
        $this->assert("7D: Re-capturing COMMITTED confirmed order returns idempotent success", $capIdempotent['isDuplicate'] === true && $capIdempotent['status'] === 'Confirmed');
    }

    // =========================================================================
    // SECTION 4: Network Loss Capture Recovery Reconciliation
    // =========================================================================

    private function testCaptureLostResponseReconciliation(): void {
        echo "\n--- Test 8: Failed Local DB Update After Successful PayPal Capture (Reconciliation) ---\n";
        $pdo = $this->createBasePdo();
        PhPayPalService::ensureTables($pdo);

        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('prod-recon', 'Recon Toy', 1500, 1)");

        $orderId = 'ORD-RECON-999';
        $paypalOrderId = 'PAYPAL-RECON-777';
        $items = [['id' => 'prod-recon', 'name' => 'Recon Toy', 'qty' => 1, 'price' => 1500]];
        $futureTime = gmdate('Y-m-d H:i:s', time() + 900);

        // Order and payment are still in Pending/CREATED locally because previous response was lost
        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, 1500, 1500, 0, 'Pending Payment', 'paypal', ?)")
            ->execute([$orderId, json_encode($items)]);
        $pdo->prepare("INSERT INTO ph_payments (order_id, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status) VALUES (?, ?, 'USD', 5.38, 1500, 279.0, 'CREATED')")
            ->execute([$orderId, $paypalOrderId]);
        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, status, expires_at) VALUES (?, 'prod-recon', 'prod-recon', 'RESERVED', ?)")
            ->execute([$orderId, $futureTime]);

        // Mock PayPal client where PayPal already completed the order remotely
        $reconClient = new class extends PhPayPalClient {
            public int $captureCalls = 0;
            public int $getOrderCalls = 0;
            public function __construct() {}
            public function getOrder(string $id): array {
                $this->getOrderCalls++;
                return [
                    'id' => $id,
                    'status' => 'COMPLETED',
                    'purchase_units' => [[
                        'payments' => [
                            'captures' => [[
                                'id' => 'CAP-RECON-RECOVERED',
                                'status' => 'COMPLETED',
                                'amount' => ['value' => '5.38', 'currency_code' => 'USD']
                            ]]
                        ]
                    ]]
                ];
            }
            public function captureOrder(string $id, ?string $exp = null): array {
                $this->captureCalls++;
                throw new Exception("Capture should NOT be called because order was already completed!");
            }
        };

        // When customer retries capture:
        $result = PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $reconClient);

        $this->assert("Reconciliation: Capture succeeded", $result['success'] === true);
        $this->assert("Reconciliation: Reconciled flag is true", $result['reconciled'] === true);
        $this->assert("Reconciliation: PayPal captureOrder was NOT called again (0 double captures)", $reconClient->captureCalls === 0);
        $this->assert("Reconciliation: PayPal getOrder was called to verify remote state", $reconClient->getOrderCalls >= 1);
        $this->assert("Reconciliation: Capture ID matches remote PayPal capture", $result['captureId'] === 'CAP-RECON-RECOVERED');

        // Verify local DB was reconciled atomically
        $payStatus = $pdo->query("SELECT status, capture_id FROM ph_payments WHERE order_id = '{$orderId}'")->fetch(PDO::FETCH_ASSOC);
        $ordStatus = $pdo->query("SELECT status FROM ph_orders WHERE id = '{$orderId}'")->fetchColumn();
        $resStatus = $pdo->query("SELECT status, active_slot FROM ph_inventory_reservations WHERE order_id = '{$orderId}'")->fetch(PDO::FETCH_ASSOC);

        $this->assert("Reconciliation: ph_payments marked COMPLETED", $payStatus['status'] === 'COMPLETED');
        $this->assert("Reconciliation: ph_payments has capture_id", $payStatus['capture_id'] === 'CAP-RECON-RECOVERED');
        $this->assert("Reconciliation: ph_orders marked Confirmed", $ordStatus === 'Confirmed');
        $this->assert("Reconciliation: Reservation marked COMMITTED", $resStatus['status'] === 'COMMITTED');
        $this->assert("Reconciliation: Active slot freed to NULL", $resStatus['active_slot'] === null);
    }

    // =========================================================================
    // SECTION 5: Additional Targeted Security & Edge Case Tests
    // =========================================================================

    private function testMigrationDeduplicationStatusTransition(): void {
        echo "\n--- Test 9: Migration Deduplication Status Transition (Older row becomes RELEASED) ---\n";
        $pdo = $this->createBasePdo();
        $pdo->exec("CREATE TABLE ph_inventory_reservations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id TEXT NOT NULL,
            product_id TEXT NOT NULL,
            active_slot TEXT,
            qty INTEGER NOT NULL DEFAULT 1,
            status TEXT NOT NULL DEFAULT 'RESERVED',
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");

        // Insert duplicate active_slot where BOTH were previously marked 'RESERVED'
        $pdo->exec("INSERT INTO ph_inventory_reservations (id, order_id, product_id, active_slot, status, expires_at) 
            VALUES (101, 'ord-dup-old', 'prod-dup', 'prod-dup', 'RESERVED', '2026-01-01 00:00:00')");
        $pdo->exec("INSERT INTO ph_inventory_reservations (id, order_id, product_id, active_slot, status, expires_at) 
            VALUES (102, 'ord-dup-new', 'prod-dup', 'prod-dup', 'RESERVED', '2099-01-01 00:00:00')");

        PhPayPalService::ensureTables($pdo);

        $rowOld = $pdo->query("SELECT status, active_slot FROM ph_inventory_reservations WHERE id = 101")->fetch(PDO::FETCH_ASSOC);
        $rowNew = $pdo->query("SELECT status, active_slot FROM ph_inventory_reservations WHERE id = 102")->fetch(PDO::FETCH_ASSOC);

        $this->assert("Dedup: Older duplicate row status transitioned to RELEASED", $rowOld['status'] === 'RELEASED');
        $this->assert("Dedup: Older duplicate row active_slot cleared to NULL", $rowOld['active_slot'] === null);
        $this->assert("Dedup: Newer active reservation retained RESERVED status", $rowNew['status'] === 'RESERVED');
        $this->assert("Dedup: Newer active reservation retained active_slot lock", $rowNew['active_slot'] === 'prod-dup');
    }

    private function testCaptureReservedNullActiveSlotRejected(): void {
        echo "\n--- Test 10: RESERVED Row With active_slot = NULL Must Fail captureOrder() ---\n";
        $pdo = $this->createBasePdo();
        PhPayPalService::ensureTables($pdo);
        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-null-slot', 'Null Slot Item', 1200, 1)");

        $orderId = 'ORD-NULL-SLOT';
        $paypalOrderId = 'PAY-NULL-SLOT';
        $items = [['id' => 'p-null-slot', 'name' => 'Null Slot Item', 'qty' => 1, 'price' => 1200]];
        $futureTime = gmdate('Y-m-d H:i:s', time() + 900);

        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, 1200, 1200, 0, 'Pending Payment', 'paypal', ?)")
            ->execute([$orderId, json_encode($items)]);
        $pdo->prepare("INSERT INTO ph_payments (order_id, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status) VALUES (?, ?, 'USD', 4.30, 1200, 279.0, 'CREATED')")
            ->execute([$orderId, $paypalOrderId]);

        // Malformed or deduplicated state: status is 'RESERVED', but active_slot is NULL!
        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, status, expires_at) VALUES (?, 'p-null-slot', NULL, 'RESERVED', ?)")
            ->execute([$orderId, $futureTime]);

        $mockClient = new class extends PhPayPalClient {
            public function __construct() {}
            public function getOrder(string $id): array { return ['status' => 'APPROVED']; }
            public function captureOrder(string $id, ?string $exp = null): array {
                throw new Exception("Capture should not be called!");
            }
        };

        $caught = false;
        $caughtMsg = '';
        try {
            PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $mockClient);
        } catch (DomainException $e) {
            $caught = true;
            $caughtMsg = $e->getMessage();
        }

        $this->assert("Null active_slot on RESERVED row was rejected by captureOrder", $caught);
        $this->assert("Rejection mentions active slot lock or NULL slot", str_contains($caughtMsg, 'active slot lock') || str_contains($caughtMsg, 'NULL'));
    }

    private function testMultiProductPartialExpiryOrReleaseRejection(): void {
        echo "\n--- Test 11: Multi-Product Order With Partial Expiry / Release Rejection ---\n";
        $pdo = $this->createBasePdo();
        PhPayPalService::ensureTables($pdo);

        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-multi-1', 'Item 1', 1000, 1)");
        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-multi-2', 'Item 2', 2000, 1)");
        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-multi-3', 'Item 3', 3000, 1)");

        $orderId = 'ORD-MULTI-DEFECT';
        $paypalOrderId = 'PAY-MULTI-DEFECT';
        $items = [
            ['id' => 'p-multi-1', 'name' => 'Item 1', 'qty' => 1, 'price' => 1000],
            ['id' => 'p-multi-2', 'name' => 'Item 2', 'qty' => 1, 'price' => 2000],
            ['id' => 'p-multi-3', 'name' => 'Item 3', 'qty' => 1, 'price' => 3000],
        ];
        $futureTime = gmdate('Y-m-d H:i:s', time() + 900);
        $pastTime = gmdate('Y-m-d H:i:s', time() - 300);

        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, 6000, 6000, 0, 'Pending Payment', 'paypal', ?)")
            ->execute([$orderId, json_encode($items)]);
        $pdo->prepare("INSERT INTO ph_payments (order_id, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status) VALUES (?, ?, 'USD', 21.50, 6000, 279.0, 'CREATED')")
            ->execute([$orderId, $paypalOrderId]);

        // Product 1: VALID RESERVED
        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, status, expires_at) VALUES (?, 'p-multi-1', 'p-multi-1', 'RESERVED', ?)")
            ->execute([$orderId, $futureTime]);
        // Product 2: VALID RESERVED
        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, status, expires_at) VALUES (?, 'p-multi-2', 'p-multi-2', 'RESERVED', ?)")
            ->execute([$orderId, $futureTime]);
        // Product 3: EXPIRED RESERVED
        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, status, expires_at) VALUES (?, 'p-multi-3', 'p-multi-3', 'RESERVED', ?)")
            ->execute([$orderId, $pastTime]);

        $mockClient = new class extends PhPayPalClient {
            public int $captureCalls = 0;
            public function __construct() {}
            public function getOrder(string $id): array { return ['status' => 'APPROVED']; }
            public function captureOrder(string $id, ?string $exp = null): array {
                $this->captureCalls++;
                return ['status' => 'COMPLETED'];
            }
        };

        $caught = false;
        $caughtMsg = '';
        try {
            PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $mockClient);
        } catch (DomainException $e) {
            $caught = true;
            $caughtMsg = $e->getMessage();
        }

        $this->assert("Multi-product order with 1 expired line was rejected from capture", $caught);
        $this->assert("Rejection specifically names expired product 'p-multi-3'", str_contains($caughtMsg, 'p-multi-3'));
        $this->assert("PayPal captureOrder was never called (0 calls)", $mockClient->captureCalls === 0);

        // Verify remaining valid reservations were released on failure
        $res1 = $pdo->query("SELECT status, active_slot FROM ph_inventory_reservations WHERE order_id = '{$orderId}' AND product_id = 'p-multi-1'")->fetch(PDO::FETCH_ASSOC);
        $this->assert("Multi-product failure released Product 1 reservation slot", $res1['status'] === 'RELEASED' && $res1['active_slot'] === null);
    }

    private function testMultiProductAllValidCaptureAndIdempotence(): void {
        echo "\n--- Test 12: Multi-Product Order With All Lines Valid Captures & Commits Idempotently ---\n";
        $pdo = $this->createBasePdo();
        PhPayPalService::ensureTables($pdo);

        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-ok-1', 'OK Item 1', 1000, 1)");
        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-ok-2', 'OK Item 2', 2000, 1)");

        $orderId = 'ORD-MULTI-OK';
        $paypalOrderId = 'PAY-MULTI-OK';
        $items = [
            ['id' => 'p-ok-1', 'name' => 'OK Item 1', 'qty' => 1, 'price' => 1000],
            ['id' => 'p-ok-2', 'name' => 'OK Item 2', 'qty' => 1, 'price' => 2000],
        ];
        $futureTime = gmdate('Y-m-d H:i:s', time() + 900);

        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, 3000, 3000, 0, 'Pending Payment', 'paypal', ?)")
            ->execute([$orderId, json_encode($items)]);
        $pdo->prepare("INSERT INTO ph_payments (order_id, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status) VALUES (?, ?, 'USD', 10.75, 3000, 279.0, 'CREATED')")
            ->execute([$orderId, $paypalOrderId]);

        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, status, expires_at) VALUES (?, 'p-ok-1', 'p-ok-1', 'RESERVED', ?)")
            ->execute([$orderId, $futureTime]);
        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, status, expires_at) VALUES (?, 'p-ok-2', 'p-ok-2', 'RESERVED', ?)")
            ->execute([$orderId, $futureTime]);

        $mockClient = new class extends PhPayPalClient {
            public int $captureCalls = 0;
            public function __construct() {}
            public function getOrder(string $id): array { return ['status' => 'APPROVED']; }
            public function captureOrder(string $id, ?string $exp = null): array {
                $this->captureCalls++;
                return [
                    'status' => 'COMPLETED',
                    'purchase_units' => [[
                        'payments' => [
                            'captures' => [[
                                'id' => 'CAP-MULTI-OK-1',
                                'status' => 'COMPLETED',
                                'amount' => ['value' => '10.75', 'currency_code' => 'USD']
                            ]]
                        ]
                    ]]
                ];
            }
        };

        $res = PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $mockClient);
        $this->assert("Multi-product capture succeeded", $res['success'] === true && $res['status'] === 'Confirmed');

        // Verify ALL lines are COMMITTED and their active_slot is NULL
        $stRes = $pdo->prepare("SELECT product_id, status, active_slot FROM ph_inventory_reservations WHERE order_id = ?");
        $stRes->execute([$orderId]);
        $allRes = $stRes->fetchAll(PDO::FETCH_ASSOC);

        $this->assert("Exactly 2 reservations exist for order", count($allRes) === 2);
        foreach ($allRes as $r) {
            $this->assert("Product {$r['product_id']} status is COMMITTED", $r['status'] === 'COMMITTED');
            $this->assert("Product {$r['product_id']} active_slot is freed to NULL", $r['active_slot'] === null);
        }

        // Verify Idempotent repeat capture
        $resDup = PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $mockClient);
        $this->assert("Multi-product idempotent repeat capture returns Confirmed duplicate", $resDup['isDuplicate'] === true && $resDup['status'] === 'Confirmed');
    }

    private function testCodAndBankTransferCompatibility(): void {
        echo "\n--- Test 13: Cash On Delivery & Direct Bank Transfer Compatibility ---\n";
        $pdo = $this->createBasePdo();
        PhPayPalService::ensureTables($pdo);

        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-cod-1', 'COD Item 1', 1500, 1)");
        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-cod-2', 'COD Item 2', 2500, 1)");

        // 13A: Cash on Delivery order calculation and persistence
        $itemsCod = [
            ['id' => 'p-cod-1', 'qty' => 2],
            ['id' => 'p-cod-2', 'qty' => 1],
        ];
        $cartTotalsCod = ph_calculate_cart_totals($pdo, $itemsCod, '');
        $this->assert("COD: Cart subtotal correctly calculated (2*1500 + 2500 = 5500)", (float)$cartTotalsCod['subtotal'] === 5500.0);
        $this->assert("COD: Shipping fee applied (PKR 200)", (float)$cartTotalsCod['shipping'] === 200.0);
        $this->assert("COD: Total is PKR 5700", (float)$cartTotalsCod['total'] === 5700.0);

        // COD orders persist with status 'Confirmed' without touching PayPal or reservations
        $orderIdCod = ph_generate_order_id();
        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, ?, ?, ?, 'Confirmed', 'cod', ?)")
            ->execute([$orderIdCod, $cartTotalsCod['total'], $cartTotalsCod['subtotal'], $cartTotalsCod['shipping'], json_encode($itemsCod)]);

        $codSavedStatus = $pdo->query("SELECT status, payment FROM ph_orders WHERE id = '{$orderIdCod}'")->fetch(PDO::FETCH_ASSOC);
        $this->assert("COD: Order saved with status 'Confirmed' and payment 'cod'", $codSavedStatus['status'] === 'Confirmed' && $codSavedStatus['payment'] === 'cod');

        // 13B: Direct Bank Transfer order
        $orderIdBank = ph_generate_order_id();
        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, ?, ?, ?, 'Pending Payment', 'bank', ?)")
            ->execute([$orderIdBank, $cartTotalsCod['total'], $cartTotalsCod['subtotal'], $cartTotalsCod['shipping'], json_encode($itemsCod)]);

        $bankSavedStatus = $pdo->query("SELECT status, payment FROM ph_orders WHERE id = '{$orderIdBank}'")->fetch(PDO::FETCH_ASSOC);
        $this->assert("Bank Transfer: Order saved with status 'Pending Payment' and payment 'bank'", $bankSavedStatus['status'] === 'Pending Payment' && $bankSavedStatus['payment'] === 'bank');
    }

    private function testDuplicateOrderLinesQuantityAggregation(): void {
        echo "\n--- Test 14: Duplicate Order Lines Quantity Aggregation (Addition) ---\n";
        $pdo = $this->createBasePdo();
        PhPayPalService::ensureTables($pdo);

        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-dup-line', 'Duplicate Line Item', 1000, 1)");

        $orderId = 'ORD-DUP-LINE-1';
        $paypalOrderId = 'PAY-DUP-LINE-1';
        // Same product appears in multiple order lines (e.g. line 1 with qty=2 and line 2 with qty=3)
        $items = [
            ['id' => 'p-dup-line', 'name' => 'Duplicate Line Item (Teal)', 'qty' => 2, 'price' => 1000],
            ['id' => 'p-dup-line', 'name' => 'Duplicate Line Item (White)', 'qty' => 3, 'price' => 1000],
        ];
        $futureTime = gmdate('Y-m-d H:i:s', time() + 900);

        // Reserve inventory using reserveInventory()
        PhPayPalService::reserveInventory($pdo, $orderId, $items, 900);

        // Verify that quantities were summed (2 + 3 = 5), not max()'d (3)
        $res = $pdo->query("SELECT qty, status, active_slot FROM ph_inventory_reservations WHERE order_id = '{$orderId}' AND product_id = 'p-dup-line'")->fetch(PDO::FETCH_ASSOC);
        $this->assert("ReserveInventory: Summed quantities across lines (2 + 3 = 5)", (int)$res['qty'] === 5);
        $this->assert("ReserveInventory: Reservation has active_slot = 'p-dup-line'", $res['active_slot'] === 'p-dup-line');
        $this->assert("ReserveInventory: Reservation status is 'RESERVED'", $res['status'] === 'RESERVED');

        // Initiate order and payment record
        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, 5200, 5000, 200, 'Pending Payment', 'paypal', ?)")
            ->execute([$orderId, json_encode($items)]);
        $pdo->prepare("INSERT INTO ph_payments (order_id, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status) VALUES (?, ?, 'USD', 18.64, 5200, 279.0, 'CREATED')")
            ->execute([$orderId, $paypalOrderId]);

        $mockClient = new class extends PhPayPalClient {
            public int $captureCalls = 0;
            public function __construct() {}
            public function getOrder(string $id): array { return ['status' => 'APPROVED']; }
            public function captureOrder(string $id, ?string $exp = null): array {
                $this->captureCalls++;
                return [
                    'status' => 'COMPLETED',
                    'purchase_units' => [[
                        'payments' => [
                            'captures' => [[
                                'id' => 'CAP-DUP-LINE-SUCCESS',
                                'status' => 'COMPLETED',
                                'amount' => ['value' => '18.64', 'currency_code' => 'USD']
                            ]]
                        ]
                    ]]
                ];
            }
        };

        // captureOrder() aggregates required quantities (2 + 3 = 5) and checks against reservation qty (5)
        $captureResult = PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $mockClient);
        $this->assert("CaptureOrder: Multi-line duplicate product captured successfully with aggregated qty=5", $captureResult['success'] === true && $captureResult['status'] === 'Confirmed');
        $this->assert("CaptureOrder: PayPal captureOrder was invoked once", $mockClient->captureCalls === 1);
    }

    private function testInsufficientReservationQuantityRejection(): void {
        echo "\n--- Test 15: Insufficient Reservation Quantity Rejection ---\n";
        $pdo = $this->createBasePdo();
        PhPayPalService::ensureTables($pdo);

        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-insufficient', 'Insufficient Stock Item', 1000, 1)");

        $orderId = 'ORD-INSUFFICIENT-1';
        $paypalOrderId = 'PAY-INSUFFICIENT-1';
        // Order requires qty=5
        $items = [
            ['id' => 'p-insufficient', 'name' => 'Item', 'qty' => 5, 'price' => 1000]
        ];
        $futureTime = gmdate('Y-m-d H:i:s', time() + 900);

        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, 5200, 5000, 200, 'Pending Payment', 'paypal', ?)")
            ->execute([$orderId, json_encode($items)]);
        $pdo->prepare("INSERT INTO ph_payments (order_id, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status) VALUES (?, ?, 'USD', 18.64, 5200, 279.0, 'CREATED')")
            ->execute([$orderId, $paypalOrderId]);

        // Reservation only holds qty=2 (insufficient for required qty=5)
        $pdo->prepare("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, qty, status, expires_at) VALUES (?, 'p-insufficient', 'p-insufficient', 2, 'RESERVED', ?)")
            ->execute([$orderId, $futureTime]);

        $mockClient = new class extends PhPayPalClient {
            public int $captureCalls = 0;
            public function __construct() {}
            public function getOrder(string $id): array { return ['status' => 'APPROVED']; }
            public function captureOrder(string $id, ?string $exp = null): array {
                $this->captureCalls++;
                return ['status' => 'COMPLETED'];
            }
        };

        $caught = false;
        $caughtMsg = '';
        try {
            PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $mockClient);
        } catch (DomainException $e) {
            $caught = true;
            $caughtMsg = $e->getMessage();
        }

        $this->assert("Insufficient reservation qty (2 < 5) was rejected before PayPal capture", $caught);
        $this->assert("Rejection message states reservation quantity is insufficient", str_contains($caughtMsg, 'insufficient') && str_contains($caughtMsg, '2') && str_contains($caughtMsg, '5'));
        $this->assert("Zero PayPal capture HTTP requests executed on insufficient reservation", $mockClient->captureCalls === 0);

        // Verify order and payment marked expired/failed
        $orderSt = $pdo->query("SELECT status FROM ph_orders WHERE id = '{$orderId}'")->fetchColumn();
        $paySt = $pdo->query("SELECT status FROM ph_payments WHERE order_id = '{$orderId}'")->fetchColumn();
        $this->assert("Order status updated to 'Reservation Expired'", $orderSt === 'Reservation Expired');
        $this->assert("Payment status updated to 'EXPIRED'", $paySt === 'EXPIRED');
    }

    private function testCorruptDuplicateReservationRowRejection(): void {
        echo "\n--- Test 16: Missing and Duplicate Reservation Row Handling ---\n";
        $pdo = $this->createBasePdo();
        PhPayPalService::ensureTables($pdo);

        // 16A: Missing Product Reservation Rejection
        $pdo->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-missing', 'Missing Res Item', 1000, 1)");
        $orderId = 'ORD-MISSING-RES-1';
        $paypalOrderId = 'PAY-MISSING-RES-1';
        $items = [['id' => 'p-missing', 'name' => 'Missing Res Item', 'qty' => 1, 'price' => 1000]];

        $pdo->prepare("INSERT INTO ph_orders (id, total, subtotal, shipping, status, payment, items_json) VALUES (?, 1200, 1000, 200, 'Pending Payment', 'paypal', ?)")
            ->execute([$orderId, json_encode($items)]);
        $pdo->prepare("INSERT INTO ph_payments (order_id, paypal_order_id, currency, amount_usd, amount_pkr, fx_rate, status) VALUES (?, ?, 'USD', 4.30, 1200, 279.0, 'CREATED')")
            ->execute([$orderId, $paypalOrderId]);

        // Zero reservations inserted for p-missing
        $mockClient = new class extends PhPayPalClient {
            public int $captureCalls = 0;
            public function __construct() {}
            public function getOrder(string $id): array { return ['status' => 'APPROVED']; }
            public function captureOrder(string $id, ?string $exp = null): array {
                $this->captureCalls++;
                return ['status' => 'COMPLETED'];
            }
        };

        $caughtMissing = false;
        $caughtMsgMissing = '';
        try {
            PhPayPalService::captureOrder($pdo, $orderId, $paypalOrderId, $mockClient);
        } catch (DomainException $e) {
            $caughtMissing = true;
            $caughtMsgMissing = $e->getMessage();
        }

        $this->assert("Missing product reservation strictly rejected by captureOrder", $caughtMissing);
        $this->assert("Rejection explicitly names missing product 'p-missing'", str_contains($caughtMsgMissing, 'p-missing') && str_contains($caughtMsgMissing, 'missing'));
        $this->assert("Zero PayPal capture calls made for missing reservation", $mockClient->captureCalls === 0);

        // 16B: Duplicate Legacy Rows Reconciled by Migration before Unique Constraint
        $pdo2 = $this->createBasePdo();
        // Create unconstrained reservations table
        $pdo2->exec("CREATE TABLE ph_inventory_reservations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id TEXT NOT NULL,
            product_id TEXT NOT NULL,
            active_slot TEXT,
            qty INTEGER NOT NULL DEFAULT 1,
            status TEXT NOT NULL DEFAULT 'RESERVED',
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");
        $pdo2->exec("INSERT INTO products (id, name, price, in_stock) VALUES ('p-dup-row', 'Dup Item', 1000, 1)");

        $futureTime = gmdate('Y-m-d H:i:s', time() + 900);
        $pdo2->exec("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, qty, status, expires_at) VALUES ('ord-dup-legacy', 'p-dup-row', 'p-dup-row', 1, 'RESERVED', '{$futureTime}')");
        $pdo2->exec("INSERT INTO ph_inventory_reservations (order_id, product_id, active_slot, qty, status, expires_at) VALUES ('ord-dup-legacy', 'p-dup-row', 'p-dup-row', 1, 'RESERVED', '{$futureTime}')");

        // Running ensureTables reconciles the duplicate rows (removes older duplicate, adds unique index)
        $actions = PhPayPalService::ensureTables($pdo2);
        $this->assert("Legacy duplicate rows reconciled by ensureTables", in_array('reconciled_duplicate_order_products', $actions) || in_array('reconciled_duplicate_active_slots', $actions));

        $count = (int)$pdo2->query("SELECT COUNT(*) FROM ph_inventory_reservations WHERE order_id = 'ord-dup-legacy' AND product_id = 'p-dup-row'")->fetchColumn();
        $this->assert("Legacy duplicate rows collapsed to strictly 1 row", $count === 1);
    }
}

// Run suite
$runner = new Stage4ReleaseBlockerTest();
$runner->runAll();
