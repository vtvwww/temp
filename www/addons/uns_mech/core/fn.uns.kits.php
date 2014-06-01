<?php


// Получить списоск документов
function fn_acc__get_kits($params = array(), $items_per_page = 0){
    $default_params = array(
        'kit_id' => 0,
        'page' => 1,
        'limit' => 0,
        'only_opened' => false,
        'sorting_schemas' => 'view',

    );

    $params = array_merge($default_params, $params);

    $m_tbl = "?:_acc_kits";
    $m_key = "kit_id";

//    $j_tbl_1  = "?:materials";
//    $j_key_1  = "material_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.kit_type",
        "$m_tbl.description",
        "$m_tbl.comment",
        "$m_tbl.status",
        "$m_tbl.date_open",
        "$m_tbl.p_id",
        "$m_tbl.p_quantity",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.$m_key"  => "asc",
        ),
        "view_1" => array(
            "$m_tbl.$m_key"  => "desc",
        ),
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
        $condition .= db_quote(" AND ($m_tbl.date_open between ?i and ?i)", $params['time_from'], $params['time_to']);
    }

    if ($params["ps_id_array"] = to__array($params["ps_id"])){
        $condition .= db_quote(" AND ?:pumps.ps_id in (?n) ", $params["ps_id"]);
        $join .= db_quote(" LEFT JOIN ?:pumps ON (?:pumps.p_id  = $m_tbl.p_id) ");
    }

    if ($params["only_opened"]){
        $condition .= db_quote(" AND $m_tbl.status != 'Z' ");
    }

    // *************************************************************************
    // 2. ПРИСОЕДИНИТЬ ТАБЛИЦЫ
    // *************************************************************************
//    $join .= db_quote(" LEFT JOIN $j_tbl_1 ON ($j_tbl_1.$j_key_1  = $m_tbl.$j_key_1) ");


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
            'kit_id'=>array_keys($data),
            'with_weight' => true,
            'full_info' => true,
        );
        list($details) = fn_acc__get_kit_details($tmp_p);
        if (is__array($details)){
            foreach ($data as $k_d=>$v_d){
                $data[$k_d]["details"] = $details[$k_d];
            }
        }
    }

    // Запросить информацию о выпуске насосов по каждой партии
    if ($params['with_doc_type_VN']){
        $p = array(
            "type"                      => 13, /*Выпуск насосов*/
            "package_id"                => array_keys($data),
            "package_type"              => "PN",
            "only_active"               => true,

            //WITH_ITEMS
            "with_items"                => true,
            "info_category"             => true,
            "info_item"                 => false,
            "info_unit"                 => false,
            "item_type"                 => array('P', 'PN', 'PA'),

        );
        list($documents) = fn_uns__get_documents($p);
        if (is__array($documents)){
            foreach ($documents as $d){
                foreach ($d["items"] as $i){
                    $data[$d["package_id"]]["date_close"] = $d["date"];
                    $data[$d["package_id"]]["VN"][] = array(
                        "date"      => $d["date"],
                        "item_id"   => $i["item_id"],
                        "item_type" => $i["item_type"],
                        "quantity"  => $i["quantity"],
                        "name"      => $i["item_info"]["p_name"],
                    );
                }
            }
        }
    }

    return array($data, $params);
}

function fn_acc__upd_kit($id = 0, $data){
    $data = trim__data($data);
    if (!is__array($data) || !is_numeric($id) || $id < 0 ) return false;

    // Обновить информацию о KIT
    $id = fn_acc__upd_kit_info($id, $data['kit']);

    // Обновить движения по СЛ
//    fn_uns__upd_document_motions($id);

    return $id;
}


function fn_acc__upd_kit_info($id, $data){
    if (is__more_0($id) and is__array($data) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT kit_id FROM ?:_acc_kits WHERE kit_id = $id"))){
        $operation = "update";
    }else{
        $operation = "add";
        $id = 0;
    }

    $data = trim__data($data);
    $d = array();
    $d["description"]   = $data["description"];
    $d["comment"]       = (strlen($data["comment"]))?$data["description"]:"";
    $d["date_open"]     = fn_parse_date($data["date_open"]);
    $d["status"]        = (fn_check_type($data["status"], UNS_KIT_STATUS))?$data["status"]:UNS_KIT_STATUS__O;
    $d["date_close"]    = 0;
    if ($d["status"] == UNS_KIT_STATUS__Z){
        $d["date_close"]= TIME;
    }

    if ($operation == "update"){
        // ОБНОВИТЬ
        db_query(UNS_DB_PREFIX . "UPDATE ?:_acc_kits SET ?u WHERE kit_id = ?i", $d, $id);
    }elseif ($operation == "add"){
        // ДОБАВИТЬ
        $d["kit_type"]      = (fn_check_type($data["kit_type"], UNS_KIT_TYPE))?$data["kit_type"]:UNS_KIT_TYPE__D;
        if ($d["kit_type"] == UNS_KIT_TYPE__P and fn_get_db_field("p_id", $data["p_id"], "?:pumps") and is__more_0($data["p_quantity"])){
            $d["p_id"] = $data["p_id"];
            $d["p_quantity"] = $data["p_quantity"];
            $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:_acc_kits ?e", $d);
        }elseif ($d["kit_type"] == UNS_KIT_TYPE__D){
            $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:_acc_kits ?e", $d);
        } else return false;
    }
    return $id;
}

function fn_uns__del_kit($id){
    if (!($id = to__array($id))) return false;

    // Удалить сам KIT
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_kits           WHERE kit_id IN (?n)", $id);
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_kit_details    WHERE kit_id IN (?n)", $id);

    // Удалить связанные ДОКУМЕНТЫ
    $sql = db_quote(UNS_DB_PREFIX . "SELECT document_id FROM ?:_acc_documents WHERE package_id IN (?n) AND package_type=?s", $id, UNS_PACKAGE_TYPE__PN);
    $documents = db_get_hash_array($sql, "document_id");
    if (is__array($documents)){
        fn_uns__del_document(array_keys($documents));
    }
    return true;
}

function fn_acc__get_kit_details($params = array(), $items_per_page = 0){
    $default_params = array(
        'kit_id'    => 0,
        'pd_id'     => 0,
        'full_info' =>false,
        'page' => 1,
        'limit' => 0,
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_tbl      = "?:_acc_kit_details";
    $m_key      = "pd_id";

    $j_tbl_1    = "?:details";
    $j_key_1    = "detail_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.kit_id",
        "$m_tbl.detail_id",
        "$m_tbl.quantity",
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
    if ($params["{$m_key}_array"] = to__array($params[$m_key])){
        $condition .= db_quote(" AND $m_tbl.$m_key in (?n)", $params["{$m_key}_array"]);
    }

    // По KIT ID
    if ($params["kit_id_array"] = to__array($params["kit_id"])){
        $condition .= db_quote(" AND $m_tbl.kit_id in (?n)", $params["kit_id_array"]);
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
    $data = db_get_hash_array($sql, "detail_id");
//      fn_print_r(str_replace(array(UNS_DB_PREFIX,"?:"), array("", "uns_"), $sql));
    if (!is__array($data)) return array(array(), $params);

    if ($params["full_info"]){
        $p = array();
        $p["detail_id"] = array_keys($data);
        $p["with_accounting"] = true;
        $p["with_material_info"] = true;
        list($details) = fn_uns__get_details($p);
        if (is__array($details)){
            foreach ($data as $k_data=>$v_data){
                $data[$k_data] = array_merge($data[$k_data], $details[$k_data]);
            }
        }
    }
    $data = fn_group_data_by_field($data, "kit_id");
    // *************************************************************************
    // 6. ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ
    // *************************************************************************
    //foreach ($data as $k_data=>$v_data){}

    return array($data, $params);
}

// ОБНОВИТЬ ДЕТАЛЬ В КОМПЛЕКТАЦИИ ПАРТИИ
function fn_acc__upd_kit_details($kit_id, $data){
    if (!is__more_0($kit_id)
        or !is__array($data)
        or !is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT kit_id FROM ?:_acc_kits WHERE kit_id = $kit_id"))
    ) return false;
    $m_table = "?:_acc_kit_details";
    foreach ($data as $i){
        if (is__more_0($i['quantity'])){
            if (isset($i['state']) and $i['state'] == "N") continue;
            $v = array(
                "quantity"  => $i['quantity'],
            );

            if (is__more_0($i['pd_id']) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT pd_id FROM $m_table WHERE pd_id = ?i", $i['pd_id']))){
                // ОБНОВИТЬ
                db_query(UNS_DB_PREFIX . "UPDATE $m_table SET ?u WHERE pd_id = ?i", $v, $i['pd_id']);
            }elseif ($i["pd_id"] == 0){
                // ДОБАВЛЕНИЕ
                if (is__more_0($i["detail_id"])){
                    $v["kit_id"]    = $kit_id;
                    $v["detail_id"] = $i["detail_id"];
                    db_query(UNS_DB_PREFIX . "INSERT INTO $m_table ?e", $v);
                }
            }
        }
    }
    return true;
}

// УДАЛИТЬ ДЕТАЛЬ ИЗ КОМПЛЕКТАЦИИ ПАРТИИ
function fn_acc__del_kit_details($kit_id, $detail_id){
    if (!is__more_0($kit_id, $detail_id)) return false;

    // Проверка детали перед удалением.
    // Если деталь по этой партии деталей имела движение,
    // то деталь нельзя удалять

    $sql = UNS_DB_PREFIX .
        "SELECT count(?:_acc_documents.document_id) as q
        FROM ?:_acc_documents
          INNER JOIN ?:_acc_document_items ON (?:_acc_documents.document_id = ?:_acc_document_items.document_id)
        WHERE
          ?:_acc_documents.package_type         = 'PN'
          AND ?:_acc_document_items.item_type   = 'D'
          AND ?:_acc_documents.package_id       = {$kit_id}
          AND ?:_acc_document_items.item_id     = {$detail_id}
        ";
    $q = db_get_field($sql);
//    fn_print_r(str_replace(array(UNS_DB_PREFIX,"?:"), array("", "uns_"), $sql));
//    fn_print_r($q);
    if (is__more_0($q)){
        fn_set_notification("W", "Деталь нельзя удалить, так как по ней было движение.", "Сначала необходимо удалить все движения по этой детали.");
        return false;
    }else{
        fn_set_notification("N", "Деталь успешно удалена.");
        db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_kit_details WHERE kit_id=?i AND detail_id=?i", $kit_id, $detail_id);
    }
    return true;
}



