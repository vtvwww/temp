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

    //**************************************************************************
    // KIT
    //**************************************************************************
    // обновление KIT
    if($mode == 'update'){
        $id = fn_acc__upd_kit($_REQUEST['kit_id'], $_REQUEST['data']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&kit_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }



    //**************************************************************************
    // KIT_DETAILS
    //**************************************************************************
    // обновление детали KIT
    if ($mode == "detail_update"){
        $id = fn_acc__upd_kit_details($_REQUEST['kit_id'], $_REQUEST['kit_details']);
        if($id !== false){
            fn_set_notification("N", "", UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&kit_id={$_REQUEST['kit_id']}";
    }

    if (defined('AJAX_REQUEST') and $mode == 'get_details' and is__more_0($_REQUEST['dcat_id']) and $_REQUEST['event'] == "change__dcat_id"){
        $p = array('dcat_id'         => $_REQUEST['dcat_id'],
                   'with_accounting' => true,
                   'format_name'     => true);
        list ($details) = fn_uns__get_details($p);
        $view->assign('f_type', 'select');
        $view->assign('f_blank', true);
        $view->assign('f_options', $details);
        $view->assign('f_option_id', 'detail_id');
        $view->assign('f_option_value', 'format_name');
        $view->assign('f_simple_2', true);
        $options .= trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
        $ajax->assign('options', $options);
        exit;
    }

    if ($mode == "m_detail_update"){
        if (!is__more_0($_REQUEST['kit_id'])) return false;
        $id = fn_acc__upd_kit_details($_REQUEST['kit_id'], $_REQUEST['kit_details']);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&kit_id={$_REQUEST['kit_id']}";
    }

    if ($mode == "detail_mcp"){
        if (is__more_0($_REQUEST["kit_id"])
            and is__more_0($_REQUEST["detail_id"])
            and is__more_0($_REQUEST["object_from"])
            and is__more_0($_REQUEST["quantity"])
        ){
            $data = array();

            // DOCUMENT INFO
            $data["document"]["type"]           = DOC_TYPE__MCP;
            $data["document"]["package_id"]     = $_REQUEST["kit_id"];
            $data["document"]["package_type"]   = UNS_PACKAGE_TYPE__PN;
            $data["document"]["object_from"]    = $_REQUEST["object_from"];
            $data["document"]["object_to"]      = 18; // Сб.уч.

            // DOCUMENT ITEMS
            $data["document_items"][] = array(
                "item_id"           => $_REQUEST["detail_id"],
                "item_type"         => "D",
                "quantity"          => $_REQUEST["quantity"],
                "processing"        => "C",
            );

            $id = fn_uns__upd_document(0, $data);
        }
        $suffix = "update&kit_id={$_REQUEST["kit_id"]}" . "#document_$id";
    }

    //**************************************************************************
    // KIT_MOTIONS
    //**************************************************************************
    if ($mode == "motion" and $action == "update"){
        $id = fn_uns__upd_document($_REQUEST["document_id"], $_REQUEST["motion"]);
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        fn_delete_notification('changes_saved');
        $suffix = "update&kit_id={$_REQUEST["kit_id"]}" . "#document_$id";
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
    $p = array();
    $p = array(
        "with_details"      => true,
        "sorting_schemas"   => "view_1",
        "with_doc_type_VN"  => true,
        "status"            => $_REQUEST["status"],
    );
    $p = array_merge($_REQUEST, $p);
    list($kits, $search) = fn_acc__get_kits($p, UNS_ITEMS_PER_PAGE);
    $view->assign('kits', $kits);
    $view->assign('search', $search);

    // Насосы
    list($pumps) = fn_uns__get_pumps();
    $view->assign('pumps', $pumps);

    // Серии насосов
    list($pump_series) = fn_uns__get_pump_series(array('only_active' => true,'group_by_types'=>true,));
    $view->assign('pump_series', $pump_series);

}



if($mode == 'add'){
    //PUMPS
    list($pumps) = fn_uns__get_pumps(array("group_by_series"=>true));
    $view->assign('pumps', $pumps);
}

if($mode == 'update'){
    if(!is__more_0($_REQUEST['kit_id']) or !is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT kit_id FROM ?:_acc_kits WHERE kit_id = ?i", $_REQUEST['kit_id']))){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }
    $p = array(
        "with_details" => true,
    );
    $p = array_merge($_REQUEST, $p);
    $kit = array_shift(array_shift(fn_acc__get_kits($p)));
    $view->assign('kit', $kit);

    // DOCUMENTS ***************************************************************
    $p = array();
    $p["package_id"]    = $_REQUEST['kit_id'];
    $p["package_type"]  = UNS_PACKAGE_TYPE__PN;
    $p["with_items"]                = true;
    $p["get_info_objects"]          = true;
    $p["get_info_document_type"]    = true;
    $p["info_category"]             = true;
    $p["info_item"]                 = false;
    $p["info_unit"]                 = false;
    list($documents) = fn_uns__get_documents($p);
    $view->assign("documents", $documents);
//    fn_print_r($documents);

    // BALANCE *****************************************************************
    if (is__array($kit["details"])){
        $p = array();
        list ($p['time_from'], $p['time_to']) = fn_create_periods($p);
        $p["detail_id"] = $p["item_id"] = array_keys($kit["details"]);
        $p["period"]             = "A";
        $p["check_dcat_id"]      = false;
        $p["su"]["package_id"]   = $_REQUEST['kit_id'];
        $p["su"]["package_type"] = UNS_PACKAGE_TYPE__PN;
        list($balances, $search) = fn_uns__get_balance_mc_sk_su($p, true, true, true);
        $view->assign('balances', $balances);
//    fn_print_r($balances);
    }

    // CATEGORIES **************************************************************
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true, 'with_q_ty' => false, 'mcat_include_target' => ''));
    $view->assign('mcategories_plain', $mcategories_plain);
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true, "view_in_reports" => true));
    $view->assign('dcategories_plain', $dcategories_plain);

    //PUMPS ********************************************************************
    list($pumps) = fn_uns__get_pumps(array("group_by_series"=>true));
    $view->assign('pumps', $pumps);

    list($pumps_simple) = fn_uns__get_pumps();
    $view->assign('pumps_simple', $pumps_simple);

}


if($mode == 'delete'){
    if (is__more_0($_REQUEST["kit_id"])){
        fn_uns__del_kit($_REQUEST['kit_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


//**************************************************************************
// KIT_DETAILS
//**************************************************************************
if ($mode == "detail_update"){
    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true, "view_in_reports" => true));
    $view->assign('dcategories_plain', $dcategories_plain);

    if(is__more_0($_REQUEST['pd_id']) and is__more_0($_REQUEST['kit_id']) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT pd_id FROM ?:_acc_kit_details WHERE pd_id = ?i AND kit_id = ?i", $_REQUEST['pd_id'], $_REQUEST['kit_id']))){
        $p = array();
        $p["full_info"] = true;
        $p = array_merge($p, $_REQUEST);
        list($detail) = fn_acc__get_kit_details($p);
        $detail = array_shift(array_shift($detail));
        $view->assign('detail', $detail);
    }
}

if ($mode == "m_detail_update"){
    if (!is__more_0($_REQUEST["kit_id"])) return false;
    $kit = array_shift(array_shift(fn_acc__get_kits(array("kit_id"=>$_REQUEST["kit_id"]))));
    $set = fn_uns__get_packing_list_by_pump($kit["p_id"], "D", true);
    $pump = array_shift(array_shift(fn_uns__get_pumps(array("p_id"=>$kit["p_id"]))));
    list($details) = fn_uns__get_details(array("detail_id"=>array_keys($set), "with_material_info" => true));
    foreach ($details as $k=>$v){
        $details[$k] = array_merge($details[$k], $set[$k]);
    }
    $view->assign("details", $details);
    $view->assign("kit", $kit);
    $view->assign("set", $set);
    $view->assign("pump", $pump);
}


if($mode == 'detail_delete'){
    if (is__more_0($_REQUEST["kit_id"]) and is__more_0($_REQUEST["detail_id"])){
        fn_acc__del_kit_details($_REQUEST["kit_id"], $_REQUEST['detail_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, "$controller.update&kit_id={$_REQUEST["kit_id"]}");
}


//**************************************************************************
// KIT_MOTIONS
//**************************************************************************
if ($mode == "motion_delete"){
    if (is__more_0($_REQUEST["kit_id"]) and is__more_0($_REQUEST["document_id"])){
        fn_uns__del_document($_REQUEST['document_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, "$controller.update&kit_id={$_REQUEST["kit_id"]}" . "#motions");
}

if ($mode == "motion"){
    if (is__more_0($_REQUEST["kit_id"]) and is__more_0($_REQUEST["document_type"])){
        $kit = array_shift(array_shift(fn_acc__get_kits(array("kit_id"=>$_REQUEST["kit_id"], "with_details"=>true,))));
        $view->assign("kit", $kit);
//        fn_print_r($kit);
        list($pumps) = fn_uns__get_pumps(array("group_by_series"=>true));
        $view->assign('pumps', $pumps);


        if ($action == "add"){
            $view->assign("document_type", $_REQUEST["document_type"]);
        }elseif ($action == "update"){
            if (is__more_0($_REQUEST["document_id"])){

                $p = array();
                $p["document_id"] = $_REQUEST["document_id"];
                $p["with_items"]                = true;
                $p["get_info_objects"]          = true;
                $p["get_info_document_type"]    = true;
                $p["info_category"]             = true;
                $p["info_item"]                 = false;
                $p["info_unit"]                 = false;
                $motion = array_shift(array_shift(fn_uns__get_documents($p)));
                $view->assign("motion", $motion);
//                fn_print_r($motion);
            }
        }
    }
}


function fn_uns_kits__search ($controller){
    $params = array(
        'period',
        'time_from',
        'time_to',
        'ps_id',
        'status',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}


