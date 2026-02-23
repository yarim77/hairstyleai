<?php
    ob_start();

    $rb_module_table = 'rb_module_shop';
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
  'md_id' => '2',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '메인 배너',
  'md_type' => 'banner',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '쇼핑몰 메인',
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
  'md_datetime' => '2025-02-11 15:41:52',
  'md_ip' => '1.231.27.150',
  'md_order_id' => '1',
  'md_banner_id' => '',
  'md_banner_skin' => 'rb.mod/banner/skin/rb.slide',
  'md_banner_bg' => '#e9edee',
  'md_border' => '',
  'md_radius' => '0',
  'md_padding' => '0',
  'md_title_color' => '',
  'md_title_size' => '20',
  'md_title_font' => '',
  'md_title_hide' => '0',
  'md_size' => '',
  'md_show' => '',
  'md_order_latest' => '',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_order_banner' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
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
              style="width:100%; height:auto;" 
              data-order-id="1" 
              data-id="2" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="메인 배너"
              data-shop="1"
            >
            
                <ul class="content_box rb_module_shop_2    " 
                
                style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
        

                
                
                
                                   
                    <div class="bbs_main_wrap_tit" style="display:block">
                        <ul class="bbs_main_wrap_tit_l">
                            <!-- 타이틀 { -->
                            <a href="javascript:void(0);">
                                <h2 class="" style="color:; font-size:20px; ">메인 배너</h2>
                            </a>
                            <!-- } -->
                        </ul>

                        <ul class="bbs_main_wrap_tit_r"></ul>

                        <div class="cb"></div>
                    </div>
                    
                    <div class="rb-module-wrap module_banner_wrap md_arrow_0" 
                       style="
                           height:auto;                                                                                                            background-color:#e9edee;                                                                                                                                        ">
                        <?php echo rb_banners("쇼핑몰 메인", "", "rb.mod/banner/skin/rb.slide", ""); ?>                    </div>
                
                

                                
                
                                
                
                
                
                                        
                    
            </ul>
            <div class="flex_box_inner flex_box" data-layout="1-2"></div>
            
        </div>
        
        
        <?php } ?>
<?php
$row_mod = array (
  'md_id' => '4',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '오늘의딜',
  'md_type' => 'item',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_module' => '5',
  'md_skin' => 'main.40.skin.php',
  'md_cnt' => '4',
  'md_auto_time' => '3000',
  'md_gap' => '20',
  'md_gap_mo' => '10',
  'md_col' => '2',
  'md_row' => '2',
  'md_col_mo' => '2',
  'md_row_mo' => '2',
  'md_width' => '100',
  'md_height' => 'auto',
  'md_subject_is' => '1',
  'md_thumb_is' => '1',
  'md_nick_is' => '0',
  'md_date_is' => '1',
  'md_content_is' => '1',
  'md_icon_is' => '1',
  'md_comment_is' => '1',
  'md_ca_is' => '1',
  'md_swiper_is' => '1',
  'md_auto_is' => '1',
  'md_order' => 'it_id desc',
  'md_datetime' => '2025-02-14 08:47:58',
  'md_ip' => '125.131.88.3',
  'md_order_id' => '2',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_banner_bg' => '',
  'md_border' => '',
  'md_radius' => '0',
  'md_padding' => '0',
  'md_title_color' => '',
  'md_title_size' => '20',
  'md_title_font' => '',
  'md_title_hide' => '0',
  'md_size' => '',
  'md_show' => '',
  'md_order_latest' => '',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_order_banner' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
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
              style="width:100%; height:auto;" 
              data-order-id="2" 
              data-id="4" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="오늘의딜"
              data-shop="1"
            >
            
                <ul class="content_box rb_module_shop_4    " 
                
                style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
        

                
                
                
                
                

                                                        <div class="rb-module-wrap module_item_wrap md_arrow_0" 
                        style="
                           height:auto;                                                                                                                                                                                                                                                   ">
                    
<?php
if(isset($row_mod['md_soldout_hidden']) && $row_mod['md_soldout_hidden'] == 1) {
$item_where = " where it_use = '1' and it_stock_qty > 0 and it_soldout = 0";
} else { 
$item_where = " where it_use = '1'";
}
if(isset($row_mod['md_module']) && $row_mod['md_module'] > 0) {
    $item_where .= " and it_type".$row_mod['md_module']." = '1' ";
}
if(isset($row_mod['md_sca']) && $row_mod['md_sca']) {
$item_where .= " AND (ca_id = '".$row_mod['md_sca']."' OR ca_id LIKE '".$row_mod['md_sca']."%') ";
}
if(isset($row_mod['md_order']) && $row_mod['md_order']) {
if(isset($row_mod['md_soldout_asc']) && $row_mod['md_soldout_asc'] == 1) {
    $item_order = " order by it_soldout asc, " . $row_mod['md_order'];
} else { 
    $item_order = " order by " . $row_mod['md_order'];
}
} else { 
if(isset($row_mod['md_soldout_asc']) && $row_mod['md_soldout_asc'] == 1) {
    $item_order = " order by it_soldout asc, it_id desc";
} else { 
    $item_order = " order by it_id desc";
}
}
$item_limit = " limit " . $row_mod['md_cnt'];
$item_sql = " select * from {$g5['g5_shop_item_table']} " . $item_where . " " . $item_order . " " . $item_limit;
$list = new item_list();
$list->set_img_size(300, 300);
$list->set_list_skin(G5_SHOP_SKIN_PATH.'/'.$row_mod['md_skin']);
$list->set_view('it_cust_price', true);
$list->set_view('it_price', true);
$list->set_view('sns', true);
$list->set_view('md_table', $rb_module_table);
$list->set_view('md_id', $row_mod['md_id']);
$list->set_query($item_sql);
echo $list->run();
?>
                    </div>
                                
                
                                
                
                
                
                                        
                    
            </ul>
            <div class="flex_box_inner flex_box" data-layout="1-4"></div>
            
        </div>
        
        
        <?php } ?>
<?php
$row_mod = array (
  'md_id' => '3',
  'md_layout' => '1',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '히트상품',
  'md_type' => 'item',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '',
  'md_module' => '1',
  'md_skin' => 'main.10.skin.php',
  'md_cnt' => '10',
  'md_auto_time' => '0',
  'md_gap' => '20',
  'md_gap_mo' => '20',
  'md_col' => '4',
  'md_row' => '1',
  'md_col_mo' => '2',
  'md_row_mo' => '1',
  'md_width' => '100',
  'md_height' => 'auto',
  'md_subject_is' => '1',
  'md_thumb_is' => '1',
  'md_nick_is' => '0',
  'md_date_is' => '1',
  'md_content_is' => '0',
  'md_icon_is' => '1',
  'md_comment_is' => '1',
  'md_ca_is' => '1',
  'md_swiper_is' => '1',
  'md_auto_is' => '1',
  'md_order' => 'it_id desc',
  'md_datetime' => '2025-02-03 14:35:23',
  'md_ip' => '125.131.88.3',
  'md_order_id' => '4',
  'md_banner_id' => '',
  'md_banner_skin' => '',
  'md_banner_bg' => '',
  'md_border' => '',
  'md_radius' => '0',
  'md_padding' => '0',
  'md_title_color' => '',
  'md_title_size' => '20',
  'md_title_font' => '',
  'md_title_hide' => '0',
  'md_size' => '',
  'md_show' => '',
  'md_order_latest' => '',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_notice' => '0',
  'md_level' => '',
  'md_level_is' => '',
  'md_order_banner' => '',
  'md_item_tab_list' => '',
  'md_item_tab_skin' => '',
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
              style="width:100%; height:auto;" 
              data-order-id="4" 
              data-id="3" 
              data-layout="1" 
              data-sec-key=""
              data-sec-uid=""
              data-title="히트상품"
              data-shop="1"
            >
            
                <ul class="content_box rb_module_shop_3    " 
                
                style="                    margin-top:0px; 
                      margin-bottom:0px;
                    ">
        

                
                
                
                
                

                                                        <div class="rb-module-wrap module_item_wrap md_arrow_0" 
                        style="
                           height:auto;                                                                                                                                                                                                                                                   ">
                    
<?php
if(isset($row_mod['md_soldout_hidden']) && $row_mod['md_soldout_hidden'] == 1) {
$item_where = " where it_use = '1' and it_stock_qty > 0 and it_soldout = 0";
} else { 
$item_where = " where it_use = '1'";
}
if(isset($row_mod['md_module']) && $row_mod['md_module'] > 0) {
    $item_where .= " and it_type".$row_mod['md_module']." = '1' ";
}
if(isset($row_mod['md_sca']) && $row_mod['md_sca']) {
$item_where .= " AND (ca_id = '".$row_mod['md_sca']."' OR ca_id LIKE '".$row_mod['md_sca']."%') ";
}
if(isset($row_mod['md_order']) && $row_mod['md_order']) {
if(isset($row_mod['md_soldout_asc']) && $row_mod['md_soldout_asc'] == 1) {
    $item_order = " order by it_soldout asc, " . $row_mod['md_order'];
} else { 
    $item_order = " order by " . $row_mod['md_order'];
}
} else { 
if(isset($row_mod['md_soldout_asc']) && $row_mod['md_soldout_asc'] == 1) {
    $item_order = " order by it_soldout asc, it_id desc";
} else { 
    $item_order = " order by it_id desc";
}
}
$item_limit = " limit " . $row_mod['md_cnt'];
$item_sql = " select * from {$g5['g5_shop_item_table']} " . $item_where . " " . $item_order . " " . $item_limit;
$list = new item_list();
$list->set_img_size(300, 300);
$list->set_list_skin(G5_SHOP_SKIN_PATH.'/'.$row_mod['md_skin']);
$list->set_view('it_cust_price', true);
$list->set_view('it_price', true);
$list->set_view('sns', true);
$list->set_view('md_table', $rb_module_table);
$list->set_view('md_id', $row_mod['md_id']);
$list->set_query($item_sql);
echo $list->run();
?>
                    </div>
                                
                
                                
                
                
                
                                        
                    
            </ul>
            <div class="flex_box_inner flex_box" data-layout="1-3"></div>
            
        </div>
        
        
        <?php } ?>
<?php
return ob_get_clean();
?>