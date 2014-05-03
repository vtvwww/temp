<?php
if(!defined('AREA')){
    die('Access denied');
}

$current_addon = basename(__DIR__);

define("UNS_PLANS_DIR_JS",    "skins/basic/admin/addons/$current_addon/js");
define("UNS_PLANS_DIR_CSS",   "skins/basic/admin/addons/$current_addon/css");
define("UNS_PLANS_DIR_VIEW",  "skins/basic/admin/addons/$current_addon/views");



