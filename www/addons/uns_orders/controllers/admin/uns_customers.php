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

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $suffix = '';
    if($mode == 'update'){
        $id = fn_uns__upd_customer($_REQUEST['customer_id'], $_REQUEST['data']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&customer_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}

if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    if($mode == 'update' or $mode == 'add'){
        fn_add_breadcrumb(fn_get_lang_var($controller), $controller . ".manage");
        fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
    }
}

if($mode == 'manage'){
    // CUSTOMERS
    list($customers, $search) = fn_uns__get_customers(array_merge($_REQUEST, array()), UNS_ITEMS_PER_PAGE);
    $view->assign('customers', $customers);
    $view->assign('search',    $search);
}

if($mode == 'update'){
    if (is__more_0($_REQUEST["customer_id"])){
        $customer = array_shift(array_shift(fn_uns__get_customers(array("customer_id"=>$_REQUEST["customer_id"]))));
        $view->assign("customer", $customer);
    }
}

if($mode == 'delete'){
    if (is__more_0($_REQUEST["customer_id"])){
        fn_uns__del_customer($_REQUEST['customer_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

