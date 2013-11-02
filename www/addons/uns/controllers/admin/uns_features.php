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

if (!defined('AREA')) {
    die('Access denied');
}

fn_uns_defaul_functions($controller, $mode);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $suffix = '';
    if ($mode == 'update') {
        $id = fn_uns__upd_feature($_REQUEST['feature_id'], $_REQUEST['data']);
        if ($id === false) {
            fn_delete_notification('changes_saved');
        }else{
            fn_set_notification("N", $_REQUEST['data']['feature_name'], UNS_DATA_UPDATED);
            fn_delete_notification('changes_saved');
        }
        $suffix = "update&feature_id={$id}&selected_section={$_REQUEST['selected_section']}";
    }

    if ($mode == 'delete') {
        if (!empty($_REQUEST['feature_ids'])) {
            fn_uns__del_feature($_REQUEST['feature_ids']);
        }
        $suffix .= 'manage';
    }

    // ЗАПРОС СПИСКА ЕДИНИЦ ИЗМЕРЕНИЙ ПО ВЫБРАННОЙ ХАРАКТЕРИСТИКИ
    if ($mode == "get_units") {
        if (is__more_0($_REQUEST['feature_id'])) {
            list($units) = fn_uns__get_units(array('feature_id'=>$_REQUEST['feature_id'], 'sorting_schemas'=>'select'));
            $view->assign('f_type', 'select');
            $view->assign('f_options', $units);
            $view->assign('f_option_id', 'u_id');
            $view->assign('f_option_value', 'u_name');
            $view->assign('f_simple_2', true);
            $options = trim($view->display('addons/uns/views/components/get_form_field.tpl', false));
            $ajax->assign('options', $options);
        }
        exit;
    }
    return array(CONTROLLER_STATUS_OK, $controller . "." . $suffix);
}

if ($mode == 'manage' or $mode == 'update' or $mode == 'add'){
    fn_uns_add_sections($controller);

    list($unit_categories) = fn_uns__get_unit_categories();
    $view->assign('unit_categories', $unit_categories);
}

if ($mode == 'manage') {
    $p = array();
    $p = array_merge($_REQUEST, $p);
    list($features, $search) = fn_uns__get_features($p, UNS_ITEMS_PER_PAGE);
    $view->assign('features', $features);
    $view->assign('search', $search);
}

if ($mode == 'update') {
    if (!is__more_0($_REQUEST['feature_id'])) return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");

    $feature_id = $_REQUEST['feature_id'];
    $p = array(
        "feature_id" => $feature_id
    );
    list($feature) = fn_uns__get_features($p);
    $feature = array_shift($feature);
    $view->assign('feature', $feature);

    fn_add_breadcrumb(fn_get_lang_var('uns_features'), "uns_features.manage");
    fn_uns_navigation_tabs(array(
        'general' =>fn_get_lang_var('general'),
        )
    );
}

if ($mode == 'add') {
    fn_add_breadcrumb(fn_get_lang_var('uns_features'), "uns_features.manage");
    fn_uns_navigation_tabs(array(
        'general' =>fn_get_lang_var('general'),
        )
    );
}


if ($mode == 'delete') {
    if (is__more_0($_REQUEST['feature_id'])){
        fn_uns__del_feature($_REQUEST['feature_id']);
    }
    return array(CONTROLLER_STATUS_REDIRECT, $controller . ".manage");
}

