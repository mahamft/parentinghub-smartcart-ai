<?php
declare(strict_types=1);

/**
 * TEST MOCK ONLY — PhMockEmbeddingAdapter
 *
 * Provides normalized, deterministic 768-dimensional float vectors strictly
 * for unit testing, CI pipelines, and offline verification.
 *
 * IMPORTANT: This is a test mock. It is NEVER used to claim real semantic
 * retrieval accuracy or production LLM capabilities.
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/embedding_provider_interface.php';

class PhMockEmbeddingAdapter implements PhEmbeddingProviderInterface {

    private string $model;
    private int $dimension;
    private bool $simulateFailure;

    public function __construct(
        string $model = 'test-mock-gemini-embedding-2',
        int $dimension = 768,
        bool $simulateFailure = false
    ) {
        $this->model = $model;
        $this->dimension = $dimension;
        $this->simulateFailure = $simulateFailure;
    }

    public function getModel(): string {
        return $this->model;
    }

    public function getDimension(): int {
        return $this->dimension;
    }

    public function isConfigured(): bool {
        return true;
    }

    public function setSimulateFailure(bool $fail): void {
        $this->simulateFailure = $fail;
    }

    public function embedQuery(string $query): array {
        if ($this->simulateFailure) {
            throw new PhProviderUnavailableException("Test simulated embedding provider failure.");
        }
        return $this->generateDeterministicVector($query, 'query');
    }

    public function embedDocument(string $document): array {
        if ($this->simulateFailure) {
            throw new PhProviderUnavailableException("Test simulated embedding provider failure.");
        }
        return $this->generateDeterministicVector($document, 'document');
    }

    public function embedBatch(array $texts, string $taskType = 'document'): array {
        if ($this->simulateFailure) {
            throw new PhProviderUnavailableException("Test simulated batch provider failure.");
        }
        $vectors = [];
        foreach ($texts as $t) {
            $vectors[] = $this->generateDeterministicVector((string)$t, $taskType);
        }
        return $vectors;
    }

    /**
     * Compute a deterministic L2-normalized float vector of length $this->dimension.
     */
    private function generateDeterministicVector(string $text, string $type): array {
        $clean = trim($text);
        if ($clean === '') {
            throw new PhEmbeddingValidationException("Cannot generate test embedding for empty text.");
        }

        $hash = hash('sha512', $type . ':' . $clean);
        $vec = [];
        $sumSq = 0.0;

        // Generate dimension floats using hash iterations
        for ($i = 0; $i < $this->dimension; $i++) {
            $seed = substr($hash, ($i % 32) * 2, 8);
            $val = ((hexdec($seed) % 2000) - 1000) / 1000.0;
            $vec[] = $val;
            $sumSq += $val * $val;
        }

        // L2 normalize
        $norm = sqrt($sumSq) ?: 1.0;
        foreach ($vec as &$v) {
            $v = round($v / $norm, 6);
        }
        unset($v);

        return $vec;
    }
}

