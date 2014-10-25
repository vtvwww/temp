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
        "$m_table.country_id",
        "$m_table.region_id",
        "$m_table.city_id",
        "$m_table.tin",
    );

    $sorting_schemas = array(
        'view' => array(
            "$m_table.country_id"   => 'asc',
            "$m_table.position"     => 'asc',
//            "$m_table.region_id"    => 'asc',
            "$m_table.name"         => 'asc',
//            "$m_table.customer_id"  => 'asc',
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

    if ($params['country_id_array'] = to__array($params['country_id'])) {
        $condition .= db_quote(" AND $m_table.country_id in (?n)", $params['country_id_array']);
    }

    if ($params['region_id_array'] = to__array($params['region_id'])) {
        $condition .= db_quote(" AND $m_table.region_id in (?n)", $params['region_id_array']);
    }

    if ($params['city_id_array'] = to__array($params['city_id'])) {
        $condition .= db_quote(" AND $m_table.city_id in (?n)", $params['city_id_array']);
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


// Получить информацию СТРАНЕ
function fn_uns__get_countries($params = array()){
    $default_params = array(
        'country_id' => 0,
        'only_active' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_table        = "?:_acc_customer_countries";

    $fields = array(
        "$m_table.id",
        "$m_table.name",
        "$m_table.code",
        "$m_table.alpha2",
    );

    $sorting_schemas = array(
        'view' => array(
            "$m_table.position"     => 'asc',
            "$m_table.name"         => 'asc',
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (strlen(trim__data($params['name']))) {
        $condition .= db_quote(" AND ($m_table.name LIKE ?l)", "%" . trim__data($params['name']) . "%" );
    }

    if ($params['country_id_array'] = to__array($params['country_id'])) {
        $condition .= db_quote(" AND $m_table.id in (?n)", $params['country_id_array']);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])) {
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k => $v) {
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "id");

    if (!is__array($data)) return false;

    return array($data, $params, $total);
}

function fn_uns__get_regions($params = array()){
    $default_params = array(
        'region_id' => 0,
        'country_id' => 0,
        'only_active' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
        'only_used' => true,
    );

    $params = array_merge($default_params, $params);

    $m_table        = "?:_acc_customer_regions";

    $fields = array(
        "$m_table.id",
        "$m_table.name",
        "$m_table.country_id",
    );

    $sorting_schemas = array(
        'view' => array(
            "$m_table.country_id"   => 'asc',
            "$m_table.position"     => 'asc',
            "$m_table.name"         => 'asc',
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (strlen(trim__data($params['name']))) {
        $condition .= db_quote(" AND ($m_table.name LIKE ?l)", "%" . trim__data($params['name']) . "%" );
    }

    if ($params['region_id_array'] = to__array($params['region_id'])) {
        $condition .= db_quote(" AND $m_table.id in (?n)", $params['region_id_array']);
    }

    if ($params['country_id_array'] = to__array($params['country_id'])) {
        $condition .= db_quote(" AND $m_table.country_id in (?n)", $params['country_id_array']);
    }

    if ($params["only_used"]){
        $join .= db_quote(" RIGHT JOIN uns__acc_customers ON (uns__acc_customer_regions.id  = uns__acc_customers.region_id) ");
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])) {
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k => $v) {
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "id");

    if (!is__array($data)) return false;

    return array($data, $params, $total);
}

function fn_uns__get_cities($params = array()){
    $default_params = array(
        'city_id' => 0,
        'region_id' => 0,
        'only_active' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
        "only_used"=>true,
    );

    $params = array_merge($default_params, $params);

    $m_table        = "?:_acc_customer_cities";

    $fields = array(
        "$m_table.id",
        "$m_table.name",
        "$m_table.region_id",
    );

    $sorting_schemas = array(
        'view' => array(
            "$m_table.region_id"     => 'asc',
            "$m_table.name"         => 'asc',
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (strlen(trim__data($params['name']))) {
        $condition .= db_quote(" AND ($m_table.name LIKE ?l)", "%" . trim__data($params['name']) . "%" );
    }

    if ($params['city_id_array'] = to__array($params['city_id'])) {
        $condition .= db_quote(" AND $m_table.id in (?n)", $params['city_id_array']);
    }

    if ($params['region_id_array'] = to__array($params['region_id'])) {
        $condition .= db_quote(" AND $m_table.region_id in (?n)", $params['region_id_array']);
    }

    if ($params["only_used"]){
        $join .= db_quote(" RIGHT JOIN uns__acc_customers ON (uns__acc_customer_cities.id  = uns__acc_customers.city_id) ");
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])) {
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k => $v) {
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "id");

    if (!is__array($data)) return false;

    return array($data, $params, $total);
}
