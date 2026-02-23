<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Enable error reporting to catch unexpected syntax/execution issues on the live server
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Custom error handler to ensure we always output JSON even on fatal PHP errors.
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (error_reporting() === 0)
        return false;
    echo json_encode(['success' => false, 'error' => "PHP Error [$errno]: $errstr in $errfile on line $errline"]);
    exit();
});

set_exception_handler(function ($exception) {
    echo json_encode(['success' => false, 'error' => "PHP Exception: " . $exception->getMessage()]);
    exit();
});

$data = [];
$rawInput = file_get_contents('php://input');

// Support both JSON bodies and traditional x-www-form-urlencoded POST
if (!empty($rawInput)) {
    $decoded = json_decode($rawInput, true);
    if ($decoded && is_array($decoded)) {
        $data = $decoded;
    }
}

if (isset($_POST['image']) && isset($_POST['gender'])) {
    $data['image'] = $_POST['image'];
    $data['gender'] = $_POST['gender'];
}

if (empty($data) || !isset($data['image']) || !isset($data['gender'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit();
}

$promptToUse = '';
if ($data['gender'] === 'female') {
    $promptToUse = "Use the uploaded selfie as a strict identity reference. The face must remain exactly identical. Do NOT alter facial features. ONLY change the hairstyle. Generate ONE image with a 5x4 grid (20 portraits). Camera framing: Head-and-shoulders only. Cropped at the shoulders. No arms, no torso. Front-facing, eye-level. Neutral background, soft studio lighting. Style rules: Ultra-realistic Korean female hairstyle trends (2026). Each hairstyle must differ clearly in length, bangs, parting, or texture. Hairstyles: 1. Ultra short pixie 2. Long pixie with fringe 3. French bob with blunt bangs 4. Chin-length layered bob 5. Blunt straight bob 6. Shaggy textured bob 7. Lob with soft waves 8. Straight lob with center part 9. Medium layered cut (no bangs) 10. Curtain bangs with medium hair 11. Wispy bangs with soft waves 12. Shoulder-length natural perm 13. Defined S-wave perm 14. Loose beach waves 15. Straight long hair with curtain fringe 16. Face-framing long layers 17. Soft spiral perm (long hair) 18. Sleek long straight hair 19. Modern shag cut 20. Actor-style natural long waves. Quality: High resolution. Realistic hair texture. No beauty filter. No cartoon or AI artifacts. This is a hairstyle comparison sheet.";
} else {
    $promptToUse = "Use the uploaded selfie as a strict identity reference. The face must remain exactly identical. Do NOT alter facial features. ONLY change the hairstyle. Generate ONE image with a 5x4 grid (20 portraits). Do NOT reduce the number of portraits. All 20 slots must be filled. Camera framing: Head-and-shoulders only. Cropped at the shoulders. No arms, no torso. Front-facing, eye-level. Neutral background, soft studio lighting. IMPORTANT STRUCTURE: Each ROW is a different hairstyle category. Never merge, skip, or remove a slot. If two styles look similar, exaggerate differences in length, parting, or texture. ROW 1 Ultra Short or Clean: 1. Uniform buzz cut 2. Skin fade buzz cut 3. Clean military cut 4. Ultra short textured crop 5. Shaved head. ROW 2 Classic Short: 6. Ivy league with hard side part 7. Low taper classic cut 8. Slick side part 9. Blunt short fringe 10. Short textured crop. ROW 3 Medium Parted: 11. Center-part curtain hair 12. Deep side-part medium hair 13. Comma hair 14. Soft side-swept medium cut 15. Medium layered actor cut. ROW 4 Wavy Long: 16. Shadow perm 17. Defined S-wave perm 18. Messy textured perm 19. Medium wolf cut 20. Long natural waves. Quality: High resolution. Realistic hair texture. No cartoon or AI artifacts. This is a hairstyle comparison sheet.";
}

// base64_image string format: "data:image/jpeg;base64,..."
$base64ImageStr = $data['image'];
$parts = explode(',', $base64ImageStr);
if (count($parts) < 2) {
    echo json_encode(['success' => false, 'error' => 'Invalid image format']);
    exit();
}

$base64Data = $parts[1];
$mimeType = 'image/jpeg';
if (preg_match('/^data:(image\/[a-zA-Z]+);base64/', $base64ImageStr, $matches)) {
    $mimeType = $matches[1];
}

$apiKey = 'AIzaSyC6DllJ0rpRR_YGgOu5I4RBJKMPgFAbsfg'; // Google AI API Key

$requestBody = [
    'contents' => [
        [
            'parts' => [
                ['text' => $promptToUse],
                [
                    'inlineData' => [
                        'mimeType' => $mimeType,
                        'data' => $base64Data
                    ]
                ]
            ]
        ]
    ]
];

// PHP json_encode can sometimes create escaped characters that Google's strict regex rejects.
// Using JSON_UNESCAPED_UNICODE and JSON_UNESCAPED_SLASHES ensures clean output.
$jsonBody = json_encode($requestBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($jsonBody === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to encode request body: ' . json_last_error_msg()]);
    exit();
}

$ch = curl_init();
$url = "https://generativelanguage.googleapis.com/v1beta/models/nano-banana-pro-preview:generateContent?key=" . $apiKey;
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_TIMEOUT, 120); // allow up to 120 seconds for generation

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(['success' => false, 'error' => $error]);
    exit();
}

$responseData = json_decode($response, true);
if ($httpCode !== 200) {
    if (isset($responseData['error']['message'])) {
        $errMsg = $responseData['error']['message'];
        if ($httpCode === 429 && strpos($errMsg, "Quota exceeded") !== false) {
            $errMsg = "Google AI Studio 할당량이 초과되었습니다.";
        }
        echo json_encode(['success' => false, 'error' => $errMsg, 'httpCode' => $httpCode]);
    } else {
        echo json_encode(['success' => false, 'error' => 'API Error: ' . $httpCode]);
    }
    exit();
}

$generatedImageUrl = null;

if (isset($responseData['candidates']) && count($responseData['candidates']) > 0) {
    $candidate = $responseData['candidates'][0];

    if (isset($candidate['finishReason']) && ($candidate['finishReason'] === "SAFETY" || $candidate['finishReason'] === "RECITATION")) {
        echo json_encode(['success' => false, 'error' => "AI가 보안 정책({$candidate['finishReason']})에 의해 사진 분석을 거부했습니다. 너무 노출이 있거나 규정에 어긋나는 사진일 수 있습니다."]);
        exit();
    }

    if (isset($candidate['content']['parts'])) {
        foreach ($candidate['content']['parts'] as $part) {
            if (isset($part['inlineData']['data'])) {
                $outMime = isset($part['inlineData']['mimeType']) ? $part['inlineData']['mimeType'] : 'image/png';
                $generatedImageUrl = "data:{$outMime};base64," . $part['inlineData']['data'];
                break;
            } else if (isset($part['text'])) {
                $cleanJsonStr = trim(str_replace(['```json', '```'], '', $part['text']));
                $parsedResult = json_decode($cleanJsonStr, true);
                if ($parsedResult && (isset($parsedResult['image_url']) || isset($parsedResult['url']))) {
                    $generatedImageUrl = isset($parsedResult['image_url']) ? $parsedResult['image_url'] : $parsedResult['url'];
                    break;
                } else {
                    preg_match('/https?:\/\/[^\s"\']+/', $part['text'], $urlMatch);
                    if ($urlMatch) {
                        $generatedImageUrl = $urlMatch[0];
                        break;
                    }
                }
                echo json_encode(['success' => false, 'error' => "AI의 텍스트 거절 메시지: " . $part['text']]);
                exit();
            }
        }
    }
} else if (isset($responseData['promptFeedback']['blockReason'])) {
    echo json_encode(['success' => false, 'error' => "AI가 보안 정책에 의해 사진을 거절했습니다: " . $responseData['promptFeedback']['blockReason']]);
    exit();
}

if ($generatedImageUrl) {
    echo json_encode(['success' => true, 'image_url' => $generatedImageUrl]);
} else {
    echo json_encode(['success' => false, 'error' => 'API 응답에서 이미지 URL을 찾을 수 없습니다.', 'debug' => substr(json_encode($responseData), 0, 500)]);
}
?>