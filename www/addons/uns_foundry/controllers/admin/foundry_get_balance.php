<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

if(!defined('AREA')){
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $suffix = '';
    if($mode == 'update'){
        $id = fn_uns__upd_object($_REQUEST['o_id'], $_REQUEST['data']);
        fn_delete_notification('changes_saved');
        if($id !== false){
            fn_set_notification("N", $_REQUEST['data']['o_name'], UNS_DATA_UPDATED);
        }
        $suffix = "update&o_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if($mode == 'delete'){
        if(is__array($_REQUEST['o_ids'])){
            fn_uns__del_object($_REQUEST['o_ids']);
        }
        $suffix = 'manage';
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}


if($mode == 'manage' or $mode == 'update' or $mode == 'add'){
//    fn_uns_add_sections($controller);
    fn_uns_navigation_tabs(array('general' => fn_get_lang_var('general'),));
}


if($mode == 'manage'){
    $p = array(
        "plain"         => true,
        "all"           => true,
        "o_id"          => array(8),  // Склад литья
        "item_type"     => "M",
        "add_item_info" => true,
        "view_all_position" => "Y",
        "mclass_id"     => 1,
        "with_weight"   => true,
    );

    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);
    $_REQUEST["accessory_pumps"] = "Y";

    if ($_REQUEST["only_for_pumps"] == "Y" and !is__more_0($_REQUEST["mcat_id"])){
        $p["exclude_mcat_id"] = array(78,79,80); // На собственные нужды, На продажу, Старые отливки
    }

    $p = array_merge($_REQUEST, $p);
    $view->assign('search', $p);

    if (!is__more_0($_REQUEST["mcat_id"]) and $_REQUEST["all_materials"] != "Y"){
    }else{
        list($balance, $search) = fn_uns__get_balance($p);
        $view->assign('balance', $balance);
        $view->assign('search', $search);
    }
    $view->assign('expand_all', false);

    // Расчет веса
    $weights = array(
        "n"  => 0,  // начальный остаток
        "p"  => 0,  // приход
        "r"  => 0,  // расход
        "k"  => 0,  // конечный остаток
    );
    // Расчет кол-ва
    $amounts = array(
        "n"  => 0,  // начальный остаток
        "p"  => 0,  // приход
        "r"  => 0,  // расход
        "k"  => 0,  // конечный остаток
    );
    if (is__array($balance)){
        foreach ($balance as $group){
            if (is__array($group["items"])){
                foreach ($group["items"] as $i){
                    $weights["n"] += $i["weight"]*$i["nach"];
                    $weights["p"] += $i["weight"]*$i["current__in"];
                    $weights["r"] += $i["weight"]*$i["current__out"];
                    $weights["k"] += $i["weight"]*$i["konech"];

                    $amounts["n"] += $i["nach"];
                    $amounts["p"] += $i["current__in"];
                    $amounts["r"] += $i["current__out"];
                    $amounts["k"] += $i["konech"];
                }
            }
        }
    }
    $view->assign('weights', $weights);
    $view->assign('amounts', $amounts);

    // Последняя дата движения
    $info_of_the_last_movement = fn_uns__get_info_of_the_last_movement(array('o_id'=>8)); // склад литья
    $view->assign('info_of_the_last_movement', $info_of_the_last_movement);

    // Запрос категорий материалов
    list($mcategories_plain) = fn_uns__get_materials_categories(array("plain" => true, "with_q_ty"=>false, "mcat_id"=>UNS_MATERIAL_CATEGORY__CAST));
    $view->assign('mcategories_plain', $mcategories_plain);
    $view->assign('mcategories_plain_with_q_ty', false);

    list($pumps) = fn_uns__get_pumps(array("group_by_series"=>true));
    $view->assign('pumps', $pumps);

    list($mclasses) = fn_uns__get_materials_classes();
    $view->assign('mclasses', $mclasses);
}


if($mode == 'update'){
    if(!is__more_0($_REQUEST['o_id'])){
        return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
    }

    $o_id = $_REQUEST['o_id'];
    $p       = array('item_ids' => $o_id);
    list($object) = fn_uns__get_objects($p);
    $view->assign('object', array_shift($object));

    // Список для выпадающего списка
    list($objects_plain) = fn_uns__get_objects(array('plain' => true));
    $view->assign('objects_plain', $objects_plain);
}


if($mode == 'add'){
    // Если необходимо создать подкатегорию относительно выбранной
    if(isset($_REQUEST['add_child']) and $_REQUEST['add_child'] == "Y"){
        $object['o_parent_id'] = $_REQUEST['o_id'];
        $object['o_id_path'] .= "/" . $_REQUEST['o_id'];
        $view->assign('object', $object);
    }

    // Список для выпадающего списка
    list($objects_plain) = fn_uns__get_objects(array('plain' => true));
    $view->assign('objects_plain', $objects_plain);

}


if($mode == 'delete'){
    if(is__more_0($_REQUEST['o_id'])){
        fn_uns__del_object($_REQUEST['o_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}


// ПРОСМОТР ИСТОРИИ ДВИЖЕНИЯ
if ($mode == 'motion' and  defined('AJAX_REQUEST')){
    if (!is__more_0($_REQUEST['item_id'])){
        fn_set_notification('W', 'Ошибка запроса данных!', 'Обратитесь к администратору системы');
        return false;
    }
    $p = array(
        "o_id"          => array(8),  // Склад литья
        "item_type"     => "M",
        "typesize"      => "M",
        "item_id"       => $_REQUEST['item_id'],
    );

    if (!isset($_REQUEST['period'])) $_REQUEST['period'] = "M"; // Текущий месяц
    list ($_REQUEST['time_from'], $_REQUEST['time_to']) = fn_create_periods($_REQUEST);

    $p = array_merge($_REQUEST, $p);

    // ДВИЖЕНИЯ
    $motions = fn_uns__get_motions($p);
    $view->assign('motions', $motions);

    // ТИПЫ ДОКУМЕНТОВ
    list($document_types) = fn_uns__get_document_types();
    $view->assign('document_types', $document_types);

    $view->assign('params', $p);

}


function fn_foundry_get_balance__search($controller) {
    $params = array(
        'period',
        'time_from',
        'time_to',

        'item_type',
        'mclass_id',
        'mcat_id',
        'type_casting',
        'material_name',
        'material_no',
        'mode_report',
        'pump_id',
        'view_all_position',
        'accessory_pumps',
        'all_materials',
        'only_for_pumps',
    );
    fn_uns_search_set_get_params($controller, $params);
    return true;
}

