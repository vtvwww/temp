<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $suffix = '';
    if($mode == 'update'){
        $id = fn_uns__upd_option($_REQUEST['option_id'], $_REQUEST['data']);
        if($id === false){
            fn_delete_notification('changes_saved');
        } else{
            fn_set_notification("N", $_REQUEST['data']['option_name'], UNS_DATA_UPDATED);
            fn_delete_notification('changes_saved');
        }
        $suffix = "update&option_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    // DELETE SELECTED USERGROUPS
    if($mode == 'delete'){
        if(is__array($_REQUEST['option_ids'])){
            fn_uns__del_option($_REQUEST['option_ids']);
        }
        $suffix = 'manage';
    }

    // ЗАПРОС СПИСКА ВАРИАНТОВ ПО ВЫБРАННОМУ ПАРАМЕТРУ
    if($mode == "get_variants"){
        if(is__more_0($_REQUEST['option_id'])){
            list($variants) = fn_uns__get_option_variants(array('option_id'       => $_REQUEST['option_id'],
                                                                'sorting_schemas' => 'select',
                                                                'all'             => true));
            $view->assign('f_type', 'select');
            $view->assign('f_options', $variants);
            $view->assign('f_option_id', 'ov_id');
            $view->assign('f_option_value', 'ov_value');
            $view->assign('f_simple_2', true);
            $options = trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
            $ajax->assign('options', $options);
        }
        exit;
    }

    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    fn_uns_add_sections($controller);
}


if($mode == 'manage'){
    $p = array();
    $p = array_merge($_REQUEST, $p);
    list($options, $search) = fn_uns__get_options($p, UNS_ITEMS_PER_PAGE);
    $view->assign('options', $options);
    $view->assign('search', $search);
}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['option_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . "." . $suffix);
    }

    $option_id = $_REQUEST['option_id'];
    $p         = array('option_id' => $option_id,);
    list($option) = fn_uns__get_options($p);
    $view->assign('option', array_shift($option));

    // Запрос вариантов ПАРАМЕТРА
    list($option_variants) = fn_uns__get_option_variants($p);
    $view->assign('option_variants', $option_variants);

    // Запрос единиц измерения
    $p = array('group_by_categories' => true);
    list($units) = fn_uns__get_units($p);
    $view->assign('units', $units);

    fn_add_breadcrumb(fn_get_lang_var('uns_options'), "uns_options.manage");
    fn_uns_navigation_tabs(array('general'  => fn_get_lang_var('general'),
                                 'variants' => fn_get_lang_var('variants'),));
}

if($mode == 'add'){
    // Запрос единиц измерения
    list($units) = fn_uns__get_units();
    $view->assign('units', $units);

    fn_add_breadcrumb(fn_get_lang_var('uns_options'), "uns_options.manage");
    fn_uns_navigation_tabs(array('general'  => fn_get_lang_var('general'),
                                 'variants' => fn_get_lang_var('variants'),));
}

if($mode == 'delete'){
    if(is__more_0($_REQUEST['option_id'])){
        fn_uns__del_option($_REQUEST['option_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}



?>