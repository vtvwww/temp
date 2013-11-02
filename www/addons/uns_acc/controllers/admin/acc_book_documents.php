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
        $id = fn_uns__upd_document($_REQUEST['document_id'], $_REQUEST['data']);
        fn_delete_notification('changes_saved');
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        $suffix = "update&document_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'delete'){
        if(is__array($_REQUEST['document_ids'])){
            fn_uns__del_document($_REQUEST['document_ids']);
        }
        $suffix = 'manage';
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    fn_uns_add_sections($controller);

    // только при редактировании
    if($mode == 'update' or $mode == 'add'){
        fn_add_breadcrumb(fn_get_lang_var($controller), $controller . ".manage");
        fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
    }
}


if($mode == 'manage'){
    $p = array("with_count_items" => true, "object_name"=>true);
    $p = array_merge($_REQUEST, $p);
    list($documents, $search) = fn_uns__get_documents($p, UNS_ITEMS_PER_PAGE);
    $view->assign('documents', $documents);
    $view->assign('search', $search);

    // OBJECTS *****************************************************************
    list($objects_plain, $search) = fn_uns__get_objects(array('plain' => true,
                                                              'all'   => true));
    $view->assign('objects_plain', $objects_plain);

}


if($mode == 'update'){
    if(!is__more_0($_REQUEST['document_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    // DOCUMENT ****************************************************************
    $p = array("with_items" => true, "document_id" => $_REQUEST['document_id']);
    $p = array_merge($_REQUEST, $p);
    list($documents) = fn_uns__get_documents($p);

    $documents = fn_uns__calc_total_weight($documents);

    $document = array_shift($documents);
    $view->assign('document', $document);

    // OBJECTS *****************************************************************
    list($objects_plain, $search) = fn_uns__get_objects(array('plain' => true,
                                                              'all'   => true));
    $view->assign('objects_plain', $objects_plain);

    // ENABLED OBJECTS *********************************************************
    $enabled_objects = fn_uns__get_enabled_objects_old();
    $view->assign('enabled_objects', $enabled_objects);

    // CATEGORIES **************************************************************
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true));
    $view->assign('mcategories_plain', $mcategories_plain);
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('dcategories_plain', $dcategories_plain);

}


if($mode == 'add'){
    $view->assign('add_document_type', $_REQUEST['document_type']);

    // OBJECTS *****************************************************************
    list($objects_plain, $search) = fn_uns__get_objects(array('plain' => true,
                                                              'all'   => true));
    $view->assign('objects_plain', $objects_plain);

    // ENABLED OBJECTS *********************************************************
    $enabled_objects = fn_uns__get_enabled_objects_old();
    $view->assign('enabled_objects', $enabled_objects);
}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['document_id'])){
        fn_uns__del_document($_REQUEST['document_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


if($mode == 'update_status'){
    if (is__more_0($_REQUEST['id']) and in_array($_REQUEST["status"], array("A", "D"))){
        fn_uns__upd_document_items_motions($_REQUEST['id'], ($_REQUEST["status"] == "A")?"Y":"N");
        exit;
    }
}












