<?php
/*******************************************************************************
 * ТИПЫ НАСОСА
 *******************************************************************************/
/**
 * ПОЛУЧИТЬ ТИПЫ НАСОСОВ
 * @param array $params
 * @param int $items_per_page
 * @return array
 */
function fn_uns__get_pump_types($params = array(), $items_per_page = 0){
    $default_params = array(
        'pt_id' => 0,
        'only_active' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_table = "?:pump_types";

    $fields = array(
        "$m_table.pt_id",
        "$m_table.pt_name",
        "$m_table.pt_name_short",
        "$m_table.pt_status",
        "$m_table.pt_position",
        "$m_table.pt_comment",
        "$m_table.view_in_plans",
    );

    $sorting_schemas = array(
        'view' => array(
            "$m_table.pt_status"    => 'asc',
            "$m_table.pt_position"  => 'asc',
            "$m_table.pt_name"      => 'asc',
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if ($params['pt_id'] = to__array($params['pt_id'])){
        $condition .= db_quote(" AND $m_table.pt_id in (?n)", $params['pt_id']);
    }

    if ($params['view_in_plans'] == "Y") {
        $condition .= db_quote(" AND $m_table.view_in_plans = 'Y' ");
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND $m_table.pt_status in ('A') ");
    } else {
        $condition .= db_quote(" AND $m_table.pt_status in ('A', 'D') ");
    }


    if (!empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(*) FROM $m_table $join WHERE 1 $condition");
        $limit = fn_paginate($params['page'], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])){
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "pt_id");

    return array($data, $params, $total);
}


/**
 * ОБНОВИТЬ ТИП НАСОСА
 * @param int $id
 * @param $data
 * @return bool|int
 */
function fn_uns__upd_pump_type($id=0, $data){
    if (!is__array($data) || !is_numeric($id) || $id<0) return false;
    $data = trim__data($data);
    if (!strlen($data['pt_name'])) return false;

    $d = array(
        'pt_name'       => $data['pt_name'],
        'pt_status'     => $data['pt_status'],
        'pt_position'   => (is_numeric($data['pt_position']) && $data['pt_position']>=0)?$data['pt_position']:0,
        'pt_comment'    => $data['pt_comment'],
        'pt_name_short' => $data['pt_name_short'],
        'view_in_plans' => ($data['view_in_plans']=="Y")?"Y":"N",
    );

    if (db_get_field(UNS_DB_PREFIX . "SELECT pt_id FROM ?:pump_types WHERE pt_id = ?i", $id)){
        db_query(UNS_DB_PREFIX . 'UPDATE ?:pump_types SET ?u WHERE pt_id = ?i', $d, $id);
    }else{
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:pump_types ?e", $d);
    }

    return $id;
}


/**
 * УДАЛИТЬ ТИП НАСОСА
 * @param $ids
 * @return bool
 */
function fn_uns__del_pump_type($ids){
    if (!($ids = fn_check_before_deleting("del_pump_type", $ids))) return false;
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:pump_types WHERE pt_id IN (?n)", $ids);
    return true;
}


/*******************************************************************************
 * СЕРИИ (СБОРКИ) НАСОСОВ
 *******************************************************************************/

/**
 * ПОЛУЧИТЬ СЕРИИ НАСОСОВ
 * @param array $params
 * @param int $items_per_page
 * @internal param $ids
 * @return bool
 */
function fn_uns__get_pump_series($params = array(), $items_per_page = 0){
    $default_params = array(
        'ps_id' => 0,
        'p_id' => 0,
        'only_active' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_table = "?:pump_series";
    $j_table = "?:pump_types";

    $fields = array(
        "$m_table.ps_id",
        "$m_table.ps_name",
        "$m_table.ps_status",
        "$m_table.ps_position",
        "$m_table.ps_comment",
        "$m_table.pt_id",
        "$m_table.view_in_plans",
        "$m_table.party_size_min",
        "$m_table.party_size_max",
        "$m_table.party_size_step",
        "$j_table.pt_name",
        "$j_table.pt_name_short",
    );

    $sorting_schemas = array(
        'view' => array(
            "$j_table.pt_status" => 'asc',
            "$j_table.pt_position" => 'asc',
            "$j_table.pt_name" => 'asc',
            "$m_table.ps_status" => 'asc',
            "$m_table.ps_position" => 'asc',
            "$m_table.ps_name" => 'asc',
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if ($params['p_id_array'] = to__array($params['p_id'])) {
        $ps_ids = db_get_fields(UNS_DB_PREFIX . "SELECT ps_id FROM ?:pumps WHERE p_id in (?n)", $params['p_id_array']);
        if (is__array($ps_ids)){
            $params['ps_id'] = $ps_ids;
        }
    }

    if ($params['ps_id_array'] = to__array($params['ps_id'])) {
        $condition .= db_quote(" AND $m_table.ps_id in (?n)", $params['ps_id_array']);
    }

    $params['ps_name'] = trim__data($params['ps_name']);
    if (strlen($params['ps_name'])) {
        $condition .= db_quote(" AND ($m_table.ps_name LIKE ?l)", "%{$params['ps_name']}%");
    }

    if ($params['pt_id_array'] = to__array($params['pt_id'])) {
        $condition .= db_quote(" AND $m_table.pt_id in (?n)", $params['pt_id_array']);
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND $m_table.ps_status in ('A') ");
    } else {
        $condition .= db_quote(" AND $m_table.ps_status in ('A', 'D') ");
    }

    if ($params['pt_id_array'] = to__array($params['pt_id'])) {
        $condition .= db_quote(" AND $m_table.pt_id in (?n)", $params['pt_id_array']);
    }

    if ($params['view_in_plans'] == "Y") {
        $condition .= db_quote(" AND $m_table.view_in_plans = 'Y' ");
        $condition .= db_quote(" AND $j_table.view_in_plans = 'Y' ");
    }

    $join .= db_quote(" LEFT JOIN $j_table ON ($j_table.pt_id = $m_table.pt_id ) ");

    if ($params['pump_q_ty']){
        $j_table_pumps = "?:pumps";
        $fields[] = "IFNULL(t_q.q, 0) AS pump_q_ty";
        $join .= db_quote("
                LEFT JOIN (SELECT COUNT($j_table_pumps.p_id) AS q, $j_table_pumps.ps_id FROM $j_table_pumps GROUP BY $j_table_pumps.ps_id) AS t_q
                     ON   (t_q.ps_id = $m_table.ps_id) ");
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

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "ps_id");

    if ($params["group_by_types"] and is__array($data)){
        list($pump_types) = fn_uns__get_pump_types($params);
        if (is__array($pump_types)){
            foreach ($pump_types as $k=>$v){
                foreach ($data as $k_u=>$v_u){
                    if ($v_u['pt_id'] == $v['pt_id']){
                        $pump_types[$k]['pump_series'][$k_u] = $v_u;
                    }
                }
            }
        }
        $data = $pump_types;

    }

    return array($data, $params, $total);
}

/**
 * УДАЛИТЬ НАСОС
 * @param $ids
 * @return bool
 */
function fn_uns__del_pump_series($ids){
    if (!($ids = fn_check_before_deleting("del_pump_series", $ids))) return false;
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:pump_series WHERE ps_id IN (?n)", $ids);
    fn_uns__del_packing_list($ids, UNS_PACKING_TYPE__SERIES);
    return true;
}

/**
 * ОБНОВИТЬ СЕРИЮ НАСОСОВ
 * @param int $id
 * @param $data
 * @return bool|int
 */
function fn_uns__upd_pump_series($id = 0, $data){
    if (!is__array($data) || !is_numeric($id) || $id < 0) return false;
    $data = trim__data($data);

    if (!strlen($data['ps_name'])) return false;
    if (!is__more_0($data['pt_id'])) return false;

    $d = array(
        'ps_name'       => $data['ps_name'],
        'ps_status'     => $data['ps_status'],
        'ps_position'   => (is_numeric($data['ps_position']) && $data['ps_position'] >= 0) ? $data['ps_position'] : 0,
        'ps_comment'    => $data['ps_comment'],
        'pt_id'         => $data['pt_id'],
        'view_in_plans' => ($data['view_in_plans'] == "N")?"N":"Y",
        'party_size_min' => (is__more_0($data['party_size_min']))?$data['party_size_min']:1,
        'party_size_max' => (is__more_0($data['party_size_max']))?$data['party_size_max']:2,
        'party_size_step'=> (is__more_0($data['party_size_step']))?$data['party_size_step']:1,
    );

    if (db_get_field(UNS_DB_PREFIX . "SELECT ps_id FROM ?:pump_series WHERE ps_id = ?i", $id)) {
        db_query(UNS_DB_PREFIX . 'UPDATE ?:pump_series SET ?u WHERE ps_id = ?i', $d, $id);
    } else {
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:pump_series ?e", $d);
    }

    // Обновить комплектацию серии
    fn_uns__upd_packing_list($id, UNS_PACKING_TYPE__SERIES, $data['packing_list']);

    return $id;
}

/*******************************************************************************
 * НАСОСЫ
 *******************************************************************************/
function fn_uns__get_pumps($params = array(), $items_per_page = 0){
    $default_params = array(
        'p_id' => 0,
        'only_active' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_table        = "?:pumps";
    $j_table_series = "?:pump_series";
    $j_table_types  = "?:pump_types";

    $fields = array(
        "$m_table.p_id",
        "$m_table.p_name",
        "$m_table.p_status",
        "$m_table.p_position",
        "$m_table.p_comment",
        "$m_table.ps_id",
        "$m_table.weight_p",
        "$m_table.weight_pf",
        "$m_table.weight_pa",
        "$m_table.include_to_accessory",
        "$m_table.as_set_of_details",
        "$j_table_series.ps_name",
        "$j_table_types.pt_id",
        "$j_table_types.pt_name",
    );

    $sorting_schemas = array(
        'view' => array(
            "$j_table_types.pt_status"    => 'asc',
            "$j_table_types.pt_position"  => 'asc',
            "$j_table_types.pt_name"      => 'asc',
            "$j_table_series.ps_status"   => 'asc',
            "$j_table_series.ps_position" => 'asc',
            "$j_table_series.ps_name"     => 'asc',
            "$m_table.p_status"           => 'asc',
            "$m_table.p_position"         => 'asc',
            "$m_table.p_name"             => 'asc',

        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (strlen(trim__data($params['p_name']))) {
        $condition .= db_quote(" AND ($m_table.p_name LIKE ?l)", "%" . trim__data($params['p_name']) . "%" );
    }

    if ($params['p_id_array'] = to__array($params['p_id'])) {
        $condition .= db_quote(" AND $m_table.p_id in (?n)", $params['p_id_array']);
    }

    if ($params['ps_id_array'] = to__array($params['ps_id'])) {
        $condition .= db_quote(" AND $m_table.ps_id in (?n)", $params['ps_id_array']);
    }

    if ($params['pt_id_array'] = to__array($params['pt_id'])) {
        $condition .= db_quote(" AND $j_table_types.pt_id in (?n)", $params['pt_id_array']);
    }

    if ($params['without_sets_of_details']) {
        $condition .= db_quote(" AND $m_table.as_set_of_details = 'N' ");
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND $m_table.p_status in ('A') ");
    } else {
        $condition .= db_quote(" AND $m_table.p_status in ('A', 'D') ");
    }

    $join .= db_quote(" LEFT JOIN $j_table_series ON ($j_table_series.ps_id = $m_table.ps_id ) ");
    $join .= db_quote(" LEFT JOIN $j_table_types ON ($j_table_types.pt_id = $j_table_series.pt_id ) ");

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

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "p_id");

    if (!is__array($data)) return false;

    if ($params['number_of_parts']) {
        foreach ($data as $k_d => $v_d) {
            $qs = db_get_hash_array(
                UNS_DB_PREFIX . "select  ppl_item_part, count(*) as q
                       from  ?:pumps_packing_list
                      where  ppl_item_type='item' and ppl_item_id=?i
                   group by  ppl_item_part
                   order by  ppl_item_part asc", "ppl_item_part", $k_d);
            $data[$k_d]['number_of_parts'] = $qs;
        }
    }

    if ($params['group_by_series']){
        list($pump_series) = fn_uns__get_pump_series(array("only_active"=>$params["only_active"]));
        if (is__array($pump_series)){
            foreach ($pump_series as $k_ps=>$v_ps){
                foreach ($data as $k_p=>$v_p){
                    if ($v_p['ps_id'] == $v_ps['ps_id']){
                        $pump_series[$k_ps]['pumps'][$k_p] = $v_p;
                    }
                }
            }
            $data = $pump_series;
        }
    }

    return array($data, $params, $total);
}

/**
 * ОБНОВИТЬ НАСОС
 * @param int $id
 * @param $data
 * @return bool|int
 */
function fn_uns__upd_pump($id = 0, $data){
    if (!is__array($data) || !is_numeric($id) || $id < 0) return false;
    $data = trim__data($data);

    if (!strlen($data['p_name'])) return false;
    if (!is__more_0($data['ps_id'])) return false;

    $d = array(
        'p_name'        => $data['p_name'],
        'p_status'      => $data['p_status'],
        'p_position'    => (is_numeric($data['p_position']) && $data['p_position'] >= 0) ? $data['p_position'] : 0,
        'p_comment'     => $data['p_comment'],
        'ps_id'         => $data['ps_id'],
        'weight_p'      => (is__more_0(floatval($data['weight_p'])))?floatval($data['weight_p']):0,
        'weight_pf'     => (is__more_0(floatval($data['weight_pf'])))?floatval($data['weight_pf']):0,
        'weight_pa'     => (is__more_0(floatval($data['weight_pa'])))?floatval($data['weight_pa']):0,
        'include_to_accessory'  => ($data['include_to_accessory'] == "Y")?"Y":"N",
        'as_set_of_details'     => ($data['as_set_of_details'] == "Y")?"Y":"N",
    );

    if (db_get_field(UNS_DB_PREFIX . "SELECT p_id FROM ?:pumps WHERE p_id = ?i", $id)) {
        db_query(UNS_DB_PREFIX . 'UPDATE ?:pumps SET ?u WHERE p_id = ?i', $d, $id);
    } else {
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:pumps ?e", $d);
    }

    // Обновить комплектацию серии
    if (is__array($data['packing_list'])){
        fn_uns__upd_packing_list($id, UNS_PACKING_TYPE__ITEM, $data['packing_list']);
    }

    // Обновить характеристики
    if (is__array($data['features'])){
        fn_uns__upd_features_items('P', $id, $data['features']);
    }

    // Обновить параметры
    if (is__array($data['options'])){
        fn_uns__upd_options_items('P', $id, $data['options']);
    }

    // Обновить учет
    if (is__array($data['accounting'])){
        fn_uns__upd_accounting_items('P', $id, $data['accounting']);
    }



    return $id;
}

/**
 * УДАЛИТЬ НАСОС
 * @param $ids
 * @return bool
 */
function fn_uns__del_pump($ids) {
    if(!($ids = fn_check_before_deleting("del_pump", $ids))){
        return false;
    }

    // Удалить комплектацию за этим насосом
    fn_uns__del_packing_list($ids, UNS_PACKING_TYPE__ITEM);

    // Удалить сам НАСОС
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:pumps WHERE p_id IN (?n)", $ids);

    // Удаление УЧЕТА
    fn_uns__del_accounting_items('P', $ids);

    // Удаление ХАРАКТЕРИСТИК
    fn_uns__del_features_items('P', $ids);

    // Удаление ПАРАМЕТРОВ
    fn_uns__del_options_items('P', $ids);

    return true;
}
