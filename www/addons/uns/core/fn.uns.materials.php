<?php
//**************************************************************************
//***** КЛАССЫ МАТЕРИАЛОВ **************************************************
//**************************************************************************

function fn_uns__get_materials_classes_old($id = null)
{
    $res = null;
    $cond = '';
    if (is_numeric($id)) {
        $cond = " AND mclass_id = {$id} ";
    }
    $items = db_get_hash_array(UNS_DB_PREFIX . "
                    SELECT
                        mclass_id
                      , mclass_name
                      , mclass_status
                      , mclass_position
                    FROM ?:material_classes
                    WHERE 1 ?p
                    ORDER BY mclass_status asc, mclass_position asc, mclass_name asc
                    ", 'mclass_id', $cond);
    $res = $items;
    return $res;
}

function fn_uns__get_materials_classes($params = array(), $items_per_page = 0){
    $default_params = array(
        'mclass_id' => 0,
        'only_active' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_table = "?:material_classes";

    $fields = array(
        "$m_table.mclass_id",
        "$m_table.mclass_name",
        "$m_table.mclass_status",
        "$m_table.mclass_position",
    );

    $sorting_schemas = array(
        'view' => array(
            "$m_table.mclass_status" => 'asc',
            "$m_table.mclass_position" => 'asc',
            "$m_table.mclass_name" => 'asc',
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if ($params['mclass_id_array'] = to__array($params['mclass_id'])) {
        $condition .= db_quote(" AND $m_table.mclass_id in (?n)", $params['mclass_id_array']);
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND $m_table.mclass_status in ('A') ");
    } else {
        $condition .= db_quote(" AND $m_table.mclass_status in ('A', 'D') ");
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

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "mclass_id");

    return array($data, $params, $total);
}


function fn_uns__upd_materials_classes($id, $data)
{
    if (!is_array($data) || empty($data)) return false;
    else {
        if ($id && db_get_field(UNS_DB_PREFIX . "SELECT mclass_id FROM ?:material_classes WHERE mclass_id = ?i", $id)) {
            db_query(UNS_DB_PREFIX . "UPDATE ?:material_classes SET ?u WHERE mclass_id = ?i", $data, $id);
        } else {
            $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:material_classes ?e", $data);
        }
    }
    return $id;
}


function fn_uns__del_materials_classes($ids) {
    if (!($ids = fn_check_before_deleting("del_materials_classes", $ids))) return false;
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:material_classes WHERE mclass_id IN (?n)", $ids);
    return true;
}


//**************************************************************************
//***** КАТЕГОРИИ МАТЕРИАЛОВ ***********************************************
//**************************************************************************
/**
 * ПОЛУЧИТЬ ПОЛНЫЕ ПУТИ КАТЕГОРИЙ
 * @param array $params
 * @return array
 */
function fn_uns__get_materials_category_path ($params = array()){
    $default_params = array(
        'category_id'    => 0,
        'material_id'    => 0,
        'delimiter'    => " :: ",
    );

    $params = array_merge($default_params, $params);

    $fields = array(
        '?:material_categories.mcat_id',
        '?:material_categories.mcat_name',
        '?:material_categories.mcat_id_path',
    );

    $condition = $join = $limit = $sorting = '';

    if (is__array($params['material_id'])){
        $params['category_id'] = db_get_fields(UNS_DB_PREFIX . "SELECT mcat_id FROM ?:materials WHERE material_id in (?n)", $params['material_id']);
        $params['category_id'] = array_unique($params['category_id']);
    }

    if (!is__array($params['category_id'])) return false;

    $condition .= db_quote(" AND ?:material_categories.mcat_id in (?n)", $params['category_id']);

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM ?:material_categories $join WHERE 1 $condition $sorting $limit", "mcat_id");

    if (!is__array($data)) return false;

    $id_paths = array();
    foreach ($data as $k=>$v){
        $id_paths = array_merge($id_paths, explode('/', $v['mcat_id_path']));
    }
    $mcat_ids = array_unique($id_paths);

    $condition = db_quote(" AND ?:material_categories.mcat_id in (?n)", $mcat_ids);
    $mcat_names = db_get_hash_array(UNS_DB_PREFIX . "SELECT ?:material_categories.mcat_id, ?:material_categories.mcat_name FROM ?:material_categories $join WHERE 1 $condition", "mcat_id");
    foreach ($data as $k=>$v){
        $path = array();
        foreach (explode("/", $v['mcat_id_path']) as $p){
            $path[] = $mcat_names[$p]['mcat_name'];
        }
        $data[$k]['category_path'] = implode($params['delimiter'], $path);
    }

    return array($data, $params);
}


function fn_uns__get_materials_categories($params = array())
{
    $default_params = array(
        'mcat_id' => 0,
        'mclass_id_exclude' => 0,
        'visible' => false,
        'only_active' => false,
        'current_category_id' => 0,
        'simple' => true,
        'plain' => false,
        'sort_order' => 'desc',
        'limit' => 0,
        'sort_by' => 'position',
        'item_ids' => '',
        'group_by_level' => true,
        'with_q_ty' => true,
        'sorting_schemas' => 'view',
        'category_delimiter' => '/'
    );

    $params = array_merge($default_params, $params);

    $sortings = array(
        'name' => '?:material_categories.mcat_name',
        'position' => array(
            '?:material_categories.mcat_position',
        )
    );

    $directions = array(
        'asc' => 'asc',
        'desc' => 'desc'
    );

    $fields = array(
        '?:material_categories.mcat_id',
        '?:material_categories.mcat_parent_id',
        '?:material_categories.mcat_name',
        '?:material_categories.mcat_id_path',
        '?:material_categories.mcat_position',
        '?:material_categories.mcat_status'
    );

    $sorting_schemas = array(
        'view' => array(
            '?:material_categories.mcat_position' => 'asc',
            '?:material_categories.mcat_name' => 'asc',
        ),
        'mcat_position_accounting' => array(
            '?:material_categories.mcat_position_accounting' => 'asc',
            '?:material_categories.mcat_name' => 'asc',
        )
    );


    $condition = '';

    $_statuses = array('A');
    if (!$params['only_active']) {
        $_statuses[] = 'D';
    }
    $condition .= db_quote(" AND ?:material_categories.mcat_status IN (?a)", $_statuses);

    if (empty($params['mcat_include_target'])  and is__more_0($params['mcat_id'])) {
        $from_id_path = db_get_field(UNS_DB_PREFIX . "SELECT mcat_id_path FROM ?:material_categories WHERE mcat_id = ?i", $params['mcat_id']);
        if ($params['include_child']){
            $condition .= db_quote(" AND ?:material_categories.mcat_id_path LIKE ?l", "$from_id_path%");
        }else{
            $condition .= db_quote(" AND ?:material_categories.mcat_id_path LIKE ?l", "$from_id_path/%");
        }
    }

    if (!empty($params['mcat_include_target']) and is__more_0($params['mcat_id'])) {
        $condition .= db_quote(" AND ?:material_categories.mcat_id = ?i", $params["mcat_id"]);
    }

    if ($params['mcat_id_exclude'] = to__array($params['mcat_id_exclude'])){
        $from_id_path = db_get_field(UNS_DB_PREFIX . "SELECT mcat_id_path FROM ?:material_categories WHERE mcat_id in (?n)", $params['mcat_id_exclude']);
        $condition .= db_quote(" AND ?:material_categories.mcat_id_path not LIKE ?l", "$from_id_path/%");
        $condition .= db_quote(" AND ?:material_categories.mcat_id not in (?n)", $params['mcat_id_exclude']);
    }



    if (!empty($params['item_ids'])) {
        if (!is_array($params['item_ids'])) $params['item_ids'] = (array)$params['item_ids'];
        $condition .= db_quote(' AND ?:material_categories.mcat_id IN (?n)', $params['item_ids']);
    }




    $limit = $join = $group_by = $sorting = '';

    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }

    if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
        $params['sort_order'] = 'asc';
    }

    if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
        $params['sort_by'] = 'position';
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])){
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    if ($params['with_q_ty']){
        $fields[] = 'ifnull(q_materials.q,0) as q_ty';
        $join .= db_quote(" LEFT JOIN (SELECT count(?:materials.material_id) as q, ?:materials.mcat_id FROM ?:materials GROUP BY ?:materials.mcat_id) as q_materials ON (q_materials.mcat_id = ?:material_categories.mcat_id)");
    }

    $categories = db_get_hash_array(UNS_DB_PREFIX . 'SELECT ' . implode(',', $fields) . " FROM ?:material_categories $join WHERE 1 $condition $group_by $sorting $limit", 'mcat_id');

    if (empty($categories)) {
        return array(array());
    }

    $tmp = array();
    if ($params['simple'] == true || $params['group_by_level'] == true) {
        $child_for = array_keys($categories);
        $where_condition = !empty($params['except_id']) ? db_quote(' AND mcat_id != ?i', $params['except_id']) : '';
        $has_children = db_get_hash_array(UNS_DB_PREFIX . "SELECT mcat_id, mcat_parent_id FROM ?:material_categories WHERE mcat_parent_id IN(?n) ?p", 'mcat_parent_id', $child_for, $where_condition);
    }
    // Group categories by the level (simple)
    if ($params['simple'] == true) {
        foreach ($categories as $k => $v) {
            $v['level'] = substr_count($v['mcat_id_path'], '/');
            if ((!empty($params['current_category_id']) || $v['level'] == 0) && isset($has_children[$k])) {
                $v['has_children'] = $has_children[$k]['mcat_id'];
            }
            $tmp[$v['level']][$v['mcat_id']] = $v;
        }
    } elseif ($params['group_by_level'] == true) {
        // Group categories by the level (simple) and literalize path
        foreach ($categories as $k => $v) {
            $path = explode('/', $v['mcat_id_path']);
            $category_path = array();
            foreach ($path as $__k => $__v) {
                $category_path[$__v] = @$categories[$__v]['mcat_name'];
            }
            $v['category_path'] = implode($params['category_delimiter'], $category_path);
            $v['level'] = substr_count($v['mcat_id_path'], "/");
            if ((!empty($params['current_category_id']) || $v['level'] == 0) && isset($has_children[$k])) {
                $v['has_children'] = $has_children[$k]['mcat_id'];
            }
            $tmp[$v['level']][$v['mcat_id']] = $v;
        }
    } else {
        $tmp = $categories;
    }

    ksort($tmp, SORT_NUMERIC);
    $tmp = array_reverse($tmp);

    foreach ($tmp as $level => $v) {
        foreach ($v as $k => $data) {
            if (isset($data['mcat_parent_id']) && isset($tmp[$level + 1][$data['mcat_parent_id']])) {
                $tmp[$level + 1][$data['mcat_parent_id']]['subcategories'][] = $tmp[$level][$k];
                unset($tmp[$level][$k]);
            }
        }
    }

    if ($params['group_by_level'] == true) {
        $tmp = array_pop($tmp);
    }

    if ($params['plain'] == true) {
        $tmp = fn_multi_level_to_plain($tmp, 'subcategories');
    }

    if (!empty($params['add_root'])) {
        array_unshift($tmp, array('mcat_id' => 0, 'mcat_name' => $params['add_root']));
    }

    return array($tmp, $params);
}


function fn_uns__upd_materials_category($id = null, $data)
{
    if (is_numeric($id) && $id > 0) {
        // ОБНОВИТЬ
        if (is_array($data) && !empty($data)) {

            $new_parent_id = $data['mcat_parent_id'];
            $new_parent_path = db_get_field(UNS_DB_PREFIX . "SELECT mcat_id_path FROM ?:material_categories WHERE mcat_id = ?i", $new_parent_id);
            $current_path = db_get_field(UNS_DB_PREFIX . "SELECT mcat_id_path FROM ?:material_categories WHERE mcat_id = ?i", $id);

            if (!empty($new_parent_path) && !empty($current_path)) {
                db_query(UNS_DB_PREFIX . "UPDATE ?:material_categories SET mcat_parent_id = ?i, mcat_id_path = ?s WHERE mcat_id = ?i", $new_parent_id, "$new_parent_path/$id", $id);
                db_query(UNS_DB_PREFIX . "UPDATE ?:material_categories SET mcat_id_path = CONCAT(?s, SUBSTRING(mcat_id_path, ?i)) WHERE mcat_id_path LIKE ?l", "$new_parent_path/$id/", strlen($current_path . '/') + 1, "$current_path/%");
            } elseif (empty($new_parent_path) && !empty($current_path)) {
                db_query(UNS_DB_PREFIX . "UPDATE ?:material_categories SET mcat_parent_id = ?i, mcat_id_path = ?i WHERE mcat_id = ?i", $new_parent_id, $id, $id);
                db_query(UNS_DB_PREFIX . "UPDATE ?:material_categories SET mcat_id_path = CONCAT(?s, SUBSTRING(mcat_id_path, ?i)) WHERE mcat_id_path LIKE ?l", "$id/", strlen($current_path . '/') + 1, "$current_path/%");
            }
            db_query(UNS_DB_PREFIX . "UPDATE ?:material_categories SET ?u WHERE mcat_id = ?i", $data, $id);
        }
    } else {
        // ДОБАВИТЬ
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:material_categories ?e", $data);
        if (empty($id)) {
            return false;
        }
        $parent_id = intval($data['mcat_parent_id']);
        if ($parent_id == 0) {
            $id_path = $id;
        } else {
            $id_path = db_get_row(UNS_DB_PREFIX . "SELECT mcat_id_path FROM ?:material_categories WHERE mcat_id = ?i", $parent_id);
            $id_path = $id_path['mcat_id_path'] . '/' . $id;
        }
        db_query(UNS_DB_PREFIX . 'UPDATE ?:material_categories SET ?u WHERE mcat_id = ?i', array('mcat_id_path' => $id_path), $id);
    }
    return $id;
}


function fn_uns__del_materials_category($ids) {
    if (!($ids = fn_check_before_deleting("del_materials_category", $ids))) return false;

    foreach($ids as $id){
        $id_path  = db_get_field(UNS_DB_PREFIX . "SELECT mcat_id_path FROM ?:material_categories WHERE mcat_id = ?i", $id);
        $mcat_ids = db_get_fields(UNS_DB_PREFIX . "SELECT mcat_id FROM ?:material_categories WHERE mcat_id = ?i OR mcat_id_path LIKE ?l", $id, "$id_path/%");
        if (is__array($mcat_ids)){
            db_query(UNS_DB_PREFIX . "DELETE FROM ?:material_categories WHERE mcat_id in (?n)", $mcat_ids);
        }
    }

    return true;
}


//**************************************************************************
//***** МАТЕРИАЛЫ **********************************************************
//**************************************************************************
// Форматированное имя материала
function fn_uns__get_format_material_name($data, $weight=0, $type=1){
    $res = '';
    switch ($type){
        case "1":
            $type_casting = "";
            $str_weight =  "";
//            $str_weight =  " (" . fn_fvalue($weight) . " кг)";
//            $str_weight =  ($weight)?" (" . fn_fvalue($weight) . " кг)":"";
/*            if ($data["type_casting"] == "S"){
                $type_casting = " С ";
            }elseif ($data["type_casting"] == "C"){
                $type_casting = " Ч ";
            }*/
//            $res = $data['material_name'] . " " . ((strlen($data['material_no']))?"[{$data['material_no']}]":"") . $type_casting . $str_weight;
            $res = ((strlen($data['material_no']))?"[{$data['material_no']}]":"") . " " . $data['material_name'];
        break;
    }
    return $res;
}


/**
 * @param array $params
 *          with_accounting - получить информацию по учету
 * @param int   $items_per_page
 * @return array|bool
 */
function fn_uns__get_materials($params = array(), $items_per_page = 0)
{
    $default_params = array(
        'material_id' => 0,
        'mclass_id' => 0,
        'mcat_id' => 0,
        'mcat_path' => false,
        'with_accounting' => false,
        'format_name' => false,
        'only_active' => false,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $fields = array(
        '?:materials.material_id',
        '?:materials.mclass_id',
        '?:material_classes.mclass_name',
        '?:material_classes.mclass_status',
        '?:material_classes.mclass_position',
        '?:materials.mcat_id',
        '?:material_categories.mcat_name',
        '?:material_categories.mcat_status',
        '?:material_categories.mcat_position',
        '?:materials.material_no',
        '?:materials.material_name',
        '?:materials.material_name_accounting',
        '?:materials.type_casting',
        '?:materials.material_status',
        '?:materials.material_position',
        '?:materials.material_comment',
        '?:materials.material_comment_1',
    );

    $sorting_schemas = array(
        'view' => array(
            '?:material_classes.mclass_status'  => 'asc',
            '?:material_classes.mclass_position'=> 'asc',
            '?:material_classes.mclass_name'    => 'asc',

            '?:material_categories.mcat_status'  => 'asc',
            '?:material_categories.mcat_position'=> 'asc',
            '?:material_categories.mcat_name'    => 'asc',

            '?:materials.material_position'      => 'asc',
            '?:materials.material_name'          => 'asc',
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';

    if (strlen(trim__data($params['material_name']))) {
        $condition .= db_quote(" AND (?:materials.material_name LIKE ?l)", "%" . trim__data($params['material_name']) . "%" );
    }

    if (strlen(trim__data($params['material_no']))) {
        $condition .= db_quote(" AND (?:materials.material_no LIKE ?l)", "%" . trim__data($params['material_no']) . "%" );
    }

    if ($params['material_id_array'] = to__array($params['material_id'])){
        $condition .= db_quote(" AND ?:materials.material_id in (?n)", $params['material_id_array']);
    }

    if ($params['mclass_id_array'] = to__array($params['mclass_id'])) {
        $condition .= db_quote(" AND ?:materials.mclass_id in (?n)", $params['mclass_id_array']);
    }

    if ($params['mcat_id_array'] = to__array($params['mcat_id'])) {
        $condition .= db_quote(" AND ?:materials.mcat_id in (?n) ", $params['mcat_id_array']);
    }

    if (in_array($params['type_casting'], array('S', 'C'))) {
        $condition .= db_quote(" AND ?:materials.type_casting = ?s ", $params['type_casting']);
    }

    if ($params['only_active']) {
        $condition .= db_quote(" AND ?:materials.material_status in ('A') ");
    } else {
        $condition .= db_quote(" AND ?:materials.material_status in ('A', 'D') ");
    }

    if (in_array($params['material_status'],array('A', 'D'))) {
        $condition .= db_quote(" AND ?:materials.material_status in ('A') ");
    } else {
        $condition .= db_quote(" AND ?:materials.material_status in ('A', 'D') ");
    }

    //*********************
    $join .= db_quote(" LEFT JOIN ?:material_categories ON (?:material_categories.mcat_id = ?:materials.mcat_id AND ?:material_categories.mcat_status = 'A') ");
    $join .= db_quote(" LEFT JOIN ?:material_classes ON (?:material_classes.mclass_id = ?:materials.mclass_id AND ?:material_classes.mclass_status = 'A') ");


    if (!empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(*) FROM ?:materials $join WHERE 1 $condition");
        $limit = fn_paginate($params['page'], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])){
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM ?:materials $join WHERE 1 $condition $sorting $limit", "material_id");

    if (!is__array($data)) return false;

    // В добавить поле "ПОЛНЫЙ ПУТЬ КАТЕГОРИИ МАТЕРИАЛОВ"
    if ($params['mcat_path']){
        list($cat_paths) = fn_uns__get_materials_category_path (array('material_id' => array_keys($data)));
        if (is__array($cat_paths)){
            foreach ($data as $k=>$v){
                $data[$k]['category_path'] = $cat_paths[$v['mcat_id']]['category_path'];
            }
        }
    }

    // Получить информацию по учету материала
    if ($params['with_accounting']){
        $accounting_data = fn_uns__get_accounting_items ("M", array_keys($data));
        if (is__array($accounting_data)){
            foreach ($data as $k=>$v){
                $accounting_data[$v['material_id']]['weight'] = $accounting_data[$v['material_id']]['weights']["M"][0]['value'];
                $data[$k]['accounting_data'] = $accounting_data[$v['material_id']];
            }
        }
    }

    if ($params['format_name']){
        foreach ($data as $k=>$v){
            $data[$k]['format_name'] = fn_uns__get_format_material_name($v, $v['accounting_data']['weight']);
        }
    }

    if ($params['with_options']){
        foreach ($data as $k=>$v){
            $data[$k]['options'] = fn_uns__get_options_items("M", $k);
            $options_as_str = array();
            $options_as_str_names = array();
            foreach ($data[$k]['options'] as $o=>$o_v){
                $options_as_str[] = $o_v["variants"][$o_v["ov_id"]]["ov_value"];
                $options_as_str_names[] = str_replace("П/М ЗАГОТОВКА ", "", $o_v["option_name"]);
            }
            if (is__array($options_as_str)){
                $data[$k]['options_as_str'] = implode("/", $options_as_str);
            }
            if (is__array($options_as_str_names)){
                $data[$k]['options_as_str_names'] = implode(" / ", $options_as_str_names);
            }
        }
    }

    return array($data, $params, $total);
}

function fn_uns__upd_material($id=0, $data){
    if (!is__array($data)) return false;

    if (is__more_0($id) && db_get_field(UNS_DB_PREFIX . "SELECT material_id FROM ?:materials WHERE material_id = ?i", $id)){
        db_query(UNS_DB_PREFIX . 'UPDATE ?:materials SET ?u WHERE material_id = ?i', $data, $id);
    }else{
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:materials ?e", $data);
    }

    // Обновить характеристики
    if (is__array($data['features'])){
        fn_uns__upd_features_items('M', $id, $data['features']);
    }

    // Обновить параметры
    if (is__array($data['options'])){
        fn_uns__upd_options_items('M', $id, $data['options']);
    }

    // Обновить параметры
    if (is__array($data['accounting'])){
        fn_uns__upd_accounting_items('M', $id, $data['accounting']);
    }

    return $id;
}

function fn_uns__del_materials($ids){
    if (!($ids = fn_check_before_deleting("del_materials", $ids))) return false;

    // Удаление МАТЕРИАЛА
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:materials WHERE material_id IN (?n)", $ids);

    // Удаление УЧЕТА
    fn_uns__del_accounting_items('M', $ids);

    // Удаление ХАРАКТЕРИСТИК
    fn_uns__del_features_items('M', $ids);

    // Удаление ПАРАМЕТРОВ
    fn_uns__del_options_items('M', $ids);

    return true;
}

function fn_uns_generate_data_for_materials ($id, &$view){
    // Категории материалов
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true));
    $view->assign('am__mcategories_plain', $mcategories_plain);

    if (is__more_0($id)){
        // Исходные материалы
        $p = array(
            'detail_id'          => to__array($id),
            'material_info'      => true,
            'with_material_units'=> true,
        );
        $existing_materials  = fn_uns__get_details_materials($p);
        if (is__array($existing_materials[$id])){
            foreach ($existing_materials[$id] as $k_em => $v_em) {
                // Запрос список материалов по выбранной категории
                $p = array(
                    'mcat_id' => $v_em['mcat_id'],
                    'with_accounting' => true,
                    'format_name' => true
                );
                list ($materials) = fn_uns__get_materials($p);
                $existing_materials[$id][$k_em]['materials'] = $materials;
            }
        }
        $view->assign('am__existing_materials', $existing_materials[$id]);
    }
    return true;
}

