<?php
/*******************************************************************************
 * РЕГИОНЫ
 *******************************************************************************/
function fn_uns__get_regions($params = array(), $items_per_page = 0){
    $default_params = array(
        'region_id' => 0,
        'only_active' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_table        = "?:regions";

    $fields = array(
        "$m_table.region_id",
        "$m_table.name",
        "$m_table.name_short",
        "$m_table.status",
        "$m_table.position",
        "$m_table.comment",
    );

    $sorting_schemas = array(
        'view' => array(
            "$m_table.position"    => 'asc',
            "$m_table.name"  => 'asc',
            "$m_table.region_id"      => 'asc',
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (strlen(trim__data($params['name']))) {
        $condition .= db_quote(" AND ($m_table.name LIKE ?l)", "%" . trim__data($params['name']) . "%" );
    }

    if ($params['region_id_array'] = to__array($params['region_id'])) {
        $condition .= db_quote(" AND $m_table.region_id in (?n)", $params['region_id_array']);
    }

    if ($params['ps_id_array'] = to__array($params['ps_id'])) {
        $condition .= db_quote(" AND $m_table.ps_id in (?n)", $params['ps_id_array']);
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

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "region_id");

    if (!is__array($data)) return false;

    return array($data, $params, $total);
}
