<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $suffix = '';
    if($mode == 'update'){
        $id = fn_uns__upd_detail($_REQUEST['detail_id'], $_REQUEST['data']);
        if($id === false){
            fn_delete_notification('changes_saved');
        } else{
            fn_set_notification("N", $_REQUEST['data']['detail_name'], UNS_DATA_UPDATED);
            fn_delete_notification('changes_saved');

            $_REQUEST['detail_id'] = $id;
            fn_uns_mark_item($controller, $mode);
        }
        $suffix = "update&detail_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == "delete"){
        if(is__array($_REQUEST['detail_ids'])){
            fn_uns__del_details($_REQUEST['detail_ids']);
        }
        $suffix = 'manage';
    }
    // utodo вставить функции AJAX
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    fn_uns_add_sections($controller);
}

if($mode == 'manage'){
    $p = array( 'dcat_path'             => true,
                'with_accounting'       => true,
                'with_materials'        => true,
                'with_options_as_string'=> true,
                'format_name'           => true,
    );
    $p = array_merge($_REQUEST, $p);
    list($details, $search) = fn_uns__get_details($p, UNS_ITEMS_PER_PAGE);
    $view->assign('details', $details);
    $view->assign('search', $search);

    // Запрос категорий деталей
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('dcategories_plain', $dcategories_plain);
}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['detail_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $detail_id = $_REQUEST['detail_id'];

    $p = array("detail_id" => $detail_id,);
    list($detail) = fn_uns__get_details($p);
    $view->assign('detail', array_shift($detail));

    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('dcategories_plain', $dcategories_plain);

    // Запрос необходимых данных для формы характеристик/параметров
    fn_uns_generate_data_for_assign_features('D', $detail_id, $view);
    fn_uns_generate_data_for_assign_options('D', $detail_id, $view);
    fn_uns_generate_data_for_accounting('D', $detail_id, $view);
    fn_uns_generate_data_for_materials($detail_id, $view);

    fn_add_breadcrumb(fn_get_lang_var($controller), "$controller.manage");
    fn_uns_navigation_tabs(array('general'    => fn_get_lang_var('general'),
                                 'accounting' => fn_get_lang_var('uns_accounting'),
                                 'features'   => fn_get_lang_var('features'),
                                 'options'    => fn_get_lang_var('options'),));
}

if($mode == 'add'){
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('dcategories_plain', $dcategories_plain);

    // Запрос необходимых данных для формы характеристик/параметров
    fn_uns_generate_data_for_assign_features('D', 0, $view);
    fn_uns_generate_data_for_assign_options('D', 0, $view);
    fn_uns_generate_data_for_accounting('D', 0, $view);
    fn_uns_generate_data_for_materials(0, $view);

    fn_add_breadcrumb(fn_get_lang_var($controller), "$controller.manage");
    fn_uns_navigation_tabs(array('general'    => fn_get_lang_var('general'),
                                 'accounting' => fn_get_lang_var('uns_accounting'),
                                 'features'   => fn_get_lang_var('features'),
                                 'options'    => fn_get_lang_var('options'),));
}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['detail_id'])){
        fn_uns__del_details($_REQUEST['detail_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


function fn_uns_details__search($controller) {
    if(isset($_REQUEST['detail_name'])){
        $_REQUEST['detail_name'] = trim__data($_REQUEST['detail_name']);
    }
    if(isset($_REQUEST['detail_no'])){
        $_REQUEST['detail_no'] = trim__data($_REQUEST['detail_no']);
    }

    if(isset($_REQUEST['dcat_id']) or isset($_REQUEST['detail_name']) or isset($_REQUEST['detail_no'])){
        if(isset($_REQUEST['dcat_id'])){
            $_SESSION['current_search'][$controller]['dcat_id'] = $_REQUEST['dcat_id'];
        }
        if(isset($_REQUEST['detail_name'])){
            $_SESSION['current_search'][$controller]['detail_name'] = $_REQUEST['detail_name'];
        }
        if(isset($_REQUEST['detail_no'])){
            $_SESSION['current_search'][$controller]['detail_no'] = $_REQUEST['detail_no'];
        }
    }

    if(isset($_SESSION['current_search'][$controller]['dcat_id'])){
        $_REQUEST['dcat_id'] = $_SESSION['current_search'][$controller]['dcat_id'];
    }

    if(isset($_SESSION['current_search'][$controller]['detail_name'])){
        $_REQUEST['detail_name'] = $_SESSION['current_search'][$controller]['detail_name'];
    }

    if(isset($_SESSION['current_search'][$controller]['detail_no'])){
        $_REQUEST['detail_no'] = $_SESSION['current_search'][$controller]['detail_no'];
    }

    return true;
}


?>