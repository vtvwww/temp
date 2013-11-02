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
        $id = fn_uns__upd_materials_category($_REQUEST['mcat_id'], $_REQUEST['data']);
        if($id === false){
            fn_delete_notification('changes_saved');
        } else{
            fn_set_notification("N", $_REQUEST['data']['mcat__name'], UNS_DATA_UPDATED);
            fn_delete_notification('changes_saved');
        }
        $suffix = "update&mcat_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'delete'){
        if(is__array($_REQUEST['mcat_ids'])){
            fn_uns__del_materials_category($_REQUEST['mcat_ids']);
        }
        $suffix = 'manage';
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    fn_uns_add_sections($controller);
}


if($mode == 'manage'){
    $p = array('with_q_ty'=>true);
    $p = array_merge($_REQUEST, $p);
    list($mcategories, $search) = fn_uns__get_materials_categories($p, UNS_ITEMS_PER_PAGE);
    $view->assign('mcategories', $mcategories);
    $view->assign('search', $search);
    $view->assign('expand_all', true);
}


if($mode == 'update'){
    if(!is__more_0($_REQUEST['mcat_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $mcat_id = $_REQUEST['mcat_id'];
    $p       = array('item_ids' => $mcat_id,);
    list($mcategory) = fn_uns__get_materials_categories($p);
    $mcategory = array_shift($mcategory);

    $view->assign('mcategory', $mcategory);

    // Список категорий для выпадающего списка
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true));
    $view->assign('mcategories_plain', $mcategories_plain);

    fn_add_breadcrumb(fn_get_lang_var('uns_material_categories'), "uns_material_categories.manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'add'){
    // Если необходимо создать подкатегорию относительно выбранной
    if(isset($_REQUEST['add_child']) and $_REQUEST['add_child'] == "Y"){
        $mcategory['mcat_parent_id'] = $_REQUEST['mcat_id'];
        $mcategory['mcat_id_path'] .= "/" . $_REQUEST['mcat_id'];
        $view->assign('mcategory', $mcategory);
    }

    // Список категорий для выпадающего списка
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true));
    $view->assign('mcategories_plain', $mcategories_plain);

    fn_add_breadcrumb(fn_get_lang_var('uns_material_categories'), "uns_material_categories.manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['mcat_id'])){
        fn_uns__del_materials_category($_REQUEST['mcat_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

