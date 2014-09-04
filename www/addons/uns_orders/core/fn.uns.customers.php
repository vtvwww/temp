<?php
/*******************************************************************************
 * КЛИЕНТЫ
 *******************************************************************************/
// Удаление КЛИЕНТОВ + проверка
function fn_uns__del_customer ($id){
    if (!($id = to__array($id))) return false;
    // Внести запрет на удаление клиента,
    // если по нему существует оформленный заказ или расходный ордер
    foreach ($id as $i){
        $orders      = array_shift(fn_acc__get_orders(array("customer_id"=>$i)));
        $documents   = array_shift(fn_uns__get_documents(array("customer_id"=>$i, "type" => 7))); // type=7 -- расходный ордер
        if (count($orders) or count($documents)){
            if (count($orders))     fn_set_notification("E", "Удаление невозможно", "По этому клиенту существуют заказы!");
            if (count($documents))  fn_set_notification("E", "Удаление невозможно", "По этому клиенту существуют расходные ордеры!");
        }else{
            db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_customers WHERE customer_id IN (?n)", $id);
        }
    }
    return true;
}

// Обновление информации о клиентах
function fn_uns__upd_customer($id, $data){
    $data = trim__data($data);
    if (is__more_0($id) and is__array($data) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT customer_id FROM ?:_acc_customers WHERE customer_id = $id"))){
        db_query(UNS_DB_PREFIX . "UPDATE ?:_acc_customers SET ?u WHERE customer_id = ?i", $data, $id);
    }else{
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:_acc_customers ?e", $data);
    }
    return $id;
}

// Получить информацию о клиентах
function fn_uns__get_customers($params = array(), $items_per_page = 0){
    $default_params = array(
        'customer_id' => 0,
        'only_active' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_table        = "?:_acc_customers";

    $fields = array(
        "$m_table.customer_id",
        "$m_table.name",
        "$m_table.name_short",
        "$m_table.status",
        "$m_table.position",
        "$m_table.comment",
        "$m_table.to_export",
    );

    $sorting_schemas = array(
        'view' => array(
            "$m_table.position"     => 'asc',
            "$m_table.name"         => 'asc',
            "$m_table.customer_id"  => 'asc',
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (strlen(trim__data($params['name']))) {
        $condition .= db_quote(" AND ($m_table.name LIKE ?l)", "%" . trim__data($params['name']) . "%" );
    }

    if ($params['customer_id_array'] = to__array($params['customer_id'])) {
        $condition .= db_quote(" AND $m_table.customer_id in (?n)", $params['customer_id_array']);
    }

    if ($params['ps_id_array'] = to__array($params['ps_id'])) {
        $condition .= db_quote(" AND $m_table.ps_id in (?n)", $params['ps_id_array']);
    }

    if (in_array($params['to_export'], array("N", "Y"))){
        $condition .= db_quote(" AND $m_table.to_export = ?s ", $params['to_export']);
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND $m_table.status in ('A') ");
    } else {
        $condition .= db_quote(" AND $m_table.status in ('A', 'D') ");
    }

    if (!empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(*) FROM $m_table $join WHERE 1 $condition");
        $limit = fn_paginate($params['page'], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])) {
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k => $v) {
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "customer_id");

    if (!is__array($data)) return false;

    return array($data, $params, $total);
}

