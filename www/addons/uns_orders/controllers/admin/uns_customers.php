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
        $id = fn_uns__upd_customer($_REQUEST['customer_id'], $_REQUEST['data']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&customer_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    // выбор страны, региона и города
    if (defined('AJAX_REQUEST') and $mode == 'customer'){
        switch ($_REQUEST['event']){
            case "change__country_id": // Произошла смена СТРАНЫ
                $regions    = array_shift(fn_uns__get_regions(array("country_id"=>$_REQUEST["country_id"], "only_used"=>false,)));
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
                $cities    = array_shift(fn_uns__get_cities(array("region_id"=>$_REQUEST["region_id"], "only_used"=>false,)));
                $view->assign("f_type", "select");
                $view->assign("f_options", $cities);
                $view->assign("f_option_id", "id");
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

if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    if($mode == 'update' or $mode == 'add'){
        fn_add_breadcrumb(fn_get_lang_var($controller), $controller . ".manage");
        fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
    }
}

if($mode == 'manage'){
    // CUSTOMERS
    list($customers, $search) = fn_uns__get_customers(array_merge($_REQUEST, array()), UNS_ITEMS_PER_PAGE);
    $view->assign('customers', $customers);
    $view->assign('search',    $search);

    $countries  = array_shift(fn_uns__get_countries());
    $regions    = array_shift(fn_uns__get_regions());
    $cities     = array_shift(fn_uns__get_cities());
    $view->assign("countries", $countries);
    $view->assign("regions", $regions);
    $view->assign("cities", $cities);
}

if($mode == 'update'){
    if (is__more_0($_REQUEST["customer_id"])){
        $customer = array_shift(array_shift(fn_uns__get_customers(array("customer_id"=>$_REQUEST["customer_id"]))));
        $view->assign("customer", $customer);

        $countries  = array_shift(fn_uns__get_countries());
        $regions    = array_shift(fn_uns__get_regions(array("country_id"=>$customer["country_id"])));
        $cities     = array_shift(fn_uns__get_cities(array("region_id"=>$customer["region_id"], "country_id"=>$customer["country_id"])));
        $view->assign("countries", $countries);
        $view->assign("regions", $regions);
        $view->assign("cities", $cities);

    }
}

if($mode == 'add'){
    $countries  = array_shift(fn_uns__get_countries());
//    $regions    = array_shift(fn_uns__get_regions(array("country_id"=>$customer["country_id"])));
//    $cities     = array_shift(fn_uns__get_cities(array("region_id"=>$customer["region_id"], "country_id"=>$customer["country_id"])));
    $view->assign("countries", $countries);
//    $view->assign("regions", $regions);
//    $view->assign("cities", $cities);
}

if($mode == 'delete'){
    if (is__more_0($_REQUEST["customer_id"])){
        fn_uns__del_customer($_REQUEST['customer_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

