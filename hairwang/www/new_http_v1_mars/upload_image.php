<?php
/**
 * 이미지 업로드 처리
 */
header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => ''];

// 업로드 디렉토리 설정
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// 파일 검증
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $response['message'] = '파일 업로드 오류';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

$file = $_FILES['image'];

// 파일 크기 체크 (2MB)
if ($file['size'] > 2 * 1024 * 1024) {
    $response['message'] = '파일 크기는 2MB 이하여야 합니다.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// 이미지 파일 체크
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    $response['message'] = '이미지 파일만 업로드 가능합니다.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// 파일명 생성 (중복 방지)
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'push_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
$filepath = $upload_dir . $filename;

// 파일 이동
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // URL 생성
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    $file_url = $protocol . "://" . $host . $path . '/uploads/' . $filename;
    
    $response['success'] = true;
    $response['message'] = '업로드 성공';
    $response['url'] = $file_url;
    $response['filename'] = $filename;
} else {
    $response['message'] = '파일 저장 실패';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);