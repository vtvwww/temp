<?php

/**
 * ОБНОВИТЬ ИНФО СЛ
 * @param int $id
 * @param $data
 * @return bool|int
 */
function fn_acc__upd_sheet_info($id = 0, $data){;
    if (is__more_0($id) and is__array($data) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT sheet_id FROM ?:_acc_sheets WHERE sheet_id = $id"))){
        $operation = "update";
    }else{
        $operation = "add";
        $id = 0;
    }

    $data = trim__data($data);
    $d = array();
    $d["status"]        = (fn_check_type($data["status"], UNS_STATUS_SHEET))?$data["status"]:UNS_STATUS_SHEET__OP;
    $d["comment"]       = $data["comment"];
    $d["material_type"] = $data["material_type"];
    $d["target_object"] = $data["target_object"];


    if ($operation == "update"){
        db_query(UNS_DB_PREFIX . "UPDATE ?:_acc_sheets SET ?u WHERE sheet_id = ?i", $d, $id);
        return $id;
    }

    if (    !is__more_0($data["mcat_id"])
        or  !is__more_0($data["no"])
        or  !is__more_0($data["material_id"])
        or  !strlen($data["details"])
    ){
        return false;
    }

    if ($operation == "update") $d["date_update"] = TIME;
    $d["date_open"]     = fn_parse_date($data["date"]);
    $d["material_id"]   = $data["material_id"];
    $d["no"]            = $data["no"];

    // анализ деталей
    $details = (array) explode("|", $data["details"]);
    if (!is__array($details)) return false;

    // 1. Сохранение ИНФОРМАЦИИ о Сопроводительном листе
    if ($operation == "add"){
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:_acc_sheets ?e", $d);
    }else{
        db_query(UNS_DB_PREFIX . "UPDATE ?:_acc_sheets SET ?u WHERE sheet_id = ?i", $d, $id);
    }

    // 2. Сохранение Деталей Сопроводительном листе
    $sd_ids = $d = array();
    foreach ($details as $k){
        if ($sd_id = db_get_field(UNS_DB_PREFIX . "SELECT sd_id FROM ?:_acc_sheet_details WHERE sheet_id = ?i and detail_id = ?i", $id, $k)){
            db_query(UNS_DB_PREFIX . "UPDATE ?:_acc_sheet_details SET ?u WHERE sd_id = ?i", $d, $sd_id);
        }else{
            $d["sheet_id"] = $id;
            $d["detail_id"] = $k;
            $sd_id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:_acc_sheet_details ?e", $d);
        }
        $sd_ids[] = $sd_id;
    }
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_sheet_details WHERE sheet_id = ?i AND sd_id not in (?n) ", $id, $sd_ids);

    return $id;
}


/**
 * ОБНОВИТЬ СЛ
 * @param int $id
 * @param $data
 * @return bool|int
 */
function fn_acc__upd_sheet($id = 0, $data){
    $data = trim__data($data);
    if (!is__array($data) || !is_numeric($id) || $id < 0 ) return false;

    // Обновить информацию о СЛ
    $id = fn_acc__upd_sheet_info($id, $data['sheet']);

    // Обновить движения по СЛ
//    fn_uns__upd_document_motions($id);

    return $id;
}



// Получить списоск документов
function fn_acc__get_sheets($params = array(), $items_per_page = 0){
    $default_params = array(
        'sheet_id' => 0,
        'with_details'=>true,
        "with_weight" =>true,
        'page' => 1,
        'limit' => 0,
        'sorting_schemas' => 'view',

    );

    $params = array_merge($default_params, $params);

    $m_tbl = "?:_acc_sheets";
    $m_key = "sheet_id";

    $j_tbl_1  = "?:materials";
    $j_key_1  = "material_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.no",
        "$m_tbl.date_open",
        "$m_tbl.material_id",
        "$m_tbl.status",
        "$m_tbl.comment",
        "$m_tbl.material_type",
        "$m_tbl.target_object",
        "$j_tbl_1.material_no",
        "$j_tbl_1.material_name",
        "$j_tbl_1.mcat_id",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.date_open"  => "desc",
            "$m_tbl.no"  => "desc",
        )
    );

    $condition = $limit = $join = $group_by = $sorting = '';

    // *************************************************************************
    // 1. УСЛОВИЯ ОТБОРА
    // *************************************************************************
    // По ID
    if ($params["{$m_key}_array"] = to__array($params[$m_key])){
        $condition .= db_quote(" AND $m_tbl.$m_key in (?n)", $params["{$m_key}_array"]);
    }

    if ($params["time_from"]>=0 and $params["time_to"]>0){
        $condition .= db_quote(" AND ($m_tbl.date_open between ?i and ?i) ", $params['time_from'], $params['time_to']);
    }

    // Статус
    if (fn_check_type($params["status"], UNS_STATUS_SHEET)){
        $condition .= db_quote(" AND ($m_tbl.status = ?s) ", $params['status']);
    }

    // Тип материала
    if (fn_check_type($params["material_type"], UNS_SHEET_MTYPES)){
        $condition .= db_quote(" AND ($m_tbl.material_type = ?s) ", $params['material_type']);
    }

    // Местоположение
    if (fn_check_type($params["target_object"], "|10|14|17|")){
        $condition .= db_quote(" AND ($m_tbl.target_object = ?s) ", $params['target_object']);
    }

    // Категория исходного материала
    if (is__array($params["mcat_id_array"] = to__array($params["mcat_id"]))){
        $condition .= db_quote(" AND ($j_tbl_1.mcat_id in (?n)) ", $params['mcat_id_array']);
    }

    // *************************************************************************
    // 2. ПРИСОЕДИНИТЬ ТАБЛИЦЫ
    // *************************************************************************
    $join .= db_quote(" LEFT JOIN $j_tbl_1 ON ($j_tbl_1.$j_key_1  = $m_tbl.$j_key_1) ");


    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }

    if (!empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(*) FROM $m_tbl $join WHERE 1 $condition");
        $limit = fn_paginate($params["page"], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])){
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $sql = UNS_DB_PREFIX . "SELECT " . implode(", ", $fields) . " FROM $m_tbl $join WHERE 1 $condition $sorting $limit";
    $data = db_get_hash_array($sql, $m_key);
    //  fn_print_r(str_replace(array(UNS_DB_PREFIX,"?:"), array("", "uns_"), $sql));
    //  $data = fn_group_data_by_field($data, 'task_id');
    if (!is__array($data)) return array(array(), $params);

    // *************************************************************************
    // 6. ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ
    // *************************************************************************
    //foreach ($data as $k_data=>$v_data){}

    if ($params['with_details']){
        $tmp_p = array(
            'sheet_id'=>array_keys($data),
            'with_weight' => true,
        );
        list($details) = fn_acc__get_sheet_details($tmp_p);
        if (is__array($details)){
            foreach ($data as $k_d=>$v_d){
                $data[$k_d]["details"] = $details[$k_d];
            }
        }
    }

    // СУММА ВЫДАННОГО ЛИТЬЯ ПО СЛ
    if ($params['with_material_quantity_PVP']){
        $sql = db_quote(UNS_DB_PREFIX . "
            SELECT
              ?:_acc_documents.package_id as sheet_id,
              sum(?:_acc_document_items.quantity) as m_quantity
            FROM ?:_acc_document_items
              LEFT JOIN ?:_acc_documents
                ON (?:_acc_documents.document_id = ?:_acc_document_items.document_id)
              LEFT JOIN ?:_acc_document_types
                ON (?:_acc_document_types.dt_id = ?:_acc_documents.type)
            WHERE ?:_acc_document_items.item_type = 'M'
                  AND ?:_acc_document_items.motion_type LIKE '%O%'
                  AND ?:_acc_document_types.type = 'PVP'
                  AND ?:_acc_documents.package_type = 'SL'
                  AND ?:_acc_documents.package_id in (?n)
                  AND ?:_acc_documents.status = 'A'
            GROUP BY ?:_acc_documents.package_id", array_keys($data));
        $data_mq = db_get_hash_array($sql, "sheet_id");
        foreach ($data as $k_d=>$v_d){
            $data[$k_d]["material_quantity_by_PVP"] = $data_mq[$k_d]["m_quantity"];
        }
    }

    // СУММА БРАКА ПО СЛ
    if ($params['with_material_quantity_BRAK']){
        $sql = db_quote(UNS_DB_PREFIX . "
            SELECT
              ?:_acc_documents.package_id as sheet_id,
              sum(?:_acc_document_items.quantity) as d_quantity
            FROM ?:_acc_document_items
              LEFT JOIN ?:_acc_documents
                ON (?:_acc_documents.document_id = ?:_acc_document_items.document_id)
              LEFT JOIN ?:_acc_document_types
                ON (?:_acc_document_types.dt_id = ?:_acc_documents.type)
            WHERE ?:_acc_document_items.item_type = 'D'
                  AND ?:_acc_document_items.motion_type LIKE '%O%'
                  AND ?:_acc_document_types.type = 'BRAK'
                  AND ?:_acc_documents.package_type = 'SL'
                  AND ?:_acc_documents.package_id in (?n)
                  AND ?:_acc_documents.status = 'A'
            GROUP BY ?:_acc_documents.package_id", array_keys($data));
        $data_mq = db_get_hash_array($sql, "sheet_id");
        foreach ($data as $k_d=>$v_d){
            $data[$k_d]["material_quantity_by_BRAK"] = $data_mq[$k_d]["d_quantity"];
        }
    }

    return array($data, $params);
}

function fn_acc__get_sheet_details($params = array(), $items_per_page = 0){
    $default_params = array(
        'sheet_id' => 0,
        'full_info'=>false,
        'page' => 1,
        'limit' => 0,
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_tbl      = "?:_acc_sheet_details";
    $m_key      = "sd_id";

    $j_tbl_1    = "?:details";
    $j_key_1    = "detail_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.sheet_id",
        "$m_tbl.detail_id",
        "$j_tbl_1.detail_name",
        "$j_tbl_1.detail_no",
        "$j_tbl_1.dcat_id",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.$m_key"  => "asc",
        )
    );

    $condition = $limit = $join = $group_by = $sorting = '';

    // *************************************************************************
    // 1. УСЛОВИЯ ОТБОРА
    // *************************************************************************
    // По ID
    if ($params["sheet_id_array"] = to__array($params["sheet_id"])){
        $condition .= db_quote(" AND $m_tbl.sheet_id in (?n)", $params["sheet_id_array"]);
    }

    // *************************************************************************
    // 2. ПРИСОЕДИНИТЬ ТАБЛИЦЫ
    // *************************************************************************
    $join .= db_quote(" LEFT JOIN $j_tbl_1 ON ($j_tbl_1.$j_key_1  = $m_tbl.$j_key_1) ");

    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }

    if (!empty($items_per_page)) {
        $total = db_get_field(UNS_DB_PREFIX . "SELECT COUNT(*) FROM $m_tbl $join WHERE 1 $condition");
        $limit = fn_paginate($params["page"], $total, $items_per_page);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])){
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $sql = UNS_DB_PREFIX . "SELECT " . implode(", ", $fields) . " FROM $m_tbl $join WHERE 1 $condition $sorting $limit";
    $data = db_get_array($sql);
//      fn_print_r(str_replace(array(UNS_DB_PREFIX,"?:"), array("", "uns_"), $sql));
    if (!is__array($data)) return array(array(), $params);
    if ($params["with_weight"]){
        foreach ($data as $k=>$v){
            $w = fn_uns__get_accounting_item_weights("D", $v['detail_id']);
            $data[$k]['weight'] = $w[$v['detail_id']]["M"][0]['value'];
        }
    }
    $data = fn_group_data_by_field($data, 'sheet_id');

    // *************************************************************************
    // 6. ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ
    // *************************************************************************
    foreach ($data as $k_data=>$v_data){
        $v = array_shift($v_data);
        $d[$k_data][$v["detail_id"]] = $v;
    }
    $data = $d;

    return array($data, $params);
}

// ОБНОВИТЬ движение по СЛ
function fn_acc__upd_motion ($sheet_id, $document_id=0, $data){
    $data = trim__data($data);
    if (!is__more_0($sheet_id) or !is__array($data)) return false;

    $d_id = fn_uns__upd_document($document_id, $data);
    if (!is__more_0($d_id)) return false;
    $document_id = $d_id;

    return $document_id;
}

function fn_uns__del_sheet($id){
    if (!($id = to__array($id))) return false;

    // Удалить сам СЛ
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_sheets WHERE sheet_id IN (?n)", $id);

    // Удалить ДЕТАЛИ СЛ
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_sheet_details WHERE sheet_id IN (?n)", $id);

    // Удалить связанные ДОКУМЕНТЫ
    $documents = db_get_array(UNS_DB_PREFIX . "SELECT document_id FROM ?:_acc_documents WHERE package_id IN (?n) AND package_type=?s", $id, UNS_PACKAGE_TYPE__SL);
    if (is__array($documents)){
        $document_ids = array();
        foreach ($documents as $doc){
            $document_ids[] = $doc["document_id"];
        }
        if (is__array($document_ids)){
            fn_uns__del_document($document_ids);
        }
    }
    return true;
}
