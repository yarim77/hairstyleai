<?php
// lib/member_level.lib.php
// 회원 활동 레벨 관련 함수 라이브러리

// 레벨별 조건 설정 (DB에서 가져오기)
function get_level_requirements() {
    global $g5;
    
    static $requirements = null;
    
    if($requirements !== null) {
        return $requirements;
    }
    
    // 기본값 설정 (DB에 없는 경우에만 사용)
    $default_requirements = array(
        1 => array('name' => '비회원', 'posts' => 0, 'comments' => 0, 'likes' => 0, 'desc' => '이제 막 시작한 새싹 디자이너'),
        2 => array('name' => '헤린이', 'posts' => 10, 'comments' => 20, 'likes' => 20, 'desc' => '첫 활동을 시작한 신입 활동러'),
        3 => array('name' => '새싹디자이너', 'posts' => 20, 'comments' => 40, 'likes' => 50, 'desc' => '활발하게 활동하는 인기 디자이너'),
        4 => array('name' => '루키 스타', 'posts' => 30, 'comments' => 60, 'likes' => 70, 'desc' => '커뮤니티 고수, 실력자'),
        5 => array('name' => '슈퍼 스타', 'posts' => 40, 'comments' => 80, 'likes' => 100, 'desc' => '전설의 활동왕, 모두가 인정'),
        6 => array('name' => '빡고수', 'posts' => 0, 'comments' => 0, 'likes' => 0, 'desc' => '특별 권한 회원'),
        7 => array('name' => '신의손', 'posts' => 0, 'comments' => 0, 'likes' => 0, 'desc' => '명예 회원'),
        8 => array('name' => '골드회원', 'posts' => 0, 'comments' => 0, 'likes' => 0, 'desc' => '골드 등급 회원'),
        9 => array('name' => '다이아회원', 'posts' => 0, 'comments' => 0, 'likes' => 0, 'desc' => '다이아몬드 등급 회원'),
        10 => array('name' => '최고관리자', 'posts' => 0, 'comments' => 0, 'likes' => 0, 'desc' => '사이트 관리자')
    );
    
    $requirements = array();
    
    // DB에서 설정 가져오기 - 각 레벨별로 개별 처리
    for($i=1; $i<=10; $i++) {
        $sql = "SELECT cf_value FROM {$g5['config_table']} WHERE cf_name = 'level_requirement_{$i}'";
        $row = sql_fetch($sql);
        
        if($row && $row['cf_value']) {
            $decoded = json_decode($row['cf_value'], true);
            if($decoded && is_array($decoded)) {
                // DB에서 가져온 값 사용
                $requirements[$i] = $decoded;
                // desc가 없으면 기본값의 desc 사용
                if(!isset($requirements[$i]['desc']) && isset($default_requirements[$i]['desc'])) {
                    $requirements[$i]['desc'] = $default_requirements[$i]['desc'];
                }
            } else {
                // JSON 디코딩 실패시 기본값 사용
                $requirements[$i] = $default_requirements[$i];
            }
        } else {
            // DB에 데이터가 없으면 기본값 사용
            $requirements[$i] = $default_requirements[$i];
        }
    }
    
    return $requirements;
}

// 레벨 정보 새로고침 (캐시 클리어)
function refresh_level_requirements() {
    // static 변수 초기화를 위해 함수를 다시 호출
    $GLOBALS['requirements'] = null;
    return get_level_requirements();
}

// 회원의 활동 통계 가져오기
function get_member_activity_stats($mb_id) {
    global $g5;
    
    static $stats_cache = array();
    
    if(isset($stats_cache[$mb_id])) {
        return $stats_cache[$mb_id];
    }
    
    $mb_id = sql_real_escape_string($mb_id);
    
    // 게시글 수 (삭제되지 않은 것만)
    $posts = 0;
    $comments = 0;
    
    // 모든 게시판에서 게시글과 댓글 수 계산
    $sql = "SELECT bo_table FROM {$g5['board_table']}";
    $result = sql_query($sql);
    
    while($board = sql_fetch_array($result)) {
        $write_table = $g5['write_prefix'] . $board['bo_table'];
        
        if(sql_table_exists($write_table)) {
            // 게시글 수
            $row = sql_fetch("SELECT COUNT(*) as cnt FROM {$write_table} 
                            WHERE mb_id = '{$mb_id}' AND wr_is_comment = 0");
            $posts += $row['cnt'];
            
            // 댓글 수
            $row = sql_fetch("SELECT COUNT(*) as cnt FROM {$write_table} 
                            WHERE mb_id = '{$mb_id}' AND wr_is_comment = 1");
            $comments += $row['cnt'];
        }
    }
    
    // 좋아요 수 (받은 좋아요)
    $likes = get_member_total_likes($mb_id);
    
    $stats = array(
        'posts' => $posts,
        'comments' => $comments,
        'likes' => $likes
    );
    
    $stats_cache[$mb_id] = $stats;
    
    return $stats;
}

// 회원이 받은 총 좋아요 수 계산
function get_member_total_likes($mb_id) {
    global $g5;
    
    $mb_id = sql_real_escape_string($mb_id);
    $total_likes = 0;
    
    // 모든 게시판 테이블 조회
    $sql = "SELECT bo_table FROM {$g5['board_table']}";
    $result = sql_query($sql);
    
    while($board = sql_fetch_array($result)) {
        $write_table = $g5['write_prefix'] . $board['bo_table'];
        
        if(sql_table_exists($write_table)) {
            // 해당 회원이 작성한 글의 추천수 합계
            $sql2 = "SELECT SUM(wr_good) as good_sum FROM {$write_table} WHERE mb_id = '{$mb_id}'";
            $row = sql_fetch($sql2);
            $total_likes += (int)$row['good_sum'];
        }
    }
    
    return $total_likes;
}

// 회원의 적정 레벨 계산
function calculate_member_level($mb_id) {
    $stats = get_member_activity_stats($mb_id);
    $requirements = get_level_requirements();
    
    $appropriate_level = 1; // 기본 레벨
    
    // 높은 레벨부터 체크하여 조건을 만족하는 최고 레벨 찾기 (1~5 레벨만)
    for($level = 5; $level >= 1; $level--) {
        $req = $requirements[$level];
        
        if($stats['posts'] >= $req['posts'] && 
           $stats['comments'] >= $req['comments'] && 
           $stats['likes'] >= $req['likes']) {
            $appropriate_level = $level;
            break;
        }
    }
    
    return $appropriate_level;
}

// 회원 레벨 자동 업데이트
function update_member_level($mb_id) {
    global $g5, $config;
    
    $mb_id = sql_real_escape_string($mb_id);
    
    // 현재 레벨 조회
    $mb = get_member($mb_id);
    if(!$mb['mb_id']) return false;
    
    $current_level = (int)$mb['mb_level'];
    
    // 레벨 6 이상은 수동 관리 (특별회원, 관리자 등)
    if($current_level >= 6) {
        return false;
    }
    
    $new_level = calculate_member_level($mb_id);
    
    // 레벨이 변경되었을 경우만 업데이트
    if($new_level != $current_level) {
        // 레벨은 자동으로 올라가기만 하고 내려가지 않도록 설정
        if($new_level > $current_level) {
            sql_query("UPDATE {$g5['member_table']} SET mb_level = '{$new_level}' WHERE mb_id = '{$mb_id}'");
            
            // 레벨 변경 로그 기록
            $requirements = get_level_requirements();
            $old_name = $requirements[$current_level]['name'];
            $new_name = $requirements[$new_level]['name'];
            
            // 레벨 변경 로그 (선택사항)
            sql_query("INSERT INTO {$g5['board_new_table']} SET
                bn_id = '" . time() . "',
                mb_id = '{$mb_id}',
                bn_datetime = '" . G5_TIME_YMDHIS . "',
                bn_memo = '레벨업: {$old_name} → {$new_name}'
            ");
            
            // 알림 발송
            send_level_up_notification($mb_id, $old_name, $new_name, $new_level);
            
            return true;
        }
    }
    
    return false;
}

// 레벨업 알림 발송
function send_level_up_notification($mb_id, $old_level_name, $new_level_name, $new_level) {
    global $g5, $config;
    
    $mb = get_member($mb_id);
    if(!$mb['mb_id']) return;
    
    // 레벨 아이콘 URL
    $icon_url = '';
    $icon_file = G5_DATA_PATH.'/member_level/level_'.$new_level.'.png';
    if(file_exists($icon_file)) {
        $icon_url = G5_DATA_URL.'/member_level/level_'.$new_level.'.png';
    }
    
    // 쪽지 발송
    $subject = "🎉 축하합니다! 레벨업 하셨습니다!";
    $content = "
<div style='padding:20px; background:#f8f9fa; border-radius:10px; text-align:center;'>
    <h2 style='color:#667eea;'>레벨업 축하드립니다!</h2>
    " . ($icon_url ? "<img src='{$icon_url}' style='width:48px; height:48px; margin:20px auto;'>" : "") . "
    <p style='font-size:16px; margin:20px 0;'>
        회원님의 활동 등급이<br>
        <strong style='color:#666;'>[{$old_level_name}]</strong>에서<br>
        <strong style='color:#667eea; font-size:20px;'>[{$new_level_name}]</strong>(으)로 승급되었습니다!
    </p>
    <p style='color:#666;'>
        앞으로도 헤어왕에서 멋진 활동 부탁드립니다! 🌟
    </p>
</div>
    ";
    
    // HTML 쪽지 발송을 위한 처리
    $content = sql_real_escape_string($content);
    
    sql_query("INSERT INTO {$g5['memo_table']} SET
        me_recv_mb_id = '{$mb_id}',
        me_send_mb_id = '{$config['cf_admin']}',
        me_send_datetime = '".G5_TIME_YMDHIS."',
        me_memo = '{$content}',
        me_type = 'recv'
    ");
    
    // 회원 메모 알림 카운트 증가
    sql_query("UPDATE {$g5['member_table']} SET mb_memo_cnt = mb_memo_cnt + 1 WHERE mb_id = '{$mb_id}'");
}

// 전체 회원 레벨 업데이트 (배치 처리용)
function update_all_member_levels() {
    global $g5;
    
    $updated_count = 0;
    $processed_count = 0;
    
    // 탈퇴하지 않은 일반 회원만 대상 (레벨 6 미만)
    $sql = "SELECT mb_id FROM {$g5['member_table']} 
            WHERE mb_leave_date = '' AND mb_level < 6";
    $result = sql_query($sql);
    
    while($row = sql_fetch_array($result)) {
        $processed_count++;
        if(update_member_level($row['mb_id'])) {
            $updated_count++;
        }
        
        // 100명마다 잠시 쉬어줌 (서버 부하 방지)
        if($processed_count % 100 == 0) {
            sleep(1);
        }
    }
    
    return $updated_count;
}

// 회원 레벨 이름 가져오기 (캐시 없이 항상 DB에서 가져오기)
if(!function_exists('get_member_level_name')) {
    function get_member_level_name($mb_level) {
        global $g5;
        
        // DB에서 직접 가져오기
        $sql = "SELECT cf_value FROM {$g5['config_table']} WHERE cf_name = 'level_requirement_{$mb_level}'";
        $row = sql_fetch($sql);
        
        if($row && $row['cf_value']) {
            $data = json_decode($row['cf_value'], true);
            if($data && isset($data['name'])) {
                return $data['name'];
            }
        }
        
        // DB에 없으면 get_level_requirements() 사용
        $requirements = get_level_requirements();
        return isset($requirements[$mb_level]['name']) ? $requirements[$mb_level]['name'] : '회원';
    }
}

// 회원 레벨 아이콘 가져오기
if(!function_exists('get_member_level_icon')) {
    function get_member_level_icon($mb_level) {
        $icon_url = '';
        
        $extensions = array('png', 'jpg', 'jpeg', 'gif');
        foreach($extensions as $ext) {
            $icon_file = G5_DATA_PATH.'/member_level/level_'.$mb_level.'.'.$ext;
            if(file_exists($icon_file)) {
                $icon_url = G5_DATA_URL.'/member_level/level_'.$mb_level.'.'.$ext.'?v='.filemtime($icon_file);
                break;
            }
        }
        
        return $icon_url;
    }
}

// 회원 레벨 표시 HTML
if(!function_exists('get_member_level_icon_html')) {
    function get_member_level_icon_html($mb_level, $mb_id = '') {
        $level_name = get_member_level_name($mb_level);
        $icon_url = get_member_level_icon($mb_level);
        
        $html = '';
        if($icon_url) {
            $html = '<img src="'.$icon_url.'" alt="'.$level_name.'" title="'.$level_name.'" style="width:16px;height:16px;vertical-align:middle;margin-left:3px;">';
        }
        
        return $html;
    }
}

// 게시글/댓글 작성 후 자동 레벨 체크
function check_level_after_write($board, $wr_id, $w) {
    global $member;
    
    if($member['mb_id'] && $w == '') { // 새 글 작성시만
        update_member_level($member['mb_id']);
    }
}

// 댓글 작성 후 레벨 체크
function check_level_after_comment($board, $wr_id, $w) {
    global $member;
    
    if($member['mb_id']) {
        update_member_level($member['mb_id']);
    }
}

// 좋아요 후 레벨 체크
function check_level_after_good($bo_table, $wr_id) {
    global $g5;
    
    // 글 작성자 확인
    $write_table = $g5['write_prefix'] . $bo_table;
    $write = sql_fetch("SELECT mb_id FROM {$write_table} WHERE wr_id = '{$wr_id}'");
    
    if($write['mb_id']) {
        update_member_level($write['mb_id']);
    }
}

// SQL 테이블 존재 여부 확인 함수 (없을 경우 추가)
if(!function_exists('sql_table_exists')) {
    function sql_table_exists($table_name) {
        $sql = "SHOW TABLES LIKE '{$table_name}'";
        $result = sql_fetch($sql);
        return ($result) ? true : false;
    }
}
?>