<?php
if(!defined('AREA')){
    die('Access denied');
}

// include configs
require_once(DIR_ADDONS . 'uns_acc/config.php');

// include functions
foreach (fn_get_dir_contents(DIR_ADDONS . 'uns_acc/core', false, true, 'php') as $fns){
    require_once(DIR_ADDONS . 'uns_acc/core/' . $fns);
}

?>