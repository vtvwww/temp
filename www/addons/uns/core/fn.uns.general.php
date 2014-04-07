<?php

if(!defined('AREA')){
    die('Access denied');
}
/**
 * Простая функция для реализации поведения из PHP 5
 */
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


/**
 * @param $type -   I - insert;
 *                  U - update;
 * @param $data
 * @return array
 */
function fn_uns_add_date_and_who ($type, $data){
    if (in_array($type, array("I", "U")) and is__array($data) and is__more_0($_SESSION['auth']['user_id'])){
        $d = array(
            "date_of_update" => TIME,
            "who_updated" => $_SESSION['auth']['user_id'],
        );
        if ($type == "I"){
            $di = array(
                "date_of_create" => TIME,
                "who_created" => $_SESSION['auth']['user_id'],
            );
            $d = array_merge($d, $di);
        }
        $data = array_merge($d, $data);
    }
    return $data;
}


// Функции которые должны быть в каждом контроллере
function fn_uns_defaul_functions($controller, $mode) {
    fn_uns_upd_page($controller, $mode);
    fn_uns_upd_search($controller, $mode);

    fn_uns_mark_item($controller, $mode);

    /* MENU */
    switch($controller){
        case "uns_units":
        case "uns_unit_categories":
            Registry::set('navigation.selected_tab', 'uns');
            Registry::set('navigation.subsection', "uns_units");
            break;

        case "uns_features":
            Registry::set('navigation.selected_tab', 'uns');
            Registry::set('navigation.subsection', "uns_features");
            break;

        case "uns_options":
            Registry::set('navigation.selected_tab', 'uns');
            Registry::set('navigation.subsection', "uns_options");
            break;

        case "uns_material_classes":
        case "uns_material_categories":
        case "uns_materials":
            Registry::set('navigation.selected_tab', 'uns');
            Registry::set('navigation.subsection', "uns_materials");
            break;

        case "uns_detail_categories":
        case "uns_details":
            Registry::set('navigation.selected_tab', 'uns');
            Registry::set('navigation.subsection', "uns_details");
            break;

        case "uns_pump_types":
        case "uns_pump_series":
        case "uns_pumps":
            Registry::set('navigation.selected_tab', 'uns');
            Registry::set('navigation.subsection', "uns_pumps");
            break;


        case "acc_objects":
        case "acc_ostatki":
        case "acc_book_documents":
            Registry::set('navigation.selected_tab', 'uns_acc');
            Registry::set('navigation.subsection',   'acc_objects');
            break;


        case "foundry_get_balance":
        case "foundry_get_report":
            Registry::set('navigation.selected_tab', 'uns_foundry');
            break;
    }

}

// Сохранение текущей страницы
function fn_uns_upd_page($controller, $mode) {
    if($mode == 'manage'){
        if(is__more_0($_REQUEST['page'])){
            $_SESSION['current_page'][$controller] = $_REQUEST['page'];
        } else{
            if(!is__more_0($_SESSION['current_page'][$controller])){
                $_SESSION['current_page'][$controller] = 1;
            }
        }
        $_REQUEST['page'] = $_SESSION['current_page'][$controller];
    }
    return true;
}


// маркировка позиции, которая была только-что просмотрена
function fn_uns_mark_item($controller, $mode) {
    if ($mode == "update"){
        $mark_item = 0;
        switch($controller){
            case "uns_materials":
                if(is__more_0($_REQUEST['material_id'])){
                    $mark_item = $_REQUEST['material_id'];
                }
                break;

            case "uns_details":
                if(is__more_0($_REQUEST['detail_id'])){
                    $mark_item = $_REQUEST['detail_id'];
                }
                break;

            case "uns_pump_series":
                if(is__more_0($_REQUEST['ps_id'])){
                    $mark_item = $_REQUEST['ps_id'];
                }
                break;

            case "uns_pumps":
                if(is__more_0($_REQUEST['p_id'])){
                    $mark_item = $_REQUEST['p_id'];
                }
                break;

            case "acc_documents":
            case "uns_moving_mc_sk_su":
                if(is__more_0($_REQUEST['document_id'])){
                    $mark_item = $_REQUEST['document_id'];
                }
                break;

            case "uns_sheets":
                if(is__more_0($_REQUEST['sheet_id'])){
                    $mark_item = $_REQUEST['sheet_id'];
                }
                break;

            case "uns_kits":
                if(is__more_0($_REQUEST['kit_id'])){
                    $mark_item = $_REQUEST['kit_id'];
                }
                break;
        }
        $_SESSION['mark_item'][$controller] = $mark_item;
    }

    return true;
}

// Функция обработки панели фильтрации
function fn_uns_upd_search($controller, $mode) {
    if($mode == 'manage'){
        $func = "fn_{$controller}__search";
        if(function_exists($func)){
            $func($controller);
        }
    }
    return true;
}

// Функция обработки панели фильтрации
function fn_uns_search_set_get_params($controller, $params = array()) {
    if (is__array($params)){
        foreach ($params as $p){
            if (isset($_REQUEST[$p])){
                $_SESSION['current_search'][$controller][$p] = $_REQUEST[$p];
            }

            if (isset($_SESSION['current_search'][$controller][$p])){
                $_REQUEST[$p] = $_SESSION['current_search'][$controller][$p];
            }
        }
    }
    return true;
}



/**
 * ГЕНЕРАТОР БОКОВОГО МЕНЮ
 * @param $section
 * @return bool
 */
function fn_uns_add_sections($section) {
    switch($section){
        case "uns_units":
        case "uns_unit_categories":
            Registry::set('navigation.dynamic.sections', array('uns_units'           => array('title' => fn_get_lang_var('uns_units'),
                                                                                              'href'  => 'uns_units.manage',),
                                                               'uns_unit_categories' => array('title' => fn_get_lang_var('uns_unit_categories'),
                                                                                              'href'  => 'uns_unit_categories.manage',),));
            break;

        case "uns_features":
            Registry::set('navigation.dynamic.sections', array('uns_features' => array('title' => fn_get_lang_var('uns_features'),
                                                                                       'href'  => 'uns_features.manage',),));
            break;

        case "uns_options":
            Registry::set('navigation.dynamic.sections', array('uns_options' => array('title' => fn_get_lang_var('uns_options'),
                                                                                      'href'  => 'uns_options.manage',),));
            break;

        case "uns_material_classes":
        case "uns_material_categories":
        case "uns_materials":
            Registry::set('navigation.dynamic.sections', array('uns_materials'           => array('title' => fn_get_lang_var('uns_materials'),
                                                                                                  'href'  => 'uns_materials.manage',),
                                                               'uns_material_categories' => array('title' => fn_get_lang_var('uns_material_categories'),
                                                                                                  'href'  => 'uns_material_categories.manage',),
                                                               'uns_material_classes'    => array('title' => fn_get_lang_var('uns_material_classes'),
                                                                                                  'href'  => 'uns_material_classes.manage',),));
            break;

        case "uns_detail_categories":
        case "uns_details":
            Registry::set('navigation.dynamic.sections', array('uns_details'           => array('title' => fn_get_lang_var('uns_details'),
                                                                                                'href'  => 'uns_details.manage',),
                                                               'uns_detail_categories' => array('title' => fn_get_lang_var('uns_detail_categories'),
                                                                                                'href'  => 'uns_detail_categories.manage',),));
            break;

        case "uns_pump_types":
        case "uns_pump_series":
        case "uns_pumps":
            Registry::set('navigation.dynamic.sections', array('uns_pumps'       => array('title' => fn_get_lang_var('uns_pumps'),
                                                                                          'href'  => 'uns_pumps.manage',),
                                                               'uns_pump_series' => array('title' => fn_get_lang_var('uns_pump_series'),
                                                                                          'href'  => 'uns_pump_series.manage',),

                                                               'uns_pump_types'  => array('title' => fn_get_lang_var('uns_pump_types'),
                                                                                          'href'  => 'uns_pump_types.manage',),

            ));
            break;

        case "acc_ostatki":
        case "acc_book_documents":
        case "acc_objects":
            Registry::set('navigation.dynamic.sections', array('acc_objects'            => array('title' => fn_get_lang_var('acc_objects'),
                                                                                                'href'  => 'acc_objects.manage',),
                                                               'acc_ostatki'            => array('title' => fn_get_lang_var('acc_ostatki'),
                                                                                                'href'  => 'acc_ostatki.manage',),
                                                               'acc_book_documents'     => array('title' => fn_get_lang_var('acc_book_documents'),
                                                                                                'href'  => 'acc_book_documents.manage',),
//                                                               'acc_documents'          => array('title' => fn_get_lang_var('acc_documents'),
//                                                                                                'href'  => 'acc_documents.manage',),

            ));
            break;

        case "foundry_get_balance":
        case "foundry_get_report":
        case "acc_documents":
            Registry::set('navigation.dynamic.sections', array('foundry_get_balance'=> array('title' => fn_get_lang_var('uns_foundry_balance'),
                                                                                                 'href'  => 'foundry_get_balance.manage',),
                                                               'foundry_get_report'=> array('title' => fn_get_lang_var('uns_foundry_report'),
                                                                                                 'href'  => 'foundry_get_report.manage',),
                                                               'acc_documents'          => array('title' => "Движения по Складу литья",
                                                                                                 'href'  => 'acc_documents.manage',),
            ));
            break;


        case "uns_sheets":
        case "uns_kits":
        case "uns_remakes":
        case "uns_balance_mc_sk_su":
        case "uns_moving_mc_sk_su":
        case "uns_balance_sgp":
        case "uns_moving_sgp":
//        case "uns_moving_sk":
//        case "uns_moving_su":
            Registry::set('navigation.dynamic.sections', array(
                                                                'uns_balance_mc_sk_su'   => array('title' => fn_get_lang_var('uns_balance_mc_sk_su'),
                                                                                                  'href'  => 'uns_balance_mc_sk_su.manage',),
                                                                'uns_balance_sgp'   => array('title' => fn_get_lang_var('uns_balance_sgp'),
                                                                                                  'href'  => 'uns_balance_sgp.manage',),
                                                                'uns_sheets'   => array('title' => fn_get_lang_var('uns_sheets'),
                                                                                                  'href'  => 'uns_sheets.manage',),
                                                                'uns_kits'   => array('title' => fn_get_lang_var('uns_kits'),
                                                                                                  'href'  => 'uns_kits.manage',),
                                                               'uns_moving_mc_sk_su'   => array('title' => fn_get_lang_var('uns_moving_mc_sk_su'),
                                                                                                 'href'  => 'uns_moving_mc_sk_su.manage',),
//                                                               'uns_moving_sk'   => array('title' => fn_get_lang_var('uns_moving_sk'),
//                                                                                                 'href'  => 'uns_moving_sk.manage',),
//                                                               'uns_moving_su'   => array('title' => fn_get_lang_var('uns_moving_su'),
//                                                                                                 'href'  => 'uns_moving_su.manage',),
//                                                               'uns_moving_sgp'   => array('title' => fn_get_lang_var('uns_moving_sgp'),
//                                                                                                 'href'  => 'uns_moving_sgp.manage',),
            ));
            break;

        default:
    }
    Registry::set('navigation.dynamic.active_section', $section);
    return true;
}


/**
 * ГЕНЕРАТОР ВКЛАДОК НА ПОПАПЕ
 * @param     $d
 * @param int $id
 */
function fn_uns_navigation_tabs_old($d, $id = 0) {
    $tabs = array();
    foreach($d as $k => $v){
        $tabs[$k . '_' . $id] = array('title' => $v, 'js' => true);
    }
    if(!empty($tabs)){
        Registry::set('navigation.tabs', $tabs);
    }
}

function fn_uns_navigation_tabs($d) {
    $tabs = array();
    foreach($d as $k => $v){
        $tabs[$k] = array('title' => $v, 'js' => true);
    }
    if(!empty($tabs)){
        Registry::set('navigation.tabs', $tabs);
    }
}

/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
function fn_concat_date_time ($data, $default=false){
    $time = array(
        "00:00" => 0,
        "01:00" => 1*60*60,
        "02:00" => 2*60*60,
        "03:00" => 3*60*60,
        "04:00" => 4*60*60,
        "05:00" => 5*60*60,
        "06:00" => 6*60*60,
        "07:00" => 7*60*60,
        "08:00" => 8*60*60,
        "09:00" => 9*60*60,
        "10:00" => 10*60*60,
        "11:00" => 11*60*60,
        "12:00" => 12*60*60,
        "13:00" => 13*60*60,
        "14:00" => 14*60*60,
        "15:00" => 15*60*60,
        "16:00" => 16*60*60,
        "17:00" => 17*60*60,
        "18:00" => 18*60*60,
        "19:00" => 19*60*60,
        "20:00" => 20*60*60,
        "21:00" => 21*60*60,
        "22:00" => 22*60*60,
        "23:00" => 23*60*60,
    );
    $res = TIME;
    if (isset($data['date'], $data['time'])){
        $res = (fn_parse_date($data["date"]) + $time[$data['time']]);
    }elseif (isset($data['date'])) {
        $res = (fn_parse_date($data["date"]));
    }
    return $res;
}

/**
 * ПРОВЕРКА ТИПА ЕЛЕМЕНТА ИЗ ДОСПУСТИМЫХ
 * @param $item_type
 * @return bool
 */
function fn_uns_check_item_types($item_type) {
    if(false === strpos(UNS_ITEM_TYPES, "|" . $item_type . "|")){
        return false;
    } else{
        return true;
    }
}

function fn_add_current_time ($target_time){
    $t_target_0 = strtotime(date('Y-m-d 00:00:00', $target_time));
    $t_current_0 = strtotime(date('Y-m-d 00:00:00', TIME));
    return $t_target_0+(TIME-$t_current_0);
}


function fn_fvalue($value = 0, $decimals = 4, $return_as_float = true) {
    $value = sprintf('%.' . $decimals . 'f', round((double) $value + 0.00000000001, $decimals));
    return $return_as_float ? (float) $value : $value;
}

function fn_get_db_field($field_name="", $field_value="", $table=""){
    $res = false;
    if (!strlen($field_name) or !strlen($field_value) or !strlen($table)) return $res;
    $res = db_get_field(UNS_DB_PREFIX . "SELECT $field_name FROM $table WHERE $field_name = $field_value");
    return $res;
}

function fn_check_value($v) {
    return $v;
}

function fn_get_period_name($period, $time_from, $time_to) {
    list ($time_from, $time_to) = fn_create_periods(array('period'    => $period,
                                                          'time_from' => $time_from,
                                                          'time_to'   => $time_to));
    $range = date(UNS_DATE, $time_from) . " - " . date(UNS_DATE, $time_to);
    switch($period){
        case "A":
            return "За все время ";
            break;

        case "D":
            return "За сегодня ($range)";
            break;
        case "W":
            return "За текущую неделю ($range)";
            break;
        case "M":
            return "За текущий месяц ($range)";
            break;
        case "Y":
            return "За текущий год ($range)";
            break;

        case "LD":
            return "За вчера ($range)";
            break;
        case "LW":
            return "За предыдущую неделю ($range)";
            break;
        case "LM":
            return "За предыдущий месяц ($range)";
            break;
        case "LY":
            return "За предыдущий год ($range)";
            break;

        case "HH":
            return "За последние 24 часа ($range)";
            break;
        case "HW":
            return "За последние 7 дней ($range)";
            break;
        case "HM":
            return "За последние 30 дней ($range)";
            break;
        case "HC":
            return "За последние XX дней ($range)";
            break;

        case "C":
            return "За период: $range";
            break;
    }
    return "";
}


// СГРУППИРОВАТЬ ДАННЫЕ ПО ПОЛЮ
// $sql  = UNS_DB_PREFIX . "SELECT " . implode(", ", $fields) . " FROM $m_tbl $join WHERE 1 $condition $sorting $limit";
// $data = db_get_hash_array($sql, $m_key);
// $data = fn_group_data_by_field($data, 'task_id');
function fn_group_data_by_field ($data, $field){
    if (!is__array($data)) return $data;
    $res = array();
    foreach ($data as $k=>$v){
        if (isset($v[$field])){
            $res[$v[$field]][$k] = $v;
        }
    }
    return $res;
}

function fn_check_type($what, $where, $def=''){
    // define("UNS_ITEM_TYPES",             "|P|M|D|B");
    // define("UNS_TYPESIZES",              "|M|A|B|");
    // define("UNS_PACKING_TYPES",          "|I|S|");
    // define("UNS_PACKING_PARTS",          "|P|F|M|");  // Части агрегата: Насос, Рама, Двигатель
    // define("UNS_PACKING_REPLACEMENTS",   "|D|R|");
    if (false === strpos($where, "|".$what."|")){
        if (strlen($def)) return $def;
        else return false;
    }else{
        if (strlen($def)) return $what;
        else return true;
    }
}


//******************************************************************************
// ПРОВЕРКА ПЕРЕД УДАЛЕНИЕМ!!!!
// В функцию передаются желаемые ids к удалению, а возращаются только те которые
//   могут быть удалены после проверки

// if (!fn_check_before_deleting("del_unit_categories", $ids)) return false;
function fn_check_before_deleting($type, $ids) {
    if(!($ids = to__array($ids))){
        return false;
    }

    $valid_ids = array_combine($ids, $ids);
    $error_ids = array();
    switch($type){
        case "del_unit_categories":
            // 	1. Нельзя удалить КАТЕГОРИЮ ЕД. ИЗМ., если за ней числятся ЕД. ИЗМ.
            list($unit_categories) = fn_uns__get_unit_categories(array('uc_id' => $ids));
            list($units) = fn_uns__get_units(array('uc_id'               => $ids,
                                                   'group_by_categories' => true));
            if(!is__array($units)){
                $valid_ids = $ids;
            } else{
                foreach($ids as $id){
                    if(!($count = count($units[$id]['units']))){
                        $valid_ids[] = $id;
                    } else{
                        $error_ids[] = $id;
                        $names       = array();
                        foreach($units[$id]['units'] as $u){
                            $names[] = "<b>{$u['u_name']}</b>";
                        }
                        $text = "За КАТЕГОРИЕЙ <b>" . $unit_categories[$id]['uc_name'] . "</b> существуют следующие ед. изм ($count шт): " . implode(', ', $names) . ".";
                        fn_set_notification("E", UNS_DATA_NOT_BE_DELETED, $text);
                    }
                }
            }
            break;

        case "del_units":
            $f_name = "u_name";
            list($f_data) = fn_uns__get_units();

            // нельзя удалить ед. изм., если оно где-либо используется
            $f_id = "u_id";

            // Таблица ACCOUNTING__AND__ITEMS
            $error_ids = array_merge($error_ids, fn_check_before_deleting__check_table($ids, "?:accounting__and__items", $f_data, $f_id, $f_name));

            // Таблица 	UNS_DETAIL__AND__ITEMS
            $error_ids = array_merge($error_ids, fn_check_before_deleting__check_table($ids, "?:detail__and__items", $f_data, $f_id, $f_name));

            // Таблица 	UNS_FEATURES__AND__ITEMS
            $error_ids = array_merge($error_ids, fn_check_before_deleting__check_table($ids, "?:features__and__items", $f_data, $f_id, $f_name));

            // Таблица 	UNS_OPTION_VARIANTS
            $error_ids = array_merge($error_ids, fn_check_before_deleting__check_table($ids, "?:option_variants", $f_data, $f_id, $f_name));

            // Таблица 	UNS_PUMPS_PACKING_LIST
            $error_ids = array_merge($error_ids, fn_check_before_deleting__check_table($ids, "?:pumps_packing_list", $f_data, $f_id, $f_name));

            // Таблица 	UNS_PUMPS_PACKING_LIST_REPLACEMENT
            $error_ids = array_merge($error_ids, fn_check_before_deleting__check_table($ids, "?:pumps_packing_list_replacement", $f_data, $f_id, $f_name));

            break;

        case "del_feature":
            $f_name = "feature_name";
            list($f_data) = fn_uns__get_features();

            // 	Нельзя удалить ХАРАКТЕРИСТИКУ, если она где-либо используется
            $f_id        = "feature_id";
            $check_table = "?:features__and__items";
            $error_ids   = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name));

            break;

        case "del_option":
            $f_name = "option_name";
            list($f_data) = fn_uns__get_options();

            // Нельзя удалить ПАРАМЕТР, если он где-либо используется
            $f_id        = "option_id";
            $check_table = "?:options__and__items";
            $error_ids   = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name));

            break;

        case "del_option_variant":
            $f_name = "ov_value";
            list($f_data) = fn_uns__get_option_variants();

            // Нельзя удалить ВАРИАНТ ПАРАМЕТРА, если он где-либо используется
            $f_id        = "ov_id";
            $check_table = "?:options__and__items";
            $error_ids   = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name));

            break;

        case "del_materials_classes":
            $f_name = "mclass_name";
            list($f_data) = fn_uns__get_materials_classes();
            $f_id = "mclass_id";

            // Нельзя удалить КЛАСС, если за ней числятся МАТЕРИАЛЫ
            $check_table = "?:materials";
            $error_ids   = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name));

            break;

        case "del_materials_category":
            $f_name = "mcat_name";
            list($f_data) = fn_uns__get_materials_categories(array("plain" => true));

            $f_id        = "mcat_id";
            $check_table = "?:materials";

            // Нельзя удалить КАТЕГОРИЮ, если у нее или у ее дочерних категорий есть МАТЕРИАЛЫ
            foreach($ids as $id){
                $id_path       = db_get_field(UNS_DB_PREFIX . "SELECT mcat_id_path FROM ?:material_categories WHERE mcat_id = ?i", $id);
                $mcat_ids      = db_get_fields(UNS_DB_PREFIX . "SELECT mcat_id FROM ?:material_categories WHERE mcat_id = ?i OR mcat_id_path LIKE ?l", $id, "$id_path/%");
                $add_condition = db_quote(" OR mcat_id in (?n)", $mcat_ids);
                $res           = fn_check_before_deleting__check_table(array($id), $check_table, $f_data, $f_id, $f_name, $add_condition);
                if(is__array($res)){
                    $error_ids[] = $id;
                }
            }

            break;

        case "del_materials":
            $f_name = "material_name";
            list($f_data) = fn_uns__get_materials(array('format_name'     => true,
                                                        'with_accounting' => true));

            // Нельзя удалить МАТЕРИАЛ, если он используется в какой-либо ДЕТАЛИ
            $f_id        = "material_id";
            $check_table = "?:detail__and__items";
            $error_ids   = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name));

            // Нельзя удалить МАТЕРИАЛ, если он используется в какой-либо комплектации СЕРИИ НАСОСОВ или НАСОСА
            $f_id          = "item_id";
            $check_table   = "?:pumps_packing_list";
            $add_condition = db_quote(" AND item_type = 'M' ");
            $error_ids     = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name, $add_condition));

            // Нельзя удалить МАТЕРИАЛ, если он используется в какой-либо комплектации ЗАМЕНЫ
            $f_id          = "item_id";
            $check_table   = "?:pumps_packing_list_replacement";
            $add_condition = db_quote(" AND item_type = 'M' ");
            $error_ids     = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name, $add_condition));

            // Нельзя удалить МАТЕРИАЛ, если он используется в какой-либо ДВИЖЕНИИ МАТЕРИАЛА
            // utodo - реализовать позже

            break;

        case "del_details_category":
            $f_name = "dcat_name";
            list($f_data) = fn_uns__get_details_categories(array("plain" => true));

            $f_id        = "dcat_id";
            $check_table = "?:details";

            // Нельзя удалить КАТЕГОРИЮ, если у нее или у ее дочерних категорий есть МАТЕРИАЛЫ
            foreach($ids as $id){
                $id_path       = db_get_field(UNS_DB_PREFIX . "SELECT dcat_id_path FROM ?:detail_categories WHERE dcat_id = ?i", $id);
                $dcat_ids      = db_get_fields(UNS_DB_PREFIX . "SELECT dcat_id FROM ?:detail_categories WHERE dcat_id = ?i OR dcat_id_path LIKE ?l", $id, "$id_path/%");
                $add_condition = db_quote(" OR dcat_id in (?n)", $dcat_ids);
                $res           = fn_check_before_deleting__check_table(array($id), $check_table, $f_data, $f_id, $f_name, $add_condition);
                if(is__array($res)){
                    $error_ids[] = $id;
                }
            }
            break;

        case "del_details":
            $f_name = "detail_name";
            list($f_data) = fn_uns__get_details(array('format_name'     => true,
                                                      'with_accounting' => true));

            // Нельзя удалить ДЕТАЛЬ, если она используется в какой-либо комплектации СЕРИИ НАСОСОВ или НАСОСА
            $f_id          = "item_id";
            $check_table   = "?:pumps_packing_list";
            $add_condition = db_quote(" AND item_type = 'D' ");
            $error_ids     = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name, $add_condition));

            // Нельзя удалить ДЕТАЛЬ, если она используется в какой-либо комплектации ЗАМЕНЫ
            $f_id          = "item_id";
            $check_table   = "?:pumps_packing_list_replacement";
            $add_condition = db_quote(" AND item_type = 'D' ");
            $error_ids     = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name, $add_condition));

            // Нельзя удалить ДЕТАЛЬ, если она используется в какой-либо ДВИЖЕНИИ МАТЕРИАЛА
            // utodo - реализовать позже

            break;

        case "del_pump_type":
            $f_name = "pt_name";
            list($f_data) = fn_uns__get_pump_types();

            // Нельзя удалить ТИП НАСОСА, если за ним числятся СЕРИЯ НАСОСОВ
            $f_id        = "pt_id";
            $check_table = "?:pump_series";
            $error_ids   = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name));
            break;

        case "del_pump_series":
            $f_name = "ps_name";
            list($f_data) = fn_uns__get_pump_series();

            // Нельзя удалить СЕРИЮ НАСОСОВ, если за ней числятся НАСОСЫ
            $f_id        = "ps_id";
            $check_table = "?:pumps";
            $error_ids   = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name));
            break;

        case "del_pumps":
            //            $f_name = "ps_name";
            //            list($f_data) = fn_uns__get_pumps();
            //
            //            // Нельзя удалить НАСОС, если он используется в какой-либо ДВИЖЕНИИ
            //            $f_id = "ps_id";
            //            $check_table = "?:pumps";
            //            $error_ids = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name));
            break;

        case "del_packing_list_items":
            //            $f_name = "ov_value";
            //            list($f_data) = fn_uns__get_option_variants();
            $f_name = "";
            $f_data = array();

            // Нельзя удалить позицию в СЕРИИ НАСОСОВ,
            // если она в режиме замещения в комплектации НАСОСА,
            // но можно при  других режимах (режим удаления)

            $f_id          = "ppl_id";
            $check_table   = "?:pumps_packing_list_replacement";
            $add_condition = db_quote(" AND pplr_type = ?s ", UNS_PACKING_REPLACEMENT__REPLACE);
            $error_ids     = array_merge($error_ids, fn_check_before_deleting__check_table($ids, $check_table, $f_data, $f_id, $f_name, $add_condition));
            break;
    }

    if(is__array($error_ids)){
        $error_ids = array_unique($error_ids);
        foreach($error_ids as $e){
            unset($valid_ids[$e]);
        }
    }

    return ((is__array($valid_ids)) ? $valid_ids : false);
}


function fn_check_before_deleting__check_table($ids, $table, $main_data, $field, $name, $add_condition = "", $message = "") {
    if(!is__array($ids)){
        return array();
    }
    $error_ids = array();
    $data      = db_get_hash_array(UNS_DB_PREFIX . "SELECT $field, COUNT($field) as count FROM $table WHERE $field in (?n) $add_condition GROUP BY $field", $field, $ids);
    if(is__array($data)){
        foreach($data as $v_c){
            if($v_c['count']){
                $error_ids[] = $v_c[$field];
                $text        = "Значение <b>" . $main_data[$v_c[$field]][$name] . "</b> в таблице <b>$table</b> используется " . $v_c['count'] . " раз.";
                fn_set_notification("E", UNS_DATA_NOT_BE_DELETED, $text);
            }
        }
    }
    return $error_ids;
}





























