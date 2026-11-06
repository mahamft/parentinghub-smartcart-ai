<?php
declare(strict_types=1);

/**
 * ParentingHub AI Embedding Provider Interface
 *
 * Provider-agnostic contract for text embeddings.
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

class PhProviderUnavailableException extends RuntimeException {}
class PhEmbeddingValidationException extends InvalidArgumentException {}

interface PhEmbeddingProviderInterface {

    /**
     * Get the active embedding model name (e.g. 'gemini-embedding-2').
     */
    public function getModel(): string;

    /**
     * Get configured output embedding dimension (e.g. 768).
     */
    public function getDimension(): int;

    /**
     * Check if provider credentials and network endpoint are configured.
     */
    public function isConfigured(): bool;

    /**
     * Generate embedding for a search query.
     * Uses task-oriented prompt prefix per documentation.
     *
     * @param string $query Query text
     * @return float[] Array of float values
     * @throws PhProviderUnavailableException
     * @throws PhEmbeddingValidationException
     */
    public function embedQuery(string $query): array;

    /**
     * Generate embedding for a knowledge chunk or document.
     *
     * @param string $document Document or chunk text
     * @return float[] Array of float values
     * @throws PhProviderUnavailableException
     * @throws PhEmbeddingValidationException
     */
    public function embedDocument(string $document): array;

    /**
     * Generate embeddings for multiple texts using the documented batch interface.
     *
     * @param string[] $texts Array of texts
     * @param string $taskType 'document' or 'query'
     * @return array<int, float[]> Array of float arrays
     * @throws PhProviderUnavailableException
     * @throws PhEmbeddingValidationException
     */
    public function embedBatch(array $texts, string $taskType = 'document'): array;
}

