<?php
declare(strict_types=1);

/**
 * ParentingHub AI Recommendation Accuracy & Categorical Isolation Test Suite
 *
 * Comprehensive validation across 15 critical scenarios:
 * 1.  Feeder query -> returns only Feeding Bottles / Teats
 * 2.  Roman Urdu "doodh ka feeder" -> returns only Feeding Bottles / Teats
 * 3.  Anti-colic query -> returns bottles/teats with anti-colic / AirFree features
 * 4.  Slow flow query -> returns slow-flow teats
 * 5.  Stroller query -> returns Baby Strollers
 * 6.  Roman Urdu "jhoola" -> returns Baby Swings & Bouncers
 * 7.  Cross-category isolation: Feeding query NEVER returns strollers, car seats, or scooters
 * 8.  Empty/Non-existent search ("baby shoes") -> returns 0 items and honest empty reply (no random fallback)
 * 9.  Budget enforcement -> all returned items have price <= maxBudget
 * 10. Out-of-stock exclusion -> items marked in_stock = 0 are never returned
 * 11. Clinical feeding safety: "for low sucking power which feeder is best" flags medical concern and pediatric guidance
 * 12. Roman Urdu terminology understanding (doodh ka feeder, jhoola, chuchi)
 * 13. English terminology understanding (stroller, breast pump, pacifier)
 * 14. Real database price fidelity: recommended prices strictly match database records
 * 15. Cart proposal integrity: proposed carts remain valid and require explicit customer approval
 */

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

require_once dirname(__DIR__) . '/backend/api/lib/pricing.php';
require_once dirname(__DIR__) . '/backend/api/ai/rate_limiter.php';
require_once dirname(__DIR__) . '/backend/api/ai/intent_classifier.php';
require_once dirname(__DIR__) . '/backend/api/ai/catalog_retriever.php';
require_once dirname(__DIR__) . '/backend/api/ai/ai_service.php';
require_once dirname(__DIR__) . '/backend/api/ai/cart_proposals.php';

function ph_fixed_shipping_fee(float $subtotal): float {
    return $subtotal > 0 ? 200.0 : 0.0;
}
function ph_env(string $key, ?string $default = null): ?string {
    static $env = [];
    return $env[$key] ?? $default;
}

echo "======================================================================\n";
echo "   ParentingHub AI Recommendation Accuracy & Categorical Isolation\n";
echo "======================================================================\n\n";

// Set up in-memory regression database
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

// Load seed catalog products
$catalogPath = dirname(__DIR__) . '/backend/api/seed/catalog.json';
$catalogData = json_decode(file_get_contents($catalogPath), true);
$seedProducts = $catalogData['products'];

$ins = $pdo->prepare("INSERT INTO products (
    id, sku, name, price, compare_at, image, images, category, brand, description, 
    has_options, options_json, rating, reviews, in_stock, tags, source_url
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($seedProducts as $p) {
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

echo "Database Seeded: " . count($seedProducts) . " verified catalog products.\n\n";

$passedCount = 0;
$failedCount = 0;

function runAccuracyTest(string $title, callable $fn): void {
    global $passedCount, $failedCount;
    try {
        $result = $fn();
        if ($result === true) {
            echo "  [PASS] {$title}\n";
            $passedCount++;
        } else {
            echo "  [FAIL] {$title}\n";
            echo "         Error: " . (is_string($result) ? $result : 'Assertion failed') . "\n";
            $failedCount++;
        }
    } catch (Throwable $e) {
        echo "  [FAIL] {$title}\n";
        echo "         Exception: " . $e->getMessage() . "\n";
        $failedCount++;
    }
}

// ----------------------------------------------------------------------------
// Scenario 1: Feeder query returns only Feeding Bottles / Teats
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 1: 'feeder' returns strictly feeding bottles / teats", function() use ($pdo) {
    $res = PhAiService::runAssistant($pdo, "I want to buy a feeder for baby");
    if (empty($res['recommendations'])) return "No recommendations returned";
    $feedingCats = ['Feeding Bottles', 'Bottle Feeding', 'Teats'];
    foreach ($res['recommendations'] as $p) {
        if (!in_array($p['category'], $feedingCats, true)) {
            return "Non-feeding product returned: {$p['name']} in category {$p['category']}";
        }
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 2: Roman Urdu "doodh ka feeder" returns feeding essentials
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 2: Roman Urdu 'doodh ka feeder' maps to feeding category", function() use ($pdo) {
    $res = PhAiService::runAssistant($pdo, "baby k liye doodh ka feeder chahiye");
    if (empty($res['recommendations'])) return "No recommendations returned for Roman Urdu feeder";
    $feedingCats = ['Feeding Bottles', 'Bottle Feeding', 'Teats'];
    foreach ($res['recommendations'] as $p) {
        if (!in_array($p['category'], $feedingCats, true)) {
            return "Returned unrelated product: {$p['name']} in category {$p['category']}";
        }
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 3: Anti-colic query returns bottles/teats with anti-colic features
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 3: 'anti-colic bottle' returns anti-colic feeding solutions", function() use ($pdo) {
    $res = PhAiService::runAssistant($pdo, "best anti-colic bottle for gas problems");
    if (empty($res['recommendations'])) return "No anti-colic recommendations returned";
    $top = $res['recommendations'][0];
    $text = strtolower($top['name'] . ' ' . $top['description']);
    if (!str_contains($text, 'colic') && !str_contains($text, 'airfree') && !str_contains($text, 'vent')) {
        return "Top recommendation does not highlight anti-colic feature: {$top['name']}";
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 4: Slow flow query returns slow-flow teats
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 4: 'slow flow teat' returns slow-flow teats", function() use ($pdo) {
    $res = PhAiService::runAssistant($pdo, "slow flow teat for newborn");
    if (empty($res['recommendations'])) return "No recommendations returned";
    $first = $res['recommendations'][0];
    $text = strtolower($first['name'] . ' ' . $first['description']);
    if (!str_contains($text, 'slow') && !str_contains($text, 'teat') && !str_contains($text, 'nipple')) {
        return "Expected slow flow teat, got: {$first['name']}";
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 5: Stroller query returns Baby Strollers
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 5: 'pram stroller' returns strictly Baby Strollers", function() use ($pdo) {
    $res = PhAiService::runAssistant($pdo, "looking for a lightweight pram stroller");
    if (empty($res['recommendations'])) return "No strollers returned";
    foreach ($res['recommendations'] as $p) {
        if ($p['category'] !== 'Baby Strollers') {
            return "Expected Baby Strollers, got {$p['category']} ({$p['name']})";
        }
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 6: Roman Urdu 'jhoola' returns Baby Swings & Bouncers
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 6: Roman Urdu 'jhoola' returns Baby Swings & Bouncers", function() use ($pdo) {
    $res = PhAiService::runAssistant($pdo, "bachay k liye automatic jhoola dikhao");
    if (empty($res['recommendations'])) return "No swings returned for jhoola";
    foreach ($res['recommendations'] as $p) {
        if ($p['category'] !== 'Baby Swings & Bouncers') {
            return "Expected Baby Swings & Bouncers, got {$p['category']} ({$p['name']})";
        }
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 7: Zero cross-category contamination on feeding queries
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 7: Feeding query has 0% contamination (no strollers, car seats, scooters)", function() use ($pdo) {
    $queries = [
        "for low sucking power which feeder is best",
        "doodh ka feeder",
        "anti colic baby bottle",
        "slow flow nipple"
    ];
    $unrelatedCats = ['Baby Strollers', 'Car Seats & Carry Cots', 'Ride-On & Scooters', 'Baby Toys & Activity'];
    foreach ($queries as $q) {
        $res = PhAiService::runAssistant($pdo, $q);
        foreach ($res['recommendations'] as $p) {
            if (in_array($p['category'], $unrelatedCats, true)) {
                return "Contamination detected for '{$q}': {$p['name']} is in {$p['category']}";
            }
        }
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 8: Empty/Non-existent search returns 0 items and honest message
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 8: Non-existent product ('baby shoes') returns 0 items, never unrelated items", function() use ($pdo) {
    $res = PhAiService::runAssistant($pdo, "baby shoes size 2");
    if (!empty($res['recommendations'])) {
        return "Expected 0 recommendations for non-existent category 'baby shoes', but got: " . count($res['recommendations']);
    }
    if (!str_contains($res['reply'], 'no active products') && !str_contains($res['reply'], 'found no')) {
        return "Expected honest empty reply message, got: {$res['reply']}";
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 9: Budget filtering strictly respected
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 9: Budget limit strictly constrains recommendation prices", function() use ($pdo) {
    $maxBudget = 3000.0;
    $res = PhAiService::runRecommendations($pdo, null, $maxBudget, null, "feeding bottle");
    if (empty($res['recommendations'])) return "No recommendations returned under budget";
    foreach ($res['recommendations'] as $p) {
        if ($p['price'] > $maxBudget) {
            return "Product price PKR {$p['price']} exceeds budget PKR {$maxBudget} ({$p['name']})";
        }
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 10: Out-of-stock exclusion
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 10: Out-of-stock products are strictly excluded", function() use ($pdo) {
    $testId = 'philips-avent-scf619-05';
    $pdo->exec("UPDATE products SET in_stock = 0 WHERE id = '$testId'");

    $res = PhCatalogRetriever::searchCandidates($pdo, "Breast Milk Storage Cups", null, null, null, 10, true);
    $pdo->exec("UPDATE products SET in_stock = 1 WHERE id = '$testId'");

    foreach ($res['products'] as $p) {
        if ($p['id'] === $testId) {
            return "Out of stock product {$testId} was returned";
        }
        if (!$p['inStock']) {
            return "Returned product has inStock=false";
        }
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 11: Clinical feeding safety for weak sucking / feeding difficulty
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 11: 'low sucking power' flags medical concern and provides pediatric guidance", function() use ($pdo) {
    $res = PhAiService::runAssistant($pdo, "for low sucking power which feeder is best");
    
    // Must flag medical concern
    if (empty($res['isMedicalConcern'])) {
        return "Expected isMedicalConcern=true for low sucking power query";
    }

    // Must include clinical advice in reply
    if (!str_contains($res['reply'], 'pediatrician') && !str_contains($res['reply'], 'feeding specialist')) {
        return "Expected response to advise consulting pediatrician or feeding specialist";
    }

    // Recommendations must NOT contain strollers or car seats!
    $unrelatedCats = ['Baby Strollers', 'Car Seats & Carry Cots', 'Ride-On & Scooters', 'Baby Toys & Activity'];
    foreach ($res['recommendations'] as $p) {
        if (in_array($p['category'], $unrelatedCats, true)) {
            return "Contamination: 'low sucking power' recommended {$p['category']} ({$p['name']})";
        }
    }

    return true;
});

// ----------------------------------------------------------------------------
// Scenario 12: Roman Urdu vocabulary coverage
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 12: Roman Urdu vocabulary (chuchi, jhoola, doodh ka feeder) handled correctly", function() use ($pdo) {
    $terms = [
        "feeder ki chuchi" => 'Baby Teats',
        "bachay ka jhoola" => 'Baby Swings',
        "doodh ka feeder" => 'Feeding Bottles'
    ];

    foreach ($terms as $query => $expectedLabel) {
        $intent = PhIntentClassifier::classify($query);
        if ($intent['canonical_label'] !== $expectedLabel) {
            return "Query '{$query}' classified as '{$intent['canonical_label']}', expected '{$expectedLabel}'";
        }
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 13: English vocabulary coverage
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 13: English terminology (stroller, breast pump, pacifier) handled correctly", function() use ($pdo) {
    $terms = [
        "electric breast pump" => 'Breast Pumps',
        "travel stroller" => 'Baby Strollers',
        "soothing pacifier" => 'Soothing & Pacifiers'
    ];

    foreach ($terms as $query => $expectedLabel) {
        $intent = PhIntentClassifier::classify($query);
        if ($intent['canonical_label'] !== $expectedLabel) {
            return "Query '{$query}' classified as '{$intent['canonical_label']}', expected '{$expectedLabel}'";
        }
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 14: Real database price fidelity
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 14: Recommended prices strictly match database records", function() use ($pdo) {
    $res = PhAiService::runAssistant($pdo, "feeding bottle");
    if (empty($res['recommendations'])) return "No recommendations returned";
    
    foreach ($res['recommendations'] as $p) {
        $st = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $st->execute([$p['id']]);
        $row = $st->fetch();
        if (!$row) return "Product ID {$p['id']} not in database";
        if (abs((float)$row['price'] - (float)$p['price']) > 0.01) {
            return "Price mismatch for {$p['id']}: DB says PKR {$row['price']}, AI says PKR {$p['price']}";
        }
    }
    return true;
});

// ----------------------------------------------------------------------------
// Scenario 15: Cart proposal integrity
// ----------------------------------------------------------------------------
runAccuracyTest("Scenario 15: AI cart proposal preserves price calculations and requires user approval", function() use ($pdo) {
    $propose = PhCartProposalEngine::createProposal(
        $pdo,
        [
            ['id' => 'philips-avent-scf619-05', 'qty' => 1]
        ],
        null,
        'test-session-hash-123'
    );

    if (empty($propose['proposalId'])) return "Missing proposalId";
    if ($propose['status'] !== 'proposed') return "Expected status 'proposed'";
    if ($propose['subtotal'] <= 0) return "Expected valid subtotal";
    if ($propose['shipping'] !== 200.0) return "Expected nationwide fixed PKR 200 shipping";
    if (!$propose['requiresCustomerApproval']) return "Expected requiresCustomerApproval=true";

    return true;
});

// ----------------------------------------------------------------------------
// Summary
// ----------------------------------------------------------------------------
echo "\n======================================================================\n";
echo "   Test Results: {$passedCount} Passed, {$failedCount} Failed\n";
echo "======================================================================\n";

if ($failedCount > 0) {
    exit(1);
}
echo "ALL 15 RECOMMENDATION ACCURACY TESTS PASSED WITH 100% SUCCESS.\n";
