<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://www.parentinghub.pk');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// 1. Read input JSON
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: [];

$childName = trim((string)($data['child_name'] ?? $data['childName'] ?? 'Little One'));
$dob = trim((string)($data['dob'] ?? ''));
$parentName = trim((string)($data['parent_name'] ?? $data['parentName'] ?? 'Parent'));
$parentEmail = trim((string)($data['parent_email'] ?? $data['parentEmail'] ?? ''));
$query = trim((string)($data['query'] ?? ''));

$queryLength = function_exists('mb_strlen') ? mb_strlen($query) : strlen($query);
if ($query === '' || $queryLength > 2000) {
    http_response_code(422);
    echo json_encode(['success'=>false,'message'=>'Please enter a parenting question up to 2,000 characters.']);
    exit;
}

// Calculate age in months
$ageMonths = null;
if (!empty($dob)) {
    try {
        $dobDate = new DateTime($dob);
        $now = new DateTime();
        $diff = $dobDate->diff($now);
        $ageMonths = ($diff->y * 12) + $diff->m;
        if ($ageMonths < 0) $ageMonths = 0;
    } catch (Exception $e) {
        $ageMonths = null;
    }
}

// If the caller does not provide a DOB, safely infer a simple age only when the
// parent explicitly includes it in the question (for example, "8 month old").
if ($ageMonths === null) {
    if (preg_match('/\b(\d{1,2})\s*(?:month|months|mo|mos|mths)\b/i', $query, $m)) {
        $candidate = (int)$m[1];
        if ($candidate >= 0 && $candidate <= 60) $ageMonths = $candidate;
    } elseif (preg_match('/\b(\d(?:\.\d)?)\s*(?:year|years|yr|yrs)\b/i', $query, $m)) {
        $candidate = (int)round((float)$m[1] * 12);
        if ($candidate >= 0 && $candidate <= 60) $ageMonths = $candidate;
    }
}

// 2. Prepare payload for the optional production advisor webhook
$payload = json_encode([
    'child_name'   => $childName,
    'dob'          => $dob,
    'parent_name'  => $parentName,
    'parent_email' => $parentEmail,
    'query'        => $query
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

// The production webhook is configured server-side; never expose temporary tunnel URLs in browser code.
$configuredWebhook = trim((string)ph_env('AI_ADVISOR_WEBHOOK_URL', ''));
$webhookUrls = $configuredWebhook !== '' ? [$configuredWebhook] : [];

$webhookResponse = null;
$httpCode = 0;

foreach ($webhookUrls as $url) {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 35);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        
        $result = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($result)) {
            $parsed = json_decode($result, true);
            if (is_array($parsed) && (isset($parsed['advice']) || isset($parsed['status']) || isset($parsed['store_recommendation']))) {
                $webhookResponse = $parsed;
                break;
            }
        }
    }
}

// 3. If n8n answered successfully
if ($webhookResponse !== null) {
    echo json_encode([
        'success' => true,
        'source'  => 'n8n_ai',
        'status'  => $webhookResponse['status'] ?? 'success',
        'child_name' => $webhookResponse['child_name'] ?? $childName,
        'age_months' => isset($webhookResponse['age_months']) ? (int)$webhookResponse['age_months'] : $ageMonths,
        'stage'   => $webhookResponse['stage'] ?? ($ageMonths !== null ? getDevelopmentalStage($ageMonths) : 'Age not provided'),
        'advice'  => $webhookResponse['advice'] ?? '',
        'store_recommendation' => $webhookResponse['store_recommendation'] ?? ''
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

// 4. Fallback: If n8n workflow is currently paused or inactive, provide pediatric milestone advice
$stage = $ageMonths !== null ? getDevelopmentalStage($ageMonths) : 'Age not provided';
$fallbackAdvice = generateMilestoneAdvice($childName, $ageMonths, $stage, $query);
$fallbackGear = $ageMonths !== null ? generateStoreRecommendation($ageMonths, $stage) : '';

echo json_encode([
    'success' => true,
    'source'  => 'parentinghub_expert_engine',
    'status'  => 'active',
    'child_name' => $childName,
    'age_months' => $ageMonths,
    'stage'   => $stage,
    'advice'  => $fallbackAdvice,
    'store_recommendation' => $fallbackGear,
    'safety_note' => 'General parenting information only; not a diagnosis or substitute for a qualified pediatrician.'
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;

// ---------------- Helper Functions ----------------
function getDevelopmentalStage(int $months): string {
    if ($months <= 3) return 'Newborn & Fourth Trimester (0-3 Months)';
    if ($months <= 6) return 'Sensory Explorer & Rolling (4-6 Months)';
    if ($months <= 9) return 'Sitting & Weaning Stage (7-9 Months)';
    if ($months <= 12) return 'Crawling & First Words (10-12 Months)';
    if ($months <= 24) return 'Toddler Explorer & Walking (1-2 Years)';
    return 'Preschool & Active Motor Skills (2+ Years)';
}

function generateMilestoneAdvice(string $name, ?int $months, string $stage, string $query): string {
    if ($months === null) {
        return "I can share general parenting and development information, but I do not have your child's age. Include the age in months for more age-specific guidance. If you are worried about illness, development, feeding safety, medication, or an urgent symptom, contact a qualified clinician.";
    }

    if ($months <= 3) {
        $advice = "At around {$months} months, development varies widely. Helpful everyday activities include supervised tummy time while awake, talking and responding to your baby, and following your baby's feeding and sleep cues.";
    } elseif ($months <= 6) {
        $advice = "At around {$months} months, many babies are building head control, reaching and grasping, rolling, and becoming more socially responsive. Floor play, talking, reading, and supervised movement practice can support development.";
    } elseif ($months <= 9) {
        $advice = "At around {$months} months, many babies are working on sitting, transferring objects between hands, babbling, and exploring food textures when developmentally ready. During meals, keep your baby upright and closely supervised and use age-appropriate textures.";
    } elseif ($months <= 12) {
        $advice = "At around {$months} months, many babies are becoming more mobile, using a pincer grasp, responding to familiar words, and practicing self-feeding skills. Offer safe opportunities for floor play, interaction, and supervised meals.";
    } elseif ($months <= 24) {
        $advice = "During the second year, children often make rapid gains in walking, language, imitation, and self-feeding. Encourage safe movement, simple choices, reading, conversation, and predictable routines.";
    } else {
        $advice = "At this stage, active play, conversation, reading, predictable routines, and age-appropriate independence can support development. Children develop at different rates, so focus on progress over time rather than comparing one child with another.";
    }

    $q = strtolower($query);
    if (str_contains($q, 'solid') || str_contains($q, 'food') || str_contains($q, 'wean')) {
        $advice .= "\n\nFor solids, look for developmental readiness, keep the child upright and supervised, choose soft age-appropriate textures, and avoid foods that are obvious choking hazards. Introduce new foods calmly and discuss allergy concerns or feeding difficulties with your child's clinician.";
    } elseif (str_contains($q, 'sleep')) {
        $advice .= "\n\nFor sleep questions, use a consistent calming routine and follow current safe-sleep guidance for your child's age. If sleep changes are accompanied by breathing problems, unusual lethargy, fever, or other concerning symptoms, seek medical advice.";
    } elseif (str_contains($q, 'milestone') || str_contains($q, 'development') || str_contains($q, 'crawl') || str_contains($q, 'walk') || str_contains($q, 'talk')) {
        $advice .= "\n\nMilestone timing has a normal range. If your child loses a skill they previously had, or you have a persistent concern about development, arrange an assessment with a qualified child-health professional.";
    }

    return $advice;
}

function generateStoreRecommendation(int $months, string $stage): string {
    if ($months <= 6) return "Age-appropriate feeding bottles, simple sensory toys, and supervised tummy-time play items may be useful depending on your family's routine.";
    if ($months <= 12) return "Age-appropriate feeding cups, high-chair accessories, and simple developmental play items may be useful depending on your child's skills and supervision needs.";
    return "Age-appropriate cups, feeding accessories, books, and supervised active-play items may be useful depending on your child's current skills.";
}
