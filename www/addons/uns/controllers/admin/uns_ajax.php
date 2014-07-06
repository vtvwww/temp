<?php

if(!defined('AREA')){
    die('Access denied');
}

// Только для обслуживания AJAX - запросов

if($_SERVER['REQUEST_METHOD'] == 'POST' && defined('AJAX_REQUEST')){
    switch($mode){
        case "packing_list":
            switch($action){
                case "item_type":
                    // Произошла смена ТИПА ДЕТАЛИ
                    if(in_array($_REQUEST['item_type'], array('D', 'M'))){
                        $options = "<option value='0'>---</option>";
                        if($_REQUEST['item_type'] == "D"){
                            list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true, "view_in_reports" => true));
                            $view->assign('f_type', 'dcategories_plain');
                            $view->assign('f_options', $dcategories_plain);
                            $view->assign('f_option_id', 'dcat_id');
                            $view->assign('f_option_value', 'dcat_name');
                            $view->assign('f_simple_2', true);
                            $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                        } else{
                            list($mcategories_plain) = fn_uns__get_materials_categories(array('plain'           => true,
                                                                                              'mcat_id_exclude' => UNS_MATERIAL_CATEGORY__CAST)); // Исключить категрию "ОТЛИВКИ"
                            $view->assign('f_type', 'mcategories_plain');
                            $view->assign('f_options', $mcategories_plain);
                            $view->assign('f_option_id', 'mcat_id');
                            $view->assign('f_option_value', 'mcat_name');
                            $view->assign('f_simple_2', true);
                            $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                        }
                        $ajax->assign('options', $options);
                        exit;
                    }
                    break;

                case "item_cat_id":
                    // Произошла смена категории Детали/Материала
                    if(in_array($_REQUEST['item_type'], array('D',
                        'M')) && is__more_0($_REQUEST['item_cat_id'])
                    ){
                        $options = "<option value='0'>---</option>";
                        if($_REQUEST['item_type'] == "D"){
                            $p = array('dcat_id'         => $_REQUEST['item_cat_id'],
//                                       'with_accounting' => true,
//                                       'with_materials'  => true,
                                       'format_name'     => true);
                            list ($details) = fn_uns__get_details($p);
                            $view->assign('f_type', 'select');
                            $view->assign('f_options', $details);
                            $view->assign('f_option_id', 'detail_id');
                            $view->assign('f_option_value', 'format_name');
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

                case "item_id":
                    // Произошла смена Детали/Материала
                    if(in_array($_REQUEST['item_type'], array('D',
                        'M')) && is__more_0($_REQUEST['item_id'])
                    ){
                        //$options = "<option value='0'>---</option>";
                        if($_REQUEST['item_type'] == "D"){
                            $p = array('detail_id' => $_REQUEST['item_id'],
                                       'item_type' => $_REQUEST['item_type']);
                        } else{
                            $p = array('material_id' => $_REQUEST['item_id'],
                                       'item_type'   => $_REQUEST['item_type']);
                        }
                        list ($units) = fn_uns__get_units($p);

                        $view->assign('f_type', 'select');
                        $view->assign('f_options', $units);
                        $view->assign('f_option_id', 'u_id');
                        $view->assign('f_option_value', 'u_name');
                        $view->assign('f_simple_2', true);

                        $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));

                        $ajax->assign('options', $options);

                        $typesizes = '';
                        if($_REQUEST['item_type'] == "D"){
                            list($detail) = fn_uns__get_details(array("detail_id" => $_REQUEST['item_id']));
                            $detail = array_shift($detail);
                            $view->assign('f_type', 'typesize');
                            $view->assign('f_a', $detail['size_a']);
                            $view->assign('f_b', $detail['size_b']);
                            $view->assign('f_simple', true);
                            $typesizes =  trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                        }
                        $ajax->assign('typesizes', $typesizes);
                        exit;
                    }
                    break;
            }
            break;

        case "uns_details":
            switch($action){
                case "get_materials":
                    if(is__more_0($_REQUEST['cat_id'])){
                        $p = array('mcat_id'         => $_REQUEST['cat_id'],
                                   'with_accounting' => true,
                                   'format_name'     => true);
                        list ($variants) = fn_uns__get_materials($p);

                        $options = "<option value='0'>---</option>";
                        if(is__array($variants)){
                            $view->assign('f_type', 'select');
                            $view->assign('f_options', $variants);
                            $view->assign('f_option_id', 'material_id');
                            $view->assign('f_option_value', 'format_name');
                            $view->assign('f_simple_2', true);
                            $view->assign('f_blank', true);
                            $options = trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                        }
                        $ajax->assign('options', $options);
                        exit;
                    }
                    break;

                case "get_material_units":
                    if(is__more_0($_REQUEST['material_id'])){
                        $p = array('material_id' => $_REQUEST['material_id'],
                                   'item_type'   => 'M');
                        list ($variants) = fn_uns__get_units($p);

                        $options = "";

                        if(is__array($variants)){
                            $view->assign('f_type', 'select');
                            $view->assign('f_options', $variants);
                            $view->assign('f_option_id', 'u_id');
                            $view->assign('f_option_value', 'u_name');
                            $view->assign('f_simple_2', true);
                            $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                        }
                        $add_quantity_state = "D";
                        $unit = array_shift($variants);
                        if ($unit['uc_id'] == UNS_UNIT_CATEGORY__AREA){
                            $add_quantity_state = "A";
                        }

                        $ajax->assign('options', $options);
                        $ajax->assign('add_quantity_state', $add_quantity_state);
                        exit;
                    }
                    break;

                default:
                    break;
            }
            break;

        case "document":
            switch($action){
                case "item_type":
                    // Произошла смена ТИПА ДЕТАЛИ
                    if(in_array($_REQUEST['item_type'], array('D', 'M'))){
                        $options = "<option value='0'>---</option>";
                        if($_REQUEST['item_type'] == "D"){
                            list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true, "view_in_reports" => true));
                            $view->assign('f_type', 'dcategories_plain');
                            $view->assign('f_options', $dcategories_plain);
                            $view->assign('f_option_id', 'dcat_id');
                            $view->assign('f_option_value', 'dcat_name');
                            $view->assign('f_simple_2', true);
                            $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                        } else{
                            list($mcategories_plain) = fn_uns__get_materials_categories(array('plain'           => true,
                                                                                              /*'mcat_id_exclude' => UNS_MATERIAL_CATEGORY__CAST*/)); // Исключить категрию "ОТЛИВКИ"
                            $view->assign('f_type', 'mcategories_plain');
                            $view->assign('f_options', $mcategories_plain);
                            $view->assign('f_option_id', 'mcat_id');
                            $view->assign('f_option_value', 'mcat_name');
                            $view->assign('f_simple_2', true);
                            $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                        }
                        $ajax->assign('options', $options);
                        exit;
                    }
                    break;

                case "item_cat_id":
                    // Произошла смена категории Детали/Материала
                    if(in_array($_REQUEST['item_type'], array('D',
                        'M')) && is__more_0($_REQUEST['item_cat_id'])
                    ){
                        $options = "<option value='0'>---</option>";
                        if($_REQUEST['item_type'] == "D"){
                            $p = array('dcat_id'         => $_REQUEST['item_cat_id'],
                                       'with_accounting' => true,
                                       'with_materials'  => true,
                                       'format_name'     => true);
                            list ($details) = fn_uns__get_details($p);
                            $view->assign('f_type', 'select');
                            $view->assign('f_options', $details);
                            $view->assign('f_option_id', 'detail_id');
                            $view->assign('f_option_value', 'format_name');
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

                case "item_id":
                    // Произошла смена Детали/Материала
                    if(in_array($_REQUEST['item_type'], array('D',
                        'M')) && is__more_0($_REQUEST['item_id'])
                    ){
                        //$options = "<option value='0'>---</option>";
                        if($_REQUEST['item_type'] == "D"){
                            $p = array('detail_id' => $_REQUEST['item_id'],
                                       'item_type' => $_REQUEST['item_type']);
                        } else{
                            $p = array('material_id' => $_REQUEST['item_id'],
                                       'item_type'   => $_REQUEST['item_type'],
                                       'u_id_add'    => array(UNS_UNIT_WEIGHT)
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

                        $typesizes = '';
                        if($_REQUEST['item_type'] == "D"){
                            list($detail) = fn_uns__get_details(array("detail_id" => $_REQUEST['item_id']));
                            $detail = array_shift($detail);
                            $view->assign('f_type', 'typesize');
                            $view->assign('f_a', $detail['size_a']);
                            $view->assign('f_b', $detail['size_b']);
                            $view->assign('f_simple', true);
                            $typesizes =  trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                        }
                        $ajax->assign('typesizes', $typesizes);
                        exit;
                    }
                    break;
            }
            break;

        default:
            break;
    }
}

?>