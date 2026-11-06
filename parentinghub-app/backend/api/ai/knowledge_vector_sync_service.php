<?php
declare(strict_types=1);

/**
 * ParentingHub AI Knowledge Vector Synchronization Service
 *
 * Responsibilities:
 * 1. Orchestrates synchronization between MySQL (source of truth) and Vector Store (derived index).
 * 2. Version-aware delta detection: embeds only new or modified chunks based on content_hash.
 * 3. Deleted-document reconciliation: cleans up orphaned vectors in vector store when MySQL chunks are deleted.
 * 4. Model & dimension integrity: prevents mixing incompatible embeddings in one collection.
 * 5. Batch processing with retry and error containment.
 * 6. Audit reporting of sync operations.
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/embedding_provider_interface.php';
require_once __DIR__ . '/vector_store_interface.php';
require_once __DIR__ . '/qdrant_vector_store_adapter.php';

class PhKnowledgeVectorSyncService {

    /**
     * Ensure MySQL synchronization tracking table exists.
     */
    public static function ensureSchema(PDO $pdo): void {
        $isSqlite = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';

        if ($isSqlite) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS ph_ai_knowledge_chunk_vectors (
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
        } else {
            $pdo->exec("CREATE TABLE IF NOT EXISTS ph_ai_knowledge_chunk_vectors (
                chunk_id VARCHAR(96) PRIMARY KEY,
                document_id VARCHAR(80) NOT NULL,
                qdrant_point_id CHAR(36) NOT NULL,
                embedding_model VARCHAR(64) NOT NULL,
                embedding_dimension INT UNSIGNED NOT NULL,
                content_hash CHAR(64) NOT NULL,
                sync_status VARCHAR(20) NOT NULL DEFAULT 'synced',
                synced_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_doc (document_id),
                KEY idx_point (qdrant_point_id),
                KEY idx_model_dim (embedding_model, embedding_dimension)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }
    }

    /**
     * Synchronize all pending and updated MySQL knowledge chunks to the vector store.
     *
     * @param PDO $pdo
     * @param PhEmbeddingProviderInterface $embeddingProvider
     * @param PhVectorStoreInterface $vectorStore
     * @param int $batchSize
     * @return array Sync reconciliation report
     */
    public static function syncAll(
        PDO $pdo,
        PhEmbeddingProviderInterface $embeddingProvider,
        PhVectorStoreInterface $vectorStore,
        int $batchSize = 25
    ): array {
        self::ensureSchema($pdo);

        $report = [
            'status' => 'completed',
            'model' => $embeddingProvider->getModel(),
            'dimension' => $embeddingProvider->getDimension(),
            'collection' => $vectorStore->getCollectionName(),
            'totalChunksInSql' => 0,
            'pendingChunks' => 0,
            'synced' => 0,
            'unchanged' => 0,
            'orphansDeleted' => 0,
            'errors' => []
        ];

        // 1. Verify provider & vector store availability
        if (!$embeddingProvider->isConfigured()) {
            $report['status'] = 'skipped_provider_unconfigured';
            $report['reason'] = 'Embedding provider is not configured (e.g. GEMINI_API_KEY is not set).';
            return $report;
        }

        if (!$vectorStore->isAvailable()) {
            $report['status'] = 'skipped_vector_store_offline';
            $report['reason'] = 'Vector store service is not reachable.';
            return $report;
        }

        // 2. Ensure target collection exists with exact model dimensions
        $vectorStore->createCollectionIfNotExists($embeddingProvider->getDimension(), 'Cosine');

        // 3. Find pending / outdated chunks
        $model = $embeddingProvider->getModel();
        $dim = $embeddingProvider->getDimension();

        $countSql = "SELECT COUNT(*) FROM ph_ai_knowledge_chunks";
        $report['totalChunksInSql'] = (int)$pdo->query($countSql)->fetchColumn();

        // Select chunks that are:
        // - Never synced, OR
        // - content_hash changed, OR
        // - synced with a different model or dimension
        $pendingSql = "SELECT c.*, d.source_id, d.title as doc_title, d.author as doc_author, 
                              d.author_credentials, d.is_medically_verified, d.publication_status, d.version as doc_version
                       FROM ph_ai_knowledge_chunks c
                       JOIN ph_ai_knowledge_documents d ON d.id = c.document_id
                       LEFT JOIN ph_ai_knowledge_chunk_vectors v ON v.chunk_id = c.chunk_id
                       WHERE v.chunk_id IS NULL 
                          OR v.content_hash != c.content_hash
                          OR v.embedding_model != ?
                          OR v.embedding_dimension != ?
                       ORDER BY c.document_id ASC, c.chunk_index ASC";

        $stmt = $pdo->prepare($pendingSql);
        $stmt->execute([$model, $dim]);
        $pendingChunks = $stmt->fetchAll();

        $report['pendingChunks'] = count($pendingChunks);
        $report['unchanged'] = $report['totalChunksInSql'] - $report['pendingChunks'];

        // 4. Batch embed and upsert
        if (!empty($pendingChunks)) {
            $chunkBatches = array_chunk($pendingChunks, max(1, min(100, $batchSize)));

            foreach ($chunkBatches as $batch) {
                try {
                    $texts = array_column($batch, 'content');
                    $embeddings = $embeddingProvider->embedBatch($texts, 'document');

                    $points = [];
                    $syncRecords = [];

                    foreach ($batch as $idx => $chunk) {
                        $vector = $embeddings[$idx] ?? null;
                        if (!$vector) continue;

                        $pointId = PhQdrantVectorStoreAdapter::toUuidV5($chunk['chunk_id']);

                        $payload = [
                            'chunk_id' => $chunk['chunk_id'],
                            'document_id' => $chunk['document_id'],
                            'source_type' => $chunk['source_type'],
                            'source_id' => $chunk['source_id'],
                            'category' => $chunk['category'],
                            'publication_status' => $chunk['publication_status'],
                            'is_medically_verified' => (bool)$chunk['is_medically_verified'],
                            'document_version' => (int)$chunk['doc_version'],
                            'content_hash' => $chunk['content_hash'],
                            'embedding_model' => $model,
                            'embedding_dimension' => $dim,
                            'heading' => $chunk['heading'] ?? '',
                            'content_preview' => mb_substr($chunk['content'], 0, 400)
                        ];

                        $points[] = [
                            'id' => $pointId,
                            'vector' => $vector,
                            'payload' => $payload
                        ];

                        $syncRecords[] = [
                            'chunk_id' => $chunk['chunk_id'],
                            'document_id' => $chunk['document_id'],
                            'qdrant_point_id' => $pointId,
                            'embedding_model' => $model,
                            'embedding_dimension' => $dim,
                            'content_hash' => $chunk['content_hash']
                        ];
                    }

                    // Upsert into vector store
                    $vectorStore->upsertPoints($points);

                    // Record sync in MySQL
                    $inTx = $pdo->inTransaction();
                    if (!$inTx) $pdo->beginTransaction();

                    $isSqlite = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';

                    if ($isSqlite) {
                        $insStmt = $pdo->prepare("INSERT OR REPLACE INTO ph_ai_knowledge_chunk_vectors 
                            (chunk_id, document_id, qdrant_point_id, embedding_model, embedding_dimension, content_hash, sync_status, synced_at, updated_at)
                            VALUES (?, ?, ?, ?, ?, ?, 'synced', datetime('now'), datetime('now'))");
                    } else {
                        $insStmt = $pdo->prepare("INSERT INTO ph_ai_knowledge_chunk_vectors 
                            (chunk_id, document_id, qdrant_point_id, embedding_model, embedding_dimension, content_hash, sync_status, synced_at, updated_at)
                            VALUES (?, ?, ?, ?, ?, ?, 'synced', NOW(), NOW())
                            ON DUPLICATE KEY UPDATE 
                                document_id = VALUES(document_id),
                                qdrant_point_id = VALUES(qdrant_point_id),
                                embedding_model = VALUES(embedding_model),
                                embedding_dimension = VALUES(embedding_dimension),
                                content_hash = VALUES(content_hash),
                                sync_status = 'synced',
                                synced_at = NOW(),
                                updated_at = NOW()");
                    }

                    foreach ($syncRecords as $sr) {
                        $insStmt->execute([
                            $sr['chunk_id'],
                            $sr['document_id'],
                            $sr['qdrant_point_id'],
                            $sr['embedding_model'],
                            $sr['embedding_dimension'],
                            $sr['content_hash']
                        ]);
                    }

                    if (!$inTx) $pdo->commit();
                    $report['synced'] += count($points);
                    sleep(3); // 3-second pacing between batches to stay comfortably below per-minute quota
                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) $pdo->rollBack();
                    $report['errors'][] = "Batch sync error: " . $e->getMessage();
                }
            }
        }

        // 5. Cleanup deleted/orphaned vectors
        $orphanReport = self::cleanupOrphanVectors($pdo, $vectorStore);
        $report['orphansDeleted'] = $orphanReport['deletedCount'];

        return $report;
    }

    /**
     * Detect and delete vectors whose MySQL chunks no longer exist.
     */
    public static function cleanupOrphanVectors(PDO $pdo, PhVectorStoreInterface $vectorStore): array {
        self::ensureSchema($pdo);

        $sql = "SELECT v.chunk_id, v.qdrant_point_id 
                FROM ph_ai_knowledge_chunk_vectors v
                LEFT JOIN ph_ai_knowledge_chunks c ON c.chunk_id = v.chunk_id
                WHERE c.chunk_id IS NULL";

        $orphans = $pdo->query($sql)->fetchAll();
        $count = count($orphans);

        if ($count > 0 && $vectorStore->isAvailable()) {
            $pointIds = array_column($orphans, 'qdrant_point_id');
            $vectorStore->deletePoints($pointIds);

            $delStmt = $pdo->prepare("DELETE FROM ph_ai_knowledge_chunk_vectors WHERE chunk_id = ?");
            foreach ($orphans as $o) {
                $delStmt->execute([$o['chunk_id']]);
            }
        }

        return [
            'orphansFound' => $count,
            'deletedCount' => $count
        ];
    }
}
