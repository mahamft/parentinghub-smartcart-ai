<?php
/**
 * ParentingHub SmartCart AI - MySQL/InnoDB Integration Verification Suite
 *
 * Verifies:
 * 1. MySQL/InnoDB engine and table integrity (17 tables, 287 catalog products).
 * 2. PhPayPalService::ensureTables() execution and idempotency against MySQL.
 * 3. Inventory reservation lifecycle on MySQL.
 * 4. Duplicate product lines aggregation in reservations.
 * 5. Unique active_slot constraint enforcement (preventing concurrent reservations).
 * 6. Composite UNIQUE(order_id, product_id) enforcement.
 * 7. Real InnoDB transaction rollback behavior.
 * 8. Reservation release and commit operations (clearing active_slot to NULL).
 */

declare(strict_types=1);

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_client.php';
require_once __DIR__ . '/../backend/api/paypal/paypal_service.php';

date_default_timezone_set('UTC');

class MySQLIntegrationTestSuite {
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
        echo "====================================================================\n";
        echo "Starting ParentingHub MySQL/InnoDB Integration Verification Suite\n";
        echo "====================================================================\n\n";

        $this->testDatabaseSchemaAndEngines();
        $this->testEnsureTablesAgainstMySQL();
        $this->testEnsureTablesIdempotency();
        $this->testInventoryReservationLifecycle();
        $this->testDuplicateProductLinesAggregation();
        $this->testUniqueActiveSlotConstraint();
        $this->testDuplicateOrderProductConstraint();
        $this->testTransactionRollback();
        $this->testReservationReleaseAndCommit();

        echo "\n====================================================================\n";
        echo "Test Results: {$this->passed} Passed, {$this->failed} Failed\n";
        echo "====================================================================\n";

        if ($this->failed > 0) {
            echo "Failures:\n";
            foreach ($this->errors as $err) {
                echo " - {$err}\n";
            }
            exit(1);
        }
    }

    /**
     * 1. Schema & Engine verification
     */
    private function testDatabaseSchemaAndEngines(): void {
        echo "[1/8] Verifying Database Tables and InnoDB Storage Engines...\n";

        $stmt = $this->pdo->query("SHOW TABLE STATUS");
        $tables = $stmt->fetchAll();
        $this->assert("Database has tables", count($tables) > 0, "Found " . count($tables) . " tables");

        $nonInnoDB = [];
        $tableNames = [];
        foreach ($tables as $t) {
            $tableNames[] = $t['Name'];
            if (strcasecmp((string)$t['Engine'], 'InnoDB') !== 0) {
                $nonInnoDB[] = $t['Name'] . " (" . $t['Engine'] . ")";
            }
        }

        $this->assert("All tables use InnoDB engine", empty($nonInnoDB), "Non-InnoDB: " . implode(', ', $nonInnoDB));

        // Required tables from init_local_dev.sql
        $expectedCoreTables = [
            'products',
            'legacy_products_archive',
            'ph_ai_cart_proposals',
            'ph_catalog_state',
            'ph_community_answers',
            'ph_community_posts',
            'ph_inquiries',
            'ph_newsletter_subscribers',
            'ph_orders',
            'ph_password_resets',
            'ph_payments',
            'ph_payment_events',
            'ph_rate_limits',
            'ph_sessions',
            'ph_users',
            'site_settings'
        ];

        foreach ($expectedCoreTables as $tbl) {
            $this->assert("Core table '{$tbl}' exists", in_array($tbl, $tableNames, true));
        }

        // Product count check
        $prodCount = (int)$this->pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $this->assert("Catalog has exactly 287 products", $prodCount === 287, "Actual count: {$prodCount}");
    }

    /**
     * 2. PhPayPalService::ensureTables() execution against MySQL
     */
    private function testEnsureTablesAgainstMySQL(): void {
        echo "\n[2/8] Executing PhPayPalService::ensureTables() against MySQL...\n";

        $actions = PhPayPalService::ensureTables($this->pdo);
        $this->assert("ensureTables() executed without fatal error", !in_array('fatal_error', $actions, true));

        // Verify ph_inventory_reservations exists and is InnoDB
        $stmt = $this->pdo->query("SHOW TABLE STATUS LIKE 'ph_inventory_reservations'");
        $status = $stmt->fetch();
        $this->assert("Table ph_inventory_reservations exists", !empty($status));
        $this->assert("ph_inventory_reservations uses InnoDB", strcasecmp((string)($status['Engine'] ?? ''), 'InnoDB') === 0);

        // Verify active_slot column
        $cols = $this->pdo->query("SHOW COLUMNS FROM ph_inventory_reservations LIKE 'active_slot'")->fetchAll();
        $this->assert("Column active_slot exists in ph_inventory_reservations", !empty($cols));

        // Verify uq_active_slot index
        $activeSlotIdx = $this->pdo->query("SHOW INDEX FROM ph_inventory_reservations WHERE Key_name = 'uq_active_slot'")->fetchAll();
        $this->assert("Unique index 'uq_active_slot' exists", !empty($activeSlotIdx) && ((int)$activeSlotIdx[0]['Non_unique'] === 0));

        // Verify uq_order_product composite index
        $orderProdIdx = $this->pdo->query("SHOW INDEX FROM ph_inventory_reservations WHERE Key_name = 'uq_order_product'")->fetchAll();
        $this->assert("Unique index 'uq_order_product' exists", !empty($orderProdIdx) && ((int)$orderProdIdx[0]['Non_unique'] === 0));
        $this->assert("uq_order_product covers 2 columns", count($orderProdIdx) === 2);
    }

    /**
     * 3. PhPayPalService::ensureTables() Idempotency
     */
    private function testEnsureTablesIdempotency(): void {
        echo "\n[3/8] Testing PhPayPalService::ensureTables() Idempotency...\n";

        $actions2 = PhPayPalService::ensureTables($this->pdo);
        $hasError = false;
        foreach ($actions2 as $act) {
            if (str_starts_with($act, 'error_') || str_starts_with($act, 'fatal_error')) {
                $hasError = true;
            }
        }
        $this->assert("Repeated ensureTables() call executes cleanly without error", !$hasError, "Actions: " . json_encode($actions2));
    }

    /**
     * 4. Inventory reservation lifecycle on MySQL
     */
    private function testInventoryReservationLifecycle(): void {
        echo "\n[4/8] Testing Inventory Reservation Lifecycle on MySQL/InnoDB...\n";

        $orderId = 'my-order-' . bin2hex(random_bytes(6));
        $productId = 'philips-avent-scf619-05';

        // Clean any leftover test rows
        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id = ? OR active_slot = ?")->execute([$orderId, $productId]);

        $items = [
            ['id' => $productId, 'qty' => 1]
        ];

        $resSuccess = false;
        try {
            PhPayPalService::reserveInventory($this->pdo, $orderId, $items, 900);
            $resSuccess = true;
        } catch (Throwable $e) {
            $resSuccess = false;
        }
        $this->assert("reserveInventory succeeds for available product", $resSuccess);

        // Verify database state
        $stmt = $this->pdo->prepare("SELECT * FROM ph_inventory_reservations WHERE order_id = ? AND product_id = ?");
        $stmt->execute([$orderId, $productId]);
        $row = $stmt->fetch();

        $this->assert("Reservation row exists in MySQL", !empty($row));
        $this->assert("Reservation status is RESERVED", ($row['status'] ?? '') === 'RESERVED');
        $this->assert("Reservation active_slot is set to product_id", ($row['active_slot'] ?? '') === $productId);
        $this->assert("Reservation qty is 1", (int)($row['qty'] ?? 0) === 1);
        $this->assert("Reservation expires_at is in future", strtotime((string)$row['expires_at']) > time());

        // Cleanup
        PhPayPalService::releaseInventoryReservation($this->pdo, $orderId);
    }

    /**
     * 5. Duplicate product lines aggregation
     */
    private function testDuplicateProductLinesAggregation(): void {
        echo "\n[5/8] Testing Duplicate Product Lines Aggregation on MySQL...\n";

        $orderId = 'my-dup-order-' . bin2hex(random_bytes(6));
        $productId = 'philips-avent-scf355-09';

        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id = ? OR active_slot = ?")->execute([$orderId, $productId]);

        // Same product appears twice with qty 1 and 2 (sum = 3)
        $items = [
            ['id' => $productId, 'qty' => 1],
            ['id' => $productId, 'qty' => 2]
        ];

        $resSuccess = false;
        try {
            PhPayPalService::reserveInventory($this->pdo, $orderId, $items, 900);
            $resSuccess = true;
        } catch (Throwable $e) {
            $resSuccess = false;
        }
        $this->assert("reserveInventory handles duplicated product lines", $resSuccess);

        // Check exactly one aggregated reservation row exists due to uq_order_product
        $stmt = $this->pdo->prepare("SELECT * FROM ph_inventory_reservations WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $rows = $stmt->fetchAll();

        $this->assert("Exactly one reservation row stored per (order_id, product_id)", count($rows) === 1);
        $this->assert("Aggregated reservation qty is 3", (int)$rows[0]['qty'] === 3);

        // Cleanup
        PhPayPalService::releaseInventoryReservation($this->pdo, $orderId);
    }

    /**
     * 6. Unique active_slot constraint enforcement
     */
    private function testUniqueActiveSlotConstraint(): void {
        echo "\n[6/8] Testing Unique active_slot Constraint Enforcement (InnoDB Collision)...\n";

        $order1 = 'order-slot-1-' . bin2hex(random_bytes(4));
        $order2 = 'order-slot-2-' . bin2hex(random_bytes(4));
        $productId = 'philips-avent-scy100-01';

        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id IN (?, ?) OR active_slot = ?")->execute([$order1, $order2, $productId]);

        // Customer 1 reserves product
        $cust1Success = false;
        try {
            PhPayPalService::reserveInventory($this->pdo, $order1, [['id' => $productId, 'qty' => 1]], 900);
            $cust1Success = true;
        } catch (Throwable $e) {
            $cust1Success = false;
        }
        $this->assert("Customer 1 successfully reserves product", $cust1Success);

        // Customer 2 attempts to reserve same product while Customer 1 holds active_slot
        $cust2Blocked = false;
        $cust2Msg = '';
        try {
            PhPayPalService::reserveInventory($this->pdo, $order2, [['id' => $productId, 'qty' => 1]], 900);
        } catch (DomainException $e) {
            $cust2Blocked = true;
            $cust2Msg = $e->getMessage();
        } catch (Throwable $e) {
            $cust2Blocked = true;
            $cust2Msg = $e->getMessage();
        }
        $this->assert("Customer 2 reservation is REJECTED while Customer 1 holds slot", $cust2Blocked);
        $this->assert("Customer 2 receives reserved warning", str_contains($cust2Msg, 'currently reserved'), $cust2Msg);

        // Test direct raw MySQL INSERT collision to verify InnoDB rejects duplicate active_slot with Error 1062
        $collisionCaught = false;
        try {
            $stmt = $this->pdo->prepare("INSERT INTO ph_inventory_reservations 
                (order_id, product_id, active_slot, qty, status, expires_at) 
                VALUES (?, ?, ?, 1, 'RESERVED', DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
            $stmt->execute([$order2, $productId, $productId]);
        } catch (PDOException $e) {
            $collisionCaught = true;
            $this->assert("InnoDB triggers SQLSTATE 23000 / Error 1062 for duplicate active_slot", 
                str_contains($e->getMessage(), '23000') || str_contains($e->getMessage(), '1062') || str_contains($e->getMessage(), 'uq_active_slot'),
                $e->getMessage());
        }
        $this->assert("Raw duplicate active_slot insert was rejected by MySQL", $collisionCaught);

        // Cleanup
        PhPayPalService::releaseInventoryReservation($this->pdo, $order1);
    }

    /**
     * 7. Composite UNIQUE(order_id, product_id) enforcement
     */
    private function testDuplicateOrderProductConstraint(): void {
        echo "\n[7/8] Testing Composite UNIQUE(order_id, product_id) Constraint...\n";

        $orderId = 'order-comp-' . bin2hex(random_bytes(4));
        $prod1 = 'philips-avent-scy103-01';

        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id = ?")->execute([$orderId]);

        // Insert first row directly
        $stmt = $this->pdo->prepare("INSERT INTO ph_inventory_reservations 
            (order_id, product_id, active_slot, qty, status, expires_at) 
            VALUES (?, ?, ?, 1, 'RESERVED', DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
        $stmt->execute([$orderId, $prod1, $prod1]);

        // Attempt second row with same order_id and product_id
        $compositeCaught = false;
        try {
            $stmt = $this->pdo->prepare("INSERT INTO ph_inventory_reservations 
                (order_id, product_id, active_slot, qty, status, expires_at) 
                VALUES (?, ?, NULL, 2, 'RELEASED', DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
            $stmt->execute([$orderId, $prod1]);
        } catch (PDOException $e) {
            $compositeCaught = true;
            $this->assert("InnoDB triggers SQLSTATE 23000 / Error 1062 for duplicate (order_id, product_id)",
                str_contains($e->getMessage(), '23000') || str_contains($e->getMessage(), '1062') || str_contains($e->getMessage(), 'uq_order_product'),
                $e->getMessage());
        }
        $this->assert("Raw duplicate (order_id, product_id) insert was rejected by MySQL", $compositeCaught);

        // Cleanup
        PhPayPalService::releaseInventoryReservation($this->pdo, $orderId);
    }

    /**
     * 8. Real InnoDB Transaction Rollback Behavior
     */
    private function testTransactionRollback(): void {
        echo "\n[8/8] Testing Real InnoDB Transaction Rollback Behavior...\n";

        $testOrderId = 'tx-rollback-' . bin2hex(random_bytes(4));
        $prodId = 'philips-avent-scy106-01';

        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id = ? OR active_slot = ?")->execute([$testOrderId, $prodId]);

        // Begin transaction
        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare("INSERT INTO ph_inventory_reservations 
            (order_id, product_id, active_slot, qty, status, expires_at) 
            VALUES (?, ?, ?, 1, 'RESERVED', DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
        $stmt->execute([$testOrderId, $prodId, $prodId]);

        // Row is visible inside the transaction
        $inTxCount = (int)$this->pdo->query("SELECT COUNT(*) FROM ph_inventory_reservations WHERE order_id = '{$testOrderId}'")->fetchColumn();
        $this->assert("Row is visible inside active transaction", $inTxCount === 1);

        // Rollback
        $this->pdo->rollBack();

        // Row MUST NOT exist after rollback
        $afterCount = (int)$this->pdo->query("SELECT COUNT(*) FROM ph_inventory_reservations WHERE order_id = '{$testOrderId}'")->fetchColumn();
        $this->assert("Row was completely rolled back by InnoDB", $afterCount === 0);
    }

    /**
     * 9. Reservation release and commit operations
     */
    private function testReservationReleaseAndCommit(): void {
        echo "\n[Bonus] Testing Reservation Release and Commit (active_slot -> NULL)...\n";

        $orderA = 'rel-order-A-' . bin2hex(random_bytes(4));
        $orderB = 'rel-order-B-' . bin2hex(random_bytes(4));
        $productId = 'philips-avent-scd180-01';

        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id IN (?, ?) OR active_slot = ?")->execute([$orderA, $orderB, $productId]);

        // Order A reserves
        $resASuccess = false;
        try {
            PhPayPalService::reserveInventory($this->pdo, $orderA, [['id' => $productId, 'qty' => 1]], 900);
            $resASuccess = true;
        } catch (Throwable $e) {
            $resASuccess = false;
        }
        $this->assert("Order A reserves product", $resASuccess);

        // Release Order A
        PhPayPalService::releaseInventoryReservation($this->pdo, $orderA);

        // Verify Order A row has status 'RELEASED' and active_slot is NULL
        $stmt = $this->pdo->prepare("SELECT status, active_slot FROM ph_inventory_reservations WHERE order_id = ?");
        $stmt->execute([$orderA]);
        $rowA = $stmt->fetch();
        $this->assert("Order A status changed to RELEASED", ($rowA['status'] ?? '') === 'RELEASED');
        $this->assert("Order A active_slot cleared to NULL", $rowA['active_slot'] === null);

        // Order B should now be able to reserve the product immediately
        $resBSuccess = false;
        try {
            PhPayPalService::reserveInventory($this->pdo, $orderB, [['id' => $productId, 'qty' => 1]], 900);
            $resBSuccess = true;
        } catch (Throwable $e) {
            $resBSuccess = false;
        }
        $this->assert("Order B can reserve now that Order A released active_slot", $resBSuccess);

        // Commit Order B reservation
        PhPayPalService::commitInventoryReservation($this->pdo, $orderB);

        // Verify Order B row has status 'COMMITTED' and active_slot is NULL
        $stmt->execute([$orderB]);
        $rowB = $stmt->fetch();
        $this->assert("Order B status changed to COMMITTED", ($rowB['status'] ?? '') === 'COMMITTED');
        $this->assert("Order B active_slot cleared to NULL after commit", $rowB['active_slot'] === null);

        // Cleanup
        $this->pdo->prepare("DELETE FROM ph_inventory_reservations WHERE order_id IN (?, ?)")->execute([$orderA, $orderB]);
    }
}

// Run test suite
$suite = new MySQLIntegrationTestSuite();
$suite->runAll();
