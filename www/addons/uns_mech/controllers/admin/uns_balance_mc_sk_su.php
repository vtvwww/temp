<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    return array(CONTROLLER_STATUS_OK, $controller . "." . $mode);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    fn_uns_add_sections($controller);
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'manage'){
    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    if (!isset($_REQUEST["total_balance_of_details"])) $_REQUEST["total_balance_of_details"] = "Y";

//    fn_uns__get_balance_mc(10, $_REQUEST);
//    fn_uns__get_balance_mc(14, $_REQUEST);


    if ($_REQUEST["total_balance_of_details"] == "Y"){
        $balances = array();
        list($balances, $search) = fn_uns__get_balance_mc_sk_su($_REQUEST, true, true, true);
        $view->assign('balances',    $balances);
//        fn_print_r($balances);
    }

    $view->assign('search',     $search);
    $view->assign('expand_all', false);

    // Корректное отображение принадлежности

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
if ($mode == 'motions' /*and  defined('AJAX_REQUEST')*/){
    if (!is__more_0($_REQUEST['item_id'])){
        fn_set_notification('W', 'Ошибка запроса данных!', 'Обратитесь к администратору системы');
        return false;
    }
    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    $balances = array();
    list($balances, $search) = fn_uns__get_balance_mc_sk_su($_REQUEST, true, true, true);
    fn_print_r($balances);
    $view->assign('balances', $_REQUEST);
/*    // ДВИЖЕНИЯ
    $motions = fn_uns__get_motions($p);
    $view->assign('motions', $motions);

    // ТИПЫ ДОКУМЕНТОВ
    list($document_types) = fn_uns__get_document_types();
    $view->assign('document_types', $document_types);

    */

}


function fn_uns_balance_mc_sk_su__search($controller) {
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

