<?php
declare(strict_types=1);

/**
 * ParentingHub AI Gemini Embedding Adapter
 *
 * Implements real Google Generative AI REST embedding client:
 * - Model: gemini-embedding-2
 * - Official REST endpoints: embedContent & batchEmbedContents
 * - Configurable output dimension (initial: 768)
 * - Task-oriented prompt instructions in text (no unsupported task_type parameter)
 * - Zero API key leakage in logs or exceptions
 * - Validates response structure, dimensions, and numerical validity
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/embedding_provider_interface.php';

class PhGeminiEmbeddingAdapter implements PhEmbeddingProviderInterface {

    public const DEFAULT_MODEL = 'gemini-embedding-2';
    public const DEFAULT_DIMENSION = 768;

    private string $apiKey;
    private string $model;
    private int $dimension;
    private int $timeoutSeconds;

    public function __construct(
        ?string $apiKey = null,
        ?string $model = null,
        int $dimension = self::DEFAULT_DIMENSION,
        int $timeoutSeconds = 12
    ) {
        $envKey = trim((string)ph_env('GEMINI_API_KEY', ''));
        $this->apiKey = $apiKey !== null ? trim($apiKey) : $envKey;
        $this->model = $model !== null && trim($model) !== '' ? trim($model) : self::DEFAULT_MODEL;
        $this->dimension = $dimension > 0 ? $dimension : self::DEFAULT_DIMENSION;
        $this->timeoutSeconds = max(3, $timeoutSeconds);
    }

    public function getModel(): string {
        return $this->model;
    }

    public function getDimension(): int {
        return $this->dimension;
    }

    public function isConfigured(): bool {
        return $this->apiKey !== '' && !str_starts_with($this->apiKey, '__');
    }

    /**
     * Embed query text with task-oriented instruction prefix.
     */
    public function embedQuery(string $query): array {
        $clean = trim($query);
        if ($clean === '') {
            throw new PhEmbeddingValidationException('Query text cannot be empty for embedding generation.');
        }

        // Task-oriented prompt instruction per current documentation
        $taskPrefixed = "task: Given a parenting question, retrieve relevant knowledge chunks: " . $clean;
        return $this->executeEmbedContent($taskPrefixed);
    }

    /**
     * Embed document or knowledge chunk text with task-oriented instruction prefix.
     */
    public function embedDocument(string $document): array {
        $clean = trim($document);
        if ($clean === '') {
            throw new PhEmbeddingValidationException('Document text cannot be empty for embedding generation.');
        }

        $taskPrefixed = "task: Represent this parenting article or product knowledge chunk for retrieval: " . $clean;
        return $this->executeEmbedContent($taskPrefixed);
    }

    /**
     * Embed a batch of texts using batchEmbedContents endpoint.
     */
    public function embedBatch(array $texts, string $taskType = 'document'): array {
        if (empty($texts)) {
            return [];
        }

        $this->assertConfigured();

        $requests = [];
        $prefix = $taskType === 'query'
            ? "task: Given a parenting question, retrieve relevant knowledge chunks: "
            : "task: Represent this parenting article or product knowledge chunk for retrieval: ";

        foreach ($texts as $idx => $t) {
            $clean = trim((string)$t);
            if ($clean === '') {
                throw new PhEmbeddingValidationException("Batch item at index {$idx} cannot be empty.");
            }
            $requests[] = [
                'model' => 'models/' . $this->model,
                'content' => [
                    'parts' => [
                        ['text' => $prefix . $clean]
                    ]
                ],
                'outputDimensionality' => $this->dimension
            ];
        }

        // Chunk requests into max 100 per batch per Gemini limits
        $batches = array_chunk($requests, 100);
        $allEmbeddings = [];

        foreach ($batches as $batchRequests) {
            $payload = json_encode(['requests' => $batchRequests], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:batchEmbedContents";

            $response = $this->callApi($endpoint, $payload);
            $data = json_decode($response, true);

            if (!is_array($data) || empty($data['embeddings']) || !is_array($data['embeddings'])) {
                throw new PhEmbeddingValidationException("Gemini batch embedding response did not contain expected 'embeddings' array.");
            }

            foreach ($data['embeddings'] as $emb) {
                $values = $emb['values'] ?? [];
                $this->validateVector($values);
                $allEmbeddings[] = $values;
            }
        }

        return $allEmbeddings;
    }

    /**
     * Single content embedding call.
     */
    private function executeEmbedContent(string $text): array {
        $this->assertConfigured();

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:embedContent";
        $payload = json_encode([
            'model' => 'models/' . $this->model,
            'content' => [
                'parts' => [
                    ['text' => $text]
                ]
            ],
            'outputDimensionality' => $this->dimension
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $response = $this->callApi($endpoint, $payload);
        $data = json_decode($response, true);

        if (!is_array($data) || empty($data['embedding']['values']) || !is_array($data['embedding']['values'])) {
            throw new PhEmbeddingValidationException("Gemini embedding response did not contain expected 'embedding.values' structure.");
        }

        $vector = $data['embedding']['values'];
        $this->validateVector($vector);
        return $vector;
    }

    /**
     * Validate vector dimensions and finite numerical float values.
     */
    public function validateVector(array $values): void {
        $count = count($values);
        if ($count !== $this->dimension) {
            throw new PhEmbeddingValidationException("Vector dimension mismatch: expected {$this->dimension}, got {$count}.");
        }

        foreach ($values as $idx => $val) {
            if (!is_numeric($val) || !is_finite((float)$val)) {
                throw new PhEmbeddingValidationException("Vector element at index {$idx} is not a valid finite float.");
            }
        }
    }

    /**
     * Perform HTTP POST to Gemini REST API with transient retry handling and zero credential leakage.
     */
    private function callApi(string $url, string $payload): string {
        $safeUrl = $url . '?key=' . rawurlencode($this->apiKey);
        $maxAttempts = 5;
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxAttempts) {
            $attempt++;

            $ch = curl_init($safeUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                CURLOPT_CONNECTTIMEOUT => 4,
                CURLOPT_TIMEOUT => $this->timeoutSeconds,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $res = curl_exec($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);

            if ($res === false) {
                $lastException = new PhProviderUnavailableException("Gemini embedding API network failure: " . $err);
                if ($attempt < $maxAttempts) {
                    usleep($attempt * 400000); // 400ms, 800ms...
                    continue;
                }
                throw $lastException;
            }

            if ($code === 200) {
                return (string)$res;
            }

            // Strip any raw API key from error body if echoed back
            $cleanBody = str_replace($this->apiKey, '[REDACTED_API_KEY]', (string)$res);
            $cleanBody = mb_substr(trim($cleanBody), 0, 500);

            if ($code === 401 || $code === 403) {
                throw new PhProviderUnavailableException("Gemini embedding authentication failed (HTTP {$code}): {$cleanBody}");
            }

            // Retryable errors: Rate limit (429) or transient server errors (500, 503)
            if ($code === 429 && $attempt < $maxAttempts) {
                sleep($attempt * 2); // 2s, 4s, 6s, 8s backoff for 429 rate limit
                continue;
            }

            if (($code === 500 || $code === 503) && $attempt < $maxAttempts) {
                usleep($attempt * 600000); // 600ms, 1200ms
                continue;
            }

            if ($code === 429) {
                throw new PhProviderUnavailableException("Gemini embedding rate limit exceeded (HTTP 429).");
            }

            throw new PhProviderUnavailableException("Gemini embedding error (HTTP {$code}): {$cleanBody}");
        }

        throw $lastException ?? new PhProviderUnavailableException("Gemini embedding request failed after {$maxAttempts} attempts.");
    }

    /**
     * Enforce credentials presence.
     */
    private function assertConfigured(): void {
        if (!$this->isConfigured()) {
            throw new PhProviderUnavailableException("Gemini API key is not configured in backend/.env (GEMINI_API_KEY is empty).");
        }
    }
}

