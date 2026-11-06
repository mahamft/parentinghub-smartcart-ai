<?php
declare(strict_types=1);

/**
 * Stage 3 Automated Integration Test Suite
 *
 * Verifies end-to-end integration across:
 * 1. Knowledge Ingestion (Articles + Products -> MySQL Chunks)
 * 2. Vector Synchronization (MySQL Chunks -> Embedding Provider -> Vector Store)
 * 3. Point and metadata verification
 * 4. Semantic vector search simulation
 * 5. Category filtering and thresholding
 * 6. Medical verification default state
 * 7. Graceful fallback to lexical retrieval when vector store is offline
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
echo "   ParentingHub Stage 3: End-to-End Integration Test Suite\n";
echo "======================================================================\n\n";

// In-memory regression database
$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$pdo->exec("CREATE TABLE articles (
    id TEXT PRIMARY KEY,
    title TEXT NOT NULL,
    excerpt TEXT,
    cover TEXT,
    author TEXT,
    date TEXT,
    read_time TEXT,
    category TEXT,
    body_json TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

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
    source_url TEXT
)");

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

$pdo->exec("CREATE TABLE ph_ai_knowledge_chunk_vectors (
    chunk_id TEXT PRIMARY KEY,
    document_id TEXT NOT NULL,
    qdrant_point_id TEXT NOT NULL,
    embedding_model TEXT NOT NULL,
    embedding_dimension INTEGER NOT NULL,
    content_hash TEXT NOT NULL,
    sync_status TEXT NOT NULL DEFAULT 'synced',
    synced_at TEXT DEFAULT CURRENT_TIMESTAMP,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

// Seed articles
$artStmt = $pdo->prepare("INSERT INTO articles (id, title, excerpt, author, category, body_json) VALUES (?, ?, ?, ?, ?, ?)");
$artStmt->execute([
    'anti-colic-feeding-guide',
    'Anti-Colic Bottles Explained',
    'AirFree vents prevent infant gas',
    'Dr. Ayesha Siddiqui, Pediatric Specialist',
    'Newborn',
    json_encode(['Paragraph 1 on anti colic mechanisms.', 'Paragraph 2 on feeding angles.'])
]);

$artStmt->execute([
    'bottle-sterilization-pakistan',
    'Bottle Sterilization in Pakistan',
    'Steam vs microwave sterilization',
    'Dr. Fatima Zahra, Maternal Health Consultant',
    'Feeding',
    json_encode(['Steam sterilization reaches 100 degrees Celsius.', 'Descale sterilizers regularly.'])
]);

// Seed sample products
$prodStmt = $pdo->prepare("INSERT INTO products (id, sku, name, price, category, brand, description, tags, in_stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$prodStmt->execute([
    'philips-avent-bottle-125ml',
    'SCF100',
    'Anti-Colic Bottle 125ml with Airfree vent',
    5220.0,
    'Feeding Bottles',
    'Philips Avent',
    'Clinically proven to reduce colic and gas.',
    json_encode(['feeding', 'anti-colic']),
    1
]);

$prodStmt->execute([
    'kidilo-d8-stroller',
    'KID-D8',
    'Kidilo D8 Luxury Reversible Stroller',
    19700.0,
    'Baby Strollers',
    'Kidilo',
    'High landscape reversible travel pram.',
    json_encode(['stroller', 'pram']),
    1
]);

$passed = 0;
$failed = 0;

function runIntTest(string $title, callable $fn): void {
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

// 1. Ingest Knowledge
runIntTest("Step 1: PhKnowledgeIngestionService ingests seed sources into MySQL documents and chunks", function() use ($pdo) {
    $stats = PhKnowledgeIngestionService::ingestAll($pdo);
    if ($stats['processed'] !== 4) return "Expected 4 processed sources, got: {$stats['processed']}";
    if ($stats['created'] !== 4) return "Expected 4 created documents, got: {$stats['created']}";
    if ($stats['chunks'] < 6) return "Expected at least 6 chunks, got: {$stats['chunks']}";
    return true;
});

// 2. Vector Synchronization
$embMock = new PhMockEmbeddingAdapter('gemini-embedding-2', 768);
$storeMock = new PhMockVectorStoreAdapter('parentinghub_integration_test', 768);

runIntTest("Step 2: PhKnowledgeVectorSyncService synchronizes all chunks into vector store", function() use ($pdo, $embMock, $storeMock) {
    $report = PhKnowledgeVectorSyncService::syncAll($pdo, $embMock, $storeMock);
    if ($report['status'] !== 'completed') return "Sync did not complete: {$report['status']}";
    if ($report['synced'] < 6) return "Expected at least 6 synced chunks, got: {$report['synced']}";
    if ($storeMock->count() < 6) return "Vector store count mismatch";
    return true;
});

// 3. Metadata Verification
runIntTest("Step 3: Point payloads preserve document version, source_id, and category metadata", function() use ($storeMock) {
    $pointId = PhQdrantVectorStoreAdapter::toUuidV5('chk:article:anti-colic-feeding-guide:1');
    $retrieved = $storeMock->retrieve([$pointId]);

    if (empty($retrieved)) return "Could not retrieve anti-colic chunk point";
    $payload = $retrieved[0]['payload'];

    if ($payload['source_type'] !== 'article') return "Expected source_type='article'";
    if ($payload['source_id'] !== 'anti-colic-feeding-guide') return "Expected source_id='anti-colic-feeding-guide'";
    if ($payload['category'] !== 'Newborn') return "Expected category='Newborn'";
    if ($payload['embedding_model'] !== 'gemini-embedding-2') return "Expected model='gemini-embedding-2'";
    if ($payload['embedding_dimension'] !== 768) return "Expected dimension=768";
    if ($payload['is_medically_verified'] !== false) return "is_medically_verified must be FALSE by default";

    return true;
});

// 4. Semantic Search with Filter
runIntTest("Step 4: Vector search with category filter returns strictly category-aligned knowledge", function() use ($embMock, $storeMock) {
    $queryVec = $embMock->embedQuery("anti colic airfree vent");
    $hits = $storeMock->search($queryVec, [
        'must' => [
            ['key' => 'category', 'match' => ['value' => 'Newborn']]
        ]
    ], 3);

    if (empty($hits)) return "Search returned zero hits";
    foreach ($hits as $h) {
        if ($h['payload']['category'] !== 'Newborn') {
            return "Cross-category leakage: expected 'Newborn', got: '{$h['payload']['category']}'";
        }
    }
    return true;
});

// 5. Fallback to Lexical Search
runIntTest("Step 5: When vector store is offline, lexical keyword search remains fully functional", function() use ($pdo) {
    $lexical = PhKnowledgeIngestionService::searchChunks($pdo, "colic", "Newborn", 3);
    if (empty($lexical)) return "Expected lexical search to find colic chunks";
    if (!str_contains(strtolower($lexical[0]['content']), 'colic')) {
        return "Lexical search result does not contain keyword 'colic'";
    }
    return true;
});

echo "\n======================================================================\n";
echo "   Test Results: {$passed} Passed, {$failed} Failed\n";
echo "======================================================================\n";

if ($failed > 0) exit(1);
echo "ALL STAGE 3 INTEGRATION TESTS PASSED CLEANLY.\n";
