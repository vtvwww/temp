<?php
/* ФУНКЦИИ ДЛЯ РАБОТЫ С КОМПЛЕКТАЦИЯМИ
 * */

/**
 * ПРОВЕРКА ТИПА ЕЛЕМЕНТА ИЗ ДОСПУСТИМЫХ
 * @param $item_type
 * @return bool
 */
function fn_uns_check_packing_list_types ($item_type){
    if (false === strpos(UNS_PACKING_TYPES, "|".$item_type."|")){
        return false;
    }else{
        return true;
    }
}

/**
 * ПРОВЕРКА ТИПА ЕЛЕМЕНТА ИЗ ДОСПУСТИМЫХ
 * @param $item_part
 * @return bool
 */
function fn_uns_check_packing_list_parts ($item_part){
    if (false === strpos(UNS_PACKING_PARTS, "|".$item_part."|")){
        return false;
    }else{
        return true;
    }
}


/**
 * ПРОВЕРКА ТИПА ЕЛЕМЕНТА ИЗ ДОСПУСТИМЫХ
 * @param $item_part
 * @return bool
 */
function fn_uns_check_packing_list_replacements ($item_part="0"){
    if (false === strpos(UNS_PACKING_REPLACEMENTS, "|".$item_part."|")){
        return false;
    }else{
        return true;
    }
}



/**
 * ПОЛУЧИТЬ КОМПЛЕКТАЦИЮ СБОРОЧНОЙ ЕДИНИЦЫ - ЭТО МОЖЕТ БЫТЬ КАК ПО "СЕРИИ НАСОСОВ", ТАК И ПО КОНКРЕТНОМУ "НАСОСУ"
 * @param array $params
 * @return array
 */
function fn_uns__get_packing_list ($params = array()){
    $default_params = array(
        'ps_id' => 0,
        'p_id' => 0,
        'limit' => 0,
        'page' => 1,
        'sort_by' => '',
        'sort_order' => 'desc',
        'sorting_schemas' => 'view',
    );

    $params = array_merge($default_params, $params);

    $m_table = "?:pumps_packing_list";

    $fields = array(
        "$m_table.ppl_id",
        "$m_table.ppl_item_type",
        "$m_table.ppl_item_id",
        "$m_table.ppl_item_part",
        "$m_table.ppl_status",
        "$m_table.item_type",
        "$m_table.item_id",
        "$m_table.quantity",
        "$m_table.u_id",
        "$m_table.typesize",
    );

    $sorting_schemas = array(
        'view' => array(
            "$m_table.ppl_status" => 'asc',
            "$m_table.item_type"  => 'asc',
        )
    );

    //*********************
    $total = 0;
    $condition = $join = $limit = $sorting = '';



    // Это ДЛЯ ЕДИНИЧНОГО НАСОСА
    if ($params['p_id_array'] = to__array($params['p_id'])){
        $condition .= db_quote(" AND $m_table.ppl_item_id in (?n) ", $params['p_id_array']);
        $condition .= db_quote(" AND $m_table.ppl_item_type = ?s ", UNS_PACKING_TYPE__ITEM);
    }

    // Выборка комплектации для СЕРИИ насосов
    if ($params['ps_id_array'] = to__array($params['ps_id'])){
        $condition .= db_quote(" AND $m_table.ppl_item_id in (?n) ", $params['ps_id_array']);
        $condition .= db_quote(" AND $m_table.ppl_item_type = ?s ", UNS_PACKING_TYPE__SERIES);
    }

    if (is__array($sorting_schemas[$params['sorting_schemas']])){
        $s = array();
        foreach ($sorting_schemas[$params['sorting_schemas']] as $k=>$v){
            $s[] = " {$k} {$v} ";
        }
        $sorting = " ORDER BY " . implode(', ', $s);
    }

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "ppl_id");

    if ($params['categories_info']){
        // utodo Не очень корректная реализация
        // Информация о категории к которой принадлежит позиция
        if (is__array($data)){
            foreach ($data as $k_d => $v_d) {
                if ($v_d['item_type'] == "D"){
                    list($item) = fn_uns__get_details(array('detail_id' => $v_d['item_id']));
                }else{
                    list($item) = fn_uns__get_materials(array('material_id' => $v_d['item_id']));
                }
                $data[$k_d]['item_info'] = $item[$v_d['item_id']];
            }
        }
    }

    if ($params['with_other_items_current_categories']){
        // Получить список деталей/материалов по каждой выбранной категории
        // 1. По ЦЕЛЕВОЙ категории получить информацию на детали по упрощенной схеме
        // 2. Для ЦЕЛЕВОЙ детали получить полную информацию
         if (is__array($data)){
            foreach ($data as $k_d => $v_d) {
                if ($v_d['item_type'] == "D"){
                    // Запрос ВСЕХ деталей по упрощенной схеме
                    $p = array(
                        'dcat_id' => $v_d['item_info']['dcat_id'],
                        'with_accounting' => true,
                        'format_name' => true
                    );
                    list ($details) = fn_uns__get_details($p);

                    // Запрос ЦЕЛЕВОЙ детали с полной информацией
                    $detail_id = $v_d['item_info']['detail_id'];
                    $p = array(
                        'detail_id' => $detail_id,
                        'with_accounting' => true,
                        'with_materials' => true,
                        'format_name' => true
                    );
                    list ($target_details)  = fn_uns__get_details($p);

                    $details[$detail_id] = $target_details[$detail_id];

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
        }
    }

    if ($params['with_units']){
        if (is__array($data)){
            foreach ($data as $k_d => $v_d) {
                list($units) = fn_uns__get_units(array('u_id'=>$v_d['u_id']));
                $data[$k_d]['units'] = $units;
            }
        }
    }

    if (is__array($data)){
        $tmp = array();
        foreach ($data as $k_d => $v_d) {
            if     ($v_d['ppl_item_part'] == UNS_PACKING_PART__PUMP)  $tmp[UNS_PACKING_PART__PUMP][$k_d]  = $v_d;
            elseif ($v_d['ppl_item_part'] == UNS_PACKING_PART__FRAME) $tmp[UNS_PACKING_PART__FRAME][$k_d] = $v_d;
            elseif ($v_d['ppl_item_part'] == UNS_PACKING_PART__MOTOR) $tmp[UNS_PACKING_PART__MOTOR][$k_d] = $v_d;
        }

        $data = $tmp;

        if (!isset($data[UNS_PACKING_PART__PUMP])){
            $data[UNS_PACKING_PART__PUMP] = array();
        }
        if (!isset($data[UNS_PACKING_PART__FRAME])){
            $data[UNS_PACKING_PART__FRAME] = array();
        }
        if (!isset($data[UNS_PACKING_PART__MOTOR])){
            $data[UNS_PACKING_PART__MOTOR] = array();
        }
    }

    return array($data, $params, $total);
}



/**
 * ПОЛУЧИТЬ КОМПЛЕКТАЦИЮ ЗАМЕЩЕНИЯ
 * @param $p_id
 * @param array $packing_list_series
 * @internal param array $params
 * @return array
 */
function fn_uns__get_packing_list_replacement ($params=array(), $packing_list_series=array()){
    if (is__array($packing_list_series)){
        foreach ($packing_list_series as $k=>$part){
            if (is__array($part)){
                $ppl_ids = array_keys($part);

                $data = db_get_array(UNS_DB_PREFIX . "SELECT
                                            pplr_id, p_id, ppl_id, pplr_type,
                                            item_type, item_id, quantity, u_id, typesize
                                          FROM ?:pumps_packing_list_replacement
                                          WHERE p_id = ?i AND ppl_id in (?n)", $params['p_id'], $ppl_ids);

                if (is__array($data)){
                    foreach ($data as $k_d => $v_d) {
                        if ($v_d['pplr_type'] == UNS_PACKING_REPLACEMENT__REPLACE){

                            // 1. ИНФОРМАЦИЯ О КАТЕГОРИИ К КОТОРОЙ ПРИНАДЛЕЖИТ ПОЗИЦИЯ
                            if ($v_d['item_type'] == "D"){
                                list($item) = fn_uns__get_details(array('detail_id' => $v_d['item_id']));
                            }else{
                                list($item) = fn_uns__get_materials(array('material_id' => $v_d['item_id']));
                            }
                            $data[$k_d]['item_info'] = $item[$v_d['item_id']];


                            if ($params['with_items']){
                                // 2. ПОЛУЧИТЬ СПИСОК ДЕТАЛЕЙ/МАТЕРИАЛОВ ПО КАЖДОЙ ВЫБРАННОЙ КАТЕГОРИИ
                                if ($v_d['item_type'] == "D"){
                                    $p = array(
                                        'dcat_id' => $data[$k_d]['item_info']['dcat_id'],
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

                            if ($params['with_units']){
                                // 3. C ЕДИНИЦАМИ ИЗМЕРЕНИЯМИ
                                list($units) = fn_uns__get_units(array('u_id'=>$v_d['u_id']));
                                $data[$k_d]['units'] = $units;
                            }
                        }
                    }

                    foreach ($data as $d){
                        $packing_list_series[$k][$d['ppl_id']]['replacement'] = $d;
                    }
                }
            }
        }
    }
    return $packing_list_series;
}


/**
 * ОБНОВИТЬ КОМПЛЕКТАЦИЮ СБОРОЧНОЙ ЕДИНИЦЫ
 * @param $id
 * @param $type
 * @param $data
 * @return bool
 */
function fn_uns__upd_packing_list ($id, $type, $data){
    $data = trim__data($data);
    if (!is__more_0($id) || !fn_uns_check_packing_list_types($type) || !is__array($data)) return false;
    $pplr_ids = array();
    $m_table_ppl = "?:pumps_packing_list";
    $m_table_pplr = "?:pumps_packing_list_replacement";
    foreach ($data as $k_d=>$v_d){
        if (fn_uns_check_packing_list_parts($k_d)){
            $ppl_ids = array();
            foreach ($v_d as $i){
                if (is__more_0($i['ppl_id']) and $i['ppl_item_type'] == UNS_PACKING_TYPE__SERIES
                    and
                    (is__more_0($i['replacement']['pplr_id']) or fn_uns_check_packing_list_replacements($i['replacement']['pplr_type']))
                ){
                    // ************************************************************************************************
                    // ОБРАБОТКА ЗАМЕЩЕНИЯ ПРИ ФОРМИРОВАНИИ КОМПЛЕКТАЦИИ "ЕДИНИЧНОГО НАСОСА" НА БАЗЕ КОМПЛЕКТАЦИИ СЕРИИ
                    // ************************************************************************************************
                    $pplr_id   = $i['replacement']['pplr_id'];
                    $pplr_type = $i['replacement']['pplr_type'];
                    $d = array(
                        'p_id'=>$id,
                        'ppl_id' => $i['ppl_id'],
                        'pplr_type' => $pplr_type,
                        'item_type' => null,
                        'item_id'   => 0,
                        'quantity'  => 0,
                        'u_id'      => 0,
                    );

                    if (is__more_0($pplr_id)){
                        // Было DELETE или REPLACE, а стало DELETE или REPLACE или ИСПОЛЬЗОВАТЬ
                        if (fn_uns_check_packing_list_replacements($pplr_type)){
                            if ($pplr_type == UNS_PACKING_REPLACEMENT__REPLACE){
                                // Обновить ЗАМЕЩЕНИЕ
                                if (fn_uns_check_item_types($i['replacement']['item_type']) && is__more_0($i['replacement']['item_cat_id'], $i['replacement']['item_id'], $i['replacement']['quantity'], $i['replacement']['u_id'])){
                                    $d['item_type'] = $i['replacement']['item_type'];
                                    $d['item_id']   = $i['replacement']['item_id'];
                                    $d['quantity']  = $i['replacement']['quantity'];
                                    $d['u_id']      = $i['replacement']['u_id'];
                                    $d['typesize']  = $i['replacement']['typesize'];
                                }else{
                                    $d['pplr_type'] =  UNS_PACKING_REPLACEMENT__DELETE;
                                }
                            }
                            db_query(UNS_DB_PREFIX . "UPDATE $m_table_pplr SET ?u WHERE p_id = ?i AND pplr_id = ?i", $d, $id, $pplr_id);
                            $pplr_ids[] = $pplr_id;
                        }
                    }else{
                        // Было ИСПОЛЬЗОВАТЬ, а стало DELETE или REPLACE
                        if($pplr_type == UNS_PACKING_REPLACEMENT__REPLACE){
                            // Добавить ЗАМЕЩЕНИЕ
                            if (fn_uns_check_item_types($i['replacement']['item_type']) && is__more_0($i['replacement']['item_cat_id'], $i['replacement']['item_id'], $i['replacement']['quantity'], $i['replacement']['u_id'])){
                                $d['item_type'] = $i['replacement']['item_type'];
                                $d['item_id']   = $i['replacement']['item_id'];
                                $d['quantity']  = $i['replacement']['quantity'];
                                $d['u_id']      = $i['replacement']['u_id'];
                                $d['typesize']  = $i['replacement']['typesize'];
                            }else{
                                $d['pplr_type'] =  UNS_PACKING_REPLACEMENT__DELETE;
                            }
                        }
                        $pplr_ids[] = db_query(UNS_DB_PREFIX . "INSERT INTO $m_table_pplr ?e", $d);
                    }
                }else{
                    // ОБРАБОТКА ПОЗИЦИЙ ПРИ ФОРМИРОВАНИИ КОМПЛЕКТАЦИИ ДЛЯ СЕРИИ
                    // ОБРАБОТКА ПОЗИЦИЙ ПРИ ФОРМИРОВАНИИ ИНДИВИДУАЛЬНОЙ КОМПЛЕКТАЦИИ ДЛЯ ЕДИНИЧНОГО НАСОСА
                    if (fn_uns_check_item_types($i['item_type']) && is__more_0($i['item_cat_id'], $i['item_id'], $i['quantity'], $i['u_id'])){
                        $d = array(
                            'ppl_item_type'         => $type,
                            'ppl_item_id'           => $id,
                            'ppl_item_part'         => $k_d,
                            'ppl_status'            => "A",
                            'item_type'             => $i['item_type'],
                            'item_id'               => $i['item_id'],
                            'quantity'              => $i['quantity'],
                            'u_id'                  => $i['u_id'],
                            'typesize'              => $i['typesize'],
                        );
                        if (is__more_0($i['ppl_id'])){
                            db_query(UNS_DB_PREFIX . "UPDATE $m_table_ppl SET ?u WHERE ppl_id = ?i", $d, $i['ppl_id']);
                            $ppl_ids[] = $i['ppl_id'];
                        }elseif ($i['ppl_id'] == 0){
                            $ppl_ids[] = db_query(UNS_DB_PREFIX . "INSERT INTO $m_table_ppl ?e", $d);
                        }
                    }
                }
            }

            fn_uns__del_packing_list_items($ppl_ids, $type, $id, $k_d, $f);
        }
    }

    // Удаление лишнее из таблицы ЗАМЕЩЕНИЯ
    if ($type == UNS_PACKING_TYPE__ITEM){
        if (is__array($pplr_ids)){
            db_query(UNS_DB_PREFIX . "DELETE FROM $m_table_pplr WHERE p_id = ?i AND pplr_id not in (?n) ", $id, $pplr_ids);
        }else{
            db_query(UNS_DB_PREFIX . "DELETE FROM $m_table_pplr WHERE p_id = ?i ", $id);
        }
    }

    return true;
}

function fn_uns__del_packing_list_items($ids, $item_type, $item_id, $item_part) {
    $not = "";
    if($item_type == UNS_PACKING_TYPE__SERIES){
        // В данном случае (именно с ПОЗИЯМИ КОМПЛЕКТАЦИИ СЕРИИ или ИНДИВИДУАЛЬНОЙ КОМПЛЕКТАЦИИ НАСОСА) - $ids содержит id, которые нельзя удалять!
        // Ну, а если он пустой, тогда удалить все варианты этого параметра.
        $cond = "";
        if(is__array($ids)){
            $cond = db_quote(" AND ppl_id not in (?n)", $ids);
        }

        $ppl_ids_for_delete = db_get_fields(UNS_DB_PREFIX . "SELECT ppl_id FROM ?:pumps_packing_list WHERE ppl_item_type = ?s AND ppl_item_id = ?s AND ppl_item_part = ?s $cond ", $item_type, $item_id, $item_part);
        if(!($ppl_ids_for_delete = fn_check_before_deleting("del_packing_list_items", $ppl_ids_for_delete))){
            return false;
        }
        $ids = $ppl_ids_for_delete;
    }

    if($item_type == UNS_PACKING_TYPE__ITEM){
        $not = " not ";
    }

    if(is__array($ids)){
        db_query(UNS_DB_PREFIX . "DELETE FROM ?:pumps_packing_list WHERE ppl_item_type = ?s AND ppl_item_id = ?s AND ppl_item_part = ?s AND ppl_id $not in (?n) ", $item_type, $item_id, $item_part, $ids);
    } else{
        db_query(UNS_DB_PREFIX . "DELETE FROM ?:pumps_packing_list WHERE ppl_item_type = ?s AND ppl_item_id = ?s AND ppl_item_part = ?s ", $item_type, $item_id, $item_part);
    }

    return true;
}


/**
 * УДАЛИТЬ КОМПЛЕКТАЦИЮ
 * @param $ids - ids серии_насосов/насосов необходимых удалить
 * @param $type
 * @return bool
 */
function fn_uns__del_packing_list($ids, $type) {
    if(!($ids = to__array($ids)) || !fn_uns_check_packing_list_types($type)){
        return false;
    }

    if($type == UNS_PACKING_TYPE__ITEM){
        // Зачистить лишнее в таблице ЗАМЕЩЕНИЯ
        $ps_ids  = db_get_fields(UNS_DB_PREFIX . "SELECT DISTINCT ps_id FROM ?:pumps WHERE p_id in (?n) ", $ids);
        $ppl_ids = db_get_fields(UNS_DB_PREFIX . "SELECT ppl_id FROM ?:pumps_packing_list WHERE ppl_item_type = 'S' AND ppl_item_id in (?n) ", $ps_ids);
        if(is__array($ppl_ids)){
            db_query(UNS_DB_PREFIX . "DELETE FROM ?:pumps_packing_list_replacement WHERE ppl_id in (?n) ", $ppl_ids);
        }
    }
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:pumps_packing_list WHERE ppl_item_type = ?s AND ppl_item_id in (?n) ", $type, $ids);
    return true;
}


/**
 * ПОЛУЧЕНИЕ ДАННЫХ ДЛЯ ГЕНЕРАЦИИ ФОРМЫ КОМПЛЕКТАЦИЙ
 * @param $id
 * @param $type
 * @param $view
 * @return bool
 */
// Функции сортировки
function sort_get_data($d, $type) {
    $r = 0;
    switch($type){
        case "item_class_position":
            if($d['item_type'] == "D"){
                $r = array_shift($d['items'][$d['item_id']]['accounting_data']['materials']);
            } else{
                $r = array_shift($d['items'][$d['item_id']]);
            }
            $r = $r['mclass_position'];
            break;

        case "item_cat_position":
            if($d['item_type'] == "D"){
                $r = $d['item_info']['dcat_position'];
            } else{
                $r = $d['item_info']['mcat_position'];
            }
            break;

        case "item_cat_name":
            if($d['item_type'] == "D"){
                $r = $d['item_info']['dcat_name'];
            } else{
                $r = $d['item_info']['mcat_name'];
            }
            break;

        case "item_position":
            if($d['item_type'] == "D"){
                $r = $d['item_info']['detail_position'];
            } else{
                $r = $d['item_info']['material_position'];
            }
            break;

        case "item_name":
            if($d['item_type'] == "D"){
                $r = $d['item_info']['detail_name'];
            } else{
                $r = $d['item_info']['material_name'];
            }
            break;
    }
    return $r;
}
function sort_numcmp($a, $b){
    if ($a > $b) return 1;
    elseif ($a < $b) return -1;
    else return 0;
}
function sort_items($a, $b) {
    // Сортировка по Деталям - Материалам
    $res = 0;
    $d_m = strcasecmp($a['item_type'], $b['item_type']);
    if($d_m != 0){
        $res = $d_m;
    } else{
        if ($class_position = sort_numcmp(sort_get_data($a, "item_class_position"), sort_get_data($b, "item_class_position"))){
            $res = $class_position;
        }else{
            if ($cat_position = sort_numcmp(sort_get_data($a, "item_cat_position"), sort_get_data($b, "item_cat_position"))){
                $res = $cat_position;
            }else{
                if ($cat_name = strcasecmp(sort_get_data($a, "item_cat_name"), sort_get_data($b, "item_cat_name"))){
                    $res = $cat_name;
                }else{
                    if ($item_position = sort_numcmp(sort_get_data($a, "item_position"), sort_get_data($b, "item_position"))){
                        $res = $item_position;
                    }else{
                        if ($item_name = strcasecmp(sort_get_data($a, "item_name"), sort_get_data($b, "item_name"))){
                            $res = $item_name;
                        }else{
                            $res = $item_name;
                        }
                    }
                }
            }
        }
    }
    return $res;
}



function fn_uns_generate_data_for_packing ($id, $type, &$view){
    // ЗАПРОСИТЬ ТИП ПОЗИЦИИ:
    $item_type = array(
        array('id'=>'0','name'=>'---'),
        array('id'=>'D','name'=>'Деталь'),
        array('id'=>'M','name'=>'Материал')
    );
    $view->assign('pl__item_type', $item_type);

    // ЗАПРОСИТЬ СПИСОК КАТЕГОРИЙ ДЕТАЛЕЙ и МАТЕРИАЛОВ:
    list($mcategories_plain) = fn_uns__get_materials_categories(array('plain' => true, 'mcat_id_exclude' => UNS_MATERIAL_CATEGORY__CAST));
    $view->assign('pl__mcategories_plain', $mcategories_plain);

    list($dcategories_plain) = fn_uns__get_details_categories(array('plain' => true));
    $view->assign('pl__dcategories_plain', $dcategories_plain);



    if (!is__more_0($id) || !fn_uns_check_packing_list_types($type)) return false;

    // ЗАПРОСИТЬ ПОЗИЦИИ КОМПЛЕКТАЦИИ
    $p_default = array(
        'categories_info' => true,
        'with_other_items_current_categories' => true,
        'with_units' => true,
    );

    $view->assign('pl__packing_list_type', $type);

    $pplr_type = array(
        array('id'=>'0','name'=>'---'),
        array('id'=>'D','name'=>'Удалить'),
        array('id'=>'R','name'=>'Заменить')
    );
    $view->assign('pl__pplr_type', $pplr_type);

    if ($type == UNS_PACKING_TYPE__ITEM){
        // 1. Взять всю комплектацию из серии
        // 2. Наложить комплектацию замещения из таблицы замещений
        // 3. Дополнить индивидуальной комплектацией

        // 1.
        list($pump_series) = fn_uns__get_pump_series(array("p_id"=>$id));
        if (!is__array($pump_series)) return false;
        $pump_series = array_shift($pump_series);
        $p = array('ps_id' => $pump_series['ps_id']);
        list($packing_list_series) = fn_uns__get_packing_list(array_merge($p_default, $p));

        // 2. Наложение замещения
        $packing_list_series = fn_uns__get_packing_list_replacement(array('p_id'=>$id, 'with_items'=>true, 'with_units'=>true), $packing_list_series);

        // 3.
        $p = array('p_id' => $id);
        list($packing_list_item) = fn_uns__get_packing_list(array_merge($p_default, $p));

        $packing_list = array();
        $packing_list[UNS_PACKING_PART__PUMP]  = array_merge((is__array($packing_list_series[UNS_PACKING_PART__PUMP])?$packing_list_series[UNS_PACKING_PART__PUMP]:array()), (is__array($packing_list_item[UNS_PACKING_PART__PUMP])?$packing_list_item[UNS_PACKING_PART__PUMP]:array()));
        $packing_list[UNS_PACKING_PART__FRAME]  = array_merge((is__array($packing_list_series[UNS_PACKING_PART__FRAME])?$packing_list_series[UNS_PACKING_PART__FRAME]:array()), (is__array($packing_list_item[UNS_PACKING_PART__FRAME])?$packing_list_item[UNS_PACKING_PART__FRAME]:array()));
        $packing_list[UNS_PACKING_PART__MOTOR]  = array_merge((is__array($packing_list_series[UNS_PACKING_PART__MOTOR])?$packing_list_series[UNS_PACKING_PART__MOTOR]:array()), (is__array($packing_list_item[UNS_PACKING_PART__MOTOR])?$packing_list_item[UNS_PACKING_PART__MOTOR]:array()));

    }else/*if ($type == UNS_PACKING_TYPE__SERIES)*/{
        $p = array('ps_id' => $id);
        list($packing_list) = fn_uns__get_packing_list(array_merge($p_default, $p));
    }

    usort($packing_list[UNS_PACKING_PART__PUMP], "sort_items");
    usort($packing_list[UNS_PACKING_PART__FRAME], "sort_items");
    usort($packing_list[UNS_PACKING_PART__MOTOR], "sort_items");

    $view->assign('pl__packing_list', $packing_list);

    return array('type'              => $type,
                 'item_type'         => $item_type,
                 'mcategories_plain' => $mcategories_plain,
                 'dcategories_plain' => $dcategories_plain,
                 'packing_list'      => $packing_list,
                 'pplr_type'         => $pplr_type,);
}



/**
 * ПОЛУЧИТЬ КОМПЛЕКТАЦИЮ НАСОСА ТОЛЬКО ИЗ ЛИТЬЯ
 */
function fn_uns__get_packing_list_by_pump ($p_id, $item_type="M", $all_classes=false){
    $pump = array_shift(array_shift(fn_uns__get_pumps(array('p_id'=>$p_id))));
    if (!is__array($pump)) return false;

    $field = "if(details.r_detail_id>0, r_uns_detail__and__items.material_id, uns_detail__and__items.material_id) as material_id";
    $m_key = "material_id";
    if ($item_type == "D"){
        $field = "if(details.r_detail_id>0, r_uns_detail__and__items.detail_id, uns_detail__and__items.detail_id) as detail_id";
        $m_key = "detail_id";
    }

    $classes = " and uns_materials.mclass_id = 1 ";
    if ($all_classes) $classes = "";

    $sql = UNS_DB_PREFIX . "
        SELECT
        #     details.detail_id
        #   , details.detail_quantity
        #   , details.r_detail_id
        #   , details.r_detail_quantity
        #
        #   , uns_detail__and__items.material_id as material_id
        #   , uns_detail__and__items.quantity  as material_quantity
        #   , r_uns_detail__and__items.material_id as r_material_id
        #   , r_uns_detail__and__items.quantity as r_material_quantity
        #   ,
            $field
        # , sum(if(details.r_detail_id>0, details.r_detail_quantity*r_uns_detail__and__items.quantity, details.detail_quantity*uns_detail__and__items.quantity)) as quantity
          , sum(if(details.r_detail_id>0, details.r_detail_quantity, details.detail_quantity)) as quantity
        #  , if(r_uns_detail__and__items.material_id>0, 'Y', 'N') as __replace

        FROM (
          SELECT
             uns_pumps_packing_list.ppl_id
            ,uns_pumps_packing_list.item_id               as detail_id
            ,uns_pumps_packing_list.quantity              as detail_quantity

            ,uns_pumps_packing_list_replacement.item_id   as r_detail_id
            ,uns_pumps_packing_list_replacement.quantity  as r_detail_quantity
          FROM uns_pumps_packing_list
            LEFT JOIN uns_pumps_packing_list_replacement ON (
              uns_pumps_packing_list.ppl_id = uns_pumps_packing_list_replacement.ppl_id
              AND uns_pumps_packing_list_replacement.p_id = {$pump['p_id']}
            )
          WHERE
            uns_pumps_packing_list.ppl_status                 = 'A'
            AND uns_pumps_packing_list.ppl_item_part          = 'P'
            AND uns_pumps_packing_list.ppl_item_type          = 'S'
            AND uns_pumps_packing_list.ppl_item_id            = {$pump['ps_id']}
            AND uns_pumps_packing_list.item_type              = 'D'
            AND (uns_pumps_packing_list_replacement.pplr_type is null or uns_pumps_packing_list_replacement.pplr_type = 'R')
          UNION
          SELECT
            uns_pumps_packing_list.ppl_id
           ,uns_pumps_packing_list.item_id               as detail_id
           ,uns_pumps_packing_list.quantity              as detail_quantity

           ,uns_pumps_packing_list_replacement.item_id   as r_detail_id
           ,uns_pumps_packing_list_replacement.quantity  as r_detail_quantity
          FROM uns_pumps_packing_list
            LEFT JOIN uns_pumps_packing_list_replacement ON (
              uns_pumps_packing_list.ppl_id = uns_pumps_packing_list_replacement.ppl_id
              AND uns_pumps_packing_list_replacement.p_id = {$pump['p_id']}
            )
          WHERE
                uns_pumps_packing_list.ppl_status             = 'A'
            AND uns_pumps_packing_list.ppl_item_part          = 'P'
            AND uns_pumps_packing_list.ppl_item_type          = 'I'
            AND uns_pumps_packing_list.ppl_item_id            = {$pump['p_id']}
            AND uns_pumps_packing_list.item_type              = 'D'
            AND (uns_pumps_packing_list_replacement.pplr_type is null OR uns_pumps_packing_list_replacement.pplr_type = 'R')
        ) as details
          left join uns_detail__and__items on (details.detail_id = uns_detail__and__items.detail_id)
          left join uns_detail__and__items as r_uns_detail__and__items on (details.r_detail_id = r_uns_detail__and__items.detail_id)
          inner join uns_materials on (uns_detail__and__items.material_id = uns_materials.material_id {$classes})
        GROUP BY $m_key
    ";
    $data = db_get_hash_array($sql, $m_key);
    if (!is__array($data)) return false;
    return $data;
}




