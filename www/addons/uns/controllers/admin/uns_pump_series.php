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
        $id = fn_uns__upd_pump_series($_REQUEST['ps_id'], $_REQUEST['data']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['ps_name'], UNS_DATA_UPDATED);
            fn_delete_notification('changes_saved');
        }
        $suffix = "update&ps_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'delete'){
        if(is__array($_REQUEST['ps_ids'])){
            fn_uns__del_pump_series($_REQUEST['ps_ids']);
        }
        $suffix = 'manage';
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    fn_uns_add_sections($controller);
}

if($mode == 'manage'){
    $p = array('pump_q_ty' => true);
    $p = array_merge($_REQUEST, $p);
    list($pump_series, $search) = fn_uns__get_pump_series($p, UNS_ITEMS_PER_PAGE);
    $view->assign('pump_series', $pump_series);
    $view->assign('search', $search);

    // Список типов насосов
    list($pump_types) = fn_uns__get_pump_types();
    $view->assign('pump_types', $pump_types);
}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['ps_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    // Информация о выбранной серии
    $ps_id = $_REQUEST['ps_id'];

    $pump_series_one = array_shift(array_shift(fn_uns__get_pump_series(array("ps_id" => $ps_id))));
    $view->assign('pump_series_one', $pump_series_one);

    // Список типов насосов
    list($pump_types) = fn_uns__get_pump_types();
    $view->assign('pump_types', $pump_types);

    fn_uns_generate_data_for_packing($ps_id, UNS_PACKING_TYPE__SERIES, $view);

    fn_add_breadcrumb(fn_get_lang_var($controller), "$controller.manage");
    fn_uns_navigation_tabs(array('general'      => fn_get_lang_var('general'),
                                 'packing_list' => fn_get_lang_var('uns_packing_list'),));
}

if($mode == 'add'){
    // Список типов насосов
    list($pump_types) = fn_uns__get_pump_types();
    $view->assign('pump_types', $pump_types);

    fn_uns_generate_data_for_packing(0, UNS_PACKING_TYPE__SERIES, $view);

    fn_add_breadcrumb(fn_get_lang_var($controller), "$controller.manage");
    fn_uns_navigation_tabs(array('general'      => fn_get_lang_var('general'),
                                 'packing_list' => fn_get_lang_var('uns_packing_list'),));
}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['ps_id'])){
        fn_uns__del_pump_series($_REQUEST['ps_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

function fn_uns_pump_series__search($controller) {
    if(isset($_REQUEST['ps_name'])){
        $_REQUEST['ps_name'] = trim__data($_REQUEST['ps_name']);
    }

    if(isset($_REQUEST['pt_id']) or isset($_REQUEST['ps_name'])){
        if(isset($_REQUEST['pt_id'])){
            $_SESSION['current_search'][$controller]['pt_id'] = $_REQUEST['pt_id'];
        }
        if(isset($_REQUEST['ps_name'])){
            $_SESSION['current_search'][$controller]['ps_name'] = $_REQUEST['ps_name'];
        }
    }

    if(isset($_SESSION['current_search'][$controller]['pt_id'])){
        $_REQUEST['pt_id'] = $_SESSION['current_search'][$controller]['pt_id'];
    }

    if(isset($_SESSION['current_search'][$controller]['ps_name'])){
        $_REQUEST['ps_name'] = $_SESSION['current_search'][$controller]['ps_name'];
    }

    return true;
}


?>