<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>차단 기능 문서</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        .block-doc-container {
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .block-doc-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .block-doc-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .block-doc-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .block-doc-tabs {
            display: flex;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        .block-doc-tab {
            flex: 1;
            padding: 18px 24px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        .block-doc-tab:hover {
            background: #e9ecef;
            color: #495057;
        }
        .block-doc-tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
            background: #fff;
        }
        .block-doc-content {
            padding: 40px;
            min-height: 500px;
            max-height: 800px;
            overflow-y: auto;
            background: #fff;
        }
        .block-doc-panel {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .block-doc-panel.active {
            display: block;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .block-doc-content h1 {
            color: #667eea;
            font-size: 32px;
            margin: 0 0 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #e9ecef;
        }
        .block-doc-content h2 {
            color: #495057;
            font-size: 24px;
            margin: 30px 0 15px;
            padding-top: 20px;
        }
        .block-doc-content h3 {
            color: #6c757d;
            font-size: 20px;
            margin: 25px 0 12px;
        }
        .block-doc-content h4 {
            color: #868e96;
            font-size: 18px;
            margin: 20px 0 10px;
        }
        .block-doc-content p {
            line-height: 1.8;
            color: #495057;
            margin: 12px 0;
        }
        .block-doc-content ul, .block-doc-content ol {
            margin: 15px 0;
            padding-left: 30px;
        }
        .block-doc-content li {
            line-height: 1.8;
            color: #495057;
            margin: 8px 0;
        }
        .block-doc-content code {
            background: #f1f3f5;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #e83e8c;
        }
        .block-doc-content a {
            color: #667eea;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: all 0.2s;
        }
        .block-doc-content a:hover {
            border-bottom-color: #667eea;
        }
        .block-doc-content strong {
            color: #212529;
            font-weight: 600;
        }
        .block-doc-content em {
            color: #6c757d;
            font-style: italic;
        }
        .block-doc-content pre {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 16px;
            overflow-x: auto;
            margin: 20px 0;
        }
        .block-doc-content pre code {
            background: transparent;
            padding: 0;
            color: #495057;
            font-size: 14px;
            line-height: 1.6;
        }
        .block-doc-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 6px;
            overflow: hidden;
        }
        .block-doc-content table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        .block-doc-content table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .block-doc-content table tr:hover {
            background: #f8f9fa;
        }
        .block-doc-content::-webkit-scrollbar {
            width: 8px;
        }
        .block-doc-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .block-doc-content::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 4px;
        }
        .block-doc-content::-webkit-scrollbar-thumb:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="block-doc-container">
        <div class="block-doc-header">
            <h1>🚫 사용자 차단 기능 문서</h1>
            <p>iOS 앱 심사 대응 및 차단 기능 구현 가이드</p>
        </div>
        
        <div class="block-doc-tabs">
            <button class="block-doc-tab active" onclick="showTab('analysis')">
                📋 분석 문서
            </button>
            <button class="block-doc-tab" onclick="showTab('plan')">
                📝 구현 계획
            </button>
        </div>
        
        <div class="block-doc-content">
            <div id="analysis-panel" class="block-doc-panel active">
                <h1>iOS 앱 심사 리젝 사항 분석: 사용자 차단 기능 (웹 전용)</h1>
                
                <h2>📋 리젝 사항 요약</h2>
                <p><strong>가이드라인</strong>: 1.2 - Safety - User-Generated Content<br>
                <strong>요구사항</strong>: 사용자가 악의적인 사용자를 차단할 수 있는 메커니즘 (A mechanism for users to block abusive users)<br>
                <strong>적용 범위</strong>: 웹 소스만 (앱은 패키징이므로 상관없음)</p>
                
                <h2>🎯 상세 요구사항</h2>
                <h3>1. 사용자별 차단 기능</h3>
                <ul>
                    <li>✅ 사용자마다 개별 차단 기능</li>
                    <li>✅ 마이페이지에 차단 리스트 표시</li>
                    <li>✅ 차단 리스트 항목: 차단 일자, 복구 일자, 차단 이유</li>
                    <li>✅ 복구 버튼 (차단 해제)</li>
                    <li>✅ 관리자: 모든 사용자의 차단 리스트 조회 가능</li>
                    <li>✅ 사용자: 자신의 차단 리스트만 조회</li>
                    <li>✅ 관리자용 검색 기능: 날짜별, 아이디, 이름, 사유 검색</li>
                </ul>
                
                <h3>2. 별도 폴더 구조</h3>
                <ul>
                    <li>✅ 재사용 가능한 모듈 형태로 구현</li>
                    <li>✅ 다른 사이트에 쉽게 적용 가능</li>
                </ul>
                
                <h3>3. 차단 기능 관리 페이지</h3>
                <ul>
                    <li>✅ 관리자 페이지에서 차단 기능 사용 가능 여부 설정</li>
                </ul>
                
                <h3>4. 신고 폼 통합</h3>
                <ul>
                    <li>✅ 신고 버튼 클릭 시 나타나는 폼에 차단 기능 추가</li>
                </ul>
                
                <h2>🔍 현재 시스템 분석</h2>
                <h3>✅ 현재 구현된 기능</h3>
                <p><strong>1. 신고 시스템 (Report System)</strong></p>
                <ul>
                    <li>파일 위치: <code>www/extend/rb.report.extend.php</code></li>
                    <li>게시글/댓글 신고 기능</li>
                    <li>신고 사유 선택 (스팸, 욕설, 음란물, 허위정보, 기타)</li>
                    <li>신고 누적 시 자동 잠금 기능</li>
                </ul>
                
                <h3>❌ 현재 구현되지 않은 기능</h3>
                <ul>
                    <li>사용자 차단 기능 없음</li>
                    <li>차단된 사용자의 게시글/댓글 숨김 기능 없음</li>
                    <li>차단 목록 관리 기능 없음</li>
                </ul>
                
                <h2>💡 가장 빠르고 쉬운 개발 방안</h2>
                <h3>🚀 핵심 전략: 기존 기능 활용 + 차단 버튼만 추가</h3>
                <p>기존 신고 시스템과 동일한 패턴으로 구현하여 최소한의 파일 수정으로 빠르게 개발합니다.</p>
                
                <h3>📦 구현 단계</h3>
                <h4>Step 1: 데이터베이스 테이블 생성</h4>
                <pre><code>CREATE TABLE IF NOT EXISTS `g5_member_block` (
  `bl_id` int(11) NOT NULL AUTO_INCREMENT,
  `mb_id` varchar(20) NOT NULL COMMENT '차단한 사용자 ID',
  `blocked_mb_id` varchar(20) NOT NULL COMMENT '차단당한 사용자 ID',
  `bl_datetime` datetime NOT NULL COMMENT '차단 일시',
  PRIMARY KEY (`bl_id`),
  UNIQUE KEY `unique_block` (`mb_id`, `blocked_mb_id`),
  KEY `idx_mb_id` (`mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</code></pre>
                
                <h4>Step 2: 차단 기능 Extend 파일 생성</h4>
                <p>파일 생성: <code>www/extend/rb.block.extend.php</code></p>
                <ul>
                    <li>차단/해제 AJAX 처리</li>
                    <li>차단 버튼 UI 자동 삽입</li>
                    <li>JavaScript 필터링 로직</li>
                </ul>
                
                <h2>📌 결론</h2>
                <p><strong>신고 기능만으로는 iOS 심사 통과가 불가능합니다.</strong></p>
                <p>iOS 가이드라인 1.2는 명확히 "사용자가 악의적인 사용자를 차단할 수 있는 메커니즘"을 요구합니다.</p>
            </div>
            
            <div id="plan-panel" class="block-doc-panel">
                <h1>사용자 차단 기능 구현 계획서</h1>
                
                <h2>1. 폴더 구조</h2>
                <p><strong>📁 www/rb/rb.mod/block/</strong> (새로 생성)</p>
                <pre><code>www/rb/rb.mod/block/
├── admin/          # 관리자 페이지
├── member/         # 회원 페이지
├── api/            # AJAX API
├── lib/            # 라이브러리 함수
├── extend/         # Extend 파일
├── css/            # 스타일시트
└── js/             # JavaScript</code></pre>
                
                <h2>2. 데이터베이스 설계</h2>
                <h3>차단 테이블</h3>
                <pre><code>CREATE TABLE IF NOT EXISTS `g5_member_block` (
  `bl_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '차단 고유 ID',
  `mb_id` varchar(20) NOT NULL COMMENT '차단한 사용자 ID',
  `blocked_mb_id` varchar(20) NOT NULL COMMENT '차단당한 사용자 ID',
  `bl_reason` varchar(255) DEFAULT NULL COMMENT '차단 사유',
  `bl_datetime` datetime NOT NULL COMMENT '차단 일시',
  `bl_unblock_datetime` datetime DEFAULT NULL COMMENT '복구 일시',
  `bl_status` enum('blocked','unblocked') DEFAULT 'blocked' COMMENT '차단 상태',
  PRIMARY KEY (`bl_id`),
  UNIQUE KEY `unique_block` (`mb_id`, `blocked_mb_id`, `bl_status`),
  KEY `idx_mb_id` (`mb_id`),
  KEY `idx_blocked_mb_id` (`blocked_mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</code></pre>
                
                <h2>3. 파일 구조</h2>
                <table>
                    <tr>
                        <th>파일 경로</th>
                        <th>설명</th>
                        <th>우선순위</th>
                    </tr>
                    <tr>
                        <td>rb/rb.mod/block/lib/block.lib.php</td>
                        <td>차단 관련 함수</td>
                        <td>필수</td>
                    </tr>
                    <tr>
                        <td>rb/rb.mod/block/extend/rb.block.extend.php</td>
                        <td>메인 Extend 파일</td>
                        <td>필수</td>
                    </tr>
                    <tr>
                        <td>rb/rb.mod/block/api/block.php</td>
                        <td>차단/해제 AJAX 처리</td>
                        <td>필수</td>
                    </tr>
                    <tr>
                        <td>rb/rb.mod/block/member/block_list.php</td>
                        <td>마이페이지 차단 리스트</td>
                        <td>필수</td>
                    </tr>
                    <tr>
                        <td>rb/rb.mod/block/admin/block_list.php</td>
                        <td>관리자 차단 리스트</td>
                        <td>필수</td>
                    </tr>
                </table>
                
                <h2>4. 기능 상세 명세</h2>
                <h3>4.1 사용자 차단 기능</h3>
                <ul>
                    <li>게시글/댓글 작성자 옆에 차단 버튼 추가</li>
                    <li>프로필 페이지에 차단 버튼 추가</li>
                    <li>신고 폼에 "이 사용자 차단하기" 체크박스 추가</li>
                </ul>
                
                <h3>4.2 마이페이지 차단 리스트</h3>
                <ul>
                    <li>차단한 사용자 목록 표시</li>
                    <li>차단 일자, 복구 일자, 차단 사유 표시</li>
                    <li>복구 버튼으로 차단 해제</li>
                </ul>
                
                <h3>4.3 관리자 차단 리스트</h3>
                <ul>
                    <li>모든 사용자의 차단 리스트 조회</li>
                    <li>검색 기능: 날짜별, 아이디, 이름, 사유</li>
                </ul>
                
                <h2>5. 현재 사이트 적용 방법</h2>
                <h3>Step 1: 폴더 생성</h3>
                <pre><code>mkdir -p www/rb/rb.mod/block/{admin,member,api,lib,extend,css,js}</code></pre>
                
                <h3>Step 2: 데이터베이스 테이블 생성</h3>
                <p>위의 SQL 실행</p>
                
                <h3>Step 3: 파일 복사</h3>
                <p>www/rb/rb.mod/block/ 폴더에 모든 파일 복사</p>
                
                <h2>7. 견적</h2>
                <h3>최종 재견적 (AI 개발 기준)</h3>
                <table>
                    <tr>
                        <th>작업 항목</th>
                        <th>시간</th>
                        <th>단가</th>
                        <th>금액</th>
                    </tr>
                    <tr>
                        <td>데이터베이스 설계</td>
                        <td>0.2시간</td>
                        <td>15,000원</td>
                        <td>3,000원</td>
                    </tr>
                    <tr>
                        <td>라이브러리 함수 개발</td>
                        <td>1시간</td>
                        <td>15,000원</td>
                        <td>15,000원</td>
                    </tr>
                    <tr>
                        <td>Extend 파일 개발</td>
                        <td>1.5시간</td>
                        <td>15,000원</td>
                        <td>22,500원</td>
                    </tr>
                    <tr>
                        <td>API 개발</td>
                        <td>1시간</td>
                        <td>15,000원</td>
                        <td>15,000원</td>
                    </tr>
                    <tr>
                        <td>마이페이지 리스트</td>
                        <td>1.5시간</td>
                        <td>15,000원</td>
                        <td>22,500원</td>
                    </tr>
                    <tr>
                        <td>관리자 리스트</td>
                        <td>2시간</td>
                        <td>15,000원</td>
                        <td>30,000원</td>
                    </tr>
                    <tr>
                        <td>설정 페이지</td>
                        <td>0.5시간</td>
                        <td>15,000원</td>
                        <td>7,500원</td>
                    </tr>
                    <tr>
                        <td>신고 폼 통합</td>
                        <td>0.3시간</td>
                        <td>15,000원</td>
                        <td>4,500원</td>
                    </tr>
                    <tr>
                        <td>CSS/JS 개발</td>
                        <td>1시간</td>
                        <td>15,000원</td>
                        <td>15,000원</td>
                    </tr>
                    <tr>
                        <td>테스트 및 디버깅</td>
                        <td>2시간</td>
                        <td>15,000원</td>
                        <td>30,000원</td>
                    </tr>
                    <tr>
                        <td><strong>총계</strong></td>
                        <td><strong>11시간</strong></td>
                        <td><strong>15,000원</strong></td>
                        <td><strong>165,000원</strong></td>
                    </tr>
                </table>
                
                <p><strong>총 견적: 165,000원 (부가세 별도)</strong><br>
                예상 소요 기간: 약 1.5일</p>
            </div>
        </div>
    </div>

    <script>
    function showTab(tabName) {
        // 모든 탭과 패널 비활성화
        document.querySelectorAll('.block-doc-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.block-doc-panel').forEach(panel => {
            panel.classList.remove('active');
        });
        
        // 선택한 탭과 패널 활성화
        event.target.classList.add('active');
        document.getElementById(tabName + '-panel').classList.add('active');
        
        // 스크롤 맨 위로
        document.querySelector('.block-doc-content').scrollTop = 0;
    }
    </script>
</body>
</html>
