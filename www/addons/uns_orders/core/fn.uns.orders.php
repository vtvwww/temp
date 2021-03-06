<?php

function fn_acc__get_orders($params = array(), $items_per_page = 0){
    $default_params = array(
        'order_id' => 0,
        'info_RO'     =>false,
        'full_info'     =>false,
        'page' => 1,
        'only_active'=>false,
        'limit' => 0,
        'sorting_schemas' => 'view',
        'date_finished_begin'   =>0,
        'date_finished_end'     =>0,

    );

    $params = array_merge($default_params, $params);

    $m_tbl = "?:_acc_orders";
    $m_key = "order_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.comment",
        "$m_tbl.status",
        "$m_tbl.date_updated",
        "$m_tbl.date_finished",
        "$m_tbl.customer_id",
        "$m_tbl.country_id",
        "$m_tbl.region_id",
        "$m_tbl.city_id",
        "$m_tbl.no_1s",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.status"  => "asc",
            "$m_tbl.date_finished"  => "desc",
        ),
        "view_in_sgp" => array(
//            "$m_tbl.country_id"  => "asc",
            "$m_tbl.date_finished"  => "asc",
        ),
    );

    $condition = $limit = $join = $group_by = $sorting = '';

    // *************************************************************************
    // 1. УСЛОВИЯ ОТБОРА
    // *************************************************************************
    // По ID
    if ($params["{$m_key}_array"] = to__array($params[$m_key])){
        $condition .= db_quote(" AND $m_tbl.$m_key in (?n)", $params["{$m_key}_array"]);
    }

    // customer_id
    if ($params["customer_id_array"] = to__array($params["customer_id"])){
        $condition .= db_quote(" AND $m_tbl.customer_id in (?n)", $params["customer_id_array"]);
    }

    if ($params['region_id_array'] = to__array($params['region_id'])) {
        $condition .= db_quote(" AND $m_tbl.region_id in (?n)", $params['region_id_array']);
    }

    if ($params['country_id_array'] = to__array($params['country_id'])) {
        $condition .= db_quote(" AND $m_tbl.country_id in (?n)", $params['country_id_array']);
    }

    if (is__more_0($params["date_finished_begin"], $params["date_finished_end"])){
        $condition .= db_quote(" AND $m_tbl.date_finished between ?i AND ?i ", $params["date_finished_begin"], $params["date_finished_end"]);
    }

    if ($params['status']) {
        $condition .= db_quote(" AND $m_tbl.status = ?s ", $params['status']);
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND $m_tbl.status in ('Open','Paid') ");
    }

    // *************************************************************************
    // 2. ПРИСОЕДИНИТЬ ТАБЛИЦЫ
    // *************************************************************************
//    $join .= db_quote(" LEFT JOIN $j_tbl_1 ON ($j_tbl_1.$j_key_1  = $m_tbl.$j_key_1) ");


    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }

    if (!empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(*) FROM $m_tbl $join WHERE 1 $condition");
        $limit = fn_paginate($params["page"], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])){
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $sql = UNS_DB_PREFIX . "SELECT " . implode(", ", $fields) . " FROM $m_tbl $join WHERE 1 $condition $sorting $limit";
    $data = db_get_hash_array($sql, $m_key);
    //  fn_print_r(str_replace(array(UNS_DB_PREFIX,"?:"), array("", "uns_"), $sql));
    //  $data = fn_group_data_by_field($data, 'task_id');
    if (!is__array($data)) return array(array(), $params);

    // *************************************************************************
    // 6. ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ
    // *************************************************************************
    //foreach ($data as $k_data=>$v_data){}

    if ($params['with_items']){
        $p = array(
            "order_id"                 =>array_keys($data),
            "full_info"                =>$params["full_info"],
            "info_RO"                  =>$params["info_RO"],
            "item_type"                =>$params["item_type"],
            "item_id"                  =>$params["item_id"],
            "without_shipped_items"    =>$params["without_shipped_items"],
        );
        list($items) = fn_acc__get_order_items($p);
        if (is__array($items)){
            foreach ($data as $k_d=>$v_d){
                $data[$k_d]["items"] = $items[$k_d];
            }
        }

        // подготовка общего кол-ва данных для smarty
        if ($params["data_for_tmp"]){
            if (is__array($items)){
                foreach ($items as $k_o=>$v_o){
                    foreach ($v_o as $i){
                        $data[$k_o]["data_for_tmp"][$i["item_type"]][$i["item_id"]]["quantity"] += $i["quantity"];
                        $data[$k_o]["data_for_tmp"][$i["item_type"]][$i["item_id"]]["comment"] = $i["comment"];
                    }
                }
            }
        }
    }

    // Объединить заказы по Украине
    if ($params['group_orders'] == "UKR"){
        $customers_of_ukr = array_shift(fn_uns__get_customers(array("to_export"=>"N",))); // Клиенты только по Украине
        $customers_of_ukr_keys = array_keys($customers_of_ukr);
        $data_temp["ukr"]["order_id"]   = "ukr";
        $data_temp["ukr"]["customer_id"]= "ukr";
        $data_temp["ukr"]["items"]      = array();
        foreach ($data as $k_d=>$v_d){
            if (fn_is_not_empty($v_d["items"])){
                if (in_array($v_d["customer_id"], $customers_of_ukr_keys)/*  and count($v_d["items"])*/){
                    // Клиент принадлежит Украине
                    $data_temp["ukr"]["items"] = array_merge($data_temp["ukr"]["items"], $v_d["items"]);
                    foreach ($v_d["items"] as $i){
                        $data_temp["ukr"]["data_for_tmp"][$i["item_type"]][$i["item_id"]]["quantity"] += $i["quantity"];
                    }
                    $data_temp["ukr"]["order_list"][$k_d] =  array(
                        "order_id"      => $k_d,
                        "status"        => $v_d["status"],
                        "comment"       => $v_d["comment"],
                        "customer_id"   => $v_d["customer_id"],
                    );
                }else{
                    $data_temp[$k_d] = $v_d;
                }
            }
        }
        if (count($data_temp["ukr"]["items"])){
            $data = $data_temp;
        }
    }

    if ($params["total_weight_and_quantity"]){
        $weight     = array("pumps"=>0, "details"=>0,);
        $quantity   = array("pumps"=>0, "details"=>0,);
        foreach ($data as $order_id=>$order_data){
            if (is__array($order_data["items"])){
                foreach ($order_data["items"] as $i){
                    if ($i["item_type"] == "D"){
                        $data[$order_id]["quantity"]["details"] += $i["quantity"];
                        $data[$order_id]["weight"]  ["details"] += $i["quantity"]*$i["weight"];
                    }elseif (in_array($i["item_type"], array("P", "PF", "PA"))){
                        $data[$order_id]["quantity"]["pumps"]   += $i["quantity"];
                        $data[$order_id]["weight"]  ["pumps"]   += $i["quantity"]*$i["weight"];
                    }
                }
            }
        }
        $sql = db_quote(UNS_DB_PREFIX . "SELECT sum(weight*quantity) as total_weight, sum(quantity) as total_quantity, order_id FROM ?:_acc_order_items WHERE order_id IN (?n) GROUP BY order_id", array_keys($data));
        $totals = db_get_hash_array($sql, "order_id");
        foreach ($data as $k_d=>$v_d){
            $data[$k_d]["total_weight"]     = fn_fvalue($totals[$k_d]["total_weight"]);
            $data[$k_d]["total_quantity"]   = fn_fvalue($totals[$k_d]["total_quantity"]);
        }
    }

    if ($params["with_count"]){
        $sql = db_quote(UNS_DB_PREFIX . "SELECT count(oi_id) as count, order_id FROM ?:_acc_order_items WHERE order_id IN (?n) GROUP BY order_id", array_keys($data));
        $count_data = db_get_hash_array($sql, "order_id");
        foreach ($data as $k_d=>$v_d){
            $data[$k_d]["count"] = $count_data[$k_d]["count"];
        }
    }

    if ($params["remaining_time"]){
        foreach ($data as $k_d=>$v_d){
            $data[$k_d]["remaining_time"] = round(($v_d["date_finished"] - TIME)/(86400), 0);
        }
    }

    return array($data, $params);
}

function fn_acc__upd_order($id = 0, $data){
    $data = trim__data($data);
    if (!is__array($data) || !is_numeric($id) || $id < 0 ) return false;

    // Обновить информацию о ORDER
    $id = fn_acc__upd_order_info($id, $data['order']);

    // Обновить позиции
    fn_uns__upd_order_items($id, $data['document_items']);

    return $id;
}


function fn_uns__upd_order_items($order_id, $data){
    if (!is__more_0($order_id) or !is__array($data)) return false;
    $data = trim__data($data);
    $m_table = "?:_acc_order_items";
    $oi_ids = array();

    foreach ($data as $i){
        if (is__more_0($i['item_id']) and  is_numeric($i['quantity']) and fn_check_type($i['item_type'], UNS_ITEM_TYPES)){
            $v = array(
                'order_id'              => $order_id,
                'item_type'             => $i['item_type'],
                'item_id'               => $i['item_id'],
                'quantity'              => abs($i['quantity']),
                'comment'               => $i['comment'],
                'date'                  => fn_parse_date($i["date"]),
                'weight'                => (is__more_0(floatval($i['weight'])))?floatval($i['weight']):0,
            );

            if (is__more_0($i['oi_id']) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT oi_id FROM $m_table WHERE oi_id = ?i", $i['oi_id']))){
                // ОБНОВИТЬ
                db_query(UNS_DB_PREFIX . "UPDATE $m_table SET ?u WHERE oi_id = ?i", $v, $i['oi_id']);
                $oi_ids[] = $i['oi_id'];
            }elseif ($i['oi_id'] == 0){
                // ДОБАВЛЕНИЕ
                $oi_ids[] = db_query(UNS_DB_PREFIX . "INSERT INTO $m_table ?e", $v);
            }
        }
    }
    if (is__array($oi_ids)){
        // Удалить все лишнее
        db_query(UNS_DB_PREFIX . "DELETE FROM $m_table WHERE order_id = ?i AND oi_id not in (?n) ", $order_id, $oi_ids);
    }else{
        db_query(UNS_DB_PREFIX . "DELETE FROM $m_table WHERE order_id = ?i ", $order_id);
    }

    return true;
}



function fn_acc__upd_order_info($id, $data){
    if (is__more_0($id) and is__array($data) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT order_id FROM ?:_acc_orders WHERE order_id = $id"))){
        $operation = "update";
    }else{
        $operation = "add";
        $id = 0;
    }

    $data = trim__data($data);
    $d = array();
    $d["comment"]       = (strlen($data["comment"]))?$data["comment"]:"";
    $d["date_finished"] = fn_parse_date($data["date_finished"]);
    $d["status"]        = (fn_check_type($data["status"], "|Open|Close|Hide|Paid|Shipped|"))?$data["status"]:"Hide";
    $d["customer_id"]   = $data["customer_id"];
    $d["country_id"]    = $data["country_id"];
    $d["region_id"]     = $data["region_id"];
    $d["city_id"]       = $data["city_id"];
    $d["no_1s"]         = $data["no_1s"];
    $d["date_updated"]  = fn_parse_date($data["date_updated"]);;

    if ($operation == "update"){
        // ОБНОВИТЬ
        db_query(UNS_DB_PREFIX . "UPDATE ?:_acc_orders SET ?u WHERE order_id = ?i", $d, $id);
    }elseif ($operation == "add"){
        // ДОБАВИТЬ
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:_acc_orders ?e", $d);
    }
    return $id;
}

function fn_uns__del_order($id){
    if (!($id = to__array($id))) return false;
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_orders         WHERE order_id IN (?n)", $id);
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_order_items    WHERE order_id IN (?n)", $id);
    return true;
}

function fn_acc__get_order_items($params = array(), $items_per_page = 0){
    $default_params = array(
        'order_id'    => 0,
        'full_info' =>false,
        'page' => 1,
        'limit' => 0,
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_tbl      = "?:_acc_order_items";
    $m_key      = "oi_id";

    $j_tbl_2    = "?:_acc_document_items";
    $j_key_2    = "di_id";

    $j_tbl_3    = "?:_acc_documents";
    $j_key_3    = "document_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.item_type",
        "$m_tbl.item_id",
        "$m_tbl.quantity",
        "$m_tbl.quantity_in_reserve",
        "$m_tbl.comment",
        "$m_tbl.weight",
        "$m_tbl.order_id",
        "$m_tbl.date",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.date"   => "asc",
            "$m_tbl.$m_key" => "asc",
        )
    );

    $condition = $limit = $join = $group_by = $sorting = '';

    // *************************************************************************
    // 1. УСЛОВИЯ ОТБОРА
    // *************************************************************************
    // По ID
    if ($params["{$m_key}_array"] = to__array($params[$m_key])){
        $condition .= db_quote(" AND $m_tbl.$m_key in (?n)", $params["{$m_key}_array"]);
    }

    // По ORDER ID
    if ($params["order_id_array"] = to__array($params["order_id"])){
        $condition .= db_quote(" AND $m_tbl.order_id in (?n)", $params["order_id_array"]);
    }

    // По item_id
    if ($params["item_id_array"] = to__array($params["item_id"])){
        $condition .= db_quote(" AND $m_tbl.item_id in (?n)", $params["item_id_array"]);
    }

    // По item_type
    if (is__array($params["item_type"])){
        $condition .= db_quote(" AND $m_tbl.item_type in (?a)", $params["item_type"]);
    }


    // *************************************************************************
    // 2. ПРИСОЕДИНИТЬ ТАБЛИЦЫ
    // *************************************************************************

    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }

    if (!empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(*) FROM $m_tbl $join WHERE 1 $condition");
        $limit = fn_paginate($params["page"], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])){
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $sql = UNS_DB_PREFIX . "SELECT " . implode(", ", $fields) . " FROM $m_tbl $join WHERE 1 $condition $sorting $limit";
    $data = db_get_hash_array($sql, $m_key);
//      fn_print_r(str_replace(array(UNS_DB_PREFIX,"?:"), array("", "uns_"), $sql));
    if (!is__array($data)) return array(array(), $params);

    // *************************************************************************
    // 6. ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ
    // *************************************************************************
    if ($params["info_RO"]){
        // Проверить все отгрузки по каждой позиции каждого заказа - возможен вариант нескольких отгрузок по одной позиции
        $m_tbl = "?:_acc_document_items";
        $sql = db_quote("SELECT $m_tbl.document_id,
                                ?:_acc_documents.date,
                                $m_tbl.di_id,
                                $m_tbl.quantity,
                                $m_tbl.oi_id
                         FROM $m_tbl
                           LEFT JOIN ?:_acc_documents ON ($m_tbl.document_id = ?:_acc_documents.document_id)
                         WHERE $m_tbl.oi_id in (?n)
                         ORDER BY ?:_acc_documents.date asc, $m_tbl.document_id asc
                         ", array_keys($data));

        $data_RO = db_get_array(UNS_DB_PREFIX.$sql);
        if (is__array($data_RO)){
            foreach ($data_RO as $v){
                if ($params["without_shipped_items"]){
                    $data[$v["oi_id"]]["quantity"] -= $v["quantity"];
                }
                $data[$v["oi_id"]]["info_RO"]["total_q"] += $v["quantity"];
                $data[$v["oi_id"]]["info_RO"]["items"][$v["di_id"]] = $v;
            }
        }

        foreach ($data as $k_data=>$v_data){
            if ($v_data["info_RO"]["total_q"]>0){
                if ($v_data["info_RO"]["total_q"] >= $v_data["quantity"]){
                    $data[$k_data]["shipped"] = "full";        // Полная отгрузка
                }else{
                    $data[$k_data]["shipped"] = "partially";   // Частичная отгрузка
                }
            }else{
                $data[$k_data]["shipped"] = "no";              // Отгрузок небыло
            }
        }
    }

    if ($params["full_info"]){
        $i = array(
            "D" => array(),
            "P" => array(),
            "PF" => array(),
            "PA" => array(),
        );
        foreach ($data as $k_data=>$v_data){
           $i[$v_data["item_type"]][] = $v_data["item_id"];
        }

        if (count($i["D"])){
            $p = array(
               "detail_id" => $i["D"],
            );
            list($details) = fn_uns__get_details($p);

        }

        if (count($i["P"])){
            $p = array(
               "p_id" => $i["P"],
            );
            list($pumps) = fn_uns__get_pumps($p);

        }

        if (count($i["PF"])){
            $p = array(
               "p_id" => $i["PF"],
            );
            list($pumps_frame) = fn_uns__get_pumps($p);

        }

        if (count($i["PA"])){
            $p = array(
               "p_id" => $i["PA"],
            );
            list($pumps_agregat) = fn_uns__get_pumps($p);
        }


        foreach ($data as $k_data=>$v_data){
            if ($v_data["item_type"] == "D"){
                $data[$k_data] += $details[$v_data["item_id"]];

            }elseif ($v_data["item_type"] == "P"){
                $data[$k_data] += $pumps[$v_data["item_id"]];

            }elseif ($v_data["item_type"] == "PF"){
                $data[$k_data] += $pumps_frame[$v_data["item_id"]];

            }elseif ($v_data["item_type"] == "PA"){
                $data[$k_data] += $pumps_agregat[$v_data["item_id"]];
            }
        }
    }

    $data = fn_group_data_by_field($data, "order_id");

    return array($data, $params);
}

// ОБНОВИТЬ ДЕТАЛЬ В КОМПЛЕКТАЦИИ ПАРТИИ
function fn_acc__upd_order_items($kit_id, $data){
    if (!is__more_0($kit_id)
        or !is__array($data)
        or !is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT kit_id FROM ?:_acc_kits WHERE kit_id = $kit_id"))
    ) return false;
    $m_table = "?:_acc_kit_details";
    foreach ($data as $i){
        if (is__more_0($i['quantity'])){
            if (isset($i['state']) and $i['state'] == "N") continue;
            $v = array(
                "quantity"  => $i['quantity'],
            );

            if (is__more_0($i['pd_id']) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT pd_id FROM $m_table WHERE pd_id = ?i", $i['pd_id']))){
                // ОБНОВИТЬ
                db_query(UNS_DB_PREFIX . "UPDATE $m_table SET ?u WHERE pd_id = ?i", $v, $i['pd_id']);
            }elseif ($i["pd_id"] == 0){
                // ДОБАВЛЕНИЕ
                if (is__more_0($i["detail_id"])){
                    $v["kit_id"]    = $kit_id;
                    $v["detail_id"] = $i["detail_id"];
                    db_query(UNS_DB_PREFIX . "INSERT INTO $m_table ?e", $v);
                }
            }
        }
    }
    return true;
}


//==============================================================================
// ОТГРУЗКИ и РЕЗЕРВИРОВАНИЯ по заказу
//==============================================================================
/**
 * ДОБАВИТЬ ОТГРУЗКУ по заказу
 *
 */
function fn_uns__add_shipment_by_order ($order_id, $order_data){
    // DOCUMENT ----------------------------------------------------------------
    $document = array(
        "type"          => 7, // Расходный ордер
        "date"          => $order_data["shipment_add"]["date"],
        "object_from"   => 0,
        "object_to"     => 19, // СГП Александрия
        "status"        => "A",
        "comment"       => $order_data["shipment_add"]["comment"],
        "customer_id"   => $order_data["order_data"]["order"]["customer_id"],
    );

    $document_items = null;
    if (is__array($order_data["order_data"]["document_items"])){
        foreach ($order_data["order_data"]["document_items"] as $i){
            if (is__more_0($i["RO_q"])){
                $document_items[] = array(
                    "oi_id"     => $i["oi_id"],
                    "item_type" => $i["item_type"],
                    "item_id"   => $i["item_id"],
                    "quantity"  => $i["RO_q"],
                    "weight"    => $i["weight"],
                );
            }
        }
    }
    if (!is__array($document) or !is__array($document_items)) return false;
    $document_id = fn_uns__upd_document(0, array(
            "document"          => $document,
            "document_items"    => $document_items,
        )
    );
    if (!is__more_0($document_id)) return false;
    //--------------------------------------------------------------------------

    // Сохранить в таблице ORDERS_DOCUMENTS ------------------------------------
    if (!db_get_field(UNS_DB_PREFIX . "SELECT od_id FROM ?:_acc_orders_documents WHERE document_id = ?i and order_id = ?i", $document_id, $order_id)){
        $v = array(
            "order_id" => $order_id,
            "document_id" => $document_id,
        );
        db_query(UNS_DB_PREFIX . "INSERT INTO ?:_acc_orders_documents ?e", $v);
    }
    //--------------------------------------------------------------------------

    // ОБНОВИТЬ РЕЗЕРВ ---------------------------------------------------------
    // при отгрузке, сначала отгружаются зарезервированные позиции
    foreach ($document_items as $i){
        db_query(UNS_DB_PREFIX . "UPDATE ?:_acc_order_items
                                  SET quantity_in_reserve=IF(quantity_in_reserve-?i<0,0,quantity_in_reserve-?i)
                                  WHERE oi_id = ?i", $i['quantity'], $i['quantity'], $i['oi_id']);
    }
    //--------------------------------------------------------------------------
}


/**
 * УДАЛИТЬ ОТГРУЗКУ ПО ЗАКАЗУ
 * @param $document_id
 */
function fn_uns__del_shipment_by_order($document_id){
    fn_uns__del_document($document_id);
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_orders_documents WHERE document_id = ?i ", $document_id);
}

/**
 * ОБНОВИТЬ РЕЗЕРВ ПО ЗАКАЗУ
 * @param $order_id
 * @param $data
 */
function fn_uns__upd_reserve_by_order ($order_id, $data){
    if (is__more_0($order_id) and is__array($data)){
        $d = null;
        foreach ($data as $v){
            if (    $v["quantity"]>$v["RO_total_q"]
                and $v["quantity_in_reserve"]>=0
                and $v["quantity_in_reserve"] != $v["quantity_in_reserve_old"]){ // Произошла смена резерва
                if (in__range($v["quantity_in_reserve"], 0, $v["quantity"]-$v["RO_total_q"])){
                    db_query(UNS_DB_PREFIX . "UPDATE ?:_acc_order_items SET ?u WHERE oi_id = ?i", array("quantity_in_reserve" => $v["quantity_in_reserve"]), $v["oi_id"]);
                }
            }
        }
    }
}


function fn_uns__get_details_of_orders ($month, $year){
    if (!is__more_0($month, $year)) return false;
    $day = strtotime($year . "-" . $month . "-" . "1" . " 00:00:00");
    $sql = db_quote(" SELECT
                         a.m
                        ,a.item_id
                        ,sum(a.remain) as remain
                      FROM (
                             SELECT
                               uns__acc_order_items.item_id
                              ,uns__acc_order_items.order_id
                              ,uns__acc_order_items.quantity-uns__acc_order_items.quantity_in_reserve-ifnull(sum(uns__acc_document_items.quantity), 0) as remain
                              ,'before' as m
                             FROM uns__acc_order_items
                               INNER JOIN uns__acc_orders          on (uns__acc_orders.order_id      = uns__acc_order_items.order_id)
                               LEFT JOIN  uns__acc_document_items  on (uns__acc_document_items.oi_id = uns__acc_order_items.oi_id and uns__acc_document_items.item_type = 'D')
                             WHERE uns__acc_orders.status in ('Open', 'Paid')
                               AND uns__acc_order_items.item_type = 'D'
                               AND uns__acc_order_items.date < ?i
                             GROUP BY uns__acc_order_items.oi_id

                             UNION

                             SELECT
                                 uns__acc_order_items.item_id
                                ,uns__acc_order_items.order_id
                                ,uns__acc_order_items.quantity as remain
                                ,'current' as m
                              FROM uns__acc_order_items
                                INNER JOIN uns__acc_orders         on (uns__acc_orders.order_id      = uns__acc_order_items.order_id)
                              WHERE uns__acc_orders.status in ('Open', 'Paid', 'Shipped')
                                AND uns__acc_order_items.item_type = 'D'
                                AND uns__acc_order_items.date >= ?i
                                GROUP BY uns__acc_order_items.oi_id
                             ) as a
                      WHERE a.remain > 0
                      GROUP BY a.item_id,a.m
                      ORDER BY a.m
    ", $day, $day);
    $details = db_get_hash_array(UNS_DB_PREFIX . $sql, "item_id");
    return $details;
}

