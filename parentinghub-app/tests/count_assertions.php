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
    'test_mysql_integration.php'
];

echo "========================================================================\n";
echo "   Auditing Exact Assertion Totals Across All 14 Test Suites\n";
echo "========================================================================\n\n";

$suiteDetails = [];
$totalPassTags = 0;
$totalSelfReported = 0;

foreach ($tests as $idx => $t) {
    $cmd = 'C:\\xampp\\php\\php.exe ' . escapeshellarg(__DIR__ . '/' . $t);
    $out = shell_exec($cmd);
    
    // Count [PASS] tags
    $passTags = preg_match_all('/\[PASS\]/', $out);
    $totalPassTags += $passTags;

    // Search for self-reported summary in output
    $selfReported = 0;
    if (preg_match('/(\d+)\s+passed/i', $out, $m)) {
        $selfReported = (int)$m[1];
    } elseif (preg_match('/(\d+)\s*\/\s*(\d+)\s+Tests Passed/i', $out, $m)) {
        $selfReported = (int)$m[1];
    } elseif (preg_match('/ALL\s+(\d+)\s+DEMO SCENARIOS VERIFIED/i', $out, $m)) {
        $selfReported = (int)$m[1];
    } elseif (preg_match('/All\s+(\d+)\s+stages/i', $out, $m)) {
        $selfReported = (int)$m[1];
    }

    $totalSelfReported += $selfReported;

    $suiteDetails[] = [
        'index' => $idx + 1,
        'suite' => $t,
        'pass_tags' => $passTags,
        'self_reported' => $selfReported,
    ];
}

printf("%-4s %-38s | %-12s | %-14s\n", "No.", "Suite Name", "[PASS] Tags", "Self-Reported");
echo str_repeat("-", 72) . "\n";
foreach ($suiteDetails as $sd) {
    printf("[%02d] %-38s | %10d   | %12d\n", 
        $sd['index'], 
        $sd['suite'], 
        $sd['pass_tags'], 
        $sd['self_reported']
    );
}
echo str_repeat("=", 72) . "\n";
printf("%-43s | %10d   | %12d\n", "TOTALS", $totalPassTags, $totalSelfReported);
