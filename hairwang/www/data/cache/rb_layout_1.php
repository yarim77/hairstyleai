<?php
    ob_start();

    $rb_module_table = 'rb_module';
    $GLOBALS['rb_module_table'] = $rb_module_table;
    $is_admin = '';

    // -- 회원 레벨 가시성 헬퍼 (관리자 제외)
    if (!function_exists('rb__level_visible')) {
        function rb__level_visible($mb_level, $rule, $level) {
            $mb_level = (int)$mb_level;
            $rule     = (int)$rule;
            $level    = (int)$level;
            if (!$rule || !$level) return true; // 설정 없으면 항상 출력

            switch ($rule) {
                case 1: return $mb_level === $level;
                case 2: return $mb_level !== $level;
                case 3: return $mb_level >=  $level;
                case 4: return $mb_level <   $level;
                case 5: return $mb_level >=  $level;
                case 6: return $mb_level <   $level;
                default: return true;
            }
        }
    }
    ?>
<?php
$row_mod = array (
  'md_id' => '36',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => 'banner1',
  'md_type' => 'banner',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '메인배너롤링',
  'md_banner_id' => '',
  'md_banner_skin' => 'rb.mod/banner/skin/rb.slide',
  'md_module' => '',
  'md_skin' => '',
  'md_cnt' => '0',
  'md_auto_time' => '0',
  'md_gap' => '0',
  'md_gap_mo' => '0',
  'md_col' => '0',
  'md_row' => '0',
  'md_col_mo' => '0',
  'md_row_mo' => '0',
  'md_width' => '100',
  'md_height' => 'auto',
  'md_subject_is' => '0',
  'md_thumb_is' => '0',
  'md_nick_is' => '0',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '0',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-10-20 01:15:12',
  'md_ip' => '39.118.123.133',
  'md_order_id' => '1',
  'md_banner_bg' => '#FFFFFF',
  'md_border' => '',
  'md_radius' => '20',
  'md_padding' => '0',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '-30',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282b',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '1',
  'md_order_latest' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => 'bn_order, bn_id desc',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '#DDDDDD',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '#25282b16',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:100%;" 
              data-order-id="1" 
              data-id="36" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="banner1" 
              data-shop="0"
            >

                <ul class="content_box rb_module_36    " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                    
                    
                    
                                            <div class="bbs_main_wrap_tit" style="display:none">
                            <ul class="bbs_main_wrap_tit_l">
                                <a href="javascript:void(0);">
                                    <h2 class="font-B" style="color:#25282b; font-size:20px; ">banner1</h2>
                                </a>
                            </ul>
                            <ul class="bbs_main_wrap_tit_r"></ul>
                            <div class="cb"></div>
                        </div>
                        <div class="rb-module-wrap module_banner_wrap md_arrow_0" 
                           style="
                           height:auto;                           border-color:#DDDDDD;                            border-radius:20px;                                                       background-color:#FFFFFF;                            
                           ">
                            <?php echo rb_banners("메인배너롤링", "", "rb.mod/banner/skin/rb.slide", "bn_order, bn_id desc"); ?>                        </div>
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-36"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '43',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '아이콘',
  'md_type' => 'widget',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => 'rb.widget/rb.icon_menu',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_module' => '',
  'md_skin' => '',
  'md_cnt' => '0',
  'md_auto_time' => '0',
  'md_gap' => '0',
  'md_gap_mo' => '0',
  'md_col' => '0',
  'md_row' => '0',
  'md_col_mo' => '0',
  'md_row_mo' => '0',
  'md_width' => '100',
  'md_height' => '89',
  'md_subject_is' => '0',
  'md_thumb_is' => '0',
  'md_nick_is' => '0',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '0',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-10-12 00:44:47',
  'md_ip' => '39.118.123.133',
  'md_order_id' => '2',
  'md_banner_bg' => '',
  'md_border' => '',
  'md_radius' => '0',
  'md_padding' => '0',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282B',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '0',
  'md_order_latest' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => '',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:100%;" 
              data-order-id="2" 
              data-id="43" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="아이콘" 
              data-shop="0"
            >

                <ul class="content_box rb_module_43    " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                    
                    
                                            <div class="rb-module-wrap module_widget_wrap" 
                           style="
                           height:89px;                                                                                                                                       ">
                            <?php @include (G5_PATH . "/rb/rb.widget/rb.icon_menu/widget.php"); ?>                        </div>
                    
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-43"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '64',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '레이싱',
  'md_type' => 'banner',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '게임',
  'md_banner_id' => '',
  'md_banner_skin' => 'rb.mod/banner/skin/rb.banner',
  'md_module' => '',
  'md_skin' => '',
  'md_cnt' => '0',
  'md_auto_time' => '0',
  'md_gap' => '0',
  'md_gap_mo' => '0',
  'md_col' => '0',
  'md_row' => '0',
  'md_col_mo' => '0',
  'md_row_mo' => '0',
  'md_width' => '50',
  'md_height' => '160',
  'md_subject_is' => '0',
  'md_thumb_is' => '0',
  'md_nick_is' => '0',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '0',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-12-29 11:31:42',
  'md_ip' => '106.101.135.155',
  'md_order_id' => '2',
  'md_banner_bg' => '#FFFFFF',
  'md_border' => '',
  'md_radius' => '0',
  'md_padding' => '0',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '0',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => 'pc',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282B',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '1',
  'md_order_latest' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => 'bn_order, bn_id desc',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '0',
  'md_border_color' => '#DDDDDD',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '#25282b16',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box pc" 
              style="width:50%;" 
              data-order-id="2" 
              data-id="64" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="레이싱" 
              data-shop="0"
            >

                <ul class="content_box rb_module_64  pc  " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                    
                    
                    
                                            <div class="bbs_main_wrap_tit" style="display:none">
                            <ul class="bbs_main_wrap_tit_l">
                                <a href="javascript:void(0);">
                                    <h2 class="font-B" style="color:#25282B; font-size:20px; ">레이싱</h2>
                                </a>
                            </ul>
                            <ul class="bbs_main_wrap_tit_r"></ul>
                            <div class="cb"></div>
                        </div>
                        <div class="rb-module-wrap module_banner_wrap md_arrow_0" 
                           style="
                           height:160px;                           border-color:#DDDDDD;                                                                                  background-color:#FFFFFF;                            
                           ">
                            <?php echo rb_banners("게임", "", "rb.mod/banner/skin/rb.banner", "bn_order, bn_id desc"); ?>                        </div>
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-64"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '65',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => 'rpdla',
  'md_type' => 'widget',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => 'rb.widget/rb.tarot_fortune',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_module' => '',
  'md_skin' => '',
  'md_cnt' => '0',
  'md_auto_time' => '0',
  'md_gap' => '0',
  'md_gap_mo' => '0',
  'md_col' => '0',
  'md_row' => '0',
  'md_col_mo' => '0',
  'md_row_mo' => '0',
  'md_width' => '50',
  'md_height' => '160',
  'md_subject_is' => '0',
  'md_thumb_is' => '0',
  'md_nick_is' => '0',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '0',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-12-22 11:03:54',
  'md_ip' => '211.181.180.18',
  'md_order_id' => '3',
  'md_banner_bg' => '#FFFFFF',
  'md_border' => '',
  'md_radius' => '0',
  'md_padding' => '0',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '0',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282B',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '1',
  'md_order_latest' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => '',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '0',
  'md_border_color' => '#DDDDDD',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '#25282b16',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:50%;" 
              data-order-id="3" 
              data-id="65" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="rpdla" 
              data-shop="0"
            >

                <ul class="content_box rb_module_65    " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                    
                    
                                            <div class="rb-module-wrap module_widget_wrap" 
                           style="
                           height:160px;                           border-color:#DDDDDD;                                                                                  background-color:#FFFFFF;                            ">
                            <?php @include (G5_PATH . "/rb/rb.widget/rb.tarot_fortune/widget.php"); ?>                        </div>
                    
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-65"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '38',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '비밀톡',
  'md_type' => 'latest',
  'md_bo_table' => 'anonymous',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_module' => '',
  'md_skin' => 'theme/rb.latest.thumb_right',
  'md_cnt' => '10',
  'md_auto_time' => '0',
  'md_gap' => '30',
  'md_gap_mo' => '30',
  'md_col' => '1',
  'md_row' => '5',
  'md_col_mo' => '1',
  'md_row_mo' => '4',
  'md_width' => '50',
  'md_height' => '695',
  'md_subject_is' => '1',
  'md_thumb_is' => '0',
  'md_nick_is' => '1',
  'md_date_is' => '1',
  'md_content_is' => '1',
  'md_icon_is' => '1',
  'md_comment_is' => '1',
  'md_ca_is' => '1',
  'md_swiper_is' => '1',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-12-02 19:19:52',
  'md_ip' => '221.142.31.47',
  'md_order_id' => '4',
  'md_banner_bg' => '#FFFFFF',
  'md_border' => 'dashed',
  'md_radius' => '10',
  'md_padding' => '20',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282b',
  'md_title_size' => '20',
  'md_title_font' => 'font-H',
  'md_title_hide' => '0',
  'md_order_latest' => 'wr_num',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => '',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '#DDDDDD',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '#25282b16',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:50%;" 
              data-order-id="4" 
              data-id="38" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="비밀톡" 
              data-shop="0"
            >

                <ul class="content_box rb_module_38 rb_module_border_dashed rb_module_border_width_1  rb_module_padding_20   " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                                            <div class="rb-module-wrap module_latest_wrap md_arrow_0" 
                           style="
                           height:695px;                           border-color:#DDDDDD;                            border-radius:10px;                                                       background-color:#FFFFFF;                                                                                                                                        ">
                            <?php echo rb_latest("anonymous", "theme/rb.latest.thumb_right", 10, 999, 1, 38, "", "wr_num", "rb_module", "0"); ?>                        </div>
                    
                    
                    
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-38"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '39',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '공지사항',
  'md_type' => 'latest',
  'md_bo_table' => 'notice',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_module' => '',
  'md_skin' => 'theme/rb.latest.thumb_right',
  'md_cnt' => '10',
  'md_auto_time' => '0',
  'md_gap' => '30',
  'md_gap_mo' => '30',
  'md_col' => '1',
  'md_row' => '5',
  'md_col_mo' => '1',
  'md_row_mo' => '4',
  'md_width' => '50',
  'md_height' => 'auto',
  'md_subject_is' => '1',
  'md_thumb_is' => '0',
  'md_nick_is' => '1',
  'md_date_is' => '1',
  'md_content_is' => '1',
  'md_icon_is' => '1',
  'md_comment_is' => '1',
  'md_ca_is' => '1',
  'md_swiper_is' => '1',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-09-25 17:30:53',
  'md_ip' => '124.49.246.150',
  'md_order_id' => '5',
  'md_banner_bg' => '',
  'md_border' => 'dashed',
  'md_radius' => '10',
  'md_padding' => '20',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282b',
  'md_title_size' => '20',
  'md_title_font' => 'font-H',
  'md_title_hide' => '0',
  'md_order_latest' => 'wr_num',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => '',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:50%;" 
              data-order-id="5" 
              data-id="39" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="공지사항" 
              data-shop="0"
            >

                <ul class="content_box rb_module_39 rb_module_border_dashed rb_module_border_width_1  rb_module_padding_20   " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                                            <div class="rb-module-wrap module_latest_wrap md_arrow_0" 
                           style="
                           height:auto;                                                      border-radius:10px;                                                                                                                                                                                              ">
                            <?php echo rb_latest("notice", "theme/rb.latest.thumb_right", 10, 999, 1, 39, "", "wr_num", "rb_module", "0"); ?>                        </div>
                    
                    
                    
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-39"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '60',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '중간배너',
  'md_type' => 'banner',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '중간배너',
  'md_banner_id' => '',
  'md_banner_skin' => 'rb.mod/banner/skin/rb.slide',
  'md_module' => '',
  'md_skin' => '',
  'md_cnt' => '0',
  'md_auto_time' => '0',
  'md_gap' => '0',
  'md_gap_mo' => '0',
  'md_col' => '0',
  'md_row' => '0',
  'md_col_mo' => '0',
  'md_row_mo' => '0',
  'md_width' => '100',
  'md_height' => 'auto',
  'md_subject_is' => '0',
  'md_thumb_is' => '0',
  'md_nick_is' => '0',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '0',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-10-02 19:28:52',
  'md_ip' => '39.118.123.133',
  'md_order_id' => '6',
  'md_banner_bg' => '',
  'md_border' => '',
  'md_radius' => '0',
  'md_padding' => '0',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '10',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '10',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282B',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '1',
  'md_order_latest' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => '',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:100%;" 
              data-order-id="6" 
              data-id="60" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="중간배너" 
              data-shop="0"
            >

                <ul class="content_box rb_module_60    " 
                    
                    style="                    margin-top:10px; 
                      margin-bottom:10px;
                    ">
                    
                    

                    
                    
                    
                                            <div class="bbs_main_wrap_tit" style="display:none">
                            <ul class="bbs_main_wrap_tit_l">
                                <a href="javascript:void(0);">
                                    <h2 class="font-B" style="color:#25282B; font-size:20px; ">중간배너</h2>
                                </a>
                            </ul>
                            <ul class="bbs_main_wrap_tit_r"></ul>
                            <div class="cb"></div>
                        </div>
                        <div class="rb-module-wrap module_banner_wrap md_arrow_0" 
                           style="
                           height:auto;                                                                                                                                       
                           ">
                            <?php echo rb_banners("중간배너", "", "rb.mod/banner/skin/rb.slide", ""); ?>                        </div>
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-60"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '25',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '이벤트',
  'md_type' => 'latest',
  'md_bo_table' => 'activity',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_module' => '',
  'md_skin' => 'theme/rb.latest.thumb_top',
  'md_cnt' => '10',
  'md_auto_time' => '3000',
  'md_gap' => '25',
  'md_gap_mo' => '15',
  'md_col' => '2',
  'md_row' => '1',
  'md_col_mo' => '2',
  'md_row_mo' => '1',
  'md_width' => '50',
  'md_height' => 'auto',
  'md_subject_is' => '1',
  'md_thumb_is' => '1',
  'md_nick_is' => '1',
  'md_date_is' => '1',
  'md_content_is' => '1',
  'md_icon_is' => '1',
  'md_comment_is' => '1',
  'md_ca_is' => '1',
  'md_swiper_is' => '1',
  'md_auto_is' => '1',
  'md_order' => '',
  'md_datetime' => '2025-06-29 05:53:22',
  'md_ip' => '112.159.68.67',
  'md_order_id' => '7',
  'md_banner_bg' => '',
  'md_border' => '',
  'md_radius' => '10',
  'md_padding' => '10',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282b',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '0',
  'md_order_latest' => 'wr_num',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => '',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:50%;" 
              data-order-id="7" 
              data-id="25" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="이벤트" 
              data-shop="0"
            >

                <ul class="content_box rb_module_25  rb_module_padding_10   " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                                            <div class="rb-module-wrap module_latest_wrap md_arrow_0" 
                           style="
                           height:auto;                                                      border-radius:10px;                                                                                                                                                                                              ">
                            <?php echo rb_latest("activity", "theme/rb.latest.thumb_top", 10, 999, 1, 25, "", "wr_num", "rb_module", "0"); ?>                        </div>
                    
                    
                    
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-25"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '4',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '시술정보',
  'md_type' => 'latest',
  'md_bo_table' => 'gallery',
  'md_sca' => '',
  'md_widget' => 'rb.widget/rb.new_roll',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_module' => '',
  'md_skin' => 'theme/rb.latest.thumb_top',
  'md_cnt' => '10',
  'md_auto_time' => '3000',
  'md_gap' => '25',
  'md_gap_mo' => '15',
  'md_col' => '2',
  'md_row' => '1',
  'md_col_mo' => '2',
  'md_row_mo' => '1',
  'md_width' => '50',
  'md_height' => 'auto',
  'md_subject_is' => '1',
  'md_thumb_is' => '1',
  'md_nick_is' => '1',
  'md_date_is' => '0',
  'md_content_is' => '1',
  'md_icon_is' => '1',
  'md_comment_is' => '1',
  'md_ca_is' => '1',
  'md_swiper_is' => '1',
  'md_auto_is' => '1',
  'md_order' => '',
  'md_datetime' => '2025-10-21 17:15:09',
  'md_ip' => '221.142.31.34',
  'md_order_id' => '8',
  'md_banner_bg' => '#FFFFFF',
  'md_border' => '',
  'md_radius' => '10',
  'md_padding' => '10',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282b',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '0',
  'md_order_latest' => 'wr_num',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => '',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '#DDDDDD',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '#25282b16',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:50%;" 
              data-order-id="8" 
              data-id="4" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="시술정보" 
              data-shop="0"
            >

                <ul class="content_box rb_module_4  rb_module_padding_10   " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                                            <div class="rb-module-wrap module_latest_wrap md_arrow_0" 
                           style="
                           height:auto;                           border-color:#DDDDDD;                            border-radius:10px;                                                       background-color:#FFFFFF;                                                                                                                                        ">
                            <?php echo rb_latest("gallery", "theme/rb.latest.thumb_top", 10, 999, 1, 4, "", "wr_num", "rb_module", "0"); ?>                        </div>
                    
                    
                    
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-4"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '18',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '주간 인기글',
  'md_type' => 'widget',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => 'rb.widget/rb.popular_w',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_module' => '',
  'md_skin' => '',
  'md_cnt' => '0',
  'md_auto_time' => '0',
  'md_gap' => '0',
  'md_gap_mo' => '0',
  'md_col' => '0',
  'md_row' => '0',
  'md_col_mo' => '0',
  'md_row_mo' => '0',
  'md_width' => '35',
  'md_height' => '304',
  'md_subject_is' => '0',
  'md_thumb_is' => '0',
  'md_nick_is' => '0',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '0',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-10-20 01:13:12',
  'md_ip' => '39.118.123.133',
  'md_order_id' => '9',
  'md_banner_bg' => '',
  'md_border' => 'solid',
  'md_radius' => '10',
  'md_padding' => '20',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282b',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '0',
  'md_order_latest' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => '',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:35%;" 
              data-order-id="9" 
              data-id="18" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="주간 인기글" 
              data-shop="0"
            >

                <ul class="content_box rb_module_18 rb_module_border_solid rb_module_border_width_1  rb_module_padding_20   " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                    
                    
                                            <div class="rb-module-wrap module_widget_wrap" 
                           style="
                           height:304px;                                                      border-radius:10px;                                                                                  ">
                            <?php @include (G5_PATH . "/rb/rb.widget/rb.popular_w/widget.php"); ?>                        </div>
                    
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-18"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '27',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '월간 인기글',
  'md_type' => 'widget',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => 'rb.widget/rb.popular_m',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_module' => '',
  'md_skin' => '',
  'md_cnt' => '0',
  'md_auto_time' => '0',
  'md_gap' => '0',
  'md_gap_mo' => '0',
  'md_col' => '0',
  'md_row' => '0',
  'md_col_mo' => '0',
  'md_row_mo' => '0',
  'md_width' => '35',
  'md_height' => 'auto',
  'md_subject_is' => '0',
  'md_thumb_is' => '0',
  'md_nick_is' => '0',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '0',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-08-22 15:48:32',
  'md_ip' => '211.181.180.18',
  'md_order_id' => '10',
  'md_banner_bg' => '',
  'md_border' => 'solid',
  'md_radius' => '10',
  'md_padding' => '20',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282b',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '0',
  'md_order_latest' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => '',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:35%;" 
              data-order-id="10" 
              data-id="27" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="월간 인기글" 
              data-shop="0"
            >

                <ul class="content_box rb_module_27 rb_module_border_solid rb_module_border_width_1  rb_module_padding_20   " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                    
                    
                                            <div class="rb-module-wrap module_widget_wrap" 
                           style="
                           height:auto;                                                      border-radius:10px;                                                                                  ">
                            <?php @include (G5_PATH . "/rb/rb.widget/rb.popular_m/widget.php"); ?>                        </div>
                    
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-27"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '6',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '포인트 랭킹',
  'md_type' => 'widget',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => 'rb.widget/user.point_profile_rank',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_module' => '',
  'md_skin' => '',
  'md_cnt' => '0',
  'md_auto_time' => '0',
  'md_gap' => '0',
  'md_gap_mo' => '0',
  'md_col' => '0',
  'md_row' => '0',
  'md_col_mo' => '0',
  'md_row_mo' => '0',
  'md_width' => '30',
  'md_height' => '304',
  'md_subject_is' => '0',
  'md_thumb_is' => '0',
  'md_nick_is' => '0',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '0',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-10-12 00:46:08',
  'md_ip' => '39.118.123.133',
  'md_order_id' => '11',
  'md_banner_bg' => '',
  'md_border' => 'solid',
  'md_radius' => '10',
  'md_padding' => '20',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '20',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282b',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '0',
  'md_order_latest' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => '',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '0',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '',
  'md_padding_lr_pc' => '',
  'md_padding_lr_mo' => '',
  'md_padding_tb_pc' => '',
  'md_padding_tb_mo' => '',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:30%;" 
              data-order-id="11" 
              data-id="6" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="포인트 랭킹" 
              data-shop="0"
            >

                <ul class="content_box rb_module_6 rb_module_border_solid rb_module_border_width_1  rb_module_padding_20   " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:20px;
                    ">
                    
                    

                    
                    
                                            <div class="rb-module-wrap module_widget_wrap" 
                           style="
                           height:304px;                                                      border-radius:10px;                                                                                  ">
                            <?php @include (G5_PATH . "/rb/rb.widget/user.point_profile_rank/widget.php"); ?>                        </div>
                    
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-6"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '58',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '끝배너_L',
  'md_type' => 'banner',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '끝배너_L',
  'md_banner_id' => '',
  'md_banner_skin' => 'rb.mod/banner/skin/rb.slide',
  'md_module' => '',
  'md_skin' => '',
  'md_cnt' => '0',
  'md_auto_time' => '0',
  'md_gap' => '0',
  'md_gap_mo' => '0',
  'md_col' => '0',
  'md_row' => '0',
  'md_col_mo' => '0',
  'md_row_mo' => '0',
  'md_width' => '50',
  'md_height' => 'auto',
  'md_subject_is' => '0',
  'md_thumb_is' => '0',
  'md_nick_is' => '0',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '0',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-10-20 01:13:45',
  'md_ip' => '39.118.123.133',
  'md_order_id' => '12',
  'md_banner_bg' => '#FFFFFF',
  'md_border' => '',
  'md_radius' => '15',
  'md_padding' => '0',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '5',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '5',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282B',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '1',
  'md_order_latest' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => 'bn_order, bn_id desc',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '1',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '#DDDDDD',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '#25282b16',
  'md_padding_lr_pc' => '0',
  'md_padding_lr_mo' => '0',
  'md_padding_tb_pc' => '0',
  'md_padding_tb_mo' => '0',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:50%;" 
              data-order-id="12" 
              data-id="58" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="끝배너_L" 
              data-shop="0"
            >

                <ul class="content_box rb_module_58    " 
                    
                    style="                    margin-top:5px; 
                      margin-bottom:5px;
                    ">
                    
                    

                    
                    
                    
                                            <div class="bbs_main_wrap_tit" style="display:none">
                            <ul class="bbs_main_wrap_tit_l">
                                <a href="javascript:void(0);">
                                    <h2 class="font-B" style="color:#25282B; font-size:20px; ">끝배너_L</h2>
                                </a>
                            </ul>
                            <ul class="bbs_main_wrap_tit_r"></ul>
                            <div class="cb"></div>
                        </div>
                        <div class="rb-module-wrap module_banner_wrap md_arrow_1" 
                           style="
                           height:auto;                           border-color:#DDDDDD;                            border-radius:15px;                                                       background-color:#FFFFFF;                            
                           ">
                            <?php echo rb_banners("끝배너_L", "", "rb.mod/banner/skin/rb.slide", "bn_order, bn_id desc"); ?>                        </div>
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-58"></div>
            </div>

            
            <?php } ?>
<?php
$row_mod = array (
  'md_id' => '59',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '끝배너_R',
  'md_type' => 'banner',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '끝배너_R',
  'md_banner_id' => '',
  'md_banner_skin' => 'rb.mod/banner/skin/rb.slide',
  'md_module' => '',
  'md_skin' => '',
  'md_cnt' => '0',
  'md_auto_time' => '0',
  'md_gap' => '0',
  'md_gap_mo' => '0',
  'md_col' => '0',
  'md_row' => '0',
  'md_col_mo' => '0',
  'md_row_mo' => '0',
  'md_width' => '50',
  'md_height' => 'auto',
  'md_subject_is' => '0',
  'md_thumb_is' => '0',
  'md_nick_is' => '0',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '0',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-10-20 01:13:37',
  'md_ip' => '39.118.123.133',
  'md_order_id' => '13',
  'md_banner_bg' => '#ffffff',
  'md_border' => '',
  'md_radius' => '15',
  'md_padding' => '0',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '5',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '5',
  'md_size' => '%',
  'md_show' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282B',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '1',
  'md_order_latest' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
  'md_order_banner' => 'bn_order, bn_id desc',
  'md_soldout_hidden' => '0',
  'md_soldout_asc' => '0',
  'md_arrow_type' => '1',
  'md_wide_is' => '0',
  'md_sec_key' => '',
  'md_sec_uid' => '',
  'md_border_width' => '1',
  'md_border_color' => '#DDDDDD',
  'md_box_shadow' => '',
  'md_box_shadow_w' => '0',
  'md_box_shadow_c' => '#25282b16',
  'md_padding_lr_pc' => '0',
  'md_padding_lr_mo' => '0',
  'md_padding_tb_pc' => '0',
  'md_padding_tb_mo' => '0',
  'md_1' => '',
  'md_2' => '',
  'md_3' => '',
  'md_4' => '',
  'md_5' => '',
  'md_6' => '',
  'md_7' => '',
  'md_8' => '',
  'md_9' => '',
  'md_10' => '',
);
?>
<?php $__rb_mb_level = isset($GLOBALS['member']['mb_level']) ? (int)$GLOBALS['member']['mb_level'] : 1;
            if (!$is_admin && !rb__level_visible($__rb_mb_level, 0, 0)) { } else { ?>
            <div 
              class="rb_layout_box " 
              style="width:50%;" 
              data-order-id="13" 
              data-id="59" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="끝배너_R" 
              data-shop="0"
            >

                <ul class="content_box rb_module_59    " 
                    
                    style="                    margin-top:5px; 
                      margin-bottom:5px;
                    ">
                    
                    

                    
                    
                    
                                            <div class="bbs_main_wrap_tit" style="display:none">
                            <ul class="bbs_main_wrap_tit_l">
                                <a href="javascript:void(0);">
                                    <h2 class="font-B" style="color:#25282B; font-size:20px; ">끝배너_R</h2>
                                </a>
                            </ul>
                            <ul class="bbs_main_wrap_tit_r"></ul>
                            <div class="cb"></div>
                        </div>
                        <div class="rb-module-wrap module_banner_wrap md_arrow_1" 
                           style="
                           height:auto;                           border-color:#DDDDDD;                            border-radius:15px;                                                       background-color:#ffffff;                            
                           ">
                            <?php echo rb_banners("끝배너_R", "", "rb.mod/banner/skin/rb.slide", "bn_order, bn_id desc"); ?>                        </div>
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-59"></div>
            </div>

            
            <?php } ?>
<?php
return ob_get_clean();
?>