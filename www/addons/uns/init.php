<?php
if ( !defined('AREA') ) { die('Access denied'); }

define('uns', 'uns');



// include configs
require_once(DIR_ADDONS . 'uns/config.php');

// include functions
foreach (fn_get_dir_contents(DIR_ADDONS . 'uns/core', false, true, 'php') as $fns){
    require_once(DIR_ADDONS . 'uns/core/' . $fns);
}

fn_register_hooks(
    "before_dispatch"
);

?>