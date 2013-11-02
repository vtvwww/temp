<?php

if (!defined('AREA')) {
    die('Access denied');
}

/**
 * ЗАПРОС ХАРАКТЕРИСТИК старая верия
 * @param null $feature_id
 * @return array
 */
function fn_uns__get_features_old($feature_id = null)
{
    $res = null;
    $cond = '';
    if (is_numeric($feature_id)) {
        $cond = " AND feature_id = {$feature_id} ";
    }
    $items = db_get_hash_array(UNS_DB_PREFIX . "
                    SELECT
                        ?:features.feature_id
                      , ?:features.feature_name
                      , ?:features.feature_no
                      , ?:features.feature_status
                      , ?:features.feature_position
                      , ?:features.feature_comment
                      , ?:features.uc_id
                      , ?:unit_categories.uc_name
                    FROM ?:features
                      LEFT JOIN ?:unit_categories ON (?:unit_categories.uc_id = ?:features.uc_id)
                    WHERE 1 ?p
                    ORDER BY
                      feature_position asc, feature_name asc, feature_no asc
                    ", 'feature_id', $cond);
    $res = $items;
    return $res;
}


/**
 * ЗАПРОС ХАРАКТЕРИСТИК
 * @param array $params
 * @param int $items_per_page
 * @return array
 */
function fn_uns__get_features($params = array(), $items_per_page = 0){
    $default_params = array(
        'feature_id' => 0,
        'only_active' => false,
        'all' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_table = "?:features";
    $j_table_unit_categories = "?:unit_categories";

    $fields = array(
        "$m_table.feature_id",
        "$m_table.feature_name",
        "$m_table.feature_no",
        "$m_table.feature_status",
        "$m_table.feature_position",
        "$m_table.feature_comment",
        "$m_table.uc_id",
        "$j_table_unit_categories.uc_name",
    );

    $sorting_schemas = array(
        'view' => array(
            "$m_table.feature_status" => 'asc',
            "$m_table.feature_position" => 'asc',
            "$m_table.feature_name" => 'asc',
            "$m_table.feature_no" => 'asc',
        )
    );

    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if ($params['feature_id_array'] = to__array($params['feature_id'])) {
        $condition .= db_quote(" AND $m_table.feature_id in (?n)", $params['feature_id_array']);
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND $m_table.feature_status in ('A') ");
    } else {
        $condition .= db_quote(" AND $m_table.feature_status in ('A', 'D') ");
    }

    $join .= db_quote(" LEFT JOIN $j_table_unit_categories ON ($j_table_unit_categories.uc_id = $m_table.uc_id) ");


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

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "feature_id");

    return array($data, $params, $total);
}


/**
 * ДОБАВЛЕНИЕ/ОБНОВЛЕНИЕ ХАРАКТЕРИСТИК
 * @param $id
 * @param $data
 * @return integer $id
 */
function fn_uns__upd_feature($id, $data)
{
    if (!is_array($data) || empty($data)) return false;
    else {
        if ($id && db_get_field(UNS_DB_PREFIX . "SELECT feature_id FROM ?:features WHERE feature_id = ?i", $id)) {
            db_query(UNS_DB_PREFIX . "UPDATE ?:features SET ?u WHERE feature_id = ?i", $data, $id);
        } else {
            $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:features ?e", $data);
        }
    }
    return $id;
}


/**
 * УДАЛЕНИЕ ХАРАКТЕРИСТИК
 * @param $ids
 * @return bool
 * @internal param $id
 * @internal param $unit_ids
 */
function fn_uns__del_feature($ids) {
    if (!($ids = fn_check_before_deleting("del_feature", $ids))) return false;
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:features WHERE feature_id IN (?n)", $ids);
    return true;
}


/**
 * ЗАПРОС ХАРАКТЕРСТИК ПРИВЯЗАННЫХ К ЭЛЕМЕНТАМ
 * @param $item_type
 * @param $item_id
 * @return array|bool
 */
function fn_uns__get_features_items($item_type, $item_id)
{
    if (!fn_uns_check_item_types($item_type) || (!is_numeric($item_id) || $item_id == 0)) return false;

    $fields = array(
        '?:features.feature_id',
        '?:features.feature_name',
        '?:features.feature_no',
        '?:features.feature_status',
        '?:features.feature_position',
        '?:features.feature_comment',
        '?:features.uc_id',
        '?:features__and__items.fi_id',
        '?:features__and__items.item_id',
        '?:features__and__items.item_type',
        '?:features__and__items.feature_value',
        '?:features__and__items.u_id',
        '?:units.u_name',

    );

    $sorting_schemas = array(
        'views' => array(
            '?:features.feature_status' => 'asc',
            '?:features.feature_position' => 'asc',
            '?:features.feature_name' => 'asc',
            '?:features.feature_no' => 'asc',
        )
    );

    $sortings = array(
        'status' => '?:materials.material_status',
        'position' => '?:materials.material_position',
        'name' => '?:materials.material_name',
    );

    $directions = array(
        'asc' => 'asc',
        'desc' => 'desc'
    );


    $total = 0;
    $condition = $join = $limit = $sorting = '';

    $join .= db_quote(" RIGHT JOIN ?:features__and__items ON (?:features__and__items.feature_id = ?:features.feature_id AND ?:features__and__items.item_id = ?i AND ?:features__and__items.item_type = ?s ) ", $item_id, $item_type);
    $join .= db_quote(" LEFT JOIN ?:units ON (?:units.u_id = ?:features__and__items.u_id) ");

    //***

    if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
        $params['sorting_schemas'] = 'views';
    }

    // Reverse sorting (for usage in view)
    $params['sort_order'] = ($params['sort_order'] == 'asc') ? 'desc' : 'asc';

    if (is__array($sorting_schemas[$params['sorting_schemas']])) {
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k => $v) {
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM ?:features $join WHERE 1 $condition $sorting $limit", "feature_id");

    // Целевые единицы измерения
    if (is__array($data)) {
        foreach ($data as $k => $v) {
            list($target_units) = fn_uns__get_units(array('uc_id' => $v['uc_id'], 'simple' => true, 'sorting_schemas' => 'select'));
            $data[$k]['target_units'] = $target_units;
        }
    }

    return $data;
}


/**
 * ДОБАВЛЕНИЕ/ОБНОВЛЕНИЕ ХАРАКТЕРСТИК ПРИВЯЗАННЫХ К ЭЛЕМЕНТАМ
 * @param $item_type
 * @param $item_id
 * @param $data
 * @return bool
 */
function fn_uns__upd_features_items($item_type, $item_id, $data)
{
    if (!fn_uns_check_item_types($item_type) || (!is_numeric($item_id) || $item_id == 0)) return false;

    if (is__array($data)) {
        $v_ids = array();
        foreach ($data as $v) {
            $v_data = array(
                'item_id' => $item_id,
                'item_type' => $item_type,
                'feature_id' => $v['feature_id'],
                'feature_value' => $v['feature_value'],
                'u_id' => $v['u_id'],
            );
            if ($v["fi_id"] == 0) {
                // Добавить новый вариант
                if (is__more_0($v['feature_value'])) {
                    $v_ids[] = db_query(UNS_DB_PREFIX . "INSERT INTO ?:features__and__items ?e", $v_data);
                }
            } elseif ($v["fi_id"] > 0) {
                // Обновить вариант
                if (is__more_0($v['feature_value'])) {
                    db_query(UNS_DB_PREFIX . "UPDATE ?:features__and__items SET ?u WHERE fi_id = ?i", $v_data, $v["fi_id"]);
                }
                $v_ids[] = $v["fi_id"];
            }
        }
        if (is__array($v_ids)){
            db_query(UNS_DB_PREFIX . "DELETE FROM ?:features__and__items WHERE item_type = ?s AND item_id = ?i AND fi_id not in (?n)", $item_type, $item_id, $v_ids);
        }else{
            db_query(UNS_DB_PREFIX . "DELETE FROM ?:features__and__items WHERE item_type = ?s AND item_id = ?i ", $item_type, $item_id);
        }

    }
    return true;
}

/**
 * УДАЛЕНИЕ ХАРАКТЕРСТИК ПРИВЯЗАННЫХ К ЭЛЕМЕНТАМ
 * @param $item_type
 * @param $item_id
 * @internal param $data
 * @return bool
 */
function fn_uns__del_features_items($item_type, $item_id){
    if (!($item_id = to__array($item_id)) || !fn_uns_check_item_types($item_type)) return false;
    $cond  = '';
    $cond .= db_quote(" AND item_type = ?s ", $item_type);
    $cond .= db_quote(" AND item_id in (?n) ", $item_id);
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:features__and__items WHERE 1 $cond ");
    return true;
}





/**
 * ГЕНЕРАТОР ДАННЫХ ДЛЯ ФОРМЫ ХАРАКТЕРИСТИК
 * @param $type
 * @param $id
 * @param $view
 * @return array|bool
 */
function fn_uns_generate_data_for_assign_features($type, $id, &$view) {
    list($all_features) = fn_uns__get_features(array('all' => true));
    $view->assign('af__all_features', $all_features);

    if(!is__more_0($id)){
        return false;
    }
    $existing_features = fn_uns__get_features_items($type, $id);
    $view->assign('af__existing_features', $existing_features);
    return array("all" => $all_features, "existing" => $existing_features);
}

