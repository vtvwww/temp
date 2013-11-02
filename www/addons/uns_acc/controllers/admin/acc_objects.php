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
        $id = fn_uns__upd_object($_REQUEST['o_id'], $_REQUEST['data']);
        fn_delete_notification('changes_saved');
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        $suffix = "update&o_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'delete'){
        if(is__array($_REQUEST['o_ids'])){
            fn_uns__del_object($_REQUEST['o_ids']);
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
    list($objects, $search) = fn_uns__get_objects($p);
    $view->assign('objects', $objects);
    $view->assign('search', $search);

    $view->assign('expand_all', true);
}


if($mode == 'update'){
    if(!is__more_0($_REQUEST['o_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $o_id = $_REQUEST['o_id'];
    $p       = array('item_ids' => $o_id);
    list($object) = fn_uns__get_objects($p);
    $view->assign('object', array_shift($object));

    // Список для выпадающего списка
    list($objects_plain) = fn_uns__get_objects(array('plain' => true));
    $view->assign('objects_plain', $objects_plain);

    fn_add_breadcrumb(fn_get_lang_var($controller), $controller . ".manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'add'){
    // Если необходимо создать подкатегорию относительно выбранной
    if(isset($_REQUEST['add_child']) and $_REQUEST['add_child'] == "Y"){
        $object['o_parent_id'] = $_REQUEST['o_id'];
        $object['o_id_path'] .= "/" . $_REQUEST['o_id'];
        $view->assign('object', $object);
    }

    // Список для выпадающего списка
    list($objects_plain) = fn_uns__get_objects(array('plain' => true));
    $view->assign('objects_plain', $objects_plain);

    fn_add_breadcrumb(fn_get_lang_var($controller), $controller . ".manage");
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['o_id'])){
        fn_uns__del_object($_REQUEST['o_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

