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
return ob_get_clean();
?>