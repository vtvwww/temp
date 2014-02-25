<?php

function fn_uns__get_enabled_objects($document_type){
/*
    array(
        'document_type_1' => array(
            'object_from_1' => array(
                'object_to_1',
                'object_to_2',
                'object_to_3',
                'object_to_4',
                'object_to_5',
            ),
            'object_from_2' => array(
                'object_to_1',
                'object_to_2',
                'object_to_3',
                'object_to_4',
                'object_to_5',
            ),
        ),
        'document_type_2' => array(
            'object_from_1' => array(
                'object_to_1',
                'object_to_2',
                'object_to_3',
                'object_to_4',
                'object_to_5',
            ),
            'object_from_2' => array(
                'object_to_1',
                'object_to_2',
                'object_to_3',
                'object_to_4',
                'object_to_5',
            ),
        ),
    )*/

    if (!is__more_0($document_type)) return array();
    $obj = array(
        DOC_TYPE__PO    => array(   // ПРИХОДНЫЙ ОРДЕР

        ),
        DOC_TYPE__VLC   => array(   // ВЫПУСК ЛИТ. ЦЕХА
            7 => array(             // Лит цех
                8 => true, // Склад Литья
            ),
        ),
        DOC_TYPE__BRAK   => array(
            10 => array(
                21 => true,
                22 => true,
            ),
            14 => array(
                21 => true,
                22 => true,
            ),
            17 => array(
                21 => true,
                22 => true,
            ),
            18 => array(
                21 => true,
                22 => true,
            ),
        ),
        DOC_TYPE__MCP => array(     // МЕЖЦЕХОВОЕ ПЕРЕМЕЩЕНИЕ
            5 => array(             // СМП
                6 => true,          // К
                7 => true,          // Литейный цех
                23=> true,          // Цех Лопата
                24=> true,          // Мех.цех -> Склад материалов
                18=> true,          // Сборочный участок
            ),
            6 => array(             // К
                5=> true,           // СМП
                18=> true,          // Сборочный участок
            ),
            8 => array(             // Склад Литья
                24=> true,          // Мех.цех -> Склад материалов
            ),

            10 => array(
                14 => true,
                17 => true,
                18 => true,
            ),
            14 => array(
                10 => true,
                17 => true,
                18 => true,
            ),
            17 => array(
                10 => true,
                14 => true,
                18 => true,
            ),
            18 => array(
                10 => true,
                14 => true,
                17 => true,
            ),




        ),
        DOC_TYPE__AIO => array(     // АКТ ИЗМЕНЕНИЕ ОСТАТКОВ
            8 => array(             // Склад Литья
                0=> true,          // Мех.цех -> Склад материалов
            ),
            10 => array(
                0=> true,
            ),
            14 => array(
                0=> true,
            ),
            17 => array(
                0=> true,
            ),
            18 => array(
                0=> true,
            ),
            19 => array(
                0=> true,
            ),
        ),


        DOC_TYPE__AS_VLC => array(     // АКТ ИЗМЕНЕНИЕ ОСТАТКОВ
            8 => array(             // Склад Литья
                0=> true,          // Мех.цех -> Склад материалов
            ),
        ),
        DOC_TYPE__RO => array(     // РАСХОДНЫЙ ОРДЕР
            8 => array(             // Склад Литья
                0=> true,          // Мех.цех -> Склад материалов
            ),
            19 => array(           // Склад готовой продукции
                0=> true,
            ),
        ),
        DOC_TYPE__VCP => array(),   //ВНУТРИЦЕХОВЫЕ ПЕРЕМЕЩЕНИЯ
    );

    return $obj[$document_type];
}

function fn_uns__get_enabled_objects_old(){
    return array(
        UNS_DOCUMENT__PRIH_ORD => array(
            "from" => array(),
            "to" => array(
                5, // Склад СМП
                6, // Склад К
            ),
        ),
        UNS_DOCUMENT__RASH_ORD => array(
            "from" => array(
                5, // Склад СМП
                6, // Склад К
            ),
            "to" => array(
            ),
        ),
        UNS_DOCUMENT__NOPM => array(
            "from" => array(
                5, // Склад СМП
                6, // Склад К
                8, // Склад Литья
                24, // Мех.цех - 1 - Материалы
                13, // Мех.цех - 1 - Гот. детали
                25, // Мех.цех - 2 - Материалы
                16, // Мех.цех - 2 - Гот. детали

            ),
            "to" => array(
                5, // Склад СМП
                6, // Склад К
                7, // Цех Литейный
                17, // Склад ДЕТАЛЕЙ
                18, // Цех Сборки

                24, // Мех.цех - 1 - Материалы
                12, // Мех.цех - 1 - В обработке
                13, // Мех.цех - 1 - Гот. детали

                25, // Мех.цех - 2 - Материалы
                15, // Мех.цех - 2 - В обработке
                16, // Мех.цех - 2 - Гот. детали
            ),
        ),
        UNS_DOCUMENT__INPM => array(
            "from" => array(
                5, // Склад СМП
                6, // Склад К

                24, // Мех.цех - 1 - Материалы
                12, // Мех.цех - 1 - В обработке
                13, // Мех.цех - 1 - Гот. детали

                25, // Мех.цех - 2 - Материалы
                15, // Мех.цех - 2 - В обработке
                16, // Мех.цех - 2 - Гот. детали

            ),
            "to" => array(
                5, // Склад СМП
                6, // Склад К

                24, // Мех.цех - 1 - Материалы
                12, // Мех.цех - 1 - В обработке
                13, // Мех.цех - 1 - Гот. детали

                25, // Мех.цех - 2 - Материалы
                15, // Мех.цех - 2 - В обработке
                16, // Мех.цех - 2 - Гот. детали
            ),
        ),
        UNS_DOCUMENT__SDAT_N => array(
            "from" => array(
                7, // ЦЕХ Литейный
            ),
            "to" => array(
                8, // Склад Литья
            ),
        ),
    );
}

function fn_uns__get_child_objects($o, $include=true){
    if (!is__more_0($o)) return false;

    $cond = "";

    $o_id_path     = db_get_field (UNS_DB_PREFIX . "SELECT o_id_path FROM ?:_acc_objects WHERE o_id = ?i", $o);
    if ($include) $cond = db_quote(" OR o_id_path = ?s", $o_id_path);
    $objects       = db_get_fields(UNS_DB_PREFIX . "SELECT o_id      FROM ?:_acc_objects WHERE o_id_path LIKE ?l $cond ", "$o_id_path/%");

    return (is__array($objects))?$objects:false;
}


function fn_uns__get_objects_name(){

}


/**
 * ПОЛУЧИТЬ СПИСОК ОБЪЕКТОВ
 * @param array $params
 * @return array
 */
function fn_uns__get_objects($params = array()){
    $default_params = array(
        'o_id' => 0,
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
        'status' => "A",
        'category_delimiter' => '/',
        'sorting_schemas' => 'view',

    );

    if (!is__array($params)) $params = array();

    $params = array_merge($default_params, $params);

    $m_table = "?:_acc_objects";

    $fields = array(
        "$m_table.o_id",
        "$m_table.o_parent_id",
        "$m_table.o_name",
        "$m_table.o_id_path",
        "$m_table.o_position",
        "$m_table.o_status"
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_table.o_status"  => 'asc',
            "$m_table.o_position"=> 'asc',
            "$m_table.o_name"    => 'asc',
        )
    );

    $condition = '';

    if (!empty($params['o_id']) and !$params['all']) {
        $from_id_path = db_get_field(UNS_DB_PREFIX . "SELECT o_id_path FROM $m_table WHERE o_id = ?i", $params['o_id']);
        if (!$params['include_target']){
            $condition .= db_quote(" AND $m_table.o_id_path LIKE ?l", "$from_id_path/%");
        }else{
            $condition .= db_quote(" AND $m_table.o_id_path LIKE ?l", "$from_id_path%");
        }
    }

    if ($params['status'] == "A") {
        $condition .= db_quote(" AND $m_table.o_status = ?s", $params['status']);
    }

    if (!empty($params['item_ids'])) {
        if (!is_array($params['item_ids'])) $params['item_ids'] = (array)$params['item_ids'];
        $condition .= db_quote(" AND $m_table.o_id IN (?n)", $params['item_ids']);
    }


    $limit = $join = $group_by = '';

    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])){
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }


    $objects = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(',', $fields) . " FROM $m_table $join WHERE 1 $condition $group_by $sorting $limit", 'o_id');

    if (empty($objects)) {
        return array(array());
    }

    $tmp = array();
    if ($params['simple'] == true || $params['group_by_level'] == true) {
        $child_for = array_keys($objects);
        $where_condition = !empty($params['except_id']) ? db_quote(' AND o_id != ?i', $params['except_id']) : '';
        $has_children = db_get_hash_array(UNS_DB_PREFIX . "SELECT o_id, o_parent_id FROM $m_table WHERE o_parent_id IN(?n) ?p", 'o_parent_id', $child_for, $where_condition);
    }
    // Group categories by the level (simple)
    if ($params['simple'] == true) {
        foreach ($objects as $k => $v) {
            $v['level'] = substr_count($v['o_id_path'], '/');
            if ((!empty($params['current_category_id']) || $v['level'] == 0) && isset($has_children[$k])) {
                $v['has_children'] = $has_children[$k]['o_id'];
            }
            $tmp[$v['level']][$v['o_id']] = $v;
        }
    } elseif ($params['group_by_level'] == true) {
        // Group categories by the level (simple) and literalize path
        foreach ($objects as $k => $v) {
            $path = explode('/', $v['o_id_path']);
            $category_path = array();
            foreach ($path as $__k => $__v) {
                $category_path[$__v] = @$objects[$__v]['o_name'];
            }
            $v['category_path'] = implode($params['category_delimiter'], $category_path);
            $v['level'] = substr_count($v['o_id_path'], "/");
            if ((!empty($params['current_category_id']) || $v['level'] == 0) && isset($has_children[$k])) {
                $v['has_children'] = $has_children[$k]['o_id'];
            }
            $tmp[$v['level']][$v['o_id']] = $v;
        }
    } else {
        $tmp = $objects;
    }

    ksort($tmp, SORT_NUMERIC);
    $tmp = array_reverse($tmp);

    foreach ($tmp as $level => $v) {
        foreach ($v as $k => $data) {
            if (isset($data['o_parent_id']) && isset($tmp[$level + 1][$data['o_parent_id']])) {
                $tmp[$level + 1][$data['o_parent_id']]['subcategories'][] = $tmp[$level][$k];
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

    $tmp = fn_uns__get_objects_path($tmp);

    if (!empty($params['add_root'])) {
        array_unshift($tmp, array('o_id' => 0, 'o_name' => $params['add_root']));
    }

    return array($tmp, $params);
}


// Присвоить полные пути
function fn_uns__get_objects_path($data){
    $result = array();
    foreach ($data as $k=>$v){
        $result[$v["o_id"]] = $v;
    }

    foreach ($result as $k=>$v){
        $path = array();
        foreach (explode("/", $v["o_id_path"]) as $id_path){
            $path[] = $result[$id_path]["o_name"];
        }
        $result[$k]["path"] = implode(" - ", $path);
    }

    return $result;
}


/**
 * ОБНОВИТЬ СПИСОК КАТЕГОРИЙ ДЛЯ ДЕТАЛЕЙ
 * @param null $id
 * @param $data
 * @return bool|null
 */
function fn_uns__upd_object($id = null, $data){
    $m_table = "?:_acc_objects";

    if (is__more_0($id)) {
        // ОБНОВИТЬ
        if (is__array($data)) {

            $new_parent_id = $data['o_parent_id'];
            $new_parent_path = db_get_field(UNS_DB_PREFIX . "SELECT o_id_path FROM $m_table WHERE o_id = ?i", $new_parent_id);
            $current_path = db_get_field(UNS_DB_PREFIX . "SELECT o_id_path FROM $m_table WHERE o_id = ?i", $id);

            if (!empty($new_parent_path) && !empty($current_path)) {
                db_query(UNS_DB_PREFIX . "UPDATE $m_table SET o_parent_id = ?i, o_id_path = ?s WHERE o_id = ?i", $new_parent_id, "$new_parent_path/$id", $id);
                db_query(UNS_DB_PREFIX . "UPDATE $m_table SET o_id_path = CONCAT(?s, SUBSTRING(o_id_path, ?i)) WHERE o_id_path LIKE ?l", "$new_parent_path/$id/", strlen($current_path . '/') + 1, "$current_path/%");
            } elseif (empty($new_parent_path) && !empty($current_path)) {
                db_query(UNS_DB_PREFIX . "UPDATE $m_table SET o_parent_id = ?i, o_id_path = ?i WHERE o_id = ?i", $new_parent_id, $id, $id);
                db_query(UNS_DB_PREFIX . "UPDATE $m_table SET o_id_path = CONCAT(?s, SUBSTRING(o_id_path, ?i)) WHERE o_id_path LIKE ?l", "$id/", strlen($current_path . '/') + 1, "$current_path/%");
            }
            db_query(UNS_DB_PREFIX . "UPDATE $m_table SET ?u WHERE o_id = ?i", $data, $id);
        }
    } else {
        // ДОБАВИТЬ
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO $m_table ?e", $data);
        if (empty($id)) {
            return false;
        }
        $parent_id = intval($data['o_parent_id']);
        if ($parent_id == 0) {
            $id_path = $id;
        } else {
            $id_path = db_get_row(UNS_DB_PREFIX . "SELECT o_id_path FROM $m_table WHERE o_id = ?i", $parent_id);
            $id_path = $id_path['o_id_path'] . '/' . $id;
        }
        db_query(UNS_DB_PREFIX . "UPDATE $m_table SET ?u WHERE o_id = ?i", array('o_id_path' => $id_path), $id);
    }
    return $id;
}


/**
 * УДАЛИТЬ КАТЕГОРИЮ ДЕТАЛЕЙ
 * @param $ids
 * @internal param $id
 * @return array|bool
 */
function fn_uns__del_object($ids){
    // utodo - реализовать проверку перед удалением ОБЪЕКТА
//    if (!($ids = fn_check_before_deleting("del_object", $ids))) return false;

    if (!($ids = to__array($ids))) return false;
    $m_table = "?:_acc_objects";

    foreach($ids as $id){
        $id_path = db_get_field(UNS_DB_PREFIX . "SELECT o_id_path FROM $m_table WHERE o_id = ?i", $id);
        $o_ids = db_get_fields(UNS_DB_PREFIX . "SELECT o_id FROM $m_table WHERE o_id = ?i OR o_id_path LIKE ?l", $id, "$id_path/%");
        if (is__array($o_ids)){
            db_query(UNS_DB_PREFIX . "DELETE FROM $m_table WHERE o_id in (?n) ", $o_ids);
        }
    }

    return true;
}

