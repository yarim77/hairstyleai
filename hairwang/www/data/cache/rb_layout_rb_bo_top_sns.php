<?php
ob_start();

$rb_module_table = 'rb_module';
$GLOBALS['rb_module_table'] = $rb_module_table;
$is_admin = '';
?>
<?php
return ob_get_clean();
?>