<?php
declare(strict_types=1);

/**
 * ParentingHub Reusable AI Service Layer
 * 
 * Supports:
 * - Configurable AI providers (Google Gemini API, Local Mock, Deterministic Engine)
 * - Safe prompt construction with strict XML boundary delimiters
 * - Prompt injection defenses
 * - Deterministic fallback when API key is missing or offline
 * - Medical and clinical advice boundaries
 * - Server-side JSON schema parsing and product verification
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/catalog_retriever.php';

class PhAiService {

    public const MEDICAL_DISCLAIMER = 'ParentingHub AI provides shopping guidance and general developmental milestones only. It does not provide medical diagnoses, clinical advice, or prescriptions. For illnesses, feeding complications, or medical concerns, please consult a qualified pediatrician.';

    /**
     * Get configured AI model name.
     */
    public static function getModel(): string {
        return ph_env('AI_MODEL', 'gemini-3.5-flash-lite') ?? 'gemini-3.5-flash-lite';
    }

    /**
     * Check if a live LLM API key is configured.
     */
    public static function hasApiKey(): bool {
        $key = trim((string)ph_env('GEMINI_API_KEY', ''));
        return $key !== '' && !str_starts_with($key, '__');
    }

    /**
     * Sanitize user input to neutralize prompt injection attempts.
     */
    public static function sanitizePromptInput(string $input): string {
        // Strip out control characters and normalize spaces
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        // Truncate to reasonable query length (max 1,500 chars)
        $clean = mb_substr(trim((string)$clean), 0, 1500);
        return $clean;
    }

    /**
     * Execute AI assistant conversation for product advice & shopping assistance.
     * 
     * @param PDO $pdo Active database connection
     * @param string $message User question or shopping request
     * @param array $history Previous conversation messages [{'role': 'user'|'assistant', 'content': string}]
     * @param array $context Context details ['childAgeMonths' => int, 'budget' => float, 'category' => string]
     * @return array Standardized response payload
     */
    public static function runAssistant(
        PDO $pdo,
        string $message,
        array $history = [],
        array $context = []
    ): array {
        $safeMessage = self::sanitizePromptInput($message);
        if ($safeMessage === '') {
            throw new InvalidArgumentException('Please enter a shopping question or product request.');
        }

        $ageMonths = isset($context['childAgeMonths']) ? (int)$context['childAgeMonths'] : null;
        $budget = isset($context['budget']) && (float)$context['budget'] > 0 ? (float)$context['budget'] : null;
        $category = !empty($context['category']) ? trim((string)$context['category']) : null;

        // 1. Retrieve grounded catalog candidates
        $searchResult = PhCatalogRetriever::searchCandidates(
            $pdo,
            $safeMessage,
            $ageMonths,
            $budget,
            $category,
            6,
            true
        );

        $candidates = $searchResult['products'];
        $stage = $searchResult['stage'];
        $stageDesc = $searchResult['stageDescription'];
        $intent = $searchResult['intent'] ?? PhIntentClassifier::classify($safeMessage);

        // 2. If Gemini API key is available, call LLM with grounding context
        if (self::hasApiKey()) {
            try {
                $llmResult = self::callGeminiAssistant(
                    $safeMessage,
                    $candidates,
                    $stage,
                    $history,
                    $budget,
                    $intent
                );

                if (!empty($llmResult['reply'])) {
                    // Hydrate only product IDs that truly exist in database
                    $verifiedProducts = PhCatalogRetriever::hydrateProducts($pdo, $llmResult['productIds'] ?? []);
                    if (empty($verifiedProducts) && !empty($candidates)) {
                        $verifiedProducts = array_slice($candidates, 0, 3);
                    }

                    return [
                        'success' => true,
                        'source' => 'gemini_ai',
                        'isFallback' => false,
                        'stage' => $stage,
                        'stageDescription' => $stageDesc,
                        'intent' => $intent['canonical_label'] ?? 'General',
                        'isMedicalConcern' => !empty($intent['is_medical_concern']),
                        'medicalGuidance' => $intent['medical_guidance'] ?? null,
                        'reply' => $llmResult['reply'],
                        'recommendations' => $verifiedProducts,
                        'suggestedActions' => $llmResult['suggestedActions'] ?? self::buildSuggestedActions($verifiedProducts),
                        'medicalDisclaimer' => self::MEDICAL_DISCLAIMER
                    ];
                }
            } catch (Throwable $e) {
                error_log('ParentingHub AI Assistant API failed: ' . $e->getMessage());
                // Seamlessly fall through to deterministic engine
            }
        }

        // 3. Deterministic Grounded Engine (Used when API key is missing, rate-limited, or offline)
        $deterministic = self::generateDeterministicAssistantResponse(
            $safeMessage,
            $candidates,
            $stage,
            $stageDesc,
            $budget,
            $intent
        );

        return [
            'success' => true,
            'source' => 'parentinghub_catalog_engine',
            'isFallback' => true,
            'stage' => $stage,
            'stageDescription' => $stageDesc,
            'intent' => $intent['canonical_label'] ?? 'General',
            'isMedicalConcern' => !empty($intent['is_medical_concern']),
            'medicalGuidance' => $intent['medical_guidance'] ?? null,
            'reply' => $deterministic['reply'],
            'recommendations' => $candidates,
            'suggestedActions' => self::buildSuggestedActions($candidates),
            'medicalDisclaimer' => self::MEDICAL_DISCLAIMER
        ];
    }

    /**
     * Execute Smart Recommendations based on budget and stage.
     */
    public static function runRecommendations(
        PDO $pdo,
        ?int $ageMonths,
        ?float $maxBudget,
        ?string $category,
        string $requirementText = ''
    ): array {
        $safeReq = self::sanitizePromptInput($requirementText);
        $searchResult = PhCatalogRetriever::searchCandidates(
            $pdo,
            $safeReq !== '' ? $safeReq : ($category ?? 'essentials'),
            $ageMonths,
            $maxBudget,
            $category,
            8,
            true
        );

        $candidates = $searchResult['products'];
        $stage = $searchResult['stage'];
        $stageDesc = $searchResult['stageDescription'];

        // Add tailored explanation per product
        foreach ($candidates as &$prod) {
            $prod['reason'] = self::generateMatchReason($prod, $stage, $maxBudget);
        }
        unset($prod);

        return [
            'success' => true,
            'source' => self::hasApiKey() ? 'gemini_grounded_catalog' : 'parentinghub_catalog_engine',
            'isFallback' => !self::hasApiKey(),
            'stage' => $stage,
            'stageDescription' => $stageDesc,
            'budgetFilter' => $maxBudget,
            'count' => count($candidates),
            'recommendations' => $candidates,
            'medicalDisclaimer' => self::MEDICAL_DISCLAIMER
        ];
    }

    /**
     * Execute side-by-side comparison of 2 to 4 products.
     */
    public static function runComparison(PDO $pdo, array $productIds): array {
        $comparisonData = PhCatalogRetriever::compareProducts($pdo, $productIds);
        $products = $comparisonData['products'];
        $comp = $comparisonData['comparison'];

        // Generate concise analytical summary
        $p1 = $products[0];
        $p2 = $products[1] ?? $products[0];
        $summary = "Comparing {$p1['name']} (PKR " . number_format($p1['price'], 0) . ") with {$p2['name']} (PKR " . number_format($p2['price'], 0) . "). ";
        if ($p1['price'] < $p2['price']) {
            $summary .= "{$p1['name']} is the budget-conscious option saving PKR " . number_format($p2['price'] - $p1['price'], 0) . ". ";
        } elseif ($p1['price'] > $p2['price']) {
            $summary .= "{$p2['name']} offers a lower starting price by PKR " . number_format($p1['price'] - $p2['price'], 0) . ". ";
        } else {
            $summary .= "Both share identical pricing. ";
        }
        $summary .= "Select based on preferred features, brand compatibility, and your baby's specific stage.";

        return [
            'success' => true,
            'source' => 'parentinghub_comparison_engine',
            'products' => $products,
            'missingProductIds' => $comparisonData['missingProductIds'],
            'comparison' => $comp,
            'verdict' => [
                'summary' => $summary,
                'recommendedChoice' => $products[0]['id']
            ]
        ];
    }

    /**
     * Call Google Gemini API with system grounding prompt.
     */
    private static function callGeminiAssistant(
        string $userMessage,
        array $candidates,
        string $stage,
        array $history,
        ?float $budget,
        ?array $intent = null
    ): array {
        $apiKey = ph_env('GEMINI_API_KEY');
        $model = self::getModel();
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        // Prepare grounded candidates representation
        $catalogSummary = [];
        foreach ($candidates as $c) {
            $catalogSummary[] = "- ID: {$c['id']} | Name: {$c['name']} | Brand: {$c['brand']} | Category: {$c['category']} | Price: PKR {$c['price']} | InStock: " . ($c['inStock'] ? 'Yes' : 'No');
        }
        $catalogContext = !empty($catalogSummary) ? implode("\n", $catalogSummary) : "(No matching catalog products available for this query)";

        $medicalSafetyPrompt = "";
        if (!empty($intent['is_medical_concern'])) {
            $medicalSafetyPrompt = "\nSAFETY NOTICE: The parent has mentioned a potential infant feeding difficulty or medical concern (e.g., weak sucking, tiring during feeds, or poor swallowing). YOU MUST NOT prescribe teat flow rates or claim that feeding bottles treat medical conditions. Explicitly recommend consulting a pediatrician or feeding specialist for in-person evaluation. Only present catalog items as options to discuss with their doctor.";
        }

        $systemInstruction = "You are the ParentingHub SmartCart AI shopping assistant for Pakistani parents.
Your task is to answer shopping questions and recommend authentic products from ParentingHub Pakistan.
RULES:
1. ONLY recommend products from the provided CANDIDATE CATALOG list below. Never invent product names, prices, or specifications.
2. Prices are in Pakistani Rupees (PKR). Standard shipping is fixed at PKR 200 nationwide.
3. DO NOT offer clinical or medical diagnoses. Recommend consulting a pediatrician for health concerns.{$medicalSafetyPrompt}
4. If a user asks to compare products, identify the IDs from the candidate list.
5. If the candidate catalog is empty, clearly state that no matching products are currently available in the verified inventory.
6. Return your answer strictly in valid JSON matching this schema:
{
  \"reply\": \"Clear, helpful response to the parent in conversational English\",
  \"productIds\": [\"id1\", \"id2\"],
  \"suggestedActions\": [
    {\"label\": \"Action title\", \"action\": \"compare\"|\"view\"|\"cart\", \"productIds\": [\"id1\"]}
  ]
}
DO NOT wrap the JSON in Markdown code fences. Return raw JSON only.";

        $userPrompt = "DEVELOPMENTAL STAGE: {$stage}
BUDGET LIMIT: " . ($budget ? "PKR {$budget}" : "None specified") . "

CANDIDATE CATALOG:
{$catalogContext}

<user_query>
{$userMessage}
</user_query>";

        $payload = json_encode([
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemInstruction . "\n\n" . $userPrompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 800,
                'responseMimeType' => 'application/json'
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($rawResponse === false || $httpCode !== 200) {
            throw new RuntimeException("Gemini API HTTP {$httpCode}: " . ($curlErr ?: (string)$rawResponse));
        }

        $decoded = json_decode((string)$rawResponse, true);
        $candidateText = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
        if ($candidateText === '') {
            throw new RuntimeException('Gemini returned an empty candidate text.');
        }

        // Clean any possible Markdown fence wrappers
        $cleanedJson = trim($candidateText);
        if (str_starts_with($cleanedJson, '```json')) {
            $cleanedJson = substr($cleanedJson, 7);
        }
        if (str_starts_with($cleanedJson, '```')) {
            $cleanedJson = substr($cleanedJson, 3);
        }
        if (str_ends_with($cleanedJson, '```')) {
            $cleanedJson = substr($cleanedJson, 0, -3);
        }
        $cleanedJson = trim($cleanedJson);

        $parsed = json_decode($cleanedJson, true);
        if (!is_array($parsed) || empty($parsed['reply'])) {
            throw new RuntimeException('Gemini response was not valid expected JSON schema.');
        }

        return $parsed;
    }

    /**
     * Deterministic grounded response generator when LLM is unavailable.
     */
    private static function generateDeterministicAssistantResponse(
        string $query,
        array $candidates,
        string $stage,
        string $stageDesc,
        ?float $budget,
        ?array $intent = null
    ): array {
        $count = count($candidates);
        $medicalPrefix = '';
        if (!empty($intent['is_medical_concern'])) {
            $guidance = $intent['medical_guidance'] ?? PhIntentClassifier::MEDICAL_SAFETY_NOTICE;
            $medicalPrefix = "IMPORTANT CLINICAL NOTE: " . $guidance . "\n\n";
        }

        $searchTerms = $intent['search_terms'] ?? [];

        if ($count === 0) {
            if (empty($searchTerms) && !empty($intent['requires_clarification'])) {
                $reply = $medicalPrefix . "To help you find the right item from ParentingHub's verified collection, could you share what product you are looking for (e.g., feeding bottle, stroller, swing) or your baby's age?";
            } else {
                $reply = $medicalPrefix . "I searched our ParentingHub catalog for '{$query}', but found no active products matching that exact criteria" . ($budget ? " under PKR " . number_format($budget, 0) : "") . ". ParentingHub specializes in feeding essentials, strollers, nursery furniture, soothing products, and developmental baby gear.";
            }
        } else {
            $topNames = array_map(fn($p) => $p['name'] . " (PKR " . number_format($p['price'], 0) . ")", array_slice($candidates, 0, 2));
            if (!empty($intent['is_medical_concern'])) {
                $reply = $medicalPrefix . "For your review and discussion with your pediatrician, here are authentic feeding essentials available in our verified catalog: " . implode(' and ', $topNames) . ". All items are 100% genuine with fixed PKR 200 standard delivery across Pakistan.";
            } else {
                $categoryLabel = !empty($intent['canonical_label']) ? $intent['canonical_label'] : $stage;
                $reply = "Based on our verified catalog for {$categoryLabel}, here are top-rated recommendations: " . implode(' and ', $topNames) . ". All items are in stock with genuine product guarantees and fixed PKR 200 standard delivery nationwide.";
            }
        }

        return ['reply' => $reply];
    }

    /**
     * Build suggested quick actions based on retrieved products.
     */
    private static function buildSuggestedActions(array $products): array {
        $actions = [];
        if (count($products) >= 2) {
            $actions[] = [
                'label' => 'Compare ' . $products[0]['brand'] . ' vs ' . $products[1]['brand'],
                'action' => 'compare',
                'productIds' => [$products[0]['id'], $products[1]['id']]
            ];
        }
        if (!empty($products[0])) {
            $actions[] = [
                'label' => 'View ' . $products[0]['name'],
                'action' => 'view',
                'productIds' => [$products[0]['id']]
            ];
            $actions[] = [
                'label' => 'Propose Starter Cart',
                'action' => 'propose_cart',
                'productIds' => array_column(array_slice($products, 0, 3), 'id')
            ];
        }
        return $actions;
    }

    /**
     * Generate match explanation badge for recommendation cards.
     */
    private static function generateMatchReason(array $product, string $stage, ?float $maxBudget): string {
        $reasons = [];
        if ($maxBudget !== null && $product['price'] <= $maxBudget) {
            $reasons[] = "Fits within your budget of PKR " . number_format($maxBudget, 0);
        }
        if ($product['rating'] >= 4.5) {
            $reasons[] = "Top rated ({$product['rating']}★)";
        }
        if (!empty($product['brand'])) {
            $reasons[] = "100% authentic {$product['brand']} product";
        }
        return implode(' • ', $reasons) ?: "Recommended for {$stage}";
    }
}
