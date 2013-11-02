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
        $id = fn_uns__upd_details_category($_REQUEST['dcat_id'], $_REQUEST['data']);
        if($id === false){
            fn_delete_notification('changes_saved');
        } else{
            fn_set_notification("N", $_REQUEST['data']['dcat__name'], UNS_DATA_UPDATED);
            fn_delete_notification('changes_saved');
        }
        $suffix = "update&dcat_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'delete'){
        if(is__array($_REQUEST['dcat_ids'])){
            fn_uns__del_details_category($_REQUEST['dcat_ids']);
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
    list($dcategories, $search) = fn_uns__get_details_categories($p, UNS_ITEMS_PER_PAGE);
    $view->assign('dcategories', $dcategories);
    $view->assign('search', $search);

    $view->assign('expand_all', true);
}


if($mode == 'update'){
    if(!is__more_0($_REQUEST['dcat_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $dcat_id = $_REQUEST['dcat_id'];
    $p       = array('item_ids' => $dcat_id,);
    list($dcategory) = fn_uns__get_details_categories($p);
    $view->assign('dcategory', array_shift($dcategory));

    // Список категорий для выпадающего списка
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('dcategories_plain', $dcategories_plain);

    fn_add_breadcrumb(fn_get_lang_var('uns_detail_categories'), "uns_detail_categories.manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'add'){
    // Если необходимо создать подкатегорию относительно выбранной
    if(isset($_REQUEST['add_child']) and $_REQUEST['add_child'] == "Y"){
        $dcategory['dcat_parent_id'] = $_REQUEST['dcat_id'];
        $dcategory['dcat_id_path'] .= "/" . $_REQUEST['dcat_id'];
        $view->assign('dcategory', $dcategory);
    }

    // Список категорий для выпадающего списка
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('dcategories_plain', $dcategories_plain);

    fn_add_breadcrumb(fn_get_lang_var('uns_detail_categories'), "uns_detail_categories.manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['dcat_id'])){
        fn_uns__del_details_category($_REQUEST['dcat_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

