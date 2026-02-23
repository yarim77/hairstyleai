<?php
/**
 * ========================================
 * 푸시 알림 API 문서
 * ========================================
 * @file    api_docs.php
 * @version 2.0.0
 * @updated 2025-01-25
 */

// 인증 시스템 로드 (선택적)
if (file_exists(__DIR__ . '/auth.php')) {
    require_once __DIR__ . '/auth.php';
}

// config.php 로드
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
    $config = get_push_config();
    $project_id = $config['project_id'] ?? 'hairwang-web-app';
    $topic_name = $config['topic_name'] ?? 'news_test';
    $default_url = $config['default_url'] ?? 'https://hairwang.com';
    $arg3_value = $config['arg3_value'] ?? 'HAIRWANG';
} else {
    $project_id = 'hairwang-web-app';
    $topic_name = 'news_test';
    $default_url = 'https://hairwang.com';
    $arg3_value = 'HAIRWANG';
}

$base_url = 'https://hairwang.com/new_http_v1_mars';
$api_key = 'mars_push_api_key_2025';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>푸시 알림 API 문서</title>
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <style>
        :root {
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --bg-code: #0d1117;
            --bg-sidebar: #1e293b;
            --border-color: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent: #6366f1;
            --accent-light: #818cf8;
            --accent-glow: rgba(99, 102, 241, 0.3);
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --info: #3b82f6;
            --android: #3ddc84;
            --ios: #007aff;
            --radius: 12px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-main);
            color: var(--text-primary);
            line-height: 1.7;
            min-height: 100vh;
        }

        /* Layout */
        .layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            padding: 24px 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 24px 24px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-primary);
        }

        .sidebar-logo i {
            font-size: 28px;
            color: var(--accent);
        }

        .sidebar-logo span {
            font-size: 18px;
            font-weight: 700;
        }

        .sidebar-version {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .nav-section {
            margin-bottom: 24px;
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 24px;
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: rgba(99, 102, 241, 0.1);
            color: var(--text-primary);
        }

        .nav-link.active {
            background: rgba(99, 102, 241, 0.15);
            color: var(--accent-light);
            border-left-color: var(--accent);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 14px;
        }

        .nav-badge {
            margin-left: auto;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }

        .nav-badge.get { background: var(--success); color: white; }
        .nav-badge.post { background: var(--info); color: white; }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 24px;
            padding: 12px 16px;
            background: var(--accent);
            color: white;
            text-decoration: none;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background: var(--accent-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--accent-glow);
        }

        /* Main Content */
        .main-content {
            padding: 40px 60px;
            max-width: 1000px;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 12px;
            background: linear-gradient(135deg, var(--accent-light), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-desc {
            font-size: 16px;
            color: var(--text-secondary);
        }

        /* Section */
        .section {
            margin-bottom: 48px;
            scroll-margin-top: 24px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: var(--accent);
        }

        .section-desc {
            color: var(--text-secondary);
            margin-bottom: 24px;
            font-size: 15px;
        }

        /* Card */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-header-title {
            font-weight: 600;
            font-size: 15px;
        }

        .card-body {
            padding: 20px;
        }

        /* Endpoint Box */
        .endpoint-box {
            background: var(--bg-code);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .method-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .method-badge.post { background: var(--info); color: white; }
        .method-badge.get { background: var(--success); color: white; }

        .endpoint-url {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            color: var(--text-primary);
            flex: 1;
        }

        .copy-btn {
            padding: 8px 12px;
            background: transparent;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-btn:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        /* Table */
        .params-table {
            width: 100%;
            border-collapse: collapse;
        }

        .params-table th,
        .params-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .params-table th {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(0,0,0,0.2);
        }

        .params-table td {
            font-size: 14px;
        }

        .params-table tr:last-child td {
            border-bottom: none;
        }

        .param-name {
            font-family: 'JetBrains Mono', monospace;
            color: var(--accent-light);
            font-weight: 500;
        }

        .param-type {
            font-size: 12px;
            color: var(--text-muted);
            background: rgba(255,255,255,0.05);
            padding: 2px 8px;
            border-radius: 4px;
        }

        .param-required {
            color: var(--error);
            font-size: 11px;
            font-weight: 600;
        }

        .param-optional {
            color: var(--text-muted);
            font-size: 11px;
        }

        /* Code Block */
        .code-tabs {
            display: flex;
            gap: 4px;
            padding: 12px 16px;
            background: rgba(0,0,0,0.3);
            border-bottom: 1px solid var(--border-color);
            overflow-x: auto;
        }

        .code-tab {
            padding: 8px 16px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .code-tab:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-secondary);
        }

        .code-tab.active {
            background: var(--accent);
            color: white;
        }

        .code-panel {
            display: none;
            position: relative;
        }

        .code-panel.active {
            display: block;
        }

        .code-block {
            margin: 0;
            padding: 20px;
            background: var(--bg-code);
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .code-copy-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 8px 12px;
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 6px;
            color: var(--text-secondary);
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .code-copy-btn:hover {
            background: var(--accent);
            color: white;
        }

        /* Response Box */
        .response-box {
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .response-header {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 13px;
        }

        .response-header.success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }

        .response-header.error {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error);
        }

        /* Alert */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .alert i {
            font-size: 18px;
            margin-top: 2px;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: var(--info);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: var(--warning);
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .alert-text {
            font-size: 14px;
            color: var(--text-secondary);
        }

        /* Auth Card */
        .auth-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 24px;
        }

        .auth-card-icon {
            width: 48px;
            height: 48px;
            background: rgba(99, 102, 241, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .auth-card-icon i {
            font-size: 20px;
            color: var(--accent);
        }

        .auth-card-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .auth-card-desc {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .auth-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            background: var(--bg-code);
            padding: 12px 16px;
            border-radius: 8px;
            color: var(--accent-light);
        }

        /* Config Table */
        .config-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .config-item {
            background: var(--bg-code);
            padding: 16px;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
        }

        .config-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .config-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            color: var(--accent-light);
        }

        /* Platform Tabs */
        .platform-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
        }

        .platform-tab {
            padding: 12px 24px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            color: var(--text-secondary);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .platform-tab:hover {
            border-color: var(--accent);
        }

        .platform-tab.active {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        .platform-tab i.fa-android { color: var(--android); }
        .platform-tab i.fa-apple { color: var(--ios); }
        .platform-tab.active i { color: white; }

        .platform-content {
            display: none;
        }

        .platform-content.active {
            display: block;
        }

        /* Footer */
        .footer {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid var(--border-color);
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
            }

            .main-content {
                padding: 24px;
            }

            .auth-methods {
                grid-template-columns: 1fr;
            }

            .config-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-main);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="sidebar-logo">
                    <i class="fas fa-bell"></i>
                    <div>
                        <span>Push API</span>
                        <div class="sidebar-version">v2.1.0</div>
                    </div>
                </a>
            </div>

            <nav>
                <div class="nav-section">
                    <div class="nav-section-title">시작하기</div>
                    <a href="#auth" class="nav-link active">
                        <i class="fas fa-key"></i>
                        인증 방법
                    </a>
                    <a href="#config" class="nav-link">
                        <i class="fas fa-cog"></i>
                        설정 정보
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">API 엔드포인트</div>
                    <a href="#individual" class="nav-link">
                        <i class="fas fa-user"></i>
                        개별 푸시
                        <span class="nav-badge post">POST</span>
                    </a>
                    <a href="#group" class="nav-link">
                        <i class="fas fa-users"></i>
                        전체 푸시
                        <span class="nav-badge post">POST</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">플랫폼별 예제</div>
                    <a href="#ios" class="nav-link">
                        <i class="fab fa-apple"></i>
                        iOS (Swift)
                    </a>
                    <a href="#android" class="nav-link">
                        <i class="fab fa-android"></i>
                        Android (Kotlin)
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">응답</div>
                    <a href="#response" class="nav-link">
                        <i class="fas fa-exchange-alt"></i>
                        응답 형식
                    </a>
                    <a href="#errors" class="nav-link">
                        <i class="fas fa-exclamation-triangle"></i>
                        주의사항
                    </a>
                </div>
            </nav>

            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                푸시 전송 페이지
            </a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="page-header">
                <h1 class="page-title">푸시 알림 API 문서</h1>
                <p class="page-desc">FCM을 통한 푸시 알림 전송 API의 사용법을 안내합니다.</p>
            </header>

            <!-- Auth Section -->
            <section id="auth" class="section">
                <h2 class="section-title">
                    <i class="fas fa-key"></i>
                    인증 방법
                </h2>
                <p class="section-desc">API 호출 시 아래 2가지 인증 방법 중 하나를 선택할 수 있습니다.</p>

                <div class="auth-methods">
                    <div class="auth-card">
                        <div class="auth-card-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h3 class="auth-card-title">세션 인증</h3>
                        <p class="auth-card-desc">웹 브라우저에서 로그인 후 세션 쿠키로 자동 인증됩니다.</p>
                        <div class="auth-code"><?php echo $base_url; ?>/login.php</div>
                    </div>

                    <div class="auth-card">
                        <div class="auth-card-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <h3 class="auth-card-title">API 키 인증</h3>
                        <p class="auth-card-desc">프로그램에서 호출 시 HTTP 헤더에 API 키를 포함합니다.</p>
                        <div class="auth-code">X-API-Key: <?php echo $api_key; ?></div>
                    </div>
                </div>
            </section>

            <!-- Config Section -->
            <section id="config" class="section">
                <h2 class="section-title">
                    <i class="fas fa-cog"></i>
                    설정 정보
                </h2>

                <div class="config-grid">
                    <div class="config-item">
                        <div class="config-label">Firebase 프로젝트 ID</div>
                        <div class="config-value"><?php echo htmlspecialchars($project_id); ?></div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">토픽 이름 (전체 푸시용)</div>
                        <div class="config-value"><?php echo htmlspecialchars($topic_name); ?></div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">기본 URL</div>
                        <div class="config-value"><?php echo htmlspecialchars($default_url); ?></div>
                    </div>
                    <div class="config-item">
                        <div class="config-label">arg3 값 (보낸 사람)</div>
                        <div class="config-value"><?php echo htmlspecialchars($arg3_value); ?></div>
                    </div>
                </div>
            </section>

            <!-- Individual Push Section -->
            <section id="individual" class="section">
                <h2 class="section-title">
                    <i class="fas fa-user"></i>
                    개별 푸시 알림
                </h2>
                <p class="section-desc">특정 사용자에게 푸시 알림을 전송합니다.</p>

                <div class="endpoint-box">
                    <span class="method-badge post">POST</span>
                    <span class="endpoint-url"><?php echo $base_url; ?>/push_api.php</span>
                    <button class="copy-btn" onclick="copyToClipboard('<?php echo $base_url; ?>/push_api.php')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-asterisk" style="color: var(--error);"></i>
                        <span class="card-header-title">필수 파라미터</span>
                    </div>
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>파라미터</th>
                                <th>타입</th>
                                <th>설명</th>
                                <th>예시</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="param-name">app</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>기기 유형</td>
                                <td><code>android</code> 또는 <code>ios</code></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">user_token</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>FCM 토큰</td>
                                <td><code>dXYz...</code> (152자)</td>
                            </tr>
                            <tr>
                                <td><span class="param-name">title</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>알림 제목</td>
                                <td><code>새로운 메시지</code></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">memo</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>알림 내용</td>
                                <td><code>안녕하세요</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-plus-circle" style="color: var(--text-muted);"></i>
                        <span class="card-header-title">선택 파라미터</span>
                    </div>
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>파라미터</th>
                                <th>타입</th>
                                <th>설명</th>
                                <th>기본값</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="param-name">url</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>클릭 시 이동 URL</td>
                                <td><code><?php echo htmlspecialchars($default_url); ?>?call=push</code></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">file_url</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>이미지 URL</td>
                                <td><span class="param-optional">없음</span></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">arg1</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>커스텀 데이터 1</td>
                                <td><code>$arg1</code></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">arg2</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>팝업 표시 여부</td>
                                <td><code>1</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Code Examples -->
                <div class="card">
                    <div class="code-tabs">
                        <button class="code-tab active" onclick="showCodeTab(this, 'curl-individual')">cURL</button>
                        <button class="code-tab" onclick="showCodeTab(this, 'php-individual')">PHP</button>
                        <button class="code-tab" onclick="showCodeTab(this, 'js-individual')">JavaScript</button>
                        <button class="code-tab" onclick="showCodeTab(this, 'python-individual')">Python</button>
                        <button class="code-tab" onclick="showCodeTab(this, 'powershell-individual')">PowerShell</button>
                    </div>

                    <div id="curl-individual" class="code-panel active">
                        <button class="code-copy-btn" onclick="copyCode(this)">
                            <i class="fas fa-copy"></i> 복사
                        </button>
                        <pre class="code-block"><code class="language-bash">curl -X POST "<?php echo $base_url; ?>/push_api.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "X-API-Key: <?php echo $api_key; ?>" \
  -d "app=android" \
  -d "user_token=YOUR_FCM_TOKEN" \
  -d "title=새로운 메시지" \
  -d "memo=안녕하세요. 테스트 메시지입니다." \
  -d "url=<?php echo $default_url; ?>/board.php?bo_table=notice" \
  -d "file_url=<?php echo $default_url; ?>/images/notification.jpg"</code></pre>
                    </div>

                    <div id="php-individual" class="code-panel">
                        <button class="code-copy-btn" onclick="copyCode(this)">
                            <i class="fas fa-copy"></i> 복사
                        </button>
                        <pre class="code-block"><code class="language-php">&lt;?php
$data = [
    'app' => 'android',
    'user_token' => 'YOUR_FCM_TOKEN',
    'title' => '새로운 메시지',
    'memo' => '안녕하세요. 테스트 메시지입니다.',
    'url' => '<?php echo $default_url; ?>/board.php?bo_table=notice',
    'file_url' => '<?php echo $default_url; ?>/images/notification.jpg'
];

$ch = curl_init('<?php echo $base_url; ?>/push_api.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: <?php echo $api_key; ?>'
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
print_r($result);
?&gt;</code></pre>
                    </div>

                    <div id="js-individual" class="code-panel">
                        <button class="code-copy-btn" onclick="copyCode(this)">
                            <i class="fas fa-copy"></i> 복사
                        </button>
                        <pre class="code-block"><code class="language-javascript">const data = {
    app: 'android',
    user_token: 'YOUR_FCM_TOKEN',
    title: '새로운 메시지',
    memo: '안녕하세요. 테스트 메시지입니다.',
    url: '<?php echo $default_url; ?>/board.php?bo_table=notice',
    file_url: '<?php echo $default_url; ?>/images/notification.jpg'
};

fetch('<?php echo $base_url; ?>/push_api.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-API-Key': '<?php echo $api_key; ?>'
    },
    body: new URLSearchParams(data)
})
.then(response => response.json())
.then(result => console.log(result))
.catch(error => console.error('Error:', error));</code></pre>
                    </div>

                    <div id="python-individual" class="code-panel">
                        <button class="code-copy-btn" onclick="copyCode(this)">
                            <i class="fas fa-copy"></i> 복사
                        </button>
                        <pre class="code-block"><code class="language-python">import requests

data = {
    'app': 'android',
    'user_token': 'YOUR_FCM_TOKEN',
    'title': '새로운 메시지',
    'memo': '안녕하세요. 테스트 메시지입니다.',
    'url': '<?php echo $default_url; ?>/board.php?bo_table=notice',
    'file_url': '<?php echo $default_url; ?>/images/notification.jpg'
}

headers = {
    'X-API-Key': '<?php echo $api_key; ?>'
}

response = requests.post(
    '<?php echo $base_url; ?>/push_api.php',
    headers=headers,
    data=data
)

print(response.json())</code></pre>
                    </div>

                    <div id="powershell-individual" class="code-panel">
                        <button class="code-copy-btn" onclick="copyCode(this)">
                            <i class="fas fa-copy"></i> 복사
                        </button>
                        <pre class="code-block"><code class="language-powershell">$body = @{
    app = "android"
    user_token = "YOUR_FCM_TOKEN"
    title = "새로운 메시지"
    memo = "안녕하세요. 테스트 메시지입니다."
    url = "<?php echo $default_url; ?>/board.php?bo_table=notice"
    file_url = "<?php echo $default_url; ?>/images/notification.jpg"
}

Invoke-RestMethod -Uri "<?php echo $base_url; ?>/push_api.php" `
    -Method POST `
    -Headers @{"X-API-Key"="<?php echo $api_key; ?>"} `
    -Body $body</code></pre>
                    </div>
                </div>
            </section>

            <!-- Group Push Section -->
            <section id="group" class="section">
                <h2 class="section-title">
                    <i class="fas fa-users"></i>
                    전체 푸시 알림
                </h2>
                <p class="section-desc">토픽을 구독한 모든 사용자에게 푸시 알림을 전송합니다.</p>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="alert-content">
                        <div class="alert-title">주의사항</div>
                        <div class="alert-text">전체 푸시를 받으려면 앱에서 <code><?php echo htmlspecialchars($topic_name); ?></code> 토픽을 구독해야 합니다.</div>
                    </div>
                </div>

                <div class="endpoint-box">
                    <span class="method-badge post">POST</span>
                    <span class="endpoint-url"><?php echo $base_url; ?>/push_api.php</span>
                    <button class="copy-btn" onclick="copyToClipboard('<?php echo $base_url; ?>/push_api.php')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-asterisk" style="color: var(--error);"></i>
                        <span class="card-header-title">필수 파라미터</span>
                    </div>
                    <table class="params-table">
                        <thead>
                            <tr>
                                <th>파라미터</th>
                                <th>타입</th>
                                <th>설명</th>
                                <th>예시</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="param-name">push_type</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>푸시 타입</td>
                                <td><code>group</code></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">title</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>알림 제목</td>
                                <td><code>공지사항</code></td>
                            </tr>
                            <tr>
                                <td><span class="param-name">memo</span></td>
                                <td><span class="param-type">string</span></td>
                                <td>알림 내용</td>
                                <td><code>새로운 업데이트</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card">
                    <div class="code-tabs">
                        <button class="code-tab active" onclick="showCodeTab(this, 'curl-group')">cURL</button>
                        <button class="code-tab" onclick="showCodeTab(this, 'php-group')">PHP</button>
                    </div>

                    <div id="curl-group" class="code-panel active">
                        <button class="code-copy-btn" onclick="copyCode(this)">
                            <i class="fas fa-copy"></i> 복사
                        </button>
                        <pre class="code-block"><code class="language-bash">curl -X POST "<?php echo $base_url; ?>/push_api.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "X-API-Key: <?php echo $api_key; ?>" \
  -d "push_type=group" \
  -d "title=공지사항" \
  -d "memo=새로운 업데이트가 있습니다." \
  -d "url=<?php echo $default_url; ?>/notice" \
  -d "file_url=<?php echo $default_url; ?>/images/update.jpg"</code></pre>
                    </div>

                    <div id="php-group" class="code-panel">
                        <button class="code-copy-btn" onclick="copyCode(this)">
                            <i class="fas fa-copy"></i> 복사
                        </button>
                        <pre class="code-block"><code class="language-php">&lt;?php
$data = [
    'push_type' => 'group',
    'title' => '공지사항',
    'memo' => '새로운 업데이트가 있습니다.',
    'url' => '<?php echo $default_url; ?>/notice',
    'file_url' => '<?php echo $default_url; ?>/images/update.jpg'
];

$ch = curl_init('<?php echo $base_url; ?>/push_api.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: <?php echo $api_key; ?>'
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
print_r($result);
?&gt;</code></pre>
                    </div>
                </div>
            </section>

            <!-- iOS Section -->
            <section id="ios" class="section">
                <h2 class="section-title">
                    <i class="fab fa-apple"></i>
                    iOS (Swift)
                </h2>
                <p class="section-desc">Swift URLSession을 사용한 푸시 알림 전송 예제입니다.</p>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-code"></i>
                        <span class="card-header-title">Swift URLSession</span>
                    </div>
                    <div class="code-panel active">
                        <button class="code-copy-btn" onclick="copyCode(this)">
                            <i class="fas fa-copy"></i> 복사
                        </button>
                        <pre class="code-block"><code class="language-swift">import Foundation

func sendPushNotification() {
    let url = URL(string: "<?php echo $base_url; ?>/push_api.php")!
    var request = URLRequest(url: url)
    request.httpMethod = "POST"
    request.setValue("<?php echo $api_key; ?>", forHTTPHeaderField: "X-API-Key")
    request.setValue("application/x-www-form-urlencoded", forHTTPHeaderField: "Content-Type")

    let parameters = [
        "app": "ios",
        "user_token": "YOUR_FCM_TOKEN",
        "title": "새로운 메시지",
        "memo": "안녕하세요. 테스트 메시지입니다.",
        "url": "<?php echo $default_url; ?>/board.php?bo_table=notice",
        "file_url": "<?php echo $default_url; ?>/images/notification.jpg"
    ]

    let bodyString = parameters.map { "\($0.key)=\($0.value)" }.joined(separator: "&")
    request.httpBody = bodyString.addingPercentEncoding(withAllowedCharacters: .urlQueryAllowed)?.data(using: .utf8)

    let task = URLSession.shared.dataTask(with: request) { data, response, error in
        guard let data = data, error == nil else {
            print("Error: \(error?.localizedDescription ?? "Unknown error")")
            return
        }

        if let json = try? JSONSerialization.jsonObject(with: data) as? [String: Any] {
            print("Response: \(json)")
        }
    }

    task.resume()
}</code></pre>
                    </div>
                </div>
            </section>

            <!-- Android Section -->
            <section id="android" class="section">
                <h2 class="section-title">
                    <i class="fab fa-android"></i>
                    Android (Kotlin)
                </h2>
                <p class="section-desc">Kotlin Retrofit을 사용한 푸시 알림 전송 예제입니다.</p>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-code"></i>
                        <span class="card-header-title">Retrofit Interface</span>
                    </div>
                    <div class="code-panel active">
                        <button class="code-copy-btn" onclick="copyCode(this)">
                            <i class="fas fa-copy"></i> 복사
                        </button>
                        <pre class="code-block"><code class="language-kotlin">import retrofit2.Call
import retrofit2.http.*

interface PushApiService {
    @FormUrlEncoded
    @POST("push_api.php")
    fun sendIndividualPush(
        @Header("X-API-Key") apiKey: String,
        @Field("app") app: String,
        @Field("user_token") userToken: String,
        @Field("title") title: String,
        @Field("memo") memo: String,
        @Field("url") url: String? = null,
        @Field("file_url") fileUrl: String? = null
    ): Call&lt;PushResponse&gt;
}

data class PushResponse(
    val success: Boolean,
    val message: String,
    val data: Map&lt;String, Any&gt;?,
    val timestamp: String
)</code></pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-play"></i>
                        <span class="card-header-title">사용 예제</span>
                    </div>
                    <div class="code-panel active">
                        <button class="code-copy-btn" onclick="copyCode(this)">
                            <i class="fas fa-copy"></i> 복사
                        </button>
                        <pre class="code-block"><code class="language-kotlin">fun sendPushNotification(fcmToken: String) {
    RetrofitClient.pushApi.sendIndividualPush(
        apiKey = "<?php echo $api_key; ?>",
        app = "android",
        userToken = fcmToken,
        title = "새로운 메시지",
        memo = "안녕하세요. 테스트 메시지입니다.",
        url = "<?php echo $default_url; ?>/board.php?bo_table=notice",
        fileUrl = "<?php echo $default_url; ?>/images/notification.jpg"
    ).enqueue(object : Callback&lt;PushResponse&gt; {
        override fun onResponse(call: Call&lt;PushResponse&gt;, response: Response&lt;PushResponse&gt;) {
            if (response.isSuccessful) {
                val result = response.body()
                println("Success: \${result?.message}")
            }
        }

        override fun onFailure(call: Call&lt;PushResponse&gt;, t: Throwable) {
            println("Failure: \${t.message}")
        }
    })
}</code></pre>
                    </div>
                </div>
            </section>

            <!-- Response Section -->
            <section id="response" class="section">
                <h2 class="section-title">
                    <i class="fas fa-exchange-alt"></i>
                    응답 형식
                </h2>

                <div class="response-box">
                    <div class="response-header success">
                        <i class="fas fa-check-circle"></i>
                        성공 응답 (200 OK)
                    </div>
                    <pre class="code-block"><code class="language-json">{
    "success": true,
    "message": "푸시 알림이 성공적으로 전송되었습니다.",
    "data": {
        "app": "android",
        "user_token": "dXYz1234567890...",
        "title": "새로운 메시지",
        "memo": "안녕하세요. 테스트 메시지입니다.",
        "url": "<?php echo $default_url; ?>/board.php?bo_table=notice?call=push",
        "file_url": "<?php echo $default_url; ?>/images/notification.jpg",
        "arg1": "$arg1",
        "arg2": "1",
        "arg3": "<?php echo $arg3_value; ?>"
    },
    "timestamp": "2025-01-25 14:30:25"
}</code></pre>
                </div>

                <div class="response-box" style="margin-top: 24px;">
                    <div class="response-header error">
                        <i class="fas fa-times-circle"></i>
                        실패 응답
                    </div>
                    <pre class="code-block"><code class="language-json">{
    "success": false,
    "message": "푸시 알림 전송에 실패했습니다.",
    "data": {
        "app": "android",
        "user_token": "dXYz1234567890...",
        "error": "Invalid registration token",
        "firebase_config": "/path/to/firebase.json",
        "project_id": "<?php echo $project_id; ?>"
    },
    "timestamp": "2025-01-25 14:30:25"
}</code></pre>
                </div>
            </section>

            <!-- Errors Section -->
            <section id="errors" class="section">
                <h2 class="section-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    주의사항
                </h2>

                <div class="card">
                    <div class="card-body">
                        <ul style="list-style: none; padding: 0;">
                            <li style="padding: 12px 0; border-bottom: 1px solid var(--border-color); display: flex; gap: 12px;">
                                <i class="fas fa-key" style="color: var(--accent); margin-top: 4px;"></i>
                                <div>
                                    <strong>토큰 유효성</strong>
                                    <p style="color: var(--text-secondary); margin-top: 4px; font-size: 14px;">FCM 토큰은 152자 이상의 문자열입니다. 토큰이 만료되거나 잘못된 경우 전송이 실패합니다.</p>
                                </div>
                            </li>
                            <li style="padding: 12px 0; border-bottom: 1px solid var(--border-color); display: flex; gap: 12px;">
                                <i class="fas fa-link" style="color: var(--accent); margin-top: 4px;"></i>
                                <div>
                                    <strong>URL 인코딩</strong>
                                    <p style="color: var(--text-secondary); margin-top: 4px; font-size: 14px;">파라미터에 특수문자가 포함된 경우 URL 인코딩이 필요합니다.</p>
                                </div>
                            </li>
                            <li style="padding: 12px 0; border-bottom: 1px solid var(--border-color); display: flex; gap: 12px;">
                                <i class="fas fa-image" style="color: var(--accent); margin-top: 4px;"></i>
                                <div>
                                    <strong>이미지 크기</strong>
                                    <p style="color: var(--text-secondary); margin-top: 4px; font-size: 14px;"><code>file_url</code> 이미지는 1MB 이하를 권장합니다.</p>
                                </div>
                            </li>
                            <li style="padding: 12px 0; border-bottom: 1px solid var(--border-color); display: flex; gap: 12px;">
                                <i class="fas fa-rss" style="color: var(--accent); margin-top: 4px;"></i>
                                <div>
                                    <strong>토픽 구독</strong>
                                    <p style="color: var(--text-secondary); margin-top: 4px; font-size: 14px;">전체 푸시를 받으려면 앱에서 <code><?php echo htmlspecialchars($topic_name); ?></code> 토픽을 구독해야 합니다.</p>
                                </div>
                            </li>
                            <li style="padding: 12px 0; display: flex; gap: 12px;">
                                <i class="fas fa-shield-alt" style="color: var(--accent); margin-top: 4px;"></i>
                                <div>
                                    <strong>인증 보안</strong>
                                    <p style="color: var(--text-secondary); margin-top: 4px; font-size: 14px;">API 키는 서버 측에서만 사용하고, 클라이언트 앱에 노출하지 마세요.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <footer class="footer">
                <p>문제가 발생하거나 추가 정보가 필요하면 시스템 관리자에게 문의하세요.</p>
                <p style="margin-top: 8px;">
                    <a href="index.php" style="color: var(--accent);">
                        <i class="fas fa-arrow-left"></i> 푸시 전송 페이지로 돌아가기
                    </a>
                </p>
            </footer>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        // Initialize highlight.js
        hljs.highlightAll();

        // Code tab switching
        function showCodeTab(btn, panelId) {
            const card = btn.closest('.card');
            card.querySelectorAll('.code-tab').forEach(t => t.classList.remove('active'));
            card.querySelectorAll('.code-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(panelId).classList.add('active');
        }

        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('클립보드에 복사되었습니다.');
            });
        }

        function copyCode(btn) {
            const code = btn.nextElementSibling.textContent;
            navigator.clipboard.writeText(code).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> 복사됨';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            });
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 24px;
                right: 24px;
                background: var(--success);
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 500;
                z-index: 9999;
                animation: slideIn 0.3s ease;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Smooth scroll for nav links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                    // Update active state
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Update nav active state on scroll
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('.section');
            let current = '';

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });

            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>