<?php

if(!defined('AREA')){
    die('Access denied');
}

// Запрос продаж по серии за период
function fn_uns__get_sales_pump_series_by_period ($ps_id, $begin, $end){
    $res = array();
    if (fn_is_not_empty($ps_id) and is__more_0($begin, $end)){
        list($pumps) = fn_uns__get_pumps(array("only_active"=>true, ));
        $params = array(
            "time_from"     => $begin,
            "time_to"       => $end,
            "type"          => 7,           // Расходные ордера
            "o_id"          => 19,          // Склад готовой продукции
            "only_active"   => true,
            "item_type"     => array("P", "PF", "PA"),
            "with_items"    => true,
            "info_category" => false,
            "info_item"     => true,
            "info_unit"     => false,
        );
        list($docs) = fn_uns__get_documents($params);
        if(!is__array($docs)) return null;

        $ps_id = to__array($ps_id);

        foreach ($docs as $d){
            if (is__array($d["items"])){
                foreach ($d["items"] as $i){
                    if (in_array($pumps[$i["item_id"]]["ps_id"], $ps_id)){
                        $res[$pumps[$i["item_id"]]["ps_id"]] += $i["quantity"];
                    }
                }
            }
        }
    }
    return $res;

}