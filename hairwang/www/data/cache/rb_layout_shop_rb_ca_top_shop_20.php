<?php
ob_start();

$rb_module_table = 'rb_module_shop';
$GLOBALS['rb_module_table'] = $rb_module_table;
$is_admin = '';
?>
<?php
return ob_get_clean();
?>