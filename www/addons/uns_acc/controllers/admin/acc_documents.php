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
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&document_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'm_delete'){
        if(is__array($_REQUEST['document_ids'])){
            fn_uns__del_document($_REQUEST['document_ids']);
        }
        $suffix = 'manage';
    }

    // 1. ПОЛУЧИТЬ ОБЕКТЫ "FROM"
    if (defined('AJAX_REQUEST') and $mode == 'get_object_from' and is__more_0($_REQUEST['type'])){
        $objects_plain   = array_shift(fn_uns__get_objects(array('plain' => true, 'all' => true)));
        $enabled_objects_from = array_keys(fn_uns__get_enabled_objects($_REQUEST['type']));

        $view->assign('f_simple_2',         true);
        $view->assign('f_type',             'objects_plain');
        $view->assign('f_options',          $objects_plain);
        $view->assign('f_option_id',        'mcat_id');
        $view->assign('f_option_value',     'mcat_name');
        $view->assign('f_option_target_id', $material['mcat_id']);
        $view->assign('f_view_id',          true);
        $view->assign('f_options_enabled',  $enabled_objects_from);
        $view->assign('f_blank',            true);
        $view->assign('f_blank_name',       '---');
        $ajax->assign('object_from', trim($view->fetch('addons/uns/views/components/get_form_field.tpl')));

        // 2. ОТОБРАЖЕНИЕ ДАТЫ ПЛАВКИ
        if ($_REQUEST['type'] == DOC_TYPE__VLC){ // Выпуск Литейного Цеха
            $ajax->assign('date_cast', 'Y');
        }else{
            $ajax->assign('date_cast', 'N');
        }

//        if ($_REQUEST['type'] == DOC_TYPE__AIO or $_REQUEST['type'] == DOC_TYPE__RO ){ // Акт изменения остатка
        if (in_array($_REQUEST['type'], array(DOC_TYPE__AIO, DOC_TYPE__RO, DOC_TYPE__AS_VLC))){ // Акт изменения остатка
            $ajax->assign('aio', 'Y');
            $ajax->assign('ro', 'Y');
            $ajax->assign('as_vlc', 'Y');
        }else{
            $ajax->assign('aio', 'N');
            $ajax->assign('ro', 'N');
            $ajax->assign('as_vlc', 'N');
        }

        exit;
    }

    // 2. ПОЛУЧИТЬ ОБЕКТЫ "TO"
    if (defined('AJAX_REQUEST') and $mode == 'get_object_to' and is__more_0($_REQUEST['type']) and is__more_0($_REQUEST['object_from'])){
        $objects_plain   = array_shift(fn_uns__get_objects(array('plain' => true, 'all' => true)));
        $enabled_objects_from = fn_uns__get_enabled_objects($_REQUEST['type']);
        $enabled_objects_to = array_keys($enabled_objects_from[$_REQUEST['object_from']]);

        $view->assign('f_simple_2',         true);
        $view->assign('f_type',             'objects_plain');
        $view->assign('f_options',          $objects_plain);
        $view->assign('f_option_id',        'mcat_id');
        $view->assign('f_option_value',     'mcat_name');
        $view->assign('f_option_target_id', $material['mcat_id']);
        $view->assign('f_view_id',          true);
        $view->assign('f_options_enabled',  $enabled_objects_to);
        $view->assign('f_blank',            true);
        $view->assign('f_blank_name',       '---');
        $ajax->assign('object_to', trim($view->fetch('addons/uns/views/components/get_form_field.tpl')));
        exit;
    }

    // 3. Получить категории
    if (defined('AJAX_REQUEST') and $mode == 'document_items'){
        switch ($_REQUEST['event']){
            case "change__item_type": // Произошла смена ТИПА ДЕТАЛИ
                if(in_array($_REQUEST['item_type'], array('D', 'M'))){
                    $options = "<option value='0'>---</option>";
                    if($_REQUEST['item_type'] == "D"){
                        list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
                        $view->assign('f_type', 'dcategories_plain');
                        $view->assign('f_options', $dcategories_plain);
                        $view->assign('f_option_id', 'dcat_id');
                        $view->assign('f_option_value', 'dcat_name');
                        $view->assign('f_with_q_ty', false);
                        $view->assign('f_simple_2', true);
                        $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                    } else{
                        $p = array( 'plain'         => true,
                                    'mcat_id'       => UNS_MATERIAL_CATEGORY__CAST,
                                    'include_child' => true,
                        );
                        list($mcategories_plain) = fn_uns__get_materials_categories($p);
                        $view->assign('f_type', 'mcategories_plain');
                        $view->assign('f_options', $mcategories_plain);
                        $view->assign('f_option_id', 'mcat_id');
                        $view->assign('f_option_value', 'mcat_name');
                        $view->assign('f_with_q_ty', false);
                        $view->assign('f_simple_2', true);
                        $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                    }
                    $ajax->assign('options', $options);
                    exit;
                }
                break;

            case "change__item_cat_id":
                // Произошла смена категории Детали/Материала
                if(in_array($_REQUEST['item_type'], array('D', 'M')) && is__more_0($_REQUEST['item_cat_id'])){
                    $options = "<option value='0'>---</option>";
                    if($_REQUEST['item_type'] == "D"){
                        $p = array('dcat_id'            => $_REQUEST['item_cat_id'],
                                   'with_accounting'    => true,
                                   'with_materials'     => true,
                                   'with_material_info' => true,
                                   'format_name'        => true);
                        list ($details) = fn_uns__get_details($p);
                        $view->assign('f_type', 'select');
                        $view->assign('f_options', $details);
                        $view->assign('f_option_id', 'detail_id');
                        $view->assign('f_option_value', 'format_name');
                        $view->assign('f_add_value', 'material_no');
                        $view->assign('f_simple_2', true);
                        $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                    } else{
                        $p = array('mcat_id'         => $_REQUEST['item_cat_id'],
                                   'with_accounting' => true,
                                   'format_name'     => true);
                        list ($materials) = fn_uns__get_materials($p);
                        $view->assign('f_type', 'select');
                        $view->assign('f_options', $materials);
                        $view->assign('f_option_id', 'material_id');
                        $view->assign('f_option_value', 'format_name');
                        $view->assign('f_simple_2', true);
                        $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                    }
                    $ajax->assign('options', $options);
                    exit;
                }
                break;

            case "change__item_id":
                // Произошла смена Детали/Материала
                if(in_array($_REQUEST['item_type'], array('D','M')) && is__more_0($_REQUEST['item_id'])){
                    if($_REQUEST['item_type'] == "D"){
                        $p = array('detail_id' => $_REQUEST['item_id'],
                                   'item_type' => $_REQUEST['item_type']);
                    } else{
                        $p = array('material_id' => $_REQUEST['item_id'],
                                   'item_type'   => $_REQUEST['item_type'],
                                   /*'u_id_add'    => array(UNS_UNIT_WEIGHT)*/
                        );
                    }
                    list ($units) = fn_uns__get_units($p);

                    $view->assign('f_type', 'select');
                    $view->assign('f_options', $units);
                    $view->assign('f_option_id', 'u_id');
                    $view->assign('f_option_value', 'u_name');
                    $view->assign('f_simple_2', true);

                    $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));

                    $ajax->assign('options', $options);

                    // ТИПОРАЗМЕР
                    $typesizes = '';
                    if ($_REQUEST['document_type'] == 1){
                        // Лит цех
                    }else{
                        if($_REQUEST['item_type'] == "D"){
                            list($detail) = fn_uns__get_details(array("detail_id" => $_REQUEST['item_id']));
                            $detail = array_shift($detail);
                            $view->assign('f_type', 'typesize');
                            $view->assign('f_a', $detail['size_a']);
                            $view->assign('f_b', $detail['size_b']);
                            $view->assign('f_simple', true);
                            $typesizes =  trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                        }
                    }
                    $ajax->assign('typesizes', $typesizes);

                    // БАЛАНС
                    $balance = -12;
                    $p = array(
                        "plain"             => true,
                        "all"               => true,
                        "o_id"              => array(8),  // Склад литья
                        "item_type"         => $_REQUEST['item_type'],
                        "item_id"           => $_REQUEST['item_id'],
                        "add_item_info"     => false,
                        "view_all_position" => "Y",
                        "mclass_id"         => 1,
                        "with_weight"       => true,
                    );

                    list ($p['time_from'], $p['time_to']) = fn_create_periods(null);

                    list($balance, $search) = fn_uns__get_balance($p);
                    $balance = fn_fvalue($balance[$_REQUEST['item_id']]['ko']);

                    if ($balance<1) $balance = "<span title='Текущий остаток на Складе литья' style='cursor:pointer; color:red; font-weight:bold;'>$balance</span>";
                    else $balance = "<span title='Текущий остаток на Складе литья' style='cursor:cursor; font-weight:bold;'>$balance</span>";
                    $ajax->assign('balance', $balance);
                    //----------------------------------------------------------


                    // ВЕС
                    $weight = 0;
                    if($_REQUEST['item_type'] == "D"){
                    } else{
                        $p = array('material_id'     => $_REQUEST['item_id'],
                                   'with_accounting' => true,
                                   /*'format_name'     => true,*/
                        );
                        $material = array_shift(array_shift(fn_uns__get_materials($p)));
                        $weight = $material['accounting_data']['weight'];
                    }
                    $ajax->assign('weight', fn_fvalue($weight));
                    exit;
                }
            break;
            default:
                break;
        }
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
    $p = array("with_count_items" => true, "object_name" => true, "type"=>1);

    $p = array_merge($p, $_REQUEST);
    list($documents, $search) = fn_uns__get_documents($p, UNS_ITEMS_PER_PAGE);
    $view->assign('documents', $documents);
    $view->assign('search', $search);

//    fn_print_r($documents);

    // OBJECTS *****************************************************************
    list($objects_plain, $search) = fn_uns__get_objects(array('plain' => true,
                                                              'all'   => true));
    $view->assign('objects_plain', $objects_plain);

    // СПИСОК ТИПОВ ДОКУМЕНТОВ
    list($document_types) = fn_uns__get_document_types(array('status'=>'A'));
    $view->assign('document_types', $document_types);
    $document_types_enabled = array('VLC', 'RO', 'AIO', 'AS_VLC');
    $view->assign('document_types_enabled', $document_types_enabled);
}


if($mode == 'update' or $mode == 'view'){
    if(!is__more_0($_REQUEST['document_id']) or !is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT document_id FROM ?:_acc_documents WHERE document_id = ?i", $_REQUEST['document_id']))){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    // DOCUMENT ****************************************************************
    $p = array("with_items" => true, "document_id" => $_REQUEST['document_id']);
    $p = array_merge($_REQUEST, $p);
    $document = array_shift(array_shift(fn_uns__get_documents($p)));
    $view->assign('document', $document);

//    fn_print_r($document);


    // СПИСОК ТИПОВ ДОКУМЕНТОВ
    list($document_types) = fn_uns__get_document_types();
    $view->assign('document_types', $document_types);

    // OBJECTS *****************************************************************
    list($objects_plain, $search) = fn_uns__get_objects(array('plain' => true,
                                                              'all'   => true));
    $view->assign('objects_plain', $objects_plain);

    // ENABLED OBJECTS *********************************************************
    $enabled_objects_from = fn_uns__get_enabled_objects_old();
    $view->assign('enabled_objects', $enabled_objects_from);

    // CATEGORIES **************************************************************
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true, 'with_q_ty' => false));
    $view->assign('mcategories_plain', $mcategories_plain);
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('dcategories_plain', $dcategories_plain);

}


if($mode == 'add'){
    list($document_types) = fn_uns__get_document_types();
    $view->assign('document_types', $document_types);
    $document_types_enabled = array('VLC', 'RO', 'AIO', 'AS_VLC');
    $view->assign('document_types_enabled', $document_types_enabled);
}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['document_id'])){
        fn_uns__del_document($_REQUEST['document_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


if($mode == 'update_status'){
    if (is__more_0($_REQUEST['id']) and in_array($_REQUEST["status"], array("A", "D"))){
        if (false !== fn_uns__upd_document_status($_REQUEST['id'], $_REQUEST["status"])){
            fn_set_notification('N', 'Статус обновлен успешно!', '');
        }else{
            fn_set_notification('W', 'Ошибка при обновлении!', '');
        }
        exit;
    }
}

function fn_acc_documents__search ($controller){
    $params = array(
        'period',
        'time_from',
        'time_to',
        'type'
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}

