<?php
include_once('./_common.php'); // 그누보드 설정 파일
include_once(G5_LIB_PATH.'/thumbnail.lib.php'); // 🔥 썸네일 라이브러리 추가

$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$mypost = isset($_POST['mypost']) && $_POST['mypost'] === '1' ? true : false; // 내가 쓴 글만 보기
$search = sql_escape_string($search); // SQL 인젝션 방지

$posts = [];
$limit = 20; // 기본 출력할 게시물 개수

// 모든 게시판 테이블 가져오기
$sql = "SELECT bo_table, bo_subject, bo_gallery_width, bo_gallery_height FROM g5_board 
        WHERE bo_use_search = '1' 
        AND bo_list_level <= '{$member['mb_level']}' 
        AND bo_read_level <= '{$member['mb_level']}'";
$result = sql_query($sql);

while ($board = sql_fetch_array($result)) {
    $bo_table = $board['bo_table'];
    $bo_subject = $board['bo_subject'];
    $board_table = "g5_write_{$bo_table}"; // 각 게시판 테이블명
    $gallery_width = 42;  // 기본 썸네일 크기 설정
    $gallery_height = 42;

    // 게시판별 게시물 가져오기
    $where = "wr_is_comment = '0' AND wr_option NOT IN ('secret')";
    
    if ($search !== '') {
        $where .= " AND wr_subject LIKE '%{$search}%'";
    }
    if ($mypost) {
        $where .= " AND mb_id = '{$member['mb_id']}'"; // 내가 쓴 글만 보기
    }
    
    $post_sql = "SELECT wr_id, wr_subject, wr_name, mb_id, wr_datetime, wr_option, wr_content FROM {$board_table} 
                 WHERE {$where} 
                 ORDER BY wr_datetime DESC LIMIT {$limit}";
    
    $post_result = sql_query($post_sql);
    while ($row = sql_fetch_array($post_result)) {
        $wr_id = $row['wr_id'];

        // `get_list_thumbnail()`을 이용한 썸네일 생성
        $thumb = get_list_thumbnail($bo_table, $wr_id, $gallery_width, $gallery_height, false, true);
        
        $editor_url = G5_EDITOR_URL.'/rb.editor';
        $thumbnail = $editor_url."/image/no_image.png";

        if ($thumb['src']) {
            if (!strstr($row['wr_option'], 'secret')) {
                $thumbnail = $thumb['src']; // 📷 썸네일 이미지 사용
            }
        } else {
            // 썸네일이 없을 경우 `wr_content`에서 `<img>` 태그의 src를 추출
            preg_match('/<img.*?src=["\'](.*?)["\']/i', $row['wr_content'], $matches);
            if (!empty($matches[1])) {
                $thumbnail = $matches[1]; // 에디터에서 첨부한 첫 번째 이미지 사용
            }
        }

        $posts[] = [
            'title' => htmlspecialchars($row['wr_subject']),
            'wr_name' => $row['wr_name'],
            'mb_id' => $row['mb_id'],
            'wr_datetime' => $row['wr_datetime'],
            'bo_subject' => $bo_subject, // 게시판명 추가
            'url' => G5_BBS_URL . "/board.php?bo_table={$bo_table}&wr_id=" . $wr_id,
            'thumbnail' => $thumbnail // 🔥 썸네일 추가
        ];
    }
}

// 최신순 정렬 (wr_datetime 기준)
usort($posts, function ($a, $b) {
    return strtotime($b['wr_datetime']) - strtotime($a['wr_datetime']); // 🔥 최신순 정렬
});

// 검색 결과가 많을 경우 최대 50개까지만 반환
$posts = array_slice($posts, 0, $limit);

header('Content-Type: application/json');
echo json_encode($posts);
?>
