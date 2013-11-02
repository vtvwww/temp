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
    $suffix = 'manage';
    if($mode == 'update'){
        $id = fn_uns__upd_pump($_REQUEST['p_id'], $_REQUEST['data']);
        if($id === false){
            fn_delete_notification('changes_saved');
        } else{
            fn_set_notification("N", $_REQUEST['data']['ps_name'], UNS_DATA_UPDATED);
            fn_delete_notification('changes_saved');
        }
        $suffix = "update&p_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'delete'){
        if(is__array($_REQUEST['p_ids'])){
            fn_uns__del_pump($_REQUEST['p_ids']);
        }
        $suffix = 'manage';
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    fn_uns_add_sections($controller);
}

if($mode == 'manage'){
    $p = array('number_of_parts' => true);
    $p = array_merge($_REQUEST, $p);
    list($pumps, $search) = fn_uns__get_pumps($p, UNS_ITEMS_PER_PAGE);
    $view->assign('pumps', $pumps);
    $view->assign('search', $search);

    // Список типов насосов
    list($pump_types) = fn_uns__get_pump_types();
    $view->assign('pump_types', $pump_types);
    // Список серий насосов
    list($pump_series) = fn_uns__get_pump_series(array("group_by_types"=>true));
    $view->assign('pump_series', $pump_series);

}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['p_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $p_id = $_REQUEST['p_id'];

    $p = array("p_id" => $p_id);
    list($pump) = fn_uns__get_pumps($p);
    $pump = $pump[$p_id];
    $view->assign('pump', $pump);


    // Список серий насосов
    list($pump_series) = fn_uns__get_pump_series(array("group_by_types"=>true));
    $view->assign('pump_series', $pump_series);

    fn_uns_generate_data_for_assign_features('P', $p_id, $view);
    fn_uns_generate_data_for_assign_options('P', $p_id, $view);
    fn_uns_generate_data_for_accounting('P', $p_id, $view);

    fn_uns_generate_data_for_packing($p_id, UNS_PACKING_TYPE__ITEM, $view);

    fn_add_breadcrumb(fn_get_lang_var('uns_pumps'), "uns_pumps.manage");
    fn_uns_navigation_tabs(array('general'      => fn_get_lang_var('general'),
                                 'packing_list' => fn_get_lang_var('uns_packing_list'),
                                 'accounting'   => fn_get_lang_var('uns_accounting'),
                                 'features'     => fn_get_lang_var('features'),
                                 'options'      => fn_get_lang_var('options'),));
}

if($mode == 'add'){
    // Список серий насосов
    list($pump_series) = fn_uns__get_pump_series(array("group_by_types"=>true));
    $view->assign('pump_series', $pump_series);

    fn_add_breadcrumb(fn_get_lang_var('uns_pumps'), "uns_pumps.manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['p_id'])){
        fn_uns__del_pump($_REQUEST['p_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

if($mode == 'packing_list_view'){

    if(is__more_0($_REQUEST['p_id'])){
        $p_id = $_REQUEST['p_id'];

        $p = array("p_id" => $p_id, "with_packing_list" => true);
        list($pump) = fn_uns__get_pumps($p);
        $pump = $pump[$p_id];

        $units = fn_uns__get_units();

        $packing_list_data = fn_uns_generate_data_for_packing($p_id, UNS_PACKING_TYPE__ITEM, $view);
        $features          = fn_uns_generate_data_for_assign_features('P', $p_id, $view);
        $options           = fn_uns_generate_data_for_assign_options('P', $p_id, $view);
        $accounting        = fn_uns_generate_data_for_accounting('P', $p_id, $view);

        $view->assign('pump', $pump);
        $view->assign('units', $units);
        $view->assign('features', $features);
        $view->assign('options', $options);
        $view->assign('accounting', $accounting);

        $view->assign('packing_list_data', $packing_list_data);
        $view->display('addons/uns/views/uns_pumps/components/packing_list_view.tpl');
    }
    exit();
}


function fn_uns_pumps__search($controller) {

    if(isset($_REQUEST['p_name'])){
        $_REQUEST['p_name'] = trim__data($_REQUEST['p_name']);
    }

    if(isset($_REQUEST['ps_id']) or isset($_REQUEST['pt_id']) or isset($_REQUEST['p_name'])){
        if(isset($_REQUEST['pt_id'])){
            $_SESSION['current_search'][$controller]['pt_id'] = $_REQUEST['pt_id'];
        }
        if(isset($_REQUEST['p_name'])){
            $_SESSION['current_search'][$controller]['p_name'] = $_REQUEST['p_name'];
        }
    }

    if(isset($_SESSION['current_search'][$controller]['ps_id'])){
        $_REQUEST['ps_id'] = $_SESSION['current_search'][$controller]['ps_id'];
    }

    if(isset($_SESSION['current_search'][$controller]['pt_id'])){
        $_REQUEST['pt_id'] = $_SESSION['current_search'][$controller]['pt_id'];
    }

    if(isset($_SESSION['current_search'][$controller]['p_name'])){
        $_REQUEST['p_name'] = $_SESSION['current_search'][$controller]['p_name'];
    }

    return true;
}


?>