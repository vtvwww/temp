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
        $id = fn_uns__upd_object($_REQUEST['o_id'], $_REQUEST['data']);
        fn_delete_notification('changes_saved');
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        $suffix = "update&o_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'delete'){
        if(is__array($_REQUEST['o_ids'])){
            fn_uns__del_object($_REQUEST['o_ids']);
        }
        $suffix = 'manage';
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}

if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
//    fn_uns_add_sections($controller);
}

if($mode == 'manage'){

    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);

    // ДОКУМЕНТЫ СОГЛАСНО УСЛОВИЯМ ВЫБОРКИ
    $p = array("with_weight_per_each_document" => true,);
    $p = array_merge($_REQUEST, $p);
    list($documents, $search) = fn_acc__get_report_VLC($p, UNS_ITEMS_PER_PAGE);
    $view->assign('documents', $documents);
    $view->assign('search', $search);

    // ИТОГОВЫЙ ВЕС ПО ВСЕМ ДОКУМЕНТАМ
    $p1 = array("with_total_weight_all_documents" => true,);
    $p1 = array_merge($_REQUEST, $p1);
    list(,$s) = fn_acc__get_report_VLC($p1);
    $view->assign('total_weight', $s['total_weight']);

    // ТИПЫ ДОКУМЕНТОВ
    list($document_types) = fn_uns__get_document_types();
    $view->assign('document_types', $document_types);
}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['o_id'])){
        fn_uns__del_object($_REQUEST['o_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

function fn_foundry_get_report__search ($controller){
    $params = array(
        'period',
        'time_from',
        'time_to',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}
