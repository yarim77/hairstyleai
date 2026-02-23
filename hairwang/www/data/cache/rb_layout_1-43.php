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
  'md_id' => '62',
  'md_layout' => '1-43',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '포토폴리오',
  'md_type' => 'latest',
  'md_bo_table' => 'card',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_module' => '',
  'md_skin' => 'theme/rb.latest.thumb_top1',
  'md_cnt' => '20',
  'md_auto_time' => '0',
  'md_gap' => '15',
  'md_gap_mo' => '15',
  'md_col' => '5',
  'md_row' => '1',
  'md_col_mo' => '2',
  'md_row_mo' => '2',
  'md_width' => '100',
  'md_height' => 'auto',
  'md_subject_is' => '1',
  'md_thumb_is' => '1',
  'md_nick_is' => '1',
  'md_date_is' => '0',
  'md_content_is' => '0',
  'md_icon_is' => '0',
  'md_comment_is' => '0',
  'md_ca_is' => '1',
  'md_swiper_is' => '0',
  'md_auto_is' => '0',
  'md_order' => '',
  'md_datetime' => '2025-10-20 01:00:01',
  'md_ip' => '39.118.123.133',
  'md_order_id' => '16',
  'md_banner_bg' => '#FFFFFF',
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
              style="width:100%;" 
              data-order-id="16" 
              data-id="62" 
              data-layout="1-43" 
              data-sec-key=""
              data-sec-uid=""
              data-title="포토폴리오" 
              data-shop="0"
            >

                <ul class="content_box rb_module_62    " 
                    
                    style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
                    
                    

                                            <div class="rb-module-wrap module_latest_wrap md_arrow_0" 
                           style="
                           height:auto;                           border-color:#DDDDDD;                                                                                  background-color:#FFFFFF;                                                                                                                                        ">
                            <?php echo rb_latest("card", "theme/rb.latest.thumb_top1", 20, 999, 1, 62, "", "wr_num", "rb_module", "0"); ?>                        </div>
                    
                    
                    
                    
                    
                                        
                                    </ul>
                
                
                <div class="flex_box_inner flex_box" data-layout="1-43-62"></div>
            </div>

            
            <?php } ?>
<?php
return ob_get_clean();
?>