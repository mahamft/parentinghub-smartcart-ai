<?php
declare(strict_types=1);

/**
 * ParentingHub AI - Deterministic Intent & Medical Safety Classifier
 *
 * Provides:
 * 1. Multilingual intent classification for Pakistani parenting vocabulary (English, Urdu transliteration, Roman Urdu).
 * 2. Strict category mapping preventing cross-category catalog contamination.
 * 3. Pediatric & clinical safety guardrails for infant feeding difficulties.
 * 4. Specific sub-feature extraction (e.g. flow rates, anti-colic, bottle material).
 */

if (!defined('PH_ENTRY') && !defined('PH_TEST_SUITE')) {
    http_response_code(404);
    exit;
}

class PhIntentClassifier {

    public const MEDICAL_SAFETY_NOTICE = "Weak sucking, tiring easily during feeds, or feeding difficulty can be signs of early fatigue, oral-motor coordination challenges, tongue-tie, or underlying clinical concerns. Equipment alone does not diagnose or treat medical feeding difficulties. Please consult your pediatrician or a qualified pediatric feeding specialist / lactation consultant for an in-person assessment.";

    /**
     * Classify query intent, extract target categories, and detect clinical feeding flags.
     *
     * @param string $query User input text
     * @return array Classification result
     */
    public static function classify(string $query): array {
        $q = trim((string)$query);
        $normalized = self::normalizeText($q);

        // 1. Detect Medical Concerns & Feeding Difficulties
        $medicalCheck = self::detectMedicalConcern($normalized);

        // 2. Detect Specific Intent & Target Categories
        $intentData = self::detectIntent($normalized);

        // 3. Extract sub-features (flow rates, materials, anti-colic)
        $features = self::extractFeatures($normalized);

        // 4. Generate search expansion keywords
        $searchTerms = self::buildSearchTerms($normalized, $intentData, $features);

        return [
            'raw_query' => $q,
            'normalized_query' => $normalized,
            'is_medical_concern' => $medicalCheck['is_medical'],
            'medical_guidance' => $medicalCheck['guidance'],
            'medical_flags' => $medicalCheck['flags'],
            'primary_intent' => $intentData['primary_intent'],
            'canonical_label' => $intentData['canonical_label'],
            'allowed_categories' => $intentData['allowed_categories'],
            'features' => $features,
            'search_terms' => $searchTerms,
            'requires_clarification' => $intentData['requires_clarification']
        ];
    }

    /**
     * Normalize text handling Roman Urdu and common colloquial variants.
     */
    public static function normalizeText(string $text): string {
        $t = mb_strtolower(trim($text), 'UTF-8');
        // Replace punctuation with spaces
        $t = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $t);
        // Normalize multiple spaces
        $t = preg_replace('/\s+/', ' ', (string)$t);
        return trim((string)$t);
    }

    /**
     * Detect medical feeding complications and safety boundaries.
     */
    private static function detectMedicalConcern(string $text): array {
        $flags = [];

        $patterns = [
            'weak_sucking' => '/\b(low sucking|weak suck|poor suck|sucking power|weak sucking power|sucking issue|cant suck|cannot suck|difficulty sucking|kamzor suck|suck nahi|doodh nahi pee raha|doodh nahi peeta)\b/i',
            'feeding_fatigue' => '/\b(tiring during feeding|tires easily|gets tired drinking|falling asleep feeding|feeding fatigue|exhausted drinking|peete huay so jata)\b/i',
            'swallowing_difficulty' => '/\b(choking|coughing while feeding|gagging|trouble swallowing|difficulty swallowing|phanda lagna)\b/i',
            'persistent_reflux' => '/\b(severe reflux|vomiting milk|projectile vomiting|throwing up milk|doodh nikalna bar bar)\b/i',
            'failure_to_thrive' => '/\b(weight loss|losing weight|not gaining weight|wazan nahi barh raha|underweight baby)\b/i',
            'structural_concern' => '/\b(tongue tie|lip tie|cleft lip|cleft palate|hypotonia)\b/i'
        ];

        foreach ($patterns as $flagName => $regex) {
            if (preg_match($regex, $text)) {
                $flags[] = $flagName;
            }
        }

        $isMedical = !empty($flags);
        $guidance = null;

        if ($isMedical) {
            $guidance = self::MEDICAL_SAFETY_NOTICE;
        }

        return [
            'is_medical' => $isMedical,
            'guidance' => $guidance,
            'flags' => $flags
        ];
    }

    /**
     * Detect specific category intent and allowed product categories.
     */
    private static function detectIntent(string $text): array {
        $t = ' ' . $text . ' ';

        // 1. Baby Teats (specific focus over bottles if teat/chuchi/flow is the direct subject)
        if (preg_match('/\b(teat|teats|nipple|nipples|chuchi|chuchy|feeder ka nipple|feeder ki chuchi|slow flow|extra slow|extra-slow|fast flow|vari flow|variable flow|flow rate)\b/i', $t)) {
            return [
                'primary_intent' => 'baby_teats',
                'canonical_label' => 'Baby Teats',
                'allowed_categories' => ['Teats', 'Bottle Feeding', 'Feeding Bottles'],
                'requires_clarification' => false
            ];
        }

        // 2. Feeding Bottles (feeder, doodh ka feeder, anti-colic feeder)
        if (preg_match('/\b(feeder|feeders|doodh ka feeder|doodh wala feeder|feeder bottle|feeding bottle|baby bottle|milk bottle|anti colic bottle|anticolic bottle|anti-colic bottle|doodh ki botal|botal|baby feeder|newborn feeder)\b/i', $t)) {
            return [
                'primary_intent' => 'feeding_bottles',
                'canonical_label' => 'Feeding Bottles',
                'allowed_categories' => ['Feeding Bottles', 'Bottle Feeding', 'Teats'],
                'requires_clarification' => false
            ];
        }

        // 3. Breast Pumps & Nursing
        if (preg_match('/\b(breast pump|breastpump|breast pumps|milk pump|doodh nikalnay wali machine|nursing pump|manual pump|electric breast pump|breast pads|milk storage pouch)\b/i', $t)) {
            return [
                'primary_intent' => 'breast_pumps',
                'canonical_label' => 'Breast Pumps',
                'allowed_categories' => ['Breast Feeding', 'Breast Care'],
                'requires_clarification' => false
            ];
        }

        // 4. Baby Strollers & Prams
        if (preg_match('/\b(stroller|strollers|pram|prams|buggy|pushchair|carriage|bachay ki gari|bachay ka pram|bachay ka stroller|travel stroller)\b/i', $t)) {
            return [
                'primary_intent' => 'baby_strollers',
                'canonical_label' => 'Baby Strollers',
                'allowed_categories' => ['Baby Strollers'],
                'requires_clarification' => false
            ];
        }

        // 5. Baby Swings & Bouncers
        if (preg_match('/\b(jhoola|jhula|jhoole|jhoolay|swing|swings|bouncer|bouncers|rocker|rockers|baby swing|electric swing|electric jhoola|cradle)\b/i', $t)) {
            return [
                'primary_intent' => 'baby_swings',
                'canonical_label' => 'Baby Swings',
                'allowed_categories' => ['Baby Swings & Bouncers'],
                'requires_clarification' => false
            ];
        }

        // 6. Car Seats & Carry Cots
        if (preg_match('/\b(car seat|carseat|car seats|carry cot|carrycot|isofix|infant car seat|gari ki seat)\b/i', $t)) {
            return [
                'primary_intent' => 'car_seats',
                'canonical_label' => 'Car Seats & Carry Cots',
                'allowed_categories' => ['Car Seats & Carry Cots'],
                'requires_clarification' => false
            ];
        }

        // 7. Soothing & Pacifiers
        if (preg_match('/\b(pacifier|pacifiers|soother|soothers|soothing|chupni|choosni|chuchey|dummy|teether|teethers|teething|soothie)\b/i', $t)) {
            return [
                'primary_intent' => 'soothing_pacifiers',
                'canonical_label' => 'Soothing & Pacifiers',
                'allowed_categories' => ['Soothing', 'Pacifiers'],
                'requires_clarification' => false
            ];
        }

        // 8. Weaning & Cups
        if (preg_match('/\b(cup|cups|sippy cup|sippy|straw cup|training cup|weaning|non spill|spout cup|feeder spoon|suction bowl|baby spoon|baby bowl|toddler cup)\b/i', $t)) {
            return [
                'primary_intent' => 'weaning_cups',
                'canonical_label' => 'Cup & Weaning',
                'allowed_categories' => ['Cup & Weaning', 'Non-Spill Cups'],
                'requires_clarification' => false
            ];
        }

        // 9. High Chairs
        if (preg_match('/\b(high chair|highchair|high chairs|booster seat|dining chair|feeding chair|khanay wali chair)\b/i', $t)) {
            return [
                'primary_intent' => 'high_chairs',
                'canonical_label' => 'High Chairs',
                'allowed_categories' => ['High Chairs'],
                'requires_clarification' => false
            ];
        }

        // 10. Playards & Cribs
        if (preg_match('/\b(crib|cribs|cot|cots|playard|playards|bassinet|playpen|baby bed|cradle bed)\b/i', $t)) {
            return [
                'primary_intent' => 'playards_cribs',
                'canonical_label' => 'Playards & Cribs',
                'allowed_categories' => ['Playards & Cribs'],
                'requires_clarification' => false
            ];
        }

        // 11. Ride-On & Scooters
        if (preg_match('/\b(scooter|scooters|ride on|ride-on|tricycle|trike|skates|roller skates|kick scooter)\b/i', $t)) {
            return [
                'primary_intent' => 'ride_on_scooters',
                'canonical_label' => 'Ride-On & Scooters',
                'allowed_categories' => ['Ride-On & Scooters'],
                'requires_clarification' => false
            ];
        }

        // 12. Toys & Activity
        if (preg_match('/\b(toy|toys|activity gym|playmat|play mat|piano gym|baby walker|walker|rattle|sensory mat)\b/i', $t)) {
            return [
                'primary_intent' => 'toys_activity',
                'canonical_label' => 'Baby Toys & Activity',
                'allowed_categories' => ['Baby Toys & Activity'],
                'requires_clarification' => false
            ];
        }

        // 13. Electricals
        if (preg_match('/\b(sterilizer|steriliser|bottle warmer|warmer|steamer|electric sterilizer)\b/i', $t)) {
            return [
                'primary_intent' => 'electricals',
                'canonical_label' => 'Electricals',
                'allowed_categories' => ['Electricals'],
                'requires_clarification' => false
            ];
        }

        // 14. Bath & Hygiene
        if (preg_match('/\b(bath|bathtub|tub|baby bather|baby bath)\b/i', $t)) {
            return [
                'primary_intent' => 'baby_bath',
                'canonical_label' => 'Baby Bath & Care',
                'allowed_categories' => ['Baby Bath & Care'],
                'requires_clarification' => false
            ];
        }

        // 15. General Feeding Reference (weak suck, feeding, milk, bottle)
        if (preg_match('/\b(feed|feeding|milk|formula|latch|colic|burp|suck|sucking)\b/i', $t)) {
            return [
                'primary_intent' => 'feeding_general',
                'canonical_label' => 'Feeding Essentials',
                'allowed_categories' => ['Feeding Bottles', 'Bottle Feeding', 'Teats', 'Breast Feeding'],
                'requires_clarification' => false
            ];
        }

        // Default: Unspecified intent (open query)
        return [
            'primary_intent' => 'unspecified',
            'canonical_label' => 'General Catalog',
            'allowed_categories' => [],
            'requires_clarification' => true
        ];
    }

    /**
     * Extract specific attributes and features from query.
     */
    private static function extractFeatures(string $text): array {
        $features = [];

        if (preg_match('/\b(extra slow|extra-slow|first flow|0m\+? flow)\b/i', $text)) {
            $features['flow_rate'] = 'extra_slow';
        } elseif (preg_match('/\b(slow flow|slow-flow|1m\+? flow|halka flow)\b/i', $text)) {
            $features['flow_rate'] = 'slow';
        } elseif (preg_match('/\b(fast flow|fast-flow|tez flow|6m\+? flow)\b/i', $text)) {
            $features['flow_rate'] = 'fast';
        } elseif (preg_match('/\b(vari-flow|vari flow|variable flow)\b/i', $text)) {
            $features['flow_rate'] = 'vari_flow';
        }

        if (preg_match('/\b(anti-colic|anticolic|anti colic|airfree|colic|gas)\b/i', $text)) {
            $features['anti_colic'] = true;
        }

        if (preg_match('/\b(glass|shisha|sheesha)\b/i', $text)) {
            $features['material'] = 'glass';
        } elseif (preg_match('/\b(silicone)\b/i', $text)) {
            $features['material'] = 'silicone';
        }

        if (preg_match('/\b(newborn|0-3m|0-2m|0 month|0m)\b/i', $text)) {
            $features['age_bracket'] = 'newborn';
        }

        return $features;
    }

    /**
     * Build search keywords adapted to ParentingHub's database catalog.
     */
    private static function buildSearchTerms(string $text, array $intentData, array $features): array {
        $terms = [];

        switch ($intentData['primary_intent']) {
            case 'baby_teats':
                $terms[] = 'teat';
                if (!empty($features['flow_rate'])) {
                    if ($features['flow_rate'] === 'extra_slow') {
                        $terms[] = 'extra slow';
                        $terms[] = 'slow flow';
                    } elseif ($features['flow_rate'] === 'slow') {
                        $terms[] = 'slow';
                    } elseif ($features['flow_rate'] === 'vari_flow') {
                        $terms[] = 'vari';
                    }
                }
                break;

            case 'feeding_bottles':
            case 'feeding_general':
                $terms[] = 'bottle';
                if (!empty($features['flow_rate'])) {
                    if ($features['flow_rate'] === 'extra_slow') {
                        $terms[] = 'extra slow';
                        $terms[] = 'slow flow';
                        $terms[] = 'teat';
                    } elseif ($features['flow_rate'] === 'slow') {
                        $terms[] = 'slow';
                        $terms[] = 'teat';
                    } elseif ($features['flow_rate'] === 'vari_flow') {
                        $terms[] = 'vari';
                        $terms[] = 'teat';
                    }
                }
                $terms[] = 'anti-colic';
                if (!empty($features['anti_colic'])) {
                    $terms[] = 'airfree';
                }
                if (!empty($features['material']) && $features['material'] === 'glass') {
                    $terms[] = 'glass';
                }
                break;

            case 'breast_pumps':
                $terms[] = 'pump';
                $terms[] = 'breast';
                break;

            case 'baby_strollers':
                $terms[] = 'stroller';
                break;

            case 'baby_swings':
                $terms[] = 'swing';
                $terms[] = 'bouncer';
                break;

            case 'car_seats':
                $terms[] = 'car seat';
                $terms[] = 'carry cot';
                break;

            case 'soothing_pacifiers':
                $terms[] = 'soother';
                $terms[] = 'pacifier';
                $terms[] = 'teether';
                break;

            case 'weaning_cups':
                $terms[] = 'cup';
                $terms[] = 'weaning';
                break;

            case 'high_chairs':
                $terms[] = 'high chair';
                break;

            case 'playards_cribs':
                $terms[] = 'playard';
                $terms[] = 'crib';
                $terms[] = 'cradle';
                break;

            case 'ride_on_scooters':
                $terms[] = 'scooter';
                $terms[] = 'ride-on';
                break;

            case 'toys_activity':
                $terms[] = 'gym';
                $terms[] = 'playmat';
                $terms[] = 'toy';
                break;

            case 'electricals':
                $terms[] = 'warmer';
                $terms[] = 'sterilizer';
                break;

            case 'baby_bath':
                $terms[] = 'bath';
                break;

            default:
                // Extract general alphanumeric words (len >= 3), filtering out universal baby store stopwords
                $generalStopwords = [
                    'the', 'for', 'which', 'what', 'need', 'want', 'please', 'with', 'best', 'good', 'suggest',
                    'find', 'show', 'give', 'available', 'have', 'from', 'under', 'price', 'rate',
                    'baby', 'infant', 'toddler', 'newborn', 'kid', 'kids', 'child', 'children',
                    'bacha', 'bachay', 'bachon', 'bache', 'ap', 'aap', 'kya', 'hai', 'hain', 'mein', 'wala', 'wali',
                    'size', 'sizes', 'color', 'colour', 'colors', 'colours', 'pack', 'piece', 'pieces', 'pcs',
                    'review', 'reviews', 'item', 'items', 'product', 'products', 'design', 'designs', 'model', 'models'
                ];
                $words = explode(' ', $text);
                foreach ($words as $w) {
                    if (strlen($w) >= 3 && !in_array($w, $generalStopwords, true)) {
                        $terms[] = $w;
                    }
                }
                break;
        }

        return array_values(array_unique($terms));
    }
}
