<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//////////////////////////////////////////////////
//// 신고 기능 시작
//////////////////////////////////////////////////
include_once(G5_ADMIN_PATH.'/boardreport.lib.php');

// 신고 누적 임계치
if (!defined('CMT_REPORT_HIDE_LIMIT')) define('CMT_REPORT_HIDE_LIMIT', 5);

if (empty($report_table)) { $report_table = $g5['board_table'].'_report'; }

// 댓글 신고 카운트 일괄 조회
$report_counts = [];

if (!empty($list)) {
    $safe_bo_table = function_exists('sql_real_escape_string') ? sql_real_escape_string($bo_table) : addslashes($bo_table);
    $safe_wr_id    = (int)$wr_id;

    $report_sql = "
        SELECT comment_id, COUNT(*) AS cnt
        FROM `{$report_table}`
        WHERE bo_table = '{$safe_bo_table}'
          AND wr_id    = {$safe_wr_id}
          AND comment_id > 0
        GROUP BY comment_id
    ";
    $report_qry = sql_query($report_sql);
    while ($report_row = sql_fetch_array($report_qry)) {
        $report_counts[(int)$report_row['comment_id']] = (int)$report_row['cnt'];
    }
}

// 댓글 잠금 상태 일괄 조회
$comment_lock = [];

if (!empty($list)) {
    if (empty($write_table)) {
        $write_table = $g5['write_prefix'].$bo_table;
    }

    // 댓글 ID 수집
    $ids = [];
    foreach ($list as $row) {
        $id = (int)$row['wr_id'];
        if ($id > 0) $ids[$id] = $id;
    }

    if (!empty($ids)) {
        $in_ids = implode(',', array_map('intval', array_values($ids)));
        $rs = sql_query("SELECT wr_id, wr_report FROM `{$write_table}` WHERE wr_id IN ({$in_ids})");
        while ($r = sql_fetch_array($rs)) {
            $comment_lock[(int)$r['wr_id']] = ($r['wr_report'] === '잠금');
        }
    }
}

//////////////////////////////////////////////////
//// 댓글 반응 기능 시작
//////////////////////////////////////////////////

// 댓글 반응 테이블이 없으면 생성
$reaction_table = 'g5_comment_reaction';
$table_check = sql_query("SHOW TABLES LIKE '{$reaction_table}'", false);
if(!sql_num_rows($table_check)) {
    $create_table_sql = "
    CREATE TABLE IF NOT EXISTS `{$reaction_table}` (
      `reaction_id` int(11) NOT NULL AUTO_INCREMENT,
      `bo_table` varchar(20) NOT NULL DEFAULT '',
      `wr_id` int(11) NOT NULL DEFAULT '0',
      `comment_id` int(11) NOT NULL DEFAULT '0',
      `mb_id` varchar(20) NOT NULL DEFAULT '',
      `reaction_type` varchar(20) NOT NULL DEFAULT '',
      `reaction_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      `reaction_ip` varchar(50) NOT NULL DEFAULT '',
      PRIMARY KEY (`reaction_id`),
      UNIQUE KEY `unique_reaction` (`bo_table`, `wr_id`, `comment_id`, `mb_id`),
      KEY `idx_comment` (`bo_table`, `wr_id`, `comment_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    sql_query($create_table_sql, false);
}

// 댓글별 반응 수 조회
$comment_reactions = [];
$my_reactions = [];

if (!empty($list)) {
    // 전체 반응 수 조회
    $reaction_sql = "
        SELECT comment_id, reaction_type, COUNT(*) as cnt
        FROM `{$reaction_table}`
        WHERE bo_table = '{$safe_bo_table}'
          AND wr_id = {$safe_wr_id}
          AND comment_id > 0
        GROUP BY comment_id, reaction_type
    ";
    $reaction_qry = sql_query($reaction_sql);
    while ($reaction_row = sql_fetch_array($reaction_qry)) {
        $cid = (int)$reaction_row['comment_id'];
        $rtype = $reaction_row['reaction_type'];
        if (!isset($comment_reactions[$cid])) {
            $comment_reactions[$cid] = ['like' => 0, 'dislike' => 0, 'sad' => 0];
        }
        $comment_reactions[$cid][$rtype] = (int)$reaction_row['cnt'];
    }
    
    // 내가 누른 반응 조회
    if ($member['mb_id']) {
        $my_reaction_sql = "
            SELECT comment_id, reaction_type
            FROM `{$reaction_table}`
            WHERE bo_table = '{$safe_bo_table}'
              AND wr_id = {$safe_wr_id}
              AND mb_id = '{$member['mb_id']}'
              AND comment_id > 0
        ";
        $my_reaction_qry = sql_query($my_reaction_sql);
        while ($my_reaction_row = sql_fetch_array($my_reaction_qry)) {
            $my_reactions[(int)$my_reaction_row['comment_id']] = $my_reaction_row['reaction_type'];
        }
    }
}
//////////////////////////////////////////////////
//// 댓글 반응 기능 끝
//////////////////////////////////////////////////
?>

<style>
/* 댓글 신고 관련 스타일 */
.cmt-report-btn {
    color: #999;
    font-size: 12px;
    margin-left: 10px;
}
.cmt-report-btn:hover {
    color: #ff4444;
}
.cmt-report-badge {
    display: inline-block;
    background: #ff4444;
    color: white;
    border-radius: 10px;
    padding: 0 5px;
    font-size: 10px;
    margin-left: 3px;
}
.cmt-report-hidden-note {
    background: #7a4efe1a;
    border: 1px solid #7a4efe;
    padding: 10px;
    border-radius: 4px;
    color: #7a4efe;
    text-align: center;
}
.report-lock-badge {
    display: inline-block;
    background: #ffc107;
    color: #333;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    margin-left: 10px;
}

/* 댓글 반응 관련 스타일 */
.comment-reactions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e9ecef;
}

.reaction-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    background: white;
    color: #666;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.reaction-btn:hover {
    background: #f5f5f5;
}

/* active 상태 스타일 */
.reaction-btn.btn-like.active {
    color: #4CAF50 !important;
    border-color: #4CAF50 !important;
    background: rgba(76, 175, 80, 0.06) !important;
}

.reaction-btn.btn-dislike.active {
    color: #f44336 !important;
    border-color: #f44336 !important;
    background: rgba(244, 67, 54, 0.06) !important;
}

.reaction-btn.btn-sad.active {
    color: #2196F3 !important;
    border-color: #2196F3 !important;
    background: rgba(33, 150, 243, 0.06) !important;
}

.reaction-icon {
    font-size: 16px;
}

.reaction-text {
    font-size: 13px;
    font-weight: 500;
}

.reaction-count {
    font-weight: 600;
    min-width: 10px;
    text-align: center;
    margin-left: 2px;
    color: #7a4efe;
}

.reaction-btn.active .reaction-count {
    color: inherit !important;
}

.reaction-btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

/* 반응 애니메이션 */
@keyframes reaction-pop {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.reaction-btn.animating {
    animation: reaction-pop 0.3s ease;
}

/* 로딩 스피너 */
.reaction-loading {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #7a4efe;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
// 글자수 제한
var char_min = parseInt(<?php echo $comment_min ?>); // 최소
var char_max = parseInt(<?php echo $comment_max ?>); // 최대
</script>

<button type="button" class="cmt_btn"><span class="total"><b>댓글</b> <?php echo $view['wr_comment']; ?></span><span class="cmt_more"></span></button>

<!-- 댓글 시작 { -->
<section id="bo_vc">
    <h2>댓글목록</h2>
    <?php
    $cmt_amt = count($list);
    for ($i=0; $i<$cmt_amt; $i++) {
        $comment_id = $list[$i]['wr_id'];
        $cmt_depth = strlen($list[$i]['wr_comment_reply']) * 20;
        $cmt_depth_bg = $cmt_depth - 20;
        $comment = $list[$i]['content'];
        
        $comment = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $comment);
        $cmt_sv = $cmt_amt - $i + 1;
		$c_reply_href = $comment_common_url.'&amp;c_id='.$comment_id.'&amp;w=c#bo_vc_w';
		$c_edit_href = $comment_common_url.'&amp;c_id='.$comment_id.'&amp;w=cu#bo_vc_w';
        $is_comment_reply_edit = ($list[$i]['is_reply'] || $list[$i]['is_edit'] || $list[$i]['is_del']) ? 1 : 0;
        
        //////////////////////////////////////////////////
        // 신고 처리 로직
        //////////////////////////////////////////////////
        $report_cmt_count = isset($report_counts[$comment_id]) ? (int)$report_counts[$comment_id] : 0;
        
        // 관리자 또는 댓글 작성자는 항상 원문 열람 가능
        $is_owner = isset($member['mb_id']) && $member['mb_id'] && ($member['mb_id'] === $list[$i]['mb_id']);
        $report_can_view = $is_admin || $is_owner;
        
        // 잠금 여부
        $report_locked = isset($comment_lock[$comment_id]) && $comment_lock[$comment_id] === true;
        $is_locked = $report_locked || (($list[$i]['wr_report'] ?? '') === '잠금');
        
        // 일반 사용자에게 가리기 (블라인드 처리)
        if (!$report_can_view && ($report_cmt_count >= CMT_REPORT_HIDE_LIMIT || $report_locked)) {
            $comment = '<div class="cmt-report-hidden-note">
                            <strong>신고가 누적되어 블라인드 처리된 댓글입니다.</strong><br>
                            관리자의 확인 후 복구될 수 있습니다.
                        </div>';
        }
        
        // 반응 데이터 가져오기
        $reactions = isset($comment_reactions[$comment_id]) ? $comment_reactions[$comment_id] : ['like' => 0, 'dislike' => 0, 'sad' => 0];
        $my_reaction = isset($my_reactions[$comment_id]) ? $my_reactions[$comment_id] : '';
	?>

	<article id="c_<?php echo $comment_id ?>">
        <div class="pf_img"><?php echo get_member_profile_img($list[$i]['mb_id']) ?></div>
        
        <div class="cm_wrap" <?php if ($cmt_depth) { ?>style="padding-left:<?php echo $cmt_depth ?>px; background-image:url('<?php echo $board_skin_url ?>/img/ico_rep_tiny.svg'); background-position:top 22px left <?php echo $cmt_depth_bg ?>px"<?php } ?>>

            <header style="z-index:<?php echo $cmt_sv; ?>">
	            <?php echo $list[$i]['name'] ?>
	            <?php if ($board['bo_use_ip_view']) { ?>
	            <span>(<?php echo $list[$i]['ip']; ?>)</span>
	            <?php } ?>
	            <span class="bo_vc_hdinfo">　<?php echo passing_time3($list[$i]['datetime']) ?></span>
	            
	            <?php
                // 관리자 전용: 신고 내역 링크
                if ($is_admin && $report_cmt_count > 0) {
                    $admin_report_url = G5_ADMIN_URL.'/boardreport_list.php?bo_table='.urlencode($bo_table).'&kw='.$comment_id;
                ?>
                <a href="<?php echo $admin_report_url; ?>" target="_blank" class="cmt-report-btn">신고내역
                    <span id="cmt_report_badge_<?php echo $comment_id; ?>"
                        class="cmt-report-badge"
                        data-count="<?php echo $report_cmt_count; ?>">
                        <?php echo $report_cmt_count; ?>
                    </span>
                </a>
                <?php } ?>
                
                <?php
                // 관리자 전용: 잠금 표시
                if ($is_admin && $is_locked) {
                ?>
                <span class="report-lock-badge">신고 누적 잠금</span>
                <?php } ?>
	        </header>
	
	        <!-- 댓글 출력 -->
	        <div class="cmt_contents">
	            <p>
	                <?php if (strstr($list[$i]['wr_option'], "secret")) { ?><img src="<?php echo $board_skin_url; ?>/img/ico_sec.svg" alt="비밀글"><?php } ?>
	                <?php echo $comment ?>
	            </p>
	            <?php if($is_comment_reply_edit) {
	                if($w == 'cu') {
	                    $sql = " select wr_id, wr_content, mb_id from $write_table where wr_id = '$c_id' and wr_is_comment = '1' ";
	                    $cmt = sql_fetch($sql);
                        if (isset($cmt)) {
                            if (!($is_admin || ($member['mb_id'] == $cmt['mb_id'] && $cmt['mb_id']))) {
                                $cmt['wr_content'] = '';
                            }
                            $c_wr_content = $cmt['wr_content'];
                        }
	                }
				?>            
	            <?php } ?>
	            
	            <p class="p_times"><span><?php echo date('Y-m-d H:i', strtotime($list[$i]['datetime'])) ?></span></p>
	            
	            <!-- 댓글 반응 버튼 시작 -->
	            <div class="comment-reactions" id="reactions_<?php echo $comment_id ?>">
	                <button type="button" 
	                        class="reaction-btn btn-like <?php echo ($my_reaction === 'like') ? 'active' : ''; ?>" 
	                        data-type="like"
	                        onclick="toggleReaction('<?php echo $bo_table ?>', '<?php echo $wr_id ?>', '<?php echo $comment_id ?>', 'like')"
	                        <?php echo !$member['mb_id'] ? 'disabled title="로그인이 필요합니다"' : ''; ?>>
	                    <span class="reaction-icon">👍</span>
	                    <span class="reaction-text">좋아요</span>
	                    <span class="reaction-count" id="like_count_<?php echo $comment_id ?>"><?php echo $reactions['like'] ?: ''; ?></span>
	                </button>
	                
	                <button type="button" 
	                        class="reaction-btn btn-dislike <?php echo ($my_reaction === 'dislike') ? 'active' : ''; ?>" 
	                        data-type="dislike"
	                        onclick="toggleReaction('<?php echo $bo_table ?>', '<?php echo $wr_id ?>', '<?php echo $comment_id ?>', 'dislike')"
	                        <?php echo !$member['mb_id'] ? 'disabled title="로그인이 필요합니다"' : ''; ?>>
	                    <span class="reaction-icon">👎</span>
	                    <span class="reaction-text">싫어요</span>
	                    <span class="reaction-count" id="dislike_count_<?php echo $comment_id ?>"><?php echo $reactions['dislike'] ?: ''; ?></span>
	                </button>
	                
	                <button type="button" 
	                        class="reaction-btn btn-sad <?php echo ($my_reaction === 'sad') ? 'active' : ''; ?>" 
	                        data-type="sad"
	                        onclick="toggleReaction('<?php echo $bo_table ?>', '<?php echo $wr_id ?>', '<?php echo $comment_id ?>', 'sad')"
	                        <?php echo !$member['mb_id'] ? 'disabled title="로그인이 필요합니다"' : ''; ?>>
	                    <span class="reaction-icon">😢</span>
	                    <span class="reaction-text">슬퍼요</span>
	                    <span class="reaction-count" id="sad_count_<?php echo $comment_id ?>"><?php echo $reactions['sad'] ?: ''; ?></span>
	                </button>
	            </div>
	            <!-- 댓글 반응 버튼 끝 -->
	        </div>
	        <span id="edit_<?php echo $comment_id ?>" class="bo_vc_w"></span><!-- 수정 -->
	        <span id="reply_<?php echo $comment_id ?>" class="bo_vc_w"></span><!-- 답변 -->
	
	        <input type="hidden" value="<?php echo strstr($list[$i]['wr_option'],"secret") ?>" id="secret_comment_<?php echo $comment_id ?>">
	        <textarea id="save_comment_<?php echo $comment_id ?>" style="display:none"><?php echo get_text($list[$i]['content1'], 0) ?></textarea>
		</div>
        <?php if($is_comment_reply_edit){ ?>
		<div class="bo_vl_opt">
            <button type="button" class="btn_cm_opt btn_b01 btn"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg></button>
        	<ul class="bo_vc_act">
                <?php if ($list[$i]['is_reply']) { ?><li><a href="<?php echo $c_reply_href; ?>" onclick="comment_box('<?php echo $comment_id ?>', 'c'); return false;">답변</a></li><?php } ?>
                <?php if ($list[$i]['is_edit']) { ?><li><a href="<?php echo $c_edit_href; ?>" onclick="comment_box('<?php echo $comment_id ?>', 'cu'); return false;">수정</a></li><?php } ?>
                <?php if ($list[$i]['is_del']) { ?><li><a href="<?php echo $list[$i]['del_link']; ?>" onclick="return comment_delete();">삭제</a></li><?php } ?>
                <?php
                // 신고 버튼 추가 (잠금이 아닌 경우만)
                if (!$is_locked) {
                ?>
                <li><a href="javascript:void(0);" onclick="handleReportClick('<?php echo $bo_table ?>','<?php echo $wr_id ?>','<?php echo $comment_id; ?>')">신고</a></li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
        <script>
			$(function() {			    
		    // 댓글 옵션창 열기
		    $(".btn_cm_opt").on("click", function(){
		        $(this).parent("div").children(".bo_vc_act").show();
		    });
				
		    // 댓글 옵션창 닫기
		    $(document).mouseup(function (e){
		        var container = $(".bo_vc_act");
		        if( container.has(e.target).length === 0)
		        container.hide();
		    });
		});
		</script>
    </article>
    <?php } ?>
    <?php if ($i == 0) { ?><p id="bo_vc_empty">등록된 댓글이 없습니다.</p><?php } ?>

</section>
<!-- } 댓글 끝 -->

<!-- 댓글 반응 처리 스크립트 -->
<script>
var reaction_processing = {};

function toggleReaction(bo_table, wr_id, comment_id, reaction_type) {
    console.log('toggleReaction called:', {bo_table, wr_id, comment_id, reaction_type});
    
    // 로그인 체크
    if (typeof g5_is_member === 'undefined' || !g5_is_member) {
        <?php if (!$member['mb_id']) { ?>
        alert('로그인이 필요한 기능입니다.');
        return;
        <?php } ?>
    }
    
    // 중복 클릭 방지
    var key = comment_id + '_' + reaction_type;
    if (reaction_processing[key]) {
        console.log('Already processing:', key);
        return;
    }
    reaction_processing[key] = true;
    
    // 버튼 요소 찾기
    var $container = $('#reactions_' + comment_id);
    var $btn = $container.find('.btn-' + reaction_type).first();
    var $allBtns = $container.find('.reaction-btn');
    
    // 현재 활성화된 반응 찾기
    var $previousActive = $container.find('.reaction-btn.active').first();
    var previousType = $previousActive.data('type') || '';
    
    // 클릭한 버튼이 이미 활성화되어 있었는지 확인
    var wasActive = $btn.hasClass('active');
    
    console.log('Previous:', previousType, 'Current:', reaction_type, 'WasActive:', wasActive);
    
    // 모든 버튼 초기화
    $allBtns.removeClass('active').removeAttr('style');
    
    // 시나리오별 처리
    if (wasActive) {
        // 같은 버튼 재클릭 - 취소
        console.log('취소: ' + reaction_type);
        
        // 카운트 감소
        var $count = $('#' + reaction_type + '_count_' + comment_id);
        var currentCount = parseInt($count.text()) || 0;
        if (currentCount > 0) {
            $count.text(currentCount - 1 || '');
        }
        
    } else if (previousType && previousType !== reaction_type) {
        // 다른 반응으로 변경
        console.log('변경: ' + previousType + ' -> ' + reaction_type);
        
        // 기존 반응 카운트 감소
        var $prevCount = $('#' + previousType + '_count_' + comment_id);
        var prevCountVal = parseInt($prevCount.text()) || 0;
        if (prevCountVal > 0) {
            $prevCount.text(prevCountVal - 1 || '');
        }
        
        // 새 반응 카운트 증가
        var $newCount = $('#' + reaction_type + '_count_' + comment_id);
        var newCountVal = parseInt($newCount.text()) || 0;
        $newCount.text(newCountVal + 1);
        
        // 새 버튼 활성화
        $btn.addClass('active');
        applyReactionStyle($btn, reaction_type);
        
    } else {
        // 새로운 반응
        console.log('새 반응: ' + reaction_type);
        
        // 버튼 활성화
        $btn.addClass('active');
        applyReactionStyle($btn, reaction_type);
        
        // 카운트 증가
        var $count = $('#' + reaction_type + '_count_' + comment_id);
        var currentCount = parseInt($count.text()) || 0;
        $count.text(currentCount + 1);
    }
    
    // 애니메이션 효과
    $btn.addClass('animating');
    setTimeout(function() {
        $btn.removeClass('animating');
    }, 300);
    
    // AJAX 요청
    $.ajax({
        url: g5_bbs_url + '/ajax.comment_reaction.php',
        type: 'POST',
        data: {
            bo_table: bo_table,
            wr_id: wr_id,
            comment_id: comment_id,
            reaction_type: reaction_type
        },
        dataType: 'json',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        },
        success: function(response) {
            console.log('Success response:', response);
            
            if (response.error) {
                alert(response.error);
                // 오류 시 원래 상태로 복구
                refreshReactionButtons(comment_id, previousType, {});
                reaction_processing[key] = false;
                return;
            }
            
            // 서버 응답에 따라 최종 동기화
            refreshReactionButtons(comment_id, response.current_reaction, response.counts);
            reaction_processing[key] = false;
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr);
            
            // 오류 시 원래 상태로 복구
            if (wasActive) {
                refreshReactionButtons(comment_id, '', {});
            } else if (previousType) {
                refreshReactionButtons(comment_id, previousType, {});
            }
            
            alert('반응 처리 중 오류가 발생했습니다.');
            reaction_processing[key] = false;
        }
    });
}

// 반응 타입별 스타일 적용
function applyReactionStyle($btn, reaction_type) {
    if (reaction_type === 'like') {
        $btn.css({
            'color': '#4CAF50',
            'border-color': '#4CAF50',
            'background': 'rgba(76, 175, 80, 0.06)'
        });
    } else if (reaction_type === 'dislike') {
        $btn.css({
            'color': '#f44336',
            'border-color': '#f44336',
            'background': 'rgba(244, 67, 54, 0.06)'
        });
    } else if (reaction_type === 'sad') {
        $btn.css({
            'color': '#2196F3',
            'border-color': '#2196F3',
            'background': 'rgba(33, 150, 243, 0.06)'
        });
    }
}

// UI 새로고침
function refreshReactionButtons(comment_id, active_reaction, counts) {
    var $container = $('#reactions_' + comment_id);
    
    // 모든 버튼 초기화 후 업데이트
    $container.find('.reaction-btn').each(function() {
        var $btn = $(this);
        var btnType = $btn.data('type');
        
        // 초기화
        $btn.removeClass('active').removeAttr('style');
        
        // 활성 반응 처리
        if (btnType === active_reaction) {
            $btn.addClass('active');
            applyReactionStyle($btn, btnType);
        }
        
        // 카운트 업데이트
        if (counts && btnType) {
            var count = counts[btnType] || 0;
            $('#' + btnType + '_count_' + comment_id).text(count > 0 ? count : '');
        }
    });
}

// 페이지 로드 시 g5_bbs_url 확인
$(document).ready(function() {
    if (typeof g5_bbs_url === 'undefined') {
        console.error('g5_bbs_url is not defined. Check if common.js is loaded.');
    } else {
        console.log('g5_bbs_url:', g5_bbs_url);
    }
});
</script>

<?php if ($is_comment_write) {
    if($w == '')
        $w = 'c';
?>
<!-- 댓글 쓰기 시작 { -->
<aside id="bo_vc_w" class="bo_vc_w">

    <form name="fviewcomment" id="fviewcomment" action="<?php echo $comment_action_url; ?>" onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>" id="w">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="comment_id" value="<?php echo $c_id ?>" id="comment_id">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="is_good" value="">

    <span class="sound_only">내용</span>
    <span id="char_cnt"><span id="char_count"></span> 글자</span>
    <textarea id="wr_content" name="wr_content" maxlength="10000" required title="댓글" placeholder="댓글 내용을 입력해주세요." onkeyup="check_byte('wr_content', 'char_count');" <?php if ($is_guest) { ?>style="padding-bottom:60px !important;"<?php } ?>><?php echo $c_wr_content; ?></textarea>
    <script> check_byte('wr_content', 'char_count'); </script>
    <script>
    $(document).on("keyup change", "textarea#wr_content[maxlength]", function() {
        var str = $(this).val()
        var mx = parseInt($(this).attr("maxlength"))
        if (str.length > mx) {
            $(this).val(str.substr(0, mx));
            return false;
        }
    });
        
    $(document).ready(function() {
        $('#wr_content').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            this.style.minHeight = '150px';
        });
    });
    </script>
    <div class="bo_vc_w_wr">
        <?php if ($is_guest) { ?>
        <div class="bo_vc_w_info">
            <ul class="bo_vc_w_info_ul1">
                <label for="wr_name" class="sound_only">이름<strong> 필수</strong></label>
                <input type="text" name="wr_name" value="<?php echo get_cookie("ck_sns_name"); ?>" id="wr_name" required class="frm_input required" size="25" placeholder="이름">
                <label for="wr_password" class="sound_only">비밀번호<strong> 필수</strong></label>
                <input type="password" name="wr_password" id="wr_password" required class="frm_input required" size="25" placeholder="비밀번호">
            </ul>
            <ul class="bo_vc_w_info_ul2">
            <?php echo $captcha_html; ?>
            </ul>
            <div class="cb"></div>
        </div>
        <?php } ?>
        <div class="btn_confirm btn_confirm_cm_wrap">
            <ul class="cm_wrpa_write_left">
                <?php if($board['bo_comment_point'] > 0) { ?>
                    <span class="font-B">댓글을 작성하시면 <span class="main_color"><?php echo number_format($board['bo_comment_point']); ?>P</span> 를 드려요!</span>
                <?php } else if($board['bo_comment_point'] < 0) { ?>
                    <span class="font-B">댓글을 작성하시면 <span class="main_color"><?php echo number_format($board['bo_comment_point']); ?>P</span> 가 차감되요!</span>
                <?php } else { ?>
                    <span class="font-B">바르고 고운말을 사용해주세요!</span>
                <?php } ?>
            </ul>
            <ul class="cm_wrpa_write_right">
                <i><img src="<?php echo $board_skin_url ?>/img/ico_sec.svg"></i>
                <span class="btn_confirm_btn_wrap">
                    <input type="checkbox" name="wr_secret" value="secret" id="wr_secret">
                    <label for="wr_secret"><span></span>비밀댓글</label>
                </span>
                <button type="submit" id="btn_submit" class="btn_submit">댓글등록</button>
            </ul>
            <div class="cb"></div>
        </div>
    </div>
    </form>
</aside>

<script>
var save_before = '';
var save_html = document.getElementById('bo_vc_w').innerHTML;

function good_and_write()
{
    var f = document.fviewcomment;
    if (fviewcomment_submit(f)) {
        f.is_good.value = 1;
        f.submit();
    } else {
        f.is_good.value = 0;
    }
}

function fviewcomment_submit(f)
{
    var pattern = /(^\s*)|(\s*$)/g;

    f.is_good.value = 0;

    var subject = "";
    var content = "";
    $.ajax({
        url: g5_bbs_url+"/ajax.filter.php",
        type: "POST",
        data: {
            "subject": "",
            "content": f.wr_content.value
        },
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            subject = data.subject;
            content = data.content;
        }
    });

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        f.wr_content.focus();
        return false;
    }

    // 양쪽 공백 없애기
    var pattern = /(^\s*)|(\s*$)/g;
    document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");
    if (char_min > 0 || char_max > 0)
    {
        check_byte('wr_content', 'char_count');
        var cnt = parseInt(document.getElementById('char_count').innerHTML);
        if (char_min > 0 && char_min > cnt)
        {
            alert("댓글은 "+char_min+"글자 이상 쓰셔야 합니다.");
            return false;
        } else if (char_max > 0 && char_max < cnt)
        {
            alert("댓글은 "+char_max+"글자 이하로 쓰셔야 합니다.");
            return false;
        }
    }
    else if (!document.getElementById('wr_content').value)
    {
        alert("댓글을 입력하여 주십시오.");
        return false;
    }

    if (typeof(f.wr_name) != 'undefined')
    {
        f.wr_name.value = f.wr_name.value.replace(pattern, "");
        if (f.wr_name.value == '')
        {
            alert('이름이 입력되지 않았습니다.');
            f.wr_name.focus();
            return false;
        }
    }

    if (typeof(f.wr_password) != 'undefined')
    {
        f.wr_password.value = f.wr_password.value.replace(pattern, "");
        if (f.wr_password.value == '')
        {
            alert('비밀번호가 입력되지 않았습니다.');
            f.wr_password.focus();
            return false;
        }
    }

    <?php if($is_guest) echo chk_captcha_js();  ?>

    set_comment_token(f);

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}

function comment_box(comment_id, work)
{
    var el_id,
        form_el = 'fviewcomment',
        respond = document.getElementById(form_el);

    if (comment_id)
    {
        if (work == 'c')
            el_id = 'reply_' + comment_id;
        else
            el_id = 'edit_' + comment_id;
    }
    else
        el_id = 'bo_vc_w';

    if (save_before != el_id)
    {
        if (save_before)
        {
            document.getElementById(save_before).style.display = 'none';
        }

        document.getElementById(el_id).style.display = '';
        document.getElementById(el_id).appendChild(respond);
        document.getElementById('wr_content').value = '';
        
        if (work == 'cu')
        {
            document.getElementById('wr_content').value = document.getElementById('save_comment_' + comment_id).value;
            if (typeof char_count != 'undefined')
                check_byte('wr_content', 'char_count');
            if (document.getElementById('secret_comment_'+comment_id).value)
                document.getElementById('wr_secret').checked = true;
            else
                document.getElementById('wr_secret').checked = false;
        }

        document.getElementById('comment_id').value = comment_id;
        document.getElementById('w').value = work;

        if(save_before)
            $("#captcha_reload").trigger("click");

        save_before = el_id;
    }
}

function comment_delete()
{
    return confirm("이 댓글을 삭제하시겠습니까?");
}

comment_box('', 'c');

<?php if($board['bo_use_sns'] && ($config['cf_facebook_appid'] || $config['cf_twitter_key'])) { ?>
$(function() {
    $("#bo_vc_send_sns").load(
        "<?php echo G5_SNS_URL; ?>/view_comment_write.sns.skin.php?bo_table=<?php echo $bo_table; ?>",
        function() {
            save_html = document.getElementById('bo_vc_w').innerHTML;
        }
    );
});
<?php } ?>
</script>
<?php } ?>
<!-- } 댓글 쓰기 끝 -->
<script>
jQuery(function($) {            
    //댓글열기
    $(".cmt_btn").click(function(e){
        e.preventDefault();
        $(this).toggleClass("cmt_btn_op");
        $("#bo_vc").toggle();
    });
});
</script>