<?php

// ОТЧЕТ ПОСУТОЧНОГО ОБЪЕМА ВЫПУСКА ОТЛИВОК
function fn_acc__get_report_VLC($params = array(), $items_per_page = 0){
    // *************************************************************************
    // 0. ИНИЦИАЛИЗАЦИЯ
    // *************************************************************************
    $default_params = array(
        'status'            => "A",
        'type'              => DOC_TYPE__VLC,
        'object_id'         => 7,   // Лит цех
        'motion_type'       => "0", // out - выход
        'limit'             => 0,
        'page'              => 1,
        'sorting_schemas'   => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_tbl   = "?:_acc_documents";
    $m_key   = "document_id";

    $j_tbl_1  = "?:_acc_motions";
    $j_key_1  = "document_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.comment",
        "$m_tbl.type",
        "$m_tbl.date_cast",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.date_cast"   => "desc",
            "$m_tbl.$m_key"      => "desc",
        ),
        "view_asc" => array(
            "$m_tbl.date_cast"   => "asc",
            "$m_tbl.$m_key"      => "asc",
        ),
    );

    $total = 0;
    $condition = $join = $limit = $sorting = "";


    // *************************************************************************
    // 1. УСЛОВИЯ ОТБОРА ПО ОСНОВНОЙ ТАБЛИЦЕ
    // *************************************************************************
    // По ID
    if ($params["{$m_key}_array"] = to__array($params[$m_key])){
        $condition .= db_quote(" AND $m_tbl.$m_key in (?n)", $params["{$m_key}_array"]);
    }

    if ($params["status"]){
        $condition .= db_quote(" AND $m_tbl.status = ?s ", $params["status"]);
    }

    if (is__more_0($params["type"])){
        $condition .= db_quote(" AND $m_tbl.type = ?i ", $params["type"]);
    }

    if (is_numeric($params["time_from"]) and  is__more_0($params["time_to"])){
        $condition .= db_quote(" AND $m_tbl.date_cast BETWEEN ?i and ?i ", $params["time_from"], $params["time_to"]);
    }


    // *************************************************************************
    // 2. ПРИСОЕДИНИТЬ ТАБЛИЦЫ
    // *************************************************************************
     $join .= db_quote(" LEFT JOIN $j_tbl_1 ON ($j_tbl_1.$j_key_1  = $m_tbl.$m_key) ");


    // *************************************************************************
    // 3. ДОПОЛНИЕЛЬНЫЕ УСЛОВИЯ ОТБОРА
    // *************************************************************************
    if (in_array($params["motion_type"], array('I', 'O'))){
        $condition .= db_quote(" AND $j_tbl_1.motion_type = ?s ", $params["motion_type"]);
    }

    if (is__more_0($params["object_id"])){
        $condition .= db_quote(" AND $j_tbl_1.object_id = ?i ", $params["object_id"]);
    }

    // *************************************************************************
    // 4. ПАГИНАЦИЯ И СОРТИРОВКА
    // *************************************************************************
    if (!empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(*) FROM $m_tbl $join WHERE 1 $condition");
        $limit = fn_paginate($params["page"], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params["sorting_schemas"]])){
        $s = array();
        foreach ($sorting_schemas[$params["sorting_schemas"]] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(", ", $s);
    }


    // *************************************************************************
    // 5. ПОЛУЧЕНЕ ДАННЫХ
    // *************************************************************************
    $sql = UNS_DB_PREFIX . "SELECT " . implode(", ", $fields) . " FROM $m_tbl $join WHERE 1 $condition $sorting $limit";
    $data = db_get_hash_array($sql, $m_key);
//    fn_print_r(str_replace(array(UNS_DB_PREFIX,"?:"), array("", "uns_"), $sql));
//    $data = fn_group_data_by_field($data, 'task_id');
    if (!is__array($data)) return array(array(), $params);


    // *************************************************************************
    // 6. ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ
    // *************************************************************************

    if ($params["with_weight_per_each_document"]){
        $p = array(
            'document_id' => array_keys($data)
        );
        list($weights) = fn_acc__get_weight_VLC($p);
        if (is__array($weights)){
            foreach ($data as $k_data=>$v_data){
                $data[$k_data]['weight'] = $weights[$k_data];
            }
        }
    }

    if ($params["with_total_weight_all_documents"]){
        $p = array(
            'document_id' => array_keys($data)
        );
        list($weights) = fn_acc__get_weight_VLC($p, true);
        if (is__array($weights)){
            foreach ($weights as $k_data=>$v_data){
                $params['total_weight'][$v_data['type_casting']] = $v_data['weight'];
            }
        }
    }

    return array($data, $params, $total);
}


/**
 * ЗАПРОС ВЕСА ПО ВСЕМ ВИДАМ ЛИТЬЯ ПО ДОКУМЕНТУ
 * @param array $params
 * @param bool  $total_weight
 *              = true   - запросить итоговый вес по СТАЛИ и по ЧУГУНУ по всем документам сразу
 *              = false  - запросить итоговый вес по СТАЛИ и по ЧУГУНУ индивидально по каждому документу
 * @return array
 */
function fn_acc__get_weight_VLC ($params = array(), $total_weight = false){
    $default_params = array(
        'document_id'       => 0,
        'item_type'         => 'M',
        'limit'             => 0,
        'page'              => 1,
        'sorting_schemas'   => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_tbl   = "?:_acc_document_items";
    $m_key   = "document_id";

    $j_tbl_1  = "?:materials";
    $j_key_1  = "material_id";

    if (!$total_weight){
        $fields = array(
            "$m_tbl.$m_key",
            "$j_tbl_1.type_casting",
            "sum($m_tbl.quantity*$m_tbl.weight) as  weight",
        );
    }else{
        $fields = array(
            "$j_tbl_1.type_casting",
            "sum($m_tbl.quantity*$m_tbl.weight) as  weight",
        );
    }


    $sorting_schemas = array(
        "view1" => array(
            "$m_tbl.date_cast"   => "desc",
            "$m_tbl.$m_key"      => "desc",
        )
    );

    $total = 0;
    $condition = $join = $limit = $sorting = $group_by = "";

    // *************************************************************************
    // 1. УСЛОВИЯ ОТБОРА ПО ОСНОВНОЙ ТАБЛИЦЕ
    // *************************************************************************
    // По ID
    if ($params["{$m_key}_array"] = to__array($params[$m_key])){
        $condition .= db_quote(" AND $m_tbl.$m_key in (?n)", $params["{$m_key}_array"]);
    }

    if ($params["item_type"]){
        $condition .= db_quote(" AND $m_tbl.item_type = ?s ", $params["item_type"]);
    }

    // *************************************************************************
    // 2. ПРИСОЕДИНИТЬ ТАБЛИЦЫ
    // *************************************************************************
     $join .= db_quote(" LEFT JOIN $j_tbl_1 ON ($j_tbl_1.$j_key_1  = $m_tbl.item_id) ");

    if (!$total_weight){
        $group_fields = array(
            "$m_tbl.$m_key",
            "$j_tbl_1.type_casting"
        );
    }else{
        $group_fields = array(
            "$j_tbl_1.type_casting"
        );
    }
    if (is__array($group_fields)){
        $group_by = "GROUP BY " . implode(", ", $group_fields) . " ";
    }

    // *************************************************************************
    // 5. ПОЛУЧЕНЕ ДАННЫХ
    // *************************************************************************
    $sql = UNS_DB_PREFIX . "SELECT " . implode(", ", $fields) . " FROM $m_tbl $join WHERE 1 $condition $sorting $group_by $limit";
    $data = db_get_array($sql);
    if (!is__array($data)) return array(array(), $params);

    if (!$total_weight){
        $tmp = array();
        foreach ($data as $k_data=>$v_data){
            $tmp[$v_data['document_id']][$v_data['type_casting']] = $v_data['weight'];
        }
        $data = $tmp;
    }
    return array($data, $params, $total);
}


// ОТЧЕТ ПРОИЗВОДСТВА ОТЛИВОК ПО НАЗНАЧЕНИЮ
function fn_acc__purpose_VLC($type, $time_from, $time_to){
    $types = array("for_production_pumps", "for_internal", "for_sale");
    if (!in_array($type, $types)) return false;

    $cond = '';
    switch ($type){
        case "for_production_pumps":
            $cond = " AND uns_material_categories.mcat_id not in (78,79) ";
            break;
        case "for_internal":
            $cond = " AND uns_material_categories.mcat_id in (78) ";
            break;
        case "for_sale":
            $cond = " AND uns_material_categories.mcat_id in (79) ";
            break;
    }

    $sql = db_quote("
        SELECT
          uns_materials.type_casting,
          sum(uns__acc_document_items.quantity*uns__acc_document_items.weight) as total_weight
        FROM uns__acc_documents
          LEFT JOIN uns__acc_document_items
            ON (uns__acc_document_items.document_id = uns__acc_documents.document_id)
          LEFT JOIN uns__acc_motions
            ON (uns__acc_motions.document_id = uns__acc_documents.document_id)
          LEFT JOIN uns__acc_document_types
            ON (uns__acc_document_types.dt_id = uns__acc_documents.type)
          LEFT JOIN uns_materials
            ON (uns_materials.material_id = uns__acc_document_items.item_id)
          LEFT JOIN uns_material_categories
            ON (uns_material_categories.mcat_id = uns_materials.mcat_id)
        WHERE
          1
          AND uns__acc_documents.status = 'A'
          AND uns__acc_document_types.type = 'VLC'
          AND uns__acc_documents.date_cast BETWEEN {$time_from} AND {$time_to}
          AND uns__acc_motions.object_id = 7
          {$cond}
        GROUP BY
          uns_materials.type_casting
    ");
    $data = db_get_hash_array(UNS_DB_PREFIX . $sql, 'type_casting');
    return $data;
}


// СПИСОК ЛИТЬЯ НА ПРОДАЖУ
function fn_acc__sales_VLC ($time_from, $time_to){
    $sql = db_quote("
        SELECT
          uns__acc_documents.document_id,
          uns__acc_documents.date,
          uns__acc_document_items.item_id,
          replace(concat(uns_materials.material_name, ' [', uns_materials.material_no, ']'), ' []', '') as name,
          uns__acc_document_items.quantity,
          uns__acc_document_items.weight,
          (uns__acc_document_items.quantity*uns__acc_document_items.weight) as total_weight,
          1
        FROM
          uns__acc_documents
          LEFT JOIN uns__acc_document_types
            ON (uns__acc_document_types.dt_id = uns__acc_documents.type)
          LEFT JOIN uns__acc_document_items
            ON (uns__acc_document_items.document_id = uns__acc_documents.document_id)
          LEFT JOIN uns_materials
            ON (uns_materials.material_id = uns__acc_document_items.item_id)
        WHERE 1
          AND uns__acc_documents.status = 'A'
          AND uns__acc_documents.date BETWEEN {$time_from} AND {$time_to}
          AND uns__acc_document_types.type = 'RO'
        ORDER BY  uns__acc_documents.date, uns_materials.material_position, uns_materials.material_name
    ");
    $data = db_get_array(UNS_DB_PREFIX . $sql);
    return $data;
}


// ПОЛУЧИТЬ СПИСОК ВЫПУЩЕННОГО ЛИТЬЯ ПО НАЗНАЧЕНИЮ
function fn_acc__casting_list_VLC($type, $time_from, $time_to){
    $types = array("for_production_pumps", "for_internal", "for_sale");
    if (!in_array($type, $types)) return false;

    $cond = '';
    switch ($type){
        case "for_production_pumps":
            $cond = " AND uns_material_categories.mcat_id not in (78,79) ";
            break;
        case "for_internal":
            $cond = " AND uns_material_categories.mcat_id in (78) ";
            break;
        case "for_sale":
            $cond = " AND uns_material_categories.mcat_id in (79) ";
            break;
    }

    $sql = db_quote("
            SELECT
              replace(concat(uns_materials.material_name, ' [', uns_materials.material_no, ']'), ' []', '') as name,
              uns_materials.type_casting,
              sum(uns__acc_document_items.quantity) as total_quantity,
              uns__acc_document_items.weight,
              sum(uns__acc_document_items.quantity*uns__acc_document_items.weight) as total_weight
            FROM uns__acc_documents
              LEFT JOIN uns__acc_document_items
                ON (uns__acc_document_items.document_id = uns__acc_documents.document_id)
              LEFT JOIN uns__acc_motions
                ON (uns__acc_motions.document_id = uns__acc_documents.document_id)
              LEFT JOIN uns__acc_document_types
                ON (uns__acc_document_types.dt_id = uns__acc_documents.type)
              LEFT JOIN uns_materials
                ON (uns_materials.material_id = uns__acc_document_items.item_id)
              LEFT JOIN uns_material_categories
                ON (uns_material_categories.mcat_id = uns_materials.mcat_id)
            WHERE
              1
              AND uns__acc_documents.status = 'A'
              AND uns__acc_document_types.type = 'VLC'
              AND uns__acc_documents.date_cast BETWEEN {$time_from} AND {$time_to}
              AND uns__acc_motions.object_id = 7
              {$cond}
            GROUP BY
              uns__acc_document_items.item_id
            ORDER BY
              uns_material_categories.mcat_position,
              uns_material_categories.mcat_name,
              uns_materials.material_position,
              uns_materials.material_name
    ");
    $data = db_get_array(UNS_DB_PREFIX . $sql);
    return $data;
}
