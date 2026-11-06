<?php
declare(strict_types=1);

/**
 * ParentingHub AI: Stage 3 Provider Integration Suite
 *
 * Runs the complete suite of Stage 3 real provider tests:
 * 1. test_gemini_real_embedding.php
 * 2. test_qdrant_real_connection.php
 * 3. test_real_vector_search.php
 *
 * Provides clear empirical reporting of real provider readiness vs mock simulation.
 */

define('PH_TEST_SUITE', true);

require_once __DIR__ . '/../backend/api/bootstrap.php';
require_once __DIR__ . '/../backend/api/ai/embedding_provider_interface.php';
require_once __DIR__ . '/../backend/api/ai/gemini_embedding_adapter.php';
require_once __DIR__ . '/../backend/api/ai/vector_store_interface.php';
require_once __DIR__ . '/../backend/api/ai/qdrant_vector_store_adapter.php';

echo "======================================================================\n";
echo "   ParentingHub Stage 3: Master Provider Integration Runner\n";
echo "======================================================================\n\n";

$suites = [
    'test_gemini_real_embedding.php',
    'test_qdrant_real_connection.php',
    'test_real_vector_search.php'
];

$pass = 0;
$fail = 0;

foreach ($suites as $s) {
    echo "--- Executing: {$s} ---\n";
    $cmd = 'C:\\xampp\\php\\php.exe ' . escapeshellarg(__DIR__ . '/' . $s);
    passthru($cmd, $code);
    if ($code === 0) {
        $pass++;
        echo ">>> [PROVIDER SUITE PASS] {$s}\n\n";
    } else {
        $fail++;
        echo ">>> [PROVIDER SUITE FAIL] {$s} (Exit Code: {$code})\n\n";
    }
}

echo "======================================================================\n";
echo "   Stage 3 Provider Integration Total: {$pass}/" . count($suites) . " suites passed.\n";
echo "======================================================================\n";

if ($fail > 0) {
    exit(1);
}

echo "ALL STAGE 3 PROVIDER INTEGRATION SUITES EXECUTED CLEANLY.\n";
exit(0);

