<?php
/**
 * ParentingHub SmartCart AI - MySQL Transaction Atomicity & Implicit Commit Prevention Test
 *
 * Specifically verifies:
 * 1. Test A: reserveInventory() inside an active MySQL transaction does NOT trigger an implicit commit.
 *            $pdo->inTransaction() remains true before, during, and after reserveInventory().
 * 2. Test B: $pdo->commit() succeeds cleanly without throwing PDOException "There is no active transaction".
 *            Committed rows exist and are verified.
 * 3. Test C: Deliberate failure / rollback scenario:
 *            $pdo->rollBack() cleanly and completely removes uncommitted order & reservation rows.
 * 4. Test D: Calling ensureTables() or reserveInventory() inside a transaction when tables do not exist
 *            safely raises LogicException rather than running DDL that would silently destroy transaction atomicity.
 * 5. Test E: MariaDB active_slot uniqueness, duplicate product line aggregation, and commit slot clearing.
 * 6. Test F: Full initiateOrder() lifecycle using PhPayPalClient mock on MySQL completes atomic transaction
 *            cleanly without any "There is no active transaction" exception.
 *
 * NOTE: Never makes remote PayPal API calls or captures real orders.
 */

declare(strict_types=1);

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_client.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_service.php';

date_default_timezone_set('UTC');

class MySQLTransactionFixTestSuite {
    private PDO $pdo;
    private int $passed = 0;
    private int $failed = 0;
    private array $errors = [];

    public function __construct() {
        $host = ph_env('DB_HOST', '127.0.0.1');
        $port = ph_env('DB_PORT', '3306');
        $name = ph_env('DB_NAME', 'parentinghub_db');
        $user = ph_env('DB_USER', 'root');
        $pass = ph_env('DB_PASS', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        $this->pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5
        ]);

        // Ensure schema exists before running transaction tests
        PhPayPalService::ensureTables($this->pdo);
    }

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
        echo "========================================================\n";
        echo "MySQL/MariaDB Transaction Atomicity & Implicit Commit Suite\n";
        echo "========================================================\n\n";

        $this->testA_ReserveInventoryPreservesActiveTransaction();
        $this->testB_CommitSucceedsWithoutImplicitCommitException();
        $this->testC_RollbackRemovesUncommittedRowsCompletely();
        $this->testD_LogicExceptionWhenTablesMissingInsideTransaction();
        $this->testE_ActiveSlotUniquenessAggregationAndCommitClearing();
        $this->testF_InitiateOrderCompletesAtomicTransactionOnMySQL();

        echo "\n========================================================\n";
        echo "TEST RESULTS: {$this->passed} passed, {$this->failed} failed.\n";
        if ($this->failed > 0) {
            echo "Errors:\n";
            foreach ($this->errors as $err) {
                echo " - {$err}\n";
            }
            exit(1);
        }
        echo "ALL MYSQL TRANSACTION ATOMICITY TESTS PASSED CLEANLY.\n";
        echo "========================================================\n";
        exit(0);
    }

    /**
     * Test A: Start transaction on MariaDB PDO, call reserveInventory(),
     * verify PDO::inTransaction() remains true throughout (no implicit DDL commit).
     */
    private function testA_ReserveInventoryPreservesActiveTransaction(): void {
        echo "[Test A] Verifying reserveInventory() does NOT cause implicit commit...\n";

        $orderId = 'PH-TEST-TXA-' . bin2hex(random_bytes(4));
        $productId = 'test-tx-prod-isolation';

        // Clean any residual test state for this test order
        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id = ?")->execute([$orderId]);

        $this->assert("Initial state: not in transaction", !$this->pdo->inTransaction());

        $this->pdo->beginTransaction();
        $this->assert("Transaction started: inTransaction() is true", $this->pdo->inTransaction());

        // Insert a dummy order inside transaction
        $this->pdo->prepare("INSERT INTO ph_orders (id, status, subtotal, shipping, discount, total, payment, address_json, items_json, created_at)
            VALUES (?, 'Pending Payment', 500, 0, 0, 500, 'paypal', '{}', '[]', CURRENT_TIMESTAMP)")
            ->execute([$orderId]);

        // Call reserveInventory inside active transaction
        PhPayPalService::reserveInventory($this->pdo, $orderId, [['id' => $productId, 'qty' => 1]], 900);

        // Crucial assertion: transaction MUST still be active!
        $this->assert("reserveInventory() did NOT implicitly commit: inTransaction() remains true", $this->pdo->inTransaction());

        // Rollback for test A clean-up
        $this->pdo->rollBack();
        $this->assert("Transaction rolled back cleanly", !$this->pdo->inTransaction());

        // Verify rows were rolled back
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM ph_orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $this->assert("Order row was rolled back", (int)$stmt->fetchColumn() === 0);

        $stmt2 = $this->pdo->prepare("SELECT COUNT(*) FROM ph_inventory_reservations WHERE order_id = ?");
        $stmt2->execute([$orderId]);
        $this->assert("Reservation row was rolled back", (int)$stmt2->fetchColumn() === 0);
    }

    /**
     * Test B: Start transaction, insert order + payment, call reserveInventory(), call $pdo->commit().
     * Verify commit succeeds without "There is no active transaction" exception.
     */
    private function testB_CommitSucceedsWithoutImplicitCommitException(): void {
        echo "\n[Test B] Verifying PDO commit succeeds after reserveInventory()...\n";

        $orderId = 'PH-TEST-TXB-' . bin2hex(random_bytes(4));
        $payPalId = 'PAYID-TEST-TXB-' . bin2hex(random_bytes(4));
        $productId = 'test-tx-prod-isolation';

        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id = ?")->execute([$orderId]);
        $this->pdo->prepare("DELETE FROM ph_payments WHERE order_id = ?")->execute([$orderId]);
        $this->pdo->prepare("DELETE FROM ph_orders WHERE id = ?")->execute([$orderId]);

        $commitSucceeded = false;
        $noActiveTxThrown = false;

        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("INSERT INTO ph_orders (id, status, subtotal, shipping, discount, total, payment, address_json, items_json, created_at)
                VALUES (?, 'Pending Payment', 500, 0, 0, 500, 'paypal', '{}', '[]', CURRENT_TIMESTAMP)")
                ->execute([$orderId]);

            $this->pdo->prepare("INSERT INTO ph_payments (order_id, provider, paypal_order_id, payment_token, currency, amount_usd, amount_pkr, fx_rate, status, created_at)
                VALUES (?, 'paypal', ?, 'tok-txb', 'USD', 2.50, 500, 200.0, 'CREATED', CURRENT_TIMESTAMP)")
                ->execute([$orderId, $payPalId]);

            PhPayPalService::reserveInventory($this->pdo, $orderId, [['id' => $productId, 'qty' => 1]], 900);

            $this->assert("inTransaction() still true right before commit", $this->pdo->inTransaction());

            $this->pdo->commit();
            $commitSucceeded = true;
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'There is no active transaction')) {
                $noActiveTxThrown = true;
            }
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }

        $this->assert("PDO commit succeeded without error", $commitSucceeded);
        $this->assert("'There is no active transaction' was NOT thrown", !$noActiveTxThrown);

        // Verify committed data
        $stOrder = $this->pdo->prepare("SELECT status FROM ph_orders WHERE id = ?");
        $stOrder->execute([$orderId]);
        $this->assert("Committed order exists in MySQL", ($stOrder->fetchColumn() === 'Pending Payment'));

        $stRes = $this->pdo->prepare("SELECT status, active_slot, qty FROM ph_inventory_reservations WHERE order_id = ?");
        $stRes->execute([$orderId]);
        $resRow = $stRes->fetch();
        $this->assert("Committed reservation exists with status RESERVED", ($resRow['status'] ?? '') === 'RESERVED');
        $this->assert("Committed reservation owns active_slot", ($resRow['active_slot'] ?? '') === $productId);

        // Cleanup
        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id = ?")->execute([$orderId]);
        $this->pdo->prepare("DELETE FROM ph_payments WHERE order_id = ?")->execute([$orderId]);
        $this->pdo->prepare("DELETE FROM ph_orders WHERE id = ?")->execute([$orderId]);
    }

    /**
     * Test C: Deliberate failure inside transaction: verify rollback removes uncommitted rows completely.
     */
    private function testC_RollbackRemovesUncommittedRowsCompletely(): void {
        echo "\n[Test C] Verifying deliberate failure rollback removes uncommitted records...\n";

        $orderId = 'PH-TEST-TXC-' . bin2hex(random_bytes(4));
        $productId = 'test-tx-prod-isolation';

        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id = ?")->execute([$orderId]);
        $this->pdo->prepare("DELETE FROM ph_orders WHERE id = ?")->execute([$orderId]);

        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("INSERT INTO ph_orders (id, status, subtotal, shipping, discount, total, payment, address_json, items_json, created_at)
                VALUES (?, 'Pending Payment', 500, 0, 0, 500, 'paypal', '{}', '[]', CURRENT_TIMESTAMP)")
                ->execute([$orderId]);

            PhPayPalService::reserveInventory($this->pdo, $orderId, [['id' => $productId, 'qty' => 1]], 900);

            // Simulate application error before commit
            throw new RuntimeException("Simulated mid-transaction failure");
        } catch (RuntimeException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }

        $this->assert("Transaction rolled back cleanly", !$this->pdo->inTransaction());

        // Verify order does NOT exist
        $stO = $this->pdo->prepare("SELECT COUNT(*) FROM ph_orders WHERE id = ?");
        $stO->execute([$orderId]);
        $this->assert("Order record removed by rollback", (int)$stO->fetchColumn() === 0);

        // Verify reservation does NOT exist
        $stR = $this->pdo->prepare("SELECT COUNT(*) FROM ph_inventory_reservations WHERE order_id = ?");
        $stR->execute([$orderId]);
        $this->assert("Reservation record removed by rollback", (int)$stR->fetchColumn() === 0);

        // Verify slot is NOT held
        $slotCheck = PhPayPalService::checkActiveReservation($this->pdo, $productId);
        $this->assert("Product slot is completely free after rollback", $slotCheck === null);
    }

    /**
     * Test D: Calling ensureTables() or reserveInventory() inside an active transaction
     * when tables do not exist raises LogicException.
     */
    private function testD_LogicExceptionWhenTablesMissingInsideTransaction(): void {
        echo "\n[Test D] Verifying LogicException if tables missing inside transaction...\n";

        // Create an isolated in-memory SQLite PDO without tables to test the safeguard
        $isolatedPdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $this->assert("Isolated DB has no tables initially", !PhPayPalService::hasRequiredTables($isolatedPdo));

        $isolatedPdo->beginTransaction();
        $threwLogicException = false;
        try {
            // Calling ensureTables inside transaction without prior setup must throw LogicException
            PhPayPalService::ensureTables($isolatedPdo);
        } catch (LogicException $e) {
            $threwLogicException = true;
        } finally {
            if ($isolatedPdo->inTransaction()) {
                $isolatedPdo->rollBack();
            }
        }
        $this->assert("ensureTables() threw LogicException inside transaction when schema missing", $threwLogicException);

        // Now test reserveInventory() on the same isolated PDO inside a transaction
        $isolatedPdo->beginTransaction();
        $reserveThrewLogicEx = false;
        try {
            PhPayPalService::reserveInventory($isolatedPdo, 'TEST-ORDER', [['id' => 'prod-1', 'qty' => 1]]);
        } catch (LogicException $e) {
            $reserveThrewLogicEx = true;
        } finally {
            if ($isolatedPdo->inTransaction()) {
                $isolatedPdo->rollBack();
            }
        }
        $this->assert("reserveInventory() threw LogicException inside transaction when schema missing", $reserveThrewLogicEx);
    }

    /**
     * Test E: MariaDB active_slot uniqueness, duplicate product line aggregation, and commit slot clearing.
     */
    private function testE_ActiveSlotUniquenessAggregationAndCommitClearing(): void {
        echo "\n[Test E] Verifying active_slot uniqueness, aggregation & commit on MariaDB...\n";

        $order1 = 'PH-TEST-TXE1-' . bin2hex(random_bytes(4));
        $order2 = 'PH-TEST-TXE2-' . bin2hex(random_bytes(4));
        $productId = 'test-tx-prod-isolation';

        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id IN (?, ?)")->execute([$order1, $order2]);

        // 1. Order 1 reserves product with duplicate lines (qty 1 + qty 2 = 3)
        $items = [
            ['id' => $productId, 'qty' => 1],
            ['id' => $productId, 'qty' => 2]
        ];

        $this->pdo->beginTransaction();
        PhPayPalService::reserveInventory($this->pdo, $order1, $items, 900);
        $this->pdo->commit();

        $res1 = PhPayPalService::getReservations($this->pdo, $order1);
        $this->assert("Duplicate lines aggregated into single reservation row", count($res1) === 1);
        $this->assert("Quantities summed correctly (1 + 2 = 3)", (int)($res1[0]['qty'] ?? 0) === 3);
        $this->assert("Order 1 holds active_slot", ($res1[0]['active_slot'] ?? '') === $productId);

        // 2. Order 2 attempts to reserve same product while Order 1 holds it -> DomainException
        $order2Blocked = false;
        try {
            $this->pdo->beginTransaction();
            PhPayPalService::reserveInventory($this->pdo, $order2, [['id' => $productId, 'qty' => 1]], 900);
            $this->pdo->commit();
        } catch (DomainException $e) {
            $order2Blocked = true;
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
        $this->assert("Order 2 blocked while Order 1 holds active_slot", $order2Blocked);

        // 3. Commit Order 1 reservation (simulate capture)
        $this->pdo->beginTransaction();
        PhPayPalService::commitInventoryReservation($this->pdo, $order1);
        $this->assert("commitInventoryReservation() preserved active transaction", $this->pdo->inTransaction());
        $this->pdo->commit();

        $stmt = $this->pdo->prepare("SELECT status, active_slot FROM ph_inventory_reservations WHERE order_id = ?");
        $stmt->execute([$order1]);
        $row1 = $stmt->fetch();
        $this->assert("Order 1 status changed to COMMITTED", ($row1['status'] ?? '') === 'COMMITTED');
        $this->assert("Order 1 active_slot freed to NULL after commit", $row1['active_slot'] === null);

        // 4. Order 2 can now reserve the product since active_slot is NULL
        $order2Succeeded = false;
        try {
            $this->pdo->beginTransaction();
            PhPayPalService::reserveInventory($this->pdo, $order2, [['id' => $productId, 'qty' => 1]], 900);
            $this->pdo->commit();
            $order2Succeeded = true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
        }
        $this->assert("Order 2 successfully claimed slot after Order 1 committed", $order2Succeeded);

        // Cleanup
        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id IN (?, ?)")->execute([$order1, $order2]);
    }

    /**
     * Test F: Full initiateOrder() lifecycle using PhPayPalClient mock on MySQL.
     * Verifies end-to-end initiateOrder transaction atomicity on MariaDB.
     */
    private function testF_InitiateOrderCompletesAtomicTransactionOnMySQL(): void {
        echo "\n[Test F] Verifying initiateOrder() atomic execution on MariaDB...\n";

        $mockClient = new PhPayPalClient(['PAYPAL_MODE' => 'mock']);
        $productId = 'philips-avent-citric-acid-10g';

        $body = [
            'address' => [
                'name' => 'Integration Tester',
                'phone' => '03001234567',
                'email' => 'integration@test.local',
                'address' => 'House 1, Street 1, F-7/2',
                'city' => 'Islamabad',
                'notes' => 'Transaction atomicity test'
            ],
            'items' => [
                ['id' => $productId, 'qty' => 1]
            ]
        ];

        // Reclaim any expired reservations so catalog product is ready
        PhPayPalService::reclaimExpiredReservations($this->pdo);

        $res = PhPayPalService::initiateOrder($this->pdo, $body, null, $mockClient);

        $this->assert("initiateOrder returned success", ($res['success'] ?? false) === true);
        $orderId = (string)($res['orderId'] ?? '');
        $paypalOrderId = (string)($res['paypalOrderId'] ?? '');
        $this->assert("Order ID generated", $orderId !== '');
        $this->assert("PayPal Order ID generated", $paypalOrderId !== '');

        // Verify order record in ph_orders
        $stOrder = $this->pdo->prepare("SELECT status, payment, total FROM ph_orders WHERE id = ?");
        $stOrder->execute([$orderId]);
        $orderRow = $stOrder->fetch();
        $this->assert("ph_orders row created with 'Pending Payment'", ($orderRow['status'] ?? '') === 'Pending Payment');
        $this->assert("ph_orders payment is 'paypal'", ($orderRow['payment'] ?? '') === 'paypal');

        // Verify payment record in ph_payments
        $stPay = $this->pdo->prepare("SELECT status, amount_usd, fx_rate FROM ph_payments WHERE order_id = ?");
        $stPay->execute([$orderId]);
        $payRow = $stPay->fetch();
        $this->assert("ph_payments row created with 'CREATED'", ($payRow['status'] ?? '') === 'CREATED');
        $this->assert("ph_payments amount_usd is positive", (float)($payRow['amount_usd'] ?? 0) > 0);

        // Verify inventory reservation in ph_inventory_reservations
        $stRes = $this->pdo->prepare("SELECT status, active_slot, qty FROM ph_inventory_reservations WHERE order_id = ?");
        $stRes->execute([$orderId]);
        $resRow = $stRes->fetch();
        $this->assert("ph_inventory_reservations row created with 'RESERVED'", ($resRow['status'] ?? '') === 'RESERVED');
        $this->assert("ph_inventory_reservations active_slot matches product", ($resRow['active_slot'] ?? '') === $productId);
        $this->assert("ph_inventory_reservations qty is 1", (int)($resRow['qty'] ?? 0) === 1);

        // Cleanup
        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id = ?")->execute([$orderId]);
        $this->pdo->prepare("DELETE FROM ph_payments WHERE order_id = ?")->execute([$orderId]);
        $this->pdo->prepare("DELETE FROM ph_orders WHERE id = ?")->execute([$orderId]);
    }
}

// Execute test suite
$suite = new MySQLTransactionFixTestSuite();
$suite->runAll();
