<?php
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/push_history.log');

$common_paths = [
    __DIR__ . '/../common.php',
    __DIR__ . '/../../common.php',
    __DIR__ . '/../../../common.php',
    $_SERVER['DOCUMENT_ROOT'] . '/common.php',
];

$common_loaded = false;
foreach ($common_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $common_loaded = true;
        break;
    }
}

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

if (!$common_loaded) {
    echo json_encode(['success' => false, 'message' => 'common.php를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'list';

try {
    switch ($action) {
        case 'save':
            $type = isset($_POST['type']) ? trim($_POST['type']) : 'individual';
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $message = isset($_POST['message']) ? trim($_POST['message']) : '';
            $url = isset($_POST['url']) ? trim($_POST['url']) : '';
            $image_url = isset($_POST['image_url']) ? trim($_POST['image_url']) : '';
            $target_count = isset($_POST['target_count']) ? intval($_POST['target_count']) : 0;
            $success_count = isset($_POST['success_count']) ? intval($_POST['success_count']) : 0;
            $fail_count = isset($_POST['fail_count']) ? intval($_POST['fail_count']) : 0;
            $target_members = isset($_POST['target_members']) ? $_POST['target_members'] : '[]';
            $results = isset($_POST['results']) ? $_POST['results'] : '[]';
            $sender = isset($_POST['sender']) ? trim($_POST['sender']) : '';

            if (empty($title)) {
                echo json_encode(['success' => false, 'message' => '제목이 필요합니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $sql = "INSERT INTO g5_push_logs 
                    (pl_type, pl_title, pl_message, pl_url, pl_image_url, pl_target_count, pl_success_count, pl_fail_count, pl_target_members, pl_results, pl_sender, pl_datetime) 
                    VALUES 
                    ('" . sql_real_escape_string($type) . "', 
                     '" . sql_real_escape_string($title) . "', 
                     '" . sql_real_escape_string($message) . "', 
                     '" . sql_real_escape_string($url) . "', 
                     '" . sql_real_escape_string($image_url) . "', 
                     {$target_count}, 
                     {$success_count}, 
                     {$fail_count}, 
                     '" . sql_real_escape_string($target_members) . "', 
                     '" . sql_real_escape_string($results) . "', 
                     '" . sql_real_escape_string($sender) . "', 
                     NOW())";
            
            sql_query($sql);
            $insert_id = sql_insert_id();
            
            if ($insert_id) {
                echo json_encode(['success' => true, 'message' => '저장 완료', 'id' => $insert_id], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'message' => '저장 실패'], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'list':
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;
            
            $type = isset($_GET['type']) ? trim($_GET['type']) : '';
            $date_start = isset($_GET['date_start']) ? trim($_GET['date_start']) : '';
            $date_end = isset($_GET['date_end']) ? trim($_GET['date_end']) : '';
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            $where = ['1=1'];
            
            if ($type) {
                $where[] = "pl_type = '" . sql_real_escape_string($type) . "'";
            }
            if ($date_start) {
                $where[] = "DATE(pl_datetime) >= '" . sql_real_escape_string($date_start) . "'";
            }
            if ($date_end) {
                $where[] = "DATE(pl_datetime) <= '" . sql_real_escape_string($date_end) . "'";
            }
            if ($search) {
                $search_escaped = sql_real_escape_string($search);
                $where[] = "(pl_title LIKE '%{$search_escaped}%' OR pl_message LIKE '%{$search_escaped}%')";
            }
            
            $where_sql = implode(' AND ', $where);
            
            $count_result = sql_fetch("SELECT COUNT(*) as cnt FROM g5_push_logs WHERE {$where_sql}");
            $total = intval($count_result['cnt']);
            
            $sql = "SELECT * FROM g5_push_logs WHERE {$where_sql} ORDER BY pl_datetime DESC LIMIT {$offset}, {$limit}";
            $result = sql_query($sql);
            
            $logs = [];
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $logs[] = [
                        'pl_id' => $row['pl_id'],
                        'pl_type' => $row['pl_type'],
                        'pl_title' => $row['pl_title'],
                        'pl_message' => $row['pl_message'],
                        'pl_url' => $row['pl_url'],
                        'pl_image_url' => $row['pl_image_url'],
                        'pl_target_count' => intval($row['pl_target_count']),
                        'pl_success_count' => intval($row['pl_success_count']),
                        'pl_fail_count' => intval($row['pl_fail_count']),
                        'pl_target_members' => $row['pl_target_members'],
                        'pl_results' => $row['pl_results'],
                        'pl_sender' => $row['pl_sender'],
                        'pl_datetime' => $row['pl_datetime']
                    ];
                }
            }
            
            echo json_encode([
                'success' => true,
                'logs' => $logs,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'detail':
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID가 필요합니다.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $log = sql_fetch("SELECT * FROM g5_push_logs WHERE pl_id = {$id}");
            
            if ($log) {
                echo json_encode(['success' => true, 'log' => $log], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'message' => '데이터를 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => '알 수 없는 액션입니다.'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '오류: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}