<?php
/**
 * ========================================
 * 회원 목록 조회 API (FCM 토큰 포함)
 * ========================================
 * 그누보드 리빌더 회원 테이블 + 별도 푸시 토큰 테이블 JOIN
 * 
 * @file    get_members.php
 * @version 2.0.0
 * @updated 2025-01-26
 */

header('Content-Type: application/json; charset=utf-8');

// ============================================
// 그누보드 공통 파일 로드 - 경로 자동 탐색
// ============================================
$common_paths = [
    __DIR__ . '/../common.php',
    __DIR__ . '/../../common.php',
    __DIR__ . '/../../../common.php',
    $_SERVER['DOCUMENT_ROOT'] . '/common.php',
    $_SERVER['DOCUMENT_ROOT'] . '/g5/common.php',
];

$common_loaded = false;
$loaded_path = '';

foreach ($common_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $common_loaded = true;
        $loaded_path = $path;
        break;
    }
}

if (!$common_loaded) {
    echo json_encode([
        'success' => false, 
        'message' => 'common.php를 찾을 수 없습니다.',
        'debug' => [
            'tried_paths' => $common_paths,
            'current_dir' => __DIR__,
            'document_root' => $_SERVER['DOCUMENT_ROOT']
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================
// 회원등급 설정
// ============================================
$level_names = array(
    1 => '헤린이',
    2 => '루키 스타',
    3 => '슈퍼 스타',
    4 => '빡고수',
    5 => '신의손',
    6 => '특별회원',
    7 => '명예회원',
    8 => '골드회원',
    9 => '다이아회원',
    10 => '최고관리자'
);

// ============================================
// 필터 파라미터 수신
// ============================================
$filter_name = isset($_GET['name']) ? trim($_GET['name']) : '';
$filter_email = isset($_GET['email']) ? trim($_GET['email']) : '';
$filter_id = isset($_GET['mb_id']) ? trim($_GET['mb_id']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$filter_level = isset($_GET['level']) ? trim($_GET['level']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? min(100, max(10, intval($_GET['limit']))) : 50;
$offset = ($page - 1) * $limit;

try {
    global $g5;
    
    // DB 연결 확인
    if (!isset($g5) || !isset($g5['member_table'])) {
        echo json_encode([
            'success' => false,
            'message' => '그누보드 설정을 불러올 수 없습니다.',
            'debug' => [
                'g5_exists' => isset($g5),
                'loaded_path' => $loaded_path,
                'member_table' => isset($g5['member_table']) ? $g5['member_table'] : 'not set'
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ============================================
    // WHERE 조건 구성
    // ============================================
    $where = ["1=1"];
    
    // 기본: 탈퇴하지 않은 회원만 (상태 필터가 없는 경우)
    if (empty($filter_status)) {
        $where[] = "m.mb_leave_date = ''";
    }
    
    // 이름 필터
    if ($filter_name) {
        $filter_name = sql_real_escape_string($filter_name);
        $where[] = "(m.mb_name LIKE '%{$filter_name}%' OR m.mb_nick LIKE '%{$filter_name}%')";
    }
    
    // 이메일 필터
    if ($filter_email) {
        $filter_email = sql_real_escape_string($filter_email);
        $where[] = "m.mb_email LIKE '%{$filter_email}%'";
    }
    
    // 아이디 필터
    if ($filter_id) {
        $filter_id = sql_real_escape_string($filter_id);
        $where[] = "m.mb_id LIKE '%{$filter_id}%'";
    }
    
    // 상태 필터
    if ($filter_status) {
        switch ($filter_status) {
            case 'active':
                $where[] = "m.mb_leave_date = '' AND m.mb_intercept_date = ''";
                break;
            case 'inactive':
                $where[] = "m.mb_leave_date != ''";
                break;
            case 'blocked':
                $where[] = "m.mb_intercept_date != ''";
                break;
        }
    }
    
    // 회원등급 필터
    if ($filter_level !== '') {
        $filter_level = intval($filter_level);
        $where[] = "m.mb_level = {$filter_level}";
    }
    
    $where_sql = implode(' AND ', $where);
    
    // ============================================
    // 전체 카운트 조회
    // ============================================
    $count_sql = "SELECT COUNT(DISTINCT m.mb_id) as total 
                  FROM {$g5['member_table']} m 
                  WHERE {$where_sql}";
    $count_result = sql_fetch($count_sql);
    $total = $count_result['total'];
    
    // ============================================
    // 회원 목록 조회 (단순 버전)
    // ============================================
    $sql = "SELECT 
                m.mb_id, 
                m.mb_name, 
                m.mb_nick, 
                m.mb_email, 
                m.mb_hp,
                m.mb_level, 
                m.mb_datetime, 
                m.mb_today_login,
                m.mb_leave_date, 
                m.mb_intercept_date, 
                m.mb_point
            FROM {$g5['member_table']} m
            WHERE {$where_sql}
            ORDER BY m.mb_datetime DESC
            LIMIT {$offset}, {$limit}";
    
    $result = sql_query($sql);
    
    $members = [];
    while ($row = sql_fetch_array($result)) {
        // 상태 결정
        $status = 'active';
        if (!empty($row['mb_leave_date'])) {
            $status = 'inactive';
        } elseif (!empty($row['mb_intercept_date'])) {
            $status = 'blocked';
        }
        
        // 해당 회원의 FCM 토큰 조회
        $token_sql = "SELECT pt_platform, pt_token 
                      FROM g5_push_tokens 
                      WHERE mb_id = '{$row['mb_id']}' 
                      AND pt_use = '1' 
                      ORDER BY pt_datetime DESC";
        $token_result = sql_query($token_sql);
        
        $tokens = [];
        $token_string = '';
        
        while ($token_row = sql_fetch_array($token_result)) {
            $tokens[] = [
                'platform' => $token_row['pt_platform'],
                'token' => $token_row['pt_token']
            ];
            // textarea에 표시할 토큰 문자열 (줄바꿈으로 구분)
            $token_string .= $token_row['pt_token'] . "\n";
        }
        
        $members[] = [
            'mb_id' => $row['mb_id'],
            'mb_name' => $row['mb_name'],
            'mb_nick' => $row['mb_nick'],
            'mb_email' => $row['mb_email'],
            'mb_hp' => $row['mb_hp'],
            'mb_level' => intval($row['mb_level']),
            'mb_level_name' => isset($level_names[$row['mb_level']]) ? $level_names[$row['mb_level']] : '',
            'mb_datetime' => substr($row['mb_datetime'], 0, 10),
            'mb_point' => number_format($row['mb_point']),
            'status' => $status,
            'fcm_token' => rtrim($token_string),  // 마지막 줄바꿈 제거
            'fcm_tokens' => $tokens  // 상세 토큰 정보 (플랫폼별)
        ];
    }
    
    // ============================================
    // 등급별 회원 수 조회
    // ============================================
    $level_counts = [];
    for ($i = 1; $i <= 10; $i++) {
        $level_count_sql = "SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_leave_date = '' AND mb_level = {$i}";
        $level_count_result = sql_fetch($level_count_sql);
        $level_counts[] = [
            'level' => $i,
            'name' => $level_names[$i],
            'count' => intval($level_count_result['cnt'])
        ];
    }
    
    // ============================================
    // 성공 응답
    // ============================================
    echo json_encode([
        'success' => true,
        'members' => $members,
        'total' => intval($total),
        'page' => $page,
        'limit' => $limit,
        'level_names' => $level_names,
        'level_counts' => $level_counts,
        'token_table' => 'g5_push_tokens'  // 디버깅용
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '회원 조회 중 오류가 발생했습니다: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}