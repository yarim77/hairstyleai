<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error_msg = "[$errno] $errstr in $errfile on line $errline";
    error_log($error_msg);
    return false;
});

set_exception_handler(function($exception) {
    $error_msg = "Uncaught Exception: " . $exception->getMessage();
    error_log($error_msg);
    echo "<h1>500 Internal Server Error</h1><pre>" . htmlspecialchars($error_msg) . "</pre>";
    exit;
});

try {
    $log_dir = __DIR__ . '/logs';
    if (!is_dir($log_dir)) mkdir($log_dir, 0755, true);
    if (!file_exists(__DIR__ . '/auth.php')) throw new Exception('auth.php file not found');
    require_once __DIR__ . '/auth.php';
    require_login();
    $current_user = get_auth_user();
    if (!$current_user) throw new Exception('Failed to get current user info');
    require_once __DIR__ . '/config.php';
    $test_notification_config = get_test_notification_config();
} catch (Exception $e) {
    $error_details = ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()];
    error_log("Fatal Error: " . print_r($error_details, true));
    echo "<h1>Error</h1><pre>" . htmlspecialchars(print_r($error_details, true)) . "</pre>";
    exit;
}

$level_names = array(
    1 => '헤린이', 2 => '루키 스타', 3 => '슈퍼 스타', 4 => '빡고수', 5 => '신의손',
    6 => '특별회원', 7 => '명예회원', 8 => '골드회원', 9 => '다이아회원', 10 => '최고관리자'
);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>푸시 알림 시스템</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif; }
        body { background: #f5f7fa; color: #1a1d1f; }

        .header {
            background: white;
            border-bottom: 1px solid #e8ecef;
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo { font-size: 20px; font-weight: 700; color: #5b67f1; display: flex; align-items: center; gap: 10px; }

        .header-right { display: flex; align-items: center; gap: 12px; }

        .header-btn {
            padding: 8px 16px;
            background: white;
            border: 1px solid #e8ecef;
            border-radius: 8px;
            font-size: 14px;
            color: #6b7280;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .header-btn:hover { background: #f8f9fb; color: #5b67f1; }

        .user-info { padding: 6px 12px; background: #f8f9fb; border-radius: 8px; font-size: 14px; color: #6b7280; }

        .tabs-container { background: white; border-bottom: 1px solid #e8ecef; padding: 0 32px; }

        .tabs { display: flex; gap: 4px; }

        .tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab:hover { color: #5b67f1; }
        .tab.active { color: #5b67f1; border-bottom-color: #5b67f1; }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px;
            display: grid;
            grid-template-columns: 480px 1fr;
            gap: 32px;
            align-items: start;
        }

        .main-container.single-column { grid-template-columns: 1fr; }

        .left-panel {
            background: white;
            border-radius: 16px;
            border: 1px solid #e8ecef;
            padding: 28px;
        }

        .panel-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1d1f;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .panel-title i { color: #5b67f1; }

        .right-panel {
            background: white;
            border-radius: 16px;
            border: 1px solid #e8ecef;
            padding: 28px;
            position: sticky;
            top: 32px;
        }

        .form-group { margin-bottom: 20px; }

        .form-label { display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px; }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #e8ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control:focus { outline: none; border-color: #5b67f1; box-shadow: 0 0 0 3px rgba(91, 103, 241, 0.1); }

        textarea.form-control { resize: vertical; min-height: 90px; }

        input[type="file"].form-control { padding: 10px; cursor: pointer; }

        .device-selector { display: flex; gap: 12px; }

        .device-option { flex: 1; }
        .device-option input[type="checkbox"] { display: none; }

        .device-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            border: 1.5px solid #e8ecef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
        }

        .device-option input[type="checkbox"]:checked + label {
            border-color: #5b67f1;
            background: #eef1ff;
            color: #5b67f1;
        }

        .device-option.android label i { color: #3ddc84; }
        .device-option.ios label i { color: #007aff; }

        .member-select-section {
            background: #f8f9fb;
            border: 1px solid #e8ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .member-select-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .member-select-header h6 { margin: 0; font-weight: 600; color: #374151; font-size: 15px; }

        .btn-member-search {
            background: #5b67f1;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-member-search:hover { background: #4a56d9; }

        .selected-members-area {
            background: white;
            border: 1px solid #e8ecef;
            border-radius: 8px;
            padding: 15px;
            min-height: 60px;
            max-height: 150px;
            overflow-y: auto;
        }

        .selected-member-chips { display: flex; flex-wrap: wrap; gap: 8px; }

        .member-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background: #eff6ff;
            color: #1e40af;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #dbeafe;
        }

        .member-chip .chip-remove {
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #dbeafe;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 10px;
            color: #1e40af;
        }

        .member-chip .chip-remove:hover { background: #ef4444; color: white; }

        .empty-selection { text-align: center; padding: 16px; color: #9ca3af; font-size: 13px; }
        .empty-selection i { display: block; font-size: 20px; margin-bottom: 6px; }

        .selected-stats {
            padding: 12px 16px;
            background: #f8f9fb;
            border: 1px solid #e8ecef;
            border-radius: 8px;
            font-size: 14px;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .selected-stats i { color: #5b67f1; }
        .selected-stats strong { color: #5b67f1; font-weight: 600; }

        .stat-item { display: flex; align-items: center; gap: 6px; }

        .form-hint { font-size: 12px; color: #9ca3af; margin-top: 6px; }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #5b67f1, #4a56d9);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }

        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #4a56d9, #3b47c8);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(91, 103, 241, 0.3);
        }

        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

        .preview-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .preview-badge.android { background: #e8f5e9; color: #2e7d32; }
        .preview-badge.ios { background: #e3f2fd; color: #1565c0; }

        .preview-phone {
            background: linear-gradient(145deg, #1f2937 0%, #111827 100%);
            border-radius: 36px;
            padding: 14px;
            max-width: 340px;
            margin: 0 auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .preview-screen {
            background: #f3f4f6;
            border-radius: 26px;
            padding: 18px;
            min-height: 400px;
        }

        .preview-status-bar {
            display: flex;
            justify-content: space-between;
            padding: 8px 4px 20px;
            font-size: 12px;
            color: #374151;
            font-weight: 500;
        }

        .preview-notification {
            background: white;
            border-radius: 18px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .preview-notification-header { display: flex; gap: 12px; margin-bottom: 12px; }

        .preview-app-icon {
            width: 44px;
            height: 44px;
            background: #5b67f1;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }

        .preview-content { flex: 1; min-width: 0; }
        .preview-app-name { font-size: 12px; color: #9ca3af; margin-bottom: 4px; }
        .preview-title { font-weight: 600; font-size: 15px; color: #1a1d1f; margin-bottom: 4px; }
        .preview-body { font-size: 14px; color: #6b7280; line-height: 1.4; }
        .preview-time { font-size: 11px; color: #9ca3af; white-space: nowrap; }

        .preview-image { width: 100%; border-radius: 12px; margin-top: 12px; display: none; }
        .preview-image.show { display: block; }

        /* 이미지 업로드 프리뷰 */
        .image-preview-box {
            display: none;
            margin-top: 12px;
            position: relative;
            display: inline-block;
        }

        .image-preview-box img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid #e8ecef;
        }

        .image-preview-box .remove-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ef4444;
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        /* 모달 스타일 */
        .modal-dialog { max-width: 1200px; margin: 1.75rem auto; }

        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            background: white;
            padding: 24px 32px;
            border-bottom: 1px solid #f0f0f0;
            border-radius: 20px 20px 0 0;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1d1f;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-subtitle { font-size: 13px; color: #9ca3af; margin-top: 4px; font-weight: 400; }

        .modal-body { padding: 32px; background: #fafbfc; }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .filter-grid:last-child { margin-bottom: 0; }

        .filter-group { display: flex; flex-direction: column; }

        .filter-group label { font-size: 13px; font-weight: 500; color: #6b7280; margin-bottom: 8px; }

        .filter-group input,
        .filter-group select {
            padding: 10px 14px;
            border: 1px solid #e8ecef;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            transition: all 0.2s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #5b67f1;
            box-shadow: 0 0 0 3px rgba(91, 103, 241, 0.1);
        }

        .filter-actions { display: flex; justify-content: flex-end; gap: 8px; grid-column: 1 / -1; }

        .btn-reset {
            padding: 10px 20px;
            background: white;
            border: 1px solid #e8ecef;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-reset:hover { background: #f8f9fb; border-color: #d1d5db; }

        .member-count-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            background: #eef1ff;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #5b67f1;
            font-weight: 500;
        }

        .member-count-info i { font-size: 16px; }
        .member-count-info strong { font-weight: 700; font-size: 16px; }

        .member-table-wrapper {
            background: white;
            border-radius: 12px;
            border: 1px solid #f0f0f0;
            overflow: hidden;
            max-height: 450px;
            overflow-y: auto;
        }

        .member-table { width: 100%; border-collapse: collapse; }

        .member-table thead { position: sticky; top: 0; z-index: 10; }

        .member-table thead th {
            background: #fafbfc;
            padding: 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 1px solid #f0f0f0;
        }

        .member-table tbody td {
            padding: 16px;
            border-bottom: 1px solid #f8f9fb;
            font-size: 14px;
            color: #374151;
        }

        .member-table tbody tr { transition: background 0.15s; }
        .member-table tbody tr:hover { background: #fafbfc; }

        .member-table input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; accent-color: #5b67f1; }

        .member-name { font-weight: 600; color: #1a1d1f; }
        .member-subtitle { font-size: 12px; color: #9ca3af; margin-top: 2px; }

        .level-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .level-badge.lv1 { background: #fee2e2; color: #991b1b; }
        .level-badge.lv2 { background: #dbeafe; color: #1e40af; }
        .level-badge.lv3 { background: #d1fae5; color: #065f46; }
        .level-badge.lv4 { background: #fef3c7; color: #92400e; }
        .level-badge.lv5 { background: #e0e7ff; color: #3730a3; }
        .level-badge.lv6 { background: #fce7f3; color: #9d174d; }
        .level-badge.lv7 { background: #ede9fe; color: #6d28d9; }
        .level-badge.lv8 { background: #fef3c7; color: #854d0e; }
        .level-badge.lv9 { background: #dbeafe; color: #075985; }
        .level-badge.lv10 { background: #1f2937; color: #ffffff; }

        .token-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .token-badge.has-token { background: #d1fae5; color: #065f46; }
        .token-badge.no-token { background: #fee2e2; color: #991b1b; }
        .token-badge.android { background: #e8f5e9; color: #2e7d32; }
        .token-badge.ios { background: #e3f2fd; color: #1565c0; }

        .modal-footer {
            padding: 20px 32px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-radius: 0 0 20px 20px;
        }

        .selected-count { font-size: 14px; color: #6b7280; }
        .selected-count strong { color: #5b67f1; font-size: 18px; font-weight: 700; }

        .modal-footer-actions { display: flex; gap: 12px; }

        .btn-cancel {
            padding: 11px 24px;
            background: white;
            border: 1px solid #e8ecef;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover { background: #f8f9fb; }

        .btn-save {
            padding: 11px 28px;
            background: #5b67f1;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-save:hover { background: #4a56d9; }

        .tab-content { display: none; }
        .tab-content.active { display: grid; }

        .test-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: #f8f9fb;
            border-radius: 8px;
            border: 1px solid #e8ecef;
        }

        .test-toggle input { width: 18px; height: 18px; cursor: pointer; }
        .test-toggle label { font-size: 14px; font-weight: 500; color: #6b7280; cursor: pointer; }

        /* 발송 이력 스타일 */
        .history-container {
            background: white;
            border-radius: 16px;
            border: 1px solid #e8ecef;
            padding: 28px;
            width: 100%;
        }

        .history-filters { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }

        .history-filters input,
        .history-filters select {
            padding: 10px 14px;
            border: 1px solid #e8ecef;
            border-radius: 8px;
            font-size: 14px;
        }

        .history-table { width: 100%; border-collapse: collapse; }

        .history-table thead th {
            background: #fafbfc;
            padding: 14px 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 1px solid #e8ecef;
        }

        .history-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            color: #374151;
        }

        .history-table tbody tr:hover { background: #fafbfc; }

        .history-type {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .history-type.individual { background: #dbeafe; color: #1e40af; }
        .history-type.group { background: #fce7f3; color: #9d174d; }

        .history-result { display: flex; align-items: center; gap: 8px; }
        .history-success { color: #059669; font-weight: 600; }
        .history-fail { color: #dc2626; font-weight: 600; }

        .history-empty { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .history-empty i { font-size: 48px; margin-bottom: 16px; display: block; }

        .btn-refresh {
            padding: 10px 16px;
            background: white;
            border: 1px solid #e8ecef;
            border-radius: 8px;
            font-size: 14px;
            color: #6b7280;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-refresh:hover { background: #f8f9fb; }

        .pagination { display: flex; justify-content: center; gap: 4px; margin-top: 20px; }

        .pagination button {
            padding: 8px 14px;
            border: 1px solid #e8ecef;
            background: white;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        .pagination button:hover { background: #f8f9fb; }
        .pagination button.active { background: #5b67f1; color: white; border-color: #5b67f1; }
        .pagination button:disabled { opacity: 0.5; cursor: not-allowed; }

        @media (max-width: 1024px) {
            .main-container { grid-template-columns: 1fr; }
            .right-panel { display: none; }
            .filter-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <i class="fas fa-paper-plane"></i>
            푸시 발송
        </div>
        <div class="header-right">
            <div class="test-toggle">
                <input type="checkbox" id="useTestSettings">
                <label for="useTestSettings">
                    <i class="fas fa-flask"></i> 테스트 설정
                </label>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <?php echo htmlspecialchars($current_user['username']); ?>
            </div>
            <a href="view_logs.php" class="header-btn" target="_blank">
                <i class="fas fa-terminal"></i> 로그
            </a>
            <a href="api_docs.php" class="header-btn">
                <i class="fas fa-book"></i> API
            </a>
            <a href="settings.php" class="header-btn">
                <i class="fas fa-cog"></i> 설정
            </a>
            <a href="logout.php" class="header-btn">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <div class="tabs-container">
        <div class="tabs">
            <button class="tab active" data-tab="individual">
                <i class="fas fa-user"></i> 개별 푸시
            </button>
            <button class="tab" data-tab="group">
                <i class="fas fa-users"></i> 전체 푸시
            </button>
            <button class="tab" data-tab="history">
                <i class="fas fa-history"></i> 발송 이력
            </button>
        </div>
    </div>

    <!-- 개별 푸시 탭 -->
    <div class="main-container tab-content active" id="individual">
        <div class="left-panel">
            <h2 class="panel-title">
                <i class="fas fa-user"></i>
                개별 푸시 발송
            </h2>
            
            <form id="individualPushForm">
                <input type="hidden" name="push_type" value="individual">
                
                <div class="form-group">
                    <label class="form-label">기기 유형</label>
                    <div class="device-selector">
                        <div class="device-option android">
                            <input type="checkbox" id="android" value="android" checked>
                            <label for="android">
                                <i class="fab fa-android"></i> Android
                            </label>
                        </div>
                        <div class="device-option ios">
                            <input type="checkbox" id="ios" value="ios" checked>
                            <label for="ios">
                                <i class="fab fa-apple"></i> iOS
                            </label>
                        </div>
                    </div>
                    <div class="form-hint">선택한 플랫폼의 토큰만 발송됩니다</div>
                </div>

                <div class="member-select-section">
                    <div class="member-select-header">
                        <h6><i class="fas fa-user-check"></i> 대상 회원 선택</h6>
                        <button type="button" class="btn-member-search" data-bs-toggle="modal" data-bs-target="#memberSelectModal">
                            <i class="fas fa-search"></i> 회원 조회
                        </button>
                    </div>
                    <div class="selected-members-area">
                        <div id="selectedMemberChips" class="selected-member-chips"></div>
                        <div id="emptySelection" class="empty-selection">
                            <i class="fas fa-user-plus"></i>
                            회원을 선택하세요
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">선택된 회원 정보</label>
                    <div class="selected-stats">
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span id="selectedMemberCount"><strong>0</strong>명</span>
                        </div>
                        <div class="stat-item">
                            <i class="fab fa-android" style="color:#3ddc84"></i>
                            <span id="androidTokenCount"><strong>0</strong>개</span>
                        </div>
                        <div class="stat-item">
                            <i class="fab fa-apple" style="color:#007aff"></i>
                            <span id="iosTokenCount"><strong>0</strong>개</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">제목 *</label>
                    <input type="text" class="form-control" id="title" name="title" required placeholder="알림 제목을 입력하세요">
                </div>
                
                <div class="form-group">
                    <label class="form-label">내용 *</label>
                    <textarea class="form-control" id="message" name="memo" required placeholder="알림 내용을 입력하세요"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">URL (선택)</label>
                    <input type="url" class="form-control" id="url" name="url" placeholder="https://example.com">
                </div>
                
                <div class="form-group">
                    <label class="form-label">이미지 URL (선택)</label>
                    <input type="url" class="form-control" id="image_url" name="file_url" placeholder="https://example.com/image.jpg">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-image"></i> 또는 이미지 파일 업로드
                    </label>
                    <input type="file" class="form-control" id="image_file" accept="image/*">
                    <div class="form-hint">JPG, PNG, GIF (최대 2MB)</div>
                    
                    <div id="imagePreviewContainer" style="display:none;margin-top:12px;">
                        <div style="position:relative;display:inline-block;">
                            <img id="imagePreview" src="" style="max-width:200px;max-height:200px;border-radius:8px;border:1px solid #e8ecef;">
                            <button type="button" id="removeImageBtn" style="position:absolute;top:-8px;right:-8px;width:24px;height:24px;border-radius:50%;background:#ef4444;color:white;border:none;cursor:pointer;font-size:12px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div style="margin-top:8px;font-size:12px;color:#6b7280;">
                            <i class="fas fa-check-circle" style="color:#10b981;"></i>
                            <span id="imageFileName"></span>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn-primary" id="sendIndividualPushBtn">
                    <i class="fas fa-paper-plane"></i> 푸시 알림 보내기
                </button>
            </form>
        </div>

        <div class="right-panel">
            <h2 class="panel-title">
                <i class="fas fa-mobile-alt"></i>
                미리보기
            </h2>
            
            <div class="preview-badge android" id="previewBadge">
                <i class="fab fa-android"></i> Android / <i class="fab fa-apple"></i> iOS
            </div>
            
            <div class="preview-phone">
                <div class="preview-screen">
                    <div class="preview-status-bar">
                        <span>9:41</span>
                        <span>
                            <i class="fas fa-signal"></i>
                            <i class="fas fa-wifi"></i>
                            <i class="fas fa-battery-full"></i>
                        </span>
                    </div>
                    <div class="preview-notification">
                        <div class="preview-notification-header">
                            <div class="preview-app-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="preview-content">
                                <div class="preview-title" id="previewTitle">알림 제목</div>
                            </div>
                            <div class="preview-time">지금</div>
                        </div>
                        <div class="preview-body" id="previewBody">알림 내용이 여기에 표시됩니다.</div>
                        <img id="previewImage" class="preview-image" src="" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 전체 푸시 탭 -->
    <div class="main-container tab-content" id="group">
        <div class="left-panel">
            <h2 class="panel-title">
                <i class="fas fa-users"></i>
                전체 푸시 발송
            </h2>
            
            <form id="groupPushForm">
                <input type="hidden" name="push_type" value="group">
                
                <div class="form-group">
                    <label class="form-label">대상 기기</label>
                    <div class="device-selector">
                        <div class="device-option android">
                            <input type="checkbox" id="target_android" value="android" checked>
                            <label for="target_android">
                                <i class="fab fa-android"></i> Android
                            </label>
                        </div>
                        <div class="device-option ios">
                            <input type="checkbox" id="target_ios" value="ios" checked>
                            <label for="target_ios">
                                <i class="fab fa-apple"></i> iOS
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">제목 *</label>
                    <input type="text" class="form-control" id="group_title" name="title" required placeholder="알림 제목을 입력하세요">
                </div>
                
                <div class="form-group">
                    <label class="form-label">내용 *</label>
                    <textarea class="form-control" id="group_message" name="memo" required placeholder="알림 내용을 입력하세요"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">URL (선택)</label>
                    <input type="url" class="form-control" id="group_url" name="url" placeholder="https://example.com">
                </div>
                
                <div class="form-group">
                    <label class="form-label">이미지 URL (선택)</label>
                    <input type="url" class="form-control" id="group_image_url" name="file_url" placeholder="https://example.com/image.jpg">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-image"></i> 또는 이미지 파일 업로드
                    </label>
                    <input type="file" class="form-control" id="group_image_file" accept="image/*">
                    <div class="form-hint">JPG, PNG, GIF (최대 2MB)</div>
                    
                    <div id="groupImagePreviewContainer" style="display:none;margin-top:12px;">
                        <div style="position:relative;display:inline-block;">
                            <img id="groupImagePreview" src="" style="max-width:200px;max-height:200px;border-radius:8px;border:1px solid #e8ecef;">
                            <button type="button" id="removeGroupImageBtn" style="position:absolute;top:-8px;right:-8px;width:24px;height:24px;border-radius:50%;background:#ef4444;color:white;border:none;cursor:pointer;font-size:12px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div style="margin-top:8px;font-size:12px;color:#6b7280;">
                            <i class="fas fa-check-circle" style="color:#10b981;"></i>
                            <span id="groupImageFileName"></span>
                        </div>
                    </div>
                </div>
                
                <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:14px;margin-bottom:16px;">
                    <i class="fas fa-exclamation-triangle" style="color:#d97706"></i>
                    <strong>주의:</strong> 전체 푸시는 모든 사용자에게 발송됩니다.
                </div>
                
                <button type="button" class="btn-primary" id="sendGroupPushBtn">
                    <i class="fas fa-paper-plane"></i> 전체 푸시 발송
                </button>
            </form>
        </div>
        
        <div class="right-panel">
            <h2 class="panel-title">
                <i class="fas fa-mobile-alt"></i>
                미리보기
            </h2>
            
            <div class="preview-badge android">
                <i class="fab fa-android"></i> Android / <i class="fab fa-apple"></i> iOS
            </div>
            
            <div class="preview-phone">
                <div class="preview-screen">
                    <div class="preview-status-bar">
                        <span>9:41</span>
                        <span>
                            <i class="fas fa-signal"></i>
                            <i class="fas fa-wifi"></i>
                            <i class="fas fa-battery-full"></i>
                        </span>
                    </div>
                    <div class="preview-notification">
                        <div class="preview-notification-header">
                            <div class="preview-app-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="preview-content">
                                <div class="preview-app-name">앱 이름</div>
                                <div class="preview-title" id="groupPreviewTitle">알림 제목</div>
                            </div>
                            <div class="preview-time">지금</div>
                        </div>
                        <div class="preview-body" id="groupPreviewBody">알림 내용이 여기에 표시됩니다.</div>
                        <img id="groupPreviewImage" class="preview-image" src="" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 발송 이력 탭 -->
    <div class="main-container single-column tab-content" id="history">
        <div class="history-container">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <h2 class="panel-title" style="margin-bottom:0">
                    <i class="fas fa-history"></i>
                    발송 이력
                </h2>
                <button class="btn-refresh" id="refreshHistoryBtn">
                    <i class="fas fa-sync-alt"></i> 새로고침
                </button>
            </div>
            
            <div class="history-filters">
                <select id="historyTypeFilter">
                    <option value="">전체 유형</option>
                    <option value="individual">개별 푸시</option>
                    <option value="group">전체 푸시</option>
                </select>
                <input type="date" id="historyDateStart">
                <input type="date" id="historyDateEnd">
                <input type="text" id="historySearch" placeholder="제목 검색..." style="width:200px">
            </div>
            
            <div id="historyTableWrapper">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th style="width:60px">번호</th>
                            <th style="width:80px">유형</th>
                            <th>제목</th>
                            <th style="width:120px">결과</th>
                            <th style="width:100px">발송자</th>
                            <th style="width:160px">발송시간</th>
                            <th style="width:80px">상세</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <tr>
                            <td colspan="7" class="history-empty">
                                <i class="fas fa-inbox"></i>
                                <div>발송 이력이 없습니다</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination" id="historyPagination"></div>
        </div>
    </div>

    <!-- 회원 선택 모달 -->
    <div class="modal fade" id="memberSelectModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">
                            <i class="fas fa-users"></i> 대상자 조회
                        </h5>
                        <div class="modal-subtitle">FCM 토큰이 등록된 회원만 선택 가능합니다</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="filter-section">
                        <div class="filter-grid">
                            <div class="filter-group">
                                <label>이름</label>
                                <input type="text" id="filterName" placeholder="이름 검색">
                            </div>
                            <div class="filter-group">
                                <label>아이디</label>
                                <input type="text" id="filterMbId" placeholder="아이디 검색">
                            </div>
                            <div class="filter-group">
                                <label>이메일</label>
                                <input type="text" id="filterEmail" placeholder="이메일 검색">
                            </div>
                        </div>
                        <div class="filter-grid">
                            <div class="filter-group">
                                <label>토큰 상태</label>
                                <select id="filterStatus">
                                    <option value="">전체</option>
                                    <option value="active">토큰 있음</option>
                                    <option value="inactive">토큰 없음</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>회원등급</label>
                                <select id="filterLevel">
                                    <option value="">전체</option>
                                    <?php foreach($level_names as $lv => $name): ?>
                                    <option value="<?php echo $lv; ?>">Lv.<?php echo $lv; ?> <?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-actions">
                                <button class="btn-reset" id="resetFilterBtn">
                                    <i class="fas fa-redo"></i> 초기화
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="member-count-info">
                        <i class="fas fa-users"></i>
                        총 <strong id="totalMemberCount">0</strong> 명
                    </div>

                    <div class="member-table-wrapper">
                        <table class="member-table">
                            <thead>
                                <tr>
                                    <th style="width:50px"><input type="checkbox" id="selectAllMembers"></th>
                                    <th>이름</th>
                                    <th>아이디</th>
                                    <th>이메일</th>
                                    <th>등급</th>
                                    <th>가입일</th>
                                    <th>토큰</th>
                                </tr>
                            </thead>
                            <tbody id="memberTableBody">
                                <tr>
                                    <td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                                        <i class="fas fa-spinner fa-spin"></i> 불러오는 중...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div class="selected-count">
                        선택: <strong id="modalSelectedCount">0</strong>명
                    </div>
                    <div class="modal-footer-actions">
                        <button class="btn-cancel" data-bs-dismiss="modal">닫기</button>
                        <button class="btn-save" id="confirmMemberSelection">
                            <i class="fas fa-check"></i> 선택 완료 (<span id="modalSelectedCount2">0</span>명)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 발송 상세 모달 -->
    <div class="modal fade" id="historyDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> 발송 상세</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="historyDetailContent"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const testNotificationConfig = <?php echo json_encode($test_notification_config); ?>;
        const levelNames = <?php echo json_encode($level_names); ?>;
        const currentUser = '<?php echo addslashes($current_user['username']); ?>';
        
        let selectedMembers = [];
        let allMembers = [];
        let debounceTimer = null;
        let historyPage = 1;

        console.log('📱 푸시 발송 시스템 로드됨');

        // ========================================
        // 탭 전환
        // ========================================
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
                
                if (this.dataset.tab === 'history') {
                    loadHistory();
                }
            });
        });

        // ========================================
        // 이미지 업로드 처리 (개별 푸시)
        // ========================================
        document.getElementById('image_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                alert('이미지는 2MB 이하만 가능합니다.');
                this.value = '';
                return;
            }

            if (!file.type.startsWith('image/')) {
                alert('이미지 파일만 업로드 가능합니다.');
                this.value = '';
                return;
            }

            const formData = new FormData();
            formData.append('image', file);

            document.getElementById('imagePreviewContainer').style.display = 'block';
            document.getElementById('imageFileName').textContent = '업로드 중...';

            fetch('upload_image.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('image_url').value = data.url;
                    document.getElementById('imagePreview').src = data.url;
                    document.getElementById('imageFileName').textContent = file.name;
                    
                    const previewImg = document.getElementById('previewImage');
                    previewImg.src = data.url;
                    previewImg.classList.add('show');
                } else {
                    alert('업로드 실패: ' + data.message);
                    document.getElementById('imagePreviewContainer').style.display = 'none';
                    this.value = '';
                }
            })
            .catch(error => {
                alert('업로드 오류: ' + error.message);
                document.getElementById('imagePreviewContainer').style.display = 'none';
                document.getElementById('image_file').value = '';
            });
        });

        document.getElementById('removeImageBtn').addEventListener('click', function() {
            document.getElementById('image_file').value = '';
            document.getElementById('image_url').value = '';
            document.getElementById('imagePreviewContainer').style.display = 'none';
            document.getElementById('previewImage').src = '';
            document.getElementById('previewImage').classList.remove('show');
        });

        // ========================================
        // 이미지 업로드 처리 (전체 푸시)
        // ========================================
        document.getElementById('group_image_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                alert('이미지는 2MB 이하만 가능합니다.');
                this.value = '';
                return;
            }

            if (!file.type.startsWith('image/')) {
                alert('이미지 파일만 업로드 가능합니다.');
                this.value = '';
                return;
            }

            const formData = new FormData();
            formData.append('image', file);

            document.getElementById('groupImagePreviewContainer').style.display = 'block';
            document.getElementById('groupImageFileName').textContent = '업로드 중...';

            fetch('upload_image.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('group_image_url').value = data.url;
                    document.getElementById('groupImagePreview').src = data.url;
                    document.getElementById('groupImageFileName').textContent = file.name;
                    
                    const previewImg = document.getElementById('groupPreviewImage');
                    previewImg.src = data.url;
                    previewImg.classList.add('show');
                } else {
                    alert('업로드 실패: ' + data.message);
                    document.getElementById('groupImagePreviewContainer').style.display = 'none';
                    this.value = '';
                }
            })
            .catch(error => {
                alert('업로드 오류: ' + error.message);
                document.getElementById('groupImagePreviewContainer').style.display = 'none';
                document.getElementById('group_image_file').value = '';
            });
        });

        document.getElementById('removeGroupImageBtn').addEventListener('click', function() {
            document.getElementById('group_image_file').value = '';
            document.getElementById('group_image_url').value = '';
            document.getElementById('groupImagePreviewContainer').style.display = 'none';
            document.getElementById('groupPreviewImage').src = '';
            document.getElementById('groupPreviewImage').classList.remove('show');
        });

        // ========================================
        // 회원 목록 로드
        // ========================================
        function loadMembers() {
            const params = new URLSearchParams({
                name: document.getElementById('filterName').value,
                mb_id: document.getElementById('filterMbId').value,
                email: document.getElementById('filterEmail').value,
                level: document.getElementById('filterLevel').value,
                status: document.getElementById('filterStatus').value
            });

            document.getElementById('memberTableBody').innerHTML = `
                <tr><td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                    <i class="fas fa-spinner fa-spin"></i> 불러오는 중...
                </td></tr>`;

            fetch('get_members.php?' + params.toString())
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        allMembers = data.members;
                        renderMemberTable(data.members);
                        document.getElementById('totalMemberCount').textContent = data.total;
                    } else {
                        document.getElementById('memberTableBody').innerHTML = `
                            <tr><td colspan="7" style="text-align:center;padding:40px;color:#ef4444;">
                                ${data.message || '오류가 발생했습니다'}
                            </td></tr>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('memberTableBody').innerHTML = `
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:#ef4444;">
                            회원 목록을 불러올 수 없습니다
                        </td></tr>`;
                });
        }

        // ========================================
        // 회원 테이블 렌더링
        // ========================================
        function renderMemberTable(members) {
            const tbody = document.getElementById('memberTableBody');

            if (!members || members.length === 0) {
                tbody.innerHTML = `
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">
                        <i class="fas fa-inbox" style="font-size:24px;display:block;margin-bottom:8px;"></i>
                        검색 결과가 없습니다
                    </td></tr>`;
                return;
            }

            tbody.innerHTML = members.map(m => {
                const isSelected = selectedMembers.some(s => s.mb_id === m.mb_id);
                const levelName = m.mb_level_name || levelNames[m.mb_level] || '';
                const hasToken = m.fcm_tokens && m.fcm_tokens.length > 0;
                
                let androidCount = 0, iosCount = 0;
                if (m.fcm_tokens) {
                    m.fcm_tokens.forEach(t => {
                        if (t.platform === 'android') androidCount++;
                        else if (t.platform === 'ios') iosCount++;
                    });
                }
                
                let tokenBadges = '';
                if (hasToken) {
                    if (androidCount > 0) tokenBadges += `<span class="token-badge android"><i class="fab fa-android"></i> ${androidCount}</span> `;
                    if (iosCount > 0) tokenBadges += `<span class="token-badge ios"><i class="fab fa-apple"></i> ${iosCount}</span>`;
                } else {
                    tokenBadges = '<span class="token-badge no-token">없음</span>';
                }
                
                return `
                    <tr>
                        <td>
                            <input type="checkbox" class="member-checkbox" 
                                value="${m.mb_id}" 
                                data-member='${JSON.stringify(m).replace(/'/g, "&#39;")}'
                                ${isSelected ? 'checked' : ''}
                                ${!hasToken ? 'disabled' : ''}>
                        </td>
                        <td>
                            <div class="member-name">${m.mb_name}</div>
                            ${m.mb_nick ? `<div class="member-subtitle">${m.mb_nick}</div>` : ''}
                        </td>
                        <td>${m.mb_id}</td>
                        <td>${m.mb_email || '-'}</td>
                        <td><span class="level-badge lv${m.mb_level}">Lv.${m.mb_level} ${levelName}</span></td>
                        <td>${m.mb_datetime || '-'}</td>
                        <td>${tokenBadges}</td>
                    </tr>`;
            }).join('');

            document.querySelectorAll('.member-checkbox:not([disabled])').forEach(cb => {
                cb.addEventListener('change', updateModalSelectedCount);
            });

            updateSelectAllCheckbox();
        }

        function updateModalSelectedCount() {
            const count = document.querySelectorAll('.member-checkbox:checked').length;
            document.getElementById('modalSelectedCount').textContent = count;
            document.getElementById('modalSelectedCount2').textContent = count;
            updateSelectAllCheckbox();
        }

        function updateSelectAllCheckbox() {
            const all = document.querySelectorAll('.member-checkbox:not([disabled])');
            const checked = document.querySelectorAll('.member-checkbox:checked');
            const selectAll = document.getElementById('selectAllMembers');

            if (all.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (checked.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (checked.length === all.length) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            }
        }

        document.getElementById('selectAllMembers').addEventListener('change', function() {
            document.querySelectorAll('.member-checkbox:not([disabled])').forEach(cb => {
                cb.checked = this.checked;
            });
            updateModalSelectedCount();
        });

        ['filterName', 'filterMbId', 'filterEmail', 'filterLevel', 'filterStatus'].forEach(id => {
            const el = document.getElementById(id);
            el.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(loadMembers, 300);
            });
            el.addEventListener('change', loadMembers);
        });

        document.getElementById('resetFilterBtn').addEventListener('click', function() {
            document.getElementById('filterName').value = '';
            document.getElementById('filterMbId').value = '';
            document.getElementById('filterEmail').value = '';
            document.getElementById('filterLevel').value = '';
            document.getElementById('filterStatus').value = '';
            loadMembers();
        });

        document.getElementById('confirmMemberSelection').addEventListener('click', function() {
            selectedMembers = [];
            document.querySelectorAll('.member-checkbox:checked').forEach(cb => {
                const memberData = JSON.parse(cb.dataset.member);
                selectedMembers.push(memberData);
            });

            updateSelectedMemberDisplay();
            bootstrap.Modal.getInstance(document.getElementById('memberSelectModal')).hide();
        });

        function updateSelectedMemberDisplay() {
            const chipsContainer = document.getElementById('selectedMemberChips');
            const emptySelection = document.getElementById('emptySelection');

            if (selectedMembers.length > 0) {
                chipsContainer.innerHTML = selectedMembers.map(m => `
                    <span class="member-chip">
                        <i class="fas fa-user"></i>
                        ${m.mb_name} (${m.mb_id})
                        <span class="chip-remove" onclick="removeMember('${m.mb_id}')">
                            <i class="fas fa-times"></i>
                        </span>
                    </span>
                `).join('');

                emptySelection.style.display = 'none';

                let androidCount = 0, iosCount = 0;
                selectedMembers.forEach(m => {
                    if (m.fcm_tokens) {
                        m.fcm_tokens.forEach(t => {
                            if (t.platform === 'android') androidCount++;
                            else if (t.platform === 'ios') iosCount++;
                        });
                    }
                });

                document.getElementById('selectedMemberCount').innerHTML = `<strong>${selectedMembers.length}</strong>명`;
                document.getElementById('androidTokenCount').innerHTML = `<strong>${androidCount}</strong>개`;
                document.getElementById('iosTokenCount').innerHTML = `<strong>${iosCount}</strong>개`;
            } else {
                chipsContainer.innerHTML = '';
                emptySelection.style.display = 'block';
                document.getElementById('selectedMemberCount').innerHTML = '<strong>0</strong>명';
                document.getElementById('androidTokenCount').innerHTML = '<strong>0</strong>개';
                document.getElementById('iosTokenCount').innerHTML = '<strong>0</strong>개';
            }
        }

        function removeMember(mbId) {
            selectedMembers = selectedMembers.filter(m => m.mb_id !== mbId);
            updateSelectedMemberDisplay();
        }

        document.getElementById('memberSelectModal').addEventListener('shown.bs.modal', function() {
            loadMembers();
        });

        // ========================================
        // 미리보기 업데이트
        // ========================================
        function updatePreview() {
            const title = document.getElementById('title').value || '알림 제목';
            const message = document.getElementById('message').value || '알림 내용이 여기에 표시됩니다.';
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewBody').textContent = message;
        }

        function updateGroupPreview() {
            const title = document.getElementById('group_title').value || '알림 제목';
            const message = document.getElementById('group_message').value || '알림 내용이 여기에 표시됩니다.';
            document.getElementById('groupPreviewTitle').textContent = title;
            document.getElementById('groupPreviewBody').textContent = message;
        }

        document.getElementById('title').addEventListener('input', updatePreview);
        document.getElementById('message').addEventListener('input', updatePreview);
        document.getElementById('group_title').addEventListener('input', updateGroupPreview);
        document.getElementById('group_message').addEventListener('input', updateGroupPreview);

        // ========================================
        // 개별 푸시 전송
        // ========================================
        document.getElementById('sendIndividualPushBtn').addEventListener('click', async function() {
            console.log('🔥 개별 푸시 전송 시작');
            
            if (selectedMembers.length === 0) {
                alert('회원을 선택해주세요.');
                return;
            }

            const title = document.getElementById('title').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!title || !message) {
                alert('제목과 내용을 입력해주세요.');
                return;
            }

            const checkedApps = [];
            if (document.getElementById('android').checked) checkedApps.push('android');
            if (document.getElementById('ios').checked) checkedApps.push('ios');
            
            if (checkedApps.length === 0) {
                alert('발송할 기기 유형을 선택해주세요.');
                return;
            }

            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 전송 중...';

            let totalSuccess = 0;
            let totalFail = 0;
            let totalSkipped = 0;
            const results = [];

            for (const member of selectedMembers) {
                if (!member.fcm_tokens || member.fcm_tokens.length === 0) continue;
                
                for (const tokenInfo of member.fcm_tokens) {
                    const platform = tokenInfo.platform;
                    const token = tokenInfo.token;
                    
                    if (!checkedApps.includes(platform)) {
                        totalSkipped++;
                        continue;
                    }
                    
                    const formData = new FormData();
                    formData.append('push_type', 'individual');
                    formData.append('app', platform);
                    formData.append('user_token', token);
                    formData.append('title', title);
                    formData.append('memo', message);
                    formData.append('url', document.getElementById('url').value);
                    formData.append('file_url', document.getElementById('image_url').value);

                    try {
                        const response = await fetch('push_api.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            totalSuccess++;
                            results.push({ member: member.mb_name, platform, status: 'success' });
                        } else {
                            totalFail++;
                            results.push({ member: member.mb_name, platform, status: 'failed', error: data.message });
                        }
                    } catch (error) {
                        totalFail++;
                        results.push({ member: member.mb_name, platform, status: 'error', error: error.message });
                    }
                }
            }

            // 발송 이력 저장
            await saveHistory({
                type: 'individual',
                title: title,
                message: message,
                url: document.getElementById('url').value,
                image_url: document.getElementById('image_url').value,
                target_count: selectedMembers.length,
                success_count: totalSuccess,
                fail_count: totalFail,
                target_members: selectedMembers.map(m => ({ mb_id: m.mb_id, mb_name: m.mb_name })),
                results: results
            });

            btn.disabled = false;
            btn.innerHTML = originalText;

            let resultMsg = `📤 푸시 발송 완료\n\n`;
            resultMsg += `✅ 성공: ${totalSuccess}개\n`;
            if (totalFail > 0) resultMsg += `❌ 실패: ${totalFail}개\n`;
            if (totalSkipped > 0) resultMsg += `⏭️ 건너뜀: ${totalSkipped}개`;
            
            alert(resultMsg);
        });

        // ========================================
        // 전체 푸시 전송
        // ========================================
        document.getElementById('sendGroupPushBtn').addEventListener('click', async function() {
            if (!confirm('정말로 모든 사용자에게 푸시를 보내시겠습니까?')) return;

            const title = document.getElementById('group_title').value.trim();
            const message = document.getElementById('group_message').value.trim();
            
            if (!title || !message) {
                alert('제목과 내용을 입력해주세요.');
                return;
            }

            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 전송 중...';

            const formData = new FormData();
            formData.append('push_type', 'group');
            formData.append('title', title);
            formData.append('memo', message);
            formData.append('url', document.getElementById('group_url').value);
            formData.append('file_url', document.getElementById('group_image_url').value);

            try {
                const response = await fetch('push_api.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                await saveHistory({
                    type: 'group',
                    title: title,
                    message: message,
                    url: document.getElementById('group_url').value,
                    image_url: document.getElementById('group_image_url').value,
                    target_count: 0,
                    success_count: data.success ? 1 : 0,
                    fail_count: data.success ? 0 : 1,
                    results: [data]
                });

                if (data.success) {
                    alert('전체 푸시가 성공적으로 발송되었습니다.');
                } else {
                    alert('전송 실패: ' + data.message);
                }
            } catch (error) {
                alert('오류: ' + error.message);
            }

            btn.disabled = false;
            btn.innerHTML = originalText;
        });

        // ========================================
        // 발송 이력 저장
        // ========================================
        async function saveHistory(data) {
            try {
                const formData = new FormData();
                formData.append('action', 'save');
                formData.append('type', data.type);
                formData.append('title', data.title);
                formData.append('message', data.message);
                formData.append('url', data.url || '');
                formData.append('image_url', data.image_url || '');
                formData.append('target_count', data.target_count);
                formData.append('success_count', data.success_count);
                formData.append('fail_count', data.fail_count);
                formData.append('target_members', JSON.stringify(data.target_members || []));
                formData.append('results', JSON.stringify(data.results || []));
                formData.append('sender', currentUser);

                const response = await fetch('push_history.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                console.log('이력 저장 결과:', result);
            } catch (error) {
                console.error('이력 저장 실패:', error);
            }
        }

        // ========================================
        // 발송 이력 로드
        // ========================================
        async function loadHistory(page = 1) {
            historyPage = page;
            
            const params = new URLSearchParams({
                action: 'list',
                page: page,
                type: document.getElementById('historyTypeFilter').value,
                date_start: document.getElementById('historyDateStart').value,
                date_end: document.getElementById('historyDateEnd').value,
                search: document.getElementById('historySearch').value
            });

            document.getElementById('historyTableBody').innerHTML = `
                <tr><td colspan="7" class="history-empty">
                    <i class="fas fa-spinner fa-spin" style="font-size:24px"></i>
                    <div style="margin-top:12px">불러오는 중...</div>
                </td></tr>`;

            try {
                const response = await fetch('push_history.php?' + params.toString());
                const data = await response.json();
                
                console.log('이력 로드 결과:', data);
                
                if (data.success && data.logs && data.logs.length > 0) {
                    renderHistoryTable(data.logs);
                    renderHistoryPagination(data.total, data.page, data.limit);
                } else {
                    document.getElementById('historyTableBody').innerHTML = `
                        <tr><td colspan="7" class="history-empty">
                            <i class="fas fa-inbox"></i>
                            <div>발송 이력이 없습니다</div>
                        </td></tr>`;
                    document.getElementById('historyPagination').innerHTML = '';
                }
            } catch (error) {
                console.error('이력 로드 실패:', error);
                document.getElementById('historyTableBody').innerHTML = `
                    <tr><td colspan="7" class="history-empty">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>이력을 불러올 수 없습니다</div>
                        <div style="font-size:12px;margin-top:8px">${error.message}</div>
                    </td></tr>`;
            }
        }

        function renderHistoryTable(logs) {
            document.getElementById('historyTableBody').innerHTML = logs.map(log => `
                <tr>
                    <td>${log.pl_id}</td>
                    <td><span class="history-type ${log.pl_type}">${log.pl_type === 'individual' ? '개별' : '전체'}</span></td>
                    <td>
                        <div style="font-weight:500">${escapeHtml(log.pl_title)}</div>
                        <div style="font-size:12px;color:#9ca3af;margin-top:2px">${escapeHtml(log.pl_message).substring(0, 50)}${log.pl_message.length > 50 ? '...' : ''}</div>
                    </td>
                    <td>
                        <div class="history-result">
                            <span class="history-success">✓ ${log.pl_success_count}</span>
                            ${log.pl_fail_count > 0 ? `<span class="history-fail">✗ ${log.pl_fail_count}</span>` : ''}
                        </div>
                    </td>
                    <td>${escapeHtml(log.pl_sender)}</td>
                    <td>${log.pl_datetime}</td>
                    <td>
                        <button class="btn-reset" style="padding:6px 10px" onclick="showHistoryDetail(${log.pl_id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function renderHistoryPagination(total, currentPage, limit) {
            const totalPages = Math.ceil(total / limit);
            if (totalPages <= 1) {
                document.getElementById('historyPagination').innerHTML = '';
                return;
            }

            let html = '';
            html += `<button ${currentPage === 1 ? 'disabled' : ''} onclick="loadHistory(${currentPage - 1})">이전</button>`;
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    html += `<button class="${i === currentPage ? 'active' : ''}" onclick="loadHistory(${i})">${i}</button>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += '<button disabled>...</button>';
                }
            }
            
            html += `<button ${currentPage === totalPages ? 'disabled' : ''} onclick="loadHistory(${currentPage + 1})">다음</button>`;
            
            document.getElementById('historyPagination').innerHTML = html;
        }

        async function showHistoryDetail(id) {
            try {
                const response = await fetch(`push_history.php?action=detail&id=${id}`);
                const data = await response.json();
                
                if (data.success && data.log) {
                    const log = data.log;
                    let targetMembers = [];
                    let results = [];
                    
                    try { targetMembers = JSON.parse(log.pl_target_members || '[]'); } catch(e) {}
                    try { results = JSON.parse(log.pl_results || '[]'); } catch(e) {}
                    
                    document.getElementById('historyDetailContent').innerHTML = `
                        <div style="display:grid;gap:16px">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                                <div style="background:#f8f9fb;padding:16px;border-radius:8px">
                                    <div style="color:#6b7280;font-size:13px;margin-bottom:4px">발송 유형</div>
                                    <div style="font-weight:600">${log.pl_type === 'individual' ? '개별 푸시' : '전체 푸시'}</div>
                                </div>
                                <div style="background:#f8f9fb;padding:16px;border-radius:8px">
                                    <div style="color:#6b7280;font-size:13px;margin-bottom:4px">발송 시간</div>
                                    <div style="font-weight:600">${log.pl_datetime}</div>
                                </div>
                            </div>
                            <div style="background:#f8f9fb;padding:16px;border-radius:8px">
                                <div style="color:#6b7280;font-size:13px;margin-bottom:4px">제목</div>
                                <div style="font-weight:600">${escapeHtml(log.pl_title)}</div>
                            </div>
                            <div style="background:#f8f9fb;padding:16px;border-radius:8px">
                                <div style="color:#6b7280;font-size:13px;margin-bottom:4px">내용</div>
                                <div>${escapeHtml(log.pl_message)}</div>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
                                <div style="background:#d1fae5;padding:16px;border-radius:8px;text-align:center">
                                    <div style="font-size:24px;font-weight:700;color:#059669">${log.pl_success_count}</div>
                                    <div style="color:#065f46;font-size:13px">성공</div>
                                </div>
                                <div style="background:#fee2e2;padding:16px;border-radius:8px;text-align:center">
                                    <div style="font-size:24px;font-weight:700;color:#dc2626">${log.pl_fail_count}</div>
                                    <div style="color:#991b1b;font-size:13px">실패</div>
                                </div>
                                <div style="background:#dbeafe;padding:16px;border-radius:8px;text-align:center">
                                    <div style="font-size:24px;font-weight:700;color:#2563eb">${log.pl_target_count}</div>
                                    <div style="color:#1e40af;font-size:13px">대상자</div>
                                </div>
                            </div>
                            ${targetMembers.length > 0 ? `
                            <div style="background:#f8f9fb;padding:16px;border-radius:8px">
                                <div style="color:#6b7280;font-size:13px;margin-bottom:8px">대상 회원</div>
                                <div style="display:flex;flex-wrap:wrap;gap:6px">
                                    ${targetMembers.map(m => `<span style="background:white;padding:4px 10px;border-radius:16px;font-size:13px;border:1px solid #e8ecef">${escapeHtml(m.mb_name)} (${escapeHtml(m.mb_id)})</span>`).join('')}
                                </div>
                            </div>` : ''}
                        </div>
                    `;
                    
                    new bootstrap.Modal(document.getElementById('historyDetailModal')).show();
                }
            } catch (error) {
                alert('상세 정보를 불러올 수 없습니다.');
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // 이력 필터 이벤트
        document.getElementById('historyTypeFilter').addEventListener('change', () => loadHistory(1));
        document.getElementById('historyDateStart').addEventListener('change', () => loadHistory(1));
        document.getElementById('historyDateEnd').addEventListener('change', () => loadHistory(1));
        document.getElementById('historySearch').addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => loadHistory(1), 300);
        });
        document.getElementById('refreshHistoryBtn').addEventListener('click', () => loadHistory(1));

        // ========================================
        // 테스트 설정
        // ========================================
        document.getElementById('useTestSettings').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('title').value = testNotificationConfig.title || '테스트 알림';
                document.getElementById('message').value = testNotificationConfig.message || '테스트 메시지입니다.';
                document.getElementById('url').value = testNotificationConfig.domain || '';
                document.getElementById('group_title').value = testNotificationConfig.title || '테스트 알림';
                document.getElementById('group_message').value = testNotificationConfig.message || '테스트 메시지입니다.';
                document.getElementById('group_url').value = testNotificationConfig.domain || '';
                updatePreview();
                updateGroupPreview();
            } else {
                document.getElementById('title').value = '';
                document.getElementById('message').value = '';
                document.getElementById('url').value = '';
                document.getElementById('group_title').value = '';
                document.getElementById('group_message').value = '';
                document.getElementById('group_url').value = '';
                updatePreview();
                updateGroupPreview();
            }
        });

        console.log('✅ 모든 기능 로드 완료');
    </script>
</body>
</html>