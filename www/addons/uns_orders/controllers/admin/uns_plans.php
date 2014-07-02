<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $suffix = '';

    if($mode == 'update'){
        $id = fn_uns__upd_plan($_REQUEST['plan_id'], $_REQUEST['data']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&plan_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }


    if (defined('AJAX_REQUEST') and $mode == 'plan_items'){
        switch ($_REQUEST['event']){
            case "change__item_type": // Произошла смена ТИПА
                if(in_array($_REQUEST['item_type'], array('D', 'S'))){
                    $options = "<option value='0'>---</option>";
                    if ($_REQUEST["item_type"] == "S"){
                        $p = array(
                            'only_active' => true,
                            'group_by_types'=>true,
                        );
                        list($pump_series) = fn_uns__get_pump_series($p);
                        $view->assign("f_type", "select_by_group");
                        $view->assign("f_options", "pump_series");
                        $view->assign("f_option_id", "ps_id");
                        $view->assign("f_option_value", "ps_name");
                        $view->assign("f_optgroups", $pump_series);
                        $view->assign("f_optgroup_label", "pt_name_short");
                        $view->assign('f_simple_2', true);
                    }
                }
                $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                $ajax->assign('options', $options);
            break;
        }
        exit;
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}

//**************************************************************************
// PLANS
//**************************************************************************
if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    $months = array(1=>"Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
    $view->assign('months', $months);

    // только при редактировании
    if($mode == 'update' or $mode == 'add'){
        fn_add_breadcrumb(fn_get_lang_var($controller), $controller . ".manage");
        fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
    }
}


if($mode == 'manage'){
    $p = array(
        "with_count" => true,
    );
    $p = array_merge($_REQUEST, $p);
    list($plans, $search) = fn_uns__get_plans($p, UNS_ITEMS_PER_PAGE);
    $view->assign('plans', $plans);
    $view->assign('search', $search);

//    fn_print_r($plans);
}


if($mode == 'update'){
    if(!is__more_0($_REQUEST['plan_id']) or !is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT plan_id FROM ?:_plans WHERE plan_id = ?i", $_REQUEST['plan_id']))){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }
    $p = array(
        "with_items"=> true,
        "plan_id"   => $_REQUEST['plan_id'],
    );
    $plan = array_shift(array_shift(fn_uns__get_plans($p)));
    $view->assign('plan', $plan);
//    fn_print_r($plan);

    // Серии насосов
    list($pump_series) = fn_uns__get_pump_series(array('only_active' => true,'group_by_types'=>true,));
    $view->assign('pump_series', $pump_series);
//    fn_print_r($pump_series);
}


if($mode == 'delete'){
    if (is__more_0($_REQUEST["plan_id"])){
        fn_uns__del_plan($_REQUEST['plan_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


function fn_uns_plans__search ($controller){
    $params = array(
        'month',
        'year',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}


