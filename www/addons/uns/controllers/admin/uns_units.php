<?php

if (!defined('AREA')) {
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $suffix = '';
    if ($mode == 'update') {
        $unit_id = fn_uns__update_unit($_REQUEST['unit_id'], $_REQUEST['unit_data']);
        if ($unit_id == false) {
            fn_delete_notification('changes_saved');
        }
        $suffix .= 'manage';
    }

    // DELETE SELECTED
    if ($mode == 'delete') {
        if (!empty($_REQUEST['unit_ids'])) {
            fn_uns__delete_unit($_REQUEST['unit_ids']);
        }
        $suffix .= 'manage';
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}

if ($mode == 'update') {
    // Отображение в попапе
    $temp = fn_uns__get_units_old($_REQUEST['u_id']);

    $unit = $temp[$_REQUEST['u_id']];
    $view->assign('uns_unit', $unit);

    $unit_categories = fn_uns__get_unit_categories_old();
    $view->assign('uns_unit_categories', $unit_categories);

    $tabs = array(
        'general_' . $_REQUEST['u_id'] => array(
            'title' => fn_get_lang_var('general'),
            'js' => true
        ),
    );
    Registry::set('navigation.tabs', $tabs);

} elseif ($mode == 'delete') {

    if (!empty($_REQUEST['u_id']) && is_numeric($_REQUEST['u_id']) && $_REQUEST['u_id']) {
        fn_uns__delete_unit($_REQUEST['u_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");

} elseif ($mode == 'manage') {
    // Отображение бокового меню
    fn_uns_add_sections($controller);

    // Запрос списка категорий единиц измерений
    $unit_categories = fn_uns__get_unit_categories_old();
    $view->assign('uns_unit_categories', $unit_categories);

    // Запрос списка единиц измерений
    $units = fn_uns__get_units_old();
    $view->assign('uns_units', $units);
}

?>