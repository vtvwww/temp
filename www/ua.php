<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

list(,,,$project) = explode("\\", __DIR__);
define("PROJECT_NAME", $project);

define('AREA', 'A');
define('AREA_NAME', 'admin');
define('ACCOUNT_TYPE', 'admin');

require dirname(__FILE__) . '/prepare.php';
require dirname(__FILE__) . '/init.php';

define('INDEX_SCRIPT',  Registry::get('config.admin_index'));
if (in_array($_SESSION["auth"]["user_id"], array(1,9))){
    define('PROFILER',  1);
}

fn_dispatch();

?>