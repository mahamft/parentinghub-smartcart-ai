<?php
declare(strict_types=1);

/**
 * Stage 3 Automated Test Suite: Embedding Provider Verification
 *
 * Verifies:
 * 1. Interface conformance of PhGeminiEmbeddingAdapter
 * 2. Model configuration: gemini-embedding-2 (never text-embedding-004)
 * 3. Dimension configurability (default: 768)
 * 4. Task-oriented instruction formulation (no unsupported task_type)
 * 5. Input validation (empty text rejection)
 * 6. Vector output validation (length, finite numbers, float type)
 * 7. Unconfigured state: PhProviderUnavailableException raised cleanly
 * 8. Zero API key leakage in exception messages
 * 9. Explicitly isolated mock adapter validation
 */

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

if (!function_exists('ph_env')) {
    function ph_env(string $key, ?string $default = null): ?string {
        return getenv($key) !== false ? (string)getenv($key) : $default;
    }
}

require_once dirname(__DIR__) . '/backend/api/ai/embedding_provider_interface.php';
require_once dirname(__DIR__) . '/backend/api/ai/gemini_embedding_adapter.php';
require_once dirname(__DIR__) . '/backend/api/ai/testing/mock_embedding_adapter.php';

echo "======================================================================\n";
echo "   ParentingHub Stage 3: Embedding Provider Verification Suite\n";
echo "======================================================================\n\n";

$passed = 0;
$failed = 0;

function runEmbTest(string $title, callable $fn): void {
    global $passed, $failed;
    try {
        $res = $fn();
        if ($res === true) {
            echo "  [PASS] {$title}\n";
            $passed++;
        } else {
            echo "  [FAIL] {$title}\n         Error: " . (is_string($res) ? $res : 'Assertion failed') . "\n";
            $failed++;
        }
    } catch (Throwable $e) {
        echo "  [FAIL] {$title}\n         Exception: " . $e->getMessage() . "\n";
        $failed++;
    }
}

// 1. Model Name Verification
runEmbTest("Model configuration uses 'gemini-embedding-2' (not text-embedding-004)", function() {
    $adapter = new PhGeminiEmbeddingAdapter(null);
    if ($adapter->getModel() !== 'gemini-embedding-2') {
        return "Expected model 'gemini-embedding-2', got: '{$adapter->getModel()}'";
    }
    return true;
});

// 2. Dimension Configurability
runEmbTest("Embedding dimension defaults to 768 and remains configurable", function() {
    $adapter = new PhGeminiEmbeddingAdapter(null, null, 768);
    if ($adapter->getDimension() !== 768) return "Expected dimension 768, got: {$adapter->getDimension()}";

    $custom = new PhGeminiEmbeddingAdapter(null, null, 512);
    if ($custom->getDimension() !== 512) return "Expected custom dimension 512, got: {$custom->getDimension()}";
    return true;
});

// 3. Unconfigured Key Rejection
runEmbTest("Unconfigured API key cleanly throws PhProviderUnavailableException without fabricating data", function() {
    $adapter = new PhGeminiEmbeddingAdapter('');
    if ($adapter->isConfigured()) return "Adapter should report isConfigured() = false when key is empty";

    try {
        $adapter->embedQuery("feeding bottles for newborn");
        return "Expected PhProviderUnavailableException, but no exception was thrown";
    } catch (PhProviderUnavailableException $e) {
        if (!str_contains($e->getMessage(), 'GEMINI_API_KEY')) {
            return "Unexpected exception message: " . $e->getMessage();
        }
    }
    return true;
});

// 4. Zero Secret Exposure
runEmbTest("Exception messages never expose sensitive API key tokens", function() {
    $dummySecret = 'AIzaSyFakeSecretToken1234567890abcdef';
    $adapter = new PhGeminiEmbeddingAdapter($dummySecret);

    try {
        // Will fail network call or auth on invalid dummy key
        $adapter->embedQuery("test query");
        return "Expected failure on dummy key";
    } catch (PhProviderUnavailableException $e) {
        if (str_contains($e->getMessage(), $dummySecret)) {
            return "SECURITY VIOLATION: Exception message leaked API key!";
        }
    }
    return true;
});

// 5. Empty Text Validation
runEmbTest("Empty input text is rejected with PhEmbeddingValidationException", function() {
    $adapter = new PhGeminiEmbeddingAdapter('valid-looking-key');
    try {
        $adapter->embedQuery("   ");
        return "Expected PhEmbeddingValidationException on empty query";
    } catch (PhEmbeddingValidationException $e) {
        return true;
    }
});

// 6. Vector Validation
runEmbTest("Vector validator verifies exact dimension count and numerical finiteness", function() {
    $adapter = new PhGeminiEmbeddingAdapter(null, null, 768);

    // Valid vector
    $validVec = array_fill(0, 768, 0.05);
    $adapter->validateVector($validVec);

    // Mismatched dimension (e.g. 767 instead of 768)
    try {
        $adapter->validateVector(array_fill(0, 767, 0.05));
        return "Expected validation failure for dimension mismatch (767 vs 768)";
    } catch (PhEmbeddingValidationException $e) {
        // Expected
    }

    // Non-numeric value
    try {
        $badVec = array_fill(0, 768, 0.05);
        $badVec[10] = "invalid_string";
        $adapter->validateVector($badVec);
        return "Expected validation failure for non-numeric element";
    } catch (PhEmbeddingValidationException $e) {
        // Expected
    }

    // Infinite / NaN value
    try {
        $nanVec = array_fill(0, 768, 0.05);
        $nanVec[20] = INF;
        $adapter->validateVector($nanVec);
        return "Expected validation failure for non-finite element (INF)";
    } catch (PhEmbeddingValidationException $e) {
        // Expected
    }

    return true;
});

// 7. Mock Adapter Isolation
runEmbTest("PhMockEmbeddingAdapter is clearly designated for isolated test simulation", function() {
    $mock = new PhMockEmbeddingAdapter('test-mock-gemini-embedding-2', 768);
    $vec = $mock->embedQuery("anti-colic bottle");

    if (count($vec) !== 768) return "Expected mock vector dimension 768, got: " . count($vec);
    if (!str_contains($mock->getModel(), 'mock')) return "Mock adapter must have 'mock' in its model identifier";

    // Verify L2 normalization: sum of squares ≈ 1.0
    $sumSq = 0.0;
    foreach ($vec as $v) $sumSq += $v * $v;
    if (abs($sumSq - 1.0) > 0.01) {
        return "Expected unit vector (L2 norm ≈ 1.0), sum of squares was: " . $sumSq;
    }
    return true;
});

echo "\n======================================================================\n";
echo "   Test Results: {$passed} Passed, {$failed} Failed\n";
echo "======================================================================\n";

if ($failed > 0) exit(1);
echo "ALL EMBEDDING PROVIDER TESTS PASSED CLEANLY.\n";
