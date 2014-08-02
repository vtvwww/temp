<?php



function fn_uns__upd_accounting_item_weights($data, $ai_id){
    if (!is__array($data) || !is__more_0($ai_id)) return false;

    $aw_ids = array();
    foreach ($data as $k_d=>$v_d){
        foreach ($v_d as $d){
            if (is__more_0($d['aw_id']) && strlen($d['timestamp']) && strlen($d['value'])){
                // Обновить
                $v = array(
                    'timestamp' => fn_parse_date($d['timestamp']),
                    'value'     => fn_check_value($d['value']),
                    'typesize'  => $k_d,
                );
                db_query(UNS_DB_PREFIX . "UPDATE ?:accounting_weight SET ?u WHERE ai_id = ?i AND aw_id = ?i", $v, $ai_id, $d['aw_id']);
                $aw_ids[] = $d['aw_id'];
            }elseif ($d['aw_id']==0 && strlen($d['timestamp']) && strlen($d['value'])){
                // Добавление
                $v = array(
                    'ai_id'     => $ai_id,
                    'timestamp' => fn_parse_date($d['timestamp']),
                    'value'     => fn_check_value($d['value']),
                    'typesize'  => $k_d,
                );
                $aw_ids[] = db_query(UNS_DB_PREFIX . "INSERT INTO ?:accounting_weight ?e", $v);
            }
        }
    }
    if (is__array($aw_ids)){
        db_query(UNS_DB_PREFIX . "DELETE FROM ?:accounting_weight WHERE ai_id = ?i AND aw_id not in (?n) ", $ai_id, $aw_ids);
    }else{
        db_query(UNS_DB_PREFIX . "DELETE FROM ?:accounting_weight WHERE ai_id = ?i ", $ai_id);
    }
    return true;
}


/**
 * Запрос значение веса по выбранным items
 * @param        $item_type
 * @param        $item_id
 * @return array|bool
 */
function fn_uns__get_accounting_item_weights($item_type, $item_id){
    if (!is__array($item_id) && !fn_uns_check_item_types($item_type)) return false;

    $m_table = "?:accounting__and__items";
    $j_table = "?:accounting_weight";

    $fields[] = "$m_table.item_id";
    $fields[] = "$j_table.aw_id";
    $fields[] = "$j_table.timestamp";
    $fields[] = "$j_table.value";
    $fields[] = "$j_table.typesize";

    $condition = $join = $limit = $sorting = '';

    $join .= db_quote(" LEFT JOIN $j_table ON ($m_table.ai_id = $j_table.ai_id) ");

    $condition .= db_quote(" AND $m_table.item_id in (?n) ", $item_id);
    $condition .= db_quote(" AND $m_table.item_type = ?s ", (in_array($item_type, array("P", "PF", "PA")))?"P":$item_type);

    $sorting = " ORDER BY $m_table.item_id asc, $j_table.timestamp desc ";

    $data = db_get_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit");

    if (is__array($data )){
        $tmp = array();
        foreach ($data as $k=>$v){
            $tmp[$v['item_id']][$v['typesize']][] = $v;
        }
        $data = $tmp;
    }

    return (is__array($data))?$data:false;
}


/**
 * ЗАПРОС ПАРАМЕТРОВ ПРИВЯЗАННЫХ К ЭЛЕМЕНТАМ
 * @param $item_type
 * @param $item_id
 * @return array|bool
 */
function fn_uns__get_accounting_items($item_type, $item_id=array()){
    if (!is__array($item_id) && !fn_uns_check_item_types($item_type)) return false;

    $m_table = "?:accounting__and__items";
    $fields = array(
        $m_table.'.item_id',
        $m_table.'.ai_id',
        $m_table.'.u_id',
        'm_units.u_name as u_name',
    );

    $condition = $join = $limit = $sorting = '';

    $join .= db_quote(" LEFT JOIN ?:units as m_units ON (m_units.u_id = $m_table.u_id) ");

    $condition .= db_quote(" AND $m_table.item_type = ?s AND $m_table.item_id in (?n) ", (in_array($item_type, array("P", "PF", "PA")))?"P":$item_type, $item_id);

    $data = db_get_hash_array(UNS_DB_PREFIX . "SELECT " . implode(', ', $fields) . " FROM $m_table $join WHERE 1 $condition $sorting $limit", "item_id");

    if (is__array($data)){
        $weights = fn_uns__get_accounting_item_weights ($item_type, array_keys($data));
        foreach ($data as $k_d => $v_d){
            $data[$k_d]['weights'] = $weights[$k_d];
        }
    }

    return (is__array($data))?$data:false;
}


function fn_uns__del_accounting_items($item_type, $item_id){
    // Удалить список весовок
    $item_id = to__array($item_id);
    if (!$item_id || !fn_uns_check_item_types($item_type)) return false;

    $ai_ids = db_get_fields(UNS_DB_PREFIX . "SELECT DISTINCT ai_id FROM ?:accounting__and__items WHERE item_type = ?s AND item_id in (?n) ", $item_type, $item_id);

    // Удалить запись учета
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:accounting__and__items WHERE item_type = ?s AND item_id in (?n) ", $item_type, $item_id);
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:detail_typesizes WHERE detail_id in (?n) ", $item_id);

    if (is__array($ai_ids)){
        // Удалить весовки
        db_query(UNS_DB_PREFIX . "DELETE FROM ?:accounting_weight WHERE ai_id in (?n) ", $ai_ids);
    }

    return true;
}

function fn_uns__upd_accounting_items($item_type, $item_id, $data){
    if (!fn_uns_check_item_types($item_type) || !is__more_0($item_id)) return false;

    $ai_id = 0;
    if (is__more_0($data['ai_id']) && is__more_0($data['u_id'])){
        // Обновить запись
        $v = array(
            'u_id'     => $data['u_id'],
        );
        db_query(UNS_DB_PREFIX . "UPDATE ?:accounting__and__items SET ?u WHERE ai_id = ?i", $v, $data['ai_id']);
        if ($item_type == "D"){
            $dt_id = db_get_field(UNS_DB_PREFIX . "SELECT dt_id FROM ?:detail_typesizes WHERE detail_id = ?i ", $item_id);
            if(is__more_0($dt_id)){
                db_query(UNS_DB_PREFIX . "UPDATE ?:detail_typesizes SET ?u WHERE detail_id = ?i", array("size_a" => $data["typesizes"]["A"],
                                                                                                        "size_b" => $data["typesizes"]["B"],), $item_id);
            } else{
                db_query(UNS_DB_PREFIX . "INSERT INTO ?:detail_typesizes SET ?u ", array("size_a"    => $data["typesizes"]["A"],
                                                                                         "size_b"    => $data["typesizes"]["B"],
                                                                                         "detail_id" => $item_id,));
            }
        }
    }elseif (($data['ai_id'] == 0) && is__more_0($data['u_id'])){
        // Добавить запись
        $v = array(
            'u_id'     => $data['u_id'],
            'item_id'  => $item_id,
            'item_type'=> $item_type,
        );
        $data['ai_id'] = db_query(UNS_DB_PREFIX . "INSERT INTO ?:accounting__and__items ?e", $v);
        if ($item_type == "D"){
            $dt_id = db_get_field(UNS_DB_PREFIX . "SELECT dt_id FROM ?:detail_typesizes WHERE detail_id = ?i ", $item_id);
            if(is__more_0($dt_id)){
                db_query(UNS_DB_PREFIX . "UPDATE ?:detail_typesizes SET ?u WHERE detail_id = ?i", array("size_a" => $data["typesizes"]["A"],
                                                                                                        "size_b" => $data["typesizes"]["B"],), $item_id);
            } else{
                db_query(UNS_DB_PREFIX . "INSERT INTO ?:detail_typesizes SET ?u ", array("size_a"    => $data["typesizes"]["A"],
                                                                                         "size_b"    => $data["typesizes"]["B"],
                                                                                         "detail_id" => $item_id,));
            }
        }
    }elseif (is__more_0($data['ai_id']) && $data['u_id'] == 0){
        // Удалить запись
        fn_uns__del_accounting_items($item_type, $item_id);
    }

    if (is__more_0($data['ai_id'])){
        // Выполнить обновления данных веса
        fn_uns__upd_accounting_item_weights($data['weights'], $data['ai_id']);
    }

    return true;
}

// Сохранить данные о расходе материалов на деталь
function fn_uns__upd_accounting_items_materials($item_id, $data){
    if (!is__more_0($item_id)) return false;

    $di_ids = array();
    foreach ($data as $d){
        if (empty($d['allowance'])) $d['allowance'] = 0;
        if (is__more_0($d['mcat_id'], $d['material_id'], $d['quantity'], $d['u_id'])){
            if ($d['add_quantity_state'] == "A"){
                if (!is__more_0($d['add_quantity'])){
                    $d['add_quantity']      = 0.0001;
                }
            }else{
                $d['add_quantity_state']    = "D";
                $d['add_quantity']          = 0;
            }

            $v = array(
                'detail_id'     => $item_id,
                'u_id'          => $d['u_id'],
                'material_id'   => $d['material_id'],
                'quantity'      => $d['quantity'],
                'allowance'     => $d['allowance'],
                'add_quantity'  => $d['add_quantity'],
                'add_quantity_state' => $d['add_quantity_state'],
            );

            if (is__more_0($d['di_id'])){
                // Обновить
                $i = db_query(UNS_DB_PREFIX . "UPDATE ?:detail__and__items SET ?u WHERE di_id = ?i", $v, $d['di_id']);
                $di_ids[] = $d['di_id'];
            }elseif ($d['di_id'] == 0){
                // Добавление
                $di_ids[] = db_query(UNS_DB_PREFIX . "INSERT INTO ?:detail__and__items ?e", $v);
            }
        }
    }
    if (is__array($di_ids)){
        // Удалить все лишнее
        db_query(UNS_DB_PREFIX . "DELETE FROM ?:detail__and__items WHERE detail_id = ?i AND di_id not in (?n) ", $item_id, $di_ids);
    }else{
        db_query(UNS_DB_PREFIX . "DELETE FROM ?:detail__and__items WHERE detail_id = ?i ", $item_id);
    }

    return true;
}

// Удалить данные о расходе материалов на деталь
function fn_uns__del_accounting_items_materials($ids){
    if (!($ids = to__array($ids))) return false;
    db_query(UNS_DB_PREFIX . "DELETE FROM ?:detail__and__items WHERE detail_id in (?n) ", $ids);
    return true;
}

/**
 * ГЕНЕРАТОР ДАННЫХ ДЛЯ ФОРМЫ УЧЕТА
 * @param $type
 * @param $id
 * @param $view
 * @return bool
 */
function fn_uns_generate_data_for_accounting ($type, $id, &$view){
    list($units) = fn_uns__get_units(array('all'=>true, 'with_variants'=>true, 'group_by_categories' => true, /*'exclude_weight_group' => true */));
    $view->assign('ai__units', $units);

    if (!is__more_0($id)) return false;
    $accounting  = fn_uns__get_accounting_items($type, array($id));
    $view->assign('ai__accounting', $accounting[$id]);

    return $accounting[$id];
}
