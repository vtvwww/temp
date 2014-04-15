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
        $id = fn_acc__upd_order($_REQUEST['order_id'], $_REQUEST['data']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&order_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}

//**************************************************************************
// KIT
//**************************************************************************
if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
//    fn_uns_add_sections($controller);

    // только при редактировании
    if($mode == 'update' or $mode == 'add'){
        fn_add_breadcrumb(fn_get_lang_var($controller), $controller . ".manage");
        fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
    }
}


if($mode == 'manage'){
    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    $p = array();
    $p = array(
        "full_info" => true,
        "with_count" => true,
    );
    $p = array_merge($_REQUEST, $p);
    list($orders, $search) = fn_acc__get_orders($p, UNS_ITEMS_PER_PAGE);
    $view->assign('orders', $orders);
    $view->assign('search', $search);

    // CUSTOMERS
    list($customers) = fn_uns__get_customers();
    $view->assign('customers', $customers);
}



if($mode == 'add'){
    //PUMPS
    list($pumps) = fn_uns__get_pumps(array("group_by_series"=>true));
    $view->assign('pumps', $pumps);

    // customerS
    list($customers) = fn_uns__get_customers();
    $view->assign('customers', $customers);

    // PUMP_SERIES
    $p = array(
        'only_active' => true,
        'group_by_types'=>true,
    );
    list($pump_series) = fn_uns__get_pump_series($p);
    $view->assign('pump_series', $pump_series);

}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['order_id']) or !is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT order_id FROM ?:_acc_orders WHERE order_id = ?i", $_REQUEST['order_id']))){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }
    $p = array(
        "with_items" => true,
        "full_info" => true,
    );
    $p = array_merge($_REQUEST, $p);
    $order = array_shift(array_shift(fn_acc__get_orders($p)));
//    fn_print_r($order);
    $view->assign('order', $order);

    // CATEGORIES **************************************************************
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('dcategories_plain', $dcategories_plain);

    // customerS
    list($customers) = fn_uns__get_customers();
    $view->assign('customers', $customers);

    // PUMP_SERIES
    $p = array(
        'only_active' => true,
        'group_by_types'=>true,
    );
    list($pump_series) = fn_uns__get_pump_series($p);
    $view->assign('pump_series', $pump_series);
//    fn_print_r($pump_series);
}


if($mode == 'delete'){
    if (is__more_0($_REQUEST["order_id"])){
        fn_uns__del_order($_REQUEST['order_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


function fn_uns_orders__search ($controller){
    $params = array(
        'period',
        'time_from',
        'time_to',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}


