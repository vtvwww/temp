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
        $id = fn_uns__upd_pump_type($_REQUEST['pt_id'], $_REQUEST['data']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['pt_name'], UNS_DATA_UPDATED);
            fn_delete_notification('changes_saved');
        }
        $suffix = "update&pt_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'delete'){
        if(is__array($_REQUEST['pt_ids'])){
            fn_uns__del_pump_type($_REQUEST['pt_ids']);
        }
        $suffix = 'manage';
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    fn_uns_add_sections($controller);
}

if($mode == 'manage'){
    $p = array();
    $p = array_merge($_REQUEST, $p);
    list($pump_types, $search) = fn_uns__get_pump_types($p, UNS_ITEMS_PER_PAGE);
    $view->assign('pump_types', $pump_types);
    $view->assign('search', $search);
}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['pt_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $pt_id = $_REQUEST['pt_id'];
    list($pump_type) = fn_uns__get_pump_types(array("pt_id" => $pt_id));
    $view->assign('pump_type', array_shift($pump_type));

    fn_add_breadcrumb(fn_get_lang_var($controller), "$controller.manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}

if($mode == 'add'){
    // Список серий насосов
    list($pump_series) = fn_uns__get_pump_series();
    $view->assign('pump_series', $pump_series);

    fn_add_breadcrumb(fn_get_lang_var($controller), "$controller.manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['pt_id'])){
        fn_uns__del_pump_type($_REQUEST['pt_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

