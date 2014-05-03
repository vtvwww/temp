<?php
if(!defined('AREA')){
    die('Access denied');
}

$current_addon = basename(__DIR__);
// include configs
require_once(DIR_ADDONS . $current_addon . "/config.php");

// include functions
foreach (fn_get_dir_contents(DIR_ADDONS . $current_addon . "/core", false, true, 'php') as $fns){
    require_once(DIR_ADDONS . $current_addon . "/core/" . $fns);
}
