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
    $_REQUEST['o_id']           = 6;
    $_REQUEST['add_item_info']  = true;
    $_REQUEST['mclass_id']      = 3;
    $view->assign('search',     $_REQUEST);

    // Получить существующие подкатегории выбранной категории
    if (is__more_0($_REQUEST["mcat_id"]) and is__array($subcats = array_keys(array_shift(fn_uns__get_materials_categories(array('mcat_id' => $_REQUEST["mcat_id"],)))))){
        $_REQUEST["mcat_id"] = $subcats;
    }

    $balances = array();
    list($balances, $search) = fn_uns__get_balance_store($_REQUEST);
    $view->assign('balances',    $balances);

    // Запрос категорий
    $p = array( 'plain'         => true,
                'mcat_id'       => UNS_MATERIAL_CATEGORY__GI,
    );
    list($mcategories_plain) = fn_uns__get_materials_categories($p);

    $view->assign('mcategories_plain', $mcategories_plain);
    $view->assign('mcategories_plain_with_q_ty', false);

    // OBJECTS *****************************************************************
    list($objects_plain, $search) = fn_uns__get_objects(array('plain' => true, 'all'   => true));
    $view->assign('objects_plain', $objects_plain);
    $view->assign('enabled_objects', array(9,10,14,17));
}


// ПРОСМОТР ИСТОРИИ ДВИЖЕНИЯ
if ($mode == 'motion' /*and  defined('AJAX_REQUEST')*/){
    if (!is__more_0($_REQUEST['item_id'])){
        fn_set_notification('W', 'Ошибка запроса данных!', 'Обратитесь к администратору системы');
        return false;
    }
    $p = array(
        "o_id"          => $_REQUEST["o_id"],  // Склад литья
        "item_type"     => $_REQUEST["item_type"],
        "typesize"      => "M",
        "item_id"       => $_REQUEST['item_id'],
    );

    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);

    $p = array_merge($_REQUEST, $p);

    // ДВИЖЕНИЯ
    $motions = fn_uns__get_motions($p);
//    fn_print_r($motions);
    $view->assign('motions', $motions);

    // ТИПЫ ДОКУМЕНТОВ
    list($document_types) = fn_uns__get_document_types();
    $view->assign('document_types', $document_types);

    $view->assign('params', $p);
}


function fn_uns_balance_stores__search($controller) {
    $params = array(
        'period',
        'time_from',
        'time_to',
        'mcat_id',
        'o_id',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}

