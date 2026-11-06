<?php
declare(strict_types=1);

/**
 * ParentingHub AI Catalog Retriever & Grounding Engine
 * 
 * Secure database interface for AI:
 * - Uses ONLY prepared statements (prevents SQL injection)
 * - Restricts candidate sets to verified products
 * - Filters by budget, developmental stage, availability
 * - Extracts structured product specs for side-by-side comparison
 * - Re-hydrates LLM output with genuine database records
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/intent_classifier.php';

class PhCatalogRetriever {

    /**
     * Map child age in months to developmental stage taxonomy and product categories.
     */
    public static function getStageForAge(?int $months): array {
        if ($months === null) {
            return [
                'stage' => 'All Stages',
                'description' => 'General infant and toddler essentials',
                'preferredCategories' => []
            ];
        }

        if ($months <= 3) {
            return [
                'stage' => 'Newborn & Fourth Trimester (0-3 Months)',
                'description' => 'Gentle feeding, anti-colic solutions, soothing pacifiers, and breast care essentials.',
                'preferredCategories' => ['Feeding Bottles', 'Soothing', 'Pacifiers', 'Teats', 'Breast Feeding', 'Breast Care', 'Electricals']
            ];
        } elseif ($months <= 6) {
            return [
                'stage' => 'Sensory Explorer & Rolling (4-6 Months)',
                'description' => 'Sensory exploration, teething relief, beginner training cups, and developmental play.',
                'preferredCategories' => ['Soothing', 'Pacifiers', 'Cup & Weaning', 'Teats', 'Baby Toys & Activity', 'Feeding Bottles']
            ];
        } elseif ($months <= 9) {
            return [
                'stage' => 'Sitting & Weaning Stage (7-9 Months)',
                'description' => 'High chairs, weaning cups, self-feeding utensils, and bath safety.',
                'preferredCategories' => ['High Chairs', 'Cup & Weaning', 'Non-Spill Cups', 'Baby Bath & Care', 'Baby Swings & Bouncers']
            ];
        } elseif ($months <= 12) {
            return [
                'stage' => 'Crawling & First Words (10-12 Months)',
                'description' => 'Mobile exploration, strollers, playards, and advanced feeding cups.',
                'preferredCategories' => ['Baby Strollers', 'High Chairs', 'Playards & Cribs', 'Non-Spill Cups', 'Cup & Weaning']
            ];
        } elseif ($months <= 24) {
            return [
                'stage' => 'Toddler Explorer & Walking (1-2 Years)',
                'description' => 'Active mobility, ride-on toys, travel gear, and independent toddler feeding.',
                'preferredCategories' => ['Ride-On & Scooters', 'Baby Strollers', 'Car Seats & Carry Cots', 'High Chairs', 'Baby Toys & Activity']
            ];
        } else {
            return [
                'stage' => 'Preschool & Active Motor Skills (2+ Years)',
                'description' => 'Active coordination, scooters, and everyday toddler care.',
                'preferredCategories' => ['Ride-On & Scooters', 'Car Seats & Carry Cots', 'Baby Toys & Activity']
            ];
        }
    }

    /**
     * Search and retrieve candidate products safely from database with deterministic intent classification.
     */
    public static function searchCandidates(
        PDO $pdo,
        string $queryText,
        ?int $ageMonths = null,
        ?float $maxBudget = null,
        ?string $categoryFilter = null,
        int $limit = 8,
        bool $inStockOnly = true
    ): array {
        // 1. Deterministic intent classification
        $intent = PhIntentClassifier::classify($queryText);
        $allowedCategories = $intent['allowed_categories'];

        // Enforce categories strictly if intent identifies target category boundaries
        $enforcedCategories = [];
        if (!empty($allowedCategories)) {
            $enforcedCategories = $allowedCategories;
        } elseif (!empty($categoryFilter)) {
            $enforcedCategories = [trim($categoryFilter)];
        }

        // Developmental stage mapping
        $stageInfo = self::getStageForAge($ageMonths);
        $stagePreferred = $stageInfo['preferredCategories'];

        // If no explicit intent categories, use stage-preferred categories
        if (empty($enforcedCategories) && !empty($stagePreferred)) {
            $preferredCategories = $stagePreferred;
        } else {
            $preferredCategories = $enforcedCategories;
        }

        // Search terms expansion
        $searchTerms = $intent['search_terms'];
        if (empty($searchTerms)) {
            $cleanQuery = preg_replace('/[^a-zA-Z0-9\s]/', ' ', strtolower($queryText));
            $words = array_values(array_filter(explode(' ', (string)$cleanQuery), fn($w) => strlen($w) >= 3));
            $searchTerms = array_diff($words, [
                'the', 'for', 'which', 'what', 'need', 'want', 'please', 'with', 'best', 'good', 'suggest',
                'find', 'show', 'give', 'available', 'have', 'from', 'under', 'price', 'rate',
                'baby', 'infant', 'toddler', 'newborn', 'kid', 'kids', 'child', 'children',
                'bacha', 'bachay', 'bachon', 'bache', 'wala', 'wali'
            ]);
            $searchTerms = array_values($searchTerms);
        }

        $conditions = ["1=1"];
        $params = [];

        if ($inStockOnly) {
            $conditions[] = "in_stock = 1";
        }

        if ($maxBudget !== null && $maxBudget > 0) {
            $conditions[] = "price <= ?";
            $params[] = $maxBudget;
        }

        // Category constraints
        if (!empty($enforcedCategories)) {
            $catPlaceholders = implode(',', array_fill(0, count($enforcedCategories), '?'));
            $conditions[] = "category IN ($catPlaceholders)";
            $params = array_merge($params, $enforcedCategories);
        } elseif (!empty($categoryFilter)) {
            $conditions[] = "category LIKE ?";
            $params[] = '%' . trim($categoryFilter) . '%';
        }

        // Keyword search across title, description, brand, category, tags
        if (!empty($searchTerms)) {
            $kwClauses = [];
            foreach (array_slice($searchTerms, 0, 6) as $w) {
                $kwClauses[] = "(name LIKE ? OR description LIKE ? OR brand LIKE ? OR category LIKE ? OR tags LIKE ?)";
                $like = "%{$w}%";
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }
            if (!empty($kwClauses)) {
                $conditions[] = "(" . implode(' OR ', $kwClauses) . ")";
            }
        }

        $whereSql = implode(' AND ', $conditions);
        $sql = "SELECT id, sku, name, price, compare_at, image, images, category, brand, description, 
                       has_options, options_json, rating, reviews, in_stock, tags, source_url
                FROM products 
                WHERE {$whereSql}
                ORDER BY in_stock DESC, rating DESC, price ASC
                LIMIT " . (int)max(1, min(36, $limit * 3));

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Fallback: ONLY relax text constraints within enforced/stage categories.
        // NEVER perform a blind global fallback across unrelated store categories!
        if (empty($rows)) {
            if (!empty($enforcedCategories)) {
                $fallbackConditions = ["1=1"];
                $fallbackParams = [];
                if ($inStockOnly) $fallbackConditions[] = "in_stock = 1";
                if ($maxBudget !== null && $maxBudget > 0) {
                    $fallbackConditions[] = "price <= ?";
                    $fallbackParams[] = $maxBudget;
                }
                $catPlaceholders = implode(',', array_fill(0, count($enforcedCategories), '?'));
                $fallbackConditions[] = "category IN ($catPlaceholders)";
                $fallbackParams = array_merge($fallbackParams, $enforcedCategories);

                $fallbackSql = "SELECT id, sku, name, price, compare_at, image, images, category, brand, description, 
                                       has_options, options_json, rating, reviews, in_stock, tags, source_url
                                FROM products
                                WHERE " . implode(' AND ', $fallbackConditions) . "
                                ORDER BY rating DESC, price ASC
                                LIMIT " . (int)$limit;
                $fStmt = $pdo->prepare($fallbackSql);
                $fStmt->execute($fallbackParams);
                $rows = $fStmt->fetchAll();
            } elseif (!empty($preferredCategories)) {
                $fallbackConditions = ["1=1"];
                $fallbackParams = [];
                if ($inStockOnly) $fallbackConditions[] = "in_stock = 1";
                if ($maxBudget !== null && $maxBudget > 0) {
                    $fallbackConditions[] = "price <= ?";
                    $fallbackParams[] = $maxBudget;
                }
                $catPlaceholders = implode(',', array_fill(0, count($preferredCategories), '?'));
                $fallbackConditions[] = "category IN ($catPlaceholders)";
                $fallbackParams = array_merge($fallbackParams, $preferredCategories);

                $fallbackSql = "SELECT id, sku, name, price, compare_at, image, images, category, brand, description, 
                                       has_options, options_json, rating, reviews, in_stock, tags, source_url
                                FROM products
                                WHERE " . implode(' AND ', $fallbackConditions) . "
                                ORDER BY rating DESC, price ASC
                                LIMIT " . (int)$limit;
                $fStmt = $pdo->prepare($fallbackSql);
                $fStmt->execute($fallbackParams);
                $rows = $fStmt->fetchAll();
            } else {
                // Unknown category query with 0 product matches (e.g. "baby shoes")
                // Return empty candidates. Never return arbitrary unrelated items.
                $rows = [];
            }
        }

        // Post-retrieval validation: Ensure strictly zero cross-category leakage
        if (!empty($enforcedCategories)) {
            $rows = array_values(array_filter($rows, fn($r) => in_array($r['category'], $enforcedCategories, true)));
        }

        // Multi-factor relevance scoring & ranking
        $scored = [];
        $features = $intent['features'] ?? [];
        foreach ($rows as $r) {
            $score = 0;
            $nameLow = strtolower((string)$r['name']);
            $descLow = strtolower((string)$r['description']);
            $tagsLow = strtolower(is_array($r['tags']) ? implode(' ', $r['tags']) : (string)$r['tags']);
            $brandLow = strtolower((string)$r['brand']);

            // Search term keyword hits
            foreach ($searchTerms as $term) {
                $termLow = strtolower($term);
                if (str_contains($nameLow, $termLow)) $score += 25;
                if (str_contains($descLow, $termLow)) $score += 8;
                if (str_contains($tagsLow, $termLow)) $score += 12;
            }

            // Feature alignments
            if (!empty($features['extra_slow_flow'])) {
                if (str_contains($nameLow, 'extra slow') || str_contains($descLow, 'extra slow')) $score += 80;
                elseif (str_contains($nameLow, 'slow flow') || str_contains($descLow, 'slow flow')) $score += 50;
            } elseif (!empty($features['slow_flow'])) {
                if (str_contains($nameLow, 'slow flow') || str_contains($descLow, 'slow flow') || str_contains($nameLow, 'extra slow')) $score += 60;
                elseif (str_contains($nameLow, 'vari-flow') || str_contains($descLow, 'vari-flow')) $score += 35;
            }

            if (!empty($features['anti_colic'])) {
                if (str_contains($nameLow, 'anti-colic') || str_contains($nameLow, 'anticolic') || str_contains($descLow, 'anti-colic') || str_contains($descLow, 'airfree')) $score += 60;
            }

            if (!empty($features['material'])) {
                if (str_contains($nameLow, $features['material']) || str_contains($descLow, $features['material'])) $score += 40;
            }

            // Quality & Stock bonus
            $score += (float)($r['rating'] ?? 0) * 3;
            if (!empty($r['in_stock'])) $score += 50;

            $scored[] = ['score' => $score, 'product' => $r];
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

        // Format candidate items
        $candidates = [];
        foreach (array_slice($scored, 0, $limit) as $item) {
            $candidates[] = self::formatProductRow($item['product']);
        }

        return [
            'stage' => $stageInfo['stage'],
            'stageDescription' => $stageInfo['description'],
            'intent' => $intent,
            'products' => $candidates
        ];
    }

    /**
     * Fetch and compare 2 to 4 products by exact IDs with factual database specifications.
     */
    public static function compareProducts(PDO $pdo, array $productIds): array {
        $cleanIds = array_values(array_unique(array_filter(
            array_map(fn($id) => trim((string)$id), $productIds),
            fn($id) => preg_match('/^[A-Za-z0-9_-]{1,190}$/', $id) === 1
        )));

        if (count($cleanIds) < 2) {
            throw new InvalidArgumentException("Please select at least two valid products to compare.");
        }

        $cleanIds = array_slice($cleanIds, 0, 4);
        $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($cleanIds);
        $rows = $stmt->fetchAll();

        $foundMap = [];
        foreach ($rows as $r) {
            $foundMap[(string)$r['id']] = self::formatProductRow($r);
        }

        // Check if any requested product ID was missing
        $missingIds = array_diff($cleanIds, array_keys($foundMap));

        // Check if at least 2 requested products exist in catalog
        if (count($foundMap) < 2) {
            throw new DomainException("Please select at least two valid catalog products to compare. Found: " . count($foundMap));
        }

        $products = array_values($foundMap);

        // Analyze factual similarities and differences
        $brandCount = count(array_unique(array_column($products, 'brand')));
        $categoryCount = count(array_unique(array_column($products, 'category')));
        $prices = array_column($products, 'price');
        $minPrice = min($prices);
        $maxPrice = max($prices);
        $priceDiff = round($maxPrice - $minPrice, 2);

        $similarities = [];
        if ($categoryCount === 1) {
            $similarities[] = "Both are in the {$products[0]['category']} category.";
        }
        if ($brandCount === 1) {
            $similarities[] = "Both are manufactured by {$products[0]['brand']}.";
        }
        $similarities[] = "Both include nationwide fixed PKR 200 delivery across Pakistan.";

        $differences = [];
        if ($priceDiff > 0) {
            $differences[] = "Price varies by PKR " . number_format($priceDiff, 0) . " (Range: PKR " . number_format($minPrice, 0) . " to PKR " . number_format($maxPrice, 0) . ").";
        } else {
            $similarities[] = "Both products share the exact same price of PKR " . number_format($minPrice, 0) . ".";
        }
        if ($brandCount > 1) {
            $brandsList = implode(', ', array_unique(array_column($products, 'brand')));
            $differences[] = "Different brands: {$brandsList}.";
        }
        if ($categoryCount > 1) {
            $catsList = implode(', ', array_unique(array_column($products, 'category')));
            $differences[] = "Cross-category comparison: {$catsList}. Note that features serve different parenting purposes.";
        }

        // Check options differences
        $optionsComparison = [];
        foreach ($products as $p) {
            $optionsComparison[$p['id']] = !empty($p['options']) ? array_column($p['options'], 'name') : [];
        }

        return [
            'products' => $products,
            'missingProductIds' => array_values($missingIds),
            'comparison' => [
                'priceDifference' => $priceDiff,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'similarities' => $similarities,
                'differences' => $differences,
                'optionsSummary' => $optionsComparison
            ]
        ];
    }

    /**
     * Re-hydrate candidate or LLM-cited product IDs with verified live database attributes.
     * Guaranteed: prices, stock, URLs, and image links come strictly from MySQL.
     */
    public static function hydrateProducts(PDO $pdo, array $productIds): array {
        $cleanIds = array_values(array_unique(array_filter(
            array_map(fn($id) => trim((string)$id), $productIds),
            fn($id) => preg_match('/^[A-Za-z0-9_-]{1,190}$/', $id) === 1
        )));

        if (empty($cleanIds)) return [];

        $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($cleanIds);
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $r) {
            $map[(string)$r['id']] = self::formatProductRow($r);
        }

        // Preserve input order and drop non-existent IDs
        $hydrated = [];
        foreach ($cleanIds as $id) {
            if (isset($map[$id])) {
                $hydrated[] = $map[$id];
            }
        }
        return $hydrated;
    }

    /**
     * Standardize product row format across all AI responses.
     */
    public static function formatProductRow(array $r): array {
        $parseJson = static function($v): array {
            $d = is_string($v) ? json_decode($v, true) : $v;
            return is_array($d) ? array_values($d) : [];
        };

        $image = trim((string)($r['image'] ?? ''));
        if ($image !== '' && !preg_match('~^(?:https?://|data:|blob:)~i', $image)) {
            $image = '/' . ltrim($image, '/');
        }
        if ($image === '') $image = '/art/placeholder.jpg';

        $id = (string)$r['id'];
        $options = $parseJson($r['options_json'] ?? '[]');
        $tags = $parseJson($r['tags'] ?? '[]');

        return [
            'id' => $id,
            'name' => (string)$r['name'],
            'sku' => (string)($r['sku'] ?? ''),
            'price' => (float)($r['price'] ?? 0),
            'compareAt' => isset($r['compare_at']) && $r['compare_at'] !== null ? (float)$r['compare_at'] : null,
            'image' => $image,
            'category' => (string)($r['category'] ?? 'General'),
            'brand' => (string)($r['brand'] ?? 'ParentingHub'),
            'description' => (string)($r['description'] ?? ''),
            'hasOptions' => (bool)($r['has_options'] ?? false),
            'options' => $options,
            'rating' => (float)($r['rating'] ?? 0),
            'reviews' => (int)($r['reviews'] ?? 0),
            'inStock' => (bool)($r['in_stock'] ?? true),
            'tags' => $tags,
            'url' => '/product/' . rawurlencode($id)
        ];
    }
}
