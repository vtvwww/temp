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


//
// $Id: schema_professional.php 10823 2010-10-07 08:42:26Z 2tl $
//

if ( !defined('AREA') ) { die('Access denied'); }


$prefix = 'last';
$description = 'status';

if (isset($_SESSION[$prefix . '_' . $description])) {
	$data = $_SESSION[$prefix . '_' . $description];
} else {
	$data = '';
}

		
	unset($_SESSION['auth_timestamp']);



?>