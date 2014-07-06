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
        $id = fn_acc__upd_order($_REQUEST['order_id'], $_REQUEST['order_data']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&order_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if (defined('AJAX_REQUEST') and $mode == 'document_items'){
        switch ($_REQUEST['event']){
            case "change__item_type": // Произошла смена ТИПА ДЕТАЛИ
                if(in_array($_REQUEST['item_type'], array('D', 'M', 'P', 'PF', 'PA'))){
                    $options = "<option value='0'>---</option>";
                    //ДЕТАЛЬ
                    if($_REQUEST['item_type'] == "D"){
                        list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true, "view_in_reports" => true));
                        $view->assign('f_type', 'dcategories_plain');
                        $view->assign('f_options', $dcategories_plain);
                        $view->assign('f_option_id', 'dcat_id');
                        $view->assign('f_option_value', 'dcat_name');
                        $view->assign('f_with_q_ty', false);
                        $view->assign('f_simple_2', true);

                    //НАСОС, НАСОС НА РАМЕ, НАСОСНЫЙ АГРЕГАТ
                    } elseif(in_array($_REQUEST['item_type'], array("P", "PF", "PA"))){
                        $p = array(
                            'only_active' => true,
                            'group_by_types'=>true,
                        );
                        list($pump_series) = fn_uns__get_pump_series($p);
                        $view->assign("f_type", "select_by_group");
                        $view->assign("f_options", "pump_series");
                        $view->assign("f_option_id", "ps_id");
                        $view->assign("f_option_value", "ps_name");
                        $view->assign("f_optgroups", $pump_series);
                        $view->assign("f_optgroup_label", "pt_name_short");
                        $view->assign('f_simple_2', true);
                        $ajax->assign('processing', "hide");
                    }
                    $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                    $ajax->assign('options', $options);
                    exit;
                }
                break;

            case "change__item_cat_id":
                // Произошла смена категории//серии Детали/Материала//Насоса
                if(in_array($_REQUEST['item_type'], array("D", "M", "P", "PF", "PA")) && is__more_0($_REQUEST['item_cat_id'])){
                    $options = "<option value='0'>---</option>";

                    //ДЕТАЛЬ
                    if($_REQUEST['item_type'] == "D"){
                        $p = array('dcat_id'            => $_REQUEST['item_cat_id'],
                                   'with_accounting'    => true,
                                   'with_materials'     => true,
                                   'with_material_info' => true,
                                   'only_active'        => true,
                                   'format_name'        => true);
                        list ($details) = fn_uns__get_details($p);
                        $view->assign('f_type', 'select');
                        $view->assign('f_options', $details);
                        $view->assign('f_option_id', 'detail_id');
                        $view->assign('f_option_value', 'format_name');
                        $view->assign('f_add_value', 'material_no');
                        $view->assign('f_simple_2', true);

                    //НАСОС, НАСОС НА РАМЕ, НАСОСНЫЙ АГРЕГАТ
                    } elseif(in_array($_REQUEST['item_type'], array("P", "PF", "PA"))){
                        $p = array(
                            'ps_id'         => $_REQUEST['item_cat_id'],
                        );
                        list ($pumps) = fn_uns__get_pumps($p);
                        $view->assign('f_type', 'select');
                        $view->assign('f_options', $pumps);
                        $view->assign('f_option_id', 'p_id');
                        $view->assign('f_option_value', 'p_name');
                        $view->assign('f_simple_2', true);
                    }
                    $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                    $ajax->assign('options', $options);
                    exit;
                }
                break;

            case "change__item_id":
                // Произошла смена Детали/Материала
                if(in_array($_REQUEST['item_type'], array("D", "M", "P", "PF", "PA")) && is__more_0($_REQUEST['item_id'])){
                    $options = "<option value='0'>---</option>";
                    $weight = 0;

                    //ДЕТАЛЬ
                    if($_REQUEST['item_type'] == "D"){
                        $p = array('detail_id'            => $_REQUEST['item_id'],
                                   'with_accounting'    => true,
                                   'with_materials'     => true,
                                   'with_material_info' => true,
                                   'only_active'        => true,
                                   'format_name'        => true);
                        $detail = array_shift(array_shift(fn_uns__get_details($p)));
                        $weight = $detail["accounting_data"]["weight"]["M"];

                    //НАСОС, НАСОС НА РАМЕ, НАСОСНЫЙ АГРЕГАТ
                    } elseif(in_array($_REQUEST['item_type'], array("P", "PF", "PA"))){
                        $p = array(
                            'p_id'         => $_REQUEST['item_id'],
                        );
                        $pump = array_shift(array_shift(fn_uns__get_pumps($p)));
                        switch ($_REQUEST["item_type"]){
                            case "P":   $weight = $pump["weight_p"];    break;
                            case "PF":  $weight = $pump["weight_pf"];   break;
                            case "PA":  $weight = $pump["weight_pa"];   break;
                        }
                    }
                    $ajax->assign('weight', fn_fvalue($weight));
                    exit;
                }
            break;
            default: break;
        }
        exit;
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}

//**************************************************************************
// KIT
//**************************************************************************
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
        "full_info" => true,
        "with_count" => true,
        "total_weight_and_quantity" => true,
    );
    $p = array_merge($_REQUEST, $p);
    list($orders, $search) = fn_acc__get_orders($p, UNS_ITEMS_PER_PAGE);
    $view->assign('orders', $orders);
    $view->assign('search', $search);

    // CUSTOMERS
    list($customers) = fn_uns__get_customers();
    $view->assign('customers', $customers);
}



if($mode == 'add'){
    //PUMPS
    list($pumps) = fn_uns__get_pumps(array("group_by_series"=>true));
    $view->assign('pumps', $pumps);

    // customerS
    list($customers) = fn_uns__get_customers();
    $view->assign('customers', $customers);

    // PUMP_SERIES
    $p = array(
        'only_active' => true,
        'group_by_types'=>true,
    );
    list($pump_series) = fn_uns__get_pump_series($p);
    $view->assign('pump_series', $pump_series);

}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['order_id']) or !is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT order_id FROM ?:_acc_orders WHERE order_id = ?i", $_REQUEST['order_id']))){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }
    $p = array(
        "with_items"                => true,
        "full_info"                 => true,
        "total_weight_and_quantity" => true,
    );
    $p = array_merge($_REQUEST, $p);
    $order = array_shift(array_shift(fn_acc__get_orders($p)));
//    fn_print_r($order);
    $view->assign('order', $order);

    // CATEGORIES **************************************************************
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true, "view_in_reports" => true));
    $view->assign('dcategories_plain', $dcategories_plain);

    // customerS
    list($customers) = fn_uns__get_customers();
    $view->assign('customers', $customers);

    // PUMP_SERIES
    $p = array(
        'only_active' => true,
        'group_by_types'=>true,
    );
    list($pump_series) = fn_uns__get_pump_series($p);
    $view->assign('pump_series', $pump_series);
//    fn_print_r($pump_series);
}


if($mode == 'delete'){
    if (is__more_0($_REQUEST["order_id"])){
        fn_uns__del_order($_REQUEST['order_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


function fn_uns_orders__search ($controller){
    $params = array(
        'period',
        'time_from',
        'time_to',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}


