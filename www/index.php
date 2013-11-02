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

define('AREA', 'C');
list(,,,$project) = explode("\\", __DIR__);
define("PROJECT_NAME", $project);

require dirname(__FILE__) . '/prepare.php';
require dirname(__FILE__) . '/init.php';

define('INDEX_SCRIPT',  Registry::get('config.customer_index'));

header('Location: http://'.Registry::get('config.http_host').Registry::get('config.http_path').'/u.php');
exit;

fn_dispatch();

?>