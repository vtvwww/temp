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


if ( !defined('AREA') )	{ die('Access denied');	}

fn_define('KEEP_UPLOADED_FILES', true);
fn_define('NEW_FEATURE_GROUP_ID', 'OG');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	fn_trusted_vars ('feature_data');

	// Update features
	if ($mode == 'update') {
		fn_update_product_feature($_REQUEST['feature_data'], $_REQUEST['feature_id'], DESCR_SL);
	}

	return array(CONTROLLER_STATUS_OK, "product_features.manage");
}

if ($mode == 'update') {

	$view->assign('feature', fn_get_product_feature_data($_REQUEST['feature_id'], false, false, DESCR_SL));
	list($group_features) = fn_get_product_features(array('feature_types' => 'G'), 0, DESCR_SL);
	$view->assign('group_features', $group_features);
	


} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['feature_id'])) {


		$feature_type = db_get_field("SELECT feature_type FROM ?:product_features WHERE feature_id = ?i", $_REQUEST['feature_id']);

		fn_delete_feature($_REQUEST['feature_id']);

		if ($feature_type == 'G' && defined('AJAX_REQUEST')) {
			$ajax->assign('force_redirection', "product_features.manage");
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT, "product_features.manage");

} elseif ($mode == 'manage') {

	$params = $_REQUEST;
	$params['exclude_group'] = true;
	$params['get_descriptions'] = true;
	list($features, $search, $has_ungroupped) = fn_get_product_features($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

	$view->assign('features', $features);
	$view->assign('search', $search);
	$view->assign('has_ungroupped', $has_ungroupped);

	if (empty($features) && defined('AJAX_REQUEST')) {
		$ajax->assign('force_redirection', "product_features.manage");
	}

	list($group_features) = fn_get_product_features(array('feature_types' => 'G'), 0, DESCR_SL);
	$view->assign('group_features', $group_features);

} elseif ($mode == 'get_feature_variants_list') {
	if (empty($_REQUEST['feature_id'])) {
		exit;
	}

	$pattern = !empty($_REQUEST['pattern']) ? $_REQUEST['pattern'] : '';
	$start = !empty($_REQUEST['start']) ? $_REQUEST['start'] : 0;
	$limit = (!empty($_REQUEST['limit']) ? $_REQUEST['limit'] : 10) + 1;

	$join = db_quote(" LEFT JOIN ?:product_feature_variant_descriptions ON ?:product_feature_variant_descriptions.variant_id = ?:product_feature_variants.variant_id AND ?:product_feature_variant_descriptions.lang_code = ?s", DESCR_SL);
	$condition = db_quote(" AND ?:product_feature_variants.feature_id = ?i", $_REQUEST['feature_id']);

	fn_set_hook('get_feature_variants_list', $condition, $join, $pattern, $start, $limit);

	$objects = db_get_hash_array("SELECT SQL_CALC_FOUND_ROWS ?:product_feature_variants.variant_id AS value, ?:product_feature_variant_descriptions.variant AS name FROM ?:product_feature_variants $join WHERE 1 $condition AND ?:product_feature_variant_descriptions.variant LIKE ?l LIMIT ?i, ?i", 'value', '%' . $pattern . '%', $start, $limit);

	if (defined('AJAX_REQUEST') && sizeof($objects) < $limit) {
		$ajax->assign('completed', true);
	} else {
		array_pop($objects);
	}
	
	if (!defined("COMPANY_ID") && (empty($_REQUEST['enter_other']) || !empty($_REQUEST['enter_other']) && $_REQUEST['enter_other'] != 'N')) {
		$total = db_get_found_rows();
		if ($start + $limit >= $total + 1) {
			$objects[] = array('value' => 'disable_select', 'name' => '-' . fn_get_lang_var('enter_other') . '-');
		}
	}
	
	if (!$start) {
		array_unshift($objects, array('value' => '', 'name' => '-' . fn_get_lang_var('none') . '-'));
	}

	$view->assign('objects', $objects);

	$view->assign('id', $_REQUEST['result_ids']);
	$view->display('common_templates/ajax_select_object.tpl');
	exit;

} elseif ($mode == 'get_variants') {
	list($variants, $total) = fn_get_product_feature_variants($_REQUEST['feature_id'], 0, $_REQUEST['feature_type'], true, DESCR_SL, Registry::get('settings.Appearance.admin_elements_per_page'));
	$feature_variants = $variants;
	$view->assign('feature_variants', $feature_variants);
	$view->assign('feature_type', $_REQUEST['feature_type']);
	$view->assign('id', $_REQUEST['feature_id']);
	$view->display('views/product_features/components/variants_list.tpl');
	exit;
} elseif ($mode == 'update_status') {

	fn_tools_update_status($_REQUEST);

	if (!empty($_REQUEST['status']) && $_REQUEST['status'] == 'D') {
		$filter_ids = db_get_fields("SELECT filter_id FROM ?:product_filters WHERE feature_id = ?i AND status = 'A'", $_REQUEST['id']);
		if (!empty($filter_ids)) {
			list($filters) = fn_get_product_filters();
			$filter_names_array = array();
			foreach ($filter_ids as $k => $filter_id) {
				$filter_names_array[] = $filters[$filter_id]['filter'];
			}

			db_query("UPDATE ?:product_filters SET status = 'D' WHERE filter_id IN (?n)", $filter_ids);

			fn_set_notification('W', fn_get_lang_var('warning'), str_replace(array('[url]', '[filters_list]'), array(fn_url('product_filters.manage'), implode(', ', $filter_names_array)), fn_get_lang_var('text_product_filters_were_disabled')));
		}
	}

	exit;
}

function fn_update_product_feature($feature_data, $feature_id, $lang_code = DESCR_SL)
{

	
	$deleted_variants = array();

	// If this feature belongs to the group, get categories assignment from this group
	if (!empty($feature_data['parent_id'])) {
		$gdata = db_get_row("SELECT categories_path, display_on_product, display_on_catalog FROM ?:product_features WHERE feature_id = ?i", $feature_data['parent_id']);
		$feature_data = fn_array_merge($feature_data, $gdata);
	}

	if (!intval($feature_id)) { // check for intval as we use "0G" for new group
		$feature_data['feature_id'] = $feature_id = db_query("INSERT INTO ?:product_features ?e", $feature_data);
		foreach (Registry::get('languages') as $feature_data['lang_code'] => $_d) {
			db_query("INSERT INTO ?:product_features_descriptions ?e", $feature_data);
		}
	} else {
		db_query("UPDATE ?:product_features SET ?u WHERE feature_id = ?i", $feature_data, $feature_id);
		db_query('UPDATE ?:product_features_descriptions SET ?u WHERE feature_id = ?i AND lang_code = ?s', $feature_data, $feature_id, $lang_code);
	}

	// If this feature is group, set its categories to all children
	if ($feature_data['feature_type'] == 'G') {
		$u = array(
			'categories_path' => $feature_data['categories_path'],
			'display_on_product' => $feature_data['display_on_product'],
			'display_on_catalog' => $feature_data['display_on_catalog'],
		);
		db_query("UPDATE ?:product_features SET ?u WHERE parent_id = ?i", $u, $feature_id);
	}

	// Delete variants for simple features
	if (strpos('SMNE', $feature_data['feature_type']) === false) {
		$var_ids = db_get_fields("SELECT variant_id FROM ?:product_feature_variants WHERE feature_id = ?i", $feature_id);
		if (!empty($var_ids)) {
			db_query("DELETE FROM ?:product_feature_variants WHERE variant_id IN (?n)", $var_ids);
			db_query("DELETE FROM ?:product_feature_variant_descriptions WHERE variant_id IN (?n)", $var_ids);
			db_query("DELETE FROM ?:product_features_values WHERE variant_id IN (?n)", $var_ids);
			foreach ($var_ids as $v_id) {
				fn_delete_image_pairs($v_id, 'feature_variant');
			}
		}

	} elseif (!empty($feature_data['variants'])) {
		$var_ids = array();
		
		foreach ($feature_data['variants'] as $k => $v) {
			if (empty($v['variant'])) {
				continue;
			}
			$v['feature_id'] = $feature_id;

			if (empty($v['variant_id'])) {
				$v['variant_id'] = db_query("INSERT INTO ?:product_feature_variants ?e", $v);
				foreach (Registry::get('languages') as $v['lang_code'] => $_v) {
					db_query("INSERT INTO ?:product_feature_variant_descriptions ?e", $v);
				}
			} else {
				db_query("UPDATE ?:product_feature_variants SET ?u WHERE variant_id = ?i", $v, $v['variant_id']);
				db_query("UPDATE ?:product_feature_variant_descriptions SET ?u WHERE variant_id = ?i AND lang_code = ?s", $v, $v['variant_id'], $lang_code);
			}

			if ($feature_data['feature_type'] == 'N') { // number
				db_query('UPDATE ?:product_features_values SET ?u WHERE variant_id = ?i AND lang_code = ?s', array('value_int' => $v['variant']), $v['variant_id'], $lang_code);
			}

			$var_ids[$k] = $v['variant_id'];
			$feature_data['variants'][$k]['variant_id'] = $v['variant_id']; // for addons
		}
		
		if (!empty($var_ids)) {
			fn_attach_image_pairs('variant_image', 'feature_variant', 0, $lang_code, $var_ids);
		}

		// Delete obsolete variants
		$original_var_ids = explode(',', $feature_data['original_var_ids']);
		$deleted_variants = array_diff($original_var_ids, $var_ids);
		if (!empty($deleted_variants)) {
			db_query("DELETE FROM ?:product_feature_variants WHERE variant_id IN (?n)", $deleted_variants);
			db_query("DELETE FROM ?:product_feature_variant_descriptions WHERE variant_id IN (?n)", $deleted_variants);
			db_query("DELETE FROM ?:product_features_values WHERE variant_id IN (?n)", $deleted_variants);
			foreach ($deleted_variants as $v_id) {
				fn_delete_image_pairs($v_id, 'feature_variant');
			}
		}
	}

	fn_set_hook('update_product_feature', $feature_data, $feature_id, $deleted_variants, $lang_code);

	return $feature_id;
}

?>
