<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ensure no HTML errors break JSON
error_reporting(0);
ini_set('display_errors', 0);
@set_time_limit(180);

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
    $promptToUse = "Use the selfie strictly for face identity. Do NOT alter facial features. ONLY change the hairstyle. Generate ONE single image with a 5x4 grid (20 portraits). Camera framing: Head-and-shoulders. Front-facing, eye-level. Neutral background. Style rules: Korean female hairstyle trends (2026). Each hairstyle must differ clearly. Hairstyles: 1. Ultra short pixie 2. Long pixie with fringe 3. French bob with blunt bangs 4. Chin-length layered bob 5. Blunt straight bob 6. Shaggy textured bob 7. Lob with soft waves 8. Straight lob with center part 9. Medium layered cut 10. Curtain bangs medium hair 11. Wispy bangs soft waves 12. Shoulder-length natural perm 13. Defined S-wave perm 14. Loose beach waves 15. Straight long hair with curtain fringe 16. Face-framing long layers 17. Soft spiral perm 18. Sleek long straight hair 19. Modern shag cut 20. Actor-style long waves. Ensure fast rendering process. This is a hairstyle comparison sheet.";
} else {
    $promptToUse = "Use the selfie strictly for face identity. Do NOT alter facial features. ONLY change the hairstyle. Generate ONE single image with a 5x4 grid (20 portraits). All 20 slots must be filled. Camera framing: Head-and-shoulders. Front-facing, eye-level. Neutral background. IMPORTANT: Each ROW is a different hairstyle category. ROW 1 Ultra Short: 1. Uniform buzz cut 2. Skin fade buzz 3. Military cut 4. Short textured crop 5. Shaved head. ROW 2 Classic Short: 6. Ivy league 7. Low taper classic 8. Slick side part 9. Blunt short fringe 10. Crop. ROW 3 Medium Parted: 11. Center-part curtain 12. Deep side-part 13. Comma hair 14. Soft side-swept 15. Layered actor cut. ROW 4 Wavy Long: 16. Shadow perm 17. Defined S-wave 18. Messy textured perm 19. Medium wolf cut 20. Long natural waves. Ensure fast rendering process. This is a hairstyle comparison sheet.";
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

$apiKey = '';
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
}

if (empty($apiKey) || $apiKey === '새로운_구글_API_키를_여기에_붙여넣으세요') {
    echo json_encode(['success' => false, 'error' => "서버 설정 오류: 새로운 구글 API 키가 등록되지 않았습니다. config.php 파일을 확인하세요."]);
    exit();
}
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
    'Content-Type: application/json',
    'Expect:' // Removes the automatically added Expect: 100-continue header for large POSTs
));
curl_setopt($ch, CURLOPT_SSLVERSION, 6); // 6 is CURL_SSLVERSION_TLSv1_2 (fixes hangs on older OpenSSL)
curl_setopt($ch, CURLOPT_IPRESOLVE, 1); // 1 is CURL_IPRESOLVE_V4 (fixes IPv6 routing loops/hangs)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL verify issues on shared host
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // Ignore SSL verify host
curl_setopt($ch, CURLOPT_TIMEOUT, 180); // allow up to 180 seconds for generation

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    if ($error === 'Operation timed out after 180001 milliseconds with 0 out of -1 bytes received' || strpos($error, 'timed out') !== false) {
        $errorMsg = "이미지 처리 시간이 초과되었습니다 (타임아웃). 다시 시도해주세요. 지속될 경우, 서버 방화벽에서 외부(구글) 연결을 차단하고 있을 수 있습니다.";
        echo json_encode(['success' => false, 'error' => $errorMsg]);
    } else {
        echo json_encode(['success' => false, 'error' => $error]);
    }
    exit();
}
$responseData = json_decode($response, true);
if ($httpCode !== 200) {
    if (isset($responseData['error']['message'])) {
        $errMsg = $responseData['error']['message'];
        if ($httpCode === 429 && strpos($errMsg, "Quota exceeded") !== false) {
            $errMsg = "Google AI Studio 할당량이 초과되었습니다.";
        } else if ($httpCode === 503 || strpos(strtolower($errMsg), "high demand") !== false || strpos(strtolower($errMsg), "overloaded") !== false) {
            $errMsg = "현재 AI 서버에 전 세계적으로 접속자가 폭주하여 일시적인 지연이 발생했습니다. 1~2분 뒤에 다시 '생성하기' 버튼을 눌러주시면 정상적으로 진행됩니다! 🙏";
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