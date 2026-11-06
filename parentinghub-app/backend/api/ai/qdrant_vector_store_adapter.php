<?php
declare(strict_types=1);

/**
 * ParentingHub AI Qdrant Vector Store Adapter
 *
 * Implements real Qdrant REST API client:
 * - Deterministic UUIDv5 point ID generation from MySQL chunk_id
 * - Collection verification and creation (Cosine distance)
 * - Batch points upsert with strict metadata preservation
 * - Semantic vector search with payload filters
 * - Point deletion & orphan cleanup
 * - Zero private customer data in vector payloads
 * - Handles service unavailability and connection timeouts cleanly
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/vector_store_interface.php';

class PhQdrantVectorStoreAdapter implements PhVectorStoreInterface {

    public const DEFAULT_HOST = 'http://127.0.0.1:6333';
    public const DEFAULT_COLLECTION = 'parentinghub_knowledge';
    public const DNS_NAMESPACE_UUID = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    private string $host;
    private ?string $apiKey;
    private string $collection;
    private int $timeoutSeconds;

    public function __construct(
        ?string $host = null,
        ?string $apiKey = null,
        ?string $collection = null,
        int $timeoutSeconds = 5
    ) {
        $envHost = trim((string)(ph_env('QDRANT_URL', '') ?: ph_env('QDRANT_HOST', self::DEFAULT_HOST)));
        $envKey = trim((string)ph_env('QDRANT_API_KEY', ''));
        $envCollection = trim((string)ph_env('QDRANT_COLLECTION', self::DEFAULT_COLLECTION));

        $this->host = rtrim($host !== null && trim($host) !== '' ? trim($host) : ($envHost ?: self::DEFAULT_HOST), '/');
        $this->apiKey = $apiKey !== null ? trim($apiKey) : ($envKey !== '' ? $envKey : null);
        $this->collection = $collection !== null && trim($collection) !== '' ? trim($collection) : ($envCollection ?: self::DEFAULT_COLLECTION);
        $this->timeoutSeconds = max(2, $timeoutSeconds);
    }

    public function getCollectionName(): string {
        return $this->collection;
    }

    /**
     * Check if Qdrant daemon is reachable.
     */
    public function isAvailable(): bool {
        try {
            $res = $this->callApi('GET', '/collections');
            return isset($res['result']['collections']) && is_array($res['result']['collections']);
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Create collection if it does not exist.
     */
    public function createCollectionIfNotExists(int $dimension, string $distance = 'Cosine'): bool {
        $endpoint = '/collections/' . rawurlencode($this->collection);

        try {
            $info = $this->callApi('GET', $endpoint);
            if (isset($info['result']['status'])) {
                // Verify existing collection dimension
                $existingDim = $info['result']['config']['params']['vectors']['size'] ?? null;
                if ($existingDim !== null && (int)$existingDim !== $dimension) {
                    throw new PhVectorStoreException("Collection '{$this->collection}' already exists with incompatible dimension {$existingDim} (requested {$dimension}).");
                }
                return true;
            }
        } catch (PhVectorStoreUnavailableException $e) {
            throw $e;
        } catch (Throwable) {
            // Collection does not exist, proceed to create
        }

        $payload = [
            'vectors' => [
                'size' => $dimension,
                'distance' => ucfirst($distance)
            ]
        ];

        $res = $this->callApi('PUT', $endpoint, $payload);
        return !empty($res['result']) || ($res['status'] ?? '') === 'ok';
    }

    /**
     * Upsert a batch of points into Qdrant.
     */
    public function upsertPoints(array $points): bool {
        if (empty($points)) return true;

        $endpoint = '/collections/' . rawurlencode($this->collection) . '/points?wait=true';
        $formattedPoints = [];

        foreach ($points as $p) {
            $rawId = (string)($p['id'] ?? '');
            $uuid = self::toUuidV5($rawId);
            $vector = $p['vector'] ?? [];
            $payload = $this->sanitizePayload($p['payload'] ?? [], $rawId);

            $formattedPoints[] = [
                'id' => $uuid,
                'vector' => $vector,
                'payload' => $payload
            ];
        }

        // Send in batches of 100
        $chunks = array_chunk($formattedPoints, 100);
        foreach ($chunks as $batch) {
            $res = $this->callApi('PUT', $endpoint, ['points' => $batch]);
            if (($res['status'] ?? '') !== 'ok' && empty($res['result'])) {
                throw new PhVectorStoreException("Failed to upsert points into collection '{$this->collection}'.");
            }
        }

        return true;
    }

    /**
     * Search nearest vectors with metadata filtering.
     */
    public function search(array $vector, array $filter = [], int $limit = 5, ?float $scoreThreshold = null): array {
        $endpoint = '/collections/' . rawurlencode($this->collection) . '/points/search';

        $body = [
            'vector' => $vector,
            'limit' => max(1, min(50, $limit)),
            'with_payload' => true
        ];

        if (!empty($filter)) {
            $body['filter'] = $filter;
        }

        if ($scoreThreshold !== null) {
            $body['score_threshold'] = (float)$scoreThreshold;
        }

        $res = $this->callApi('POST', $endpoint, $body);
        $hits = $res['result'] ?? [];
        if (!is_array($hits)) return [];

        $results = [];
        foreach ($hits as $hit) {
            $results[] = [
                'id' => (string)($hit['id'] ?? ''),
                'score' => (float)($hit['score'] ?? 0),
                'payload' => (array)($hit['payload'] ?? [])
            ];
        }

        return $results;
    }

    /**
     * Retrieve points by ID.
     */
    public function retrieve(array $pointIds): array {
        if (empty($pointIds)) return [];

        $endpoint = '/collections/' . rawurlencode($this->collection) . '/points';
        $uuids = array_map(fn($id) => self::toUuidV5((string)$id), $pointIds);

        $res = $this->callApi('POST', $endpoint, [
            'ids' => array_values($uuids),
            'with_payload' => true,
            'with_vector' => false
        ]);

        return $res['result'] ?? [];
    }

    /**
     * Delete points by ID.
     */
    public function deletePoints(array $pointIds): bool {
        if (empty($pointIds)) return true;

        $endpoint = '/collections/' . rawurlencode($this->collection) . '/points/delete?wait=true';
        $uuids = array_map(fn($id) => self::toUuidV5((string)$id), $pointIds);

        $res = $this->callApi('POST', $endpoint, [
            'points' => array_values($uuids)
        ]);

        return ($res['status'] ?? '') === 'ok' || !empty($res['result']);
    }

    /**
     * Get count of points in collection.
     */
    public function count(): int {
        $endpoint = '/collections/' . rawurlencode($this->collection) . '/points/count';
        try {
            $res = $this->callApi('POST', $endpoint, ['exact' => true]);
            return (int)($res['result']['count'] ?? 0);
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * Generate deterministic UUIDv5 identifier from arbitrary chunk ID.
     */
    public static function toUuidV5(string $name): string {
        // If already a valid UUID string, return as-is
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $name)) {
            return strtolower($name);
        }

        $nhex = str_replace(['-', '{', '}'], '', self::DNS_NAMESPACE_UUID);
        $nstr = '';
        for ($i = 0; $i < strlen($nhex); $i += 2) {
            $nstr .= chr((int)hexdec(substr($nhex, $i, 2)));
        }
        $hash = sha1($nstr . $name);

        return sprintf(
            '%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }

    /**
     * Sanitize metadata payload and strictly purge any customer/order private attributes.
     */
    private function sanitizePayload(array $payload, string $fallbackChunkId): array {
        $clean = $payload;

        // Ensure original chunk_id is always present
        if (empty($clean['chunk_id'])) {
            $clean['chunk_id'] = $fallbackChunkId;
        }

        // Forbid private customer data
        $forbiddenKeys = [
            'user_id', 'order_id', 'email', 'phone', 'address', 'password', 'token',
            'session_id', 'child_name', 'dob', 'credit_card', 'payment_id'
        ];
        foreach ($forbiddenKeys as $fk) {
            unset($clean[$fk]);
        }

        return $clean;
    }

    /**
     * Call Qdrant REST API.
     */
    private function callApi(string $method, string $path, ?array $body = null): array {
        $url = $this->host . $path;

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        if (!empty($this->apiKey)) {
            $headers[] = 'api-key: ' . $this->apiKey;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }

        $res = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($res === false) {
            throw new PhVectorStoreUnavailableException("Qdrant service unreachable at {$this->host}: {$err}");
        }

        if ($code < 200 || $code >= 300) {
            $cleanBody = mb_substr(trim((string)$res), 0, 400);
            throw new PhVectorStoreException("Qdrant HTTP {$code}: {$cleanBody}");
        }

        $decoded = json_decode((string)$res, true);
        return is_array($decoded) ? $decoded : [];
    }
}

