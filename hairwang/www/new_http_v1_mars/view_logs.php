<?php
/**
 * 로그 뷰어 - 개발용
 */
require_once __DIR__ . '/auth.php';
require_login();

$log_file = __DIR__ . '/logs/php_errors.log';
$lines = 100; // 마지막 100줄만 표시

if (isset($_GET['clear'])) {
    file_put_contents($log_file, '');
    header('Location: view_logs.php');
    exit;
}

$log_content = '';
if (file_exists($log_file)) {
    $file_lines = file($log_file);
    $total_lines = count($file_lines);
    $start = max(0, $total_lines - $lines);
    $log_content = implode('', array_slice($file_lines, $start));
} else {
    $log_content = '로그 파일이 없습니다.';
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그 뷰어</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: #f5f7fa;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #1a1d1f;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #5b67f1;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: #4a56d9;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            border: none;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: white;
            color: #6b7280;
            border: 1px solid #e8ecef;
        }

        .btn-secondary:hover {
            background: #f8f9fb;
        }

        .log-container {
            background: #1a1d1f;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .log-content {
            background: #0d0f10;
            border-radius: 8px;
            padding: 20px;
            color: #e5e7eb;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
            max-height: 70vh;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }

        .log-content::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .log-content::-webkit-scrollbar-track {
            background: #1a1d1f;
            border-radius: 4px;
        }

        .log-content::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }

        .log-content::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        .highlight-error {
            color: #f87171;
            font-weight: 600;
        }

        .highlight-warning {
            color: #fbbf24;
        }

        .highlight-success {
            color: #34d399;
        }

        .highlight-info {
            color: #60a5fa;
        }

        .empty-log {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-log i {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
        }

        .stats {
            background: white;
            border-radius: 12px;
            padding: 16px 24px;
            margin-bottom: 20px;
            display: flex;
            gap: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stat-icon.info {
            background: #dbeafe;
            color: #1e40af;
        }

        .stat-icon.success {
            background: #d1fae5;
            color: #065f46;
        }

        .stat-details {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 12px;
            color: #9ca3af;
            font-weight: 500;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #1a1d1f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-alt"></i> 로그 뷰어</h1>
            <div class="header-actions">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> 돌아가기
                </a>
                <button onclick="location.reload()" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i> 새로고침
                </button>
                <a href="?clear=1" class="btn btn-danger" onclick="return confirm('정말로 로그를 삭제하시겠습니까?')">
                    <i class="fas fa-trash"></i> 로그 삭제
                </a>
            </div>
        </div>

        <?php if (file_exists($log_file)): ?>
        <div class="stats">
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-file"></i>
                </div>
                <div class="stat-details">
                    <span class="stat-label">파일 크기</span>
                    <span class="stat-value"><?php echo number_format(filesize($log_file) / 1024, 2); ?> KB</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-details">
                    <span class="stat-label">총 라인 수</span>
                    <span class="stat-value"><?php echo number_format(count(file($log_file))); ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="log-container">
            <div class="log-content" id="logContent">
                <?php if (empty(trim($log_content))): ?>
                <div class="empty-log">
                    <i class="fas fa-inbox"></i>
                    <div>로그가 비어있습니다</div>
                </div>
                <?php else: ?>
                <?php
                // 로그 하이라이팅
                $highlighted = $log_content;
                $highlighted = preg_replace('/\[.*?ERROR.*?\]/i', '<span class="highlight-error">$0</span>', $highlighted);
                $highlighted = preg_replace('/\[.*?WARNING.*?\]/i', '<span class="highlight-warning">$0</span>', $highlighted);
                $highlighted = preg_replace('/성공|SUCCESS/i', '<span class="highlight-success">$0</span>', $highlighted);
                $highlighted = preg_replace('/=== .* ===/i', '<span class="highlight-info">$0</span>', $highlighted);
                echo $highlighted;
                ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // 로그 자동 스크롤 (맨 아래로)
        const logContent = document.getElementById('logContent');
        if (logContent.scrollHeight > logContent.clientHeight) {
            logContent.scrollTop = logContent.scrollHeight;
        }

        // 5초마다 자동 새로고침 (선택사항)
        // setInterval(() => location.reload(), 5000);
    </script>
</body>
</html>