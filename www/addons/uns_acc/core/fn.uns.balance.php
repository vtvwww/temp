<?php

function fn_uns__get_type_balance_for_object($o_id){
    // A - аналитический анализ - сумма
    // S - синтетическмй анализ
    $types = array(
        4  => array("type"=>"A", "objects"=>array(5,6)),                        //  |--->Склад СМП + К [4]
        5  => array("type"=>"S", "objects"=>array(5)),                          //  |    |--->Склад СМП [5]
        6  => array("type"=>"S", "objects"=>array(6)),                          //  |    `--->Склад К [6]
                                                                                //  |
        7  => array("type"=>"S", "objects"=>array(7)),                          //  |--->ЦЕХ Литейный [7]
                                                                                //  |
        8  => array("type"=>"S", "objects"=>array(8)),                          //  |--->Склад литья [8]
                                                                                //  |
        23 => array("type"=>"S", "objects"=>array(23)),                         //  |--->ЦЕХ Лопата [23]
                                                                                //  |
        9  => array("type"=>"A", "objects"=>array(10, 14)),                     //  |--->ЦЕХ Мех. обработки [9]
        24 => array("type"=>"S", "objects"=>array(24)),                         //  |    |--->Склад материалов мех. цеха [24]
        10 => array("type"=>"S", "objects"=>array(10)),                         //  |    |--->№1 [10]
//        12 => array("type"=>"S", "objects"=>array(12)),                         //  |    |    |--->В обработке [12]
//        13 => array("type"=>"S", "objects"=>array(13)),                         //  |    |    `--->Гот. изд. [13]
        14 => array("type"=>"S", "objects"=>array(14)),                         //  |    `--->№2 [14]
//        15 => array("type"=>"S", "objects"=>array(15)),                         //  |         |--->В обработке [15]
//        16 => array("type"=>"S", "objects"=>array(16)),                         //  |         `--->Гот. изд. [16]
                                                                                //  |
        17 => array("type"=>"S", "objects"=>array(17)),                         //  |--->Склад деталей [17]
                                                                                //  |
        18 => array("type"=>"S", "objects"=>array(18)),                         //  |--->Участок сборки  [18]
                                                                                //  |
        19 => array("type"=>"S", "objects"=>array(19)),                         //  |--->Склад Гот. продукции [19]
                                                                                //  |
        20 => array("type"=>"A", "objects"=>array(21,22)),                      //  `--->Склад Брака [20]
        21 => array("type"=>"S", "objects"=>array(21)),                         //       |--->На отжиг [21]
        22 => array("type"=>"S", "objects"=>array(22)),                         //       `--->На переплавку [22]
    );

    return (is__more_0($o_id)?$types[$o_id]:false);
}

function fn_uns__get_balance($params = array(), $time=false){
    if ($time) $start_time = microtime_float();
    $default_params = array(
        'time_from' =>'',
        'time_to'=>'',

        'dcat_id'=>0,
        'detail_no'=>'',
        'detail_name'=>'',

        'mclass_id'=>0,
        'mcat_id'=>0,
        'material_no'=>'',
        'material_name'=>'',

        'o_id'=>0,

        'limit' => 0,
        'sort_by' => 'position',
        'item_ids' => '',
        'sorting_schemas' => 'view',

    );

    $params = trim__data($params);

    $params = array_merge($default_params, $params);

    $m_tbl = "?:_acc_document_items";
    $m_key = "item_id";

    $fields = array(
        "$m_tbl.item_id",
        "(ifnull(nach_prih.total,0)-ifnull(nach_rash.total,0)) as no",
        "(ifnull(tek_prih.total,0)) as prih",
        "(ifnull(tek_rash.total,0)) as rash",
        "(ifnull(nach_prih.total,0)-ifnull(nach_rash.total,0)+ifnull(tek_prih.total,0)-ifnull(tek_rash.total,0)) as ko",
    );

    $sorting_schemas = array(
        "view_1" => array(
            "m.document_id"  => "desc",
        )
    );


    $condition = $limit = $join = $group_by = $sorting = '';


    // =========================================================================
    // УСЛОВИЯ
    // =========================================================================
    // 1. ОБЪЕКТЫ
    $cond__objects = array();     // Объект по которому необходимо собрать данные
    if (!$params['o_id_array'] = to__array($params['o_id'])) return false;
    else {
        foreach ($params['o_id_array'] as $o_id){
            $o_s = fn_uns__get_type_balance_for_object($o_id);
            $cond__objects += $o_s['objects'];
        }
    }


    // 2. Типы документов
    if (is__array($params["prihod_doc_types"])) $cond_prih__doc_types    = $params["prihod_doc_types"];
    else{
        $cond_prih__doc_types    = array("'AIO'", "'VLC'");         // Типы документов ПРИХОДА по объекту + "8" -  АКТ ИЗМ. ОСТАТ
    }
    if (is__array($params["rashod_doc_types"])) $cond_rash__doc_types    = $params["rashod_doc_types"];
    else{
        $cond_rash__doc_types    = array("'AIO'", "'MCP'", "'RO'", "'AS_VLC'", "'PVP'"); // Типы документов РАСХОДА по объекту
    }

    //**************************************************************************
    // 3. ДОПОЛНИТЕЛЬНЫЕ УСЛОВИЯ
    //**************************************************************************
    $cond_prih = $cond_rash = "";
    // ПРИХОД
    if (strlen($params["cond_prih"])) $cond_prih .= $params["cond_prih"];
    else{
        $cond_prih .=   "
                       AND (
                            (uns__acc_document_types.type = 'VLC' and uns__acc_document_items.change_type in ('POZ', 'NEG'))
                            OR
                            (uns__acc_document_types.type = 'AIO' and uns__acc_document_items.change_type = 'POZ')
                       )
                            ";
    }

    // РАСХОД
    if (strlen($params["cond_rash"])) $cond_rash .= $params["cond_rash"];
    else{
        $cond_rash .= "
                       AND (
                         (uns__acc_document_types.type = 'MCP' and uns__acc_document_items.change_type in ('POZ', 'NEG'))
                         OR
                         (uns__acc_document_types.type = 'PVP')
                         OR
                         (uns__acc_document_types.type = 'AIO' and uns__acc_document_items.change_type = 'NEG')
                         OR
                         (uns__acc_document_types.type = 'RO' and uns__acc_document_items.change_type = 'POZ')
                         OR
                         (uns__acc_document_types.type = 'AS_VLC' and uns__acc_document_items.change_type = 'POZ')
                       )
                        ";
    }

    //**************************************************************************

    // 3. Items
    $cond__items = '';
    if (fn_check_type($params['item_type'], UNS_ITEM_TYPES)){
        $cond__items .= db_quote(" AND uns__acc_document_items.item_type = ?s AND uns__acc_document_items.typesize = 'M' ", $params['item_type']);
        if ($params['item_id_array'] = to__array($params['item_id'])){
            $cond__items .= db_quote(" AND uns__acc_document_items.item_id in (?n) ", $params['item_id_array']);
        }
//        if ($params['item_type'] == 'M'){
//            if (is__more_0($params['item_id'])){
//                $cond__items .= db_quote(" AND uns__acc_document_items.item_id = ?i ", $params['item_id']);
//            }
//        }elseif ($params['item_type'] == 'D'){
//            if ($params['item_id_array'] = to__array($params['item_id'])){
//                $cond__items .= db_quote(" AND uns__acc_document_items.item_id in (?n) ", $params['item_id_array']);
//            }
//        }elseif (fn_check_type($params['item_type'], UNS_ITEM_TYPES)){
//            $cond__items .= db_quote(" AND uns__acc_document_items.item_type = ?s ", $params['item_type']);
//        }
    }

    if (fn_check_type($params['package_type'], UNS_PACKAGE_TYPES) and is__more_0($params['package_id'])){
        $cond__items .= db_quote(" AND uns__acc_documents.package_id = ?i ", $params['package_id']);
        $cond__items .= db_quote(" AND uns__acc_documents.package_type = ?s ", $params['package_type']);
    }

    // Категории деталей
    if (is__array($params['dcat_id_array'] = to__array($params['dcat_id']))){
        $cond__tables = " , uns_details ";
        $cond__items .= db_quote(" AND uns__acc_document_items.item_id = uns_details.detail_id ");
        $cond__items .= db_quote(" AND uns_details.dcat_id in (?n) ", $params['dcat_id_array']);
    }

    // =========================================================================
    // JOINS
    // =========================================================================
    $join .= db_quote("
      LEFT JOIN (SELECT
                   uns__acc_document_items.item_id,
                   ifnull(sum(uns__acc_document_items.quantity), 0) AS total
                 FROM uns__acc_document_items
                   , uns__acc_documents
                   , uns__acc_motions
                   , uns__acc_document_types
                   {$cond__tables}
                 WHERE 1 {$cond__items}
                       {$cond_prih}
                       #UNS__ACC_DOCUMENTS
                       AND uns__acc_documents.document_id = uns__acc_document_items.document_id
                       AND uns__acc_documents.status = 'A'
                       AND uns__acc_documents.date < {$params['time_from']}

                       #UNS__ACC_DOCUMENT_TYPES
                       AND uns__acc_document_types.dt_id = uns__acc_documents.type
                       AND uns__acc_document_types.type IN (". implode(', ', $cond_prih__doc_types) .")

                       #UNS__ACC_MOTIONS
                       AND uns__acc_motions.document_id = uns__acc_document_items.document_id
                       AND uns__acc_motions.motion_type             = 'I'
                       AND uns__acc_document_items.motion_type like  '%I%'
                       AND uns__acc_motions.object_id IN (". implode(", ", $cond__objects) .")
                 GROUP BY uns__acc_document_items.item_id
      ) as nach_prih ON (uns__acc_document_items.item_id = nach_prih.item_id)

      LEFT JOIN (SELECT
                   uns__acc_document_items.item_id,
                   ifnull(sum(uns__acc_document_items.quantity), 0) AS total
                 FROM uns__acc_document_items
                   , uns__acc_documents
                   , uns__acc_motions
                   , uns__acc_document_types
                   {$cond__tables}
                 WHERE 1 {$cond__items}
                       {$cond_rash}
                       #UNS__ACC_DOCUMENTS
                       AND uns__acc_documents.document_id = uns__acc_document_items.document_id
                       AND uns__acc_documents.status = 'A'
                       AND uns__acc_documents.date < {$params['time_from']}

                       #UNS__ACC_DOCUMENT_TYPES
                       AND uns__acc_document_types.dt_id = uns__acc_documents.type
                       AND uns__acc_document_types.type IN (". implode(", ", $cond_rash__doc_types) .")

                       #UNS__ACC_MOTIONS
                       AND uns__acc_motions.document_id = uns__acc_document_items.document_id
                       AND uns__acc_motions.motion_type             = 'O'
                       AND uns__acc_document_items.motion_type like  '%O%'

                       AND uns__acc_motions.object_id IN (". implode(", ", $cond__objects) .")
                 GROUP BY uns__acc_document_items.item_id
      ) as nach_rash ON (uns__acc_document_items.item_id = nach_rash.item_id)

      LEFT JOIN (SELECT
                   uns__acc_document_items.item_id,
                   ifnull(sum(uns__acc_document_items.quantity), 0) AS total
                 FROM uns__acc_document_items
                   , uns__acc_documents
                   , uns__acc_motions
                   , uns__acc_document_types
                   {$cond__tables}
                 WHERE 1 {$cond__items}
                       {$cond_prih}
                       #UNS__ACC_DOCUMENTS
                       AND uns__acc_documents.document_id = uns__acc_document_items.document_id
                       AND uns__acc_documents.status = 'A'
                         AND uns__acc_documents.date BETWEEN {$params['time_from']} AND {$params['time_to']}

                       #UNS__ACC_DOCUMENT_TYPES
                       AND uns__acc_document_types.dt_id = uns__acc_documents.type
                       AND uns__acc_document_types.type IN (". implode(", ", $cond_prih__doc_types) .")

                       #UNS__ACC_MOTIONS
                       AND uns__acc_motions.document_id = uns__acc_document_items.document_id
                       AND uns__acc_motions.motion_type             = 'I'
                       AND uns__acc_document_items.motion_type like  '%I%'
                       AND uns__acc_motions.object_id IN (". implode(", ", $cond__objects) .")
                 GROUP BY uns__acc_document_items.item_id
      ) as tek_prih ON (uns__acc_document_items.item_id = tek_prih.item_id)

      LEFT JOIN (SELECT
                   uns__acc_document_items.item_id,
                   ifnull(sum(uns__acc_document_items.quantity), 0) AS total
                 FROM uns__acc_document_items
                   , uns__acc_documents
                   , uns__acc_motions
                   , uns__acc_document_types
                   {$cond__tables}
                 WHERE 1 {$cond__items}
                       {$cond_rash}
                       #UNS__ACC_DOCUMENTS
                       AND uns__acc_documents.document_id = uns__acc_document_items.document_id
                       AND uns__acc_documents.status = 'A'
                         AND uns__acc_documents.date BETWEEN {$params['time_from']} AND {$params['time_to']}

                       #UNS__ACC_DOCUMENT_TYPES
                       AND uns__acc_document_types.dt_id = uns__acc_documents.type
                       AND uns__acc_document_types.type IN (". implode(", ", $cond_rash__doc_types) .")

                       #UNS__ACC_MOTIONS
                       AND uns__acc_motions.document_id = uns__acc_document_items.document_id
                       AND uns__acc_motions.motion_type             = 'O'
                       AND uns__acc_document_items.motion_type like  '%O%'
                       AND uns__acc_motions.object_id IN (". implode(", ", $cond__objects) .")
                 GROUP BY uns__acc_document_items.item_id
      ) as tek_rash ON (uns__acc_document_items.item_id = tek_rash.item_id)
      ");

    // =========================================================================
    // УСЛОВИЯ
    // =========================================================================
    $condition .= db_quote(" AND nach_prih.total or nach_rash.total or tek_prih.total or tek_rash.total ");

    //**************************************************************************

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

    $sql = UNS_DB_PREFIX . "SELECT DISTINCT " . implode(", ", $fields) . " FROM $m_tbl $join WHERE 1 $condition $sorting $limit";
//    fn_print_r(str_replace(array(UNS_DB_PREFIX,"?:"), array("", "uns_"), $sql));
    $data = db_get_hash_array($sql, $m_key);
//    fn_print_r($data);
//    if (!is__array($data)) return array(array(), $params);

    // ИНФОРМАЦИЯ О ЛИТЬЕ
    if ($params["item_type"] == "M" and $params["add_item_info"] and is__more_0($params["mclass_id"])){
        // Запросить ниформация по литейным отливкам
        $p = array();
        if (is__more_0($params['mclass_id'])){
            $p["mclass_id"] = $params['mclass_id'];
        }

        if (is__more_0($params['mcat_id'])){
            $p["mcat_id"] = $params['mcat_id'];
            $p["mcat_include_target"] = true;
        }else{
            $p["mcat_id"] = 27; //Отливки 
        }

        if (in_array($params['type_casting'], array("C", "S"))){
            $p["type_casting"] = $params['type_casting'];
        }

        if (strlen($params['material_name'])){
            $p["material_name"] = $params['material_name'];
        }

        if (strlen($params['material_no'])){
            $p["material_no"] = $params['material_no'];
        }

        $p['sorting_schemas'] = $params['sorting_schemas'];

        list($mcats) = fn_uns__get_materials_categories($p);
        if (is__array($mcats)){
            $mcat_items = array();
            $p["material_status"] = "A";
            $p["mcat_id"] = array_keys($mcats);
            if ($params["view_all_position"] == "N"){
                $p["material_id"] = array_keys($data);
            }
            if (is__array($params["material_id_array"] = to__array($params["material_id"]))){
                $p["material_id"] = $params["material_id_array"];
            }

            // ОТЧЕТ В РАЗРЕЗЕ НАСОСА
            if ($params['mode_report'] == 'P' and is__more_0($params['pump_id'])){
                $pump_materials = fn_uns__get_packing_list_by_pump($params['pump_id']);
                if (is__array($pump_materials)){
                    $p["material_id"] = array_keys($pump_materials);
                }
                $params['pump_materials'] = $pump_materials;
            }

            // Добавить информацию о весе
            if ($params['with_weight']){
                $p["with_accounting"] = true;
            }

            unset($p['sorting_schemas']);
            list($materials) = fn_uns__get_materials($p);
            if (is__array($materials)){

                // ОТОБРАЗИТЬ ПРИНАДЛЕЖНОСТЬ НАСОСОВ
                if ($params['accessory_pumps'] == true){
                    if (is__array($accessory_pumps = fn_uns__get_accessory_pumps('M',array_keys($materials)))){
                        foreach ($materials as $k_m=>$v_m){
                            $materials[$k_m]['accessory_pumps']         = $accessory_pumps[$k_m]['list_of_pumps'];
                            $materials[$k_m]['accessory_pump_series']   = $accessory_pumps[$k_m]['list_of_pump_series'];
                        }
                    }
                }

                foreach ($mcats as $mcats_k=>$mcats_v){
                    foreach ($materials as $materials_k=>$materials_v){
                        if ($mcats_k == $materials_v["mcat_id"]){
                            $mcat_items[$mcats_k]["group"]  = $mcats_v["mcat_name"];
                            $mcat_items[$mcats_k]["group_id"]  = $mcats_v["mcat_id"];
                            $mcat_items[$mcats_k]["items"][$materials_k]["id"]                      = $materials_v["material_id"];
                            $mcat_items[$mcats_k]["items"][$materials_k]["material_id"]             = $materials_v["material_id"];
                            $mcat_items[$mcats_k]["items"][$materials_k]["name"]                    = $materials_v["material_name"];
                            $mcat_items[$mcats_k]["items"][$materials_k]["material_comment"]        = $materials_v["material_comment"];
                            $mcat_items[$mcats_k]["items"][$materials_k]["material_comment_1"]      = $materials_v["material_comment_1"];
                            $mcat_items[$mcats_k]["items"][$materials_k]["name_accounting"]         = $materials_v["material_name_accounting"];
                            $mcat_items[$mcats_k]["items"][$materials_k]["no"]                      = $materials_v["material_no"];
                            $mcat_items[$mcats_k]["items"][$materials_k]["accessory_pumps"]         = $materials_v["accessory_pumps"];
                            $mcat_items[$mcats_k]["items"][$materials_k]["accessory_pump_series"]   = $materials_v["accessory_pump_series"];
                            $mcat_items[$mcats_k]["items"][$materials_k]["weight"]                  = fn_fvalue($materials_v["accounting_data"]["weight"],2);
                            $mcat_items[$mcats_k]["items"][$materials_k]["mcat_id"]                 = $mcats_v["mcat_id"];

                            if (is__array($data[$materials_k])){
                                $mcat_items[$mcats_k]["items"][$materials_k]["nach"]        = fn_fvalue($data[$materials_k]["no"], 2);
                                $mcat_items[$mcats_k]["items"][$materials_k]["current__in"] = fn_fvalue($data[$materials_k]["prih"], 2);;
                                $mcat_items[$mcats_k]["items"][$materials_k]["current__out"]= fn_fvalue($data[$materials_k]["rash"], 2);;
                                $mcat_items[$mcats_k]["items"][$materials_k]["konech"]      = fn_fvalue($data[$materials_k]["ko"], 2);;
                            }else{
                                $mcat_items[$mcats_k]["items"][$materials_k]["nach"]        = 0;
                                $mcat_items[$mcats_k]["items"][$materials_k]["current__in"] = 0;
                                $mcat_items[$mcats_k]["items"][$materials_k]["current__out"]= 0;
                                $mcat_items[$mcats_k]["items"][$materials_k]["konech"]      = 0;
                            }
                        }
                    }
                }
            }
            $data = $mcat_items;
        }
    }

    // ДЕТАЛИ
    if ($params["item_type"] == "D" and $params["add_item_info"]){
        $p = array();

        if (is__more_0($params["item_id"])){
            $params['dcat_id'] = db_get_field(UNS_DB_PREFIX . "SELECT dcat_id FROM ?:details WHERE detail_id = ?i", $params["item_id"]);
        }

        if (is__more_0($params['dcat_id'])){
            $p["dcat_id"] = $params['dcat_id'];
            $p["dcat_include_target"] = true;
        }

        if (is__array($params['dcat_id'])){
            $p["item_ids"] = $params['dcat_id'];
        }

        if (strlen($params['detail_name'])){
            $p["detail_name"] = $params['detail_name'];
        }

        if (strlen($params['detail_no'])){
            $p["detail_no"] = $params['detail_no'];
        }

        $p["only_active"] = true;
        list($dcats) = fn_uns__get_details_categories($p);
        if (is__array($dcats)){
            $dcat_items = array();
            $p["detail_status"] = "A";
            $p["dcat_id"] = array_keys($dcats);

            if ($params['item_id_array'] = to__array($params['item_id_array'])){
                $p["detail_id"] = $params["item_id_array"];
            }

            // Условие по выборке материала
            if ($params["with_material_info"]){
                $p["with_material_info"] = true;
            }

            // ОТЧЕТ В РАЗРЕЗЕ НАСОСА
            // .....
            list($details) = fn_uns__get_details($p);
            if (is__array($details)){
                 // ОТОБРАЗИТЬ ПРИНАДЛЕЖНОСТЬ НАСОСОВ
                if ($params['accessory_pumps'] == "Y"){
                    if (is__array($accessory_pumps = fn_uns__get_accessory_pumps('D', array_keys($details)))){
                        foreach ($details as $k_m=>$v_m){
                            $details[$k_m]['accessory_pumps']         = $accessory_pumps[$k_m]['list_of_pumps'];
                            $details[$k_m]['accessory_pump_series']   = $accessory_pumps[$k_m]['list_of_pump_series'];
                        }
                    }
                }
                foreach ($dcats as $dcats_k=>$dcats_v){
                    foreach ($details as $details_k=>$details_v){
                        if ($dcats_k == $details_v["dcat_id"]){
                            $dcat_items[$dcats_k]["group"]          = $dcats_v["dcat_name"];
                            $dcat_items[$dcats_k]["group_id"]       = $dcats_v["dcat_id"];
                            $dcat_items[$dcats_k]["group_comment"]  = $dcats_v["dcat_comment"];
                            $dcat_items[$dcats_k]["items"][$details_k]["id"]                      = $details_v["detail_id"];
                            $dcat_items[$dcats_k]["items"][$details_k]["detail_id"]               = $details_v["detail_id"];
                            $dcat_items[$dcats_k]["items"][$details_k]["accessory_view"]          = $details_v["accessory_view"];
                            $dcat_items[$dcats_k]["items"][$details_k]["name"]                    = $details_v["detail_name"];
                            $dcat_items[$dcats_k]["items"][$details_k]["name_accounting"]         = $details_v["detail_name_accounting"];
                            $dcat_items[$dcats_k]["items"][$details_k]["no"]                      = $details_v["detail_no"];
                            $dcat_items[$dcats_k]["items"][$details_k]["comment"]                 = $details_v["detail_comment"];
                            $dcat_items[$dcats_k]["items"][$details_k]["accessory_pumps"]         = $details_v["accessory_pumps"];
                            $dcat_items[$dcats_k]["items"][$details_k]["accessory_pump_series"]   = $details_v["accessory_pump_series"];
                            $dcat_items[$dcats_k]["items"][$details_k]["accessory_pump_manual"]   = $details_v["accessory_manual"];
                            $dcat_items[$dcats_k]["items"][$details_k]["dcat_id"]                 = $dcats_v["dcat_id"];
                            $dcat_items[$dcats_k]["items"][$details_k]["material_id"]             = $details_v["material_id"];
                            $dcat_items[$dcats_k]["items"][$details_k]["material_no"]             = $details_v["material_no"];
                            $dcat_items[$dcats_k]["items"][$details_k]["material_name"]           = $details_v["material_name"];
                            $dcat_items[$dcats_k]["items"][$details_k]["checked"]                 = $details_v["checked"];

                            if (is__array($data[$details_k])){
                                $dcat_items[$dcats_k]["items"][$details_k]["nach"]        = fn_fvalue($data[$details_k]["no"], 2);
                                $dcat_items[$dcats_k]["items"][$details_k]["current__in"] = fn_fvalue($data[$details_k]["prih"], 2);;
                                $dcat_items[$dcats_k]["items"][$details_k]["current__out"]= fn_fvalue($data[$details_k]["rash"], 2);;
                                $dcat_items[$dcats_k]["items"][$details_k]["konech"]      = fn_fvalue($data[$details_k]["ko"], 2);;
                            }else{
                                $dcat_items[$dcats_k]["items"][$details_k]["nach"]        = 0;
                                $dcat_items[$dcats_k]["items"][$details_k]["current__in"] = 0;
                                $dcat_items[$dcats_k]["items"][$details_k]["current__out"]= 0;
                                $dcat_items[$dcats_k]["items"][$details_k]["konech"]      = 0;
                            }
                        }
                    }
                }
            }
            $data = $dcat_items;
        }
    }

    // НАСОСЫ
    if (in_array($params["item_type"], array("P", "PF", "PA")) and $params["add_item_info"]){

        $p = array();

//        if (is__more_0($params['dcat_id'])){
//            $p["dcat_id"] = $params['dcat_id'];
//            $p["dcat_include_target"] = true;
//        }
//
//        if (strlen($params['detail_name'])){
//            $p["detail_name"] = $params['detail_name'];
//        }
//
//        if (strlen($params['detail_no'])){
//            $p["detail_no"] = $params['detail_no'];
//        }
        $dcat_items = array();
        list($pump_types) = fn_uns__get_pump_types($p);
        list($pumps) = fn_uns__get_pumps();
        if (is__array($pumps)){
            foreach ($pump_types as $pt_k=>$pt_v){
                foreach ($pumps as $p_k=>$p_v){
                    if ($pt_k == $p_v["pt_id"]){
                        $dcat_items[$pt_k]["group_id"]              = $pt_k;
                        $dcat_items[$pt_k]["group"]                 = $pt_v["pt_name"];
                        $dcat_items[$pt_k]["items"][$p_k]["id"]     = $p_v["p_id"];
                        $dcat_items[$pt_k]["items"][$p_k]["name"]   = $p_v["p_name"];

                        if (is__array($data[$p_k])){
                            $dcat_items[$pt_k]["items"][$p_k]["nach"]        = fn_fvalue($data[$p_k]["no"], 2);
                            $dcat_items[$pt_k]["items"][$p_k]["current__in"] = fn_fvalue($data[$p_k]["prih"], 2);;
                            $dcat_items[$pt_k]["items"][$p_k]["current__out"]= fn_fvalue($data[$p_k]["rash"], 2);;
                            $dcat_items[$pt_k]["items"][$p_k]["konech"]      = fn_fvalue($data[$p_k]["ko"], 2);;
                        }else{
                            $dcat_items[$pt_k]["items"][$p_k]["nach"]        = 0;
                            $dcat_items[$pt_k]["items"][$p_k]["current__in"] = 0;
                            $dcat_items[$pt_k]["items"][$p_k]["current__out"]= 0;
                            $dcat_items[$pt_k]["items"][$p_k]["konech"]      = 0;
                        }
                    }
                }
            }
            $data = $dcat_items;
        }
    }

    return array($data, $params, ($time)?(microtime_float()-$start_time):0);
}

// Принадлежность насосов
function fn_uns__get_accessory_pumps ($item_type, $items){
    if (!in_array($item_type, array('M', 'D')) or !($items = to__array($items))) return false;
    else{
        $result = array();
        $item_name = "material_id";
        if ($item_type == "D") $item_name = "detail_id";
        $sql = UNS_DB_PREFIX . "
            SELECT
              uns_detail__and__items.{$item_name},
              GROUP_CONCAT(distinct uns_pumps.p_name
                           ORDER BY uns_pump_types.pt_position, uns_pump_types.pt_name, uns_pump_series.ps_position, uns_pump_series.ps_name, uns_pumps.p_position, uns_pumps.p_name
                           SEPARATOR '; ') as list_of_pumps,
              GROUP_CONCAT(distinct uns_pump_series.ps_name
                          ORDER BY uns_pump_types.pt_position, uns_pump_types.pt_name, uns_pump_series.ps_position, uns_pump_series.ps_name
                          SEPARATOR '; ') as list_of_pump_series
            FROM
                uns_pumps_packing_list
                LEFT JOIN uns_pumps
                  ON (    uns_pumps.ps_id = uns_pumps_packing_list.ppl_item_id)
                LEFT JOIN uns_pump_series
                  ON (    uns_pump_series.ps_id = uns_pumps.ps_id)
                LEFT JOIN uns_pump_types
                  ON (    uns_pump_types.pt_id = uns_pump_series.pt_id)
                LEFT JOIN uns_pumps_packing_list_replacement
                  ON (    uns_pumps_packing_list_replacement.p_id   = uns_pumps.p_id
                      and uns_pumps_packing_list_replacement.ppl_id = uns_pumps_packing_list.ppl_id
                      and uns_pumps_packing_list_replacement.item_type = 'D')
                LEFT JOIN uns_detail__and__items
                  ON (    uns_detail__and__items.detail_id = uns_pumps_packing_list.item_id)
            WHERE
                  uns_pumps_packing_list.ppl_status             = 'A'
              AND uns_pumps_packing_list.ppl_item_part          = 'P'
              AND uns_pumps_packing_list.ppl_item_type          = 'S'
              AND uns_pumps_packing_list.item_type              = 'D'
              AND uns_detail__and__items.{$item_name}            in (".implode(", ", $items).")
            GROUP BY uns_detail__and__items.{$item_name}
        ";
        $data = db_get_hash_array($sql, $item_name);
        if (!is__array($data)) return false;
        return $data;
    }
}

// ПОЛУЧИТЬ ДАТУ ПОСЛЕДНЕГО ДВИЖЕНИЯ
function fn_uns__get_info_of_the_last_movement($params){
    if (!is__more_0($params['o_id'])) return false;
    $data = array();
    switch ($params['o_id']){
        case 8: // склад литья
           $sql = UNS_DB_PREFIX . "
               SELECT
                 uns__acc_documents.document_id,
                 uns__acc_documents.date
               FROM
                 uns__acc_document_types
                 INNER JOIN uns__acc_documents ON (uns__acc_document_types.dt_id = uns__acc_documents.type)
               WHERE
                 uns__acc_document_types.type IN ('VLC','MCP','AIO') AND
                 uns__acc_documents.status = 'A' AND
                 uns__acc_documents.object_from IN (0,7,8) AND
                 uns__acc_documents.object_to IN (8,24)
               ORDER BY
                 uns__acc_documents.date DESC
               LIMIT 1
           ";
            list($data) = db_get_array($sql);
        break;
    }
    return $data;
}


// Получить список движений по выбранному item-у
function fn_uns__get_motions($params){
    $cond_item_type = db_quote(" AND uns__acc_document_items.item_type in (?a) ", to__array($params['item_type']));
    $sql = UNS_DB_PREFIX . "
            SELECT
              uns__acc_documents.document_id,
              uns__acc_documents.date,
              uns__acc_documents.comment,
              uns__acc_documents.date_cast,
              uns__acc_documents.package_id,
              uns__acc_documents.package_type,

              uns__acc_document_items.item_id,
              uns__acc_document_items.item_type,
              uns__acc_document_items.quantity,
              uns__acc_document_types.type,
              uns__acc_document_types.dt_id,
              uns__acc_document_items.change_type,

              uns__acc_motions.motion_type
            FROM uns__acc_document_items
              , uns__acc_documents
              , uns__acc_motions
              , uns__acc_document_types
            WHERE 1
                  AND uns__acc_document_items.item_id   =  ".$params['item_id']."
                  {$cond_item_type}
                  AND uns__acc_document_items.typesize  = '".$params['typesize']."'

                  # UNS__ACC_DOCUMENTS
                  AND uns__acc_documents.document_id = uns__acc_document_items.document_id
                  AND uns__acc_documents.status = 'A'

                  # UNS__ACC_DOCUMENT_TYPES
                  AND uns__acc_document_types.dt_id = uns__acc_documents.type

                  # UNS__ACC_MOTIONS
                  AND uns__acc_motions.document_id = uns__acc_document_items.document_id
                  AND uns__acc_motions.motion_type IN ('I', 'O')
                  AND uns__acc_motions.object_id IN (".implode(',', to__array($params['o_id'])).")

                  # ALL
                  AND uns__acc_documents.date BETWEEN {$params['time_from']} AND {$params['time_to']}
                  AND (
                        (
                            uns__acc_document_types.type = 'AIO'
                            AND uns__acc_document_items.change_type = 'POZ'
                            AND uns__acc_motions.motion_type = 'I')
                        OR (
                            uns__acc_document_types.type = 'AIO'
                            AND uns__acc_document_items.change_type = 'NEG'
                            AND uns__acc_motions.motion_type = 'O')
                        OR (
                            uns__acc_document_types.type IN ('VLC', 'PVP', 'MCP', 'RO', 'AS_VLC', 'VN'))
                  )
            ORDER BY uns__acc_documents.date ASC, uns__acc_documents.document_id ASC
    ";
    $data = db_get_array($sql, "document_id");

    // Добавить привязку к СЛ
    if (is__array($data)){
        $keys = array();
        foreach ($data as $k=>$v){
            if ($v["package_type"] == UNS_PACKAGE_TYPE__SL){
                $data[$k]["sheet_no"] = db_get_field(UNS_DB_PREFIX . "SELECT uns__acc_sheets.no FROM uns__acc_sheets WHERE sheet_id=?i", $v["package_id"]);
            }
        }
    }

    return (!is__array($data))?false:$data;
}



//******************************************************************************
// Запрос баланса по МЕХ ЦЕХУ, скл. компл., сб.уч.
function fn_uns__get_balance_mc_sk_su($params, $mc=true, $sk=true, $su=false){
    $res = array();
    $p = array(
        "plain"                     => true,
        "all"                       => true,
        "item_type"                 => "D",
        "check_dcat_id"             => true,
        "add_item_info"             => true,
        "with_material_info"        => true,
        "view_all_position"         => "Y",
        "total_balance_of_details"  => "Y",
        "with_weight"               => true,
        "prihod_doc_types"          => array("'AIO'", "'MCP'", "'VCP'", "'PVP'",    "'VCP_COMPLETE'"),
        "rashod_doc_types"          => array("'AIO'", "'MCP'", "'VCP'", "'BRAK'",   "'VCP_COMPLETE'", "'VD'", "'VN'"),
    );

    $p = array_merge($p, $params);
//    if ($p["check_dcat_id"]){
//        if (!is__more_0($p["dcat_id"]) and !is__array($p["dcat_id"])) return array(array(), $p);
//    }
    // ЗАПРОСИТЬ БАЛАНС МЕХ. ЦЕХА
    if ($mc == true){
        $mc_processing = $mc_complete = array();
        foreach (array(10,14) as $o_id){
            $p["o_id"]    = $o_id;
            //======================================================================
            // условия для PROCESSING
            //======================================================================
            $p["cond_prih"]    = " AND (
                                         (
                                                uns__acc_document_types.type        = 'PVP'
                                            and uns__acc_document_items.motion_type like '%I%'
                                            and uns__acc_document_items.processing  = 'P'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'MCP'
                                            and uns__acc_document_items.motion_type like '%I%'
                                            and uns__acc_document_items.processing  = 'P'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'VCP'
                                            and uns__acc_document_items.motion_type like '%I%'
                                            and uns__acc_document_items.processing  = 'P'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'AIO'
                                            and uns__acc_document_items.change_type = 'POZ'
                                            and uns__acc_document_items.motion_type like '%I%'
                                            and uns__acc_document_items.processing  = 'P'
                                         )
                                    )
                                    ";
            $p["cond_rash"]    = " AND (
                                         (
                                                uns__acc_document_types.type        = 'VCP'
                                            and uns__acc_document_items.motion_type like '%O%'
                                            and uns__acc_document_items.processing  = 'P'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'BRAK'
                                            and uns__acc_document_items.motion_type like '%O%'
                                            and uns__acc_document_items.processing  = 'P'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'VCP_COMPLETE'
                                            and uns__acc_document_items.motion_type like '%O%'
                                            and uns__acc_document_items.processing  = 'P'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'MCP'
                                            and uns__acc_document_items.motion_type like '%O%'
                                            and uns__acc_document_items.processing  = 'P'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'AIO'
                                            and uns__acc_document_items.change_type = 'NEG'
                                            and uns__acc_document_items.motion_type like '%O%'
                                            and uns__acc_document_items.processing  = 'P'
                                         )
                                    )";
            list($mc_processing[$o_id]) = fn_uns__get_balance($p);
            if (is__array($mc_processing[$o_id])){
                foreach ($mc_processing[$o_id] as $k_gr=>$v_gr){
                    foreach ($v_gr["items"] as $k_d=>$v_d){
                        $mc_processing[$o_id][$k_gr]["items"][$k_d]["processing"]       = $mc_processing[$o_id][$k_gr]["items"][$k_d]["konech"];
                        $mc_processing[$o_id][$k_gr]["items"][$k_d]["processing_nach"]  = $mc_processing[$o_id][$k_gr]["items"][$k_d]["nach"];
                        $mc_processing[$o_id][$k_gr]["items"][$k_d]["processing_konech"]= $mc_processing[$o_id][$k_gr]["items"][$k_d]["konech"];
                    }
                }
            }

            //======================================================================
            // условия для COMPLETE
            //======================================================================
            $p["cond_prih"]    = " AND (
                                         (
                                                uns__acc_document_types.type        = 'PVP'
                                            and uns__acc_document_items.motion_type like '%I%'
                                            and uns__acc_document_items.processing  = 'C'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'VCP'
                                            and uns__acc_document_items.motion_type like '%I%'
                                            and uns__acc_document_items.processing  = 'C'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'MCP'
                                            and uns__acc_document_items.motion_type like '%I%'
                                            and uns__acc_document_items.processing  = 'C'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'VCP_COMPLETE'
                                            and uns__acc_document_items.motion_type like '%I%'
                                            and uns__acc_document_items.processing  = 'C'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'AIO'
                                            and uns__acc_document_items.change_type = 'POZ'
                                            and uns__acc_document_items.motion_type like '%I%'
                                            and uns__acc_document_items.processing  = 'C'
                                         )
                                    )
                                    ";
            $p["cond_rash"]    = " AND (
                                         (
                                                uns__acc_document_types.type        = 'VCP'
                                            and uns__acc_document_items.motion_type like '%O%'
                                            and uns__acc_document_items.processing  = 'C'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'BRAK'
                                            and uns__acc_document_items.motion_type like '%O%'
                                            and uns__acc_document_items.processing  = 'C'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'MCP'
                                            and uns__acc_document_items.motion_type like '%O%'
                                            and uns__acc_document_items.processing  = 'C'
                                         )
                                         OR
                                         (
                                                uns__acc_document_types.type        = 'AIO'
                                            and uns__acc_document_items.change_type = 'NEG'
                                            and uns__acc_document_items.motion_type like '%O%'
                                            and uns__acc_document_items.processing  = 'C'
                                         )
                                    )";
            list($mc_complete[$o_id]) = fn_uns__get_balance($p);
            if (is__array($mc_complete[$o_id])){
                foreach ($mc_complete[$o_id] as $k_gr=>$v_gr){
                    foreach ($v_gr["items"] as $k_d=>$v_d){
                        $mc_processing[$o_id][$k_gr]["items"][$k_d]["complete"]         = $mc_complete[$o_id][$k_gr]["items"][$k_d]["konech"];
                        $mc_processing[$o_id][$k_gr]["items"][$k_d]["complete_nach"]    = $mc_complete[$o_id][$k_gr]["items"][$k_d]["nach"];
                        $mc_processing[$o_id][$k_gr]["items"][$k_d]["complete_konech"]  = $mc_complete[$o_id][$k_gr]["items"][$k_d]["konech"];

                    }
                }
            }
            $res[$o_id] = $mc_processing[$o_id];
        }
    }

    //--------------------------------------------------------------------------
    // ЗАПРОСИТЬ БАЛАНС СКЛАД КОМПЛЕКТУЮЩИХ
    //--------------------------------------------------------------------------
    if ($sk == true){
        $sk_balance = array();
        $o_id = 17;
        $p["o_id"]   = $o_id;
        $p["cond_prih"]    = " AND (
                                     (
                                            uns__acc_document_types.type        = 'MCP'
                                        and uns__acc_document_items.motion_type like '%I%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'AIO'
                                        and uns__acc_document_items.change_type = 'POZ'
                                        and uns__acc_document_items.motion_type like '%I%'
                                     )
                                )
                                ";
        $p["cond_rash"]    = " AND (
                                     (
                                            uns__acc_document_types.type        = 'MCP'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'BRAK'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'AIO'
                                        and uns__acc_document_items.change_type = 'NEG'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                )";
        list($res[$o_id]) = fn_uns__get_balance($p);
    }

    //--------------------------------------------------------------------------
    // ЗАПРОСИТЬ БАЛАНС СБОРОЧНЫЙ УЧАСТОК
    //--------------------------------------------------------------------------
    if ($su == true){
        $su_balance = array();
        $o_id       = 18;
        if (is__array($params["su"])){
            $p = array_merge($p, $params["su"]);
        }
        $p["o_id"]  = $o_id;
        $p["cond_prih"]    = " AND (
                                     (
                                            uns__acc_document_types.type        = 'MCP'
                                        and uns__acc_document_items.motion_type like '%I%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'AIO'
                                        and uns__acc_document_items.change_type = 'POZ'
                                        and uns__acc_document_items.motion_type like '%I%'
                                     )
                                )
                                ";
        $p["cond_rash"]    = " AND (
                                     (
                                            uns__acc_document_types.type        = 'VD'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'VN'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'BRAK'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'AIO'
                                        and uns__acc_document_items.change_type = 'NEG'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                )";
        list($res[$o_id]) = fn_uns__get_balance($p);
    }

    return array($res, $p);
}


/**
 * Баланс СГП
 * @param $params
 * @param bool $p - запрос баланса по насосам без рам
 * @param bool $pf - запрос баланса по насосам на рамах
 * @param bool $pa - запрос баланса по насосным агрегатам
 * @return array
 */
function fn_uns__get_balance_sgp($params, $pump=false, $pump_frame=false, $pump_agregat=false, $details=false){
    $res = array();
    $p = array(
        "plain"                     => true,
        "all"                       => true,
        "check_dcat_id"             => true,
        "o_id"                      => 19,
        "add_item_info"             => true,
        "view_all_position"         => "Y",
        "with_material_info"        => true,
        "total_balance_of_details"  => "Y",
        "accessory_pumps"           => "Y",
        "with_weight"               => true,
        "prihod_doc_types"          => array("'AIO'", "'VN'", "'PN'",),
        "rashod_doc_types"          => array("'AIO'", "'RO'", "'PN'",),
    );
    $p = array_merge($p, $params);

    $p["cond_prih"]    = " AND (
                                 (
                                        uns__acc_document_types.type        = 'VN'
                                    and uns__acc_document_items.motion_type like '%I%'
                                 )
                                 OR
                                 (
                                        uns__acc_document_types.type        = 'PN'
                                    and uns__acc_document_items.motion_type like '%I%'
                                 )
                                 OR
                                 (
                                        uns__acc_document_types.type        = 'AIO'
                                    and uns__acc_document_items.change_type = 'POZ'
                                    and uns__acc_document_items.motion_type like '%I%'
                                 )
                            )
                            ";
    $p["cond_rash"]    = " AND (
                                 (
                                        uns__acc_document_types.type        = 'RO'
                                    and uns__acc_document_items.motion_type like '%O%'
                                 )
                                 OR
                                 (
                                        uns__acc_document_types.type        = 'PN'
                                    and uns__acc_document_items.motion_type like '%O%'
                                 )
                                 OR
                                 (
                                        uns__acc_document_types.type        = 'AIO'
                                    and uns__acc_document_items.change_type = 'NEG'
                                    and uns__acc_document_items.motion_type like '%O%'
                                 )
                            )";


    // ЗАПРОСИТЬ БАЛАНС ПО НАСОСАМ
    if ($pump == true){
        $p["item_type"] = "P";
        list($res[$p["item_type"]]) = fn_uns__get_balance($p);
    }

    // ЗАПРОСИТЬ БАЛАНС ПО НАСОСАМ НА РАМЕ
    if ($pump_frame == true){
        $p["item_type"] = "PF";
        list($res[$p["item_type"]]) = fn_uns__get_balance($p);
    }

    // ЗАПРОСИТЬ БАЛАНС ПО НАСОСНЫМ АГРЕГАТАМ
    if ($pump_agregat == true){
        $p["item_type"] = "PA";
        list($res[$p["item_type"]]) = fn_uns__get_balance($p);
    }


    // ЗАПРОС БАЛАНСА СГП ПО ДЕТАЛЯМ
    if ($details == true){
        $p["prihod_doc_types"] = array("'AIO'", "'MCP'");
        $p["rashod_doc_types"] = array("'AIO'", "'MCP'", "'BRAK'", "'RO'");

        $p["cond_prih"]    = " AND (
                                     (
                                            uns__acc_document_types.type        = 'MCP'
                                        and uns__acc_document_items.motion_type like '%I%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'AIO'
                                        and uns__acc_document_items.change_type = 'POZ'
                                        and uns__acc_document_items.motion_type like '%I%'
                                     )
                                )
                                ";
        $p["cond_rash"]    = " AND (
                                     (
                                            uns__acc_document_types.type        = 'RO'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'MCP'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'BRAK'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                     OR
                                     (
                                            uns__acc_document_types.type        = 'AIO'
                                        and uns__acc_document_items.change_type = 'NEG'
                                        and uns__acc_document_items.motion_type like '%O%'
                                     )
                                )";
        $p["item_type"] = "D";
        list($res[$p["item_type"]]) = fn_uns__get_balance($p);
    }
    return array($res, $p);
}