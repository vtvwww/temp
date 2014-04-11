<?php

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    return array(CONTROLLER_STATUS_OK, $controller . "." . $mode);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
//    fn_uns_add_sections($controller);
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'manage'){
    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    if (!isset($_REQUEST["total_balance_of_details"])) $_REQUEST["total_balance_of_details"] = "Y";

    $balances = array();
    list($balances, $search) = fn_uns__get_balance_sgp($_REQUEST, true, true, true, true);
    $view->assign('balances_D',    $balances["D"]);

    $balances = fn_uns_balance_sgp__format_for_tmpl($balances);
    $view->assign('balances',    $balances);
//    fn_print_r($balances);

    $view->assign('search',     $_REQUEST);
    $view->assign('expand_all', false);
//    fn_print_r($search);


    // Запрос ЗАКАЗОВ
    $p = array(
        "with_items"        => true,
        "full_info"         => true,
        "with_count"        => true,
        "only_active"       => true,
        "data_for_tmp"      => true,
        "remaining_time"    => true,
    );
    list($orders, $search) = fn_acc__get_orders(array_merge($_REQUEST, $p));
//    fn_print_r($orders);
    $view->assign('orders', $orders);

    // REGIONS
    list($regions) = fn_uns__get_regions();
    $view->assign('regions', $regions);

    // Запрос категорий
    list($dcategories_plain) = fn_uns__get_details_categories(array("plain" => true, "with_q_ty"=>false));
    $view->assign('dcategories_plain', $dcategories_plain);
    $view->assign('dcategories_plain_with_q_ty', false);

    // OBJECTS *****************************************************************
    list($objects_plain, $search) = fn_uns__get_objects(array('plain' => true, 'all'   => true));
    $view->assign('objects_plain', $objects_plain);
    $view->assign('enabled_objects', array(9,10,14,17));

}


if($mode == 'update'){
    if(!is__more_0($_REQUEST['o_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $o_id = $_REQUEST['o_id'];
    $p       = array('item_ids' => $o_id);
    list($object) = fn_uns__get_objects($p);
    $view->assign('object', array_shift($object));

    // Список для выпадающего списка
    list($objects_plain) = fn_uns__get_objects(array('plain' => true));
    $view->assign('objects_plain', $objects_plain);
}



// ПРОСМОТР ИСТОРИИ ДВИЖЕНИЯ
if (defined('AJAX_REQUEST') and  $mode == 'motion'){
    if (!is__more_0($_REQUEST['item_id'])){
        fn_set_notification('W', 'Ошибка запроса данных!', 'Обратитесь к администратору системы');
        return false;
    }
    $p = array(
        "o_id"          => 19,  // Склад литья
        "item_type"     => array("P", "PF", "PA"),
        "typesize"      => "M",
        "item_id"       => $_REQUEST['item_id'],
    );

    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);

    $p = array_merge($_REQUEST, $p);

    // ДВИЖЕНИЯ
    $motions = fn_uns__get_motions($p);
    $view->assign('motions', $motions);

    // ОТСТАКИ
    $p = array(
        "plain"             => true,
        "all"               => true,
        "item_type"         => $_REQUEST['item_type'],
        "item_id"           => $_REQUEST['item_id'],
        "add_item_info"     => false,
    );
    $p = array_merge($_REQUEST, $p);
    list($balances) = fn_uns__get_balance_sgp($p, true, true, true, false);
    $view->assign("balances", $balances);
    $view->assign("item_id", $_REQUEST["item_id"]);

    // ТИПЫ ДОКУМЕНТОВ
    list($document_types) = fn_uns__get_document_types();
    $view->assign('document_types', $document_types);

    $view->assign('params', $p);

}


function fn_uns_balance_sgp__search($controller) {
    $params = array(
        'period',
        'time_from',
        'time_to',

        'item_type',
        'dcat_id',
        'type_casting',
        'detail_name',
        'detail_no',
        'accessory_pumps',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}

// переформатирование данных для простого отобраджения view
function fn_uns_balance_sgp__format_for_tmpl($b) {
    $res = null;
//    fn_print_r($res);

    $p = array("only_active"=>true);

    // Типы насосов
    list($pump_types)   = fn_uns__get_pump_types($p);

    // Серии насосов
    list($pump_series)  = fn_uns__get_pump_series($p);

    // Насосы
    list($pumps)        = fn_uns__get_pumps($p);

    // Скомпоновать ТИПЫ
    foreach ($pump_types as $pt){
        $res[$pt["pt_id"]]["pt_id"]     = $pt["pt_id"];
        $res[$pt["pt_id"]]["pt_name"]   = $pt["pt_name"];
    }

    // Скомпоновать СЕРИИ НАСОСОВ
    foreach ($pump_series as $ps){
        $res[$ps["pt_id"]]["pump_series"][$ps["ps_id"]]["pt_id"]    = $ps["pt_id"];
        $res[$ps["pt_id"]]["pump_series"][$ps["ps_id"]]["ps_id"]    = $ps["ps_id"];
        $res[$ps["pt_id"]]["pump_series"][$ps["ps_id"]]["ps_name"]  = $ps["ps_name"];
    }

    // Скомпоновать НАСОСЫ
    foreach ($pumps as $p){
        $res[$p["pt_id"]]["pump_series"][$p["ps_id"]]["pumps"][$p["p_id"]]["pt_id"]     = $p["pt_id"];
        $res[$p["pt_id"]]["pump_series"][$p["ps_id"]]["pumps"][$p["p_id"]]["ps_id"]     = $p["ps_id"];
        $res[$p["pt_id"]]["pump_series"][$p["ps_id"]]["pumps"][$p["p_id"]]["p_id"]      = $p["p_id"];
        $res[$p["pt_id"]]["pump_series"][$p["ps_id"]]["pumps"][$p["p_id"]]["p_name"]    = $p["p_name"];
    }

    // Получить заказы
    // Запрос ЗАКАЗОВ
    $p = array(
        "with_items"        => true,
        "only_active"       => true,
    );
    list($orders, $search) = fn_acc__get_orders(array_merge($_REQUEST, $p));
//    fn_print_r($orders);

    // Скомпоновать остатки и заказы
    foreach ($res as $k_pt=>$v_pt){
        foreach ($v_pt["pump_series"] as $k_ps=>$v_ps){
            foreach ($v_ps["pumps"] as $k_p=>$v_p){
                // Проход по каждому балансу
                foreach ($b as $k_item_type=>$v_item_type){
                    if (in_array($k_item_type, array("P", "PF", "PA"))){
                        foreach ($v_item_type as $k_group=>$v_group){
                            foreach ($v_group["items"] as $k_item=>$v_item){
                                if ($k_item == $k_p){
                                    $res[$k_pt]["pump_series"][$k_ps]["pumps"][$k_p]["balances"][$k_item_type] = $v_item["konech"];
                                }
                            }
                        }
                    }
                }

                // Проход по каждому заказу, если они есть
                if (is__array($orders)){
                    foreach ($orders as $k_o=>$v_o){
                        foreach ($v_o["items"] as $i){
                            if (in_array($i["item_type"], array("P", "PF", "PA")) and $i["p_id"] == $k_p){
                                $res[$k_pt]["pump_series"][$k_ps]["pumps"][$k_p]["orders"][$k_o][$i["item_type"]] = $i["quantity"];
//                                $res[$k_pt]["pump_series"][$k_ps]["pumps"][$k_p]["orders"][$k_o][$i["item_type"]] = $i["quantity"];
                            }
                        }
                    }
                }
            }
        }
    }


    // Убрать с нулевыми значениями
    $balance = array();
    foreach ($res as $k_pt=>$v_pt){
        foreach ($v_pt["pump_series"] as $k_ps=>$v_ps){
            foreach ($v_ps["pumps"] as $k_p=>$v_p){
                $sum_orders = fn_uns_balance_sgp__sum_orders($v_p["orders"]);
                if (!array_sum($v_p["balances"]) and !$sum_orders){
                    unset($res[$k_pt]["pump_series"][$k_ps]["pumps"][$k_p]);
                }
            }
        }
    }

    foreach ($res as $k_pt=>$v_pt){
        foreach ($v_pt["pump_series"] as $k_ps=>$v_ps){
            if (!count($v_ps["pumps"])){
                unset($res[$k_pt]["pump_series"][$k_ps]);
            }
        }
    }

    foreach ($res as $k_pt=>$v_pt){
        if (!count($v_pt["pump_series"])){
            unset($res[$k_pt]);
        }
    }

//    fn_print_r($res);
    return $res;
}

function fn_uns_balance_sgp__sum_orders ($arr){
    $res = 0;
    if (is__array($arr)){
        foreach ($arr as $a){
            $res += array_sum($a);
        }
    }
    return $res;
}
