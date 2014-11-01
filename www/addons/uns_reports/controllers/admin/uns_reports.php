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
    return false;
}

if ($mode == 'manage'){
    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "D"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);

    list($pumps) = fn_uns__get_pumps(array("group_by_series"=>true));
    $view->assign('pumps', $pumps);
    $view->assign('search', $_REQUEST);

    $production_LC_date_from = strtotime(date("Y-m-", fn_parse_date($_REQUEST['time_to'])) . "1");
    $view->assign('production_LC_date_from', $production_LC_date_from);

}

if ($mode == 'get_report'){
    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "D"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    $view->assign('search', $_REQUEST);

    switch ($action){
        case "foundry":
            $data = array();
            // GET DATA
            // todo-uns выборку данных перенести в ф-ию fn_rpt__foundry()
            if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "LM"; // Текущий месяц
            list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);

            // ДОКУМЕНТЫ СОГЛАСНО УСЛОВИЯМ ВЫБОРКИ
            $p = array("with_weight_per_each_document" => true, "with_total_weight_all_documents" => true, "sorting_schemas"=>"view_asc");
            $p = array_merge($_REQUEST, $p);
            list($documents, $search) = fn_acc__get_report_VLC($p);

            // GENERATE_REPORT
            fn_rpt__foundry(array('documents'=>$documents, 'search'=>$search));
        break;
        case "sl":
            $p = array(
                "plain"         => true,
                "all"           => true,
                "o_id"          => array(8),  // Склад литья
                "item_type"     => "M",
                "add_item_info" => true,
                "view_all_position" => "Y",
                "mclass_id"     => 1,
                "with_weight"   => true,
            );

            if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
            list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
            $p = array_merge($_REQUEST, $p);

            list($balance, $search) = fn_uns__get_balance($p);
            $exclude_items = array(412, 414, 426);
            fn_rpt__sl(array('period'=>$_REQUEST['period'], 'time_from'=>$_REQUEST['time_from'], 'time_to'=>$_REQUEST['time_to'], 'balance'=>$balance, 'exclude_items'=>$exclude_items));
        break;

        case "accounting":
            $p = array(
                "plain"             => true,
                "all"               => true,
                "o_id"              => array(8),  // Склад литья
                "item_type"         => "M",
                "add_item_info"     => true,
                "view_all_position" => "Y",
                "mclass_id"         => 1,
                "with_weight"       => true,
                "accessory_pumps"   => "Y",
                "sorting_schemas"   => "mcat_position_accounting",
            );

            if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
            list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);

            // Запросить категории заготовок склада литья разрешенные к отображению
            $_REQUEST["mcat_id"] = array_keys(array_shift(fn_uns__get_materials_categories(array("view_in_reports" => true, "only_active"=>true, "mcat_id"=>27, "sorting_schemas"=>"mcat_position_accounting",))));

            $p = array_merge($_REQUEST, $p);
            list($balance, $search) = fn_uns__get_balance($p);
            $exclude_items = array(412, 414, 426, // кольца сальника из болванок
                428, // крышка патрубка 10
                226, // крышка улитки 9

                490, // Кольцо сальника [III]
                523, // Кольцо сальника [Д8]
                524, // Кольцо уплотняющее [IIII]
                //529, // Крышка подшипника [Д8]
                //530, // Крышка подшипника [Д8]
                527, // Крышка подшипника пяты [Д8]
                528, // Крышка сальника [Д8]
                //525, // Стакан подшипника [Д8]

                437, // Кольцо сальника [б/к]

                244, // ПАТРУБОК  [25]
                186, //  Корпус подшипников [17]

            );

            // Правила отображения
            /*
            N   - name
            NA  - name accounting
            P   - список насосов
            PS  - список серий наосов
            */
            $rules = array(
                "mcat_id"       => array(
                    75 => "N", // ДЕТАЛИ ЦНС
                    77 => "N", // ДЕТАЛИ СД, СДВ
                    74 => "N", //
                    76 => "N", //
                    28 => "N", //
                    67 => "N", //
                    78 => "N", //
                    79 => "N", //
                    80 => "N", //
                ),
                "material_id"   => array(
                    425 => "NA",  // Опора 2СМ200-150-500; 2СМ250-200-400
                    183 => "P",
                    192 => "P",
                    323 => "P",
                    324 => "P",
                    328 => "P",
                    209 => "P",
                    125 => "P",
                    135 => "P",
                    160=> "P",
                    172=> "P",
                    180=> "P",
                    276=> "P",
                    288=> "P",
                    159=> "P",
                    162=> "P",
                    164=> "P",
                    341=> "P",
                    229=> "P",
                    230=> "P",
                    240=> "P",
                    243=> "P",
                    143=> "P",
                    229=> "NA",
                    230=> "NA",
                ),
            );
            fn_rpt__accounting(array('period'=>$_REQUEST['period'], 'time_from'=>$_REQUEST['time_from'], 'time_to'=>$_REQUEST['time_to'], 'balance'=>$balance, 'exclude_items'=>$exclude_items, 'rules'=>$rules));
        break;


        case "mc":
            if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
            list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
            $balances = array();
            $_REQUEST["dcat_id"] = array_keys(array_shift(fn_uns__get_details_categories(array("view_in_reports" => true, "only_active"=>true))));
//            $_REQUEST["dcat_id"] = array(7,6,4,2,35,15,9,8,38,36,5,13,14,20,26,37,24,28,40);
            $_REQUEST["accessory_pumps"] = "Y";
            list($balances, $search) = fn_uns__get_balance_mc_sk_su($_REQUEST, true, true, true);
            // Запрос категорий
            list($dcategories_plain) = fn_uns__get_details_categories(array("plain" => true, "with_q_ty"=>false, "view_in_reports" => true));

            fn_rpt__mc(array(   'period'        =>$_REQUEST['period'],
                                'time_from'     =>$_REQUEST['time_from'],
                                'time_to'       =>$_REQUEST['time_to'],
                                'balance'       =>$balances,
                                'exclude_items' =>$exclude_items,
                                'rules'         =>$rules,
                                'as_blank'      =>$_REQUEST['as_blank']
                ));

        break;

        case "test":
            // 1/ Выборка серий насосов
//            $p = array("ps_id" => array(
//                78,
//                79,
//                80,
//                29,
//                37,
//                77,
//                76,
//                65,
//                66,
//                94,
//                    78
//                78, 79, 80, 29, 37, 77, 76, 65, 66, 36, 39, 48, 40, 58, 62, 52,
//                63, 68, 75, 74, 69, 38, 42, 51, 47, 61, 57, 64, 30, 31, 60, 67,
//                70, 88, 89, 90, 92, 91, 99, 94, 72, 71, 93, 106, 84, 105, 100,
//                95, 97, 73, 85, 87, 83, 86, 82, 107, 101, 102, 108, 103, 104, 109, 110,
//            ));
            if (is__more_0($_REQUEST["ps_id"])){
                $p = array("ps_id" => $_REQUEST["ps_id"]);
                if (is__array($pump_series = array_shift(fn_uns__get_pump_series($p)))){
                    foreach ($pump_series as $k_ps=>$v_ps){
                        if (is__array($pumps = array_shift(fn_uns__get_pumps(array("ps_id" => $k_ps, "only_active"=>true))))){
                            foreach ($pumps as $k_p=>$v_p){
                                $list_details = fn_uns__get_packing_list_by_pump($k_p, "D", true);
                                $p_details = array_shift(fn_uns__get_details(array(
                                                    "detail_id" => array_keys($list_details),
                                                    "with_materials" => true,
                                                    "with_accessory_pumps" => true,
                                                )));
                                if (is__array($p_details)){
                                    foreach ($p_details as $k_p_details=>$v_p_details){
                                        $p_details[$k_p_details]["quantity"] = $list_details[$k_p_details]["quantity"];
                                    }
                                }

                                $pumps[$k_p]["details"] = $p_details;
                            }
                            $pump_series[$k_ps]["pumps"] = $pumps;
                        }
                    }
                }
                fn_rpt__test(array("ps"=>$pump_series));
            }
        break;

        // Отчет работы предприятия
        case "general_report":
            if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "LM"; // Текущий месяц
            list ($_REQUEST['time_from'],    $_REQUEST['time_to']) = fn_create_periods($_REQUEST);

            // 1. Выпуск Литейного цеха
            $total_weight_VLC = null;
            $p = array("with_weight_per_each_document" => true, "with_total_weight_all_documents" => true, "sorting_schemas"=>"view_asc");
            list($report_VLC) = fn_acc__get_report_VLC(array_merge($_REQUEST, $p));

            if ($_REQUEST["production_LC_from_the_beginning_of_the_month"] == "Y"){
                $production_LC_date_from = strtotime(date("Y-m-", fn_parse_date($_REQUEST['time_to'])) . "1");
                $p = array(
                    "with_total_weight_all_documents"   => true,
                    "time_from"                         => $production_LC_date_from,
                    "time_to"                           => $_REQUEST['time_to'],
                );
                list(, $s) = fn_acc__get_report_VLC(array_merge($_REQUEST, $p));
                $total_weight_VLC = $s['total_weight'];
            }


            // 2. Продажа отливок со Склада литья
            $p = array("type" => 7, "o_id" => 8, "only_active" =>  true, "with_items" =>  true, "info_unit"=>false, "info_item" => false, "sorting_schemas" => "view_asc"); // RO = 7; Sklad Litya = 8
            list($sales_VLC) = fn_uns__get_documents(array_merge($_REQUEST, $p));

            // 3.0.0 Список всех насосов
            // 3.0.1 Список всех серий насосов
            $pumps = array_shift(fn_uns__get_pumps(array("without_sets_of_details"=>true, )));
            $pump_series = array_shift(fn_uns__get_pump_series());


            // 3. Выпуск насосной продукции
            $p = array("type" => 13, "o_id" => 19, "only_active" =>  true, "with_items" =>  true, "info_unit"=>false, "info_item" => false, "sorting_schemas" => "view_asc"); // RO = 7; Sklad Litya = 8
            list($vn_SGP) = fn_uns__get_documents(array_merge($_REQUEST, $p));
            $vn_SGP_groups = null;
            $vn_SGP_groups_weight = null;
            foreach ($vn_SGP as $doc){
                foreach ($doc["items"] as $item){
                    if (in_array($item["item_type"], array("P", "PF", "PA")) and is__more_0($item["quantity"]) and is__array($pumps[$item["item_id"]])){
                        $vn_SGP_groups[$pumps[$item["item_id"]]["ps_id"]] += $item["quantity"];
                        $vn_SGP_groups_weight[$pumps[$item["item_id"]]["ps_id"]] += $item["quantity"]*$pumps[$item["item_id"]]["weight_p"];
                    }
                }
            }

            // 4.0. Список клиентов
            $customers = array_shift(fn_uns__get_customers(array("only_active"=>true,)));

            // 4. Продажа насосной продукции (19 - сгп александрия; 25 - сгп днепропетровск)
            $p = array("type" => 7, "o_id" => array(19, 25), "only_active" =>  true, "with_items" =>  true, "info_unit"=>false, "info_item" => false, "sorting_schemas" => "view_order_by_customer",); // RO = 7; СГП = 8
            list($sales_SGP) = fn_uns__get_documents(array_merge($_REQUEST, $p));
            $sales_SGP_groups = null;
            $sales_SGP_groups_weight = null;
            $sales_SGP_details = null;
            foreach ($sales_SGP as $doc){
                foreach ($doc["items"] as $item){
                    if (in_array($item["item_type"], array("P", "PF", "PA")) and is__more_0($item["quantity"]) and is__array($pumps[$item["item_id"]])){
                        $ps_id          = $pumps[$item["item_id"]]["ps_id"];
                        $customer_id    = $doc["customer_id"];
                        if ($customers[$customer_id]["to_export"]=="N"){
                            $customer_id = 1; // Принудительно все продажи по Украине как Клиент id=1
                        }
                        $object         = $doc["object_to"];
                        $weight         = ($item["item_type"] == "P")?$pumps[$item["item_id"]]["weight_p"]:$pumps[$item["item_id"]]["weight_pf"];
                        $sales_SGP_groups        [$object][$customer_id][$ps_id] += $item["quantity"];
                        $sales_SGP_groups_weight [$object][$customer_id][$ps_id] += $item["quantity"]*$weight;
                        $sales_SGP_groups_counter[$ps_id] += $item["quantity"];
                    }elseif ($item["item_type"] == "D" and is__more_0($item["quantity"])){
                        $sales_SGP_details[$item["item_id"]] += $item["quantity"];
                    }
                }
            }

            // Анализ проданных деталей
            if (is__array($sales_SGP_details)){
                $details = array_shift(fn_uns__get_details(array("detail_id"=>array_keys($sales_SGP_details), "with_accounting" => true, )));
                $list_details =  null;
                foreach ($details as $detail_id=>$d){
                    $list_details[$d["dcat_name"]][$detail_id] = array(
                        "name"   => $d["detail_name"] . " " . $d["detail_no"],
                        "sold"   => $sales_SGP_details[$detail_id],
                        "weight" => $d["accounting_data"]["weight"]["M"],
                    );
                }
                $sales_SGP_details = $list_details;
            }

            list($customers) = fn_uns__get_customers(array('status'=>'A'));

            fn_rpt__general_report(array("report_VLC"=>$report_VLC, "total_weight_VLC"=>$total_weight_VLC, "production_LC_date_from"=>$production_LC_date_from, "sales_VLC"=>$sales_VLC, "vn_SGP"=>$vn_SGP, "vn_SGP_groups"=>$vn_SGP_groups, "vn_SGP_groups_weight"=>$vn_SGP_groups_weight, "sales_SGP"=>$sales_SGP, "sales_SGP_groups"=>$sales_SGP_groups, "sales_SGP_groups_weight"=>$sales_SGP_groups_weight, "sales_SGP_groups_counter"=>$sales_SGP_groups_counter, "sales_SGP_details"=>$sales_SGP_details, "customers"=>$customers, "pump_series"=>$pump_series, "pumps"=>$pumps));
        break;

        case "planning_report":
            fn_rpt__planning_report();
        break;

    }
    exit;
}


function fn_uns_reports__search ($controller){
    $params = array(
        "time_from",
        "time_to",
        "production_LC_from_the_beginning_of_the_month",
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}

