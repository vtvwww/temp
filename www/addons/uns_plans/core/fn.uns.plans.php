<?php

/**
 * Получить ПЛАНЫ продаж
 * @param array $params
 * @param int $items_per_page
 * @return array
 */
function fn_uns__get_plans($params = array(), $items_per_page = 0){
    $default_params = array(
        'id'        => 0,
        'ps_id'     => 0,
        'quantity'  => 0,
        'month'     => 0,
        'year'      => 0,
        'limit'     => 0,
        'sorting_schemas' => 'view',

    );

    $params = array_merge($default_params, $params);

    $m_tbl = "?:_plans";
    $m_key = "plan_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.type",
        "$m_tbl.month",
        "$m_tbl.year",
        "$m_tbl.status",
        "$m_tbl.comment",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.status" => "asc",
            "$m_tbl.year"   => "desc",
            "$m_tbl.month"  => "desc",
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

    // type's
    if (in_array($params["type"], array("sales"))){
        $condition .= db_quote(" AND $m_tbl.type = ?s ", $params["type"]);
    }

    // month
    if ($params["month_array"] = to__array($params["month"])){
        $condition .= db_quote(" AND $m_tbl.month in (?n)", $params["month_array"]);
    }

    // year
    if ($params["year_array"] = to__array($params["year"])){
        $condition .= db_quote(" AND $m_tbl.year in (?n)", $params["year_array"]);
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND $m_tbl.status = 'A' ");
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

    if ($params["with_count"]){
        $counts = db_get_hash_array(UNS_DB_PREFIX . "SELECT plan_id, COUNT(pi_id) as count FROM ?:_plan_items WHERE plan_id in (?n) GROUP BY plan_id", "plan_id", array_keys($data));
        if (is__array($counts)){
            foreach ($data as $k_d=>$v_d){
                $data[$k_d]["count"] = $counts[$k_d]["count"];
            }
        }
    }

    if ($params["with_sum"]){
        $counts = db_get_hash_array(UNS_DB_PREFIX . "SELECT plan_id, sum(ukr_curr) as sum_ukr_curr, sum(ukr_next) as sum_ukr_next, sum(exp_curr) as sum_exp_curr, sum(exp_next) as sum_exp_next FROM ?:_plan_items WHERE plan_id in (?n) GROUP BY plan_id", "plan_id", array_keys($data));
        if (is__array($counts)){
            foreach ($data as $k_d=>$v_d){
                $data[$k_d]["sum_ukr_curr"] = $counts[$k_d]["sum_ukr_curr"];
                $data[$k_d]["sum_ukr_next"] = $counts[$k_d]["sum_ukr_next"];
                $data[$k_d]["sum_exp_curr"] = $counts[$k_d]["sum_exp_curr"];
                $data[$k_d]["sum_exp_next"] = $counts[$k_d]["sum_exp_next"];
            }
        }
    }

    if ($params["with_items"]){
        $p = array(
            'plan_id'=>array_keys($data),
        );
        list($items) = fn_uns__get_plan_items($p);
        if (is__array($items)){
            foreach ($data as $k_d=>$v_d){
                $data[$k_d]["items"] = $items[$k_d];
            }
        }
    }

    // Сгруппировать позиции плана по item_type и item_id
    if ($params["with_items"] and $params["group_by_item"]) {
        foreach ($data as $k_d=>$v_d){
            foreach ($v_d["items"] as $k_i=>$v_i){
                $data[$k_d]["group_by_item"][$v_i["item_type"]][$v_i["item_id"]] = $v_i;
            }
        }
    }

    return array($data, $params);
}


/**
 * Получить ПОЗИЦИИ ПЛАНА ПРОИЗВОДСТВА
 * @param array $params
 * @param int $items_per_page
 * @return array
 */
function fn_uns__get_plan_items($params = array(), $items_per_page = 0){
    $default_params = array(
        'plan_id'           => 0,
        'page'              => 1,
        'limit'             => 0,
        'sorting_schemas'   => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_tbl      = "?:_plan_items";
    $m_key      = "pi_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.item_type",
        "$m_tbl.item_id",
        "$m_tbl.ukr_curr",
        "$m_tbl.ukr_next",
        "$m_tbl.exp_curr",
        "$m_tbl.exp_next",
        "$m_tbl.plan_id",
        "$m_tbl.forced_status",
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

    // По plan_id
    if ($params["plan_id_array"] = to__array($params["plan_id"])){
        $condition .= db_quote(" AND $m_tbl.plan_id in (?n)", $params["plan_id_array"]);
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

    // Сгруппировать данные по plan_id
    $data = fn_group_data_by_field($data, "plan_id");

    // Отсортировать по типам
    // Сначала идут Серии, затем Детали. (Они уже отсортированы по своим правилам)
    if ($params["order_by_item_type"]){

    }

    return array($data, $params);
}

/**
 * Обновить ПЛАН ПРОИЗВОДСТВА
 * @param int $id
 * @param $data
 * @return bool|int
 */
function fn_uns__upd_plan($id = 0, $data){
    $data = trim__data($data);
    if (!is__array($data) || !is_numeric($id) || $id < 0 ) return false;

    // Обновить информацию о PLAN
    $id = fn_uns__upd_plan_info($id, $data['plan']);

    // Обновить позиции
    fn_uns__upd_plan_items($id, $data['plan_items']);

    return $id;
}

/**
 * Обновить позиции плана производства
 * @param $order_id
 * @param $data
 * @return bool
 */
function fn_uns__upd_plan_items($id, $data){
    if (!is__more_0($id) or !is__array($data)) return false;
    $forced_status = array("N", "R", "Y", "G");
    $data = trim__data($data);
    $m_table = "?:_plan_items";
    $pi_ids = array();
    foreach ($data as $i){
        if (is__more_0($i['item_id']) and fn_check_type($i['item_type'], "|S|D|")){
            $v = array(
                'plan_id'       => $id,
                'item_type'     => $i['item_type'],
                'item_id'       => $i['item_id'],
                'ukr_curr'      => is__more_0($i['ukr_curr'])?$i['ukr_curr']:0,
                'ukr_next'      => is__more_0($i['ukr_next'])?$i['ukr_next']:0,
                'exp_curr'      => is__more_0($i['exp_curr'])?$i['exp_curr']:0,
                'exp_next'      => is__more_0($i['exp_next'])?$i['exp_next']:0,
                'forced_status' => in_array($i['forced_status'], $forced_status)?$i['forced_status']:"N",
            );

            if (is__more_0($i['pi_id']) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT pi_id FROM $m_table WHERE pi_id = ?i", $i['pi_id']))){
                // ОБНОВИТЬ
                db_query(UNS_DB_PREFIX . "UPDATE $m_table SET ?u WHERE pi_id = ?i", $v, $i['pi_id']);
                $pi_ids[] = $i['pi_id'];
            }elseif ($i['pi_id'] == 0){
                // ДОБАВЛЕНИЕ
                $pi_ids[] = db_query(UNS_DB_PREFIX . "INSERT INTO $m_table ?e", $v);
            }
        }
    }
    if (is__array($pi_ids)){
        // Удалить все лишнее
        db_query(UNS_DB_PREFIX . "DELETE FROM $m_table WHERE plan_id = ?i AND pi_id not in (?n) ", $id, $pi_ids);
    }else{
        db_query(UNS_DB_PREFIX . "DELETE FROM $m_table WHERE plan_id = ?i ", $id);
    }

    return true;
}

/**
 * Обновить позицию плана производства при редактировании из ПЛАНА ПРОИЗВОДСТВА
 */
function fn_uns__upd_plan_item($month, $year, $data){
    if (is__more_0($month, $year) and !is__array($data)) return false;
    $forced_status = array("N", "R", "Y", "G");
    $data = trim__data($data);
    $m_table = "?:_plan_items";
    if (is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT pi_id FROM $m_table WHERE pi_id = ?i", $data['pi_id']))){
        $v = array(
            'ukr_curr'      => is__more_0($data['ukr_curr'])?$data['ukr_curr']:0,
            'ukr_next'      => is__more_0($data['ukr_next'])?$data['ukr_next']:0,
            'exp_curr'      => is__more_0($data['exp_curr'])?$data['exp_curr']:0,
            'exp_next'      => is__more_0($data['exp_next'])?$data['exp_next']:0,
            'forced_status' => in_array($data['forced_status'], $forced_status)?$data['forced_status']:"N",
        );
        db_query(UNS_DB_PREFIX . "UPDATE $m_table SET ?u WHERE pi_id = ?i", $v, $data['pi_id']);
    }
    return true;
}




/**
 * Обновить информацию о плане производства
 * @param $id
 * @param $data
 * @return bool|int
 */
function fn_uns__upd_plan_info($id, $data){
    $data = trim__data($data);

    // План продаж сохраняется при расчете плана продаж
    if ($data["override"] == "Y"){
        $id = db_get_field(UNS_DB_PREFIX . "SELECT plan_id FROM ?:_plans WHERE type = ?s AND month = ?i and year = ?i ", $data["type"], $data["month"], $data["year"]);
    }

    if (is__more_0($id) and is__array($data) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT plan_id FROM ?:_plans WHERE plan_id = $id"))){
        db_query(UNS_DB_PREFIX . "UPDATE ?:_plans SET ?u WHERE plan_id = ?i", $data, $id);
    }else{
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:_plans ?e", $data);
    }
    return $id;
}

/**
 * Удалить план производства
 * @param $id
 * @return bool
 */
function fn_uns__del_plan($id){
    if (!($id = to__array($id))) return false;
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_plans         WHERE plan_id IN (?n)", $id);
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_plan_items    WHERE plan_id IN (?n)", $id);
    return true;
}
