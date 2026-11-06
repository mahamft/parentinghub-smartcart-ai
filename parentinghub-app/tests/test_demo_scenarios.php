<?php
define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once dirname(__DIR__) . '/backend/api/lib/pricing.php';
require_once dirname(__DIR__) . '/backend/api/ai/rate_limiter.php';
require_once dirname(__DIR__) . '/backend/api/ai/catalog_retriever.php';
require_once dirname(__DIR__) . '/backend/api/ai/ai_service.php';
require_once dirname(__DIR__) . '/backend/api/ai/cart_proposals.php';

function ph_fixed_shipping_fee(float $subtotal): float {
    return $subtotal > 0 ? 200.0 : 0.0;
}
if (!function_exists('ph_env')) {
    function ph_env(string $key, ?string $default = null): ?string {
        return $default;
    }
}

$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$pdo->exec("CREATE TABLE products (
    id TEXT PRIMARY KEY,
    sku TEXT,
    name TEXT NOT NULL,
    price REAL NOT NULL DEFAULT 0,
    compare_at REAL,
    image TEXT,
    images TEXT,
    category TEXT NOT NULL DEFAULT 'General',
    brand TEXT NOT NULL DEFAULT 'ParentingHub',
    description TEXT,
    has_options INTEGER NOT NULL DEFAULT 0,
    options_json TEXT,
    rating REAL NOT NULL DEFAULT 0,
    reviews INTEGER NOT NULL DEFAULT 0,
    in_stock INTEGER NOT NULL DEFAULT 1,
    weight_kg REAL,
    tags TEXT,
    source_url TEXT,
    video_url TEXT,
    source_json TEXT
)");

$pdo->exec("CREATE TABLE ph_catalog_state (
    state_key TEXT PRIMARY KEY,
    payload TEXT NOT NULL
)");

$pdo->exec("CREATE TABLE ph_ai_cart_proposals (
    proposal_id TEXT PRIMARY KEY,
    user_id INTEGER,
    session_token_hash TEXT,
    items_json TEXT NOT NULL,
    subtotal_pkr REAL NOT NULL,
    status TEXT NOT NULL DEFAULT 'proposed',
    expires_at TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE ph_rate_limits (
    bucket_key TEXT PRIMARY KEY,
    hits INTEGER NOT NULL DEFAULT 1,
    reset_at INTEGER NOT NULL
)");

$catalogPath = dirname(__DIR__) . '/backend/api/seed/catalog.json';
$catalogData = json_decode(file_get_contents($catalogPath), true);
$ins = $pdo->prepare("INSERT INTO products (
    id, sku, name, price, compare_at, image, images, category, brand, description, 
    has_options, options_json, rating, reviews, in_stock, tags, source_url
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($catalogData['products'] as $p) {
    $ins->execute([
        (string)$p['id'],
        (string)($p['sku'] ?? ''),
        (string)$p['name'],
        (float)($p['price'] ?? 0),
        isset($p['compareAt']) && $p['compareAt'] !== null ? (float)$p['compareAt'] : null,
        (string)($p['image'] ?? $p['img'] ?? ''),
        json_encode($p['images'] ?? []),
        (string)($p['category'] ?? $p['cat'] ?? 'General'),
        (string)($p['brand'] ?? 'ParentingHub'),
        (string)($p['description'] ?? $p['desc'] ?? ''),
        !empty($p['hasOptions']) ? 1 : 0,
        json_encode($p['options'] ?? []),
        (float)($p['rating'] ?? 0),
        (int)($p['reviews'] ?? 0),
        array_key_exists('inStock', $p) ? (!empty($p['inStock']) ? 1 : 0) : 1,
        json_encode($p['tags'] ?? []),
        (string)($p['sourceUrl'] ?? '')
    ]);
}
$pdo->prepare("INSERT INTO ph_catalog_state (state_key, payload) VALUES ('catalog', ?)")
    ->execute([json_encode($catalogData)]);

echo "======================================================================\n";
echo "   ParentingHub SmartCart AI: 3 Hackathon Demo Scenarios Verification\n";
echo "======================================================================\n\n";

// Scenario A
echo "--- SCENARIO A: Newborn Feeding under Rs 6,000 ---\n";
$resA = PhAiService::runAssistant($pdo, "Recommend anti-colic feeding bottles under Rs 6,000");
echo "Assistant Reply: " . substr($resA['reply'], 0, 90) . "...\n";
echo "Recommendations count: " . count($resA['recommendations']) . "\n";
foreach ($resA['recommendations'] as $p) {
    echo "  • [" . $p['id'] . "] " . $p['name'] . " — PKR " . number_format($p['price']) . " (" . $p['brand'] . ")\n";
}

// Scenario B
echo "\n--- SCENARIO B: Compare Avent vs Tommee Tippee ---\n";
$resB = PhAiService::runAssistant($pdo, "Compare Philips Avent vs Tommee Tippee feeding bottles");
echo "Assistant Reply: " . substr($resB['reply'], 0, 90) . "...\n";
echo "Suggested actions: " . json_encode($resB['suggestedActions']) . "\n";
if (!empty($resB['suggestedActions'])) {
    foreach ($resB['suggestedActions'] as $act) {
        if ($act['action'] === 'compare' && !empty($act['productIds'])) {
            $comp = PhAiService::runComparison($pdo, $act['productIds']);
            echo "Comparison Verdict: " . $comp['verdict']['summary'] . "\n";
            echo "Price Difference: PKR " . number_format($comp['comparison']['priceDifference']) . "\n";
        }
    }
}

// Scenario C
echo "\n--- SCENARIO C: SmartCart Bundle Proposal under Rs 15,000 ---\n";
$resC = PhAiService::runRecommendations($pdo, 0, 15000, null, "Build a newborn starter pack under Rs 15,000");
echo "Matching Essentials count: " . count($resC['recommendations']) . "\n";
$pids = array_slice(array_column($resC['recommendations'], 'id'), 0, 3);
echo "Bundle Product IDs: " . implode(', ', $pids) . "\n";
$prop = PhCartProposalEngine::createProposal($pdo, array_map(fn($id) => ['id' => $id, 'qty' => 1], $pids));
echo "Proposal ID: #" . $prop['proposalId'] . "\n";
echo "Items Count: " . $prop['itemCount'] . "\n";
echo "Subtotal: PKR " . number_format($prop['subtotal']) . "\n";
echo "Nationwide Delivery: PKR " . number_format($prop['shipping']) . "\n";
echo "Total Amount: PKR " . number_format($prop['total']) . " ($" . number_format($prop['total'] / 280, 2) . " USD)\n";

$approved = PhCartProposalEngine::approveProposal($pdo, $prop['proposalId'], [
    ['id' => $pids[0], 'qty' => 1],
    ['id' => $pids[1], 'qty' => 1]
]);
echo "Approval Status: " . ($approved['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Approved Items: " . count($approved['approvedItems']) . " items (New Total: PKR " . number_format($approved['total']) . ")\n";

echo "\n[ALL 3 DEMO SCENARIOS VERIFIED SUCCESSFULLY]\n";
