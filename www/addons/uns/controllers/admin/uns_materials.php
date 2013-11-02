<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $suffix = '';
    if($mode == "update"){
        $id = fn_uns__upd_material($_REQUEST['material_id'], $_REQUEST['data']);
        if($id === false){
            fn_delete_notification('changes_saved');
        } else{
            fn_set_notification("N", $_REQUEST['data']['material_name'], UNS_DATA_UPDATED);
            fn_delete_notification('changes_saved');
        }
        $suffix = "update&material_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == "delete"){
        if(is__array($_REQUEST['material_ids'])){
            fn_uns__del_materials($_REQUEST['material_ids']);
        }
        $suffix = 'manage';
    }

    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    fn_uns_add_sections($controller);
}


if($mode == 'manage'){
    $p = array('mcat_path' => true, 'with_accounting' => true);
    $p = array_merge($_REQUEST, $p);
    list($materials, $search) = fn_uns__get_materials($p, UNS_ITEMS_PER_PAGE);
    $view->assign('materials', $materials);
    $view->assign('search', $search);

    // Запрос категорий материалов
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true, 'with_q_ty'=>true));
    $view->assign('mcategories_plain', $mcategories_plain);

    // Запрос классов материалов
    list($mclasses) = fn_uns__get_materials_classes();
    $view->assign('mclasses', $mclasses);
}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['material_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $material_id = $_REQUEST['material_id'];

    $p = array("material_id" => $material_id,);
    list($material) = fn_uns__get_materials($p);
    $view->assign('material', array_shift($material));

    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true, 'with_q_ty'=>true));
    $view->assign('mcategories_plain', $mcategories_plain);

    list($mclasses) = fn_uns__get_materials_classes();
    $view->assign('mclasses', $mclasses);

    // Запрос необходимых данных для формы характеристик/параметров
    fn_uns_generate_data_for_assign_features('M', $material_id, $view);
    fn_uns_generate_data_for_assign_options('M', $material_id, $view);
    fn_uns_generate_data_for_accounting('M', $material_id, $view);

    fn_add_breadcrumb(fn_get_lang_var('uns_materials'), "uns_materials.manage#".$material_id);
    fn_uns_navigation_tabs(array('general'    => fn_get_lang_var('general'),
                                 'accounting' => fn_get_lang_var('uns_accounting'),
                                 'features'   => fn_get_lang_var('features'),
                                 'options'    => fn_get_lang_var('options'),));
}

if($mode == 'add'){
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true));
    $view->assign('mcategories_plain', $mcategories_plain);

    list($mclasses) = fn_uns__get_materials_classes();
    $view->assign('mclasses', $mclasses);

    // Запрос необходимых данных для формы характеристик/параметров
    fn_uns_generate_data_for_assign_features('M', 0, $view);
    fn_uns_generate_data_for_assign_options('M', 0, $view);
    fn_uns_generate_data_for_accounting('M', 0, $view);

    fn_add_breadcrumb(fn_get_lang_var('uns_materials'), "uns_materials.manage");
    fn_uns_navigation_tabs(array('general'    => fn_get_lang_var('general'),
                                 'accounting' => fn_get_lang_var('uns_accounting'),
                                 'features'   => fn_get_lang_var('features'),
                                 'options'    => fn_get_lang_var('options'),));
}

if($mode == 'delete'){
    if(is__more_0($_REQUEST['material_id'])){
        fn_uns__del_materials($_REQUEST['material_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

function fn_uns_materials__search ($controller){
//    if (isset($_REQUEST['material_name'])){
//        $_REQUEST['material_name'] = trim__data($_REQUEST['material_name']);
//    }
//
//    if (isset($_REQUEST['material_no'])){
//        $_REQUEST['material_no'] = trim__data($_REQUEST['material_no']);
//    }
//
//    if (isset($_REQUEST['mcat_id']) or isset($_REQUEST['mclass_id']) or isset($_REQUEST['material_name']) or isset($_REQUEST['material_no'])){
//        if (isset($_REQUEST['mcat_id'])){
//            $_SESSION['current_search'][$controller]['mcat_id'] = $_REQUEST['mcat_id'];
//        }
//        if (isset($_REQUEST['mclass_id'])){
//            $_SESSION['current_search'][$controller]['mclass_id'] = $_REQUEST['mclass_id'];
//        }
//        if (isset($_REQUEST['material_name'])){
//            $_SESSION['current_search'][$controller]['material_name'] = $_REQUEST['material_name'];
//        }
//        if (isset($_REQUEST['material_no'])){
//            $_SESSION['current_search'][$controller]['material_no'] = $_REQUEST['material_no'];
//        }
//    }
//
//    if (isset($_SESSION['current_search'][$controller]['mcat_id'])){
//        $_REQUEST['mcat_id'] = $_SESSION['current_search'][$controller]['mcat_id'];
//    }
//
//    if (isset($_SESSION['current_search'][$controller]['mclass_id'])){
//        $_REQUEST['mclass_id'] = $_SESSION['current_search'][$controller]['mclass_id'];
//    }
//
//    if (isset($_SESSION['current_search'][$controller]['material_name'])){
//        $_REQUEST['material_name'] = $_SESSION['current_search'][$controller]['material_name'];
//    }
//
//    if (isset($_SESSION['current_search'][$controller]['material_no'])){
//        $_REQUEST['material_no'] = $_SESSION['current_search'][$controller]['material_no'];
//    }

    $params = array(
        'mclass_id',
        'mcat_id',
        'material_name',
        'material_no',
    );
    fn_uns_search_set_get_params($controller, $params);

    return true;
}


?>