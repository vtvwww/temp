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

    if ($mode == "shipment" and $action == "update"){
//        fn_print_r($_REQUEST);
        $document_id = $_REQUEST['document_id'];
        $data = array("document"=> $_REQUEST["shipment"]["document"],
                      "document_items"=> $_REQUEST["order_data"]["document_items"],
        );
        fn_uns__upd_document($document_id, $data);
        $suffix = "update&order_id={$_REQUEST['order_id']}";
    }

    if (defined('AJAX_REQUEST') and $mode == 'document_items'){
        switch ($_REQUEST['event']){
            case "change__item_type": // Произошла смена ТИПА ДЕТАЛИ
                if(in_array($_REQUEST['item_type'], array('D', 'M', 'P', 'PF', 'PA'))){
                    $options = "<option value='0'>---</option>";
                    //ДЕТАЛЬ
                    if($_REQUEST['item_type'] == "D"){
                        $p = array(
                            'only_active' => true,
                            'group_by_categories'=>true,
                            'use_short_name'=>true,
                        );
                        list($category_details) = fn_uns__get_details($p);
                        $view->assign("f_type", "select_by_group");
                        $view->assign("f_options", "details");
                        $view->assign("f_option_id", "detail_id");
                        $view->assign("f_option_value", "detail_name");
                        $view->assign("f_optgroups", $category_details);
                        $view->assign("f_optgroup_label", "dcat_name");
                        $view->assign('f_simple_2', true);


                    //НАСОС, НАСОС НА РАМЕ, НАСОСНЫЙ АГРЕГАТ
                    } elseif(in_array($_REQUEST['item_type'], array("P", "PF", "PA"))){
                        list($pumps) = fn_uns__get_pumps(array("group_by_series"=>true));
                        $view->assign("f_type", "select_by_group");
                        $view->assign("f_options", "pumps");
                        $view->assign("f_option_id", "p_id");
                        $view->assign("f_option_value", "p_name");
                        $view->assign("f_optgroups", $pumps);
                        $view->assign("f_optgroup_label", "ps_name");
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

    // выбор КЛИЕНТА по стране, региону и городу
    if (defined('AJAX_REQUEST') and $mode == 'customer'){
        switch ($_REQUEST['event']){
            case "change__country_id": // Произошла смена СТРАНЫ
                $regions    = array_shift(fn_uns__get_regions(array("country_id"=>$_REQUEST["country_id"], "only_used"=>true,)));
                $view->assign("f_type", "select");
                $view->assign("f_options", $regions);
                $view->assign("f_option_id", "id");
                $view->assign("f_option_value", "name");
                $view->assign('f_simple_2', true);
                $options = "<option value='0'>---</option>";
                $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                $ajax->assign('options', $options);
            break;
            case "change__region_id": // Произошла смена РЕГИОНА
                $cities    = array_shift(fn_uns__get_cities(array("region_id"=>$_REQUEST["region_id"], "only_used"=>true,)));
                $view->assign("f_type", "select");
                $view->assign("f_options", $cities);
                $view->assign("f_option_id", "id");
                $view->assign("f_option_value", "name");
                $view->assign('f_simple_2', true);
                $options = "<option value='0'>---</option>";
                $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                $ajax->assign('options', $options);
            break;
            case "change__city_id": // Произошла смена ГОРОДА
                $customers = array_shift(fn_uns__get_customers(array("country_id"=>$_REQUEST["country_id"],"region_id"=>$_REQUEST["region_id"],"city_id"=>$_REQUEST["city_id"],)));
                $view->assign("f_type", "select");
                $view->assign("f_options", $customers);
                $view->assign("f_option_id", "customer_id");
                $view->assign("f_option_value", "name");
                $view->assign('f_simple_2', true);
                $options = "<option value='0'>---</option>";
                $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
                $ajax->assign('options', $options);
            break;
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
        $anchor = (is__more_0($_REQUEST['order_id']))?"#".$_REQUEST['order_id']:"";
        fn_add_breadcrumb(fn_get_lang_var($controller), $controller . ".manage" . $anchor);
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

    $countries  = array_shift(fn_uns__get_countries());
    $regions    = array_shift(fn_uns__get_regions());
    $cities     = array_shift(fn_uns__get_cities());
    $view->assign("countries", $countries);
    $view->assign("regions", $regions);
    $view->assign("cities", $cities);
}

if($mode == 'add'){
    //PUMPS
    list($pumps) = fn_uns__get_pumps(array("group_by_series"=>true));
    $view->assign('pumps', $pumps);

    $countries  = array_shift(fn_uns__get_countries());
    $regions    = array_shift(fn_uns__get_regions());
    $cities     = array_shift(fn_uns__get_cities());
    $view->assign("countries", $countries);
    $view->assign("regions", $regions);
    $view->assign("cities", $cities);
}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['order_id']) or !is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT order_id FROM ?:_acc_orders WHERE order_id = ?i", $_REQUEST['order_id']))){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    //--------------------------------------------------------------------------
    $p = array(
        "with_items"                => true,
        "full_info"                 => true,
        "total_weight_and_quantity" => true,
    );
    $p = array_merge($_REQUEST, $p);
    $order = array_shift(array_shift(fn_acc__get_orders($p)));
    $view->assign('order', $order);


    //--------------------------------------------------------------------------
    // Все Отгрузки по заказу
    // DOCUMENTS ***************************************************************
    $p = array();
    $p["with_items"]                = true;
    $p["info_category"]             = true;
    $p["info_item"]                 = false;
    $p["info_unit"]                 = false;
    $p["order_id"]                  = $_REQUEST['order_id'];
    list($documents) = fn_uns__get_documents($p);
    $view->assign("documents", $documents);
//    fn_print_r($documents);


    //--------------------------------------------------------------------------
    $countries  = array_shift(fn_uns__get_countries());
    $regions    = array_shift(fn_uns__get_regions(array("country_id"=>$order["country_id"])));
    $cities     = array_shift(fn_uns__get_cities(array("region_id"=>$order["region_id"])));
    $view->assign("countries", $countries);
    $view->assign("regions", $regions);
    $view->assign("cities", $cities);

    //--------------------------------------------------------------------------
    list($customers) = fn_uns__get_customers(array("country_id"=>$order["country_id"],"region_id"=>$order["region_id"],"city_id"=>$order["city_id"],));
    $view->assign('customers', $customers);

    //--------------------------------------------------------------------------
    list($pumps_by_series) = fn_uns__get_pumps(array("group_by_series"=>true, "only_active" => true,));
    $view->assign('pumps_by_series', $pumps_by_series);

    //--------------------------------------------------------------------------
    list($details_by_categories) = fn_uns__get_details(array('only_active' => true,'group_by_categories'=>true,"use_short_name"=>true,));
    $view->assign('details_by_categories', $details_by_categories);
}

if ($mode == 'shipment'){
    if ($action == "update"){
        $p = array();
        $p["with_items"]                = true;
        $p["info_category"]             = true;
        $p["info_item"]                 = false;
        $p["info_unit"]                 = false;
        $p["order_id"]                  = $_REQUEST["order_id"];
        $p["document_id"]               = $_REQUEST["document_id"];
        $shipment = array_shift(array_shift(fn_uns__get_documents($p)));
        $view->assign("shipment", $shipment);
//        fn_print_r($shipment);

        //----------------------------------------------------------------------
        $order = array_shift(array_shift(fn_acc__get_orders($_REQUEST)));
        $view->assign('order', $order);

        //----------------------------------------------------------------------
        list($pumps_by_series) = fn_uns__get_pumps(array("group_by_series"=>true, "only_active" => true,));
        $view->assign('pumps_by_series', $pumps_by_series);

        //--------------------------------------------------------------------------
        list($details_by_categories) = fn_uns__get_details(array('only_active' => true,'group_by_categories'=>true,"use_short_name"=>true,));
        $view->assign('details_by_categories', $details_by_categories);

    }

    if ($action == "add"){
//        //--------------------------------------------------------------------------
//        $p = array(
//            "with_items"                => true,
//            "full_info"                 => true,
//            "total_weight_and_quantity" => true,
//        );
//        $p = array_merge($_REQUEST, $p);
//        $order = array_shift(array_shift(fn_acc__get_orders($p)));
//        $view->assign('order', $order);
    }

    if ($action == "delete"){

    }
}




if($mode == 'delete'){
    if (is__more_0($_REQUEST["order_id"])){
        fn_uns__del_order($_REQUEST['order_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


function fn_uns_orders__search ($controller){
    $params = array(
        'country_id',
        'region_id',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}


