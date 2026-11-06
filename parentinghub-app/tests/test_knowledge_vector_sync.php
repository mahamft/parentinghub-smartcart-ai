<?php
declare(strict_types=1);

/**
 * Stage 3 Automated Test Suite: Knowledge Vector Synchronization Verification
 *
 * Verifies:
 * 1. MySQL tracking table (ph_ai_knowledge_chunk_vectors) initialization
 * 2. Version-aware delta synchronization (identifies pending/new chunks)
 * 3. Batch synchronization with model and dimension tracking
 * 4. Idempotent re-sync: 0 duplicate points or unnecessary re-embeddings
 * 5. Content update synchronization on chunk hash change
 * 6. Orphan vector cleanup on document/chunk deletion
 * 7. Graceful containment when provider or vector store is unconfigured
 */

define('PH_ENTRY', true);
define('PH_TEST_SUITE', true);

if (!function_exists('ph_env')) {
    function ph_env(string $key, ?string $default = null): ?string {
        return getenv($key) !== false ? (string)getenv($key) : $default;
    }
}

require_once dirname(__DIR__) . '/backend/api/ai/embedding_provider_interface.php';
require_once dirname(__DIR__) . '/backend/api/ai/vector_store_interface.php';
require_once dirname(__DIR__) . '/backend/api/ai/knowledge_ingestion_service.php';
require_once dirname(__DIR__) . '/backend/api/ai/knowledge_vector_sync_service.php';
require_once dirname(__DIR__) . '/backend/api/ai/testing/mock_embedding_adapter.php';
require_once dirname(__DIR__) . '/backend/api/ai/testing/mock_vector_store_adapter.php';

echo "======================================================================\n";
echo "   ParentingHub Stage 3: Knowledge Vector Synchronization Suite\n";
echo "======================================================================\n\n";

// In-memory SQLite regression harness
$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$pdo->exec("CREATE TABLE ph_ai_knowledge_documents (
    id TEXT PRIMARY KEY,
    source_type TEXT NOT NULL,
    source_id TEXT NOT NULL,
    title TEXT NOT NULL,
    slug TEXT NOT NULL DEFAULT '',
    category TEXT NOT NULL DEFAULT 'General',
    author TEXT NOT NULL,
    author_credentials TEXT,
    is_medically_verified INTEGER NOT NULL DEFAULT 0,
    publication_status TEXT NOT NULL DEFAULT 'published',
    content_hash TEXT NOT NULL,
    version INTEGER NOT NULL DEFAULT 1,
    metadata_json TEXT,
    indexed_at TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE ph_ai_knowledge_chunks (
    chunk_id TEXT PRIMARY KEY,
    document_id TEXT NOT NULL,
    chunk_index INTEGER NOT NULL,
    heading TEXT,
    content TEXT NOT NULL,
    token_count INTEGER NOT NULL DEFAULT 0,
    content_hash TEXT NOT NULL,
    source_type TEXT NOT NULL,
    category TEXT NOT NULL DEFAULT 'General',
    metadata_json TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE ph_ai_knowledge_ingestion_jobs (
    job_id TEXT PRIMARY KEY,
    job_type TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending',
    total_sources INTEGER NOT NULL DEFAULT 0,
    processed_sources INTEGER NOT NULL DEFAULT 0,
    documents_created INTEGER NOT NULL DEFAULT 0,
    documents_updated INTEGER NOT NULL DEFAULT 0,
    documents_unchanged INTEGER NOT NULL DEFAULT 0,
    documents_deindexed INTEGER NOT NULL DEFAULT 0,
    chunks_created INTEGER NOT NULL DEFAULT 0,
    retry_count INTEGER NOT NULL DEFAULT 0,
    errors_json TEXT,
    started_at TEXT,
    completed_at TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

// Seed 2 sample documents and chunks
$doc1Id = 'doc:article:feeding-guide';
$pdo->exec("INSERT INTO ph_ai_knowledge_documents 
    (id, source_type, source_id, title, category, author, content_hash, version)
    VALUES ('{$doc1Id}', 'article', 'feeding-guide', 'Feeding Guide', 'Newborn', 'Dr. Ayesha', 'hash111', 1)");

$chk1Id = 'chk:article:feeding-guide:0';
$pdo->exec("INSERT INTO ph_ai_knowledge_chunks 
    (chunk_id, document_id, chunk_index, heading, content, token_count, content_hash, source_type, category)
    VALUES ('{$chk1Id}', '{$doc1Id}', 0, 'Overview', 'Feeding guide content for newborn infants.', 8, 'hash111', 'article', 'Newborn')");

$chk2Id = 'chk:article:feeding-guide:1';
$pdo->exec("INSERT INTO ph_ai_knowledge_chunks 
    (chunk_id, document_id, chunk_index, heading, content, token_count, content_hash, source_type, category)
    VALUES ('{$chk2Id}', '{$doc1Id}', 1, 'Paced Feeding', 'Paced feeding technique prevents colic discomfort.', 8, 'hash222', 'article', 'Newborn')");

$passed = 0;
$failed = 0;

function runSyncTest(string $title, callable $fn): void {
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

// 1. Schema Initialization
runSyncTest("PhKnowledgeVectorSyncService::ensureSchema creates chunk vector tracking table", function() use ($pdo) {
    PhKnowledgeVectorSyncService::ensureSchema($pdo);
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='ph_ai_knowledge_chunk_vectors'")->fetchAll();
    if (empty($tables)) return "ph_ai_knowledge_chunk_vectors was not created";
    return true;
});

// 2. Initial Synchronization
$embMock = new PhMockEmbeddingAdapter('gemini-embedding-2', 768);
$storeMock = new PhMockVectorStoreAdapter('test_sync_collection', 768);

runSyncTest("Initial synchronization embeds all pending chunks and updates MySQL tracking", function() use ($pdo, $embMock, $storeMock) {
    $report = PhKnowledgeVectorSyncService::syncAll($pdo, $embMock, $storeMock);

    if ($report['status'] !== 'completed') return "Expected status 'completed', got: {$report['status']}";
    if ($report['pendingChunks'] !== 2) return "Expected 2 pending chunks, found: {$report['pendingChunks']}";
    if ($report['synced'] !== 2) return "Expected 2 synced chunks, found: {$report['synced']}";
    if ($storeMock->count() !== 2) return "Expected 2 points in vector store, found: " . $storeMock->count();

    // Verify MySQL tracking records
    $trackedCount = (int)$pdo->query("SELECT COUNT(*) FROM ph_ai_knowledge_chunk_vectors WHERE sync_status = 'synced'")->fetchColumn();
    if ($trackedCount !== 2) return "Expected 2 tracked rows in MySQL, found: {$trackedCount}";

    return true;
});

// 3. Idempotence Check
runSyncTest("Subsequent synchronization is idempotent: 0 pending, 0 re-embedded", function() use ($pdo, $embMock, $storeMock) {
    $report = PhKnowledgeVectorSyncService::syncAll($pdo, $embMock, $storeMock);

    if ($report['pendingChunks'] !== 0) return "Expected 0 pending chunks on second run, got: {$report['pendingChunks']}";
    if ($report['synced'] !== 0) return "Expected 0 synced chunks on second run, got: {$report['synced']}";
    if ($report['unchanged'] !== 2) return "Expected 2 unchanged chunks, got: {$report['unchanged']}";

    return true;
});

// 4. Content Hash Modification
runSyncTest("Updating chunk content_hash triggers re-sync and updates vector store", function() use ($pdo, $embMock, $storeMock, $chk2Id) {
    // Modify chunk 2 in MySQL
    $pdo->exec("UPDATE ph_ai_knowledge_chunks SET content = 'Updated paced feeding guidance.', content_hash = 'hash222_updated' WHERE chunk_id = '{$chk2Id}'");

    $report = PhKnowledgeVectorSyncService::syncAll($pdo, $embMock, $storeMock);

    if ($report['pendingChunks'] !== 1) return "Expected 1 pending chunk after hash update, got: {$report['pendingChunks']}";
    if ($report['synced'] !== 1) return "Expected 1 synced chunk after hash update, got: {$report['synced']}";

    // Verify tracking table updated
    $updatedHash = $pdo->query("SELECT content_hash FROM ph_ai_knowledge_chunk_vectors WHERE chunk_id = '{$chk2Id}'")->fetchColumn();
    if ($updatedHash !== 'hash222_updated') return "Tracking table was not updated with new content hash";

    return true;
});

// 5. Orphan Vector Reconciliation
runSyncTest("Deleting a chunk from MySQL removes orphaned point from vector store", function() use ($pdo, $embMock, $storeMock, $chk2Id) {
    // Delete chunk 2 from MySQL
    $pdo->exec("DELETE FROM ph_ai_knowledge_chunks WHERE chunk_id = '{$chk2Id}'");

    $report = PhKnowledgeVectorSyncService::syncAll($pdo, $embMock, $storeMock);

    if ($report['orphansDeleted'] !== 1) return "Expected 1 orphan deleted, got: {$report['orphansDeleted']}";
    if ($storeMock->count() !== 1) return "Expected 1 point remaining in vector store, found: " . $storeMock->count();

    // Verify removed from tracking table
    $tracked = (int)$pdo->query("SELECT COUNT(*) FROM ph_ai_knowledge_chunk_vectors WHERE chunk_id = '{$chk2Id}'")->fetchColumn();
    if ($tracked !== 0) return "Deleted chunk remains in tracking table";

    return true;
});

// 6. Offline / Unconfigured Handling
runSyncTest("Offline vector store is contained gracefully and reported without crash", function() use ($pdo, $embMock) {
    $offlineStore = new PhMockVectorStoreAdapter('offline_collection', 768);
    $offlineStore->setAvailable(false);

    $report = PhKnowledgeVectorSyncService::syncAll($pdo, $embMock, $offlineStore);
    if ($report['status'] !== 'skipped_vector_store_offline') {
        return "Expected status 'skipped_vector_store_offline', got: {$report['status']}";
    }
    return true;
});

echo "\n======================================================================\n";
echo "   Test Results: {$passed} Passed, {$failed} Failed\n";
echo "======================================================================\n";

if ($failed > 0) exit(1);
echo "ALL KNOWLEDGE VECTOR SYNC TESTS PASSED CLEANLY.\n";
