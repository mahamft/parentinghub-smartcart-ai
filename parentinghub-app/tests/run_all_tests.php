<?php
$tests = [
    'test_pricing_engine.php',
    'test_stage2_suite.php',
    'test_stage2_targeted_qa.php',
    'test_commerce_regression.php',
    'test_demo_scenarios.php',
    'test_full_journey_e2e.php',
    'test_stage3_suite.php',
    'test_stage3_security_audit.php',
    'test_paypal_suite.php',
    'test_stage4_paypal_suite.php',
    'test_stage4_recovery_concurrency.php',
    'test_stage4_final_audit.php',
    'test_stage4_release_blocker.php',
    'test_mysql_integration.php',
    'test_mysql_transaction_fix.php',
    'test_ai_suite.php',
    'test_ai_recommendation_accuracy.php',
    'test_embedding_provider.php',
    'test_qdrant_vector_store.php',
    'test_knowledge_vector_sync.php',
    'test_stage3_integration.php',
    'test_stage3_provider_integration.php',
    'test_stage3_cross_language_queries.php'
];

$pass = 0;
$fail = 0;
$failedList = [];

foreach ($tests as $t) {
    echo "Running {$t}...\n";
    $cmd = 'C:\\xampp\\php\\php.exe ' . escapeshellarg(__DIR__ . '/' . $t);
    passthru($cmd, $code);
    if ($code === 0) {
        $pass++;
        echo ">>> [SUITE PASS] {$t}\n\n";
    } else {
        $fail++;
        $failedList[] = $t;
        echo ">>> [SUITE FAIL] {$t} (Exit Code: {$code})\n\n";
    }
}

echo "========================================================\n";
echo "REGRESSION & INTEGRATION TOTAL: {$pass}/" . count($tests) . " suites passed.\n";
if ($fail > 0) {
    echo "Failed suites:\n";
    foreach ($failedList as $fl) {
        echo " - {$fl}\n";
    }
    exit(1);
} else {
    echo "ALL SUITES PASSED CLEANLY.\n";
    exit(0);
}

