<?php

/**
 * ЗАПРОС ПАРАМЕТРОВ старая версия
 * @param null $id
 * @return array
 */
function fn_uns__get_options_old($id = null)
{
    $res = null;
    $cond = '';
    if (is_numeric($id)) {
        $cond = " AND option_id = {$id} ";
    }
    $items = db_get_hash_array(UNS_DB_PREFIX . "
                    SELECT
                        ?:options.option_id
                      , ?:options.option_name
                      , ?:options.option_no
                      , ?:options.option_status
                      , ?:options.option_position
                      , ?:options.option_comment
                    FROM ?:options
                    WHERE 1 ?p
                    ORDER BY option_position asc, option_name asc, option_no asc
                    ", 'option_id', $cond);
    $res = $items;
    return $res;
}

/**
 * ЗАПРОС ВАРИАНТОВ ПАРАМЕТРА старая версия
 * @param $option_id
 * @param null $ov_id
 * @return array
 */
function fn_uns__get_option_variants_old($option_id, $ov_id = null)
{
    $res = null;
    $cond = " AND option_id = {$option_id} ";
    if (is_numeric($ov_id)) {
        $cond .= " AND ov_id = {$ov_id} ";
    }
    $items = db_get_hash_array(UNS_DB_PREFIX . "
                    SELECT
                        ?:option_variants.ov_id
                      , ?:option_variants.ov_value
                      , ?:option_variants.ov_status
                      , ?:option_variants.ov_position
                      , ?:option_variants.u_id
                    FROM ?:option_variants
                    WHERE 1 ?p
                    ORDER BY ov_position asc, ov_value asc
                    ", 'ov_id', $cond);
    $res = $items;
    return $res;
}


/**
 * ЗАПРОС ПАРАМЕТРОВ
 * @param array $params
 * @param int $items_per_page
 * @internal param null $id
 * @return array
 */
function fn_uns__get_options($params = array(), $items_per_page = 0)
{
    $default_params = array(
        'option_id' => 0,
        'only_active' => false,
        'with_variants' => false,
        'all' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $fields = array(
        '?:options.option_id',
        '?:options.option_name',
        '?:options.option_no',
        '?:options.option_status',
        '?:options.option_position',
        '?:options.option_comment',
    );

    $sorting_schemas = array(
        'view' => array(
            '?:options.option_status' => 'asc',
            '?:options.option_position' => 'asc',
            '?:options.option_name' => 'asc',
        )
    );

    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (!empty($params['option_id'])) {
        if (!is__array($params['option_id'])) $params['option_id'] = (array)$params['option_id'];
        $condition .= db_quote(" AND ?:options.option_id in (?n)", $params['option_id']);
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND ?:options.option_status in ('A') ");
    } else {
        $condition .= db_quote(" AND ?:options.option_status in ('A', 'D') ");
    }

    if (!$params['all'] && !empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(option_id) FROM ?:options $join WHERE 1 $condition");
        $limit = fn_paginate($params['page'], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])) {
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k => $v) {
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM ?:options $join WHERE 1 $condition $sorting $limit", "option_id");

    // Добавить варианты к характеристикам
    if ($params['with_variants'] && is_array($data) && !empty($data)) {
        $p = array(
            'option_id' => array_keys($data),
            'all' => true,
            'group_by_options' => true,
        );
        list($variants) = fn_uns__get_option_variants($p);

        foreach ($data as $k => $v) {
            if (isset($variants[$k])) {
                $data[$k]['variants'] = $variants[$k];
            }
        }
    }

    return array($data, $params, $total);
}


/**
 * ЗАПРОС ВАРИАНТОВ ПАРАМЕТРА
 * @param array $params
 * @param int $items_per_page
 * @internal param null $id
 * @return array
 */
function fn_uns__get_option_variants($params = array(), $items_per_page = 0)
{
    $default_params = array(
        'option_id' => 0,
        'variant_id' => 0,
        'group_by_options' => false,
        'only_active' => false,
        'all' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $fields = array(
        '?:option_variants.ov_id',
        '?:option_variants.option_id',
        '?:option_variants.ov_value',
        '?:option_variants.ov_status',
        '?:option_variants.ov_position',
        '?:option_variants.u_id',
        '?:units.u_name',
    );

    $sorting_schemas = array(
        'view' => array(
            '?:option_variants.ov_status' => 'asc',
            '?:option_variants.ov_position' => 'asc',
            '?:option_variants.ov_value' => 'asc',
        ),
        'select' => array(
            '?:option_variants.ov_status' => 'asc',
            '?:option_variants.ov_position' => 'asc',
            '?:option_variants.ov_value' => 'asc',
        )
    );

    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (!empty($params['option_id'])) {
        if (!is_array($params['option_id'])) $params['option_id'] = (array)$params['option_id'];
        $condition .= db_quote(" AND ?:option_variants.option_id in (?n)", $params['option_id']);
    }

    if (!empty($params['variant_id'])) {
        if (!is_array($params['variant_id'])) $params['variant_id'] = (array)$params['variant_id'];
        $condition .= db_quote(" AND ?:option_variants.ov_id in (?n)", $params['variant_id']);
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND ?:option_variants.ov_status in ('A') ");
    } else {
        $condition .= db_quote(" AND ?:option_variants.ov_status in ('A', 'D') ");
    }

    if (!$params['all'] && !empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(ov_id) FROM ?:option_variants $join WHERE 1 $condition");
        $limit = fn_paginate($params['page'], $total, $items_per_page);
    }

    $join .= db_quote(" LEFT JOIN ?:units ON (?:units.u_id = ?:option_variants.u_id) ");


    if (is__array($sorting_schemas[$params['sorting_schemas']])) {
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k => $v) {
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM ?:option_variants $join WHERE 1 $condition $sorting $limit", "ov_id");

    if ($params['group_by_options'] && is_array($data) && !empty($data)) {
        $temp = array();
        foreach ($data as $k => $v) {
            $temp[$v['option_id']][$k] = $v;
        }
        $data = $temp;
    }

    return array($data, $params, $total);
}


/**
 * ДОБАВЛЕНИЕ/ОБНОВЛЕНИЕ ПАРАМЕТРОВ
 * @param $id
 * @param $data
 * @return integer $id
 */
function fn_uns__upd_option($id, $data)
{
    if (!is__array($data)) return false;
    else {
        $new_variants = $data['variants'];

        // Обновить параметр
        unset($data['variants']);
        if ($id && db_get_field(UNS_DB_PREFIX . "SELECT option_id FROM ?:options WHERE option_id = ?i", $id)) {
            db_query(UNS_DB_PREFIX . "UPDATE ?:options SET ?u WHERE option_id = ?i", $data, $id);
        } else {
            $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:options ?e", $data);
        }

        // Обновить варианты параметра
        $v_ids = array();
        if (!is_array($new_variants) || empty($new_variants)) {
            // Удалить все варианты
            db_query(UNS_DB_PREFIX . "DELETE FROM ?:option_variants WHERE option_id = ?i)", $id);
        } elseif (is_array($new_variants) && !empty($new_variants)) {
            foreach ($new_variants as $new_v) {
                if ($new_v["ov_id"] == 0) {
                    if (strlen(trim($new_v["ov_value"]))) {
                        // Добавить новый вариант
                        $v_data = array(
                            'ov_value' => $new_v['ov_value'],
                            'ov_status' => $new_v['ov_status'],
                            'ov_position' => $new_v['ov_position'],
                            'u_id' => $new_v['u_id'],
                            'option_id' => $id,
                        );
                        $v_ids[] = db_query(UNS_DB_PREFIX . "INSERT INTO ?:option_variants ?e", $v_data);
                    }
                } elseif ($new_v["ov_id"] > 0) {
                    // Обновить вариант
                    $v_data = array(
                        'ov_value' => (strlen(trim($new_v['ov_value'])) > 0) ? trim($new_v['ov_value']) : "-empty-",
                        'ov_status' => $new_v['ov_status'],
                        'ov_position' => $new_v['ov_position'],
                        'u_id' => $new_v['u_id'],
                    );
                    db_query(UNS_DB_PREFIX . "UPDATE ?:option_variants SET ?u WHERE ov_id = ?i", $v_data, $new_v["ov_id"]);
                    $v_ids[] = $new_v["ov_id"];
                }
            }
            fn_uns__del_option_variant ($id, $v_ids);
        }
    }
    return $id;
}


/**
 * УДАЛЕНИЕ ПАРАМЕТРА
 * @param $option_id
 * @param $ids
 * @return bool
 * @internal param $id
 * @internal param $unit_ids
 */
function fn_uns__del_option_variant($option_id, $ids) {
    if(!is__more_0($option_id)){
        return false;
    }

    // В данном случае (именно с ПАРАМЕТРАМИ) - $ids содержит id вариантов, которые нельзя удалить!
    // Ну, а если он пустой, тогда удалить все варианты этого параметра.
    $cond = "";
    if (is__array($ids)){
        $cond = db_quote(" AND ov_id not in (?n)", $ids);

    }

    $variant_ids_for_delete = db_get_fields(UNS_DB_PREFIX . "SELECT ov_id FROM ?:option_variants WHERE 1 AND option_id = ?i $cond", $option_id);
    if(!($variant_ids_for_delete = fn_check_before_deleting("del_option_variant", $variant_ids_for_delete))){
        return false;
    }

    db_query(UNS_DB_PREFIX . "DELETE FROM ?:option_variants WHERE ov_id IN (?n)", $variant_ids_for_delete);
    return true;
}


/**
 * УДАЛЕНИЕ ПАРАМЕТРА
 * @param $ids
 * @return bool
 * @internal param $id
 * @internal param $unit_ids
 */
function fn_uns__del_option($ids) {
    if (!($ids = fn_check_before_deleting("del_option", $ids))) return false;
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:options WHERE option_id IN (?n)", $ids);
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:option_variants WHERE option_id IN (?n)", $ids);
    return true;
}


/**
 * ЗАПРОС ПАРАМЕТРОВ ПРИВЯЗАННЫХ К ЭЛЕМЕНТАМ
 * @param $item_type
 * @param $item_id
 * @return array|bool
 */
function fn_uns__get_options_items($item_type, $item_id)
{
    if (!fn_uns_check_item_types($item_type) || !is__more_0($item_id)) return false;

    $fields = array(
        '?:options__and__items.oi_id',
        '?:options__and__items.option_id',
        '?:options__and__items.ov_id',
    );


    $total = 0;
    $condition = $join = $limit = $sorting = '';

    $condition .= db_quote(" AND ?:options__and__items.item_type = ?s AND ?:options__and__items.item_id = ?i ", $item_type, $item_id);

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM ?:options__and__items $join WHERE 1 $condition $sorting $limit", "oi_id");

    if (is__array($data)){
        $current_options = array();
        foreach ($data as $d){
            $current_options[] = $d['option_id'];
        }

        $p = array(
            'option_id' => $current_options,
            'all' => true,
            'with_variants' => true,
        );
        list($options) = fn_uns__get_options($p);
        foreach ($options as $k => $v) {
            foreach ($data as $k_d => $v_d){
                if ($v_d['option_id'] == $k){
                    $options[$k]['ov_id'] = $data[$k_d]['ov_id'];
                    $options[$k]['oi_id'] = $data[$k_d]['oi_id'];
                }
            }
        }
        $data = $options;

    } else $data = false;

    return $data;
}


/**
 * ДОБАВЛЕНИЕ/ОБНОВЛЕНИЕ ПАРАМЕТРОВ ПРИВЯЗАННЫХ К ЭЛЕМЕНТАМ
 * @param $item_type
 * @param $item_id
 * @param $data
 * @return bool
 */
function fn_uns__upd_options_items($item_type, $item_id, $data)
{
    if (!fn_uns_check_item_types($item_type) || (!is_numeric($item_id) || $item_id == 0)) return false;

    if (is_array($data) && !empty($data)) {
        $v_ids = array();
        foreach ($data as $v) {
            if (is__more_0($v['ov_id']) && is__more_0($v['option_id'])) {
                $v_data = array(
                    'item_id' => $item_id,
                    'item_type' => $item_type,
                    'option_id' => $v['option_id'],
                    'ov_id' => $v['ov_id'],
                );
                if ($v["oi_id"] == 0) {
                    // Добавить новый вариант
                    $v_ids[] = db_query(UNS_DB_PREFIX . "INSERT INTO ?:options__and__items ?e", $v_data);
                } elseif ($v["oi_id"] > 0) {
                    // Обновить вариант
                    db_query(UNS_DB_PREFIX . "UPDATE ?:options__and__items SET ?u WHERE oi_id = ?i", $v_data, $v["oi_id"]);
                    $v_ids[] = $v["oi_id"];
                }

            }
        }
        if (is__array($v_ids)){
            db_query(UNS_DB_PREFIX . "DELETE FROM ?:options__and__items WHERE item_type = ?s AND item_id = ?i AND oi_id not in (?n)", $item_type, $item_id, $v_ids);
        }else{
            db_query(UNS_DB_PREFIX . "DELETE FROM ?:options__and__items WHERE item_type = ?s AND item_id = ?i ", $item_type, $item_id);
        }
    }
    return true;
}


/**
 * УДАЛЕНИЕ ПАРАМЕТРОВ ПРИВЯЗАННЫХ К ЭЛЕМЕНТАМ
 * @param $item_type
 * @param $item_id
 * @internal param $data
 * @return bool
 */
function fn_uns__del_options_items($item_type, $item_id){
    if (!($item_id = to__array($item_id)) || !fn_uns_check_item_types($item_type)) return false;
    $cond  = '';
    $cond .= db_quote(" AND item_type = ?s ", $item_type);
    $cond .= db_quote(" AND item_id in (?n) ", $item_id);
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:options__and__items WHERE 1 $cond ");
    return true;
}


/**
 * ГЕНЕРАТОР ДАННЫХ ДЛЯ ФОРМЫ ОПЦИЙ
 * @param $type
 * @param $id
 * @param $view
 * @return array|bool
 */
function fn_uns_generate_data_for_assign_options($type, $id, &$view) {
    list($all_options) = fn_uns__get_options(array('all'           => true,
                                                   'with_variants' => true));
    $view->assign('ao__all_options', $all_options);

    if(!is__more_0($id)){
        return false;
    }

    $existing_options = fn_uns__get_options_items($type, $id);
    $view->assign('ao__existing_options', $existing_options);

    return array("all" => $all_options, "existing" => $existing_options);
}
