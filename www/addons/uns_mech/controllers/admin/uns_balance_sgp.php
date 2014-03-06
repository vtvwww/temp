<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    return array(CONTROLLER_STATUS_OK, $controller . "." . $mode);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
//    fn_uns_add_sections($controller);
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'manage'){
    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    if (!isset($_REQUEST["total_balance_of_details"])) $_REQUEST["total_balance_of_details"] = "Y";

    $balances = array();
    list($balances, $search) = fn_uns__get_balance_sgp($_REQUEST, true, true, true, true);
    $view->assign('balances',    $balances);
//    fn_print_r($balances);

    $view->assign('search',     $_REQUEST);
    $view->assign('expand_all', false);
//    fn_print_r($search);


    // Запрос ЗАКАЗОВ
    $p = array(
        "with_items"        => true,
        "full_info"         => true,
        "with_count"        => true,
        "only_active"       => true,
        "data_for_tmp"      => true,
        "remaining_time"    => true,
    );
    list($orders, $search) = fn_acc__get_orders(array_merge($_REQUEST, $p));
//    fn_print_r($orders);
    $view->assign('orders', $orders);

    // REGIONS
    list($regions) = fn_uns__get_regions();
    $view->assign('regions', $regions);

    // Запрос категорий
    list($dcategories_plain) = fn_uns__get_details_categories(array("plain" => true, "with_q_ty"=>false));
    $view->assign('dcategories_plain', $dcategories_plain);
    $view->assign('dcategories_plain_with_q_ty', false);

    // OBJECTS *****************************************************************
    list($objects_plain, $search) = fn_uns__get_objects(array('plain' => true, 'all'   => true));
    $view->assign('objects_plain', $objects_plain);
    $view->assign('enabled_objects', array(9,10,14,17));

}


if($mode == 'update'){
    if(!is__more_0($_REQUEST['o_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $o_id = $_REQUEST['o_id'];
    $p       = array('item_ids' => $o_id);
    list($object) = fn_uns__get_objects($p);
    $view->assign('object', array_shift($object));

    // Список для выпадающего списка
    list($objects_plain) = fn_uns__get_objects(array('plain' => true));
    $view->assign('objects_plain', $objects_plain);
}



// ПРОСМОТР ИСТОРИИ ДВИЖЕНИЯ
if (defined('AJAX_REQUEST') and  $mode == 'motion'){
    if (!is__more_0($_REQUEST['item_id'])){
        fn_set_notification('W', 'Ошибка запроса данных!', 'Обратитесь к администратору системы');
        return false;
    }
    $p = array(
        "o_id"          => array(8),  // Склад литья
        "item_type"     => "M",
        "typesize"      => "M",
        "item_id"       => $_REQUEST['item_id'],
    );

    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);

    $p = array_merge($_REQUEST, $p);

    // ДВИЖЕНИЯ
    $motions = fn_uns__get_motions($p);
    $view->assign('motions', $motions);

    // ТИПЫ ДОКУМЕНТОВ
    list($document_types) = fn_uns__get_document_types();
    $view->assign('document_types', $document_types);

    $view->assign('params', $p);

}


function fn_uns_balance_sgp__search($controller) {
    $params = array(
        'period',
        'time_from',
        'time_to',

        'item_type',
        'dcat_id',
        'type_casting',
        'detail_name',
        'detail_no',
        'accessory_pumps',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}

