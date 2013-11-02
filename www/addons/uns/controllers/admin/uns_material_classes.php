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
        $id = fn_uns__upd_materials_classes($_REQUEST['mclass_id'], $_REQUEST['data']);
        if($id === false){
            fn_delete_notification('changes_saved');
        } else{
            fn_set_notification("N", $_REQUEST['data']['mclass_name'], UNS_DATA_UPDATED);
            fn_delete_notification('changes_saved');
        }
        $suffix = "update&mclass_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'delete'){
        if(is__array($_REQUEST['mclass_ids'])){
            fn_uns__del_materials_classes($_REQUEST['mclass_ids']);
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
    list($mclasses, $search) = fn_uns__get_materials_classes($p, UNS_ITEMS_PER_PAGE);
    $view->assign('mclasses', $mclasses);
    $view->assign('search', $search);
}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['mclass_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $mclass_id = $_REQUEST['mclass_id'];
    $p         = array("mclass_id" => $mclass_id,);
    list($mclass) = fn_uns__get_materials_classes($p);
    $view->assign('mclass', array_shift($mclass));

    fn_add_breadcrumb(fn_get_lang_var('uns_material_classes'), "uns_material_classes.manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}

if($mode == 'add'){
    fn_add_breadcrumb(fn_get_lang_var('uns_material_classes'), "uns_material_classes.manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}

if($mode == 'delete'){
    if(is__more_0($_REQUEST['mclass_id'])){
        fn_uns__del_materials_classes($_REQUEST['mclass_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}



?>