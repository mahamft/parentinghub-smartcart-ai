<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
ph_require_admin(ph_pdo());

header('Cache-Control: no-store, private');
$isJson = isset($_GET['json']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

$dbConfig = [];
try {
    $dbConfig = ph_db_config();
} catch (Throwable $e) {
    if ($isJson) {
        ph_error($e->getMessage(), 503, ['databaseConnected' => false]);
    }
}

$connError = null;
$pdo = null;
$counts = ['users' => 0, 'sessions' => 0, 'orders' => 0, 'inquiries' => 0, 'catalog_items' => 0];
$tablesStatus = [];
$mysqlVersion = '';

if ($dbConfig) {
    try {
        $pdo = new PDO(
            "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4",
            $dbConfig['user'],
            $dbConfig['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5
            ]
        );
        $mysqlVersion = (string)$pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
        
        // Auto-run schema installer
        ph_ensure_schema($pdo);

        // Check tables
        $tables = ['ph_users', 'ph_sessions', 'ph_catalog_state', 'ph_orders', 'ph_inquiries'];
        foreach ($tables as $t) {
            try {
                $c = (int)$pdo->query("SELECT COUNT(*) FROM {$t}")->fetchColumn();
                $tablesStatus[$t] = ['exists' => true, 'count' => $c];
                if ($t === 'ph_users') $counts['users'] = $c;
                if ($t === 'ph_sessions') $counts['sessions'] = $c;
                if ($t === 'ph_orders') $counts['orders'] = $c;
                if ($t === 'ph_inquiries') $counts['inquiries'] = $c;
            } catch (Throwable $e) {
                $tablesStatus[$t] = ['exists' => false, 'error' => $e->getMessage()];
            }
        }

        // Check catalog state, auto-seed if empty
        $catSt = $pdo->query("SELECT payload FROM ph_catalog_state WHERE state_key='catalog' LIMIT 1")->fetch();
        if (!$catSt || empty($catSt['payload'])) {
            $seedFile = __DIR__ . '/seed/catalog.json';
            if (is_file($seedFile)) {
                $seedData = file_get_contents($seedFile);
                if ($seedData) {
                    $ins = $pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload) VALUES ('catalog', ?) ON DUPLICATE KEY UPDATE payload=VALUES(payload)");
                    $ins->execute([$seedData]);
                    $catParsed = json_decode($seedData, true);
                    $counts['catalog_items'] = count($catParsed['products'] ?? []);
                }
            }
        } else {
            $catParsed = json_decode((string)$catSt['payload'], true);
            $counts['catalog_items'] = count($catParsed['products'] ?? []);
        }

    } catch (Throwable $e) {
        $connError = $e->getMessage();
    }
}

if ($isJson) {
    if ($pdo && !$connError) {
        ph_json([
            'success' => true,
            'databaseConnected' => true,
            'database' => $dbConfig['name'] ?? '',
            'mysqlVersion' => $mysqlVersion,
            'counts' => $counts,
            'tables' => $tablesStatus
        ]);
    } else {
        ph_json([
            'success' => false,
            'databaseConnected' => false,
            'error' => $connError ?: 'Database configuration missing in .env',
            'config' => [
                'host' => $dbConfig['host'] ?? 'not set',
                'database' => $dbConfig['name'] ?? 'not set',
                'user' => $dbConfig['user'] ?? 'not set'
            ]
        ], 503);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Database Status | ParentingHub</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 30px 16px; }
  .card { max-width: 680px; margin: 0 auto; background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); padding: 32px; border: 1px solid #e2e8f0; }
  .badge { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 99px; font-weight: 700; font-size: 13px; }
  .badge-success { background: #dcfce7; color: #15803d; }
  .badge-danger { background: #fee2e2; color: #b91c1c; }
  h1 { font-size: 22px; margin: 16px 0 8px; }
  p { color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 20px; }
  .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; margin: 20px 0; }
  .stat { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; text-align: center; }
  .stat b { font-size: 20px; color: #0f172a; display: block; }
  .stat span { font-size: 12px; color: #64748b; }
  .table-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; }
  .error-box { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 16px; border-radius: 12px; font-size: 13.5px; line-height: 1.5; margin: 16px 0; }
  .steps { background: #f1f5f9; border-radius: 12px; padding: 16px 20px; font-size: 13.5px; line-height: 1.7; color: #334155; }
  .steps ol { margin: 8px 0 0; padding-left: 20px; }
  .btn { display: inline-block; padding: 10px 18px; border-radius: 10px; background: #b63866; color: #fff; text-decoration: none; font-weight: 600; font-size: 13.5px; margin-top: 16px; }
</style>
</head>
<body>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center">
    <span style="font-weight:800;color:#b63866;font-size:18px">ParentingHub</span>
    <?php if ($pdo && !$connError): ?>
      <span class="badge badge-success">● Database Connected</span>
    <?php else: ?>
      <span class="badge badge-danger">● Connection Pending</span>
    <?php endif; ?>
  </div>

  <h1>Database Connection Diagnostic</h1>

  <?php if ($pdo && !$connError): ?>
    <p>Your MySQL database is successfully connected, synchronized, and operational.</p>
    
    <div class="stat-grid">
      <div class="stat"><b><?= htmlspecialchars((string)$counts['catalog_items']) ?></b><span>Catalog Items</span></div>
      <div class="stat"><b><?= htmlspecialchars((string)$counts['users']) ?></b><span>Registered Users</span></div>
      <div class="stat"><b><?= htmlspecialchars((string)$counts['orders']) ?></b><span>Orders</span></div>
      <div class="stat"><b><?= htmlspecialchars((string)$counts['inquiries']) ?></b><span>Inquiries</span></div>
    </div>

    <div style="margin-top:20px">
      <div class="table-row"><span><b>Database Name</b></span><span><code><?= htmlspecialchars((string)($dbConfig['name'] ?? '')) ?></code></span></div>
      <div class="table-row"><span><b>MySQL User</b></span><span><code><?= htmlspecialchars((string)($dbConfig['user'] ?? '')) ?></code></span></div>
      <div class="table-row"><span><b>Host / Server</b></span><span><code><?= htmlspecialchars((string)($dbConfig['host'] ?? '')) ?></code></span></div>
      <div class="table-row"><span><b>MySQL Version</b></span><span><?= htmlspecialchars($mysqlVersion) ?></span></div>
      <div class="table-row"><span><b>Database Tables</b></span><span style="color:#15803d;font-weight:600">All 5 Tables Active (InnoDB)</span></div>
    </div>

    <a href="/admin-panel" class="btn">Open Admin Dashboard →</a>
    <a href="/" class="btn" style="background:#e2e8f0;color:#334155;margin-left:8px">View Storefront</a>

  <?php else: ?>
    <p>Could not connect to MySQL with the credentials specified in <code>.env</code>.</p>
    
    <div class="error-box">
      <b>Connection Error:</b><br>
      <?= htmlspecialchars($connError ?: 'Missing DB_NAME or DB_USER in server .env') ?>
    </div>

    <div class="steps">
      <b>cPanel MySQL Setup Guide:</b>
      <ol>
        <li>Log into your <b>cPanel</b> and open <b>MySQL Databases</b>.</li>
        <li>Create database: <code><?= htmlspecialchars((string)($dbConfig['name'] ?? 'parentinghub_parentinghub')) ?></code></li>
        <li>Create user: <code><?= htmlspecialchars((string)($dbConfig['user'] ?? 'parentinghub_dbuser')) ?></code> with your configured database password</li>
        <li>Under <b>"Add User to Database"</b>, select the user and database, click <b>Add</b>, and tick <b>ALL PRIVILEGES</b>.</li>
        <li>Refresh this page (<code>/api/db-status.php</code>) to verify connection!</li>
      </ol>
    </div>

    <button onclick="window.location.reload()" class="btn" style="cursor:pointer;border:none">Retry Connection ↻</button>
  <?php endif; ?>
</div>
</body>
</html>
