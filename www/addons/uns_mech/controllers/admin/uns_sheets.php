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
        $id = fn_acc__upd_sheet($_REQUEST['sheet_id'], $_REQUEST['data']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&sheet_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    // ЗАПРОС МАТЕРИАЛА ПРИ РЕДАКТИРОВАНИИ СЛ
    if (defined('AJAX_REQUEST') and $mode == 'get_materials' and is__more_0($_REQUEST['mcat_id']) and $_REQUEST['event'] == "change__mcat_id"){
        $p = array('mcat_id'         => $_REQUEST['mcat_id'],
                   'with_accounting' => true,
                   'format_name'     => true);
        list ($materials) = fn_uns__get_materials($p);
        $view->assign('f_type', 'select');
        $view->assign('f_blank', true);
        $view->assign('f_options', $materials);
        $view->assign('f_option_id', 'material_id');
        $view->assign('f_option_value', 'format_name');
        $view->assign('f_simple_2', true);
        $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
        $ajax->assign('options', $options);
        exit;
    }

    // ЗАПРОС ДЕТАЛЕЙ ПРИ РЕДАКТИРОВАНИИ СЛ
    if (defined('AJAX_REQUEST') and $mode == 'get_details' and is__more_0($_REQUEST['dcat_id']) and $_REQUEST['event'] == "change__dcat_id"){
        $p = array('dcat_id'         => $_REQUEST['dcat_id'],
                   'with_accounting' => true,
                   'format_name'     => true,
                   'with_material_info'=>true,
        );
        list ($details) = fn_uns__get_details($p);
        $view->assign('f_type', 'select');
        $view->assign('f_blank', true);
        $view->assign('f_options', $details);
        $view->assign('f_option_id', 'detail_id');
        $view->assign('f_option_value', 'format_name');
        $view->assign('f_add_value', 'material_no');
        $view->assign('f_simple_2', true);
        $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
        $ajax->assign('options', $options);
        exit;
    }


    // РЕДАКТИРОВАНИЕ ДВИЖЕНИЙ
    if (($mode == 'motion') and ($action == 'update')){
        if (is__more_0($_REQUEST['sheet_id'])){
            $id = fn_acc__upd_motion($_REQUEST['sheet_id'], $_REQUEST['document_id'], $_REQUEST['motion']);
            if($id !== false){
                fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
            }
            fn_delete_notification('changes_saved');
        }
        $suffix = "update&sheet_id={$_REQUEST['sheet_id']}&selected_section={$_REQUEST['selected_section']}";
    }

    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
//    fn_uns_add_sections($controller);

    // только при редактировании
    if($mode == 'update' or $mode == 'add'){
        fn_add_breadcrumb(fn_get_lang_var($controller), $controller . ".manage");
        fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
    }
}


if($mode == 'manage'){

    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    $p = array(
        "with_details"                  => true,
        "with_material_quantity_PVP"    => true, // Кол*во выданного литья по СЛ
        "with_material_quantity_BRAK"   => true, // Кол*во выданного литья по СЛ
    );

    $p = array_merge($_REQUEST, $p);

    list($sheets, $search) = fn_acc__get_sheets($p, UNS_ITEMS_PER_PAGE);
//    fn_print_r($sheets);
    $view->assign('sheets', $sheets);
    $view->assign('search', $search);

    // Запрос категорий
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true, "with_q_ty"=>false));
    $view->assign('mcategories_plain', $mcategories_plain);

    list($dcategories_plain) = fn_uns__get_details_categories(array("plain" => true, "with_q_ty"=>false));
    $view->assign('dcategories_plain', $dcategories_plain);
}


if($mode == 'update'){
    if(!is__more_0($_REQUEST['sheet_id']) or !is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT sheet_id FROM ?:_acc_sheets WHERE sheet_id = ?i", $_REQUEST['sheet_id']))){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    // SHEET *******************************************************************
    $p = array(
        "with_details" =>   true,
    );
    $p = array_merge($_REQUEST, $p);
    $sheet = array_shift(array_shift(fn_acc__get_sheets($p)));
    $view->assign('sheet', $sheet);
//    fn_print_r($sheet);

    // MOTIONS *****************************************************************
    $p = array(
        "with_count_items"          => true,
        "object_name"               => true,
        "period"                    => "A",
        "package_id"                => $_REQUEST['sheet_id'],
        "package_type"              => "SL",
        "get_info_document_type"    => true,
        "get_info_objects"          => true,
        "movement_items"            => true, // движение элементов
        "sorting_schemas"           => "view_asc",
    );
    list($motions, $search) = fn_uns__get_documents($p);
    $view->assign("motions", $motions);
//    fn_print_r($motions);

    // CATEGORIES **************************************************************
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true, 'with_q_ty' => false, 'mcat_include_target' => ''));
    $view->assign('mcategories_plain', $mcategories_plain);
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('dcategories_plain', $dcategories_plain);
}

if ($mode == 'motion' and $action != 'delete'){
    if(is__more_0($_REQUEST['sheet_id'])){
        // ИНФОРМАЦИЯ О СОПРОВОДИТЕЛЬНОМ ПИСЬМЕ
        $d = array(
            "with_details"  => true,
            "sheet_id"      => $_REQUEST['sheet_id'],
        );
        $sheet = array_shift(array_shift(fn_acc__get_sheets($d)));
        $view->assign('sheet', $sheet);

        // ТИПЫ ДОКУМЕНТОВ
        list($document_types) = fn_uns__get_document_types(array('status'=>'A'));
        $view->assign('document_types', $document_types);
        if (strlen($_REQUEST['document_type'])){
            $view->assign('document_type', $_REQUEST['document_type']);
        }

        // ИНФОРМАЦИЯ О ДОКУМЕНТЕН
        if(is__more_0($_REQUEST['document_id'])){
            $p = array(
                "with_count_items"          => true,
                "object_name"               => true,
                "document_id"               => $_REQUEST['document_id'],
                "package_id"                => $_REQUEST['sheet_id'],
                "package_type"              => "SL",
                "get_info_document_type"    => true,
                "get_info_objects"          => true,
                "movement_items"            => true, // движение элементов
            );
            $motion = array_shift(array_shift(fn_uns__get_documents($p)));
            $view->assign("motion", $motion);
        }
    }
}

if ($mode == 'motion' and $action == 'delete' and is__more_0($_REQUEST['document_id'], $_REQUEST['sheet_id'])){
    fn_uns__del_document($_REQUEST['document_id']);
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".update&sheet_id=".$_REQUEST['sheet_id']);
}

if($mode == 'add'){
    // CATEGORIES **************************************************************
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true, 'with_q_ty' => false, 'mcat_include_target' => ''));
    $view->assign('mcategories_plain', $mcategories_plain);
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('dcategories_plain', $dcategories_plain);
}


if($mode == 'delete'){
    if (is__more_0($_REQUEST["sheet_id"])){
        fn_uns__del_sheet($_REQUEST['sheet_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


if($mode == 'update_status'){
}

function fn_uns_sheets__search ($controller){
    $params = array(
        'period',
        'time_from',
        'time_to',

        'status',
        'material_type',
        'target_object',
        'mcat_id',
        'dcat_id',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}


