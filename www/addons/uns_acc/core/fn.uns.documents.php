<?php

/**
 * ЗАПРОС ТИПОВ ДОКУМЕНТОВ
 * @param array $params
 * @param int $items_per_page
 * @return array
 */
function fn_uns__get_document_types($params = array(), $items_per_page = 0){
    // *************************************************************************
    // 0. ИНИЦИАЛИЗАЦИЯ
    // *************************************************************************
    $default_params = array(
        'dt_id' => 0,
        'type' => '',
        'limit' => 0,
        'page' => 1,
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_tbl   = "?:_acc_document_types";
    $m_key   = "dt_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.type",
        "$m_tbl.name",
        "$m_tbl.name_short",
        "$m_tbl.order",
        "$m_tbl.status",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.order"   => "asc",
        )
    );

    $total = 0;
    $condition = $join = $limit = $sorting = "";


    // *************************************************************************
    // 1. УСЛОВИЯ ОТБОРА
    // *************************************************************************
    // По ID
    if ($params["{$m_key}_array"] = to__array($params[$m_key])){
        $condition .= db_quote(" AND $m_tbl.$m_key in (?n)", $params["{$m_key}_array"]);
    }

    if ($params["type"]){
        $condition .= db_quote(" AND $m_tbl.type = ?s ", $params["type"]);
    }

    if ($params["status"]){
        $condition .= db_quote(" AND $m_tbl.status = ?s ", $params["status"]);
    }

    // *************************************************************************
    // 2. ПРИСОЕДИНИТЬ ТАБЛИЦЫ
    // *************************************************************************
    // $join .= db_quote(" LEFT JOIN $j_tbl_1 ON ($j_tbl_1.$j_key_1  = $m_tbl.$m_key) ");


    // *************************************************************************
    // 3. ДОПОЛНИЕЛЬНЫЕ УСЛОВИЯ ОТБОРА
    // *************************************************************************




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
    if (!is__array($data)) return array(array(), $params);


    // *************************************************************************
    // 6. ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ
    // *************************************************************************
    //foreach ($data as $k_data=>$v_data){}

//    if ($params["config_1"]){
//    }
//
//    if ($params["config_2"]){
//    }
//
    return array($data, $params, $total);
}





// Получить списоск документов
function fn_uns__get_documents($params = array(), $items_per_page = 0){
    $default_params = array(
        'document_id' => 0,
        'with_items'=>false,
        'visible' => false,
        'only_active' => false,
        'sort_order' => 'desc',
        'page' => 1,
        'limit' => 0,
        'sort_by' => 'position',
        'item_ids' => '',
        'group_by_level' => true,
        'with_q_ty' => true,
        'category_delimiter' => '/',
        'sorting_schemas' => 'view',
        'sheet_id'      => 0,
        'package_state' => "N",
        'package_id'    => 0,
        'package_type'  => "N",
    );

    $params = array_merge($default_params, $params);

    $m_tbl = "?:_acc_documents";
    $m_key = "document_id";

//    $j_tbl_1  = "?:_acc_document_date_cast";
//    $j_key_1  = "document_id";

    $fields = array(
        "$m_tbl.$m_key",
        "$m_tbl.date",
        "$m_tbl.type",
        "$m_tbl.status",
        "$m_tbl.comment",
        "$m_tbl.object_from",
        "$m_tbl.object_to",
        "$m_tbl.date_cast",
        "$m_tbl.package_id",
        "$m_tbl.package_type",
        //        "$j_tbl_1.date_cast",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_tbl.date"  => "desc",
            "$m_tbl.document_id"  => "desc",
        ),
        "view_asc" => array(
            "$m_tbl.date"  => "asc",
            "$m_tbl.document_id"  => "asc",
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
        $condition .= db_quote(" AND ($m_tbl.date between ?i and ?i)", $params['time_from'], $params['time_to']);

    }

    if (is__more_0($params["type"])){
        $condition .= db_quote(" AND $m_tbl.type = ?i", $params['type']);
    }

    // Привязка к ПАКЕТАМ ДОКУМЕНТОВ
    if (is__more_0($params["package_id"]) and fn_check_type($params["package_type"], UNS_PACKAGE_TYPES)){
        $params["package_state"] = "Y";
        $condition .= db_quote(" AND $m_tbl.package_id = ?i AND $m_tbl.package_type = ?s ", $params['package_id'], $params['package_type']);
    }else{
        $params["package_state"] = "N";
        if (is__array($params["packages"])){
            $condition .= db_quote(" AND $m_tbl.package_type in (" . implode(", ", array_map(function($v){return "'$v'";}, $params["packages"])) . ") ");
        }
    }

    // ПО ОБЪЕКТАМ
    if ($params["o_id_array"] = to__array($params["o_id"])){
        if (array_sum($params["o_id_array"])){
            $condition .= db_quote(" AND ($m_tbl.object_from in (?n) OR $m_tbl.object_to in (?n))", $params["o_id_array"], $params["o_id_array"]);
        }
    }



    // *************************************************************************
    // 2. ПРИСОЕДИНИТЬ ТАБЛИЦЫ
    // *************************************************************************
//    $join .= db_quote(" LEFT JOIN $j_tbl_1 ON ($j_tbl_1.$j_key_1  = $m_tbl.$m_key) ");

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
//      fn_print_r(str_replace(array(UNS_DB_PREFIX,"?:"), array("", "uns_"), $sql));
//      $data = fn_group_data_by_field($data, 'task_id');
    if (!is__array($data)) return array(array(), $params);
//    fn_print_r($data);
    // *************************************************************************
    // 6. ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ
    // *************************************************************************
    //foreach ($data as $k_data=>$v_data){}
    if ($params["with_count_items"]){
        $t = "?:_acc_document_items";
        $condition = $group_by = $sorting = $limit = "";
        $condition = db_quote(" AND $t.document_id in (?n)", array_keys($data));
        $group_by = "GROUP BY document_id ";
        $counts = db_get_hash_array(UNS_DB_PREFIX . "SELECT document_id, COUNT(di_id) as count FROM $t WHERE 1 $condition $group_by $sorting $limit", "document_id");
        if (is__array($counts)){
            foreach ($data as $k_d=>$v_d){
                $data[$k_d]["count"] = $counts[$k_d]["count"];
            }
        }
    }

    if ($params["with_items"]){
        $p = array(
            "document_id"   => array_keys($data),
            "info_category" => ($params["info_category"] === false)?false:true,
            "info_item"     => ($params["info_item"]     === false)?false:true,
            "info_unit"     => ($params["info_unit"]     === false)?false:true,
            /*"with_accounting"=>true,*/
        );

        list($items) = fn_uns__get_document_items($p);
        if (is__array($items)){
            foreach ($data as $k_d=>$v_d){
                $data[$k_d]["items"] = $items[$k_d];
            }
        }
    }

    // ПОЛУЧИТЬ ПОЛНУЮ ИНФОРМАЦИЮ О ТИПЕ ДОКУМЕНТА
    if ($params['get_info_document_type']){
        list($document_types) = fn_uns__get_document_types(array('status'=> 'A'));
        foreach ($data as $k_data=>$v_data){
            $data[$k_data]['document_type_info'] = $document_types[$v_data['type']];
        }
    }

    // ПОЛУЧИТЬ ПОЛНУЮ ИНФОРМАЦИЮ ОБ ОБЪЕКТАХ
    if ($params['get_info_objects']){
        list($objects) = fn_uns__get_objects(array('plain' => true, 'all'   => true));
        foreach ($data as $k_data=>$v_data){
            $data[$k_data]['objects_info']['object_from'] = $objects[$v_data['object_from']];
            $data[$k_data]['objects_info']['object_to'] = $objects[$v_data['object_to']];
        }
    }

    // ПОЛУЧИТЬ ДВИЖЕНИЕ ЭЛЕМЕНТОВ по СЛ
    if ($params["movement_items"] and $params["package_state"] == "Y"){
        $movement_items = fn_uns__get_document_movement_items($params["package_id"]);
        if (is__array($movement_items)){
            foreach ($data as $k_data=>$v_data){
                $data[$k_data]['movement_items'] = $movement_items[$k_data];
            }

            $calc_movement_items = fn_uns__get_document_calc_movement_items($params["package_id"], $data);
            foreach ($data as $k_data=>$v_data){
                $data[$k_data]['calc_movement_items'] = $calc_movement_items[$k_data];
            }
        }
    }

    return array($data, $params);
}


function fn_uns__get_document_items($params = array()){
    $default_params = array(
        'document_id' => 0,
        'with_items'=>false,
        'visible' => false,
        'only_active' => false,
        'sort_order' => 'desc',
        'limit' => 0,
        'sort_by' => 'position',
        'item_ids' => '',
        'group_by_level' => true,
        'with_q_ty' => true,
        'category_delimiter' => '/',
        'sorting_schemas' => 'view',

    );

    $params = array_merge($default_params, $params);

    $m_table = "?:_acc_document_items";
    $m_field = "di_id";

    $fields = array(
        "$m_table.$m_field",
        "$m_table.document_id",
        "$m_table.item_type",
        "$m_table.item_id",
        "$m_table.typesize",
        "$m_table.quantity",
        "$m_table.u_id",
        "$m_table.weight",
        "$m_table.change_type",
        "$m_table.processing",
    );

    $sorting_schemas = array(
        "view" => array(
            "$m_table.$m_field"  => "asc",
        )
    );

    $condition = '';

    if ($params['document_id_array'] = to__array($params['document_id'])){
        $condition .= db_quote(" AND $m_table.document_id in (?n)", $params['document_id_array']);
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

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(',', $fields) . " FROM $m_table $join WHERE 1 $condition $group_by $sorting $limit", $m_field);

    if (!is__array($data)) return array(array());

    if ($params["info_category"]){
        // информация о категории
        foreach ($data as $k_d => $v_d) {
            if ($v_d['item_type'] == "D"){
                list($item) = fn_uns__get_details(array('detail_id' => $v_d['item_id']));
            }elseif ($v_d['item_type'] == "M"){
                list($item) = fn_uns__get_materials(array('material_id' => $v_d['item_id']));
            }elseif ($v_d['item_type'] == "P" or $v_d['item_type'] == "PF" or $v_d['item_type'] == "PA"){
                list($item) = fn_uns__get_pumps(array('p_id' => $v_d['item_id']));
            }
            $data[$k_d]['item_info'] = $item[$v_d['item_id']];
        }
    }

    if ($params["info_item"] and $params["info_category"]){
        // информация о материале
        foreach ($data as $k_d=>$v_d){
            if ($v_d['item_type'] == "D"){
                $p = array(
                    'dcat_id' => $v_d['item_info']['dcat_id'],
                    'with_accounting' => true,
                    'with_materials' => true,
                    'format_name' => true
                );
                list ($details) = fn_uns__get_details($p);
                $data[$k_d]['items'] = $details;
            }else{
                $p = array(
                    'mcat_id'           => $v_d['item_info']['mcat_id'],
                    'with_accounting'   => true,
                    'material_id'       => $v_d['item_id'],
                    'format_name' => true
                );
                list ($materials) = fn_uns__get_materials($p);
                $data[$k_d]['items'] = $materials;
            }
        }
    }

    if ($params["info_unit"]){
        // информация о единицах измерениях
        foreach ($data as $k_d => $v_d) {
            list($units) = fn_uns__get_units(array('u_id'=>array($v_d['items'][$v_d['item_id']]['accounting_data']['u_id']/*, UNS_UNIT_WEIGHT*/)));
            $data[$k_d]['units'] = $units;
        }
    }


    // utodo - Плохая реализация - BEGIN
    if ($params["with_accounting"]){
        // информация о категории
        foreach ($data as $k_d => $v_d) {
            if ($v_d['item_type'] == "D"){
                list($item) = fn_uns__get_details(array('detail_id' => $v_d['item_id']));
            }else{
                list($item) = fn_uns__get_materials(array('material_id' => $v_d['item_id']));
            }
            $data[$k_d]['item_info'] = $item[$v_d['item_id']];
        }

        // информация о материале
        foreach ($data as $k_d=>$v_d){
            if ($v_d['item_type'] == "D"){
                $p = array(
                    'dcat_id' => $v_d['item_info']['dcat_id'],
                    'with_accounting' => true,
                    'with_materials' => true,
                    'format_name' => true
                );
                list ($details) = fn_uns__get_details($p);
                $data[$k_d]['items'] = $details;
            }else{
                $p = array(
                    'mcat_id' => $v_d['item_info']['mcat_id'],
                    'with_accounting' => true,
                    'format_name' => true
                );
                list ($materials) = fn_uns__get_materials($p);
                $data[$k_d]['items'] = $materials;
            }
        }

        // информация о единицах измерениях
        foreach ($data as $k_d => $v_d) {
            list($units) = fn_uns__get_units(array('u_id'=>array($v_d['items'][$v_d['item_id']]['accounting_data']['u_id'], UNS_UNIT_WEIGHT)));
            $data[$k_d]['units'] = $units;
        }

    }
    // utodo - Плохая реализация - END


    $tmp = array();
    foreach ($data as $v_d){
        $tmp[$v_d["document_id"]][$v_d[$m_field]] = $v_d;
    }
    $data = $tmp;

    return array($data, $params);
}

// Получитть расчет движения по сопроводительному листу
function fn_uns__get_document_calc_movement_items_old($sheet_id = 0, &$motions){
    $res = array();
    if (is__more_0($sheet_id)){
        // РАСЧЕТ ДВИЖЕНИЯ ПО СОПРОВОДИТЕЛЬНОМУ ЛИСТУ
        $sheet = array_shift(array_shift(fn_acc__get_sheets(array("sheet_id"=>$sheet_id))));
        $calc_motions = array();
        $details = $sheet["details"];
        $enabled_objects = array(10,14,21,22); // мех-цех1, мех-цех2, Брак отжиг, Брак переплавка
        $total = $subtotal = array();
        foreach (array_keys($details) as $detail_id){
            $total[$detail_id] = 0;
        }

        foreach ($enabled_objects as $eo){
            foreach (array_keys($details) as $detail_id){
                $subtotal[$eo][$detail_id] = 0;
            }
        }

        foreach ($motions as $document_id=>$motion){
            $movement_items = $motion["movement_items"];
            if (is__array($movement_items)){
                foreach (array_shift($movement_items) as $object_id=>$motion_items){
                    if (in_array($object_id, $enabled_objects)){
                        if (is__array($motion_items)){
                            foreach (array_keys($details) as $detail_id){
                                if (isset($motion_items[$detail_id])){
                                    $sign = ($motion_items[$detail_id]["motion_type"] == "I")?"+":"-";
                                    $calc_motions[$document_id][$object_id]['current'][$detail_id] = $sign.fn_fvalue($motion_items[$detail_id]["quantity"],0,false);
                                }else{
                                    $calc_motions[$document_id][$object_id]['current'][$detail_id] = 0;
                                }
                                if ($motion["status"] == "D") continue;// Документ в СЛ откл.
                                $subtotal[$object_id][$detail_id] += intval($calc_motions[$document_id][$object_id]['current'][$detail_id]);
                                $calc_motions[$document_id][$object_id]['subtotal'][$detail_id] = $subtotal[$object_id][$detail_id];
                            }
                        }
                    }
                }
                foreach (array_keys($details) as $detail_id){
                    if ($motion["status"] == "D") continue;// Документ в СЛ откл.
                    $total[$detail_id] += intval($calc_motions[$document_id][10]['current'][$detail_id]) ;
                    $total[$detail_id] += intval($calc_motions[$document_id][14]['current'][$detail_id]) ;
                }
                $calc_motions[$document_id]["total"] = $total;
            }
        }
        $res = $calc_motions;
    }
    return $res;
}

// Получитть расчет движения по сопроводительному листу
function fn_uns__get_document_calc_movement_items($sheet_id = 0, &$motions){
    $res = array();
    if (is__more_0($sheet_id)){
        // РАСЧЕТ ДВИЖЕНИЯ ПО СОПРОВОДИТЕЛЬНОМУ ЛИСТУ
        $sheet = array_shift(array_shift(fn_acc__get_sheets(array("sheet_id"=>$sheet_id))));
        $calc_motions = array();
        $details = $sheet["details"];
        $enabled_objects = array(10,14,21,22); // мех-цех1, мех-цех2, Брак отжиг, Брак переплавка

        // *********************************************************************
        // расчет MOTION
        // *********************************************************************
        foreach ($motions as $document_id=>$motion){
            $movement_items = $motion["movement_items"];
            if (is__array($movement_items)){
                foreach (array(10,14,21,22) as $object_id){
                    foreach (array("I", "O") as $motion_type){
                        foreach (array_keys($details) as $detail_id){
                            $q = 0;
                            if ($movement_items[$motion_type][$object_id][$detail_id]["item_type"] == "D"){
                                $q = $movement_items[$motion_type][$object_id][$detail_id]["quantity"];
                            }
                            $calc_motions[$document_id]["motion"][$object_id][$motion_type][$detail_id] = fn_fvalue($q,0,false);
                        }
                    }
                }



            }
        }

        // *********************************************************************
        // расчет BALANCE по каждому объекту
        // *********************************************************************
        foreach ($calc_motions as $document_id=>$motion){
            foreach ($motion["motion"] as $k_obj=>$v_obj){
                $sub_t = array();
                foreach (array_keys($details) as $detail_id){
                    $sub_t[$detail_id] = 0;
                }
                if (is__more_0(array_sum($v_obj["I"]))){
                    foreach ($v_obj["I"] as $k=>$v){
                        $sub_t[$k] += $v;
                    }
                }

                if (is__more_0(array_sum($v_obj["O"]))){
                    foreach ($v_obj["O"] as $k=>$v){
                        $sub_t[$k] -= $v;
                    }
                }

                foreach ($sub_t as $k=>$v){
                    if (is__more_0($v)){
                        $sub_t[$k] = "+".$v;
                    }
                }

                $calc_motions[$document_id]["motion"][$k_obj]["total"]        = $sub_t;
                if ($motions[$document_id]["document_type_info"]["type"] == "VCP_COMPLETE"){
                    $m_I = array_sum($calc_motions[$document_id]["motion"][$k_obj]["I"]);
                    $m_O = array_sum($calc_motions[$document_id]["motion"][$k_obj]["O"]);
                    if (is__more_0($m_I, $m_O) and ($m_I == $m_O)){
                        foreach ($calc_motions[$document_id]["motion"][$k_obj]["I"] as $k=>$v){
                            if (is__more_0($v)){
                                $sub_t[$k] = "&plusmn;".$v;
                            }
                        }
                    }
                }
                $calc_motions[$document_id]["motion"][$k_obj]["total_str"]    = implode("/", $sub_t);
            }
        }

        // *********************************************************************
        // расчет BALANCE по каждому документу
        // *********************************************************************
        $total = $total_obj = array();
        foreach (array_keys($details) as $detail_id){
            $total[$detail_id] = 0;
        }
        foreach ($enabled_objects as $eo){
            foreach (array_keys($details) as $detail_id){
                $total_obj[$eo][$detail_id] = 0;
            }
        }

        foreach ($calc_motions as $document_id=>$motion){
            foreach ($motion["motion"] as $k_obj=>$v_obj){
                if (in_array($k_obj, array(10,14,21,22))){
                    foreach (array_keys($details) as $detail_id){
                        if ($motions[$document_id]["status"] == "A"){
                            $total_obj[$k_obj][$detail_id] += $v_obj["total"][$detail_id];
                        }
                    }
                    $calc_motions[$document_id]["balance"][$k_obj]["total"] = $total_obj[$k_obj];
                    $calc_motions[$document_id]["balance"][$k_obj]["total_str"] = implode("/", $total_obj[$k_obj]);
                }
            }
        }

        // *********************************************************************
        // расчет ИТОГОВОГО BALANCE
        // *********************************************************************
        $total = array();
        foreach (array_keys($details) as $detail_id){
            $total[$detail_id] = 0;
        }

        foreach ($calc_motions as $document_id=>$motion){
            foreach ($motion["motion"] as $k_obj=>$v_obj){
                if (in_array($k_obj, array(10,14))){
                    foreach (array_keys($details) as $detail_id){
                        if ($motions[$document_id]["status"] == "A"){
                            $total[$detail_id] += $v_obj["I"][$detail_id];
                            $total[$detail_id] -= $v_obj["O"][$detail_id];
                        }
                    }
                    $calc_motions[$document_id]["balance"]["total"] = $total;
                    $calc_motions[$document_id]["balance"]["total_str"] = implode("/", $total);
                }
            }
        }

        $res = $calc_motions;
    }
    return $res;
}

// Получить движение по сопроводительному листу
function fn_uns__get_document_movement_items($sheet_id = 0){
    $res = array();
    if (is__more_0($sheet_id)){
        $sql = "
            SELECT
              uns__acc_document_items.di_id,
              uns__acc_documents.document_id,
              uns__acc_motions.object_id,
              uns__acc_document_items.item_type,
              uns__acc_document_items.motion_type,
              uns__acc_document_items.item_id,
              uns__acc_document_items.quantity,
              uns__acc_document_items.weight,
              uns__acc_document_items.processing
            FROM
              uns__acc_documents
              LEFT JOIN uns__acc_document_items ON (uns__acc_documents.document_id = uns__acc_document_items.document_id)
              LEFT JOIN uns__acc_motions        ON (uns__acc_document_items.document_id = uns__acc_motions.document_id AND uns__acc_document_items.motion_type = uns__acc_motions.motion_type)
            WHERE
              uns__acc_documents.package_id = {$sheet_id}
              AND uns__acc_documents.package_type = 'SL'
            ORDER BY
              uns__acc_documents.document_id
        ";
        $data = db_get_array(UNS_DB_PREFIX . $sql);
        if (is_array($data)){
            $data = fn_group_data_by_field($data, "document_id");
            foreach ($data as $k_data=>$v_data){
                foreach ($v_data as $m){
                    $res[$k_data][$m["motion_type"]][$m["object_id"]][$m["item_id"]] = $m;
                }
            }
        }
    }
    return $res;
}


//******************************************************************************
// ОБНОВИТЬ ДОКУМЕНТ
//******************************************************************************
function fn_uns__upd_document_info($id = 0, $doc){;
    if (is__more_0($id) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT document_id FROM ?:_acc_documents WHERE document_id = $id"))){
        $operation = "update";
        $type = db_get_field(UNS_DB_PREFIX . "SELECT type FROM ?:_acc_documents WHERE document_id = $id");
    }else{
        $operation = "add";
        $type = $doc['type'];
    }

    $d = array();

    $d["date"]      = fn_concat_date_time($doc);
    $d["date_cast"] = fn_parse_date($doc["date_cast"]);
//    $d["date"]      = fn_add_current_time(fn_parse_date($doc["date"]));
//    $d["date_cast"] = fn_add_current_time(fn_parse_date($doc["date_cast"]));
    $d["status"]    = (in_array($doc["status"], array("A","D")))?$doc["status"]:"A";
    $d["comment"]   = (strlen($doc["comment"]))?$doc["comment"]:"";
    if (is__more_0($doc["package_id"]) and fn_check_type($doc["package_type"], UNS_PACKAGE_TYPES)){
        $d["package"]  = "Y";
        $d["package_id"]  = $doc["package_id"];
        $d["package_type"]  = $doc["package_type"];
    }else{
        $d["package"]  = "N";
        $d["package_id"]  = 0;
        $d["package_type"]  = UNS_PACKAGE_TYPE__N;
    }

    switch($type){
        case DOC_TYPE__VLC: // Выпуск Лит. цеха
            if ($operation == 'add'){
                if(!is__more_0($type, $doc["object_to"], $doc["object_from"])) return false;
                else{
                    $d["type"]          = $type;
                    $d["object_to"]     = $doc["object_to"];
                    $d["object_from"]   = $doc["object_from"];
                }
            }
        break;

        case DOC_TYPE__MCP:         // Межцеховое перемещение
        case DOC_TYPE__VD:          // Выпуск деталей
        case DOC_TYPE__VN:          // Выпуск насоса
        case DOC_TYPE__VCP:         // Внутрицеховое перемещение
        case DOC_TYPE__VCP_COMPLETE:// Обработка детали
            if ($operation == "add"){
                if(!is__more_0($type/*, $doc["object_to"], $doc["object_from"]*/)) return false;
                else{
                    $d["type"]          = $type;
                    $d["object_to"]     = $doc["object_to"];
                    $d["object_from"]   = $doc["object_from"];
                }
            }elseif($operation == "update"){
                if (is__more_0($doc["object_to"]))      $d["object_to"]     = $doc["object_to"];
                if (is__more_0($doc["object_from"]))    $d["object_from"]   = $doc["object_from"];
            }
        break;

        case DOC_TYPE__RO:      // Расходный ордер
        case DOC_TYPE__AIO:     // АКТ изменения отстатка
        case DOC_TYPE__AS_VLC:  // АКТ списания мат. на лит. цех
            if ($operation == 'add'){
                if(!is__more_0($type, $doc["object_to"])) return false;
                else{
                    $d["type"]        = $type;
                    $d["object_to"]   = $doc["object_to"];
                }
            }
        break;


        case DOC_TYPE__PVP: // ПЕРЕДАЧА В ПРОИЗВОДСТВО
            if(!is__more_0($type, $doc["object_to"], $doc["object_from"])) return false;
            else{
                if ($operation == 'add'){
                    $d["date"]          = fn_concat_date_time($doc);
                    $d["type"]          = $type;
                    $d["object_from"]   = $doc["object_from"];
                    $d["object_to"]     = $doc["object_to"];
                }elseif ($operation == 'update'){
                    $d["date"]          = fn_concat_date_time($doc);
                    $d["object_to"]     = $doc["object_to"];
                }
            }
        break;

        case DOC_TYPE__BRAK: // В БРАК
            if(!is__more_0($type, $doc["object_to"], $doc["object_from"])) return false;
            else{
                if ($operation == 'add'){
                    $d["date"]          = fn_concat_date_time($doc);
                }
                $d["object_to"]     = $doc["object_to"];
                $d["object_from"]   = $doc["object_from"];
                $d["type"]          = $type;
            }
        break;

        default:
            return false;
            break;
    }

    if ($operation == "add"){
        $id = db_query(UNS_DB_PREFIX . "INSERT INTO ?:_acc_documents ?e", $d);
    }else{
        db_query(UNS_DB_PREFIX . "UPDATE ?:_acc_documents SET ?u WHERE document_id = ?i", $d, $id);
    }

    return $id;
}


//******************************************************************************
// ОБНОВИТЬ СТАТУС ДОКУМЕНТ
//******************************************************************************
function fn_uns__upd_document_status($id, $status){;
    if (is__more_0($id) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT document_id FROM ?:_acc_documents WHERE document_id = $id"))){
        db_query(UNS_DB_PREFIX . "UPDATE ?:_acc_documents SET ?u WHERE document_id = ?i", array("status" => $status), $id);
    } else return false;
    return true;
}


//******************************************************************************
// Обновить позиции документа UNS__ACC_DOCUMENT_ITEMS
//******************************************************************************
function fn_uns__upd_document_items($document_id, $data){
    if (!is__more_0($document_id) or !is__array($data)) return false;
    $m_table = "?:_acc_document_items";
    $di_ids = array();
    $document = array_shift(array_shift(fn_uns__get_documents(array('document_id'=>$document_id))));
    foreach ($data as $i){
        if (is__more_0($i['item_id']) and  is_numeric($i['quantity']) and fn_check_type($i['item_type'], UNS_ITEM_TYPES)){
            if (fn_check_type($i['state'], "|Y|N|") and $i['state'] == "N") continue;
            $v = array(
                'document_id'       => $document_id,
                'item_type'         => $i['item_type'],
                'item_id'           => $i['item_id'],
                'quantity'          => abs($i['quantity']),
            );

            //******************************************************************
            // запрос ВЕС
            if (is__more_0($i['weight'])){
                $v['weight'] = $i['weight'];
            }else{
                $w = fn_uns__get_accounting_item_weights($i['item_type'], $i['item_id']);
                $v['weight'] = $w[$i['item_id']]["M"][0]['value'];
            }

            //******************************************************************
            // запрос ЕДИНИЦ ИЗМЕРЕНИЯ
            if (is__more_0($i['u_id'])){
                $v['u_id'] = $i['u_id'];
            }else{
                $u = fn_uns__get_accounting_items($i['item_type'], $i['item_id']);
                $v['u_id'] = $u[$i['item_id']]['u_id'];
            }

            //******************************************************************
            // запрос ТИПОРАЗМЕРА ДАТАЛИ
            if (fn_check_type($i['typesize'], UNS_TYPESIZES)){
                $v['typesize'] = $i['typesize'];
            }else{
                $v['typesize'] = "M";
            }
            //******************************************************************
            // запрос статуса обработки
            if ($v['item_type'] == "D" and fn_check_type($i['processing'], "|P|C|")){
                $v['processing'] = $i['processing'];
            }

            //******************************************************************
            if (fn_check_type($i['motion_type'], UNS_MOTION_TYPES)){
                $v['motion_type'] = $i['motion_type'];
            }else{
                $v['motion_type'] = UNS_MOTION_TYPE__IO;
            }

            //******************************************************************
            if ($document['type'] == DOC_TYPE__AIO){
                $v['change_type'] = ($i['quantity']>0)?'POZ':'NEG';
            }

            if (is__more_0($i['di_id']) and is__more_0(db_get_field(UNS_DB_PREFIX . "SELECT di_id FROM $m_table WHERE di_id = ?i", $i['di_id']))){
                // ОБНОВИТЬ
                db_query(UNS_DB_PREFIX . "UPDATE $m_table SET ?u WHERE di_id = ?i", $v, $i['di_id']);
                $di_ids[] = $i['di_id'];
            }elseif ($i['di_id'] == 0){
                // ДОБАВЛЕНИЕ
                $di_ids[] = db_query(UNS_DB_PREFIX . "INSERT INTO $m_table ?e", $v);
            }
        }
    }
    if (is__array($di_ids)){
        // Удалить все лишнее
        db_query(UNS_DB_PREFIX . "DELETE FROM $m_table WHERE document_id = ?i AND di_id not in (?n) ", $document_id, $di_ids);
    }else{
        db_query(UNS_DB_PREFIX . "DELETE FROM $m_table WHERE document_id = ?i ", $document_id);
    }

    return true;
}


//******************************************************************************
// ОБНОВИТЬ ДОКУМЕНТ В "ЖУРНАЛЕ ДВИЖЕНИЙ"
//******************************************************************************
function fn_uns__upd_document_motions($document_id){
    if (!is__more_0($document_id)) return false;

    $m_table = "?:_acc_motions";

    // 1. Удалить любое движение по этому документу
    db_query(UNS_DB_PREFIX . "DELETE FROM $m_table WHERE document_id = ?i ", $document_id);

    $doc = array_shift(array_shift(fn_uns__get_documents(array('document_id'=>$document_id, 'sheet_id'=>''))));
    if (is__array($doc)){
        $v_in = $v_out = array();

        // РАСХОД c объекта ====================================================
        switch ($doc["type"]){
            case DOC_TYPE__VLC:         // Выпуск лит. цеха
            case DOC_TYPE__MCP:         // Межцеховое перемещение
            case DOC_TYPE__VD:          // Выпуск деталей
            case DOC_TYPE__VN:          // Выпуск деталей
            case DOC_TYPE__VCP:         // Внутрицеховое перемещение
            case DOC_TYPE__VCP_COMPLETE:
            case DOC_TYPE__BRAK:        // Брак
            case DOC_TYPE__PVP:         // Передача в производство
                $v_out = array(
                    "motion_type"   => "O",
                    "object_id"     => $doc["object_from"],
                    "document_id"   => $document_id,
                );
            break;

            case DOC_TYPE__RO: // РАСХОДНЫЙ ОРДЕР
            case DOC_TYPE__AS_VLC: // Акт списания мат. на лит. цех
            case DOC_TYPE__AIO: // АКТ изменения остатков
                $v_out = array(
                    "motion_type"   => "O",
                    "object_id"     => $doc["object_to"],
                    "document_id"   => $document_id,
                );
            break;

        }

        // ПРИХОД на объект ====================================================
        switch ($doc["type"]){
            case DOC_TYPE__AIO: // АКТ изменения остатков
            case DOC_TYPE__VLC: // Выпуск лит. цеха
            case DOC_TYPE__MCP: // Межцеховое перемещение
            case DOC_TYPE__VD:  // Выпуск деталей
            case DOC_TYPE__VN:  // Выпуск деталей
            case DOC_TYPE__VCP: // Внутрицеховое перемещение
            case DOC_TYPE__VCP_COMPLETE:
            case DOC_TYPE__BRAK: // Брак
            case DOC_TYPE__PVP: // Передача в производство
                $v_in = array(
                    "motion_type"   => "I",
                    "object_id"     => $doc["object_to"],
                    "document_id"   => $document_id,
                );
            break;
        }

        if (is__array($v_out)){
            db_query(UNS_DB_PREFIX . "INSERT INTO $m_table ?e", $v_out);
        }

        if (is__array($v_in)){
            db_query(UNS_DB_PREFIX . "INSERT INTO $m_table ?e", $v_in);
        }
    }
    return true;
}

//******************************************************************************
// ОБНОВИТЬ ПОЗИЦИИ ДОКУМЕНТА В "ЖУРНАЛЕ ДВИЖЕНИЙ"
//******************************************************************************
function fn_uns__upd_document_items_motions($document_id, $document_status){
    if (!is__more_0($document_id) or !in_array($document_status, array("Y", "N"))) return false;

    db_query(UNS_DB_PREFIX . 'UPDATE ?:_acc_documents SET ?u WHERE document_id = ?i', array("document_status" => $document_status), $document_id);

    $m_table = "?:_acc_motions";

    // 1. Удалить любое движение по этому документу
    db_query(UNS_DB_PREFIX . "DELETE FROM $m_table WHERE document_id = ?i ", $document_id);

    // 2. Если статус документа - УЧИТЫВАТЬ, тогда добавить новые движения
    if ($document_status == "Y"){
        list($doc) = fn_uns__get_documents(array('with_items'=>true, 'document_id'=>$document_id));
        $doc = array_shift($doc);
        if (is__array($doc) and is__array($doc["items"])){
            $main_data = array(
                "document_id" => $document_id,
                "document_date" => $doc["document_date"],
                "document_type" => $doc["document_type"],
            );

            foreach ($doc["items"] as $i){
                $v_in = $v_out = array();

                switch ($doc["document_type"]){
                    case UNS_DOCUMENT__PRIH_ORD:
                        $v_in = array(
                            "motion_type"   => "IN",
                            "object_id"     => $doc["object_id_to"],

                            "item_type"     => $i["item_type"],
                            "item_id"       => $i["item_id"],

                            "q_in"          => $i["quantity"],
                            "koef_in"       => ($i["items"][$i["item_id"]]["accounting_data"]["u_id"] == $i["u_id"])?1:$i["items"][$i["item_id"]]["accounting_data"]["weight"],
                            "u_id"          => $i["u_id"],
                        );

                        break;
                    case UNS_DOCUMENT__RASH_ORD:
                        break;
                    case UNS_DOCUMENT__NOPM:
                    case UNS_DOCUMENT__INPM:
                        $v_in = array(
                            "motion_type"   => "IN",
                            "object_id"     => $doc["object_id_to"],

                            "item_type"     => $i["item_type"],
                            "item_id"       => $i["item_id"],

                            "q_in"          => $i["quantity"],
                            "koef_in"       => ($i["items"][$i["item_id"]]["accounting_data"]["u_id"] == $i["u_id"])?1:$i["items"][$i["item_id"]]["accounting_data"]["weight"],
                            "u_id"          => $i["u_id"],
                        );

                        $v_out = array(
                            "motion_type"   => "OUT",
                            "object_id"     => $doc["object_id_from"],

                            "item_type"     => $i["item_type"],
                            "item_id"       => $i["item_id"],

                            "q_out"          => $i["quantity"],
                            "koef_out"       => ($i["items"][$i["item_id"]]["accounting_data"]["u_id"] == $i["u_id"])?1:$i["items"][$i["item_id"]]["accounting_data"]["weight"],
                            "u_id"          => $i["u_id"],
                        );
                        break;
                    case UNS_DOCUMENT__SDAT_N:
                        $v_in = array(
                            "motion_type"   => "IN",
                            "object_id"     => $doc["object_id_to"],

                            "item_type"     => $i["item_type"],
                            "item_id"       => $i["item_id"],

                            "q_in"          => $i["quantity"],
                            "koef_in"       => ($i["items"][$i["item_id"]]["accounting_data"]["u_id"] == $i["u_id"])?1:$i["items"][$i["item_id"]]["accounting_data"]["weight"],
                            "u_id"          => $i["u_id"],
                        );
                        break;
                }

                if (is__array($v_in)){
                    db_query(UNS_DB_PREFIX . "INSERT INTO $m_table ?e", array_merge($main_data, $v_in));
                }

                if (is__array($v_out)){
                    db_query(UNS_DB_PREFIX . "INSERT INTO $m_table ?e", array_merge($main_data, $v_out));
                }
            }
        }
    }
    return true;
}




/**
 * ОБНОВИТЬ ДОКУМЕНТ
 * @param int $id
 * @param $data
 * @return bool|int
 */
function fn_uns__upd_document($id = 0, $data){
    $data = trim__data($data);
    if (!is__array($data) || !is_numeric($id) || $id < 0 ) return false;

    // Обновить информацию о документе
    $id = fn_uns__upd_document_info($id, $data['document']);

    // Обновить позиции документа UNS__ACC_DOCUMENT_ITEMS
    fn_uns__upd_document_items($id, $data['document_items']);

    // Обновить позиции документа в ЖУРНАЛЕ ДВИЖЕНИЙ
    fn_uns__upd_document_motions($id);

    return $id;
}


function fn_uns__del_document ($id){
    if (!($id = to__array($id))) return false;

    // Удалить сам ДОКУМЕНТ
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_documents WHERE document_id IN (?n)", $id);

    // Удалить ПОЗИЦИИ ДОКУМЕНТА
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_document_items WHERE document_id IN (?n)", $id);

    // Удалить ДВИЖЕНИЯ по ДОКУМЕНТУ
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:_acc_motions WHERE document_id IN (?n)", $id);

    return true;
}

function fn_uns__calc_total_weight($documents, $only_total_weight=false){
    $total_weight = array("C"=>0, "S" => 0);
    if (is__array($documents)){
        foreach ($documents as $k=>$document){
            $weight = array("C"=>0, "S" => 0);
            if (is__array($document["items"])){
                foreach ($document["items"] as $k_i=>$v_i){
                    if ($v_i["item_type"] == "M"){
                        $i = $v_i["items"][$v_i["item_id"]];
                        $w_i = $i["accounting_data"]["weight"]*$v_i["quantity"];
                        $document["items"][$k_i]["weight"] = $w_i;
                        $weight[$i["type_casting"]] += $w_i;
                    }
                }
            }
            $documents[$k]["weight"] = $weight;
            if ($only_total_weight) {
                $total_weight["C"] += $weight["C"];
                $total_weight["S"] += $weight["S"];
            }
        }
    }
    return ((!$only_total_weight)?$documents:$total_weight);
}
