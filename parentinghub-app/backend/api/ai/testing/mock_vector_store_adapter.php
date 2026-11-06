<?php
declare(strict_types=1);

/**
 * TEST MOCK ONLY — PhMockVectorStoreAdapter
 *
 * In-memory / isolated test harness vector store simulating Qdrant REST operations
 * and exact cosine similarity search for testing synchronization, filtering,
 * point deletion, and lifecycle logic without requiring a live Qdrant daemon.
 *
 * IMPORTANT: This is a test mock. It is NEVER claimed as a production Qdrant daemon.
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/vector_store_interface.php';

class PhMockVectorStoreAdapter implements PhVectorStoreInterface {

    private string $collection;
    private int $dimension;
    private array $points = [];
    private bool $isAvailable = true;

    public function __construct(string $collection = 'test_parentinghub_mock', int $dimension = 768) {
        $this->collection = $collection;
        $this->dimension = $dimension;
    }

    public function getCollectionName(): string {
        return $this->collection;
    }

    public function isAvailable(): bool {
        return $this->isAvailable;
    }

    public function setAvailable(bool $avail): void {
        $this->isAvailable = $avail;
    }

    public function createCollectionIfNotExists(int $dimension, string $distance = 'Cosine'): bool {
        if (!$this->isAvailable) {
            throw new PhVectorStoreUnavailableException("Mock vector store is simulated offline.");
        }
        $this->dimension = $dimension;
        return true;
    }

    public function upsertPoints(array $points): bool {
        if (!$this->isAvailable) {
            throw new PhVectorStoreUnavailableException("Mock vector store is simulated offline.");
        }
        foreach ($points as $p) {
            $id = (string)($p['id'] ?? '');
            if ($id === '') continue;
            $this->points[$id] = [
                'id' => $id,
                'vector' => $p['vector'] ?? [],
                'payload' => $p['payload'] ?? []
            ];
        }
        return true;
    }

    public function search(array $vector, array $filter = [], int $limit = 5, ?float $scoreThreshold = null): array {
        if (!$this->isAvailable) {
            throw new PhVectorStoreUnavailableException("Mock vector store is simulated offline.");
        }

        $candidates = [];

        foreach ($this->points as $p) {
            // Apply simple filter match if provided
            if (!empty($filter['must'])) {
                $matchAll = true;
                foreach ($filter['must'] as $cond) {
                    $k = $cond['key'] ?? '';
                    $expected = $cond['match']['value'] ?? null;
                    if ($k !== '' && $expected !== null) {
                        if (($p['payload'][$k] ?? null) != $expected) {
                            $matchAll = false;
                            break;
                        }
                    }
                }
                if (!$matchAll) continue;
            }

            // Compute cosine similarity
            $sim = $this->cosineSimilarity($vector, $p['vector']);
            if ($scoreThreshold !== null && $sim < $scoreThreshold) {
                continue;
            }

            $candidates[] = [
                'id' => $p['id'],
                'score' => $sim,
                'payload' => $p['payload']
            ];
        }

        usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($candidates, 0, $limit);
    }

    public function retrieve(array $pointIds): array {
        $found = [];
        foreach ($pointIds as $id) {
            if (isset($this->points[$id])) {
                $found[] = $this->points[$id];
            }
        }
        return $found;
    }

    public function deletePoints(array $pointIds): bool {
        if (!$this->isAvailable) {
            throw new PhVectorStoreUnavailableException("Mock vector store is simulated offline.");
        }
        foreach ($pointIds as $id) {
            unset($this->points[$id]);
        }
        return true;
    }

    public function count(): int {
        return count($this->points);
    }

    private function cosineSimilarity(array $v1, array $v2): float {
        $dot = 0.0;
        $norm1 = 0.0;
        $norm2 = 0.0;
        $len = min(count($v1), count($v2));

        for ($i = 0; $i < $len; $i++) {
            $dot += $v1[$i] * $v2[$i];
            $norm1 += $v1[$i] * $v1[$i];
            $norm2 += $v2[$i] * $v2[$i];
        }

        if ($norm1 <= 0.0 || $norm2 <= 0.0) return 0.0;
        return round($dot / (sqrt($norm1) * sqrt($norm2)), 6);
    }
}

