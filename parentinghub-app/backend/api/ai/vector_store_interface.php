<?php
declare(strict_types=1);

/**
 * ParentingHub AI Vector Store Interface
 *
 * Provider-agnostic contract for vector index operations.
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

class PhVectorStoreUnavailableException extends RuntimeException {}
class PhVectorStoreException extends RuntimeException {}

interface PhVectorStoreInterface {

    /**
     * Get the active vector collection name.
     */
    public function getCollectionName(): string;

    /**
     * Check if the vector store service is reachable.
     */
    public function isAvailable(): bool;

    /**
     * Create collection with dimensions and distance metric if it does not exist.
     *
     * @param int $dimension Vector dimension (e.g. 768)
     * @param string $distance 'Cosine', 'Euclid', or 'Dot'
     * @return bool True if created or already exists
     * @throws PhVectorStoreUnavailableException
     * @throws PhVectorStoreException
     */
    public function createCollectionIfNotExists(int $dimension, string $distance = 'Cosine'): bool;

    /**
     * Upsert a batch of points into the vector store.
     * Each point: ['id' => UUID/int, 'vector' => float[], 'payload' => array]
     *
     * @param array $points Array of points
     * @return bool True on success
     * @throws PhVectorStoreUnavailableException
     * @throws PhVectorStoreException
     */
    public function upsertPoints(array $points): bool;

    /**
     * Search nearest vectors with optional metadata filter.
     * Filter format: ['must' => [['key' => 'category', 'match' => ['value' => 'Newborn']]]]
     *
     * @param float[] $vector Query vector
     * @param array $filter Metadata filter conditions
     * @param int $limit Number of top results
     * @param float|null $scoreThreshold Minimum similarity score
     * @return array Array of results: [['id' => string, 'score' => float, 'payload' => array]]
     * @throws PhVectorStoreUnavailableException
     * @throws PhVectorStoreException
     */
    public function search(array $vector, array $filter = [], int $limit = 5, ?float $scoreThreshold = null): array;

    /**
     * Retrieve points by IDs with payloads and vectors.
     *
     * @param string[] $pointIds Array of point IDs
     * @return array Array of retrieved points
     */
    public function retrieve(array $pointIds): array;

    /**
     * Delete points by IDs.
     *
     * @param string[] $pointIds Array of point IDs
     * @return bool True on success
     */
    public function deletePoints(array $pointIds): bool;

    /**
     * Get point count in the collection.
     */
    public function count(): int;
}

