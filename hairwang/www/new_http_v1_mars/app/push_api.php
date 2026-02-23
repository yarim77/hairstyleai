<?php
/**
 * 푸시 알림 API
 * 
 * @version 2.2.0
 * @updated 2025-01-26 - 다중 토큰 지원 개선
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/push_api_errors.log');

header('Content-Type: application/json; charset=utf-8');

// 로그 디렉토리 생성
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// 디버그 로그 함수
function debug_log($message) {
    $log_file = __DIR__ . '/logs/push_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

debug_log("=== 새 요청 시작 ===");
debug_log("POST 데이터: " . print_r($_POST, true));
debug_log("REQUEST 데이터: " . print_r($_REQUEST, true));

// GET 또는 POST 요청 파라미터 처리
$push_type = isset($_REQUEST['push_type']) ? $_REQUEST['push_type'] : '';

// 앱 유형 처리 - 단일 값 우선
$app = '';
if (isset($_REQUEST['app'])) {
    if (is_array($_REQUEST['app'])) {
        // 배열인 경우 첫 번째 값 사용
        $app = !empty($_REQUEST['app']) ? $_REQUEST['app'][0] : '';
    } else {
        $app = $_REQUEST['app'];
    }
}
$app = trim(strtolower($app));

debug_log("앱 유형: " . $app);

$user_token = isset($_REQUEST['user_token']) ? $_REQUEST['user_token'] : '';
$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
$message = isset($_REQUEST['message']) ? $_REQUEST['message'] : '';
$memo = isset($_REQUEST['memo']) ? $_REQUEST['memo'] : '';
$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
$file_url = isset($_REQUEST['file_url']) ? $_REQUEST['file_url'] : '';
$image_url = isset($_REQUEST['image_url']) ? $_REQUEST['image_url'] : '';
$topic = isset($_REQUEST['topic']) ? $_REQUEST['topic'] : '';
$target_devices = isset($_REQUEST['target_devices']) ? $_REQUEST['target_devices'] : [];

$order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';
$arg1 = isset($_REQUEST['arg1']) ? $_REQUEST['arg1'] : '$arg1';
$arg2 = isset($_REQUEST['arg2']) ? $_REQUEST['arg2'] : '1';
$arg3 = isset($_REQUEST['arg3']) ? $_REQUEST['arg3'] : 'MARS';

if (empty($memo) && !empty($message)) {
    $memo = $message;
}

if (empty($file_url) && !empty($image_url)) {
    $file_url = $image_url;
}

debug_log("제목: $title, 메모: $memo");

// 푸시 타입에 따른 처리
if ($push_type === 'group') {
    // 전체 푸시 처리
    if (empty($title) || empty($memo)) {
        $response['message'] = '필수 파라미터가 누락되었습니다. title, memo는 필수 입력 항목입니다.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    debug_log('Topic push request - Topic: news, Title: ' . $title . ', Memo: ' . $memo);
    
    $topic_name = 'news_test';
    
    $result = send_topic_push($topic_name, $title, $memo, $url, $file_url, '', '$arg1', '1', 'MARS');
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = '전체 푸시 알림이 성공적으로 전송되었습니다.';
        $response['data'] = [
            'topic' => $topic_name,
            'title' => $title,
            'memo' => $memo,
            'url' => $url,
            'file_url' => $file_url
        ];
    } else {
        $response['message'] = '전체 푸시 알림 전송에 실패했습니다.';
        $response['data'] = [
            'debug_info' => '클라이언트 앱에서 "news" 토픽을 구독했는지 확인하세요.'
        ];
    }
}
else if (!empty($topic)) {
    // 토픽 푸시 처리
    if (empty($title) && empty($memo)) {
        $response['message'] = '필수 파라미터가 누락되었습니다. topic, title, memo 중 하나는 필수 입력 항목입니다.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $result = send_topic_push($topic, $title, $memo, $url, $file_url, $order_id, $arg1, $arg2, $arg3);

    if ($result) {
        $response['success'] = true;
        $response['message'] = '푸시 알림이 성공적으로 전송되었습니다.';
        $response['data'] = [
            'topic' => $topic,
            'title' => $title,
            'memo' => $memo
        ];
    } else {
        $response['message'] = '푸시 알림 전송에 실패했습니다.';
    }
}
else {
    // ===================================================
    // 개별 푸시 발송
    // ===================================================
    
    debug_log("개별 푸시 처리 시작");
    debug_log("app: '$app', user_token 길이: " . strlen($user_token) . ", title: '$title', memo: '$memo'");
    
    // 필수 파라미터 검증
    if (empty($app)) {
        $response['message'] = '앱 유형이 지정되지 않았습니다.';
        $response['data'] = ['received_app' => $_REQUEST['app'] ?? 'null'];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if ($app !== 'android' && $app !== 'ios') {
        $response['message'] = '앱 유형은 android 또는 ios만 가능합니다.';
        $response['data'] = ['received_app' => $app];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if (empty($user_token)) {
        $response['message'] = '사용자 토큰이 필요합니다.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if (empty($title) || empty($memo)) {
        $response['message'] = '제목과 내용이 필요합니다.';
        $response['data'] = ['title' => $title, 'memo' => $memo];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // 토큰이 여러 개인 경우 줄바꿈으로 분리
    $raw_tokens = $user_token;
    
    // 다양한 줄바꿈 형식 처리
    $raw_tokens = str_replace("\r\n", "\n", $raw_tokens);
    $raw_tokens = str_replace("\r", "\n", $raw_tokens);
    
    $token_array = explode("\n", $raw_tokens);
    $tokens = [];
    
    foreach ($token_array as $t) {
        $t = trim($t);
        if (!empty($t) && strlen($t) > 20) { // FCM 토큰은 최소 20자 이상
            $tokens[] = $t;
        }
    }
    
    debug_log("토큰 파싱 결과: " . count($tokens) . "개");
    foreach ($tokens as $idx => $t) {
        debug_log("  토큰[$idx]: " . substr($t, 0, 50) . "... (길이: " . strlen($t) . ")");
    }
    
    if (empty($tokens)) {
        $response['message'] = '유효한 토큰이 없습니다.';
        $response['data'] = [
            'raw_token_length' => strlen($user_token),
            'parsed_count' => 0
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $success_count = 0;
    $fail_count = 0;
    $results = [];
    
    // 각 토큰에 대해 개별 전송
    foreach ($tokens as $index => $single_token) {
        debug_log("[$app] 토큰 #$index 전송 시도");
        
        $result = send_push_v1_jwt(
            $app, 
            $single_token, 
            $title, 
            $memo, 
            $url, 
            $file_url, 
            $order_id, 
            $arg1, 
            $arg2, 
            $arg3
        );
        
        if ($result === true) {
            $success_count++;
            $results[] = [
                'index' => $index,
                'token' => substr($single_token, 0, 20) . '...', 
                'status' => 'success'
            ];
            debug_log("[$app] 토큰 #$index 전송 성공");
        } else {
            $fail_count++;
            $results[] = [
                'index' => $index,
                'token' => substr($single_token, 0, 20) . '...', 
                'status' => 'failed',
                'error' => $result
            ];
            debug_log("[$app] 토큰 #$index 전송 실패: $result");
        }
    }
    
    debug_log("전송 완료 - 성공: $success_count, 실패: $fail_count");
    
    // 결과 처리
    if ($success_count > 0) {
        $response['success'] = true;
        $response['message'] = "푸시 알림 전송 완료 - 성공: {$success_count}개" . ($fail_count > 0 ? ", 실패: {$fail_count}개" : "");
        $response['data'] = [
            'app' => $app,
            'total_tokens' => count($tokens),
            'success_count' => $success_count,
            'fail_count' => $fail_count,
            'results' => $results
        ];
    } else {
        $response['message'] = "푸시 알림 전송에 실패했습니다.";
        $response['data'] = [
            'app' => $app,
            'total_tokens' => count($tokens),
            'success_count' => 0,
            'fail_count' => $fail_count,
            'results' => $results,
            'debug_hint' => 'FCM 토큰이 유효한지, Firebase 설정이 올바른지 확인하세요.'
        ];
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;


/**
 * FCM V1 API를 사용한 푸시 전송
 */
function send_push_v1_jwt($app, $user_token, $title, $memo, $url, $file_url = '', $order_id = '', $arg1 = '$arg1', $arg2 = '1', $arg3 = 'MARS') {
    
    debug_log("send_push_v1_jwt 호출 - app: $app, token: " . substr($user_token, 0, 30) . "...");
    
    if ($url === null || $url == "" || $url == "http://" || $url == "https://") {
        $url = "https://edumars.net";
    }

    $fcm_url = 'https://fcm.googleapis.com/v1/projects/mars-38372/messages:send';
    $serviceAccountPath = __DIR__ . '/mars-38372-firebase-adminsdk-60l5a-23f211e854.json';

    if (!file_exists($serviceAccountPath)) {
        debug_log("Error: Service account file not found: " . $serviceAccountPath);
        return "Service account file not found";
    }

    $accessToken = getAccessToken($serviceAccountPath);
    
    if (empty($accessToken)) {
        debug_log("Error: Failed to get access token");
        return "Failed to get access token";
    }
    
    debug_log("Access token 획득 성공");

    if ($app == 'android') {
        $pushdata = [
            'message' => [
                'token' => $user_token,
                'data' => [
                    'title' => $title,
                    'message' => $memo,
                    'order_id' => (string)$order_id,
                    'arg1' => (string)$arg1,
                    'arg2' => (string)$arg2,
                    'arg3' => (string)$arg3,
                    'url' => $url,
                    'param_intent_url' => $url
                ]
            ]
        ];
        
        if (!empty($file_url)) {
            $pushdata['message']['data']['file_url'] = $file_url;
        }
    }
    else if ($app == 'ios') {
        $pushdata = [
            'message' => [
                'token' => $user_token,
                'notification' => [
                    'title' => $title,
                    'body' => $memo,
                ],
                'data' => [
                    'order_id' => (string)$order_id,
                    'arg1' => (string)$arg1,
                    'arg2' => (string)$arg2,
                    'arg3' => (string)$arg3,
                    'url' => $url,
                    'param_intent_url' => $url
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default'
                        ]
                    ]
                ]
            ]
        ];
        
        if (!empty($file_url)) {
            $pushdata['message']['data']['file_url'] = $file_url;
        }
    } else {
        debug_log("Error: Invalid app type: " . $app);
        return "Invalid app type: " . $app;
    }

    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    debug_log("FCM 요청 전송 중...");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fcm_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pushdata));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $fcm_response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        debug_log("cURL Error: " . $error);
        curl_close($ch);
        return "cURL Error: " . $error;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    debug_log("FCM 응답 (HTTP $http_code): " . $fcm_response);

    $obj = json_decode($fcm_response, true);
    
    if (!$obj) {
        debug_log("Error: Invalid JSON response");
        return "Invalid JSON response from FCM";
    }
    
    if (isset($obj['error'])) {
        $error_msg = isset($obj['error']['message']) ? $obj['error']['message'] : json_encode($obj['error']);
        debug_log("FCM Error: " . $error_msg);
        return "FCM Error: " . $error_msg;
    }
    
    if (!isset($obj['name'])) {
        debug_log("Error: No 'name' in response");
        return "Unexpected FCM response";
    }

    debug_log("FCM 전송 성공: " . $obj['name']);
    return true;
}


/**
 * 토픽 푸시 전송
 */
function send_topic_push($topic, $title, $memo, $url, $file_url = '', $order_id = '', $arg1 = '$arg1', $arg2 = '1', $arg3 = 'MARS') {
    if ($url === null || $url == "" || $url == "http://" || $url == "https://") {
        $url = "https://edumars.net";
    }

    $fcm_url = 'https://fcm.googleapis.com/v1/projects/mars-38372/messages:send';
    $serviceAccountPath = __DIR__ . '/mars-38372-firebase-adminsdk-60l5a-23f211e854.json';

    if (!file_exists($serviceAccountPath)) {
        debug_log("Error: Service account file not found: " . $serviceAccountPath);
        return false;
    }

    $accessToken = getAccessToken($serviceAccountPath);
    
    if (empty($accessToken)) {
        debug_log("Error: Failed to get access token");
        return false;
    }

    $pushdata = [
        'message' => [
            'topic' => $topic,
            'notification' => [
                'title' => $title,
                'body' => $memo
            ],
            'data' => [
                'title' => $title,
                'message' => $memo,
                'arg1' => (string)$arg1,
                'arg2' => (string)$arg2,
                'arg3' => (string)$arg3,
                'url' => $url,
            ],
            'android' => [
                'priority' => 'high'
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'sound' => 'default'
                    ]
                ]
            ]
        ]
    ];
    
    if (!empty($file_url)) {
        $pushdata['message']['data']['file_url'] = $file_url;
    }

    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fcm_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pushdata));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        debug_log("cURL Error: " . $error);
        curl_close($ch);
        return false;
    }

    curl_close($ch);

    debug_log("Topic push response: " . $response);

    $obj = json_decode($response, true);
    
    if (!$obj || !isset($obj['name'])) {
        debug_log("Error: Invalid topic push response");
        return false;
    }

    return true;
}


/**
 * Google OAuth2 액세스 토큰 획득
 */
function getAccessToken($serviceAccountPath) {
    if (!file_exists($serviceAccountPath)) {
        debug_log("getAccessToken: File not found");
        return '';
    }

    $jsonContent = file_get_contents($serviceAccountPath);
    if ($jsonContent === false) {
        debug_log("getAccessToken: Failed to read file");
        return '';
    }

    $jwt = json_decode($jsonContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        debug_log("getAccessToken: JSON parse error");
        return '';
    }

    $now = time();
    $token = [
        'iss' => $jwt['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => $jwt['token_uri'],
        'exp' => $now + 3600,
        'iat' => $now
    ];

    $header = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
    $payload = rtrim(strtr(base64_encode(json_encode($token)), '+/', '-_'), '=');
    $signature = '';

    $privateKey = $jwt['private_key'];

    if (!openssl_sign("$header.$payload", $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
        debug_log("getAccessToken: openssl_sign failed");
        return '';
    }

    $signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
    $jwtToken = "$header.$payload.$signature";

    $ch = curl_init($jwt['token_uri']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwtToken
    ]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        debug_log("getAccessToken: cURL error - " . curl_error($ch));
        curl_close($ch);
        return '';
    }

    curl_close($ch);
    $data = json_decode($response, true);

    if (!isset($data['access_token'])) {
        debug_log("getAccessToken: No access_token in response - " . $response);
        return '';
    }

    return $data['access_token'];
}
?>