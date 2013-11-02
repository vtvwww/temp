<?php

if (!defined('AREA')) {
    die('Access denied');
}

/**
 * ЗАПРОС ВСЕХ ЕДИНИЦ ИЗМЕРЕНИЙ
 * @param null $unit_id
 * @return array
 */
function fn_uns__get_units_old($unit_id = null)
{
    $res = null;
    $cond = '';
    if (is_numeric($unit_id)) {
        $cond = " AND u_id = {$unit_id} ";
    }
    $items = db_get_hash_array(UNS_DB_PREFIX . "
                SELECT
                    uns_units.u_id
                  , uns_units.u_name
                  , uns_units.u_type
                  , uns_units.u_coefficient
                  , uns_units.u_status
                  , uns_units.u_position
                  , uns_units.u_comment
                  , uns_units.uc_id
                  , uns_unit_categories.uc_name
                FROM uns_units
                  LEFT JOIN uns_unit_categories ON (uns_unit_categories.uc_id = uns_units.uc_id)
                WHERE 1 ?p
                ORDER BY
                  uc_position asc, uc_name asc, u_position asc, u_name asc
                ", 'u_id', $cond);
    $res = $items;
    return $res;
}

function fn_uns__get_units($params = array(), $items_per_page = 0)
{
    $default_params = array(
        'u_id'          => 0,
        'feature_id'    => 0,
        'material_id'    => 0,
        'unit_type'     => 0,
        'group_by_categories'   => false,
        'uc_id'    => 0,
        'exclude_weight_group' => false,
        'only_active'   => false,
        'all'           => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'views',
        'simple' => false,
    );

    $params = array_merge($default_params, $params);

    $fields = array(
        '?:units.u_id',
        '?:units.u_name',
        '?:units.u_type',
        '?:units.u_coefficient',
        '?:units.u_status',
        '?:units.u_position',
        '?:units.u_comment',
        '?:units.uc_id',
        '?:unit_categories.uc_name',
    );

    if ($params['simple']){
        $fields = array(
            '?:units.u_id',
            '?:units.u_name'
        );
    }

    $sorting_schemas = array(
        'views' => array(
            '?:unit_categories.uc_position' => 'asc',
            '?:unit_categories.uc_name'     => 'asc',
            '?:units.u_position'            => 'asc',
            '?:units.u_name'                => 'asc',
        ),
        'select' => array(
            '?:units.u_type'                => 'desc',
            '?:units.u_position'            => 'asc',
            '?:units.u_name'                => 'asc',
        )
    );


    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (is__more_0($params['feature_id'])){
        $uc_id = db_get_field(UNS_DB_PREFIX . "SELECT uc_id FROM ?:features WHERE feature_id = ?i", $params['feature_id']);
        if (is__more_0($uc_id)){
            $params['uc_id'] = $uc_id;
        }
    }

    // Получить список единиц измерений по материалу
    if (is__more_0($params['material_id']) && fn_uns_check_item_types($params['item_type'])){
        $u_id = db_get_field(UNS_DB_PREFIX . "SELECT u_id FROM ?:accounting__and__items WHERE item_id = ?i AND item_type = ?s", $params['material_id'], $params['item_type']);
        if (is__more_0($u_id)){
            $params['u_id'] = $u_id;
            $params['u_id'] = to__array($params['u_id']);
            if (is__array($params['u_id_add'])){
                $params['u_id'] = array_merge($params['u_id'], $params['u_id_add']);
            }
        }
    }

    if (is__more_0($params['detail_id']) && fn_uns_check_item_types($params['item_type'])){
        $u_id = db_get_field(UNS_DB_PREFIX . "SELECT u_id FROM ?:accounting__and__items WHERE item_id = ?i AND item_type = ?s", $params['detail_id'], $params['item_type']);
        if (is__more_0($u_id)){
            $params['u_id'] = $u_id;
        }
    }


    if ($params['u_id'] = to__array($params['u_id'])) {
        $condition .= db_quote(" AND ?:units.u_id in (?n) ", $params['u_id']);
    }

    if ($params['uc_id_array'] = to__array($params['uc_id'])){
        $condition .= db_quote(" AND ?:units.uc_id in (?n) ", $params['uc_id_array']);
    }

    if ($params['unit_type']) {
        $condition .= db_quote(" AND ?:units.u_type = ?s ", $params['unit_type']);
    }

    if ($params['exclude_weight_group']) {
        $condition .= db_quote(" AND ?:units.uc_id != 2 "); // uc_id = 2 -- группа единиц веса
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND ?:units.u_status in ('A') ");
    } else {
        $condition .= db_quote(" AND ?:units.u_status in ('A', 'D') ");
    }

    if (!$params['simple']){
        $join .= db_quote(" LEFT JOIN ?:unit_categories ON (?:unit_categories.uc_id = ?:units.uc_id) ");
    }

    if (!$params['all'] && !empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(u_id) FROM ?:units $join WHERE 1 $condition");
        $limit = fn_paginate($params['page'], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])){
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM ?:units $join WHERE 1 $condition $sorting $limit", "u_id");

    if ($params['group_by_categories']){
        if (is__array($data)){
            list($unit_categories) = fn_uns__get_unit_categories();
            if (is__array($unit_categories)){
                foreach ($unit_categories as $k=>$v){
                    foreach ($data as $k_u=>$v_u){
                        if ($v_u['uc_id'] == $v['uc_id']){
                            $unit_categories[$k]['units'][$k_u] = $v_u;
                        }
                    }
                }
            }
            $data = $unit_categories;
        }
    }

    return array($data, $params, $total);
}


/**
 * ADD/UPDATE UNIT
 * @param $unit_id
 * @param $unit_data
 * @return integer $unit_id
 */
function fn_uns__update_unit($unit_id, $unit_data)
{
    if (!is_array($unit_data) || empty($unit_data)) return false;
    else {
        if ($unit_id && db_get_field(UNS_DB_PREFIX . "SELECT u_id FROM ?:units WHERE u_id = ?i", $unit_id)) {
            // Update
            db_query(UNS_DB_PREFIX . "UPDATE ?:units SET ?u WHERE u_id = ?i", $unit_data, $unit_id);

        } else {
            // Add
            $unit_id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:units ?e", $unit_data);
        }
    }
    return $unit_id;
}

/**
 * УДАЛЕНИЕ UNIT(S)
 * @param $ids
 * @internal param $unit_id
 * @return bool
 * @internal param $unit_ids
 */
function fn_uns__delete_unit($ids){
    if (!($ids = fn_check_before_deleting("del_units", $ids))) return false;
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:units WHERE u_id IN (?n)", $ids);
    return true;
}


/**
 * ЗАПРОС ГРУПП ЕДИНИЦ ИЗМЕРЕНИЙ старая версия
 * @param null $uc_id
 * @return array
 */
function fn_uns__get_unit_categories_old($uc_id = null)
{
    $res = null;
    $cond = '';
    if (is_numeric($uc_id)) {
        $cond .= " AND uc_id = {$uc_id} ";
    }
    $items = db_get_hash_array(UNS_DB_PREFIX . "
                    SELECT
                        uc_id
                      , uc_name
                      , uc_position
                      , uc_comment
                    FROM uns_unit_categories
                    WHERE 1 ?p
                    ORDER BY
                      uc_position asc
                    ", 'uc_id', $cond);
    $res = $items;
    return $res;
}

/**
 * ЗАПРОС ГРУПП ЕДИНИЦ ИЗМЕРЕНИЙ старая версия
 * @param array $params
 * @param int $items_per_page
 * @internal param null $uc_id
 * @return array
 */
function fn_uns__get_unit_categories($params = array(), $items_per_page = 0)
{
    $default_params = array(
        'uc_id' => 0,
        'all' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $fields = array(
        '?:unit_categories.uc_id',
        '?:unit_categories.uc_name',
        '?:unit_categories.uc_position',
        '?:unit_categories.uc_comment',
    );

    $sorting_schemas = array(
        'view' => array(
            '?:unit_categories.uc_position' => 'asc',
            '?:unit_categories.uc_name' => 'asc',
        )
    );

    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (!empty($params['uc_id'])) {
        if (!is__array($params['uc_id'])) $params['uc_id'] = (array)$params['uc_id'];
        $condition .= db_quote(" AND ?:unit_categories.uc_id in (?n)", $params['uc_id']);
    }

    if (!$params['all'] && !empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(uc_id) FROM ?:unit_categories $join WHERE 1 $condition");
        $limit = fn_paginate($params['page'], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])) {
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k => $v) {
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM ?:unit_categories $join WHERE 1 $condition $sorting $limit", "uc_id");

    return array($data, $params, $total);
}


/**
 * ADD/UPDATE UNIT
 * @param $id
 * @param $data
 * @return integer $unit_id
 */
function fn_uns__upd_unit_categories($id, $data){
    if (!is__array($data)) return false;
    else {
        if ($id && db_get_field(UNS_DB_PREFIX . "SELECT uc_id FROM ?:unit_categories WHERE uc_id = ?i", $id)) {
            db_query(UNS_DB_PREFIX . "UPDATE ?:unit_categories SET ?u WHERE uc_id = ?i", $data, $id);
        } else {
            $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:unit_categories ?e", $data);
        }
    }
    return $id;
}

/**
 * УДАЛЕНИЕ UNIT(S)
 * @param $ids
 * @return bool
 * @internal param $unit_category_id
 * @internal param $unit_ids
 */
function fn_uns__del_unit_categories($ids) {
    if (!($ids = fn_check_before_deleting("del_unit_categories", $ids))) return false;
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:unit_categories WHERE uc_id IN (?n)", $ids);
    return true;
}
