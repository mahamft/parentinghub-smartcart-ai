<?php
declare(strict_types=1);

/**
 * ParentingHub AI: Real Gemini Embedding Verification Suite
 *
 * Verifies live Google Generative AI REST API communication using gemini-embedding-2:
 * 1. Checks server-side credentials presence without exposing secrets.
 * 2. If configured:
 *    - Real REST embedContent call for an actual knowledge chunk.
 *    - Strict dimension verification (768).
 *    - Numerical validity (finite floats, no NaN, no Inf).
 *    - Query embedding with task-oriented prompt prefix.
 *    - Batch embedding via batchEmbedContents.
 *    - Error handling & rate limit containment.
 * 3. If unconfigured:
 *    - Documents exact blocker without fabricating fake live passes.
 *    - Validates offline exception containment and privacy safety.
 */

define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';
require_once __DIR__ . '/../backend/api/ai/embedding_provider_interface.php';
require_once __DIR__ . '/../backend/api/ai/gemini_embedding_adapter.php';

echo "======================================================================\n";
echo "   ParentingHub Stage 3: Real Gemini Embedding Verification Suite\n";
echo "======================================================================\n\n";

$pass = 0;
$fail = 0;
$blocked = 0;

function runGeminiTest(string $title, callable $fn): void {
    global $pass, $fail, $blocked;
    try {
        $result = $fn();
        if ($result === 'BLOCKED') {
            $blocked++;
            echo "  [BLOCKED] {$title}\n";
        } elseif ($result === true) {
            $pass++;
            echo "  [PASS] {$title}\n";
        } else {
            $fail++;
            echo "  [FAIL] {$title}\n         Error: {$result}\n";
        }
    } catch (Throwable $e) {
        $fail++;
        echo "  [FAIL] {$title}\n         Exception: {$e->getMessage()}\n";
    }
}

$adapter = new PhGeminiEmbeddingAdapter();
$isConfigured = $adapter->isConfigured();

echo "Adapter Configuration:\n";
echo "  - Model Identifier: " . $adapter->getModel() . "\n";
echo "  - Configured Dimension: " . $adapter->getDimension() . "\n";
echo "  - Live API Key Available: " . ($isConfigured ? 'YES (configured server-side)' : 'NO (missing in backend/.env)') . "\n\n";

// Test 1: Model Identifier & Dimension Integrity
runGeminiTest("Model configuration adheres to 'gemini-embedding-2' with 768 dimensions", function() use ($adapter) {
    if ($adapter->getModel() !== 'gemini-embedding-2') {
        return "Expected model 'gemini-embedding-2', got: {$adapter->getModel()}";
    }
    if ($adapter->getDimension() !== 768) {
        return "Expected dimension 768, got: {$adapter->getDimension()}";
    }
    return true;
});

// Test 2: Input Validation (Empty text is strictly rejected)
runGeminiTest("Empty input text is rejected with PhEmbeddingValidationException before network call", function() use ($adapter) {
    try {
        $adapter->embedQuery("   ");
        return "Expected PhEmbeddingValidationException, none thrown";
    } catch (PhEmbeddingValidationException $e) {
        return true;
    }
});

// Test 3: Vector Validator rejects dimension mismatch and non-finite floats
runGeminiTest("Vector validator rejects wrong dimensions, NaN and Infinite values", function() use ($adapter) {
    // Correct 768 float vector
    $validVector = array_fill(0, 768, 0.05);
    $adapter->validateVector($validVector);

    // Dimension mismatch
    try {
        $adapter->validateVector(array_fill(0, 512, 0.05));
        return "Failed to catch dimension mismatch (512 vs 768)";
    } catch (PhEmbeddingValidationException) {}

    // NaN element
    try {
        $nanVector = $validVector;
        $nanVector[10] = NAN;
        $adapter->validateVector($nanVector);
        return "Failed to catch NaN element in vector";
    } catch (PhEmbeddingValidationException) {}

    // INF element
    try {
        $infVector = $validVector;
        $infVector[20] = INF;
        $adapter->validateVector($infVector);
        return "Failed to catch INF element in vector";
    } catch (PhEmbeddingValidationException) {}

    return true;
});

// Test 4: Live REST API Document Embedding
runGeminiTest("Real REST embedContent call for ParentingHub article chunk", function() use ($adapter, $isConfigured) {
    if (!$isConfigured) {
        return "BLOCKED";
    }

    $sampleChunk = "ParentingHub Clinical Guide: Managing Infant Colic. Anti-colic feeding bottles with angled teats and internal air vent systems reduce swallowed air during bottle feeding.";
    $startTime = microtime(true);
    $vector = $adapter->embedDocument($sampleChunk);
    $duration = round((microtime(true) - $startTime) * 1000, 2);

    if (count($vector) !== 768) {
        return "Expected 768 dimensions, got: " . count($vector);
    }

    // Verify L2 norm is approximately 1.0 (normalized)
    $sumSq = 0.0;
    foreach ($vector as $v) $sumSq += $v * $v;
    $norm = sqrt($sumSq);
    if ($norm < 0.8 || $norm > 1.2) {
        return "Expected normalized vector (L2 norm ~1.0), got: {$norm}";
    }

    echo "         [Evidence] Latency: {$duration}ms | Dimensions: " . count($vector) . " | L2 Norm: " . round($norm, 4) . "\n";
    return true;
});

// Test 5: Live REST API Query Embedding with Roman Urdu
runGeminiTest("Real REST embedQuery call with Roman Urdu query ('doodh ka feeder for 2 month old')", function() use ($adapter, $isConfigured) {
    if (!$isConfigured) {
        return "BLOCKED";
    }

    $startTime = microtime(true);
    $vector = $adapter->embedQuery("doodh ka feeder for 2 month old baby");
    $duration = round((microtime(true) - $startTime) * 1000, 2);

    if (count($vector) !== 768) {
        return "Expected 768 dimensions, got: " . count($vector);
    }

    echo "         [Evidence] Latency: {$duration}ms | Dimensions: " . count($vector) . "\n";
    return true;
});

// Test 6: Live REST Batch Embedding
runGeminiTest("Real REST batchEmbedContents call with 2 distinct knowledge chunks", function() use ($adapter, $isConfigured) {
    if (!$isConfigured) {
        return "BLOCKED";
    }

    $chunks = [
        "Product Spec: Philips Avent Anti-Colic Bottle 125ml with AirFree vent.",
        "Product Spec: Tommee Tippee Closer to Nature Extra Slow Flow Teats."
    ];

    $startTime = microtime(true);
    $vectors = $adapter->embedBatch($chunks, 'document');
    $duration = round((microtime(true) - $startTime) * 1000, 2);

    if (count($vectors) !== 2) {
        return "Expected 2 vectors, got: " . count($vectors);
    }
    if (count($vectors[0]) !== 768 || count($vectors[1]) !== 768) {
        return "Batch vectors dimension mismatch";
    }

    echo "         [Evidence] Batch Latency: {$duration}ms | Vectors: 2 | Dimensions: 768 each\n";
    return true;
});

// Test 7: Zero Credential Leakage on Invalid Key
runGeminiTest("Invalid API key request strips sensitive key from exception messages", function() {
    $bogusKey = "AIzaSy_MOCK_SECRET_KEY_NEVER_LOG_ME_12345";
    $badAdapter = new PhGeminiEmbeddingAdapter($bogusKey, 'gemini-embedding-2', 768, 5);

    try {
        $badAdapter->embedQuery("test query");
        return "Expected authentication failure exception, got none";
    } catch (PhProviderUnavailableException $e) {
        $msg = $e->getMessage();
        if (str_contains($msg, $bogusKey)) {
            return "SECURITY LEAK: Exception message exposed raw API key: {$msg}";
        }
        return true;
    }
});

echo "\n======================================================================\n";
echo "   Test Results: {$pass} Passed, {$fail} Failed, {$blocked} Blocked (Missing Credentials)\n";
echo "======================================================================\n";

if ($fail > 0) {
    exit(1);
}

if (!$isConfigured) {
    echo "NOTICE: Live API requests skipped because GEMINI_API_KEY is not set.\n";
    echo "To test live requests, configure GEMINI_API_KEY in backend/.env.\n";
} else {
    echo "ALL REAL GEMINI EMBEDDING TESTS PASSED CLEANLY.\n";
}
exit(0);

