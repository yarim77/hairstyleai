<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css?ver='.G5_TIME_YMDHIS.'">', 0);
$thumb_width = 120;
$thumb_height = 120;

$ca = isset($_GET['ca']) ? $_GET['ca'] : '';
$bo_table_selected = isset($_GET['bo_table']) ? $_GET['bo_table'] : '';

// 카카오 JavaScript API 키 가져오기
$kakao_js_key = '';
if(isset($config['cf_kakao_js_apikey'])) {
    $kakao_js_key = $config['cf_kakao_js_apikey'];
} else {
    $kakao_key_row = sql_fetch("SELECT cf_value FROM {$g5['config_table']} WHERE cf_name = 'cf_kakao_js_apikey'");
    if($kakao_key_row) {
        $kakao_js_key = $kakao_key_row['cf_value'];
    }
}

// 관리자가 설정한 레벨별 등급명과 설명 가져오기
$level_info = array();
for($i=1; $i<=10; $i++) {
    $row = sql_fetch("SELECT cf_value FROM {$g5['config_table']} WHERE cf_name = 'level_requirement_{$i}'");
    if($row) {
        $level_info[$i] = json_decode($row['cf_value'], true);
    }
}

// 기본값 설정 (DB에 없을 경우)
if(empty($level_info)) {
    $level_info = array(
        1 => array('name' => '헤린이', 'desc' => '이제 막 시작한 새싹 디자이너'),
        2 => array('name' => '새싹디자이너', 'desc' => '첫 활동을 시작한 신입 활동러'),
        3 => array('name' => '루키 스타', 'desc' => '첫 활동을 시작한 신입 활동러'),
        4 => array('name' => '슈퍼 스타', 'desc' => '활발하게 활동하는 인기 디자이너'),
        5 => array('name' => '신의손', 'desc' => '전설의 활동왕, 모두가 인정'),
        6 => array('name' => '특별회원', 'desc' => '특별 권한 회원'),
        7 => array('name' => '명예회원', 'desc' => '명예 회원'),
        8 => array('name' => '골드회원', 'desc' => '골드 등급 회원'),
        9 => array('name' => '다이아회원', 'desc' => '다이아몬드 등급 회원'),
        10 => array('name' => '최고관리자', 'desc' => '사이트 관리자')
    );
}

// 현재 회원의 레벨 정보
$member_level_name = isset($level_info[$mb['mb_level']]['name']) ? $level_info[$mb['mb_level']]['name'] : $mb['mb_level'].'레벨';
$member_level_desc = isset($level_info[$mb['mb_level']]['desc']) ? $level_info[$mb['mb_level']]['desc'] : '';

// 레벨 아이콘 파일 확인
$level_icon_file = G5_DATA_PATH.'/member_level/level_'.$mb['mb_level'].'.png';
$level_icon_url = '';
if(file_exists($level_icon_file)) {
    $level_icon_url = G5_DATA_URL.'/member_level/level_'.$mb['mb_level'].'.png?v='.filemtime($level_icon_file);
}

// 추천인 링크 생성
$referral_link = "/bbs/register.php?mb_recommend=".urlencode($mb['mb_id']);
$referral_full_link = G5_URL.$referral_link;
?>

<style>
    #container_title {display: none;}
    
    /* 레벨 아이콘 스타일 */
    .rb_prof_info_nick {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .level_name {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: #f5f5f5;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        color: #666;
    }
    
    .level_icon {
        width: 20px;
        height: 20px;
        object-fit: contain;
    }
    
    /* 게시판 서브탭 스타일 추가 */
    .rb_board_subtab {
        padding: 10px 0px;
        margin-bottom: 20px;
    }
    
    .rb_board_subtab ul {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .rb_board_subtab li {
        display: inline-block;
    }
    
    .rb_board_subtab a {
        display: block;
        padding: 8px 16px;
        background: #fff;
        border: 1px solid #000;
        border-radius: 5px;
        color: #000;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .rb_board_subtab a:hover {
        border-color: #000;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .rb_board_subtab a.active {
        background: #fff;
        color: #7a4efe;
        border-color: #7a4efe;
        border-width: 1px;
        font-weight: 700;
    }
    
    /* 추천인 공유 팝업 스타일 */
    .referral-share-popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    
    .referral-share-popup.active {
        display: flex;
    }
    
    .referral-share-content {
        background: #fff;
        border-radius: 12px;
        padding: 30px;
        max-width: 500px;
        width: 90%;
        position: relative;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .referral-share-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .referral-share-header h3 {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }
    
    .referral-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .referral-close-btn:hover {
        color: #000;
    }
    
    .referral-link-box {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .referral-link-label {
        font-size: 14px;
        color: #666;
        margin-bottom: 8px;
        display: block;
    }
    
    .referral-link-input-box {
        display: flex;
        gap: 10px;
        flex-direction: column;
    }
    
    .referral-link-input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        background: #fff;
    }
    
    .referral-copy-btn {
        padding: 10px 20px;
        background: #7a4efe;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
    }
    
    .referral-copy-btn:hover {
        background: #6840e0;
    }
    
    .referral-share-methods {
        margin-top: 25px;
    }
    
    .referral-share-methods h4 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }
    
    .referral-share-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 10px;
    }
    
    .referral-share-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px 10px;
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        color: #333;
        min-height: 90px;
    }
    
    .referral-share-btn:hover {
        background: #fff;
        border-color: #7a4efe;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(122, 78, 254, 0.15);
    }
    
    .referral-share-btn svg {
        width: 32px;
        height: 32px;
        margin-bottom: 8px;
        display: block;
        flex-shrink: 0;
    }
    
    .referral-share-btn:hover svg {
        transform: scale(1.1);
        transition: transform 0.2s;
    }
    
    .referral-share-btn span {
        font-size: 13px;
        font-weight: 500;
    }
    
    .referral-benefits {
        background: #f0e6ff;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
    }
    
    .referral-benefits h5 {
        font-size: 14px;
        font-weight: 600;
        color: #7a4efe;
        margin: 0 0 10px 0;
    }
    
    .referral-benefits ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .referral-benefits li {
        font-size: 13px;
        color: #666;
        margin-bottom: 5px;
    }
    
    /* 추천인 버튼 스타일 */
    .referral-share-trigger-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #7a4efe;
        color: #fff !important;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        height: 35px;
    }
    
    .referral-share-trigger-btn:hover {
        background: #6840e0;
        transform: translateY(-1px);
    }
    
    .referral-share-trigger-btn svg {
        width: 16px;
        height: 16px;
    }
    
    /* 버튼 리스트 정렬 */
    .copy_urls li {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* 버튼 기본 스타일 */
    .fl_btns {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
        height: 32px;
    }
    
    /* SVG 아이콘 스타일 */
    .fl_btns svg {
        width: 20px;
        height: 20px;
        color: #666;
    }
    
    /* 이미지 정렬 */
    .fl_btns img {
        width: 20px;
        height: 20px;
        vertical-align: middle;
    }
    
    /* 텍스트 버튼 추가 스타일 */
    .fl_btns_txt {
        padding: 0 12px;
        line-height: 32px;
    }
    
    @media (max-width: 768px) {
        .rb_board_subtab {
            padding: 10px;
        }

        .rb_board_subtab a {
            padding: 6px 12px;
            font-size: 13px;
        }

        .referral-share-content {
            padding: 20px;
        }

        .referral-share-buttons {
            grid-template-columns: repeat(3, 1fr);
        }

        .mobile-only-btn {
            display: inline-flex !important;
        }

        /* 버튼 영역 모바일 최적화 */
        .rb_prof .copy_urls li {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: flex-start;
        }

        .rb_prof .copy_urls {
            height: auto !important;
            padding: 10px;
            line-height: normal;
        }

        /* 모든 버튼 크기 통일 */
        .rb_prof_btn .fl_btns,
        .rb_prof_btn .fl_btns_txt,
        .referral-share-trigger-btn {
            height: 32px !important;
            line-height: 32px !important;
            padding: 0 12px !important;
            font-size: 12px !important;
            white-space: nowrap;
            margin: 0 !important;
        }

        /* 아이콘 버튼 */
        .rb_prof_btn .fl_btns img,
        .rb_prof_btn .fl_btns svg {
            width: 20px !important;
            height: 20px !important;
        }

        /* 추천인 버튼 SVG */
        .referral-share-trigger-btn svg {
            width: 14px !important;
            height: 14px !important;
        }

        /* 시스템설정 버튼 SVG 크기 증가 */
        .rb_prof_btn .mobile-only-btn svg,
        .copy_urls .mobile-only-btn svg {
            width: 24px !important;
            height: 24px !important;
            min-width: 24px !important;
            min-height: 24px !important;
        }

        .rb_prof_btn .mobile-only-btn,
        .copy_urls .mobile-only-btn {
            width: 40px !important;
            height: 32px !important;
            padding: 0 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    }
    
    @media (max-width: 480px) {
        .referral-share-buttons {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .referral-share-btn {
            padding: 10px 5px;
        }
        
        .referral-share-btn span {
            font-size: 11px;
        }
    }
</style>

<div class="rb_prof rb_prof_new">
   
    <ul class="rb_prof_info">
        <div class="rb_prof_info_img">
        <span id="prof_image_ch"><?php echo get_member_profile_img($mb['mb_id']); ?></span>
        <?php if($mb['mb_id'] == $member['mb_id']) { ?>
        <button type="button" id="prof_ch_btn">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.58597 1.1C9.93996 0.746476 10.4136 0.538456 10.9134 0.516981C11.4132 0.495507 11.903 0.662139 12.286 0.984002L12.414 1.101L14.304 3H17C17.5044 3.00009 17.9901 3.19077 18.3599 3.53384C18.7297 3.8769 18.9561 4.34702 18.994 4.85L19 5V7.686L20.9 9.586C21.2538 9.94004 21.462 10.4139 21.4834 10.9139C21.5049 11.414 21.3381 11.9039 21.016 12.287L20.899 12.414L18.999 14.314V17C18.9991 17.5046 18.8086 17.9906 18.4655 18.3605C18.1224 18.7305 17.6521 18.9572 17.149 18.995L17 19H14.315L12.415 20.9C12.0609 21.2538 11.5871 21.462 11.087 21.4835C10.587 21.505 10.097 21.3382 9.71397 21.016L9.58697 20.9L7.68697 19H4.99997C4.49539 19.0002 4.0094 18.8096 3.63942 18.4665C3.26944 18.1234 3.04281 17.6532 3.00497 17.15L2.99997 17V14.314L1.09997 12.414C0.746165 12.06 0.537968 11.5861 0.516492 11.0861C0.495016 10.586 0.661821 10.0961 0.98397 9.713L1.09997 9.586L2.99997 7.686V5C3.00006 4.4956 3.19074 4.00986 3.53381 3.64009C3.87687 3.27032 4.34699 3.04383 4.84997 3.006L4.99997 3H7.68597L9.58597 1.1ZM11 8C10.2043 8 9.44126 8.31607 8.87865 8.87868C8.31604 9.44129 7.99997 10.2044 7.99997 11C7.99997 11.7957 8.31604 12.5587 8.87865 13.1213C9.44126 13.6839 10.2043 14 11 14C11.7956 14 12.5587 13.6839 13.1213 13.1213C13.6839 12.5587 14 11.7957 14 11C14 10.2044 13.6839 9.44129 13.1213 8.87868C12.5587 8.31607 11.7956 8 11 8Z" fill="#09244B"/>
            </svg>
        </button>
        <?php } ?>
        <input type="file" id="prof_image_ch_input" style="display:none" accept="image/*" readonly>
        
        <script>
            $(document).ready(function(){
                $('#prof_ch_btn').on('click', function() {
                    $('#prof_image_ch_input').click();
                });

                $('#prof_image_ch_input').on('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const img = new Image();
                        img.onload = function() {
                                const formData = new FormData();
                                formData.append('profile_image', file);

                                $.ajax({
                                    url: '<?php echo G5_URL ?>/rb/rb.lib/ajax.upload_prof_image.php',
                                    type: 'POST',
                                    data: formData,
                                    contentType: false,
                                    processData: false,
                                    success: function(response) {
                                        const data = JSON.parse(response);
                                        if (data.success) {
                                            $('#prof_image_ch').html('<img src="' + data.image_url + '" alt="profile_image">');
                                        } else {
                                            alert(data.message);
                                        }
                                    }
                                });
                            
                        }
                        img.src = URL.createObjectURL(file);
                    }
                });
            });
        </script>
        </div>

        <div class="rb_prof_info_info">
            <li class="rb_prof_info_nick font-B">
                <?php echo $mb['mb_nick'] ?>
                <span class="level_name">
                    <?php if($level_icon_url) { ?>
                    <img src="<?php echo $level_icon_url ?>" alt="<?php echo $member_level_name ?>" class="level_icon">
                    <?php } ?>
                    <?php echo $member_level_name ?>
                </span>
            </li>

            <li class="rb_prof_info_txt">
            <span>게시물 <?php echo number_format(wr_cnt($mb['mb_id'], "w")); ?>개</span>
            <span>댓글 <?php echo number_format(wr_cnt($mb['mb_id'], "c")); ?>개</span>
            <?php if(isset($sb['sb_use']) && $sb['sb_use'] == 1) { ?><?php echo sb_cnt($mb['mb_id']) ?><?php } ?>
            </li>
            
        </div>
        
        <div class="cb"></div>
        
        <?php if(isset($mb['mb_profile']) && $mb['mb_profile']) { ?>
        <li class="rb_prof_info_txt"><?php echo $mb['mb_profile'] ?></li>
        <?php } ?>
        
    </ul>

    <ul class="rb_prof_btn">
    <div id="bo_v_share">
        <ul class="copy_urls">
            <li>
                <a class="fl_btns" id="data-copy" title="미니홈 링크 복사" alt="미니홈 링크 복사" href="javascript:void(0);">
                    <img src="<?php echo G5_THEME_URL ?>/rb.img/icon/ico_link.svg">
                </a>

                <?php if($mb['mb_id'] == $member['mb_id']) { ?>
                    <!-- 추천인 링크 공유 버튼 추가 -->
                    <a href="javascript:void(0);" class="referral-share-trigger-btn" onclick="openReferralShare()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                        공유
                    </a>
                    
                    <a class="fl_btns fl_btns_txt fl_btns_txt_mgl" title="정보수정" alt="정보수정" href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php">정보수정</a>

                    <!-- 계정 삭제 버튼 (Google Play 데이터 안전 정책 준수) -->
                    <a class="fl_btns fl_btns_txt" title="계정삭제" alt="계정삭제" href="<?php echo G5_URL ?>/member/delete_account.php" style="color:#e74c3c;">계정삭제</a>

                    <!-- 모바일에서만 표시되는 시스템설정 버튼 -->
                    <a class="fl_btns mobile-only-btn" title="시스템설정" alt="시스템설정" href="<?php echo G5_URL ?>/rb/settings.php" style="display: none;">
                        <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 1280 1280" preserveAspectRatio="xMidYMid meet">
                            <g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" fill="currentColor" stroke="none">
                                <path d="M6033 12790 c-409 -27 -913 -104 -973 -149 -61 -44 -60 -36 -60 -519 l-1 -443 -22 -9 c-12 -4 -98 -31 -192 -60 -190 -58 -383 -128 -555 -203 -136 -58 -420 -199 -505 -250 l-60 -36 -310 308 c-170 170 -320 314 -332 320 -30 15 -97 14 -136 -3 -41 -17 -256 -171 -412 -295 -394 -312 -817 -735 -1126 -1126 -186 -235 -297 -397 -309 -450 -18 -78 -10 -88 325 -425 l314 -315 -36 -60 c-51 -86 -192 -370 -250 -505 -75 -172 -145 -365 -203 -555 -29 -93 -56 -180 -60 -192 l-9 -22 -443 -1 c-485 0 -474 1 -521 -62 -34 -46 -98 -426 -134 -793 -24 -252 -24 -838 0 -1090 36 -367 100 -748 135 -794 46 -62 36 -61 520 -61 l443 -1 9 -22 c4 -12 31 -98 60 -192 58 -190 128 -383 203 -555 58 -136 199 -420 250 -505 l36 -60 -308 -310 c-170 -170 -314 -320 -320 -332 -15 -30 -14 -97 3 -136 17 -41 171 -256 295 -412 312 -394 735 -817 1126 -1126 235 -186 397 -297 450 -309 78 -18 88 -10 425 325 l315 314 60 -36 c86 -51 370 -192 505 -250 172 -75 365 -145 555 -203 94 -29 180 -56 192 -60 l22 -9 1 -443 c0 -485 -1 -474 62 -521 46 -34 426 -98 793 -134 252 -24 838 -24 1090 0 367 36 748 100 794 135 62 46 61 36 61 520 l1 443 22 9 c12 4 99 31 192 60 190 58 383 128 555 203 136 58 420 199 505 250 l60 36 310 -308 c171 -170 320 -314 332 -320 30 -15 97 -14 136 3 41 17 256 171 412 295 394 312 817 735 1126 1126 186 235 297 397 309 450 18 78 10 88 -325 425 l-314 315 36 60 c51 86 192 370 250 505 75 172 145 365 203 555 29 94 56 180 60 192 l9 22 443 1 c485 0 474 -1 521 62 34 46 98 426 134 793 24 252 24 838 0 1090 -36 367 -100 748 -135 794 -46 62 -36 61 -520 61 l-443 1 -9 22 c-4 12 -31 99 -60 192 -58 190 -128 383 -203 555 -58 136 -199 420 -250 505 l-36 60 308 310 c170 171 314 320 320 332 15 30 14 97 -3 136 -17 41 -171 256 -295 412 -312 394 -735 817 -1126 1126 -235 186 -397 297 -450 309 -78 18 -88 10 -425 -325 l-315 -314 -60 36 c-86 51 -370 192 -505 250 -172 75 -365 145 -555 203 -93 29 -180 56 -192 60 l-22 9 -1 443 c0 485 1 474 -62 521 -46 34 -432 99 -783 132 -200 19 -733 27 -922 15z m660 -3441 c1017 -92 1935 -737 2383 -1674 170 -355 261 -715 283 -1118 41 -739 -194 -1457 -668 -2037 -471 -578 -1145 -953 -1901 -1060 -208 -29 -572 -29 -780 0 -660 93 -1242 382 -1705 845 -463 463 -752 1045 -845 1705 -29 208 -29 572 0 780 107 756 482 1430 1060 1901 619 505 1364 731 2173 658z"/>
                            </g>
                        </svg>
                    </a>
                    
                <?php } else { ?>
                    <a class="fl_btns" title="쪽지보내기" alt="쪽지보내기" href="<?php echo G5_BBS_URL ?>/memo_form.php?me_recv_mb_id=<?php echo $mb['mb_id'] ?>" onclick="win_memo(this.href); return false;">
                        <img src="<?php echo G5_THEME_URL ?>/rb.img/icon/ico_msg.svg">
                    </a>
                    
                    <?php if (isset($chat_set['ch_use']) && $chat_set['ch_use'] == 1) { ?>
                    <a class="fl_btns" title="채팅하기" alt="채팅하기" href="<?php echo G5_URL ?>/rb/chat_form.php?me_recv_mb_id=<?php echo $mb['mb_id'] ?>" onclick="win_chat(this.href); return false;">
                        <img src="<?php echo G5_THEME_URL ?>/rb.img/icon/ico_chat.svg">
                    </a>
                    <?php } ?>
                    
                    <?php 
                        if(isset($sb['sb_use']) && $sb['sb_use'] == 1) { // 구독 사용시
                            $sb_mb_id = $mb['mb_id'];
                            include_once(G5_PATH.'/rb/rb.mod/subscribe/subscribe_my.skin.php');
                        }
                    ?>
                <?php } ?>
            </li>
            <?php
            $currents_url = G5_URL."/rb/home.php?mb_id=".$mb['mb_id'];
            ?>
            <input type="hidden" id="data-area" class="data-area" value="<?php echo $currents_url ?>">
            <script>
                $(document).ready(function() {
                    $('#data-copy').click(function() {
                        $('#data-area').attr('type', 'text');
                        $('#data-area').select();
                        var copy = document.execCommand('copy');
                        $('#data-area').attr('type', 'hidden');
                        if (copy) {
                            alert("미니홈 링크가 복사 되었습니다.");
                        }
                    });
                });
            </script>
        </ul>
    </div>
</ul>

<!-- 추천인 링크 공유 팝업 -->
<div id="referralSharePopup" class="referral-share-popup">
    <div class="referral-share-content">
        <div class="referral-share-header">
            <h3>🎁 추천인 링크 공유하기</h3>
            <button class="referral-close-btn" onclick="closeReferralShare()">&times;</button>
        </div>
        
        <div class="referral-link-box">
            <label class="referral-link-label">나의 추천인 가입 링크</label>
            <div class="referral-link-input-box">
                <input type="text" id="referralLinkInput" class="referral-link-input" value="<?php echo $referral_full_link ?>" readonly>
                <button class="referral-copy-btn" onclick="copyReferralLink()">복사</button>
            </div>
        </div>
        
        <div class="referral-share-methods">
            <h4>SNS로 공유하기</h4>
            <div class="referral-share-buttons">
                <a href="javascript:void(0);" onclick="shareKakao()" class="referral-share-btn">
                    <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#FFE812"/>
                        <path d="M16 9c-3.85 0-7 2.38-7 5.32 0 1.89 1.33 3.57 3.29 4.48-.14.56-.56 2.03-.56 2.17 0 .14.07.28.21.28.07 0 .14 0 .21-.07.28-.21 2.52-1.68 3.01-2.03.28 0 .56.07.84.07 3.85 0 7-2.38 7-5.32S19.85 9 16 9z" fill="#3A1D1D"/>
                    </svg>
                    <span>카카오톡</span>
                </a>
                
                <a href="javascript:void(0);" onclick="shareNaver()" class="referral-share-btn">
                    <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <rect width="32" height="32" rx="16" fill="#03C75A"/>
                        <path d="M11 10v7.5L17.5 10H21v12h-3.5v-7.5L11 22H7.5V10H11z" fill="white"/>
                    </svg>
                    <span>네이버</span>
                </a>
                
                <a href="javascript:void(0);" onclick="shareFacebook()" class="referral-share-btn">
                    <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#1877F2"/>
                        <path d="M22 11h-2c-1 0-1.5.8-1.5 1.5V14h2.5l-.3 3h-2.2v8h-3v-8h-2v-3h2v-2c0-2.2 1.8-4 4-4H22v3z" fill="white"/>
                    </svg>
                    <span>페이스북</span>
                </a>
                
                <a href="javascript:void(0);" onclick="shareTwitter()" class="referral-share-btn">
                    <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#000000"/>
                        <path d="M19 9h2.5l-5.4 6.2L22.3 23h-5l-3.9-5.1L9 23H6.5l5.8-6.6L6.3 9h5.1l3.5 4.6L19 9z" fill="white"/>
                    </svg>
                    <span>X (트위터)</span>
                </a>
                
                <a href="javascript:void(0);" onclick="shareBand()" class="referral-share-btn">
                    <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#5EC14B"/>
                        <path d="M10 13h3c.8 0 1.5.7 1.5 1.5v1c0 .8-.7 1.5-1.5 1.5h-3v-4zm0 6h3c1.7 0 3-1.3 3-3v-1c0-1.7-1.3-3-3-3H8v11h2v-4zm8-6h1.5l2 4 2-4H25l-3 5.5V23h-1.5v-4.5L18 13z" fill="white"/>
                    </svg>
                    <span>밴드</span>
                </a>
                
                <a href="javascript:void(0);" onclick="copyLink()" class="referral-share-btn">
                    <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#6B7280"/>
                        <g fill="white">
                            <path d="M19.5 12h-3c-1.38 0-2.5 1.12-2.5 2.5s1.12 2.5 2.5 2.5h3c1.38 0 2.5-1.12 2.5-2.5s-1.12-2.5-2.5-2.5zm-3 3.5c-.55 0-1-.45-1-1s.45-1 1-1h3c.55 0 1 .45 1 1s-.45 1-1 1h-3z"/>
                            <path d="M12.5 15h3c1.38 0 2.5 1.12 2.5 2.5S16.88 20 15.5 20h-3c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5zm3 3.5c.55 0 1-.45 1-1s-.45-1-1-1h-3c-.55 0-1 .45-1 1s.45 1 1 1h3z"/>
                            <rect x="15" y="14" width="2" height="4"/>
                        </g>
                    </svg>
                    <span>링크복사</span>
                </a>
            </div>
        </div>
        
        <div class="referral-benefits">
            <h5>🎉 추천인 혜택</h5>
            <ul>
                <li>추천인과 가입자 모두 포인트 지급!</li>
                <li>추천인: <strong>1,000 포인트</strong> 즉시 지급</li>
                <li>신규가입자: <strong>500 포인트</strong> 즉시 지급</li>
                <li>추천 횟수 제한 없음</li>
            </ul>
        </div>
    </div>
</div>

</div>

<div class="rb_prof_tab">
    
    <div>
        
        <nav id="bo_cate" class="swiper-container swiper-container-category">
            <ul id="bo_cate_ul" class="swiper-wrapper swiper-wrapper-category">
                <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL ?>/rb/home.php?mb_id=<?php echo $mb['mb_id'] ?>" <?php if($ca == "") { ?>id="bo_cate_on"<?php } ?>>홈</a></li>
                <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL ?>/rb/home.php?mb_id=<?php echo $mb['mb_id'] ?>&ca=bbs" <?php if($ca == "bbs") { ?>id="bo_cate_on"<?php } ?>>새글</a></li>
                <?php if(isset($sb['sb_use']) && $sb['sb_use'] == 1) { ?>
                    <?php if($mb['mb_id'] == $member['mb_id']) { ?>
                    <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL ?>/rb/home.php?mb_id=<?php echo $mb['mb_id'] ?>&ca=fw" <?php if($ca == "fw") { ?>id="bo_cate_on"<?php } ?>>구독자</a></li>
                    <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL ?>/rb/home.php?mb_id=<?php echo $mb['mb_id'] ?>&ca=fn" <?php if($ca == "fn") { ?>id="bo_cate_on"<?php } ?>>내 구독</a></li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </nav>

        <script>
            $(document).ready(function(){
                $("#bo_cate_ul li").addClass("swiper-slide swiper-slide-category");
            });
            
            var activeElement = document.querySelector('.swiper-slide-category a#bo_cate_on');
            var initialSlideIndex = 0;

            if (activeElement) {
                var parentLi = activeElement.closest('.swiper-slide-category');
                var allSlides = document.querySelectorAll('.swiper-slide-category');
                initialSlideIndex = Array.prototype.indexOf.call(allSlides, parentLi);
            }
            
            var swiper = new Swiper('.swiper-container-category', {
                slidesPerView: 'auto',
                spaceBetween: 0,
                observer: true,
                observeParents: true,
                touchRatio: 1,
                initialSlide: initialSlideIndex,
            });
        </script>
        
    </div>

    <?php
    // 새글 탭일 때 게시판별 서브탭 표시
    if($ca == "bbs") {
        $board_list_sql = "SELECT DISTINCT a.bo_table, b.bo_subject 
                          FROM {$g5['board_new_table']} a
                          JOIN {$g5['board_table']} b ON a.bo_table = b.bo_table
                          WHERE a.mb_id = '{$mb['mb_id']}' 
                          AND a.wr_id = a.wr_parent
                          ORDER BY b.bo_order, b.bo_table";
        $board_list_result = sql_query($board_list_sql);
        
        $board_count = sql_num_rows($board_list_result);
        
        if($board_count > 1) {
    ?>
    <div class="rb_board_subtab">
        <ul>
            <li><a href="<?php echo G5_URL ?>/rb/home.php?mb_id=<?php echo $mb['mb_id'] ?>&ca=bbs" <?php if(!$bo_table_selected) { ?>class="active"<?php } ?>>전체</a></li>
            <?php
            while($board_row = sql_fetch_array($board_list_result)) {
                $active_class = ($bo_table_selected == $board_row['bo_table']) ? 'class="active"' : '';
            ?>
            <li><a href="<?php echo G5_URL ?>/rb/home.php?mb_id=<?php echo $mb['mb_id'] ?>&ca=bbs&bo_table=<?php echo $board_row['bo_table'] ?>" <?php echo $active_class ?>><?php echo $board_row['bo_subject'] ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <?php
        }
    }
    ?>
        
        <?php if($ca == "") { ?>
        <div>
            <ul class="cont_info_wrap">
                <li class="cont_info_wrap_l">
                    <dd>닉네임</dd>
                    <dd><?php echo $mb['mb_nick'] ?></dd>
                </li>
                <li class="cont_info_wrap_r">
                    <dd>회원레벨</dd>
                    <dd>
                        <?php echo $member_level_name ?>
                        <?php if($member_level_desc) { ?>
                        <br><span style="font-size:12px; color:#7a4efe; font-weight: 600;"><?php echo $member_level_desc ?></span>
                        <?php } ?>
                    </dd>
                </li>
                <div class="cb"></div>
            </ul>
            <ul class="cont_info_wrap">
                <li class="cont_info_wrap_l">
                    <dd>포인트</dd>
                    <dd>
                    <?php if($member['mb_id'] == $mb['mb_id']) { ?><a href="<?php echo G5_BBS_URL ?>/point.php" target="_blank" class="win_point"><?php } ?>
                        <?php echo number_format($mb['mb_point']) ?>P
                    <?php if($member['mb_id'] == $mb['mb_id']) { ?></a><?php } ?>
                    </dd>
                </li>
                <li class="cont_info_wrap_r">
                    <dd>가입일</dd>
                    <dd><?php echo ($member['mb_level'] >= $mb['mb_level']) ?  substr($mb['mb_datetime'],0,10) ." (+".number_format($mb_reg_after)."일)" : "알 수 없음";  ?></dd>
                </li>
                <div class="cb"></div>
            </ul>
            
            <?php
            // 추천인 통계 표시 (본인만 볼 수 있음)
            if($mb['mb_id'] == $member['mb_id']) {
                $recommend_count = sql_fetch("SELECT COUNT(*) as cnt FROM {$g5['member_table']} WHERE mb_recommend = '{$mb['mb_id']}'");
                $my_recommend_count = $recommend_count['cnt'];
                
                if($my_recommend_count > 0) {
            ?>
            <ul class="cont_info_wrap">
                <li class="cont_info_wrap_l">
                    <dd>추천 현황</dd>
                    <dd>
                        내가 추천한 회원: <strong style="color:#7a4efe;"><?php echo number_format($my_recommend_count) ?>명</strong>
                        <?php if($my_recommend_count > 0) { ?>
                        <br><span style="font-size:12px; color:#999;">추천 포인트 획득: <?php echo number_format($my_recommend_count * 1000) ?>P</span>
                        <?php } ?>
                    </dd>
                </li>
                <?php if($mb['mb_recommend']) { ?>
                <li class="cont_info_wrap_r">
                    <dd>나의 추천인</dd>
                    <dd><?php echo get_text($mb['mb_recommend']); ?></dd>
                </li>
                <?php } ?>
                <div class="cb"></div>
            </ul>
            <?php
                }
            }
            ?>
            
            <ul class="cont_info_wrap">
                <li class="cont_info_wrap_l">
                    <dd>운영채널</dd>
                    <dd>
                    <?php if($mb['mb_homepage']) { ?>
                    <a href="<?php echo $mb['mb_homepage'] ?>" target="_blank"><?php echo $mb['mb_homepage'] ?></a>
                    <?php } else { ?>
                    -
                    <?php } ?>
                    </dd>
                </li>
                <li class="cont_info_wrap_r">
                    <dd>최종접속</dd>
                    <dd><?php echo ($member['mb_level'] >= $mb['mb_level']) ? $mb['mb_today_login'] : "알 수 없음"; ?></dd>
                </li>
                <div class="cb"></div>
            </ul>
            
        </div>
        
        <?php if(isset($rb_builder['bu_mini_use1']) && $rb_builder['bu_mini_use1'] == 1) { ?>
        <?php
        // 최근 7일 배열 준비 (오늘 포함)
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $dates[] = date('Y-m-d', strtotime("-{$i} days"));
        }

        // 초기 카운터 0으로 세팅
        $post_counts    = array_fill(0, count($dates), 0);
        $comment_counts = array_fill(0, count($dates), 0);

        $bn_table = isset($g5['board_new_table']) ? $g5['board_new_table'] : G5_TABLE_PREFIX.'board_new';

        $from_dt = $dates[0] . ' 00:00:00';

        $sql = "
            SELECT DATE(bn_datetime) AS ymd,
                   SUM(CASE WHEN wr_id = wr_parent THEN 1 ELSE 0 END) AS posts,
                   SUM(CASE WHEN wr_id <> wr_parent THEN 1 ELSE 0 END) AS comments
            FROM {$bn_table}
            WHERE bn_datetime >= '{$from_dt}' and mb_id = '{$mb['mb_id']}' 
            GROUP BY DATE(bn_datetime)
            ORDER BY ymd
        ";
        $res = sql_query($sql);

        $idx_map = array_flip($dates);
        while ($row = sql_fetch_array($res)) {
            $ymd = $row['ymd'];
            if (isset($idx_map[$ymd])) {
                $i = $idx_map[$ymd];
                $post_counts[$i]    = (int)$row['posts'];
                $comment_counts[$i] = (int)$row['comments'];
            }
        }
        ?>

        <div class="minihome_charts">
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

            <div class="rb_chart rb_chart_div1">
                <li class="bbs_main_wrap_tit_l">
                    <h2 class="font-B font-18">최근 게시물 현황</h2>
                </li>
                <li class="bbs_main_wrap_tit_r mt-20">
                    <button type="button" class="more_btn" onclick="location.href='<?php echo G5_BBS_URL ?>/search.php?stx=<?php echo $mb['mb_id'] ?>&sfl=mb_id&sop=and';">더보기</button>
                </li>
                <div class="cb"></div>
                <div id="rb-chart1" class="font-R"></div>
            </div>

            <div class="cb"></div>

            <script>
                function numberWithCommas(x) {
                    try {
                        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    } catch (e) {
                        return x;
                    }
                }

                const categories = <?php echo json_encode($dates, JSON_UNESCAPED_UNICODE); ?>;
                const postSeries = <?php echo json_encode(array_map('intval', $post_counts)); ?>;
                const cmtSeries = <?php echo json_encode(array_map('intval', $comment_counts)); ?>;

                const postMax = (postSeries.length ? Math.max.apply(null, postSeries) : 0);
                const cmtMax  = (cmtSeries.length  ? Math.max.apply(null, cmtSeries)  : 0);
                const overallMax = Math.max(1, postMax, cmtMax);

                const mainColor = '<?php echo isset($rb_config['co_color']) ? $rb_config['co_color'] : '#7b5cff'; ?>';
                const subColor = '#b989ff';

                const options1 = {
                    chart: {
                        type: 'line',
                        height: 260,
                        toolbar: { show: false }
                    },
                    series: [
                        { name: '글', data: postSeries },
                        { name: '댓글', data: cmtSeries }
                    ],
                    xaxis: {
                        categories: categories,
                        labels: {
                            style: { fontSize: '11px', colors: '#000' },
                            formatter: (val) => String(val).replace(/^\d{4}-/, '')
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        min: 0,
                        max: overallMax,
                        tickAmount: 6,
                        labels: {
                            show: false,
                            style: { fontSize:'11px', colors:'#000' },
                            formatter: (val) => numberWithCommas(val)
                        },
                        axisBorder: { show:false },
                        axisTicks: { show:false }
                    },
                    stroke: { width: [2, 2], curve: 'smooth' },
                    markers: { size: 0, hover: { size: 5 } },
                    dataLabels: {
                        enabled: true,
                        background: { enabled: false },
                        offsetY: -6,
                        style: { fontSize: '11px', colors: ['#000'] },
                        formatter: (val) => val ? numberWithCommas(val) + '건' : ''
                    },
                    colors: [mainColor, subColor],
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: { formatter: (val) => numberWithCommas(val) + '건' },
                        style: { fontSize: '11px' }
                    },
                    grid: {
                        show: true,
                        borderColor: '#e5e5ef',
                        strokeDashArray: 3,
                        yaxis: { lines: { show: true } },
                        xaxis: { lines: { show: false } }
                    },
                    legend: { show: true, fontSize: '11px' }
                };

                const chart1 = new ApexCharts(document.querySelector("#rb-chart1"), options1);
                chart1.render();
            </script>
        </div>
        <?php } ?>
        
        <?php if(isset($rb_builder['bu_mini_use2']) && $rb_builder['bu_mini_use2'] == 1) { ?>
        <?php if (($config['cf_admin'] ?? '') !== ($mb['mb_id'] ?? '')) { ?>
        <?php
            $target_mb_id = $mb['mb_id'];

            $day_labels  = [];
            $day_points  = [];

            for ($i = 6; $i >= 0; $i--) {
                $ts = strtotime("-{$i} day");
                $ds = date('Y-m-d 00:00:00', $ts);
                $de = date('Y-m-d 23:59:59', $ts);

                $day_labels[] = date('m-d', $ts);

                $sql_day = "
                    SELECT
                        SUM(IF(po_point > 0, po_point, 0)) AS total_points
                    FROM {$g5['point_table']}
                    WHERE mb_id = '".sql_real_escape_string($target_mb_id)."'
                      AND po_datetime >= '{$ds}'
                      AND po_datetime <= '{$de}'
                ";
                $row_day = sql_fetch($sql_day);
                $day_points[] = (int)($row_day['total_points'] ?? 0);
            }
        ?>
        <div class="minihome_charts">
            <div class="rb_chart rb_chart_div1">
                <li class="bbs_main_wrap_tit_l">
                    <h2 class="font-B font-18">최근 포인트 획득</h2>
                </li>
                <li class="bbs_main_wrap_tit_r mt-20"></li>
                <div class="cb"></div>
                <div id="rb-chart2" class="font-R"></div>
            </div>
            <div class="cb"></div>

            <script>
                const weekCats = <?php echo json_encode($day_labels, JSON_UNESCAPED_UNICODE); ?>;
                const weekVals = <?php echo json_encode(array_map('intval', $day_points)); ?>;

                const mainColor2 = '<?php echo $rb_config['co_color'] ?? "#7b5cff"; ?>';

                const BASE_STEP  = 1000;
                const MAX_TICKS  = 8;

                let rawMax = weekVals.length ? Math.max.apply(null, weekVals) : 0;
                if (rawMax <= 0) {
                    rawMax = BASE_STEP * 2;
                }

                let step  = BASE_STEP;
                while (Math.ceil(rawMax / step) > MAX_TICKS) {
                    step *= 2;
                }

                const ticks = Math.ceil(rawMax / step);
                const yMax  = ticks * step;

                const options2 = {
                    chart: {
                        type: 'bar',
                        height: 260,
                        toolbar: { show: false }
                    },
                    series: [{ name: '포인트', data: weekVals }],
                    xaxis: {
                        categories: weekCats,
                        labels: { style: { fontSize: '11px', colors: '#000' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        min: 0,
                        max: yMax,
                        tickAmount: ticks,
                        decimalsInFloat: 0,
                        labels: {
                            style: { fontSize: '11px', colors: '#000' },
                            formatter: (val) => numberWithCommas(val)
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '35%',
                            borderRadius: 6,
                            borderRadiusApplication: 'end',
                            borderRadiusWhenStacked: 'last',
                            dataLabels: { position: 'top' }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -20,
                        style: { fontSize: '11px', colors: ['#000'] },
                        formatter: (val) => val ? numberWithCommas(val) + '점' : ''
                    },
                    colors: [mainColor2],
                    tooltip: {
                        y: { formatter: (val) => numberWithCommas(val) + '점' },
                        style: { fontSize: '12px' }
                    },
                    grid: {
                        show: true,
                        borderColor: '#e5e5ef',
                        strokeDashArray: 3,
                        yaxis: { lines: { show: true } },
                        xaxis: { lines: { show: false } }
                    },
                    legend: { show: false }
                };

                const chart2 = new ApexCharts(document.querySelector("#rb-chart2"), options2);
                chart2.render();
            </script>
        </div>
        <?php } ?>
        <?php } ?>
        
        <?php if(empty($rb_builder['bu_mini_use3']) || $rb_builder['bu_mini_use3'] == 0) { ?>
        <style>
            .main_latest_inc {display: none;}
        </style>
        <?php } ?>
        
        <?php } ?>

        <?php if($ca == "bbs" || $ca == "") { ?>
        <div <?php if($ca == "") { ?>class="main_latest_inc"<?php } ?>>

            <ul class="cont_info_wrap cont_info_wrap_mmt">
                <?php
                    // 게시판 필터 조건 추가
                    $bo_table_condition = "";
                    if($bo_table_selected) {
                        $bo_table_condition = " and a.bo_table = '".sql_real_escape_string($bo_table_selected)."' ";
                    }
                    
                    $sql_commons = " from {$g5['board_new_table']} a, {$g5['board_table']} b where a.bo_table = b.bo_table and a.wr_id = a.wr_parent and a.mb_id = '{$mb['mb_id']}' and b.bo_use_search = '1' {$bo_table_condition} order by a.bn_id desc ";
                
                    if($ca == "bbs") {
                        
                        /* 페이징 추가 { */
                        $rpg_sql = " select count(*) as cnt {$sql_commons} ";
                        $rpg_row = sql_fetch($rpg_sql);
                        $rpg_total_count = $rpg_row['cnt'];

                        $rpg_rows = G5_IS_MOBILE ? 10 : 10;
                        $rpg_total_page  = ceil($rpg_total_count / $rpg_rows);
                        if ($page < 1) $page = 1;
                        $from_record = ($page - 1) * $rpg_rows;

                        // 페이징 URL에 bo_table 파라미터 추가
                        $paging_url = "?mb_id=$mb_id&amp;ca=$ca";
                        if($bo_table_selected) {
                            $paging_url .= "&amp;bo_table=$bo_table_selected";
                        }
                        $paging_url .= "&amp;page=";
                        
                        $rpg_write_pages = get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $rpg_total_page, $paging_url);
                        /* } */
                        
                        $sqls = " select a.*, b.bo_subject, b.bo_mobile_subject {$sql_commons} limit {$from_record}, {$rpg_rows} ";
                    } else { 
                        $sqls = " select a.*, b.bo_subject, b.bo_mobile_subject {$sql_commons} limit 10 ";
                    }
                    
                    $results = sql_query($sqls);
                ?>
                
                <div class="bbs_main">
                
                <ul class="bbs_main_wrap_thumb_con">
                    <div class="swiper-container swiper-container-home">
                        <ul class="swiper-wrapper swiper-wrapper-home">
                
                <?php 
                for ($i=0; $rows=sql_fetch_array($results); $i++) { 
                    $tmp_write_table = $g5['write_prefix'].$rows['bo_table'];
                    $row2 = sql_fetch(" select * from {$tmp_write_table} where wr_id = '{$rows['wr_id']}' ");
                    $hrefs = get_pretty_url($rows['bo_table'], $row2['wr_id']);
                    
                    $thumb = get_list_thumbnail($rows['bo_table'], $row2['wr_id'], $thumb_width, $thumb_height, false, true);
                    
                    if($thumb['src']) {
                        $img = $thumb['src'];
                    } else {
                        $img = G5_THEME_URL.'/rb.img/no_image.png';
                        $thumb['alt'] = '이미지가 없습니다.';
                    }
                    
                    $img_content = '<img src="'.$img.'" alt="'.$thumb['alt'].'" class="skin_list_image">';
                    $wr_href = get_pretty_url($rows['bo_table'], $row2['wr_id']);
                    $sec_txt = '<span style="opacity:0.6">작성자 및 관리자 외 열람할 수 없습니다.<br>비밀글 기능으로 보호된 글입니다.</span>';
                    
                    $wr_content = preg_replace("/<(.*?)\>/","",$row2['wr_content']);
                    $wr_content = preg_replace("/&nbsp;/","",$wr_content);
                    $wr_content = get_text($wr_content);
                ?>

                            <dd class="swiper-slide swiper-slide-home" onclick="location.href='<?php echo $hrefs ?>';">
                                
                                <div>
                                    
                                    <?php if($thumb['src']) { ?>
                                    <ul class="bbs_main_wrap_con_ul1">
                                        <a href="<?php echo $hrefs ?>"><?php echo run_replace('thumb_image_tag', $img_content, $thumb); ?></a>
                                    </ul>
                                    <?php } ?>

                                    <ul class="bbs_main_wrap_con_ul2" <?php if(!$thumb['src']) { ?>style="padding-right:0px;"<?php } ?>>
                                        <li class="bbs_main_wrap_con_subj cut"><a href="<?php echo $hrefs ?>"><?php echo $row2['wr_subject'] ?></a></li>
                                        
                                        <?php if (strstr($row2['wr_option'], 'secret')) { ?>
                                            <li class="bbs_main_wrap_con_cont">
                                                <?php echo $sec_txt; ?>
                                            </li>
                                        <?php } else { ?>
                                        <li class="bbs_main_wrap_con_cont cut2">
                                            <a href="<?php echo $hrefs ?>"><?php echo $wr_content; ?></a>
                                            </li>
                                        <?php } ?>

                                            <li class="bbs_main_wrap_con_info">
                                                <span class="prof_tiny_name font-B"><?php echo $row2['wr_name'] ?></span>
                                                <?php echo passing_time($row2['wr_datetime']) ?><br>
                                                
                                                <?php if($row2['ca_name']) { ?>
                                                <?php echo $rows['bo_subject'] ?> [<?php echo $row2['ca_name'] ?>]　
                                                <?php } else { ?>
                                                <?php echo $rows['bo_subject'] ?>　
                                                <?php } ?>
                                                
                                                댓글 <?php echo number_format($row2['wr_comment']); ?>　
                                                조회 <?php echo number_format($row2['wr_hit']); ?>　
                                                
                                            </li>

                                    </ul>
                                    <div class="cb"></div>
                                </div>
                            </dd>
                            
                            <?php }  ?>
                            <?php if ($i == 0) { ?>
                            <dd class="no_data" style="width:100% !important;">등록한 게시물이 없습니다.</dd>
                            <?php }  ?>
                            
                        </ul>
                    </div>
                    
                    <script>
                        var swiper = new Swiper('.swiper-container-home', {
                            slidesPerColumnFill: 'row',
                            slidesPerView: 2,
                            slidesPerColumn: 999,
                            spaceBetween: 20,
                            observer: true,
                            observeParents: true,
                            touchRatio: 0,
                            
                            breakpoints: {
                                1024: {
                                    slidesPerView: 2,
                                    slidesPerColumn: 999,
                                    spaceBetween: 20,
                                },
                                10: {
                                    slidesPerView: 1,
                                    slidesPerColumn: 999,
                                    spaceBetween: 20,
                                }
                            }
                        });
                    </script>
                    
                </ul>
                
            </div>

            </ul>
            
            <?php 
            if($ca == "bbs") {
                echo $rpg_write_pages;
            }
            ?>

        </div>
        <?php } ?>
        
        
        <?php 
        // 구독자/내구독 탭 콘텐츠
        if(isset($sb['sb_use']) && $sb['sb_use'] == 1) { 
            if($mb['mb_id'] == $member['mb_id']) {
                include_once(G5_PATH.'/rb/rb.mod/subscribe/subscribe_table.skin.php');
            } else { 
                if($ca == "fn" || $ca == "fw") { 
                    alert('올바른 방법으로 이용해주세요.');
                }
            }
        }
        ?>

    
</div>


<div class="cb"></div>

<!-- 카카오 SDK 로드 및 스크립트 -->
<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>
<script>
// 카카오 SDK 초기화
<?php if($kakao_js_key) { ?>
if (typeof Kakao !== 'undefined' && !Kakao.isInitialized()) {
    Kakao.init('<?php echo $kakao_js_key ?>');
    console.log('Kakao SDK initialized:', Kakao.isInitialized());
}
<?php } ?>

// 추천인 공유 팝업 열기
function openReferralShare() {
    document.getElementById('referralSharePopup').classList.add('active');
}

// 추천인 공유 팝업 닫기
function closeReferralShare() {
    document.getElementById('referralSharePopup').classList.remove('active');
}

// 추천인 링크 복사
function copyReferralLink() {
    const input = document.getElementById('referralLinkInput');
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(input.value)
            .then(() => {
                alert('추천인 링크가 복사되었습니다!');
            })
            .catch(() => {
                fallbackCopy(input);
            });
    } else {
        fallbackCopy(input);
    }
}

function fallbackCopy(input) {
    input.select();
    input.setSelectionRange(0, 99999);
    
    try {
        document.execCommand('copy');
        alert('추천인 링크가 복사되었습니다!');
    } catch (err) {
        alert('링크 복사에 실패했습니다. 직접 복사해주세요.');
    }
}

function copyLink() {
    copyReferralLink();
}

// 카카오톡 공유
function shareKakao() {
    const url = document.getElementById('referralLinkInput').value;
    const nickname = '<?php echo addslashes($mb['mb_nick']) ?>';
    
    <?php if($kakao_js_key) { ?>
    if (typeof Kakao !== 'undefined' && Kakao.isInitialized()) {
        Kakao.Share.sendDefault({
            objectType: 'feed',
            content: {
                title: '🎁 헤어왕 가입하고 포인트 받아가세요!',
                description: nickname + '님의 추천으로 가입하시면 500포인트를 즉시 드립니다! 추천인도 1,000포인트를 받습니다.',
                imageUrl: '<?php echo G5_URL ?>/img/sns_share.jpg',
                link: {
                    mobileWebUrl: url,
                    webUrl: url
                }
            },
            buttons: [
                {
                    title: '지금 가입하기',
                    link: {
                        mobileWebUrl: url,
                        webUrl: url
                    }
                }
            ],
            installTalk: true
        });
    } else {
        console.error('Kakao SDK not initialized');
        window.open('https://story.kakao.com/share?url=' + encodeURIComponent(url));
    }
    <?php } else { ?>
    alert('카카오톡 공유 기능이 설정되지 않았습니다.\n관리자에게 문의해주세요.');
    <?php } ?>
}

// 네이버 공유
function shareNaver() {
    const url = encodeURIComponent(document.getElementById('referralLinkInput').value);
    const title = encodeURIComponent('헤어왕 가입하고 포인트 받아가세요!');
    window.open('https://blog.naver.com/openapi/share?url=' + url + '&title=' + title);
}

// 페이스북 공유
function shareFacebook() {
    const url = encodeURIComponent(document.getElementById('referralLinkInput').value);
    window.open('https://www.facebook.com/sharer/sharer.php?u=' + url, 'facebook-share', 'width=600,height=400');
}

// 트위터 공유
function shareTwitter() {
    const url = encodeURIComponent(document.getElementById('referralLinkInput').value);
    const nickname = '<?php echo addslashes($mb['mb_nick']) ?>';
    const text = encodeURIComponent(nickname + '님이 헤어왕에 초대했습니다! 지금 가입하면 500포인트를 받을 수 있어요 💝');
    window.open('https://twitter.com/intent/tweet?url=' + url + '&text=' + text, 'twitter-share', 'width=600,height=400');
}

// 밴드 공유
function shareBand() {
    const url = encodeURIComponent(document.getElementById('referralLinkInput').value);
    const nickname = '<?php echo addslashes($mb['mb_nick']) ?>';
    const body = encodeURIComponent(nickname + '님의 추천으로 가입하면 500포인트를 즉시 받을 수 있습니다! 추천인도 1,000포인트를 받아요!');
    window.open('http://band.us/plugin/share?body=' + body + '&route=' + url);
}

// ESC 키로 팝업 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReferralShare();
    }
});

// 팝업 외부 클릭시 닫기
document.getElementById('referralSharePopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReferralShare();
    }
});

// 모바일 Web Share API
if (navigator.share) {
    function nativeShare() {
        const url = document.getElementById('referralLinkInput').value;
        const nickname = '<?php echo addslashes($mb['mb_nick']) ?>';
        
        navigator.share({
            title: '헤어왕 추천인 가입',
            text: nickname + '님의 추천으로 가입하시면 500포인트를 받을 수 있습니다!',
            url: url
        }).catch(err => console.log('Error sharing:', err));
    }
}
</script>