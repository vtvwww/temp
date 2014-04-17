<?php

function fn_acc__get_orders($params = array(), $items_per_page = 0){
    $default_params = array(
        'order_id' => 0,
        'page' => 1,
        'only_active'=>false,
        'limit' => 0,
        'sorting_schemas' => 'view',

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
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.status"  => "asc",
            "$m_tbl.date_finished"  => "desc",
        ),
        "view_in_sgp" => array(
//            "$m_tbl.status"  => "asc",
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

    if ($params['only_active']) {
        $condition .= db_quote(" AND $m_tbl.status = 'Open' ");
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
            'order_id'=>array_keys($data),
            'full_info'=>true,
        );
        list($items) = fn_acc__get_order_items($p);
        if (is__array($items)){
            foreach ($data as $k_d=>$v_d){
                $data[$k_d]["items"] = $items[$k_d];
            }
        }


        // подготовка данных для smarty
        if ($params["data_for_tmp"]){
            if (is__array($items)){
                foreach ($items as $k_o=>$v_o){
                    foreach ($v_o as $i){
                        $data[$k_o]["data_for_tmp"][$i["item_type"]][$i["item_id"]] = $i;
                    }
                }
            }
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
                'order_id'  => $order_id,
                'item_type' => $i['item_type'],
                'item_id'   => $i['item_id'],
                'quantity'  => abs($i['quantity']),
                'comment'   => $i['comment'],
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
    $d["status"]        = (fn_check_type($data["status"], "|Open|Close|"))?$data["status"]:"Open";
    $d["customer_id"]   = $data["customer_id"];
    $d["date_updated"]  = TIME;

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

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.item_type",
        "$m_tbl.item_id",
        "$m_tbl.quantity",
        "$m_tbl.comment",
        "$m_tbl.order_id",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.$m_key"  => "asc",
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
    // *************************************************************************
    // 6. ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ
    // *************************************************************************
    //foreach ($data as $k_data=>$v_data){}

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



