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
    list($pumps) = fn_uns__get_pumps(array("group_by_series"=>true));
    $view->assign('pumps', $pumps);
}

if ($mode == 'get_report'){
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
            $_REQUEST["dcat_id"] = array(7,6,4,2,35,15,9,8,38,36,5,13,14,20);
//            $_REQUEST["dcat_id"] = array(38);
            $_REQUEST["accessory_pumps"] = "Y";
            list($balances, $search) = fn_uns__get_balance_mc_sk_su($_REQUEST, true, true, true);
            // Запрос категорий
            list($dcategories_plain) = fn_uns__get_details_categories(array("plain" => true, "with_q_ty"=>false));

            fn_rpt__mc(array(   'period'        =>$_REQUEST['period'],
                                'time_from'     =>$_REQUEST['time_from'],
                                'time_to'       =>$_REQUEST['time_to'],
                                'balance'       =>$balances,
                                'exclude_items' =>$exclude_items,
                                'rules'         =>$rules));

        break;

        case "test":
            // 1/ Выборка серий насосов
            $p = array("ps_id" => array(
//                78,
//                79,
//                80,
//                29,
//                37,
//                77,
//                76,
//                65,
//                66,
                94,
            ));
            if (is__array($pump_series = array_shift(fn_uns__get_pump_series($p)))){
                foreach ($pump_series as $k_ps=>$v_ps){
                    if (is__array($pumps = array_shift(fn_uns__get_pumps(array("ps_id" => $k_ps))))){
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
        break;
    }
    exit;
}

if ($mode == 'get_blank'){
    switch ($action){
/*        case "planning_LC":
            if (is__more_0($_REQUEST['pump_id'])){
                $pump_materials = fn_uns__get_packing_list_by_pump($_REQUEST['pump_id']);
                fn_print_r($pump_materials);
                $p = $d = array();
                if (is__array($pump_materials)){
                    $p["material_id"] = array_keys($pump_materials);
                    $p["with_accounting"] = true;
                    list($materials) = fn_uns__get_materials($p);
                    if (is__array($materials)){
                        foreach ($materials as $k=>$v){
                            $d[] = array("material_id"  => $pump_materials[$k]["material_id"],
                                         "quantity"     => $pump_materials[$k]["quantity"],
                                         "info"         => $materials[$k]);
                        }
                    }
                }
                $pump = array_shift(array_shift(fn_uns__get_pumps(array('p_id'=>$_REQUEST['pump_id']))));
                fn_rpt__planning_LC(array('items'=>$d, 'pump'=>$pump));
            }

        break;*/
        case "planning_LC":
            $p_ids = array(
                1,
                15,
                14,
                17,
                79,
                19,
                44,
                37,
                54,
                75,
                73,
                57,
                16,
                22,
                25,
                43,
                2,
                8,
                31,
                51,
                108,
                114,
                120,
                138,
                126,
                135,
                66,
                67,
                132,
                166,
                94,
                150,
                153,
                156,
                70,
                97,
                91,
                99,
                103,
                84,
                85,
                176,
            );

            $pumps_data = array();
            foreach ($p_ids as $p_id){
                $pump_materials = fn_uns__get_packing_list_by_pump($p_id);
                $p = $d = array();
                if (is__array($pump_materials)){
                    $p["material_id"] = array_keys($pump_materials);
                    $p["with_accounting"] = true;
                    list($materials) = fn_uns__get_materials($p);
                    if (is__array($materials)){
                        foreach ($materials as $k=>$v){
                            $d[] = array("material_id"  => $pump_materials[$k]["material_id"],
                                         "quantity"     => $pump_materials[$k]["quantity"],
                                         "info"         => $materials[$k]);
                        }
                    }
                }
                $pump = array_shift(array_shift(fn_uns__get_pumps(array('p_id'=>$p_id))));
                $pumps_data[] = array('items'=>$d, 'pump'=>$pump);
            }

            fn_rpt__planning_LC($pumps_data);
        break;
    }
    exit;
}
