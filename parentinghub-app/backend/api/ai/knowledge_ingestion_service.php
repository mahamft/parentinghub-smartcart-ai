<?php
declare(strict_types=1);

/**
 * ParentingHub AI Knowledge Foundation & Ingestion Engine
 *
 * Responsibilities:
 * 1. Normalized knowledge document and chunk management.
 * 2. Ingestion of clinical parenting articles (body_json parsing, section context).
 * 3. Ingestion of verified product catalog specifications (price/stock excluded).
 * 4. SHA-256 content hashing & versioning.
 * 5. Idempotent re-indexing, duplicate detection, and deletion/de-indexing.
 * 6. Background ingestion job audit logging.
 * 7. Strict privacy protection: Whitelist source types only; private user/order data is barred.
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

class PhKnowledgeIngestionService {

    public const SOURCE_ARTICLE = 'article';
    public const SOURCE_PRODUCT = 'product';

    public const ALLOWED_SOURCES = [
        self::SOURCE_ARTICLE,
        self::SOURCE_PRODUCT
    ];

    private static function isSqlite(PDO $pdo): bool {
        return strtolower((string)$pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) === 'sqlite';
    }

    private static function nowExpr(PDO $pdo): string {
        return self::isSqlite($pdo) ? "datetime('now')" : "NOW()";
    }

    /**
     * Start a new ingestion tracking job.
     */
    public static function startJob(PDO $pdo, string $jobType, int $totalSources = 0): string {
        $jobId = 'job_ingest_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4));
        $now = self::nowExpr($pdo);
        $stmt = $pdo->prepare("INSERT INTO ph_ai_knowledge_ingestion_jobs 
            (job_id, job_type, status, total_sources, started_at)
            VALUES (?, ?, 'running', ?, {$now})");
        $stmt->execute([$jobId, $jobType, $totalSources]);
        return $jobId;
    }

    /**
     * Complete an ingestion tracking job.
     */
    public static function completeJob(PDO $pdo, string $jobId, array $stats, ?array $errors = null): void {
        $now = self::nowExpr($pdo);
        $stmt = $pdo->prepare("UPDATE ph_ai_knowledge_ingestion_jobs SET 
            status = 'completed',
            processed_sources = ?,
            documents_created = ?,
            documents_updated = ?,
            documents_unchanged = ?,
            documents_deindexed = ?,
            chunks_created = ?,
            errors_json = ?,
            completed_at = {$now}
            WHERE job_id = ?");
        
        $stmt->execute([
            (int)($stats['processed'] ?? 0),
            (int)($stats['created'] ?? 0),
            (int)($stats['updated'] ?? 0),
            (int)($stats['unchanged'] ?? 0),
            (int)($stats['deindexed'] ?? 0),
            (int)($stats['chunks'] ?? 0),
            !empty($errors) ? json_encode(array_values($errors), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
            $jobId
        ]);
    }

    /**
     * Mark an ingestion job as failed.
     */
    public static function failJob(PDO $pdo, string $jobId, Throwable $e): void {
        $now = self::nowExpr($pdo);
        $stmt = $pdo->prepare("UPDATE ph_ai_knowledge_ingestion_jobs SET 
            status = 'failed',
            errors_json = ?,
            completed_at = {$now}
            WHERE job_id = ?");
        $stmt->execute([
            json_encode(['error' => $e->getMessage(), 'trace' => $e->getFile() . ':' . $e->getLine()]),
            $jobId
        ]);
    }

    /**
     * Ingest all verified content sources (articles + products) in an idempotent batch.
     */
    public static function ingestAll(PDO $pdo): array {
        // Count available sources
        $articleCount = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        $productCount = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $totalSources = $articleCount + $productCount;

        $jobId = self::startJob($pdo, 'full_catalog_and_articles', $totalSources);

        $stats = [
            'jobId' => $jobId,
            'total' => $totalSources,
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'deindexed' => 0,
            'chunks' => 0,
            'articles' => 0,
            'products' => 0
        ];
        $errors = [];

        try {
            // 1. Ingest Articles
            $artStats = self::ingestArticles($pdo);
            $stats['articles'] = $artStats['processed'];
            $stats['processed'] += $artStats['processed'];
            $stats['created'] += $artStats['created'];
            $stats['updated'] += $artStats['updated'];
            $stats['unchanged'] += $artStats['unchanged'];
            $stats['chunks'] += $artStats['chunks'];

            // 2. Ingest Products
            $prodStats = self::ingestProducts($pdo);
            $stats['products'] = $prodStats['processed'];
            $stats['processed'] += $prodStats['processed'];
            $stats['created'] += $prodStats['created'];
            $stats['updated'] += $prodStats['updated'];
            $stats['unchanged'] += $prodStats['unchanged'];
            $stats['chunks'] += $prodStats['chunks'];

            self::completeJob($pdo, $jobId, $stats, $errors);
            $stats['status'] = 'completed';
        } catch (Throwable $e) {
            self::failJob($pdo, $jobId, $e);
            $stats['status'] = 'failed';
            $stats['error'] = $e->getMessage();
            throw $e;
        }

        return $stats;
    }

    /**
     * Ingest all parenting and clinical articles.
     */
    public static function ingestArticles(PDO $pdo): array {
        $articles = $pdo->query("SELECT id, title, excerpt, cover, author, date, read_time, category, body_json, created_at FROM articles")->fetchAll();
        $stats = ['processed' => 0, 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'chunks' => 0];

        foreach ($articles as $art) {
            $res = self::ingestSingleArticle($pdo, $art);
            $stats['processed']++;
            if ($res['status'] === 'created') $stats['created']++;
            elseif ($res['status'] === 'updated') $stats['updated']++;
            elseif ($res['status'] === 'unchanged') $stats['unchanged']++;
            $stats['chunks'] += $res['chunks'];
        }

        return $stats;
    }

    /**
     * Ingest a single article into document and chunks with content hashing & versioning.
     */
    public static function ingestSingleArticle(PDO $pdo, array $art): array {
        $sourceType = self::SOURCE_ARTICLE;
        $sourceId = trim((string)$art['id']);
        if ($sourceId === '') {
            throw new InvalidArgumentException("Article source ID cannot be empty.");
        }

        $docId = 'doc:' . $sourceType . ':' . $sourceId;
        $title = trim((string)$art['title']);
        $slug = $sourceId;
        $category = trim((string)($art['category'] ?? 'General')) ?: 'General';
        $authorRaw = trim((string)($art['author'] ?? 'ParentingHub Care Editorial'));

        // Parse recorded author credentials metadata
        $authorName = $authorRaw;
        $authorCredentials = null;
        if (str_contains($authorRaw, ',')) {
            $parts = explode(',', $authorRaw, 2);
            $authorName = trim($parts[0]);
            $authorCredentials = trim($parts[1]);
        }

        // Note: is_medically_verified is explicitly FALSE (0) until independently verified
        $isMedicallyVerified = 0;

        // Parse body JSON paragraphs
        $bodyRaw = $art['body_json'] ?? '[]';
        $paragraphs = is_string($bodyRaw) ? json_decode($bodyRaw, true) : $bodyRaw;
        if (!is_array($paragraphs)) $paragraphs = [];

        // Build canonical normalized text representation for SHA-256 hashing
        $normalizedPieces = [
            "TITLE: " . $title,
            "CATEGORY: " . $category,
            "AUTHOR: " . $authorName,
            "CREDENTIALS: " . ($authorCredentials ?? 'None recorded'),
            "EXCERPT: " . trim((string)($art['excerpt'] ?? '')),
        ];
        foreach ($paragraphs as $idx => $p) {
            $normalizedPieces[] = "P" . ($idx + 1) . ": " . trim((string)$p);
        }
        $fullCanonicalText = implode("\n", $normalizedPieces);
        $contentHash = hash('sha256', $fullCanonicalText);

        // Check existing document state
        $existing = self::getDocument($pdo, $docId);
        $status = 'created';
        $version = 1;

        if ($existing) {
            if ($existing['content_hash'] === $contentHash) {
                // Idempotent: Content identical, skip re-chunking
                $existingChunks = (int)$pdo->query("SELECT COUNT(*) FROM ph_ai_knowledge_chunks WHERE document_id = " . $pdo->quote($docId))->fetchColumn();
                return [
                    'documentId' => $docId,
                    'status' => 'unchanged',
                    'version' => (int)$existing['version'],
                    'chunks' => $existingChunks,
                    'contentHash' => $contentHash
                ];
            }
            // Modified: Increment version
            $status = 'updated';
            $version = (int)$existing['version'] + 1;
        }

        // Generate knowledge chunks
        $chunksData = [];
        
        // Chunk 0: Article Header & Overview
        $headerContent = "[ParentingHub Guide: {$title}]\n"
            . "Category: {$category} | Author: {$authorName}" . ($authorCredentials ? " ({$authorCredentials})" : "") . "\n"
            . "Summary: " . trim((string)($art['excerpt'] ?? ''));
        
        $chunksData[] = [
            'index' => 0,
            'heading' => 'Article Overview',
            'content' => $headerContent,
            'tokens' => max(1, (int)ceil(mb_strlen($headerContent) / 4)),
            'hash' => hash('sha256', $headerContent),
            'meta' => [
                'type' => 'overview',
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'author' => $authorName,
                'author_credentials' => $authorCredentials,
                'category' => $category
            ]
        ];

        // Chunks 1..N: Individual paragraphs with contextual document attribution
        foreach ($paragraphs as $idx => $p) {
            $pText = trim((string)$p);
            if ($pText === '') continue;

            $chunkContent = "[Topic: {$title} | Category: {$category} | Source: Article {$sourceId}]\n"
                . $pText;

            $chunksData[] = [
                'index' => $idx + 1,
                'heading' => 'Section ' . ($idx + 1),
                'content' => $chunkContent,
                'tokens' => max(1, (int)ceil(mb_strlen($chunkContent) / 4)),
                'hash' => hash('sha256', $chunkContent),
                'meta' => [
                    'type' => 'paragraph',
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,
                    'paragraph_index' => $idx + 1,
                    'author' => $authorName,
                    'author_credentials' => $authorCredentials,
                    'category' => $category
                ]
            ];
        }

        // Save Document and Chunks inside Transaction
        $inTx = $pdo->inTransaction();
        if (!$inTx) $pdo->beginTransaction();

        try {
            // Upsert document record
            if (self::isSqlite($pdo)) {
                $docStmt = $pdo->prepare("INSERT OR REPLACE INTO ph_ai_knowledge_documents 
                    (id, source_type, source_id, title, slug, category, author, author_credentials, is_medically_verified, publication_status, content_hash, version, metadata_json, indexed_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', ?, ?, ?, datetime('now'), datetime('now'))");
            } else {
                $docStmt = $pdo->prepare("INSERT INTO ph_ai_knowledge_documents 
                    (id, source_type, source_id, title, slug, category, author, author_credentials, is_medically_verified, publication_status, content_hash, version, metadata_json, indexed_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', ?, ?, ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE 
                        title = VALUES(title),
                        category = VALUES(category),
                        author = VALUES(author),
                        author_credentials = VALUES(author_credentials),
                        content_hash = VALUES(content_hash),
                        version = VALUES(version),
                        metadata_json = VALUES(metadata_json),
                        indexed_at = NOW(),
                        updated_at = NOW()");
            }

            $metaJson = json_encode([
                'excerpt' => $art['excerpt'] ?? '',
                'read_time' => $art['read_time'] ?? '',
                'cover' => $art['cover'] ?? '',
                'chunk_count' => count($chunksData)
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $docStmt->execute([
                $docId,
                $sourceType,
                $sourceId,
                $title,
                $slug,
                $category,
                $authorName,
                $authorCredentials,
                $isMedicallyVerified,
                $contentHash,
                $version,
                $metaJson
            ]);

            // Reconcile chunks: Delete previous chunks before re-inserting
            $delStmt = $pdo->prepare("DELETE FROM ph_ai_knowledge_chunks WHERE document_id = ?");
            $delStmt->execute([$docId]);

            // Insert new chunks
            $chkStmt = $pdo->prepare("INSERT INTO ph_ai_knowledge_chunks 
                (chunk_id, document_id, chunk_index, heading, content, token_count, content_hash, source_type, category, metadata_json)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            foreach ($chunksData as $cd) {
                $chunkId = 'chk:' . $sourceType . ':' . $sourceId . ':' . $cd['index'];
                $chkStmt->execute([
                    $chunkId,
                    $docId,
                    $cd['index'],
                    $cd['heading'],
                    $cd['content'],
                    $cd['tokens'],
                    $cd['hash'],
                    $sourceType,
                    $category,
                    json_encode($cd['meta'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                ]);
            }

            if (!$inTx) $pdo->commit();
        } catch (Throwable $e) {
            if (!$inTx && $pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }

        return [
            'documentId' => $docId,
            'status' => $status,
            'version' => $version,
            'chunks' => count($chunksData),
            'contentHash' => $contentHash
        ];
    }

    /**
     * Ingest all 287 catalog products into knowledge foundation.
     */
    public static function ingestProducts(PDO $pdo): array {
        $products = $pdo->query("SELECT id, sku, name, price, category, brand, description, tags, in_stock, rating, reviews FROM products")->fetchAll();
        $stats = ['processed' => 0, 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'chunks' => 0];

        foreach ($products as $p) {
            $res = self::ingestSingleProduct($pdo, $p);
            $stats['processed']++;
            if ($res['status'] === 'created') $stats['created']++;
            elseif ($res['status'] === 'updated') $stats['updated']++;
            elseif ($res['status'] === 'unchanged') $stats['unchanged']++;
            $stats['chunks'] += $res['chunks'];
        }

        return $stats;
    }

    /**
     * Ingest a single product specification.
     * Crucial: NEVER indexes volatile prices or stock status as authoritative facts in the knowledge chunk.
     */
    public static function ingestSingleProduct(PDO $pdo, array $p): array {
        $sourceType = self::SOURCE_PRODUCT;
        $sourceId = trim((string)$p['id']);
        if ($sourceId === '') {
            throw new InvalidArgumentException("Product source ID cannot be empty.");
        }

        $docId = 'doc:' . $sourceType . ':' . $sourceId;
        $name = trim((string)$p['name']);
        $brand = trim((string)($p['brand'] ?? 'ParentingHub')) ?: 'ParentingHub';
        $category = trim((string)($p['category'] ?? 'General')) ?: 'General';
        $sku = trim((string)($p['sku'] ?? ''));
        $description = trim((string)($p['description'] ?? ''));

        // Parse tags
        $tagsRaw = $p['tags'] ?? '[]';
        $tags = is_string($tagsRaw) ? json_decode($tagsRaw, true) : $tagsRaw;
        $tagsList = is_array($tags) ? implode(', ', $tags) : '';

        // Canonical product knowledge text (strictly excludes volatile prices & real-time stock numbers)
        $canonicalText = "PRODUCT SPECIFICATION: {$name}\n"
            . "Brand: {$brand}\n"
            . "Category: {$category}\n"
            . "SKU: {$sku}\n"
            . "Product Details: {$description}\n"
            . ($tagsList !== '' ? "Features & Tags: {$tagsList}\n" : "")
            . "Notice: Current pricing, promotional discounts, and live inventory are verified dynamically from MySQL during checkout.";

        $contentHash = hash('sha256', $canonicalText);

        $existing = self::getDocument($pdo, $docId);
        $status = 'created';
        $version = 1;

        if ($existing) {
            if ($existing['content_hash'] === $contentHash) {
                return [
                    'documentId' => $docId,
                    'status' => 'unchanged',
                    'version' => (int)$existing['version'],
                    'chunks' => 1,
                    'contentHash' => $contentHash
                ];
            }
            $status = 'updated';
            $version = (int)$existing['version'] + 1;
        }

        $tokenCount = max(1, (int)ceil(mb_strlen($canonicalText) / 4));
        $chunkId = 'chk:' . $sourceType . ':' . $sourceId . ':0';

        $meta = [
            'type' => 'product_spec',
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'brand' => $brand,
            'category' => $category,
            'sku' => $sku
        ];

        $inTx = $pdo->inTransaction();
        if (!$inTx) $pdo->beginTransaction();

        try {
            if (self::isSqlite($pdo)) {
                $docStmt = $pdo->prepare("INSERT OR REPLACE INTO ph_ai_knowledge_documents 
                    (id, source_type, source_id, title, slug, category, author, author_credentials, is_medically_verified, publication_status, content_hash, version, metadata_json, indexed_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NULL, 0, 'published', ?, ?, ?, datetime('now'), datetime('now'))");
            } else {
                $docStmt = $pdo->prepare("INSERT INTO ph_ai_knowledge_documents 
                    (id, source_type, source_id, title, slug, category, author, author_credentials, is_medically_verified, publication_status, content_hash, version, metadata_json, indexed_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NULL, 0, 'published', ?, ?, ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE 
                        title = VALUES(title),
                        category = VALUES(category),
                        author = VALUES(author),
                        content_hash = VALUES(content_hash),
                        version = VALUES(version),
                        metadata_json = VALUES(metadata_json),
                        indexed_at = NOW(),
                        updated_at = NOW()");
            }

            $metaJson = json_encode($meta, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $docStmt->execute([
                $docId,
                $sourceType,
                $sourceId,
                $name,
                $sourceId,
                $category,
                $brand,
                $contentHash,
                $version,
                $metaJson
            ]);

            // Reconcile chunk
            $delStmt = $pdo->prepare("DELETE FROM ph_ai_knowledge_chunks WHERE document_id = ?");
            $delStmt->execute([$docId]);

            $chkStmt = $pdo->prepare("INSERT INTO ph_ai_knowledge_chunks 
                (chunk_id, document_id, chunk_index, heading, content, token_count, content_hash, source_type, category, metadata_json)
                VALUES (?, ?, 0, 'Product Specifications', ?, ?, ?, ?, ?, ?)");

            $chkStmt->execute([
                $chunkId,
                $docId,
                $canonicalText,
                $tokenCount,
                $contentHash,
                $sourceType,
                $category,
                $metaJson
            ]);

            if (!$inTx) $pdo->commit();
        } catch (Throwable $e) {
            if (!$inTx && $pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }

        return [
            'documentId' => $docId,
            'status' => $status,
            'version' => $version,
            'chunks' => 1,
            'contentHash' => $contentHash
        ];
    }

    /**
     * Reconcile deletion: de-index document and associated chunks from the knowledge foundation.
     */
    public static function deindexSource(PDO $pdo, string $sourceType, string $sourceId): bool {
        self::assertAllowedSourceType($sourceType);

        $docId = 'doc:' . $sourceType . ':' . $sourceId;
        $inTx = $pdo->inTransaction();
        if (!$inTx) $pdo->beginTransaction();

        try {
            // Delete chunks first (or cascade via FK)
            $pdo->prepare("DELETE FROM ph_ai_knowledge_chunks WHERE document_id = ?")->execute([$docId]);
            $stmt = $pdo->prepare("DELETE FROM ph_ai_knowledge_documents WHERE id = ?");
            $stmt->execute([$docId]);
            $affected = $stmt->rowCount();

            if (!$inTx) $pdo->commit();
            return $affected > 0;
        } catch (Throwable $e) {
            if (!$inTx && $pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Retrieve document record by ID.
     */
    public static function getDocument(PDO $pdo, string $docId): ?array {
        $stmt = $pdo->prepare("SELECT * FROM ph_ai_knowledge_documents WHERE id = ?");
        $stmt->execute([$docId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Retrieve all chunks belonging to a document.
     */
    public static function getChunks(PDO $pdo, string $docId): array {
        $stmt = $pdo->prepare("SELECT * FROM ph_ai_knowledge_chunks WHERE document_id = ? ORDER BY chunk_index ASC");
        $stmt->execute([$docId]);
        return $stmt->fetchAll();
    }

    /**
     * Lexical keyword search across knowledge chunks with relevance scoring and optional category filter.
     */
    public static function searchChunks(PDO $pdo, string $query, ?string $category = null, int $limit = 5): array {
        $stopWords = [
            'the', 'and', 'for', 'are', 'with', 'that', 'this', 'from', 'baby', 'bachay', 'bacha',
            'liye', 'karein', 'karna', 'karni', 'hai', 'hain', 'kaise', 'kya', 'meri', 'mera', 'mein',
            'daily', 'routine', 'tips', 'about', 'some', 'give', 'tell', 'show'
        ];

        $clean = trim(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $query));
        $allWords = array_values(array_filter(explode(' ', strtolower($clean)), fn($w) => strlen($w) >= 3));

        // Distinguish topic keywords from generic stopwords
        $topicWords = array_values(array_filter($allWords, fn($w) => !in_array($w, $stopWords, true)));
        $searchWords = !empty($topicWords) ? $topicWords : $allWords;

        if (empty($searchWords)) return [];

        $scoreParts = [];
        $whereParts = [];
        $scoreParams = [];
        $whereParams = [];

        foreach ($searchWords as $w) {
            $scoreParts[] = "(CASE WHEN LOWER(c.content) LIKE ? THEN 2 ELSE 0 END + CASE WHEN LOWER(c.heading) LIKE ? THEN 3 ELSE 0 END + CASE WHEN LOWER(d.title) LIKE ? THEN 4 ELSE 0 END)";
            $scoreParams[] = "%{$w}%";
            $scoreParams[] = "%{$w}%";
            $scoreParams[] = "%{$w}%";

            $whereParts[] = "(LOWER(c.content) LIKE ? OR LOWER(c.heading) LIKE ? OR LOWER(d.title) LIKE ?)";
            $whereParams[] = "%{$w}%";
            $whereParams[] = "%{$w}%";
            $whereParams[] = "%{$w}%";
        }

        $scoreSql = implode(' + ', $scoreParts);
        $whereSql = '(' . implode(' OR ', $whereParts) . ')';

        $conds = [$whereSql];
        $fullWhereParams = $whereParams;

        if (!empty($category)) {
            $conds[] = "c.category = ?";
            $fullWhereParams[] = $category;
        }

        $whereFinal = implode(' AND ', $conds);
        $minScore = count($searchWords) > 1 ? 4 : 2;

        $sql = "SELECT * FROM (
                    SELECT c.*, d.title as doc_title, d.author as doc_author, d.author_credentials as doc_credentials, d.source_id as doc_source_id,
                           ({$scoreSql}) as relevance_score
                    FROM ph_ai_knowledge_chunks c
                    JOIN ph_ai_knowledge_documents d ON d.id = c.document_id
                    WHERE {$whereFinal}
                ) sub
                WHERE sub.relevance_score >= {$minScore}
                ORDER BY sub.relevance_score DESC, sub.chunk_index ASC
                LIMIT " . (int)max(1, min(20, $limit));

        $execParams = array_merge($scoreParams, $fullWhereParams);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($execParams);
        return $stmt->fetchAll();
    }

    /**
     * Security barrier: strictly block private customer data from knowledge ingestion.
     */
    private static function assertAllowedSourceType(string $sourceType): void {
        if (!in_array($sourceType, self::ALLOWED_SOURCES, true)) {
            throw new InvalidArgumentException("Security violation: Ingestion of source type '{$sourceType}' is strictly prohibited. Only public articles and catalog products are allowed.");
        }
    }
}
