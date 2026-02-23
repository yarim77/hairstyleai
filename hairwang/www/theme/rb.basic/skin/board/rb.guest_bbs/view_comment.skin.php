<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//////////////////////////////////////////////////
//// 신고 기능 시작
//////////////////////////////////////////////////
// 신고 테이블이 없으면 생성
$report_table = $g5['board_table'].'_report';
$table_check = sql_query("SHOW TABLES LIKE '{$report_table}'", false);
if(!sql_num_rows($table_check)) {
    $create_report_table = "
    CREATE TABLE IF NOT EXISTS `{$report_table}` (
      `report_id` int(11) NOT NULL AUTO_INCREMENT,
      `bo_table` varchar(20) NOT NULL DEFAULT '',
      `wr_id` int(11) NOT NULL DEFAULT '0',
      `comment_id` int(11) NOT NULL DEFAULT '0',
      `mb_id` varchar(20) NOT NULL DEFAULT '',
      `report_reason` text NOT NULL,
      `report_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
      `report_ip` varchar(50) NOT NULL DEFAULT '',
      PRIMARY KEY (`report_id`),
      KEY `idx_board` (`bo_table`, `wr_id`, `comment_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    sql_query($create_report_table, false);
}

// 신고 누적 임계치
if (!defined('CMT_REPORT_HIDE_LIMIT')) define('CMT_REPORT_HIDE_LIMIT', 5);

// 댓글 신고 카운트 일괄 조회
$report_counts = [];
if (!empty($list)) {
    $safe_bo_table = sql_real_escape_string($bo_table);
    $safe_wr_id = (int)$wr_id;

    $report_sql = "
        SELECT comment_id, COUNT(*) AS cnt
        FROM `{$report_table}`
        WHERE bo_table = '{$safe_bo_table}'
          AND wr_id = {$safe_wr_id}
          AND comment_id > 0
        GROUP BY comment_id
    ";
    $report_qry = sql_query($report_sql);
    while ($report_row = sql_fetch_array($report_qry)) {
        $report_counts[(int)$report_row['comment_id']] = (int)$report_row['cnt'];
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

if (!empty($list) && isset($safe_bo_table) && isset($safe_wr_id)) {
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
?>

<style>
/* 댓글 신고 관련 스타일 */
.cmt-report-btn {
    color: #999;
    font-size: 12px;
    margin-left: 10px;
    cursor: pointer;
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
    background: #fff3cd;
    border: 1px solid #ffc107;
    padding: 10px;
    border-radius: 4px;
    color: #856404;
    text-align: center;
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

.reaction-btn.btn-like.active {
    color: #4CAF50;
    border-color: #4CAF50;
    background: rgba(76, 175, 80, 0.1);
}

.reaction-btn.btn-dislike.active {
    color: #f44336;
    border-color: #f44336;
    background: rgba(244, 67, 54, 0.1);
}

.reaction-btn.btn-sad.active {
    color: #2196F3;
    border-color: #2196F3;
    background: rgba(33, 150, 243, 0.1);
}

.reaction-icon {
    font-size: 16px;
}

.reaction-count {
    font-weight: 600;
    min-width: 10px;
    text-align: center;
    margin-left: 2px;
    color: #999;
}

.reaction-btn.active .reaction-count {
    color: inherit;
}

.reaction-btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

@keyframes reaction-pop {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.reaction-btn.animating {
    animation: reaction-pop 0.3s ease;
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
        
        // 신고 처리
        $report_cmt_count = isset($report_counts[$comment_id]) ? (int)$report_counts[$comment_id] : 0;
        $is_owner = isset($member['mb_id']) && $member['mb_id'] && ($member['mb_id'] === $list[$i]['mb_id']);
        $report_can_view = $is_admin || $is_owner;
        
        // 일반 사용자에게 블라인드 처리
        if (!$report_can_view && $report_cmt_count >= CMT_REPORT_HIDE_LIMIT) {
            $comment = '<div class="cmt-report-hidden-note">
                            <strong>신고가 누적되어 블라인드 처리된 댓글입니다.</strong><br>
                            관리자의 확인 후 복구될 수 있습니다.
                        </div>';
        }
        
        // 반응 데이터
        $reactions = isset($comment_reactions[$comment_id]) ? $comment_reactions[$comment_id] : ['like' => 0, 'dislike' => 0, 'sad' => 0];
        $my_reaction = isset($my_reactions[$comment_id]) ? $my_reactions[$comment_id] : '';
	?>

	<article id="c_<?php echo $comment_id ?>">

        <div class="cm_wrap" <?php if ($cmt_depth) { ?>style="padding-left:<?php echo $cmt_depth ?>px; background-image:url('<?php echo $board_skin_url ?>/img/ico_rep_tiny.svg'); background-position:top 22px left <?php echo $cmt_depth_bg ?>px"<?php } ?>>

            <header style="z-index:<?php echo $cmt_sv; ?>">
                <span class="sv_wrap">
                    <a href="javascript:void(0);" class="sv_member" title="<?php echo get_text($list[$i]['wr_name']) ?>">
                        <span class="profile_img"><img src="<?php echo $board_skin_url ?>/img/guest_noname.png" alt="<?php echo get_text($list[$i]['wr_name']) ?>" width="22" height="22"></span> <?php echo get_text($list[$i]['wr_name']) ?>
                    </a>
                </span>
	            
	            <?php if ($board['bo_use_ip_view']) { ?>
	            <span>(<?php echo $list[$i]['ip']; ?>)</span>
	            <?php } ?>
	            <span class="bo_vc_hdinfo">　<?php echo passing_time3($list[$i]['datetime']) ?></span>
	            <?php if($is_member) { ?>
	                <?php if($list[$i]['mb_id'] == $member['mb_id']) { ?>
	                　<span class="bo_vc_my">내가 쓴 글</span>
	                <?php } ?>
	            <?php } ?>
	            
	            <?php if ($is_admin && $report_cmt_count > 0) { ?>
                <span class="cmt-report-btn">
                    신고 <span class="cmt-report-badge"><?php echo $report_cmt_count; ?></span>
                </span>
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
	            
	            <!-- 댓글 반응 버튼 -->
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
                <li><a href="javascript:void(0);" onclick="reportComment('<?php echo $bo_table ?>','<?php echo $wr_id ?>','<?php echo $comment_id; ?>')">신고</a></li>
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
    // 중복 클릭 방지
    var key = comment_id + '_' + reaction_type;
    if (reaction_processing[key]) {
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
    
    // 모든 버튼 초기화
    $allBtns.removeClass('active');
    
    // UI 즉시 업데이트
    if (wasActive) {
        // 같은 버튼 재클릭 - 취소
        var $count = $('#' + reaction_type + '_count_' + comment_id);
        var currentCount = parseInt($count.text()) || 0;
        if (currentCount > 0) {
            $count.text(currentCount - 1 || '');
        }
    } else {
        // 다른 반응으로 변경 또는 새 반응
        if (previousType && previousType !== reaction_type) {
            // 기존 반응 카운트 감소
            var $prevCount = $('#' + previousType + '_count_' + comment_id);
            var prevCountVal = parseInt($prevCount.text()) || 0;
            if (prevCountVal > 0) {
                $prevCount.text(prevCountVal - 1 || '');
            }
        }
        
        // 새 반응 카운트 증가 및 버튼 활성화
        $btn.addClass('active');
        var $newCount = $('#' + reaction_type + '_count_' + comment_id);
        var newCountVal = parseInt($newCount.text()) || 0;
        $newCount.text(newCountVal + 1);
    }
    
    // 애니메이션
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
        success: function(response) {
            if (response.error) {
                alert(response.error);
                location.reload();
            }
            reaction_processing[key] = false;
        },
        error: function() {
            alert('반응 처리 중 오류가 발생했습니다.');
            location.reload();
            reaction_processing[key] = false;
        }
    });
}

// 댓글 신고
function reportComment(bo_table, wr_id, comment_id) {
    if (!confirm('이 댓글을 신고하시겠습니까?')) {
        return;
    }
    
    var reason = prompt('신고 사유를 입력해주세요:');
    if (!reason) {
        return;
    }
    
    $.ajax({
        url: g5_bbs_url + '/ajax.comment_report.php',
        type: 'POST',
        data: {
            bo_table: bo_table,
            wr_id: wr_id,
            comment_id: comment_id,
            reason: reason
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('신고가 접수되었습니다.');
            } else {
                alert(response.message || '신고 처리 중 오류가 발생했습니다.');
            }
        },
        error: function() {
            alert('신고 처리 중 오류가 발생했습니다.');
        }
    });
}
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
    
    // 안드로이드 감지 함수
    function isAndroid() {
        return /android/i.test(navigator.userAgent);
    }
    
    // textarea 자동 높이 조절 및 스크롤 처리
    $(document).ready(function() {
        var $textarea = $('#wr_content');
        var lastHeight = 0;
        var scrollTimeout = null;
        
        $textarea.on('input', function() {
            var el = this;
            
            // 높이 자동 조절
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
            el.style.minHeight = '150px';
            
            // 높이가 변경되었을 때만 스크롤 처리
            var currentHeight = el.scrollHeight;
            if (currentHeight !== lastHeight) {
                lastHeight = currentHeight;
                
                // 안드로이드에서 자동 스크롤 처리
                if (isAndroid()) {
                    // 기존 타이머 취소
                    if (scrollTimeout) {
                        clearTimeout(scrollTimeout);
                    }
                    
                    // 약간의 지연 후 스크롤 (키보드 애니메이션 대기)
                    scrollTimeout = setTimeout(function() {
                        // 커서 위치가 보이도록 스크롤
                        var cursorPos = el.selectionStart;
                        var textBeforeCursor = el.value.substring(0, cursorPos);
                        var lines = textBeforeCursor.split('\n').length;
                        
                        // textarea 하단이 화면에 보이도록 스크롤
                        var rect = el.getBoundingClientRect();
                        var viewportHeight = window.innerHeight;
                        var keyboardHeight = viewportHeight * 0.4; // 키보드 높이 추정
                        
                        // textarea 하단이 키보드에 가려지는지 확인
                        if (rect.bottom > viewportHeight - keyboardHeight) {
                            // 스크롤 필요
                            var scrollAmount = rect.bottom - (viewportHeight - keyboardHeight) + 50;
                            
                            // 부드러운 스크롤
                            window.scrollBy({
                                top: scrollAmount,
                                behavior: 'smooth'
                            });
                        }
                        
                        // textarea 내부 스크롤도 하단으로
                        el.scrollTop = el.scrollHeight;
                        
                    }, 100);
                }
            }
        });
        
        // 포커스 시에도 스크롤 처리 (안드로이드)
        $textarea.on('focus', function() {
            if (isAndroid()) {
                var el = this;
                setTimeout(function() {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        });
        
        // 터치 이벤트로 키보드 올라올 때 처리 (안드로이드)
        if (isAndroid()) {
            var initialViewportHeight = window.innerHeight;
            
            window.addEventListener('resize', function() {
                // 키보드가 올라왔는지 확인 (화면 높이 감소)
                if (window.innerHeight < initialViewportHeight * 0.75) {
                    // 키보드가 올라온 상태
                    if (document.activeElement === $textarea[0]) {
                        setTimeout(function() {
                            $textarea[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 100);
                    }
                }
            });
        }
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
                
                <button type="button" id="nickname-suggest-btn" style="height:30px; margin-left:0px;">닉네임추천</button>
                <script>
                let nicknameList = [];

                function loadNicknames() {
                    fetch('<?php echo $board_skin_url ?>/nickname_list.txt')
                        .then(response => response.text())
                        .then(text => {
                            nicknameList = text.split('\n').map(name => name.trim()).filter(name => name !== '');
                        })
                        .catch(error => {
                            console.error('닉네임 목록 로드 실패:', error);
                        });
                }

                document.addEventListener("DOMContentLoaded", function () {
                    loadNicknames();

                    const btn = document.getElementById('nickname-suggest-btn');
                    if (btn) {
                        btn.addEventListener('click', function () {
                            if (nicknameList.length > 0) {
                                const randomName = nicknameList[Math.floor(Math.random() * nicknameList.length)];
                                document.getElementById('wr_name').value = randomName;
                            } else {
                                alert("닉네임 리스트를 불러오지 못했습니다.");
                            }
                        });
                    }
                });
                </script>
                
            </ul>
            <ul class="bo_vc_w_info_ul2">
            <?php echo $captcha_html; ?>
            </ul>
            <div class="cb"></div>
        </div>
        
        <?php } else if(!$is_admin) { ?>
        
        <div class="bo_vc_w_info">
            <ul class="bo_vc_w_info_ul1">
                <label for="wr_name" class="sound_only">이름<strong> 필수</strong></label>
                <input type="text" name="wr_name" value="<?php echo get_cookie("ck_sns_name"); ?>" id="wr_name" required class="frm_input required" size="25" placeholder="이름">
                
                <button type="button" id="nickname-suggest-btn" style="height:30px; margin-left:0px;">닉네임추천</button>
                <script>
                let nicknameList = [];

                function loadNicknames() {
                    fetch('<?php echo $board_skin_url ?>/nickname_list.txt')
                        .then(response => response.text())
                        .then(text => {
                            nicknameList = text.split('\n').map(name => name.trim()).filter(name => name !== '');
                        })
                        .catch(error => {
                            console.error('닉네임 목록 로드 실패:', error);
                        });
                }

                document.addEventListener("DOMContentLoaded", function () {
                    loadNicknames();

                    const btn = document.getElementById('nickname-suggest-btn');
                    if (btn) {
                        btn.addEventListener('click', function () {
                            if (nicknameList.length > 0) {
                                const randomName = nicknameList[Math.floor(Math.random() * nicknameList.length)];
                                document.getElementById('wr_name').value = randomName;
                            } else {
                                alert("닉네임 리스트를 불러오지 못했습니다.");
                            }
                        });
                    }
                });
                </script>
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
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

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
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
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

    // 댓글 아이디가 넘어오면 답변, 수정
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
        //입력값 초기화
        document.getElementById('wr_content').value = '';
        
        // 댓글 수정
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

comment_box('', 'c'); // 댓글 입력폼이 보이도록 처리하기위해서 추가 (root님)

<?php if($board['bo_use_sns'] && ($config['cf_facebook_appid'] || $config['cf_twitter_key'])) { ?>

$(function() {
    // sns 등록
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