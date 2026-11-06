<?php
declare(strict_types=1);

/**
 * ParentingHub AI: Dedicated CLI Knowledge Vector Synchronization
 *
 * Usage:
 *   php sync_knowledge_vectors.php                  # Dry-run inspection (default)
 *   php sync_knowledge_vectors.php --dry-run        # Explicit dry-run
 *   php sync_knowledge_vectors.php --execute        # Execute synchronization
 *   php sync_knowledge_vectors.php --execute --batch-size=25
 *   php sync_knowledge_vectors.php --execute --force
 *
 * Responsibilities:
 * - Offline by default (dry-run). Requires explicit --execute flag.
 * - Indexes all eligible published knowledge chunks (articles + products).
 * - Uses Gemini batch embedding requests (gemini-embedding-2, 768 dimensions).
 * - Uses Qdrant batch upserts with wait=true for verified write completion.
 * - Preserves deterministic RFC 4122 UUIDv5 point IDs derived from MySQL chunk IDs.
 * - Stores original chunk IDs, categories, and source metadata in payloads.
 * - Strictly skips unchanged vectors (version & content-hash validation).
 * - Tracks sync state in ph_ai_knowledge_chunk_vectors.
 * - Cleanly resumes interrupted batches without duplicate points.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "This script can only be executed from the command line.\n";
    exit(1);
}

define('PH_ENTRY', true);

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../embedding_provider_interface.php';
require_once __DIR__ . '/../gemini_embedding_adapter.php';
require_once __DIR__ . '/../vector_store_interface.php';
require_once __DIR__ . '/../qdrant_vector_store_adapter.php';
require_once __DIR__ . '/../knowledge_vector_sync_service.php';

// Parse CLI options
$options = getopt('', ['execute', 'dry-run', 'batch-size::', 'force', 'collection::', 'help']);

if (isset($options['help'])) {
    echo "ParentingHub AI: Knowledge Vector Synchronization CLI\n\n";
    echo "Options:\n";
    echo "  --execute           Perform live batch embedding and Qdrant upserts\n";
    echo "  --dry-run           Inspect chunk inventory without mutating index (default)\n";
    echo "  --batch-size=N      Batch size for embedding and upsert (default: 25, max: 100)\n";
    echo "  --force             Force re-embedding of all chunks even if hash matches\n";
    echo "  --collection=NAME   Override target Qdrant collection name\n";
    echo "  --help              Display this help message\n\n";
    exit(0);
}

$isExecute = isset($options['execute']);
$isForce = isset($options['force']);
$batchSize = isset($options['batch-size']) ? max(1, min(100, (int)$options['batch-size'])) : 25;
$collectionOverride = isset($options['collection']) ? trim((string)$options['collection']) : null;

echo "======================================================================\n";
echo "   ParentingHub AI: Knowledge Vector Synchronization CLI\n";
echo "======================================================================\n";
echo "Mode: " . ($isExecute ? "EXECUTE (Live Synchronization)" : "DRY-RUN (Inspection Only)") . "\n";
echo "Timestamp: " . date('Y-m-d H:i:s T') . "\n\n";

$pdo = ph_pdo();
PhKnowledgeVectorSyncService::ensureSchema($pdo);

// Initialize adapters
$embeddingAdapter = new PhGeminiEmbeddingAdapter();
$vectorStore = new PhQdrantVectorStoreAdapter(null, null, $collectionOverride, 5);

$isGeminiConfigured = $embeddingAdapter->isConfigured();
$isQdrantAvailable = $vectorStore->isAvailable();

$model = $embeddingAdapter->getModel();
$dimension = $embeddingAdapter->getDimension();
$collection = $vectorStore->getCollectionName();

echo "Infrastructure & Provider Status:\n";
echo sprintf("  %-25s: %s\n", "Embedding Model", "{$model} ({$dimension} dimensions)");
echo sprintf("  %-25s: %s\n", "Gemini API Configured", $isGeminiConfigured ? "YES (server-side key set)" : "NO (GEMINI_API_KEY unconfigured)");
echo sprintf("  %-25s: %s\n", "Qdrant Collection", $collection);
echo sprintf("  %-25s: %s\n", "Qdrant Service Status", $isQdrantAvailable ? "CONNECTED (online)" : "DISCONNECTED (offline/unreachable)");
echo "\n";

// Query Knowledge Inventory from MySQL
$totalDocs = (int)$pdo->query("SELECT COUNT(*) FROM ph_ai_knowledge_documents WHERE publication_status = 'published'")->fetchColumn();
$totalChunks = (int)$pdo->query("SELECT COUNT(*) FROM ph_ai_knowledge_chunks c 
    JOIN ph_ai_knowledge_documents d ON d.id = c.document_id 
    WHERE d.publication_status = 'published'")->fetchColumn();

// Check chunk categories breakdown
$catStmt = $pdo->query("SELECT category, COUNT(*) as cnt FROM ph_ai_knowledge_chunks GROUP BY category ORDER BY cnt DESC");
$categories = $catStmt->fetchAll();

// Determine pending vs synced chunks
if ($isForce) {
    $pendingCount = $totalChunks;
    $unchangedCount = 0;
} else {
    $pendingSql = "SELECT COUNT(*) FROM ph_ai_knowledge_chunks c
                   JOIN ph_ai_knowledge_documents d ON d.id = c.document_id
                   LEFT JOIN ph_ai_knowledge_chunk_vectors v ON v.chunk_id = c.chunk_id
                   WHERE d.publication_status = 'published'
                     AND (v.chunk_id IS NULL 
                          OR v.content_hash != c.content_hash
                          OR v.embedding_model != ?
                          OR v.embedding_dimension != ?)";
    $stmt = $pdo->prepare($pendingSql);
    $stmt->execute([$model, $dimension]);
    $pendingCount = (int)$stmt->fetchColumn();
    $unchangedCount = $totalChunks - $pendingCount;
}

echo "MySQL Knowledge Inventory:\n";
echo sprintf("  %-25s: %d\n", "Published Documents", $totalDocs);
echo sprintf("  %-25s: %d\n", "Total Eligible Chunks", $totalChunks);
echo sprintf("  %-25s: %d\n", "Pending / Outdated", $pendingCount);
echo sprintf("  %-25s: %d\n", "Already Synchronized", $unchangedCount);
echo "  Categories Breakdown:\n";
foreach ($categories as $cat) {
    echo sprintf("    - %-25s: %d chunks\n", $cat['category'], (int)$cat['cnt']);
}
echo "\n";

// If in DRY-RUN mode, present inspection report and exit cleanly
if (!$isExecute) {
    echo "======================================================================\n";
    echo "   DRY-RUN INSPECTION COMPLETED\n";
    echo "======================================================================\n";
    echo "No modifications were written to MySQL or Qdrant.\n";
    if ($pendingCount === 0) {
        echo "All {$totalChunks} knowledge chunks are already synchronized with {$model}.\n";
    } else {
        echo "{$pendingCount} knowledge chunks are queued for synchronization.\n";
        if (!$isGeminiConfigured) {
            echo "BLOCKER: Cannot synchronize. GEMINI_API_KEY is not configured in backend/.env.\n";
        }
        if (!$isQdrantAvailable) {
            echo "BLOCKER: Cannot synchronize. Qdrant vector store is offline at configured endpoint.\n";
        }
        echo "\nTo execute synchronization when infrastructure is active, run:\n";
        echo "  php backend/api/ai/cli/sync_knowledge_vectors.php --execute\n";
    }
    exit(0);
}

// In EXECUTE mode: enforce provider availability before mutating
if (!$isGeminiConfigured) {
    echo "ERROR: Synchronization aborted. GEMINI_API_KEY is not configured in backend/.env.\n";
    exit(1);
}

if (!$isQdrantAvailable) {
    echo "ERROR: Synchronization aborted. Qdrant vector store is unreachable at {$vectorStore->getCollectionName()}.\n";
    exit(1);
}

echo "Starting Live Knowledge Vector Synchronization...\n";
echo "Batch Size: {$batchSize}\n";
echo "Force Re-sync: " . ($isForce ? 'YES' : 'NO') . "\n\n";

$startTime = microtime(true);

$report = PhKnowledgeVectorSyncService::syncAll(
    $pdo,
    $embeddingAdapter,
    $vectorStore,
    $batchSize
);

$duration = round(microtime(true) - $startTime, 2);

// Confirm exact points in Qdrant
$exactQdrantCount = $vectorStore->count();

echo "======================================================================\n";
echo "   SYNCHRONIZATION RESULTS SUMMARY\n";
echo "======================================================================\n";
echo sprintf("  %-25s: %s\n", "Execution Status", strtoupper($report['status']));
echo sprintf("  %-25s: %s seconds\n", "Duration", $duration);
echo sprintf("  %-25s: %d\n", "Total Eligible Chunks", $report['totalChunksInSql']);
echo sprintf("  %-25s: %d\n", "Chunks Pending", $report['pendingChunks']);
echo sprintf("  %-25s: %d\n", "Vectors Generated & Synced", $report['synced']);
echo sprintf("  %-25s: %d\n", "Chunks Skipped (Unchanged)", $report['unchanged']);
echo sprintf("  %-25s: %d\n", "Orphan Vectors Cleaned", $report['orphansDeleted']);
echo sprintf("  %-25s: %d\n", "Confirmed Qdrant Count", $exactQdrantCount);

if (!empty($report['errors'])) {
    echo "\n  Synchronization Errors:\n";
    foreach ($report['errors'] as $err) {
        echo "    ! " . $err . "\n";
    }
    exit(1);
}

echo "\nSUCCESS: Knowledge vector synchronization completed cleanly.\n";
exit(0);
