<?php
//**************************************************************************
//***** КАТЕГОРИИ МАТЕРИАЛОВ ***********************************************
//**************************************************************************
/**
 * ПОЛУЧИТЬ ПОЛНЫЕ ПУТИ КАТЕГОРИЙ
 * @param array $params
 * @return array
 */
function fn_uns__get_details_category_path ($params = array()){
    $default_params = array(
        'category_id'    => 0,
        'detail_id'    => 0,
        'delimiter'    => " :: ",
    );

    $params = array_merge($default_params, $params);

    $fields = array(
        '?:detail_categories.dcat_id',
        '?:detail_categories.dcat_name',
        '?:detail_categories.dcat_id_path',
    );

    $condition = $join = $limit = $sorting = '';

    if (is__array($params['detail_id'])){
        $params['category_id'] = db_get_fields(UNS_DB_PREFIX . "SELECT dcat_id FROM ?:details WHERE detail_id in (?n)", $params['detail_id']);
        $params['category_id'] = array_unique($params['category_id']);
    }

    if (!is__array($params['category_id'])) return false;

    $condition .= db_quote(" AND ?:detail_categories.dcat_id in (?n)", $params['category_id']);

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM ?:detail_categories $join WHERE 1 $condition $sorting $limit", "dcat_id");

    if (!is__array($data)) return false;

    $id_paths = array();
    foreach ($data as $k=>$v){
        $id_paths = array_merge($id_paths, explode('/', $v['dcat_id_path']));
    }
    $dcat_ids = array_unique($id_paths);

    $condition = db_quote(" AND ?:detail_categories.dcat_id in (?n)", $dcat_ids);
    $dcat_names = db_get_hash_array(UNS_DB_PREFIX . "SELECT ?:detail_categories.dcat_id, ?:detail_categories.dcat_name FROM ?:detail_categories $join WHERE 1 $condition", "dcat_id");
    foreach ($data as $k=>$v){
        $path = array();
        foreach (explode("/", $v['dcat_id_path']) as $p){
            $path[] = $dcat_names[$p]['dcat_name'];
        }
        $data[$k]['category_path'] = implode($params['delimiter'], $path);
    }

    return array($data, $params);
}


/**
 * ПОЛУЧИТЬ СПИСОК КАТЕГОРИЙ ДЛЯ ДЕТАЛЕЙ
 * @param array $params
 * @return array
 */
function fn_uns__get_details_categories($params = array()){
    $default_params = array(
        'dcat_id' => 0,
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
        'category_delimiter' => '/',
        'dcat_include_target'=>false,
        'sorting_schemas' => 'views',

    );

    $params = array_merge($default_params, $params);

    $fields = array(
        '?:detail_categories.dcat_id',
        '?:detail_categories.dcat_parent_id',
        '?:detail_categories.dcat_name',
        '?:detail_categories.dcat_id_path',
        '?:detail_categories.dcat_position',
        '?:detail_categories.dcat_status',
        '?:detail_categories.dcat_comment',
        '?:detail_categories.view_in_reports',
    );

    $sorting_schemas = array(
        'views' => array(
            '?:detail_categories.dcat_status'  => 'asc',
            '?:detail_categories.dcat_position'=> 'asc',
            '?:detail_categories.dcat_name'    => 'asc',
        )
    );

    $condition = '';

    $_statuses = array('A');
    if (!$params['only_active']) {
        $_statuses[] = 'D';
    }
    $condition .= db_quote(" AND ?:detail_categories.dcat_status IN (?a)", $_statuses);

    if (!empty($params['dcat_id'])) {
        $from_id_path = db_get_field(UNS_DB_PREFIX . "SELECT dcat_id_path FROM ?:detail_categories WHERE dcat_id = ?i", $params['dcat_id']);
        if ($params['dcat_include_target']){
            $condition .= db_quote(" AND ?:detail_categories.dcat_id_path LIKE ?l", "$from_id_path");
        }else{
            $condition .= db_quote(" AND ?:detail_categories.dcat_id_path LIKE ?l", "$from_id_path/%");
        }
    }

    if (!empty($params['item_ids'])) {
        if (!is_array($params['item_ids'])) $params['item_ids'] = (array)$params['item_ids'];
        $condition .= db_quote(' AND ?:detail_categories.dcat_id IN (?n)', $params['item_ids']);
    }

    if ($params['view_in_reports']) {
        $condition .= db_quote(" AND ?:detail_categories.view_in_reports = 'Y' ");
    }

    $limit = $join = $group_by = '';

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
        $fields[] = 'ifnull(q_details.q,0) as q_ty';
        $join .= db_quote(" LEFT JOIN (SELECT count(?:details.detail_id) as q, ?:details.dcat_id FROM ?:details GROUP BY ?:details.dcat_id) as q_details ON (q_details.dcat_id = ?:detail_categories.dcat_id)");
    }

    $categories = db_get_hash_array(UNS_DB_PREFIX . 'SELECT ' . implode(',', $fields) . " FROM ?:detail_categories $join WHERE 1 $condition $group_by $sorting $limit", 'dcat_id');

    if (empty($categories)) {
        return array(array());
    }

    $tmp = array();
    if ($params['simple'] == true || $params['group_by_level'] == true) {
        $child_for = array_keys($categories);
        $where_condition = !empty($params['except_id']) ? db_quote(' AND dcat_id != ?i', $params['except_id']) : '';
        $has_children = db_get_hash_array(UNS_DB_PREFIX . "SELECT dcat_id, dcat_parent_id FROM ?:detail_categories WHERE dcat_parent_id IN(?n) ?p", 'dcat_parent_id', $child_for, $where_condition);
    }
    // Group categories by the level (simple)
    if ($params['simple'] == true) {
        foreach ($categories as $k => $v) {
            $v['level'] = substr_count($v['dcat_id_path'], '/');
            if ((!empty($params['current_category_id']) || $v['level'] == 0) && isset($has_children[$k])) {
                $v['has_children'] = $has_children[$k]['dcat_id'];
            }
            $tmp[$v['level']][$v['dcat_id']] = $v;
        }
    } elseif ($params['group_by_level'] == true) {
        // Group categories by the level (simple) and literalize path
        foreach ($categories as $k => $v) {
            $path = explode('/', $v['dcat_id_path']);
            $category_path = array();
            foreach ($path as $__k => $__v) {
                $category_path[$__v] = @$categories[$__v]['dcat_name'];
            }
            $v['category_path'] = implode($params['category_delimiter'], $category_path);
            $v['level'] = substr_count($v['dcat_id_path'], "/");
            if ((!empty($params['current_category_id']) || $v['level'] == 0) && isset($has_children[$k])) {
                $v['has_children'] = $has_children[$k]['dcat_id'];
            }
            $tmp[$v['level']][$v['dcat_id']] = $v;
        }
    } else {
        $tmp = $categories;
    }

    ksort($tmp, SORT_NUMERIC);
    $tmp = array_reverse($tmp);

    foreach ($tmp as $level => $v) {
        foreach ($v as $k => $data) {
            if (isset($data['dcat_parent_id']) && isset($tmp[$level + 1][$data['dcat_parent_id']])) {
                $tmp[$level + 1][$data['dcat_parent_id']]['subcategories'][] = $tmp[$level][$k];
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
        array_unshift($tmp, array('dcat_id' => 0, 'dcat_name' => $params['add_root']));
    }

    return array($tmp, $params);
}


/**
 * ОБНОВИТЬ СПИСОК КАТЕГОРИЙ ДЛЯ ДЕТАЛЕЙ
 * @param null $id
 * @param $data
 * @return bool|null
 */
function fn_uns__upd_details_category($id = null, $data){
    if (is__more_0($id)) {
        // ОБНОВИТЬ
        if (is__array($data)) {

            $new_parent_id = $data['dcat_parent_id'];
            $new_parent_path = db_get_field(UNS_DB_PREFIX . "SELECT dcat_id_path FROM ?:detail_categories WHERE dcat_id = ?i", $new_parent_id);
            $current_path = db_get_field(UNS_DB_PREFIX . "SELECT dcat_id_path FROM ?:detail_categories WHERE dcat_id = ?i", $id);

            if (!empty($new_parent_path) && !empty($current_path)) {
                db_query(UNS_DB_PREFIX . "UPDATE ?:detail_categories SET dcat_parent_id = ?i, dcat_id_path = ?s WHERE dcat_id = ?i", $new_parent_id, "$new_parent_path/$id", $id);
                db_query(UNS_DB_PREFIX . "UPDATE ?:detail_categories SET dcat_id_path = CONCAT(?s, SUBSTRING(dcat_id_path, ?i)) WHERE dcat_id_path LIKE ?l", "$new_parent_path/$id/", strlen($current_path . '/') + 1, "$current_path/%");
            } elseif (empty($new_parent_path) && !empty($current_path)) {
                db_query(UNS_DB_PREFIX . "UPDATE ?:detail_categories SET dcat_parent_id = ?i, dcat_id_path = ?i WHERE dcat_id = ?i", $new_parent_id, $id, $id);
                db_query(UNS_DB_PREFIX . "UPDATE ?:detail_categories SET dcat_id_path = CONCAT(?s, SUBSTRING(dcat_id_path, ?i)) WHERE dcat_id_path LIKE ?l", "$id/", strlen($current_path . '/') + 1, "$current_path/%");
            }
            db_query(UNS_DB_PREFIX . "UPDATE ?:detail_categories SET ?u WHERE dcat_id = ?i", $data, $id);
        }
    } else {
        // ДОБАВИТЬ
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:detail_categories ?e", $data);
        if (empty($id)) {
            return false;
        }
        $parent_id = intval($data['dcat_parent_id']);
        if ($parent_id == 0) {
            $id_path = $id;
        } else {
            $id_path = db_get_row(UNS_DB_PREFIX . "SELECT dcat_id_path FROM ?:detail_categories WHERE dcat_id = ?i", $parent_id);
            $id_path = $id_path['dcat_id_path'] . '/' . $id;
        }
        db_query(UNS_DB_PREFIX . 'UPDATE ?:detail_categories SET ?u WHERE dcat_id = ?i', array('dcat_id_path' => $id_path), $id);
    }
    return $id;
}


/**
 * УДАЛИТЬ КАТЕГОРИЮ ДЕТАЛЕЙ
 * @param $ids
 * @internal param $id
 * @return array|bool
 */
function fn_uns__del_details_category($ids){
    if (!($ids = fn_check_before_deleting("del_details_category", $ids))) return false;

    foreach($ids as $id){
        $id_path = db_get_field(UNS_DB_PREFIX . "SELECT dcat_id_path FROM ?:detail_categories WHERE dcat_id = ?i", $id);
        $dcat_ids = db_get_fields(UNS_DB_PREFIX . "SELECT dcat_id FROM ?:detail_categories WHERE dcat_id = ?i OR dcat_id_path LIKE ?l", $id, "$id_path/%");
        if (is__array($dcat_ids)){
            db_query(UNS_DB_PREFIX . "DELETE FROM ?:detail_categories WHERE dcat_id in (?n) ", $dcat_ids);
        }
    }

    return true;
}

//**************************************************************************
//***** МАТЕРИАЛЫ **********************************************************
//**************************************************************************


/**
 * ПОЛУЧИТЬ ДЕТАЛЬ(И)
 * @param array $params
 * @param int $items_per_page
 * @return array
 */
function fn_uns__get_details($params = array(), $items_per_page = 0){
    $default_params = array(
        'detail_id'             => 0,
        'dcat_id'               => 0,
        'dcat_path'             => false,
        'with_accounting'       => false,
        'with_materials'        => false,
        'with_material_info'    => false,
        'only_active'           => false,
        'limit'                 => 0,
        'page'                  => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_table = "?:details";
    $j_table_detail_categories = "?:detail_categories";
    $j_table_detail_typesizes = "?:detail_typesizes";

    $fields = array(
        "$m_table.detail_id",
        "$m_table.dcat_id",
        "$m_table.detail_name",
        "$m_table.detail_no",
        "$m_table.detail_status",
        "$m_table.detail_position",
        "$m_table.detail_comment",
        "$m_table.accessory_view",
        "$m_table.accessory_manual",
        "$m_table.checked",
        "$m_table.min_rest_state",
        "$m_table.min_rest_value",

        "$j_table_detail_categories.dcat_name",
        "$j_table_detail_categories.dcat_status",
        "$j_table_detail_categories.dcat_position",

        "$j_table_detail_typesizes.dt_id",
        "ifnull($j_table_detail_typesizes.size_a,'D') as size_a",
        "ifnull($j_table_detail_typesizes.size_b,'D') as size_b",
    );

    $sorting_schemas = array(
        "view" => array(
            "$j_table_detail_categories.dcat_status"        => "asc",
            "$j_table_detail_categories.dcat_position"      => "asc",
            "$j_table_detail_categories.dcat_name"          => "asc",

            "$m_table.detail_status"        => "asc",
            "$m_table.detail_position"      => "asc",
            "$m_table.detail_name"          => "asc",
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = "";

    if (strlen(trim__data($params["detail_name"]))) {
        $condition .= db_quote(" AND ($m_table.detail_name LIKE ?l)", "%" . trim__data($params["detail_name"]) . "%" );
    }

    if (strlen(trim__data($params["detail_no"]))) {
        $condition .= db_quote(" AND ($m_table.detail_no LIKE ?l)", "%" . trim__data($params["detail_no"]) . "%" );
    }

    if ($params["detail_id_array"] = to__array($params["detail_id"])){
        $condition .= db_quote(" AND $m_table.detail_id in (?n)", $params["detail_id_array"]);
    }

    if ($params["dcat_id_array"] = to__array($params["dcat_id"])){
        $condition .= db_quote(" AND $m_table.dcat_id in (?n)", $params["dcat_id_array"]);
    }

    if ($params["only_active"]) {
        $condition .= db_quote(" AND $m_table.detail_status in ('A') ");
    } else {
        $condition .= db_quote(" AND $m_table.detail_status in ('A', 'D') ");
    }

    if (strlen(trim__data($params["detail_status"]))) {
        $condition .= db_quote(" AND ($m_table.detail_status = ?s) ", $params["detail_status"]);
    }

    // Получить краткую информацию об используемом материале
    if ($params["with_material_info"]){
        $j_materials            = "?:materials";
        $j_detail__and__items   = "?:detail__and__items";
        $fields[] = "$j_materials.mclass_id";
        $fields[] = "$j_materials.material_id";
        $fields[] = "$j_materials.material_name";
        $fields[] = "$j_materials.material_no";
        $fields[] = "$j_detail__and__items.quantity as material_quantity";
        $fields[] = "$j_detail__and__items.u_id as material_u_id";
        $join .= db_quote(" LEFT JOIN $j_detail__and__items ON ($j_detail__and__items.detail_id  = $m_table.detail_id) ");
        $join .= db_quote(" LEFT JOIN $j_materials          ON ($j_materials.material_id  = $j_detail__and__items.material_id) ");

        if ($params["material_id_array"] = to__array($params["material_id"])){
            $condition .= db_quote(" AND $j_materials.material_id in (?n)", $params["material_id_array"]);
        }
    }




    //*********************
    $join .= db_quote(" LEFT JOIN $j_table_detail_categories ON ($j_table_detail_categories.dcat_id  = $m_table.dcat_id AND $j_table_detail_categories.dcat_status = 'A') ");
    $join .= db_quote(" LEFT JOIN $j_table_detail_typesizes  ON ($j_table_detail_typesizes.detail_id = $m_table.detail_id) ");


    if (!empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(*) FROM $m_table $join WHERE 1 $condition");
        $limit = fn_paginate($params["page"], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params["sorting_schemas"]])){
        $s = array();
        foreach ($sorting_schemas[$params["sorting_schemas"]] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(", ", $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(", ", $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "detail_id");
    if (!is__array($data)) return array(array(), $params, 0);

    // В добавить поле "ПОЛНЫЙ ПУТЬ КАТЕГОРИИ МАТЕРИАЛОВ"
    if ($params["dcat_path"]){
        list($cat_paths) = fn_uns__get_details_category_path (array("detail_id" => array_keys($data)));
        if (is__array($cat_paths)){
            foreach ($data as $k=>$v){
                $data[$k]["category_path"] = $cat_paths[$v["dcat_id"]]["category_path"];
            }
        }
    }

    // Получить информацию по учету материала
    if ($params["with_accounting"]){
        $accounting_data = fn_uns__get_accounting_items ("D", array_keys($data));
        if (is__array($accounting_data)){
            foreach ($data as $k=>$v){
                $accounting_data[$v["detail_id"]]["weight"]["M"] = $accounting_data[$v["detail_id"]]["weights"]["M"][0]["value"];
                if ($data[$k]["size_a"] == "A" and is__array($accounting_data[$v["detail_id"]]["weights"]["A"])){
                    $accounting_data[$v["detail_id"]]["weight"]["A"] = $accounting_data[$v["detail_id"]]["weights"]["A"][0]["value"];
                }

                if ($data[$k]["size_b"] == "A" and is__array($accounting_data[$v["detail_id"]]["weights"]["B"])){
                    $accounting_data[$v["detail_id"]]["weight"]["B"] = $accounting_data[$v["detail_id"]]["weights"]["B"][0]["value"];
                }

                $data[$k]["accounting_data"] = $accounting_data[$v["detail_id"]];
            }
        }
    }

    // Получить информацию об используемых материалов на выбранную деталь
    if ($params["with_materials"]){
        $p = array(
            "detail_id" => array_keys($data),
            "material_info" => true,
            "with_material_units" => true
        );
        $materials  = fn_uns__get_details_materials($p);
        if (is__array($materials)){
            foreach ($data as $k=>$v){
                $data[$k]["accounting_data"]["materials"] = $materials[$k];
            }
        }
    }

    // ПОЛУЧИТЬ СПИСОК ПАРАМЕТРОВ КАК СТРОКУ ТОЛЬОК ДЛЯ КАТЕГОРИИ ПОЛУМУФТ ID=38
    if ($params["with_options_as_string"]){
        // ПЛОХАЯ РЕАЛИЗАЦИЯ
        foreach ($data as $k=>$v){
            if ($v["dcat_id"] == 38){
                $options = fn_uns__get_options_items("D", $k);
                if (is__array($options)){
                    $o_array = array();
                    foreach ($options as $o){
                        $o_array[] = $o["variants"][$o["ov_id"]]["ov_value"];
                    }
                }
                $data[$k]['options_as_string'] = implode('/', $o_array);
            }
        }
    }

    // Форматированное имя детали
    if ($params["format_name"]){
        foreach ($data as $k=>$v){
            $fn = " ___ОШИБКА!___ ";
            if (strlen($v["detail_no"])){
                $fn = " [{$v['detail_no']}]";
            }

            if (strlen($v["options_as_string"])){
                $fn .= " ({$v['options_as_string']})";
            }

            $data[$k]["format_name"] = $v["detail_name"] . $fn;
        }
    }

    // ПРИМЕНЯЕМОСТЬ В НАСОСАХ
    if ($params["with_accessory_pumps"]){
        if (is__array($accessory_pumps = fn_uns__get_accessory_pumps('D', array_keys($data)))){
            foreach ($data as $k=>$v){
                $data[$k]['accessory_pumps']         = $accessory_pumps[$k]['list_of_pumps'];
                $data[$k]['accessory_pump_series']   = $accessory_pumps[$k]['list_of_pump_series'];
            }
        }
    }

    // Сгруппировать детали по категориям
    if ($params["group_by_categories"]){
        list($dcategories_plain) = fn_uns__get_details_categories(array('only_active' => true,"view_in_reports" => true));
        foreach ($data as $k=>$v){
            if (in_array($v["dcat_id"], array_keys($dcategories_plain))){
                $dcategories_plain[$v["dcat_id"]]["details"][$v["detail_id"]] = array(
                    "detail_id"     => $v["detail_id"],
                    "detail_name"   => $v["detail_name"] . ((strlen($v["detail_no"]))?" [" . $v["detail_no"] . "]":""),
                );
            }
        }
        $data = $dcategories_plain;
    }

    return array($data, $params, $total);
}


/**
 * ОБНОВИТЬ ДЕТАЛЬ
 * @param int $id
 * @param $data
 * @return bool|int
 */
function fn_uns__upd_detail($id=0, $data){
    if (!is__array($data)) return false;

    if (is__more_0($id) && db_get_field(UNS_DB_PREFIX . "SELECT detail_id FROM ?:details WHERE detail_id = ?i", $id)){
        db_query(UNS_DB_PREFIX . 'UPDATE ?:details SET ?u WHERE detail_id = ?i', $data, $id);
    }else{
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:details ?e", $data);
    }

    // Обновить характеристики
    if (is__array($data['features'])){
        fn_uns__upd_features_items('D', $id, $data['features']);
    }

    // Обновить параметры
    if (is__array($data['options'])){
        fn_uns__upd_options_items('D', $id, $data['options']);
    }

    // Обновить учет
    if (is__array($data['accounting'])){
        fn_uns__upd_accounting_items('D', $id, $data['accounting']);
    }

    // Обновить расход материалов
    fn_uns__upd_accounting_items_materials($id, $data['accounting']['materials']);

    return $id;
}


/**
 * УДАЛИТЬ ДЕТАЛЬ(И)
 * @param $ids
 * @return bool
 */
function fn_uns__del_details($ids){
    if (!($ids = fn_check_before_deleting("del_details", $ids))) return false;

    // Удаление ДЕТАЛИ
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:details WHERE detail_id IN (?n)", $ids);

    // Удаление УЧЕТА
    fn_uns__del_accounting_items('D', $ids);

    // Удаление ХАРАКТЕРИСТИК
    fn_uns__del_features_items('D', $ids);

    // Удаление ПАРАМЕТРОВ
    fn_uns__del_options_items('D', $ids);

    // Удаление связи с МАТЕРИАЛАМИ, которые идут на эту деталь
    fn_uns__del_accounting_items_materials($ids);

    return true;
}


/**
 * ПОЛУЧИТЬ СПИСОК МАТЕРИАЛОВ ИДУЩИХ НА ДЕТАЛЬ
 * @param array $params
 * @internal param $ids
 * @internal param bool $full_info
 * @return bool|mixed
 */
function fn_uns__get_details_materials ($params = array()){

    //***********************************

    $default_params = array(
        'detail_id'             => 0,
        'material_info'         => false,
        'with_material_units'   => false
    );

    $params = array_merge($default_params, $params);

    $m_table = "?:detail__and__items";

    $fields = array(
        "$m_table.di_id",
        "$m_table.detail_id",
        "$m_table.material_id",
        "$m_table.quantity",
        "$m_table.allowance",
        "$m_table.add_quantity",
        "$m_table.add_quantity_state",
        "$m_table.u_id",
    );

    $condition = $join = $limit = $sorting = '';

    if ($params['detail_id'] = to__array($params['detail_id'])){
        $condition .= db_quote(" AND $m_table.detail_id in (?n)", $params['detail_id']);
    } else {
        return false;
    }

    if ($params['material_info']) {
        $j_table = "?:materials";
        $fields[] = "$j_table.mcat_id";
        $fields[] = "$j_table.mclass_id";
        $fields[] = "$j_table.material_no";
        $fields[] = "$j_table.material_name";
        $join .= db_quote(" LEFT JOIN $j_table ON ($j_table.material_id = $m_table.material_id ) ");
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "di_id");

    if (is__array($data)){
        $result = array();
        foreach ($data as $d){
            $result[$d['detail_id']][$d['di_id']] = $d;
            if ($params['with_material_units']){
                list ($units) = fn_uns__get_units(array('material_id' => $d['material_id'], 'item_type' => 'M'));
                $result[$d['detail_id']][$d['di_id']]['units'] = $units;
            }

            if ($params['material_info']){
                $p = array(
                    'material_id' => $d['material_id'],
                    'with_accounting' => true,
                    'format_name' => true
                );
                list ($materials) = fn_uns__get_materials($p);
                $result[$d['detail_id']][$d['di_id']] = array_merge($result[$d['detail_id']][$d['di_id']], $materials[$d['material_id']]);
            }
        }
        $data = $result;
    }

    return ((is__array($data))?$data:false);
}
