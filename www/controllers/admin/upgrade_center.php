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

fn_define('DIR_REPOSITORY', 'var/skins_repository/basic/');
$custom_skin_files = array(
	'customer/styles_ie.css',
	'customer/dropdown.css',
	'customer/styles.css',
	'manifest.ini'
);

$skip_files = array(
	'manifest.ini'
);

$backend_files = array(
	'admin_index' => 'admin.php',
	'vendor_index' => 'vendor.php',
);

$uc_settings = CSettings::instance()->get_values('Upgrade_center');

// If we're performing the update, check if upgrade center override controller is exist in the package
if (!empty($_SESSION['uc_package']) && file_exists(DIR_UPGRADE . $_SESSION['uc_package'] . '/uc_override.php')) {
	return include(DIR_UPGRADE . $_SESSION['uc_package'] . '/uc_override.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'update_settings') {
		if (!empty($_REQUEST['settings_data'])) {
			foreach ($_REQUEST['settings_data'] as $setting_name => $setting_value) {
				CSettings::instance()->update_value($setting_name, $setting_value, 'Upgrade_center');
			}
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT);
}

if ($mode == 'manage') {

	// Create directory structure
	fn_uc_create_structure();

	$view->assign('installed_upgrades', fn_uc_check_installed_upgrades());

	if (empty($uc_settings['license_number'])) {
		$view->assign('require_license_number', true);
	} else {
		$view->assign('packages', fn_uc_get_packages($uc_settings));
	}
	
	$view->assign('uc_settings', $uc_settings);

} elseif ($mode == 'refresh') {
	if (file_exists(DIR_UPGRADE . 'packages.xml') && false === fn_rm(DIR_UPGRADE . 'packages.xml')) {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_unable_to_remove_packages_xml'));
	}

	if (file_exists(DIR_UPGRADE . 'edition_packages.xml') && false === fn_rm(DIR_UPGRADE . 'edition_packages.xml')) {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_unable_to_remove_packages_xml'));
	}

	unset($_SESSION['uc_package']);
	unset($_SESSION['uc_base_package']);
	unset($_SESSION['uc_stages']);

	return array(CONTROLLER_STATUS_OK, !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "upgrade_center.manage");

} elseif ($mode == 'get_upgrade') {

	$package = fn_uc_get_package_details($_REQUEST['package_id']);
	if (fn_uc_get_package($_REQUEST['package_id'], $_REQUEST['md5'], $package, $uc_settings, $backend_files) == true) {
		$_SESSION['uc_package'] = $package['file'];
		$suffix = '.check';
	} else {
		unset($_SESSION['uc_package']);
		$suffix = '.manage';
	}

	return array(CONTROLLER_STATUS_OK, "upgrade_center" . $suffix);

} elseif ($mode == 'check') {

	if (empty($_SESSION['uc_package']) && empty($_SESSION['uc_base_package'])) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_upgrade_not_selected'));
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.manage");
	}

	fn_add_breadcrumb(fn_get_lang_var('upgrade_center'), "upgrade_center.manage");

	fn_set_store_mode('closed'); // close the store

	if (!empty($_SESSION['uc_base_package']) && is_file(DIR_UPGRADE .  $_SESSION['uc_base_package'] . '/packages_info.xml') || !empty($_SESSION['uc_package']) && is_file(DIR_UPGRADE .  $_SESSION['uc_package'] . '/packages_info.xml')) {
		if (empty($_SESSION['uc_base_package'])) {
			$_SESSION['uc_base_package'] = $_SESSION['uc_package'];
		}
		$packages_info = simplexml_load_file(DIR_UPGRADE . $_SESSION['uc_base_package'] . '/packages_info.xml', NULL, LIBXML_NOERROR);

		if (is_file(DIR_UPGRADE . $_SESSION['uc_base_package'] . '/stages.php')) {
			include(DIR_UPGRADE . $_SESSION['uc_base_package'] . '/stages.php');
		} else {
			$stages = array();
		}

		$i = 0;
		foreach ($packages_info->item as $item) {
			$i++;
			$stage = (string) $item;
			if (!empty($stages[$stage]) && $stages[$stage] == 'done') {
				continue;
			} else {
				$_stages['total'] = (string) $packages_info['count'];
				$_stages['stage_number'] = $i;
				$_stages['stage'] = $stage;
				$_stages['installed'] = $stages;
				$_SESSION['uc_stages'] = $_stages;
				$_SESSION['uc_package'] = $_SESSION['uc_base_package'] . '/' . $stage;
				break;
			}
		}

		if (!empty($_stages)) {
			$view->assign('stages', $_stages);
		}
	}

	$xml = simplexml_load_file(DIR_UPGRADE . $_SESSION['uc_package'] . '/uc.xml', NULL, LIBXML_NOERROR);

	if (empty($xml)) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_unable_to_parse_uc_xml'));
	} else {
		$hash_table = $result = array();

		// Get array with original files hashes
		if (isset($xml->original_files)) {
			foreach ($xml->original_files->item as $item) {
				$hash_table[(string)$item['file']] = (string)$item;
			}
		}

		fn_uc_ftp_connect($uc_settings);

		fn_uc_run_continuous_job('fn_uc_create_skins', array(DIR_UPGRADE . $_SESSION['uc_package'] . '/package', $_SESSION['uc_package'], $skip_files, $custom_skin_files));
		fn_uc_check_files(DIR_UPGRADE . $_SESSION['uc_package'] . '/package', $hash_table, $result, $_SESSION['uc_package'], $custom_skin_files);

		fn_uc_check_database_priviliges($result);

		$udata = $data = array();
		if (is_file(DIR_UPGRADE . 'installed_upgrades.php')) {
			include(DIR_UPGRADE . 'installed_upgrades.php');
		}

		if (!empty($result['changed'])) {
			foreach ($result['changed'] as $f) {
				$data[$f] = false;
			}
		}

		$udata[$_SESSION['uc_package']]['files'] = $data;
		$udata[$_SESSION['uc_package']]['not_installed'] = true;
		fn_uc_update_installed_upgrades($udata);
	}

	$view->assign('check_results', $result);

} elseif ($mode == 'run_backup') {

	if (empty($_SESSION['uc_package'])) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_upgrade_not_selected'));
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.manage");
	}

	$backup_details = array(
		'files' => array(),
		'tables' => array()
	);

	fn_uc_backup_backend_files($backend_files, DIR_UPGRADE . $_SESSION['uc_package']);

	fn_uc_run_continuous_job('fn_uc_backup_files', array(DIR_UPGRADE . $_SESSION['uc_package'] . '/package', DIR_ROOT, &$backup_details['files'], $_SESSION['uc_package']));
	$obsolete_files = fn_uc_run_continuous_job('fn_uc_backup_obsolete_files', array(DIR_UPGRADE . $_SESSION['uc_package'] . '/backup/', DIR_ROOT, DIR_UPGRADE . $_SESSION['uc_package'] . '/uc.xml'));
	$backup_details['files'] = array_merge($backup_details['files'], $obsolete_files);
	sort($backup_details['files']);
	
	$backup_details['tables'] = fn_uc_run_continuous_job('fn_uc_backup_database', array(DIR_UPGRADE . $_SESSION['uc_package']));
	
	if ($backup_details['tables'] === false) {
		return array(CONTROLLER_STATUS_OK, "upgrade_center.check");
	}

	$udata = array();
	if (is_file(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');
	} else {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_list_of_updates_missing'));
	}

	$udata[$_SESSION['uc_package']]['backup_details'] = $backup_details;
	$udata[$_SESSION['uc_package']]['not_installed'] = true;
	fn_uc_update_installed_upgrades($udata);

	return array(CONTROLLER_STATUS_OK, "upgrade_center.backup");


} elseif ($mode == 'backup') {

	if (empty($_SESSION['uc_package'])) {
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.manage");
	}

	if (is_file(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');
	} else {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_list_of_updates_missing'));
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.check");
	}

	// Put data to emergency restore script
	$c = fn_get_contents(DIR_UPGRADE . $_SESSION['uc_package'] . '/restore.php');

	$data = "\$uc_settings = " . var_export($uc_settings, true) . ";\n\n";
	$data .= "\$db = array (" . 
		"'db_host' => '" . Registry::get('config.db_host') . "'," . 
		"'db_user' => '" . Registry::get('config.db_user') . "'," . 
		"'db_password' => '" . Registry::get('config.db_password') . "'," . 
		"'db_name' => '" . Registry::get('config.db_name') . "'" .
		");\n\n";
	$data .= ("\$multistage = " . (!empty($_SESSION['uc_base_package']) ? 'true' : 'false') . ";\n\n");

	$data .= ("\$dir_compiled = '" . (defined('DIR_COMPILED') ? DIR_COMPILED : '') . "';\n\n");
	$data .= ("\$dir_cache = '" . (defined('DIR_CACHE') ? DIR_CACHE : '') . "';\n\n");

	$restore_key = md5(uniqid());
	$data .= "\$uak = '" . $restore_key . "';";

	$start = strpos($c, '//[params]') + strlen('//[params]') + 1;
	$end = strpos($c, '//[/params]') - 1;

	$c = substr_replace($c, $data, $start, $end - $start);
	fn_put_contents(DIR_UPGRADE . $_SESSION['uc_package'] . '/restore.php', $c, '', 0644);

	$view->assign('restore_key', $restore_key); 
	$view->assign('backup_details', $udata[$_SESSION['uc_package']]['backup_details']);

	if (!empty($_SESSION['uc_stages'])) {
		$view->assign('stages', $_SESSION['uc_stages']);
	}

} elseif ($mode == 'upgrade') {

	if (empty($_SESSION['uc_package'])) {
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.manage");
	}
	
	if (PRODUCT_VERSION == '2.2.3' && $_SESSION['uc_package'] == 'upgrade_2.2.3_' . fn_strtolower(PRODUCT_TYPE) . '-2.2.4_' . fn_strtolower(PRODUCT_TYPE) . '.tgz') {
		// we changed the session cookie name in 2.2.4.
		// So, we have to set session cookie with new name, otherwise admin will be logged out after upgrade.
		$session_cookie_params = session_get_cookie_params();
		if (version_compare(PHP_VERSION, '5.2.0', '<')) {
			@setcookie('sid_admin', Session::get_id(), TIME + $session_cookie_params['lifetime'], $session_cookie_params['path'], $session_cookie_params['domain'], $session_cookie_params['secure']);
		} else {
			@setcookie('sid_admin', Session::get_id(), TIME + $session_cookie_params['lifetime'], $session_cookie_params['path'], $session_cookie_params['domain'], $session_cookie_params['secure'], $session_cookie_params['httponly']);
		}
	}
	
	fn_uc_ftp_connect($uc_settings);
	fn_uc_run_continuous_job('fn_uc_copy_files', array(DIR_UPGRADE . $_SESSION['uc_package'] . '/package', DIR_ROOT));
	fn_uc_run_continuous_job('fn_uc_rm_files', array(DIR_ROOT, DIR_UPGRADE . $_SESSION['uc_package'] . '/uc.xml', 'deleted_files'));

	fn_uc_run_continuous_job('db_import_sql_file', array(DIR_UPGRADE . $_SESSION['uc_package'] . '/uc.sql', 16384, true, 1, true, true, true));
	fn_uc_run_continuous_job('fn_uc_post_upgrade', array(DIR_UPGRADE . $_SESSION['uc_package'], 'upgrade'));

	fn_uc_run_continuous_job('fn_uc_cleanup_cache', array($_SESSION['uc_package'], 'upgrade'));

	$udata = array();
	if (is_file(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');
	}

	$udata[$_SESSION['uc_package']]['not_installed'] = false;
	if (!empty($_SESSION['uc_stages'])) {
		$udata[$_SESSION['uc_package']]['stage'] = $_SESSION['uc_stages']['stage'];
		$udata[$_SESSION['uc_package']]['base_package'] = $_SESSION['uc_base_package'];
	}

	fn_uc_update_installed_upgrades($udata);

	if (!empty($_SESSION['uc_stages'])) {
		$_SESSION['uc_stages']['installed'][$_SESSION['uc_stages']['stage']] = 'done';
		if (!fn_put_contents(DIR_UPGRADE . $_SESSION['uc_base_package'] . '/stages.php', "<?php\n if ( !defined('AREA') )	{ die('Access denied');	}\n \$stages = " . var_export($_SESSION['uc_stages']['installed'], true) . ";\n?>")) {
			fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_unable_to_update_list_of_installed_upgrades'));
		}

		if ($_SESSION['uc_stages']['stage_number'] < $_SESSION['uc_stages']['total']) {
			$_SESSION['uc_package'] = $_SESSION['uc_base_package'];

			return array(CONTROLLER_STATUS_OK, 'upgrade_center.check');
		} else {
			unset($_SESSION['uc_package']);
			unset($_SESSION['uc_base_package']);
			unset($_SESSION['uc_stages']);

			return array(CONTROLLER_STATUS_OK, 'upgrade_center.summary');
		}
	} else {
		$package = $_SESSION['uc_package'];
		unset($_SESSION['uc_package']);

		return array(CONTROLLER_STATUS_OK, "upgrade_center.summary?package=" . $package);
	}

} elseif ($mode == 'revert') {
	
	if (PRODUCT_VERSION == '2.2.4' && $_REQUEST['package'] == 'upgrade_2.2.3_' . fn_strtolower(PRODUCT_TYPE) . '-2.2.4_' . fn_strtolower(PRODUCT_TYPE) . '.tgz') {
		// we changed the session cookie name in 2.2.4.
		// So, we have to set session cookie with old name, otherwise admin will be logged out after revert.
		$session_cookie_params = session_get_cookie_params();
		if (version_compare(PHP_VERSION, '5.2.0', '<')) {
			@setcookie('sess_id', Session::get_id(), TIME + $session_cookie_params['lifetime'], $session_cookie_params['path'], $session_cookie_params['domain'], $session_cookie_params['secure']);
		} else {
			@setcookie('sess_id', Session::get_id(), TIME + $session_cookie_params['lifetime'], $session_cookie_params['path'], $session_cookie_params['domain'], $session_cookie_params['secure'], $session_cookie_params['httponly']);
		}
	}
	
	fn_uc_ftp_connect($uc_settings);
	fn_uc_run_continuous_job('fn_uc_copy_files', array(DIR_UPGRADE . $_REQUEST['package'] . '/backup', DIR_ROOT));
	@fn_uc_rm(DIR_ROOT . '/uc.sql');
	fn_uc_run_continuous_job('fn_uc_rm_files', array(DIR_ROOT, DIR_UPGRADE . $_REQUEST['package'] . '/uc.xml', 'new_files'));

	fn_uc_run_continuous_job('db_import_sql_file', array(DIR_UPGRADE . $_REQUEST['package'] . '/backup/uc.sql', 16384, true, 1, true, false, true));
	fn_uc_run_continuous_job('fn_uc_post_upgrade', array(DIR_UPGRADE . $_REQUEST['package'], 'revert'));

	if (is_file(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');

		if (isset($udata[$_REQUEST['package']])) {
			if (isset($udata[$_REQUEST['package']]['stage']) && isset($udata[$_REQUEST['package']]['base_package']) && is_file(DIR_UPGRADE . $udata[$_REQUEST['package']]['base_package'] . '/stages.php')) {
				include(DIR_UPGRADE . $udata[$_REQUEST['package']]['base_package'] . '/stages.php');
				if (!empty($stages)) {
					unset($stages[$udata[$_REQUEST['package']]['stage']]);
					if (!fn_put_contents(DIR_UPGRADE . $udata[$_REQUEST['package']]['base_package'] . '/stages.php', "<?php\n if ( !defined('AREA') )	{ die('Access denied');	}\n \$stages = " . var_export($stages, true) . ";\n?>")) {
						fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_unable_to_update_list_of_installed_upgrades'));
					}
				}
			}
			unset($udata[$_REQUEST['package']]);
		}

		if (!empty($udata)) {
			fn_uc_update_installed_upgrades($udata);
		} else {
			fn_rm(DIR_UPGRADE . 'installed_upgrades.php');
		}
	} else {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_list_of_updates_missing'));
	}

	fn_rm(DIR_UPGRADE . 'packages.xml'); // cleanup packages list
	fn_rm(DIR_UPGRADE . 'edition_packages.xml'); // cleanup packages list
	fn_uc_run_continuous_job('fn_uc_cleanup_cache', array($_REQUEST['package'], 'revert'));

	fn_set_notification('W', fn_get_lang_var('important'), fn_get_lang_var('text_uc_upgrade_reverted'));

	return array(CONTROLLER_STATUS_OK, "upgrade_center.manage");


} elseif ($mode == 'summary') {

	$new_license = fn_uc_get_new_license_data();
	if (!empty($new_license['new_license']) && !empty($new_license['new_license_to']) && $new_license['new_license_to'] == PRODUCT_TYPE) {
		CSettings::instance()->update_value( 'license_number',  $new_license['new_license'], 'Upgrade_center');
		fn_uc_save_new_license_data(array('new_license_package' => '', 'new_license' => '', 'new_license_from' => '', 'new_license_to' => ''));
	}

	fn_rm(DIR_UPGRADE . 'packages.xml'); // cleanup packages list

} elseif ($mode == 'installed_upgrades') {

	fn_add_breadcrumb(fn_get_lang_var('upgrade_center'), "upgrade_center.manage");

	$udata = array();
	if (is_file(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');
	} else {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_list_of_updates_missing'));
	}

	$packages = array();
	foreach ($udata as $pkg => $f) {
		if (!empty($f['not_installed'])) {
			continue;
		}
		
		$details = array();
		if (file_exists(DIR_UPGRADE . $pkg . '/package_details.php')) {
			$details = include(DIR_UPGRADE . $pkg . '/package_details.php');
		}
		$packages[$pkg] = array(
			'details' => $details,
			'files' => $f['files']
		);
	}

	if (empty($packages)) {
		return array(CONTROLLER_STATUS_REDIRECT, "upgrade_center.manage");
	}

	$view->assign('packages', $packages);

} elseif ($mode == 'diff') {

	fn_add_breadcrumb(fn_get_lang_var('upgrade_center'), "upgrade_center.manage");
	fn_add_breadcrumb(fn_get_lang_var('installed_upgrades'), "upgrade_center.installed_upgrades");

	$view->assign('diff', fn_text_diff(fn_get_contents(DIR_UPGRADE . $_REQUEST['package'] . '/backup/' . $_REQUEST['file']), fn_get_contents(DIR_ROOT . '/' . $_REQUEST['file'])));

} elseif ($mode == 'conflicts') {

	if (is_file(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');

		if (isset($udata[$_REQUEST['package']]['files'][$_REQUEST['file']])) {
			$udata[$_REQUEST['package']]['files'][$_REQUEST['file']] = ($action == 'mark') ? true : false;

			fn_uc_update_installed_upgrades($udata);
		}
	} else {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_list_of_updates_missing'));
	}

	return array(CONTROLLER_STATUS_OK, "upgrade_center.installed_upgrades");

} elseif ($mode == 'remove') {

	if (!empty($_REQUEST['package'])) {
		if (is_file(DIR_UPGRADE . 'installed_upgrades.php')) {
			include(DIR_UPGRADE . 'installed_upgrades.php');
		} else {
			fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_list_of_updates_missing'));
		}

		$delete_dirs = array();
		foreach ($udata as $dir => $v) {
			$delete_dirs[] = $dir;
			if ($dir == $_REQUEST['package']) {
				break;
			}
		}

		if (!empty($delete_dirs)) {
			foreach ($delete_dirs as $dir) {
				fn_rm(DIR_UPGRADE . $dir, true);

				if (!empty($udata[$dir])) {
					if (isset($udata[$dir]['base_package']) && !fn_get_dir_contents(DIR_UPGRADE . $udata[$dir]['base_package'], true, false)) {
						fn_rm(DIR_UPGRADE . $udata[$dir]['base_package'], true);
					}
					unset($udata[$dir]);
				}
			}
		}

		if (!empty($udata)) {
			fn_uc_update_installed_upgrades($udata);
		} else {
			fn_rm(DIR_UPGRADE . 'installed_upgrades.php');
		}
	}

	return array(CONTROLLER_STATUS_OK, "upgrade_center.installed_upgrades");

} elseif ($mode == 'manage_editions') {

	// Create directory structure
	fn_uc_create_structure();

	$view->assign('installed_upgrades', fn_uc_check_installed_upgrades());

	if (empty($uc_settings['license_number'])) {
		$view->assign('require_license_number', true);
	} else {
		$view->assign('packages', fn_uc_get_edition_update_packages($uc_settings));
		$new_license_data = fn_uc_get_new_license_data();
		if (!empty($new_license_data['new_license_package']) && !is_dir(DIR_UPGRADE . $new_license_data['new_license_package'])) {
			unset($new_license_data['new_license_package']);
			fn_uc_save_new_license_data(array('new_license_package' => ''));
		}
		$view->assign('new_license_data', $new_license_data);
	}

	$view->assign('uc_settings', $uc_settings);

} elseif ($mode == 'check_edition_license') {
	
	$new_license = !empty($_REQUEST['new_license']) ? $_REQUEST['new_license'] : '';
	$new_license_from = !empty($_REQUEST['new_license_from']) ? $_REQUEST['new_license_from'] : '';
	$new_license_to = !empty($_REQUEST['new_license_to']) ? $_REQUEST['new_license_to'] : '';

	if (!empty($new_license)) {
		$result = fn_uc_check_edition_license($new_license, $uc_settings);

		$xml = @simplexml_load_string($result);

		if (isset($xml->errors)) {
			foreach ($xml->errors->item as $error) {
				fn_set_notification('E', fn_get_lang_var('error'), (string) $error);
			}
		}

		$is_license_valid = (isset($xml->response->license) && (string)$xml->response->license == 'VALID');
		$is_license_expired = (isset($xml->response->license) && (string)$xml->response->license == 'EXPIRED');
		$is_package_available = (isset($xml->response->package) && (string)$xml->response->package == 'AVAILABLE');

		if ($is_license_valid || $is_license_expired) {
			fn_uc_save_new_license_data(array('new_license_package' => '', 'new_license' => $new_license, 'new_license_from' => $new_license_from, 'new_license_to' => $new_license_to));
		}

		if ($is_license_expired) {
			fn_set_notification('E', fn_get_lang_var('error'), str_replace('[url]', Registry::get('config.updates_server'), fn_get_lang_var('update_period_expired')));
		}

		if ($is_package_available) {
			$package = fn_uc_get_edition_package_details($_REQUEST['package_id']);

			$package['file'] = fn_strtolower('upgrade_' . PRODUCT_VERSION . '_' . PRODUCT_TYPE . '-' . PRODUCT_VERSION . '_' . $new_license_to . '.tgz');
			$package['to_version'] = PRODUCT_VERSION . '_' . $new_license_to;

		  if (fn_uc_get_edition_package($new_license, $package, $uc_settings, $backend_files) == true) {
				$_SESSION['uc_package'] = $package['file'];
				fn_uc_save_new_license_data(array('new_license_package' => $package['file']));
				$suffix = '.check';
			} else {
				unset($_SESSION['uc_base_package']);
				unset($_SESSION['uc_package']);
				unset($_SESSION['uc_stages']);
				$suffix = '.manage_editions';
			}
		} else {
			$suffix = '.manage_editions';
		}
	} else {
		$suffix = '.manage_editions';
	}

	return array(CONTROLLER_STATUS_OK, "upgrade_center" . $suffix);
}

function fn_uc_get_package_contents_from_uc_xml($path)
{
	$contents = array();

	$xml = simplexml_load_file($path . '/uc.xml', NULL, LIBXML_NOERROR);

	if (!empty($xml)) {
		if (isset($xml->original_files)) {
			foreach ($xml->original_files->item as $item) {
				$contents[] = (string)$item['file'];
			}
		}

		if (isset($xml->new_files)) {
			foreach ($xml->new_files->item as $item) {
				$contents[] = (string)$item['file'];
			}
		}
	}

	return $contents;
}

function fn_uc_get_new_license_data()
{
	$settings = CSettings::instance()->get_values('Upgrade_center');
	return $settings;
}

function fn_uc_save_new_license_data($new_license_data)
{
	foreach ($new_license_data as $setting_name => $setting_value) {                        
		$data = array(
			'name' => $setting_name,
			'value' => $setting_value
		);

		$_id = CSettings::instance()->get_id($setting_name, 'Upgrade_center');
		if (!empty($_id)) {
			$data['object_id'] = $_id;
		}

		CSettings::instance()->update($data);
	}
}

function fn_uc_check_edition_license($new_license, $uc_settings)
{
	$data = fn_get_contents(Registry::get('config.updates_server') . '/index.php?dispatch=product_updates.check_edition&ver=' . PRODUCT_VERSION . '&new_license_number=' . $new_license . '&license_number=' . $uc_settings['license_number']);

	return $data;
}

function fn_uc_check_database_priviliges(&$result)
{
	return true;
	$result['no_db_rights'] = false;

	$table = '?:mysql_priviliges_test';
	$table_exists = db_get_row("SHOW TABLES LIKE '$table'");

	$skip_errors = Registry::get('runtime.database.skip_errors');
	Registry::set('runtime.database.skip_errors', true);

	if ($table_exists) {
		db_query("DROP TABLE $table");
		$drop_check = db_get_row("SHOW TABLES LIKE '$table'");
		if ($drop_check) {
			$result['no_db_rights']['drop'] = 'DROP';
			Registry::set('runtime.database.skip_errors', $skip_errors);
			return false;
		}
	}

	db_query("CREATE TABLE $table (`test` INT NOT NULL) ENGINE = MYISAM");
	$table_exists = db_get_row("SHOW TABLES LIKE '$table'");
	if (!$table_exists) {
		$result['no_db_rights']['create'] = 'CREATE';
		Registry::set('runtime.database.skip_errors', $skip_errors);
		return false;
	}

	db_query("ALTER TABLE $table CHANGE `test` `test1` INT( 11 ) NOT NULL");
	$column_changed = db_get_row("SHOW COLUMNS FROM $table WHERE field LIKE 'test1'");
	if (!$column_changed) {
		$result['no_db_rights']['alter'] = 'ALTER';
		Registry::set('runtime.database.skip_errors', $skip_errors);
		return false;
	}

	db_query("DROP TABLE $table");
	if (!isset($drop_check)) {
		$table_exists = db_get_row("SHOW TABLES LIKE '$table'");
		if ($table_exists) {
			$result['no_db_rights']['drop'] = 'DROP';
			Registry::set('runtime.database.skip_errors', $skip_errors);
			return false;
		}
	}

	Registry::set('runtime.database.skip_errors', $skip_errors);
	return true;
}

function fn_uc_rename_backend_files($backend_files, $dir)
{
	foreach ($backend_files as $key => $backend) {
		$new_name = Registry::get("config.$key");
		if (is_file($dir . $backend) && !empty($new_name)) {
			fn_rename($dir . $backend, $dir . $new_name);
		}
	}
}

function fn_uc_backup_backend_files($backend_files, $dir)
{
	foreach ($backend_files as $key => $backend) {
		if (is_file($dir . '/package/' . $backend) && is_file(DIR_ROOT . '/' . $backend)) {
			fn_uc_copy(DIR_ROOT . "/$backend", $dir . "/backup/$backend");
		}
	}
}

/**
 * Get upgrade packages list
 *
 * @param array $uc_settings Upgrade center settings
 * @return array packages list
 */
function fn_uc_get_packages($uc_settings)
{
	$result = array();

	// Cache packages list
	if (!file_exists(DIR_UPGRADE . 'packages.xml') || filemtime(DIR_UPGRADE . 'packages.xml') < (TIME - 60 * 60 * 24)) {
		$data = fn_get_contents(Registry::get('config.updates_server') . '/index.php?dispatch=product_updates.get_available&ver=' . PRODUCT_VERSION . '&edition=' . PRODUCT_TYPE . '&license_number=' . $uc_settings['license_number']);
		fn_put_contents(DIR_UPGRADE . 'packages.xml', $data);
	} else {
		$data = fn_get_contents(DIR_UPGRADE . 'packages.xml');
	}

	if (!empty($data)) {
		$xml = simplexml_load_string($data, NULL, LIBXML_NOERROR);
		if (!empty($xml)) {
			// Get array with original files hashes
			if (isset($xml->packages)) {
				foreach ($xml->packages->item as $package) {

					$c = array();
					if (isset($package->contents)) {
						foreach ($package->contents->item as $item) {
							$c[] = str_replace('package/', '', (string)$item);
						}
					}

					$result[] = array(
						'md5' => (string)$package->file['md5'],
						'package_id' => (string)$package['id'],
						'file' => (string)$package->file,
						'name' => (string)$package->name,
						'timestamp' => (string)$package->timestamp,
						'description' => (string)$package->description,
						'from_version' => (string)$package->from_version,
						'to_version' => (string)$package->to_version,
						'size' => (string)$package->size,
						'is_avail' => (string)$package->is_avail,
						'purchase_time_limit' => (string)$package->purchase_time_limit,
						'contents' => $c
					);
				}
			}

			if (isset($xml->errors)) {
				foreach ($xml->errors->item as $error) {
					fn_set_notification('E', fn_get_lang_var('error'), (string)$error);
				}
				fn_rm(DIR_UPGRADE . 'packages.xml'); // if we have errors, do not cache server response
			}
		}
	}

	return $result;
}

function fn_uc_get_edition_update_packages($uc_settings)
{
	$result = array();

	// Cache packages list
	if (!file_exists(DIR_UPGRADE . 'edition_packages.xml') || filemtime(DIR_UPGRADE . 'edition_packages.xml') < (TIME - 60 * 60 * 24)) {
		$data = fn_get_contents(Registry::get('config.updates_server') . '/index.php?dispatch=product_updates.get_editions&ver=' . PRODUCT_VERSION . '&license_number=' . $uc_settings['license_number']);
		fn_put_contents(DIR_UPGRADE . 'edition_packages.xml', $data);
	} else {
		$data = fn_get_contents(DIR_UPGRADE . 'edition_packages.xml');
	}

	if (!empty($data)) {
		$xml = simplexml_load_string($data, NULL, LIBXML_NOERROR);
		if (!empty($xml)) {
			// Get array with original files hashes
			if (isset($xml->packages)) {
				foreach ($xml->packages->item as $package) {

					$c = array();
					if (isset($package->contents)) {
						foreach ($package->contents->item as $item) {
							$c[] = str_replace('package/', '', (string)$item);
						}
					}

					$result[] = array(
						'package_id' => (string) $package['id'],
						'name' => (string) $package->name,
						'timestamp' => (string) $package->timestamp,
						'size' => (string) $package->size,
						'description' => (string) $package->description,
						'from_version' => (string) $package->from_version,
						'to_version' => (string) $package->to_version,
						'from_edition' => (string) $package->from_edition,
						'to_edition' => (string) $package->to_edition,
						'is_avail' => (string) $package->is_avail,
						'purchase_time_limit' => (string) $package->purchase_time_limit,
					);
				}
			}

			if (isset($xml->errors)) {
				foreach ($xml->errors->item as $error) {
					fn_set_notification('E', fn_get_lang_var('error'), (string)$error);
				}
				fn_rm(DIR_UPGRADE . 'edition_packages.xml'); // if we have errors, do not cache server response
			}
		}
	}

	return $result;
}

/**
 * Get upgrade package details
 *
 * @param int $package_id package ID
 * @return array package details
 */
function fn_uc_get_package_details($package_id)
{
	$result = array();

	$data = fn_get_contents(DIR_UPGRADE . 'packages.xml');
	if (!empty($data)) {
		$xml = simplexml_load_string($data, NULL, LIBXML_NOERROR);
		if (!empty($xml)) {
			// Get array with original files hashes
			if (isset($xml->packages)) {
				foreach ($xml->packages->item as $p) {
					if ((string)$p['id'] == $package_id) {
						$result = array(
							'md5' => (string)$p->file['md5'],
							'package_id' => (string)$p['id'],
							'file' => (string)$p->file,
							'name' => (string)$p->name,
							'description' => (string)$p->description,
							'timestamp' => (string)$p->timestamp,
							'size' => (string)$p->size,
							'is_avail' => (string)$p->is_avail,
							'purchase_time_limit' => (string)$p->purchase_time_limit,
							'from_version' => (string)$p->from_version,
							'to_version' => (string)$p->to_version,
						);

						if (isset($p->contents)) {
							foreach ($p->contents->item as $item) {
								$result['contents'][] = (string)$item;
							}
						}

						break;
					}
				}
			}
		}
	}

	return $result;
}

function fn_uc_get_edition_package_details($package_id)
{
	$result = array();

	$data = fn_get_contents(DIR_UPGRADE . 'edition_packages.xml');
	if (!empty($data)) {
		$xml = simplexml_load_string($data, NULL, LIBXML_NOERROR);
		if (!empty($xml)) {
			// Get array with original files hashes
			if (isset($xml->packages)) {
				foreach ($xml->packages->item as $p) {
					if ((string)$p['id'] == $package_id) {
						$result = array(
							'md5' => (string)$p->md5,
							'package_id' => (string)$p['id'],
							'name' => (string)$p->name,
							'description' => (string)$p->description,
							'timestamp' => (string)$p->timestamp,
							'size' => (string)$p->size,
							'from_version' => (string)$p->from_version,
							'to_version' => (string)$p->to_version,
							'from_edition' => (string)$p->from_edition,
							'to_edition' => (string)$p->to_edition,
						);

						break;
					}
				}
			}
		}
	}

	return $result;
}

/**
 * Get upgrade package
 *
 * @param int $package_id package ID
 * @param string $md5 md5 hash of package
 * @param array $package package details
 * @param array $uc_settings Upgrade center settings
 * @return boolean true if package downloaded and extracted successfully, false - otherwise
 */
function fn_uc_get_package($package_id, $md5, $package, $uc_settings, $backend_files)
{
	$result = true;

	if ($package['is_avail'] != 'Y'){
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_package_not_available'));
		return false;
	}

	$data = fn_get_contents(Registry::get('config.updates_server') . '/index.php?dispatch=product_updates.get_package&package_id=' . $package_id . '&edition=' . PRODUCT_TYPE . '&license_number=' . $uc_settings['license_number']);
	if (!empty($data)) {

		fn_put_contents(DIR_UPGRADE . 'uc.tgz', $data);

		if (md5_file(DIR_UPGRADE . 'uc.tgz') == $md5) {
			$dir = basename($package['file']);
			fn_mkdir(DIR_UPGRADE . $dir);
			fn_put_contents(DIR_UPGRADE . $dir . '/package_details.php', "<?php\n return " . var_export($package, true) . "; \n?>");

			$res = fn_decompress_files(DIR_UPGRADE . 'uc.tgz', DIR_UPGRADE . $dir);

			if ($res) {
				fn_uc_rename_backend_files($backend_files, DIR_UPGRADE . $dir . '/package/');
			} else {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_failed_to decompress_files'));
			}

			return $res;
		} else {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_broken_package'));
			$result = false;
		}
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_cant_download_package'));
		$result = false;
	}

	return $result;
}

function fn_uc_get_edition_package($new_license, $package, $uc_settings, $backend_files)
{
	$result = true;

	$data = fn_get_contents(Registry::get('config.updates_server') . '/index.php?dispatch=product_updates.get_edition_upgrade&license_number=' . $uc_settings['license_number'] . '&new_license_number=' . $new_license . '&ver=' . PRODUCT_VERSION);

	if (!empty($data)) {

		fn_put_contents(DIR_UPGRADE . 'uc.tgz', $data);

		if (md5_file(DIR_UPGRADE . 'uc.tgz') == $package['md5']) {
			$dir = basename($package['file']);
			fn_mkdir(DIR_UPGRADE . $dir);

			$result = fn_decompress_files(DIR_UPGRADE . 'uc.tgz', DIR_UPGRADE . $dir);

			if ($result) {
				if (is_file(DIR_UPGRADE . $dir . '/packages_info.xml')) {
					// if multipackage archive
					$packages = simplexml_load_file(DIR_UPGRADE . $dir . '/packages_info.xml', NULL, LIBXML_NOERROR);
					foreach ($packages->item as $item) {
						$filename = (string) $item;
						fn_uc_rename_backend_files($backend_files, DIR_UPGRADE . "$dir/$filename/package/");
						$_package = $package;
						$_package = array(
							'to_version' => substr($filename, strrpos($filename, '-') + 1),
							'name' => str_replace('upgrade_', '', $filename),
							'description' => '',
							'size' => '',
						);
						$_package['contents'] = fn_uc_get_package_contents_from_uc_xml(DIR_UPGRADE . "$dir/$filename");
						fn_put_contents(DIR_UPGRADE . "$dir/$filename/package_details.php", "<?php\n return " . var_export($_package, true) . "; \n?>");
					}
				} else {
					fn_uc_rename_backend_files($backend_files, DIR_UPGRADE . $dir . '/package/');
					$package['contents'] = fn_uc_get_package_contents_from_uc_xml(DIR_UPGRADE . $dir);
					fn_put_contents(DIR_UPGRADE . $dir . '/package_details.php', "<?php\n return " . var_export($package, true) . "; \n?>");
				}
			} else {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_failed_to decompress_files'));
			}

		} else {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_broken_package'));
			$result = false;
		}

		return $result;
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_cant_download_package'));
		$result = false;
	}

	return $result;
}

/**
 * Check if files can be upgraded
 *
 * @param string $path files path
 * @param array $hash_table table with hashes of original files
 * @param array $result resulting array
 * @param string $package package to check files from
 * @param array $custom_skin_files list of custom skin files
 * @return boolean always true
 */
function fn_uc_check_files($path, $hash_table, &$result, $package, $custom_skin_files)
{
	// Simple copy for a file
    if (is_file($path)) {
		// Get original file name
		$original_file = str_replace(DIR_UPGRADE . $package . '/package/', DIR_ROOT . '/', $path);
		$relative_file = str_replace(DIR_ROOT . '/', '', $original_file);
		$file_name = basename($original_file);

		if (file_exists($original_file)) {
			if (md5_file($original_file) != md5_file($path)) {

				$_relative_file = $relative_file;
				// For skins, convert relative path to skins_repository
				if (strpos($relative_file, 'skins/') === 0) {
					$_relative_file = preg_replace('/skins\/[\w]+\//', 'var/skins_repository/basic/', $relative_file);

					// replace all skins except basic
					if (fn_uc_check_array_value($relative_file, $custom_skin_files) && strpos($relative_file, '/basic/') === false) {
						$_relative_file = preg_replace('/skins\/([\w]+)\//', 'var/skins_repository/${1}/', $relative_file);
					}
				}

				if (!empty($hash_table[$_relative_file])) {
					if (md5_file($original_file) != $hash_table[$_relative_file]) {
						$result['changed'][] = $relative_file;
					}
				} else {
					$result['changed'][] = $relative_file;
				}
			}
		} else {
			$result['new'][] = $relative_file;
		}

		$status = fn_uc_is_writable($original_file, true);
		if ($status['result'] == false) {
			$result['non_writable'][] = $relative_file;
		}

		if ($status['no_ftp'] == true) {
			$result['no_ftp'] = true;
		}

		return true;
    }

	if (is_dir($path)) {
		$dir = dir($path);
		while (false !== ($entry = $dir->read())) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			fn_uc_check_files(rtrim($path, '/') . '/' . $entry, $hash_table, $result, $package, $custom_skin_files);
		}
		// Clean up
		$dir->close();
		return true;
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_incorrect_upgrade_path'));
		return false;
	}
}

/**
 * Check if file is writable
 *
 * @param string $path file path
 * @param boolean $extended return extended status
 * @return boolean true if file is writable, false - otherwise
 */
function fn_uc_is_writable($path, $extended = false)
{
	$result = false;
	$extended_result = array(
		'result' => false,
		'no_ftp' => false,
		'method' => ''
	);

	// File does not exist, check if directory is writable
	if (!file_exists($path)) {
		$a = explode('/', $path);
		do {
			array_pop($a);
		} while (!is_dir(implode('/', $a)));

		$path = implode('/', $a);
	}

	// Check if file can be written using php
	if (!fn_uc_is_writable_dest($path)) {
		$result = fn_uc_ftp_is_writable($path);
		if ($result == false) {
			$ftp = Registry::get('uc_ftp');
			if (!is_resource($ftp)) {
				$extended_result['no_ftp'] = true;
			}
		} else {
		    $extended_result['method'] = 'ftp';
		}
	} else {
		$result = true;
		$extended_result['method'] = 'fs';
	}

	$extended_result['result'] = $result;

	return ($extended) ? $extended_result : $result;
}

/**
 * Create directory taking into account accessibility via php/ftp
 *
 * @param string $dir directory
 * @return boolean true if directory created successfully, false - otherwise
 */
function fn_uc_mkdir($dir)
{
	// Try to make directory using php
	$r = fn_uc_is_writable($dir, true);

	$result = $r['result'];
	if ($r['method'] == 'fs') {
		$result = fn_mkdir($dir);
	} elseif ($r['method'] == 'ftp') {
		$result = fn_uc_ftp_mkdir($dir);
	}

	if (!$result) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_failed_to_create_directory'));
	}

	return $result;
}

/**
 * Copy file taking into account accessibility via php/ftp
 *
 * @param string $source source file
 * @param string $dest destination file/directory
 * @return boolean true if directory copied correctly, false - otherwise
 */
function fn_uc_copy($source, $dest)
{
	$result = false;
	$file_name = basename($source);

	if (!file_exists($dest)) {
		if (basename($dest) == $file_name) { // if we're copying the file, create parent directory
			fn_uc_mkdir(dirname($dest));
		} else {
			fn_uc_mkdir($dest);
		}
	}

	fn_echo(' .');

	if (fn_uc_is_writable_dest($dest) || (fn_uc_is_writable_dest(dirname($dest)) && !file_exists($dest))) {
		if (is_dir($dest)) {
			$dest .= '/' . basename($source);
		}
		$result = copy($source, $dest);
		fn_uc_chmod_file($dest);
	}
	
	if (!$result) { // try ftp
		$result = fn_uc_ftp_copy($source, $dest);
	}

	if (!$result) {
		$msg = str_replace('[file]', $dest, fn_get_lang_var('cannot_write_file'));
		fn_set_notification('E', fn_get_lang_var('error'), $msg);
	}

	return $result;
}

function fn_uc_chmod_file($filename)
{
	$ext = fn_get_file_ext($filename);
	$perm = ($ext == 'php' ? 0644 : DEFAULT_FILE_PERMISSIONS);

	$result = @chmod($filename, $perm);

	if (!$result) {
		$ftp = Registry::get('uc_ftp');
		if (is_resource($ftp)) {
			$dest = dirname($filename);
			$dest = rtrim($dest, '/') . '/'; // force adding trailing slash to path

			$rel_path = str_replace(DIR_ROOT . '/', '', $dest);
			$cdir = ftp_pwd($ftp);

			if (empty($rel_path)) { // if rel_path is empty, assume it's root directory
				$rel_path = $cdir;
			}

			if (ftp_chdir($ftp, $rel_path)) {
				$result = @ftp_site($ftp, "CHMOD " . sprintf('0%o', $perm) . " " . basename($filename));
				ftp_chdir($ftp, $cdir);
			}
		}
	}

	return $result;
}

/**
 * Check if destination is writable
 *
 * @param string $dest destination file/directory
 * @return boolean true if writable, false - if not
 */
function fn_uc_is_writable_dest($dest)
{
	$dest = rtrim($dest, '/');

	if (is_file($dest)) {
		$f = @fopen($dest, 'ab');
		if ($f === false) {
			return false;
		}
		fclose($f);
	} elseif (is_dir($dest)) {
		if (!fn_put_contents($dest . '/zzzz.zz', '1')) {
			return false;
		}
		fn_rm($dest . '/zzzz.zz');
	} else {
		return false;
	}

	return true;
}

/**
 * Copy files from one directory to another
 *
 * @param string $source source directory
 * @param string $dest destination directory
 * @return boolean true if directory copied correctly, false - otherwise
 */
function fn_uc_copy_files($source, $dest)
{
	// Simple copy for a file
	if (is_file($source)) {
		return fn_uc_copy($source, $dest);
	}

	// Loop through the folder
	if (is_dir($source)) {
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep copy directories
			if ($dest !== $source . '/' . $entry) {
				if (fn_uc_copy_files(rtrim($source, '/') . '/' . $entry, $dest . '/' . $entry) == false) {
					return false;
				}
			}
		}

		// Clean up
		$dir->close();

		return true;
	} else {
		$msg = str_replace('[file]', $dest, fn_get_lang_var('cannot_write_file'));
		fn_set_notification('E', fn_get_lang_var('error'), $msg);
		return false;
	}
}

/**
 * Run post-upgrade script
 *
 * @param string $path directory with post-upgrade script
 * @param string $upgrade_type script execution type - "upgrade" or "revert"
 * @return boolean always true
 */
function fn_uc_post_upgrade($path, $upgrade_type)
{
	if (file_exists($path . '/uc.php')) {
		include($path . '/uc.php');
	}

	return true;
}

/**
 * Create directory structure for upgrade
 *
 * @return boolean true if structured created correctly, false - otherwise
 */
function fn_uc_create_structure()
{
	if (fn_mkdir(DIR_UPGRADE)) {
		return true;
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_unable_to_create_upgrade_folder'));
		return false;
	}
}

/**
 * Create directory structure for current active skins and copy templates there
 *
 * @param string $path path with skins repository
 * @param string $package package to create skins structure in
 * @param array $skip_files list of files that should not be copied to installed skins
 * @param array $custom_skin_files list of custom skin files
 * @return boolean true if structured created correctly, false - otherwise
 */
function fn_uc_create_skins($path, $package, $skip_files, $custom_skin_files, $log)
{
	static $installed_skins = array();
	if (empty($installed_skins)) {
		$installed_skins = fn_get_dir_contents(DIR_SKINS, true, false);
	}

	if (is_file($path)) {
		$files = array();
		if (strpos($path, DIR_REPOSITORY) !== false) {
			// customer skin
			if (strpos($path, DIR_REPOSITORY . 'customer/') !== false || strpos($path, DIR_REPOSITORY . 'mail/') !== false) {
				foreach ($installed_skins as $s) {
					$log->write('Processing skin: ' . $s, __FILE__, __LINE__);
					if (!fn_uc_check_array_value($path, $custom_skin_files) || $s = 'basic') { // copy non-custom files only
						$files[] = str_replace(DIR_UPGRADE . $package . '/package/' . DIR_REPOSITORY, DIR_UPGRADE . $package . '/package/skins/' . $s . '/', $path);
					}
				}
			// admin skin
			} else {
				$files[] = str_replace(DIR_UPGRADE . $package . '/package/' . DIR_REPOSITORY, DIR_UPGRADE . $package . '/package/skins/' . Registry::get('settings.skin_name_admin') . '/', $path);
			}

		// Copy data from alternative skins
		} elseif (strpos($path, 'var/skins_repository/' . Registry::get('settings.skin_name_customer')) !== false) {
			$files[] = str_replace(DIR_UPGRADE . $package . '/package/var/skins_repository/', DIR_UPGRADE . $package . '/package/skins/', $path);
		}

		foreach ($files as $file) {
			$fname = basename($file);
			if (!in_array($fname, $skip_files) && !(file_exists($file) && strpos($path, '/basic/') !== false)) {
				fn_mkdir(dirname($file));
				$log->write('Copying file: ' . $file, __FILE__, __LINE__);
				fn_copy($path, dirname($file));
			}
		}

		return true;
    }

	if (is_dir($path)) {
		$dir = dir($path);
		while (false !== ($entry = $dir->read())) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			fn_uc_create_skins(rtrim($path, '/') . '/' . $entry, $package, $skip_files, $custom_skin_files, $log);
		}
		// Clean up
		$dir->close();
		return true;
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_failed_to_create_skins'));
		return false;
	}
}
/**
 * Check if file is writable using ftp
 *
 * @param string $path file path
 * @return boolean true if file is writable, false - otherwise
 */
function fn_uc_ftp_is_writable($path)
{
	$result = false;
	// If ftp connection is available, check file/directory via ftp
	$ftp = Registry::get('uc_ftp');
	if (is_resource($ftp)) {
		$rel_path = ltrim(str_replace(DIR_ROOT, '', $path), '/');
		if (empty($rel_path)) {
			$rel_path = '.';
		}
		$ftp_path = (is_dir($path) || is_file($path)) ?  $rel_path : (dirname($rel_path));
		if (is_file($path)) {
			$perm = (fn_get_file_ext($path) == 'php') ? 0644 : DEFAULT_FILE_PERMISSIONS;
		} else {
			$perm = DEFAULT_DIR_PERMISSIONS;
		}
		$ftp_site_result = @ftp_site($ftp, 'CHMOD ' . sprintf('0%o', $perm) . ' ' . $ftp_path);
		if ($ftp_site_result) {
			$result = true;
		}
	}

	return $result;
}

/**
 * Copy file using ftp
 *
 * @param string $source source file
 * @param string $dest destination file/directory
 * @return boolean true if copied successfully, false - otherwise
 */
function fn_uc_ftp_copy($source, $dest)
{
	$result = false;

	$ftp = Registry::get('uc_ftp');
	if (is_resource($ftp)) {
		if (!is_dir($dest)) { // file
			$dest = dirname($dest);
		}
		$dest = rtrim($dest, '/') . '/'; // force adding trailing slash to path

		$rel_path = str_replace(DIR_ROOT . '/', '', $dest);
		$cdir = ftp_pwd($ftp);

		if (empty($rel_path)) { // if rel_path is empty, assume it's root directory
		    $rel_path = $cdir;
		}

		if (ftp_chdir($ftp, $rel_path) && ftp_put($ftp, basename($source), $source, FTP_BINARY)) {
			@ftp_site($ftp, "CHMOD " . (fn_get_file_ext($source) == 'php' ? '0644' : sprintf('0%o', DEFAULT_FILE_PERMISSIONS)) . " " . basename($source));
			$result = true;
			ftp_chdir($ftp, $cdir);
		}
	}

	if (false === $result) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_failed_to_ftp_copy'));
	}

	return $result;
}

/**
 * Create directory using ftp
 *
 * @param string $dir directory
 * @return boolean true if directory created successfully, false - otherwise
 */
function fn_uc_ftp_mkdir($dir)
{
	if (@is_dir($dir)) {
		return true;
	}

	$ftp = Registry::get('uc_ftp');
	if (!is_resource($ftp)) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_ftp_connection_failed'));
		return false;
	}

	$result = false;

	$rel_path = str_replace(DIR_ROOT . '/', '', $dir);
	$path = '';
	$dir_arr = array();
	if (strstr($rel_path, '/')) {
		$dir_arr = explode('/', $rel_path);
	} else {
		$dir_arr[] = $rel_path;
	}

	foreach ($dir_arr as $k => $v) {
		$path .= (empty($k) ? '' : '/') . $v;
		if (!@is_dir(DIR_ROOT . '/' . $path)) {
			if (ftp_mkdir($ftp, $path)) {
				$result = true;
			} else {
				$result = false;
				break;
			}
		} else {
			$result = true;
		}
	}

	if (false === $result) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_failed_to_ftp_mkdir'));
	}

	return $result;
}

/**
 * Connect to ftp server
 *
 * @param array $uc_settings upgrade center options
 * @return boolean true if connected successfully and working directory is correct, false - otherwise
 */
function fn_uc_ftp_connect($uc_settings)
{
	$result = true;

	if (function_exists('ftp_connect')) {
		if (!empty($uc_settings['ftp_hostname'])) {
			$ftp = ftp_connect($uc_settings['ftp_hostname']);
			if (!empty($ftp)) {
				if (@ftp_login($ftp, $uc_settings['ftp_username'], $uc_settings['ftp_password'])) {
					if (!empty($uc_settings['ftp_directory'])) {
						@ftp_chdir($ftp, $uc_settings['ftp_directory']);
					}

					$files = ftp_nlist($ftp, '.');
					if (!empty($files) && in_array('config.php', $files)) {
						Registry::set('uc_ftp', $ftp);
					} else {
						fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_ftp_cart_directory_not_found'));
						$result = false;					
					}
				} else {
					fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_ftp_login_failed'));
					$result = false;
				}
			} else {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_ftp_connect_failed'));
				$result = false;
			}
		}
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_no_ftp_module'));
		$result = false;
	}

	return $result;
}

/**
 * Backup database data which will be affected during upgrade
 *
 * @param string $path path to backup directory
 * @return array backed up tables list
 */
function fn_uc_backup_database($path, $log)
{
	$log->write('Backing up database: ' . $path, __FILE__, __LINE__);

	$tables = array();

	if (file_exists($path . '/uc.sql')) {

		$f = fopen($path . '/uc.sql', 'rb');
		if (!empty($f))  {
			while (!feof($f)) {
				$s = fgets($f);

				if (preg_match_all("/(INSERT INTO|REPLACE INTO|UPDATE|ALTER TABLE|RENAME TABLE|DELETE FROM|DROP TABLE|CREATE TABLE)( IF EXISTS| IF NOT EXISTS)? [`]?(\w+)[`]?/", $s, $m)) {
					$tables[$m[3][0]] = true;
				}
			}
			fclose($f);
		}
	}

	$tables = array_keys($tables);
	@fn_uc_rm($path . '/backup/uc.sql');
	@fn_uc_rm($path . '/db_backup_tables.txt');

	$bak_tables = fn_uc_backup_tables($tables, $path, $path . '/backup/uc.sql', $log);

	if (false === $bak_tables) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_failed_to_backup_tables'));
		return false;
	}

	return $bak_tables;
}

/**
 * Backup files
 *
 * @param string $source upgrade package directory
 * @param string $dest working directory
 * @param array $result resulting list of backed up files
 * @param string $package package to make backup for
 * @return boolean true if directory copied correctly, false - otherwise
 */
function fn_uc_backup_files($source, $dest, &$result, $package, $log)
{
	// Simple copy for a file
	if (is_file($source)) {
		$log->write('Backing up: ' . $source, __FILE__, __LINE__);
		return fn_uc_backup_file($source, $dest, $result, $package);
	}

	// Loop through the folder
	if (is_dir($source)) {
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			$log->write('Backing up: ' . $entry, __FILE__, __LINE__);
			// Deep backup directories
			if ($dest !== $source . '/' . $entry) {
				if (fn_uc_backup_files(rtrim($source, '/') . '/' . $entry, $dest . '/' . $entry, $result, $package, $log) == false) {
					return false;
				}
			}
		}

		// Clean up
		$dir->close();

		return true;
	} else {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_incorrect_upgrade_path'));
		return false;
	}
}

/**
 * Backup certain file
 *
 * @param string $source source file
 * @param string $dest destination file/directory
 * @param array $result resulting list of backed up files
 * @param string $package package to make backup for
 * @return string filename of backed up file
 */
function fn_uc_backup_file($source, $dest, &$result, $package)
{
	$file_name = basename($source);

	if (is_file($dest)) {
		fn_echo(' .');
		$relative_path = str_replace(DIR_ROOT . '/', '', $dest);
		fn_mkdir(dirname(DIR_UPGRADE . $package . '/backup/' . $relative_path));
		if (false === fn_copy($dest, DIR_UPGRADE . $package . '/backup/' . $relative_path)) {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_failed_to_copy_file'));
		}
		$result[] = $relative_path;
	}

	return true;
}

/**
 * Function backup obsolete files before deleting
 *
 * @param sting $dest Destanation directory
 * @param string $source Source directory
 * @param string $xml_file Path to xml file with list of files.
 */
function fn_uc_backup_obsolete_files($dest, $source, $xml_file, $log)
{
	$files_list = fn_uc_get_files_from_xml($xml_file, 'deleted_files');
	
	$skins_files = fn_uc_find_in_skins($files_list, $source);
	$files_list = array_merge($files_list, $skins_files);

	foreach ($files_list as $l) {
		$log->write('Backing up:' . $l, __FILE__, __LINE__);
		fn_echo(' .');
		fn_mkdir(dirname($dest . $l));
		fn_copy($source . '/' . $l, $dest . $l);
	}
	
	return $files_list;
}

/**
 * Function remove obsolete or new files after upgrade or reverting upgrade.
 *
 * @param string $source Source directory
 * @param string $xml_file Path to xml file with list of files.
 * @param string $section section of the xml file
 */
function fn_uc_rm_files($source, $xml_file, $section)
{
	$files_list = fn_uc_get_files_from_xml($xml_file, $section);
	
	$skins_files = fn_uc_find_in_skins($files_list, $source);
	$files_list = array_merge($files_list, $skins_files);
	
	foreach ($files_list as $file) {
		fn_uc_rm($source . '/' . $file);
	}
}

/**
 * Function finds files from basic skin in all installed skins and return full path to those files.
 *
 * @param array $files Array with relative name of the files
 * @param string $source Path to root folder
 * @return array Array of the copies of file from all installed skins.
 */
function fn_uc_find_in_skins($files, $source)
{
	$base_skin = 'var/skins_repository/basic/';
	$len = strlen($base_skin);
	$installed_skins = fn_get_dir_contents(DIR_SKINS);
	
	$result = array();
	foreach ($files as $file) {
		if (substr($file, 0, $len) == $base_skin) {
			foreach ($installed_skins as $skin_name) {
				$relative_name = "skins/$skin_name/" . substr($file, $len);
				if (file_exists($source . '/' . $relative_name)) {
					$result[] = $relative_name;	   
				}
			} 
		}
	}
	
	return $result;
}

/**
 * Function get list of files from package xml. This list will be used for deleting obsolete or new files after upgrading or reverting.
 *
 * @param string $xml_file Path to the xml file
 * @param string $section section of the xml file
 *
 * @return array list of files
 */
function fn_uc_get_files_from_xml($xml_file, $section)
{
	$xml = simplexml_load_file($xml_file, NULL, LIBXML_NOERROR);

	$result = array();
	
	if (empty($xml)) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_unable_to_parse_uc_xml'));
	} else {
		// Get files list
		if (isset($xml->$section)) {
			foreach ($xml->$section->item as $item) {
				$result[] = (string) $item['file'];
			}
		}
	}
	
	return $result;
}

/**
 * Check installed upgrades
 *
 * @return array array which indicates, if any upgrade has conflicts and if any upgrade exist
 */
function fn_uc_check_installed_upgrades()
{
	$result = array(
		'has_conflicts' => false,
		'has_upgrades' => false,
	);

	$upgrades = 0;

	if (is_file(DIR_UPGRADE . 'installed_upgrades.php')) {
		include(DIR_UPGRADE . 'installed_upgrades.php');

		foreach ($udata as $p => $f) {
			if (!empty($f['files']) && empty($f['not_installed'])) {
				foreach ($f['files'] as $_f => $_s) {
					if ($_s == false) {
						$result['has_conflicts'] = true;
						break;
					}
				}
			}
			if (empty($f['not_installed'])) {
				$upgrades++;
			}
		}

		$result['has_upgrades'] = $upgrades;
	}

	return $result;
}

function fn_uc_update_installed_upgrades($data)
{
	if (fn_put_contents(DIR_UPGRADE . 'installed_upgrades.php', "<?php\n if ( !defined('AREA') )	{ die('Access denied');	}\n \$udata = " . var_export($data, true) . ";\n?>")) {
		return true;
	} else {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_unable_to_update_list_of_installed_upgrades'));
		return false;
	}
}

/**
 * Cleanup upgrade cache
 *
 * @param string $package package name
 * @param string $type upgrade type (upgrade/revert)
 * @return boolean always true
 */
function fn_uc_cleanup_cache($package, $type)
{
	if ($type == 'upgrade') {
		@unlink(DIR_UPGRADE . $package . '/backup/uc.sql.tmp');
	} else {
		@unlink(DIR_UPGRADE . $package . '/uc.sql.tmp');
	}
}

/**
 * Check if array item exists in the string
 *
 * @param string $value string to search array item in
 * @param string $array items list
 * @return boolean true if value found, false - otherwise
 */
function fn_uc_check_array_value($value, $array)
{
	foreach ($array as $v) {
		if (strpos($value, $v) !== false) {
			return true;
		}
	}

	return false;
}

function fn_uc_rm($path)
{
	// Try to make directory using php
	$r = fn_uc_is_writable($path, true);

	$result = $r['result'];
	if ($r['method'] == 'fs') {
		$result = fn_rm($path);
	} elseif ($r['method'] == 'ftp') {
		$result = fn_uc_ftp_rm($path);
	}

	if (file_exists($path)) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_unable_to_remove_file') . ' ' . $path);
	}

	return $result;
}

function fn_uc_ftp_rm($path)
{
	$ftp = Registry::get('uc_ftp');
	if (is_resource($ftp)) {
		$rel_path = str_replace(DIR_ROOT . '/', '', $path);
		if (is_file($path)) {
			return @ftp_delete($ftp, $rel_path);
		}

		// Loop through the folder
		if (is_dir($path)) {
			$dir = dir($path);
			while (false !== $entry = $dir->read()) {
				// Skip pointers
				if ($entry == '.' || $entry == '..') {
					continue;
				}
				if (fn_uc_ftp_rm($path . '/' . $entry) == false) {
					return false;
				}
			}
			// Clean up
			$dir->close();
			return @ftp_rmdir($ftp, $rel_path);
		}
	}

	return false;
}

/**
 * Functions creates dump of tables in $file.
 *
 * @param mixed $tables Array of tables
 * @param string $dir Directory for saving file with table names
 * @param string $file Dump file name
 * @return Boolean False on failure
 */
function fn_uc_backup_tables($tables, $dir, $file, $log)
{
	if (empty($tables)) {
		return array();
	}

	if (!is_array($tables)) {
		$tables = array($tables);
	}

	$new_license_data = fn_uc_get_new_license_data();
	if (!empty($new_license_data['new_license']) && !in_array(TABLE_PREFIX . 'settings', $tables)) {
		$tables[] = TABLE_PREFIX . 'settings';
	}

	$file_backuped_tables = "$dir/db_backup_tables.txt";
	$backuped_tables = is_file($file_backuped_tables) ? explode("\n", fn_get_contents($file_backuped_tables)) : array();

	foreach ($tables as $key => &$table) {
		$table = fn_check_db_prefix($table);
		if (in_array($table, $backuped_tables)) {
			unset($tables[$key]);
		}
	}

	if (empty($tables)) {
		return $tables;
	}
	
	if (false === fn_uc_is_enough_disk_space($file, $tables)) {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_no_enough_space_to_backup_database'));
		return false;
	}

	if (fn_uc_is_mysqldump_available(Registry::get('config.db_password'))) {
		$log->write('Using mysqldump', __FILE__, __LINE__);
		$command = 'mysqldump --compact --no-create-db --add-drop-table --default-character-set=utf8 --skip-comments --verbose --host=' . Registry::get('config.db_host') . ' --user=' . Registry::get('config.db_user') . ' --password=\'' . Registry::get('config.db_password') . '\' --databases ' . Registry::get('config.db_name') . ' --tables ' . implode(' ', $tables) . ' >> ' . $file;
		system($command, $retval);

		if (0 === $retval) {
			$backuped_tables = array_merge($backuped_tables, $tables);
			fn_put_contents($file_backuped_tables, implode("\n", $backuped_tables));
			return $tables;
		}
		$log->write('mysqldump has reported failure', __FILE__, __LINE__);
	}

	$rows_per_pass = 40;
	$max_row_size = 10000;

	$t_status = db_get_hash_array("SHOW TABLE STATUS", 'Name');
	$f = fopen($file, 'ab');
	if (!empty($f)) {
		foreach ($tables as &$table) {
			$log->write('Backing up table: ' . $table, __FILE__, __LINE__);
			fwrite($f, "\nDROP TABLE IF EXISTS " . str_replace('?:', TABLE_PREFIX, $table) . ";\n");
			if (empty($t_status[str_replace('?:', TABLE_PREFIX, $table)])) { // new table in upgrade, we need drop statement only
				continue;
			}
			$scheme = db_get_row("SHOW CREATE TABLE $table");
			fwrite($f, array_pop($scheme) . ";\n\n");

			$total_rows = db_get_field("SELECT COUNT(*) FROM $table");

			// Define iterator
			if ($t_status[str_replace('?:', TABLE_PREFIX, $table)]['Avg_row_length'] < $max_row_size) {
				$it = $rows_per_pass;
			} else {
				$it = 1;
			}

			fn_echo(' .');

			for ($i = 0; $i < $total_rows; $i = $i + $it) {
				$table_data = db_get_array("SELECT * FROM $table LIMIT $i, $it");
				foreach ($table_data as $_tdata) {
					$_tdata = fn_add_slashes($_tdata, true);
					$values = array();
					foreach ($_tdata as $v) {
						$values[] = ($v !== null) ? "'$v'" : 'NULL';
					}
					fwrite($f, "INSERT INTO " . str_replace('?:', TABLE_PREFIX, $table) . " (`" . implode('`, `', array_keys($_tdata)) . "`) VALUES (" . implode(', ', $values) . ");\n");
				}
			}
			
			$backuped_tables[] = "$table";
		}
		fclose($f);
		@chmod($file, DEFAULT_FILE_PERMISSIONS);
		fn_put_contents($file_backuped_tables, implode("\n", $backuped_tables));
		return $tables;
	}
}

/**
 * Function updates language variables for all installed languages.
 *
 * @param string $table Name of table for updating
 * @param mixed $keys Array of primary keys in table for data comparison
 * @param boolean $show_process Echo or no ' .' for showing process.
 */
function fn_uc_update_alt_languages($table, $keys, $show_process = true)
{
	static $langs;

	if (empty($langs)) {
		$langs = db_get_fields("SELECT lang_code FROM ?:languages");
	}

	if (!is_array($keys)) {
		$keys = array($keys);
	}

	$i = 0;
	$step = 50;
	while ($items = db_get_array("SELECT * FROM ?:$table WHERE lang_code = 'EN' LIMIT $i, $step")) {
		$i += $step;
		foreach ($items as $v) {
			foreach ($langs as $lang) {
				$condition = array();
				foreach ($keys as $key) {
					$condition[] = "$key = '" . $v[$key] . "'";
				}
				$condition = implode(' AND ', $condition);
				$exists = db_get_field("SELECT COUNT(*) FROM ?:$table WHERE $condition AND lang_code = ?s", $lang);
				if (empty($exists)) {
					$v['lang_code'] = $lang;
					db_query("REPLACE INTO ?:$table ?e", $v);
					if ($show_process) {
						fn_echo(' .');
					}
				}
			}
		}
	}
}

function fn_uc_is_mysqldump_available($password)
{
	// check that password do not have special char '
	if (strpos($password, "'") !== false) {
		return false;
	}

	if (function_exists('exec') && function_exists('system')) {
		exec('mysqldump', $output, $retval);
		return 1 === $retval;
	}
	
	return false;
}

function fn_uc_is_enough_disk_space($path, $tables)
{
	setlocale(LC_ALL, 'en_US.UTF8');
	$avilable_space = @disk_free_space(dirname($path));

	// we've got an strange error on some servers: "Value too large for defined data type"
	// In this case disk_free_space returns false. So try to continue upgrade
	if ($avilable_space === false) {
		return true;
	}
	
	$size_of_tables = 0;
	foreach ($tables as $table) {
		$table_data = db_get_array("SHOW TABLE STATUS LIKE '" . $table . "'");
		foreach ($table_data as $_tdata) {
			$size_of_tables += $_tdata['Data_length'] + $_tdata['Index_length'];
		}
	}

	return $avilable_space > $size_of_tables;
}

function fn_uc_run_continuous_job($fn_name, $args)
{
	// make sure no another upgrade processes are still active
	$lock_file_path = DIR_UPGRADE . '.upgrade_lock';

	if (is_file ($lock_file_path)) {
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_another_update_process_running'));
		exit;
	} else {
		$h = fopen($lock_file_path, 'w');
		if (!$h) {
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('text_uc_cannot_lock_process'));
			exit;
		}
		fclose($h);
	}

	// adjust PHP to handle the long-running process
	ignore_user_abort(true);
	set_time_limit(0);

	// set up the logger
	try {
		$log = Logger::getInstance();
		$log->logfile = DIR_UPGRADE . 'upgrade.log';
	} catch(Exception $e) {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_upgrade_log_file_not_writable'));
	}
	$args[] = $log;

	// run the job
	$result = call_user_func_array($fn_name, $args);

	// remove the lock after the job is done
	@unlink($lock_file_path);
	if (is_file($lock_file_path)) {
		fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('text_uc_unable_to_remove_upgrade_lock'));
	}

	return $result;
}

?>