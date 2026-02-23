<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

/***************************************************************
 그누보드 포인트없는 돼지레이싱 게임 설정
 
 포인트와 베팅 시스템 완전 제거
 이름 기반 5마리 돼지 레이싱
 
 버전: 4.0 (포인트 없는 버전)
****************************************************************/

// =================================================================
// 📱 기본 게임 설정
// =================================================================

// 게임 진행 설정
$set_number = 5; // 참여 돼지 총 마리 수 (5마리 고정)
$set_speed = 15; // 돼지 이동 속도
$set_meter = 1000; // 트랙 길이

// =================================================================
// 🎨 UI 개선 함수
// =================================================================

// 개선된 알림 메시지 (모바일 친화적)
function mobile_alert($msg, $url = '') {
    global $g5;
    
    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset={$g5['charset']}\">";
    echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">";
    echo "<style>
        .mobile-alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 10000;
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            max-width: 90%;
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translate(-50%, -30%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }
        .mobile-alert-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
        }
        .alert-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s ease;
        }
        .alert-btn:hover {
            background: #0056b3;
        }
    </style>";
    
    echo "<div class='mobile-alert-overlay'></div>";
    echo "<div class='mobile-alert'>";
    echo "<div style='font-size: 18px; color: #333; margin-bottom: 20px;'>" . nl2br($msg) . "</div>";
    echo "<button class='alert-btn' onclick='closeAlert()'>확인</button>";
    echo "</div>";
    
    echo "<script>
        function closeAlert() {
            document.querySelector('.mobile-alert-overlay').style.display = 'none';
            document.querySelector('.mobile-alert').style.display = 'none';
            " . ($url ? "window.location.href = '{$url}';" : "window.history.back();") . "
        }
        
        // 3초 후 자동 닫기
        setTimeout(closeAlert, 3000);
    </script>";
    
    exit;
}

// =================================================================
// 📱 모바일 최적화 설정
// =================================================================

// 게임 전용 모바일 디바이스 감지
function game_is_mobile() {
    // 그누보드의 is_mobile() 함수를 사용
    if (function_exists('is_mobile')) {
        return is_mobile();
    }
    // 만약 없다면 직접 체크
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

// 터치 이벤트 지원 확인
function game_support_touch() {
    return game_is_mobile();
}

// =================================================================
// 🎉 이벤트 및 특별 기능
// =================================================================

// 랜덤 이벤트 메시지
function get_random_events() {
    $events = array(
        '💤 잠들기',
        '😵 멍때리기',
        '🚀 터보 발동',
        '🔄 역주행',
        '⚡ 번개 스피드',
        '🌪️ 회오리',
        '🎯 집중 모드',
        '🍀 행운의 부스트'
    );
    
    return $events;
}

// =================================================================
// 💡 추가 유틸리티 함수
// =================================================================

// 랜덤 돼지 이름 제안 (사용자가 입력 안 했을 때)
function get_random_pig_names() {
    $names = array(
        array('번개', '천둥', '폭풍', '토네이도', '허리케인'),
        array('로켓', '미사일', '제트', '터보', '부스터'),
        array('체리', '딸기', '사과', '바나나', '멜론'),
        array('해피', '럭키', '스마일', '조이', '써니'),
        array('알파', '베타', '감마', '델타', '오메가')
    );
    
    return $names[array_rand($names)];
}

// 승리 메시지 생성
function get_victory_message($winner_name) {
    $messages = array(
        "{$winner_name}의 환상적인 승리!",
        "챔피언 {$winner_name}!",
        "{$winner_name}가 1등으로 골인!",
        "우승자는 {$winner_name}!",
        "{$winner_name}의 압도적인 승리!"
    );
    
    return $messages[array_rand($messages)];
}

// 게임 통계 (로컬 스토리지용)
function get_game_stats_template() {
    return array(
        'total_games' => 0,
        'winners' => array(),
        'last_played' => null,
        'favorite_names' => array()
    );
}

?>