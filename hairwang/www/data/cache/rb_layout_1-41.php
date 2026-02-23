<?php
ob_start();

$rb_module_table = 'rb_module';
$GLOBALS['rb_module_table'] = $rb_module_table;
$is_admin = '';
?>
<?php
$row_mod = array (
  'md_id' => '46',
  'md_layout' => '1-41',
  'md_layout_name' => 'basic',
  'md_theme' => 'rb.basic',
  'md_title' => '메인서브',
  'md_type' => 'banner',
  'md_bo_table' => '',
  'md_sca' => '',
  'md_widget' => '',
  'md_poll' => '',
  'md_poll_id' => '',
  'md_banner' => '메인서브배너2_mo',
  'md_banner_id' => '',
  'md_banner_skin' => 'rb.mod/banner/skin/rb.basic',
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
  'md_datetime' => '2025-06-24 09:23:39',
  'md_ip' => '221.142.31.21',
  'md_order_id' => '12',
  'md_banner_bg' => '',
  'md_border' => '',
  'md_radius' => '0',
  'md_padding' => '0',
  'md_padding_mo' => '',
  'md_margin_top_mo' => '20',
  'md_margin_top_pc' => '',
  'md_margin_btm_pc' => '',
  'md_margin_btm_mo' => '',
  'md_size' => '%',
  'md_show' => 'mobile',
  'md_tab_list' => '',
  'md_tab_skin' => '',
  'md_title_color' => '#25282b',
  'md_title_size' => '20',
  'md_title_font' => 'font-B',
  'md_title_hide' => '0',
  'md_order_latest' => '',
  'md_notice' => '0',
);
?>
        
        
        <div 
        class="rb_layout_box mobile" 
        style="width:100%; height:auto; 
        margin-top:20px; 
        margin-bottom:0px;" 
        data-order-id="46" 
        data-id="46" 
        data-layout="1-41" 
        data-title="메인서브"
        >
           
            <ul class="content_box rb_module_46 rb_module_border_ rb_module_radius_0 mobile">

                
                
                
                                    <div class="module_banner_wrap">
                        <?php echo rb_banners("메인서브배너2_mo", "", "rb.mod/banner/skin/rb.basic"); ?>                    </div>
                
                
                            </ul>
            
            <div class="flex_box_inner flex_box" data-layout="1-41-46"></div>
        </div>
        
        <?php
return ob_get_clean();
?>