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


if ( !defined('AREA') ) { die('Access denied'); }

function fn_register_custom_classes($base_path, $path) {
	if (is_dir($base_path . '/'. $path)) {
		$dirs = fn_get_dir_contents($base_path . '/'. $path);
		if (!empty($dirs)){
			foreach ($dirs as $dir) {
				fn_register_custom_classes($base_path, $path . '/'. $dir);
			}
		}

		$classes = Registry::get('auto_load_classes');
		$files = fn_get_dir_contents($base_path . '/'. $path, false, true);
		if (!empty($files)){
			foreach ($files as $file) {
				$classes[$path . '/' . $file] = $base_path . '/' . $path . '/' . $file;
			}	
		}
		Registry::set('auto_load_classes', $classes);
	}
}

function fn_auto_load_class($class_name)
{
	$core_path = DIR_CORE . 'classes';
	$filename = '';

	$path = explode('_', $class_name);
	foreach($path as $item) {
		$filename .= '/' . fn_class_name_to_path($item);
	}

	$filename .= '.php';

	if (file_exists($core_path . '/' . $filename)) {
		include_once $core_path . '/' . $filename;
	} elseif (class_exists('Registry')) {
		$custom_classes = Registry::get('auto_load_classes');
		if (isset($custom_classes[$filename]) && file_exists($custom_classes[$filename])){
			include_once $custom_classes[$filename];
		}
	}
}

function fn_class_name_to_path($class_name)
{
	$class_name = preg_replace("/(?!^)[[:upper:]][[:lower:]]/", "$0", preg_replace("/(?!^)[[:upper:]]+/", "_$0", $class_name));
	return fn_strtolower($class_name);
}

function fn_path_to_class_name($path)
{
	//$path = preg_replace("/(?!^)[[:upper:]][[:lower:]]/", "$0", preg_replace("/(?!^)[[:upper:]]+/", "_$0", $path));
	$path = preg_replace("/(?!^)[\/|_]([[:lower:]])/e", "strtoupper('$0')", $path);
	$path = str_replace('_', '', $path);
	$path = str_replace('.php', '', $path);
	$path = str_replace('/', '_', $path);
	return strtoupper(substr($path, 0, 1)) . substr($path, 1);
}

/**
 * Init mail engine
 *
 * @return boolean always true
 */
function fn_init_mailer()
{
	if (defined('MAILER_STARTED')) {
		
		$mailer = & Registry::get('mailer');
		$mailer->ClearReplyTos();
		$mailer->ClearAttachments();
		$mailer->Sender = '';
		
		return true;
	}

	$mailer_settings = CSettings::instance()->get_values('Emails');

	$mailer = new Mailer();
	$mailer->LE = (defined('IS_WINDOWS')) ? "\r\n" : "\n";
	$mailer->PluginDir = DIR_LIB . 'phpmailer/';

	if ($mailer_settings['mailer_send_method'] == 'smtp') {
		$mailer->IsSMTP();
		$mailer->SMTPAuth = ($mailer_settings['mailer_smtp_auth'] == 'Y') ? true : false;
		$mailer->Host = $mailer_settings['mailer_smtp_host'];
		$mailer->Username = $mailer_settings['mailer_smtp_username'];
		$mailer->Password = $mailer_settings['mailer_smtp_password'];

	} elseif ($mailer_settings['mailer_send_method'] == 'sendmail') {
		$mailer->IsSendmail();
		$mailer->Sendmail = $mailer_settings['mailer_sendmail_path'];

	} else {
		$mailer->IsMail();
	}

	Registry::set('mailer', $mailer);

	define('MAILER_STARTED', true);

	return true;
}

/**
 * Init template engine
 *
 * @return boolean always true
 */
function fn_init_templater()
{
	if (defined('TEMPLATER_STARTED')) {
		return true;
	}

	//
	// Template objects for processing html templates
	//
	$view = new SmartyEngine_Core();
	$view_mail = new SmartyEngine_Core();

	$view->register_prefilter(array(&$view, 'prefilter_keep_regexp_vars'));
	
	fn_set_hook('init_templater', $view, $view_mail);

	$view->register_prefilter(array(&$view, 'prefilter_hook'));
	$view_mail->register_prefilter(array(&$view_mail, 'prefilter_hook'));
	if (AREA == 'A' && !empty($_SESSION['auth']['user_id'])) {
		$view->register_prefilter(array(&$view, 'prefilter_form_tooltip'));
	}
	
	if (!Registry::get('config.tweaks.allow_php_in_templates')) {
		$view->register_prefilter(array(&$view, 'prefilter_security_exec'));
	}

	if (Registry::get('settings.customization_mode') == 'Y' && AREA == 'C') {
		$view->register_prefilter(array(&$view, 'prefilter_template_wrapper'));
		$view->register_outputfilter(array(&$view, 'outputfilter_template_ids'));
		$view->customization = true;
	} else {

		// Inline prefilter
		if (Registry::get('config.tweaks.inline_compilation') == true) {
			$view->register_prefilter(array(&$view, 'prefilter_inline'));
		}
	}

	if (Registry::get('config.tweaks.anti_csrf') == true) {
		$view->register_outputfilter(array(&$view, 'outputfilter_security_hash'));
	}


	

	
	// Output bufferring postfilter
	$view->register_prefilter(array(&$view, 'prefilter_output_buffering'));

	// Translation postfilter
	$view->register_postfilter(array(&$view, 'postfilter_translation'));

	if (Registry::get('settings.translation_mode') == 'Y') {
		$view->register_outputfilter(array(&$view, 'outputfilter_translate_wrapper'));
		$view_mail->register_outputfilter(array(&$view, 'outputfilter_translate_wrapper'));
	}

	$view->register_outputfilter(array(&$view, 'outputfilter_keep_regexp_vars'));
	
	//
	// Store all compiled templates to the single directory
	//
	$view->use_sub_dirs = false;
	$view->compile_check = (Registry::get('settings.store_optimization') == 'dev')? true : false;


	if (Registry::get('settings.General.debugging_console') == 'Y') {

		if (empty($_SESSION['debugging_console']) && !empty($_SESSION['auth']['user_id']))	{
			$user_type = db_get_field("SELECT user_type FROM ?:users WHERE user_id = ?i", $_SESSION['auth']['user_id']);
			if ($user_type == 'A') {
				$_SESSION['debugging_console'] = true;
			}
		}

		if (isset($_SESSION['debugging_console']) && $_SESSION['debugging_console'] == true) {
			error_reporting(0);
			$view->debugging = true;
		}
	}

	$skin_path = DIR_SKINS . Registry::get('config.skin_name');
	$area = AREA_NAME;
		
	$skin_path = fn_get_skin_path('[skins]/[skin]');

	$view->template_dir = $skin_path . '/' . AREA_NAME;
	$view->config_dir = $skin_path . '/' . AREA_NAME;
	$view->secure_dir = $skin_path . '/' . AREA_NAME;
	$view->assign('images_dir', Registry::get('config.full_host_name') . Registry::get('config.current_path') . (str_replace(DIR_ROOT, '', $skin_path)) . '/' . AREA_NAME . "/images");
	
 	$view->compile_dir = DIR_COMPILED . AREA_NAME . (defined('SKINS_PANEL') || PRODUCT_TYPE == 'ULTIMATE' ? '/' . Registry::get('config.skin_name') : '');
	$view->cache_dir = DIR_CACHE;
	$view->assign('skin_area', AREA_NAME);
	
	// Get manifest
	$manifest = fn_get_manifest(AREA_NAME);
	$view->assign('manifest', $manifest);
	
	// Mail templates should be taken from the customer skin
	if (AREA != 'C') {
		$manifest = fn_get_manifest('customer');
	}

	$customer_skin_path = fn_get_skin_path('[skins]/[skin]', 'customer');

	$view_mail->assign('manifest', $manifest);
	$view_mail->template_dir = $customer_skin_path . '/mail';
	$view_mail->config_dir = $customer_skin_path . '/mail';
	$view_mail->secure_dir = $customer_skin_path . '/mail';
	$view_mail->assign('images_dir', Registry::get('config.full_host_name') . Registry::get('config.current_path') . (str_replace(DIR_ROOT, '', $skin_path)) . '/mail/images');
	$view_mail->compile_dir = DIR_COMPILED . 'mail' . (defined('SKINS_PANEL') || PRODUCT_TYPE == 'ULTIMATE' ? '/' . Registry::get('config.skin_name') : '');
	$view_mail->assign('skin_area', 'mail');

	if (!is_dir($view->compile_dir)) {
		fn_mkdir($view->compile_dir);
	}

	if (!is_dir($view->cache_dir)) {
		fn_mkdir($view->cache_dir);
	}


	if (!is_dir($view_mail->compile_dir) ) {
		fn_mkdir($view_mail->compile_dir);
	}

	if (!is_writable($view->compile_dir) || !is_dir($view->compile_dir) ) {
		fn_error(debug_backtrace(), "Can't write template cache in the directory: <b>" . $view->compile_dir . '</b>.<br>Please check if it exists, and has writable permissions.', false);
	}

	$view->assign('ldelim','{');
	$view->assign('rdelim','}');
	
	$avail_languages = array();
	foreach (Registry::get('languages') as $k => $v) {
		if ($v['status'] == 'D') {
			continue;
		}

		$avail_languages[$k] = $v;
	}
	$view->assign('languages', $avail_languages);
	$view->setLanguage(CART_LANGUAGE);
	$view_mail->setLanguage(CART_LANGUAGE);

	if (Registry::get('settings.translation_mode') == 'Y') {
		$view_mail->assign('ldelim','{');
		$view_mail->assign('rdelim','}');
		$view_mail->assign('languages', $avail_languages);
	}
	
	$view->assign('localizations', fn_get_localizations(CART_LANGUAGE , true));
	if (defined('CART_LOCALIZATION')) {
		$view->assign('localization', fn_get_localization_data(CART_LOCALIZATION));
	}
	
	
	/* Get correct path to customer skin area */
	$customer_skin_path = str_replace(DIR_ROOT . '/', '', $customer_skin_path);
	$view->assign('customer_skin_path', $customer_skin_path);
	$view_mail->assign('customer_skin_path', $customer_skin_path);
	
	$view->assign('currencies', Registry::get('currencies'), false);
	$view->assign('primary_currency', CART_PRIMARY_CURRENCY, false);
	$view->assign('secondary_currency', CART_SECONDARY_CURRENCY, false);

	$view_mail->assign('currencies', Registry::get('currencies'), false);
	$view_mail->assign('primary_currency', CART_PRIMARY_CURRENCY, false);
	$view_mail->assign('secondary_currency', CART_SECONDARY_CURRENCY, false);

	$view->assign('s_companies', Registry::get('s_companies'));
	$view->assign('s_company', defined('COMPANY_ID') ? COMPANY_ID : 'all');

	$view_mail->assign('s_companies', Registry::get('s_companies'));
	$view_mail->assign('s_company', defined('COMPANY_ID') ? COMPANY_ID : 'all');

	Registry::set('view', $view);
	Registry::set('view_mail', $view_mail);

	Registry::set('skin_path', $skin_path);
	Registry::set('customer_skin_path', $customer_skin_path);
	
	Registry::set('http_skin_path', str_replace(DIR_ROOT, '', $skin_path));

	define('TEMPLATER_STARTED', true);

	return true;
}

/**
 * Init crypt engine
 *
 * @return boolean always true
 */
function fn_init_crypt()
{
	if (!defined('CRYPT_STARTED')) {
		if (!include(DIR_LIB . 'crypt/Blowfish.php')) {
			fn_error(debug_backtrace(), "Can't connect Blowfish crypt class", false);
		}

		$crypt = new Crypt_Blowfish(Registry::get('config.crypt_key'));
		Registry::set('crypt', $crypt);

		fn_define('CRYPT_STARTED', true);
	}

	return true;
}

/**
 * Init ajax engine
 *
 * @return boolean true if current request is ajax, false - otherwise
 */
function fn_init_ajax()
{
	if (defined('AJAX_REQUEST')) {
		return true;
	}

	if (empty($_REQUEST['ajax_custom']) && (!empty($_REQUEST['is_ajax']) || (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false))) {
		$ajax = new Ajax();
		Registry::set('ajax', $ajax);
		fn_define('AJAX_REQUEST', true);
		return true;
	}

	return false;
}

/**
 * Init yaml engine
 *
 * @return boolean always true
 */
function fn_init_yaml()
{
	if (!defined('YAML_STARTED')) {
		require(DIR_LIB . 'spyc/spyc.php');
		fn_define('YAML_STARTED', true);
	}

	return true;
}

/**
 * Init pdf engine
 *
 * @return boolean always true
 */
function fn_init_pdf()
{
	// pdf can't be generated correctly without DOM extension (DOMDocument class)
	if (!class_exists('DOMDocument')) {
		$msg = (AREA == 'A') ? fn_get_lang_var('error_generate_pdf_admin') : fn_get_lang_var('error_generate_pdf_customer');
		fn_set_notification('E', fn_get_lang_var('error'), $msg);
		return false;
	}

	if (defined('PDF_STARTED')) {
		return true;
	}

	define('CACHE_DIR', DIR_CACHE . 'pdf/cache');
	define('OUTPUT_FILE_DIRECTORY', DIR_CACHE . 'pdf/out');
	define('WRITER_TEMPDIR', DIR_CACHE . 'pdf/temp');

	require(DIR_CORE . '/classes/pdf/fetcher_memory.php');

	if (!is_dir('CACHE_DIR')) {
		fn_mkdir(CACHE_DIR);
	}

	if (!is_dir('OUTPUT_FILE_DIRECTORY')) {
		fn_mkdir(OUTPUT_FILE_DIRECTORY);
	}

	if (!is_dir('WRITER_TEMPDIR')) {
		fn_mkdir(WRITER_TEMPDIR);
	}

	parse_config_file(HTML2PS_DIR . 'html2ps.config');

	fn_define('PDF_STARTED', true);
	
	return true;
}

/**
 * Init diff engine
 *
 * @return boolean always true
 */
function fn_init_diff()
{
	if (!defined('DIFF_STARTED')) {
		include(DIR_LIB . 'pear/PEAR.php');
		include(DIR_LIB . 'Text/Diff.php');
		include(DIR_LIB . 'Text/Diff/Renderer.php');
		include(DIR_LIB . 'Text/Diff/Renderer/inline.php');

		fn_define('DIFF_STARTED', true);
	}

	return true;
}

/**
 * Init languages
 *
 * @param array $params request parameters
 * @return boolean always true
 */
function fn_init_language($params)
{
	$join_cond = '';
	$condition = (AREA == 'A') ? '' : ((isset($_SESSION['auth']['area']) && ($_SESSION['auth']['area'] == 'A')) ? '' : "WHERE ?:languages.status = 'A'");
	$order_by = '';
	
	if ((AREA == 'C') && defined('CART_LOCALIZATION')) {
		$join_cond = "LEFT JOIN ?:localization_elements ON ?:localization_elements.element = ?:languages.lang_code AND ?:localization_elements.element_type = 'L'";
		$separator = ($condition == '') ? 'WHERE' : 'AND';      
		$condition .= db_quote(" $separator ?:localization_elements.localization_id = ?i", CART_LOCALIZATION);
		$order_by = "ORDER BY ?:localization_elements.position ASC";
	}
	
	$languages = db_get_hash_array("SELECT ?:languages.* FROM ?:languages $join_cond ?p $order_by", 'lang_code', $condition);
	$avail_languages = array();

	foreach ($languages as $k => $v) {
		if ($v['status'] == 'D') {
			continue;
		}

		$avail_languages[$k] = $v;
	}

	if (!empty($params['sl']) && !empty($avail_languages[$params['sl']])) {
		fn_define('CART_LANGUAGE', $params['sl']);
	} elseif (!fn_get_session_data('cart_language' . AREA) && $_lc = fn_get_browser_language($avail_languages)) {
		fn_define('CART_LANGUAGE', $_lc);
	} elseif (!fn_get_session_data('cart_language' . AREA) && !empty($avail_languages[Registry::get('settings.Appearance.' . AREA_NAME . '_default_language')])) {
		fn_define('CART_LANGUAGE', Registry::get('settings.Appearance.' . AREA_NAME . '_default_language'));

	} elseif (($_c = fn_get_session_data('cart_language' . AREA)) && !empty($avail_languages[$_c])) {
		fn_define('CART_LANGUAGE', $_c);

	} else {
		reset($avail_languages);
		fn_define('CART_LANGUAGE', key($avail_languages));
	}

	// For administrative area, set description language
	if (!empty($params['descr_sl']) && !empty($avail_languages[$params['descr_sl']])) {
		fn_define('DESCR_SL', $params['descr_sl']);
		fn_set_session_data('descr_sl', $params['descr_sl'], COOKIE_ALIVE_TIME);
	} elseif (($d = fn_get_session_data('descr_sl')) && !empty($avail_languages[$d])) {
		fn_define('DESCR_SL', $d);
	} else {
		fn_define('DESCR_SL', CART_LANGUAGE);
	}


	if (CART_LANGUAGE != fn_get_session_data('cart_language' . AREA)) {
		fn_set_session_data('cart_language' . AREA, CART_LANGUAGE, COOKIE_ALIVE_TIME);
	}

	header("Content-Type: text/html; charset=" . CHARSET);

	Registry::set('languages', $languages);

	return true;
}

/**
 * Init company
 *
 * @param array $params request parameters
 * @return boolean always true
 */
function fn_init_company($params)
{
	if (AREA == 'A' && !empty($_SESSION['auth']['company_id']) && (PRODUCT_TYPE == 'MULTIVENDOR' || PRODUCT_TYPE == 'ULTIMATE')) {
		fn_define('COMPANY_ID', $_SESSION['auth']['company_id']);

		$company_ids = array(COMPANY_ID);
		if (!empty($_SESSION['auth']['companies_usergroups'])) {
			foreach ($_SESSION['auth']['companies_usergroups'] as $usergroup_data) {
				$company_ids[] = $usergroup_data['company_id'];
			}
		}

		$companies = db_get_hash_array("SELECT ?:companies.* FROM ?:companies WHERE company_id IN(?a) AND status IN ('A', 'P')", 'company_id', $company_ids);
		if (empty($companies)) {

			$_SESSION['auth'] = array();
			fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('access_denied'));
			$suffix = (strpos($_SERVER['HTTP_REFERER'], '?') !== false ? '&' : '?') . 'login_type=login' . (!empty($_REQUEST['return_url']) ? '&return_url=' . urlencode($_REQUEST['return_url']) : '');
			//TODO HTTP_REFERER could be empty
			fn_redirect("$_SERVER[HTTP_REFERER]$suffix");
		}
		
	} else {
		$companies = array(
			'all' => array(
				'company_id' => 'all',
				'company' => fn_get_lang_var('all_vendors'),
			),
			'0' => array(
				'company_id' => '0',
				'company' => Registry::get('settings.Company.company_name'),
				'status' => 'A'
			),
		);
		
		if (defined('SELECTED_COMPANY_ID') && SELECTED_COMPANY_ID != 'all') {
			// trying to select company data
			if (SELECTED_COMPANY_ID) {
				$_companies = db_get_hash_array("SELECT ?:companies.* FROM ?:companies WHERE company_id = ?i", 'company_id', SELECTED_COMPANY_ID);
				if (!empty($_companies[SELECTED_COMPANY_ID])) {
					$companies = $companies + $_companies;
				}
			}
			
			// For administrative area, set selected company
			if (isset($companies[SELECTED_COMPANY_ID])) {
				fn_define('COMPANY_ID', SELECTED_COMPANY_ID);
				fn_set_session_data('company_id', COMPANY_ID, COOKIE_ALIVE_TIME);
			} else {
				fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var('company_not_found'));

				$dispatch = $params['dispatch'];
				unset($params['s_company']);
				unset($params['dispatch']);

				$_c = fn_get_session_data('company_id');
				$_companies = db_get_hash_array("SELECT ?:companies.* FROM ?:companies WHERE company_id = ?i", 'company_id', $_c);
				if (empty($_companies[$_c])) {
					fn_delete_session_data('company_id');
					$_c = 'all';
				}
				$params['s_company'] = $_c;

				fn_redirect(fn_url("$dispatch?" . fn_build_query($params)));
			}
		}
	}
	
	Registry::set('s_companies', $companies);
	
	fn_set_hook('init_companies', $params, $companies);

	return true;
}

function fn_init_selected_company_id(&$params)
{
	if ((PRODUCT_TYPE == 'MULTIVENDOR' && AREA == 'A') || PRODUCT_TYPE == 'ULTIMATE') {
		if (
			PRODUCT_TYPE == 'ULTIMATE' 
			&& isset($params['s_company']) && $params['s_company'] !== false 
			&& !empty($_SESSION['auth']['companies_usergroups'])
		) {
			foreach ($_SESSION['auth']['companies_usergroups'] as $usergroup_data) {
				if ($usergroup_data['company_id'] == $params['s_company']) {
					$_SESSION['auth']['company_id'] = $params['s_company'];
				}
			}
		}

		if (!empty($_SESSION['auth']['company_id']) && AREA == 'A') {
			fn_define('SELECTED_COMPANY_ID', $_SESSION['auth']['company_id']);
		} else {
			// Set selected company
			$_c = fn_get_session_data('company_id');
			if (isset($params['s_company']) && $params['s_company'] !== false) {
				if ($params['s_company'] == 'all') {
					$s_company = 'all';
				} else {
					$exists = db_get_field("SELECT COUNT(*) FROM ?:companies WHERE company_id = ?i", $params['s_company']);
					if ($exists) {
						$s_company = intval($params['s_company']);
					} elseif (PRODUCT_TYPE == 'MULTIVENDOR' && $params['s_company'] == '0') {
						$s_company = '0';
					} else {
						$s_company = null;
					}
				}
				fn_define('SELECTED_COMPANY_ID', $s_company);
			} elseif ($_c !== false) {
				fn_define('SELECTED_COMPANY_ID', $_c);
			}
		}

		if (defined('SELECTED_COMPANY_ID') && SELECTED_COMPANY_ID == 'all') {
			fn_set_session_data('company_id', SELECTED_COMPANY_ID, COOKIE_ALIVE_TIME);
		}
	}
	
	$var_path = '';
	
	fn_set_hook('init_selected_company', $params, $var_path);
	
	fn_define('VAR_PATH', $var_path);
}

/**
 * Init currencies
 *
 * @param array $params request parameters
 * @return boolean always true
 */
function fn_init_currency($params)
{
	$cond = $join = $order_by = '';
	
	if ((AREA == 'C') && defined('CART_LOCALIZATION')) {
		$join = " LEFT JOIN ?:localization_elements as c ON c.element = a.currency_code AND c.element_type = 'M'";
		$cond = db_quote('AND c.localization_id = ?i', CART_LOCALIZATION);
		$order_by = "ORDER BY c.position ASC";
	}
	
	if (!$order_by) {
		$order_by = 'ORDER BY a.position';	
	}
	$currencies = db_get_hash_array("SELECT a.*, b.description FROM ?:currencies as a LEFT JOIN ?:currency_descriptions as b ON a.currency_code = b.currency_code AND lang_code = ?s $join WHERE status = 'A' ?p $order_by", 'currency_code', CART_LANGUAGE, $cond);

	if (!empty($params['currency']) && !empty($currencies[$params['currency']])) {
		$secondary_currency = $params['currency'];
	} elseif (($c = fn_get_session_data('secondary_currency' . AREA)) && !empty($currencies[$c])) {
		$secondary_currency = $c;
	} else {
		foreach ($currencies as $v) {
			if ($v['is_primary'] == 'Y') {
				$secondary_currency = $v['currency_code'];
				break;
			}
		}
	}

	if (empty($secondary_currency)) {
		reset($currencies);
		$secondary_currency = key($currencies);
	}

	if ($secondary_currency != fn_get_session_data('secondary_currency' . AREA)) {
		fn_set_session_data('secondary_currency'.AREA, $secondary_currency, COOKIE_ALIVE_TIME);
	}

	$primary_currency = '';

	foreach ($currencies as $v) {
		if ($v['is_primary'] == 'Y') {
			$primary_currency = $v['currency_code'];
			break;
		}
	}

	if (empty($primary_currency)) {
		reset($currencies);
		$first_currency = current($currencies);
		$primary_currency = $first_currency['currency_code'];
	}

	define('CART_PRIMARY_CURRENCY', $primary_currency);
	define('CART_SECONDARY_CURRENCY', $secondary_currency);

	Registry::set('currencies', $currencies);

	return true;
}

/**
 * Init skin
 *
 * @param array $params request parameters
 * @return boolean always true
 */
function fn_init_skin($params)
{
	if (defined('DEVELOPMENT')) {
		$dev_skins = Registry::if_get('config.dev_skins', array());
		if (!empty($dev_skins) && is_array($dev_skins)) {
			foreach ($dev_skins  as $k => $v) {
				Registry::set('settings.skin_name_' . $k, $v);
			}
		}
	}
	
	$skin_path = fn_get_skin_path('[skins]/[skin]', AREA_NAME);

	if ((Registry::get('settings.skin_name_' . AREA_NAME) == '' || !is_dir($skin_path)) && !defined('SKINS_PANEL')) {
		$all = fn_get_dir_contents(DIR_SKINS, true);
		$skin_found = false;
		foreach ($all as $sk) {
			if (is_file(DIR_SKINS . $sk . '/' . AREA_NAME . '/index.tpl')) {
				Registry::set('settings.skin_name_' . AREA_NAME, basename($sk));
				$skin_found = true;
				break;
			}
		}

		if ($skin_found == false) {
			die("No skins found");
		} else {
			echo <<<EOT
				<div style="background: #ff0000; color: #ffffff; font-weight: bold;" align="center">SELECTED SKIN NOT FOUND. REPLACED BY FIRST FOUND</div>
EOT;
		}
	}

	// Allow user to change the skin during the current session
	if (defined('SKINS_PANEL')) {
		$demo_skin = fn_get_session_data('demo_skin');

		if (!empty($params['demo_skin'][AREA])) {
			$tmp_skin = basename($params['demo_skin'][AREA]);

			if (is_dir(DIR_SKINS . $tmp_skin)) {
				Registry::set('settings.skin_name_' . AREA_NAME, $tmp_skin);
				$demo_skin[AREA] = $tmp_skin;
			} else {
				Registry::set('settings.skin_name_' . AREA_NAME, $demo_skin[AREA]);
			}
		} elseif (empty($demo_skin[AREA])) {
			$demo_skin[AREA] = 'basic';
		}

		Registry::set('settings.skin_name_' . AREA_NAME, $demo_skin[AREA]);
		fn_set_session_data('demo_skin', $demo_skin);

		Registry::set('demo_skin', array(
			'selected' => $demo_skin,
			'available_skins' => fn_get_available_skins(AREA_NAME)
		));


	}

	$skin_name = Registry::get('settings.skin_name_' . AREA_NAME);
	Registry::set('config.skin_name', $skin_name);
	Registry::set('config.skin_path', Registry::get('config.full_host_name') . Registry::get('config.current_path') . str_replace(DIR_ROOT, '', $skin_path . '/' . AREA_NAME));
	Registry::set('config.no_image_path', Registry::get('config.full_host_name') . Registry::get('config.images_path') . 'no_image.gif');

	return true;
}

/**
 * Init user
 *
 * @return boolean always true
 */
function fn_init_user()
{
	if (!empty($_SESSION['auth']['user_id']))	{
		$user_info = fn_get_user_short_info($_SESSION['auth']['user_id']);
		if (empty($user_info)) { // user does not exist in the database, but exists in session
			$_SESSION['auth'] = array();
		} else {
			$_SESSION['auth']['usergroup_ids'] = fn_define_usergroups(array('user_id' => $_SESSION['auth']['user_id'], 'user_type' => $user_info['user_type']));
		}
	}

	$first_init = false;
	if (empty($_SESSION['auth'])) {

		$udata = array();
		$user_id = fn_get_session_data(AREA_NAME . '_user_id');

		if ($user_id) {
			fn_define('LOGGED_VIA_COOKIE', true);
		}

		fn_login_user($user_id);

		if (!defined('NO_SESSION')) {
			$_SESSION['cart'] = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
		}

		if ((defined('LOGGED_VIA_COOKIE') && !empty($_SESSION['auth']['user_id'])) || ($cu_id = fn_get_session_data('cu_id'))) {
			$first_init = true;
			if (!empty($cu_id)) {
				fn_define('COOKIE_CART' , true);
			}

			// Cleanup cached shipping rates

			unset($_SESSION['shipping_rates']);

			$_utype = empty($_SESSION['auth']['user_id']) ? 'U' : 'R';
			$_uid = empty($_SESSION['auth']['user_id']) ? $cu_id : $_SESSION['auth']['user_id'];
			fn_extract_cart_content($_SESSION['cart'], $_uid , 'C' , $_utype);
			fn_save_cart_content($_SESSION['cart'] , $_uid , 'C' , $_utype);
			if (!empty($_SESSION['auth']['user_id'])) {
				$_SESSION['cart']['user_data'] = fn_get_user_info($_SESSION['auth']['user_id']);
				$user_info = fn_get_user_short_info($_SESSION['auth']['user_id']);
			}
		}
	}

	if (TIME > Registry::get('settings.cart_products_next_check')) {
		fn_define('CART_PRODUCTS_CHECK_PERIOD' , SECONDS_IN_HOUR * 12);
		fn_define('CART_PRODUCTS_DELETE_TIME' , TIME - SECONDS_IN_DAY * 30);
		db_query("DELETE FROM ?:user_session_products WHERE user_type = 'U' AND timestamp < ?i", CART_PRODUCTS_DELETE_TIME);
		CSettings::instance()->update_value('cart_products_next_check', TIME + CART_PRODUCTS_CHECK_PERIOD);
	}
	
	// If administrative account has usergroup, it means the access restrictions are in action
	if (AREA == 'A' && (!empty($_SESSION['auth']['usergroup_ids']) || (!empty($_SESSION['auth']['company_id']) && $_SESSION['auth']['is_root'] != 'Y'))) {
		fn_define('RESTRICTED_ADMIN', true);
	}
	
	if (
		!empty($user_info) 
		&& (
			$user_info['user_type'] == 'A' && empty($user_info['company_id']) 
			|| 
			PRODUCT_TYPE == 'ULTIMATE' && defined('COMPANY_ID') && COMPANY_ID == $user_info['company_id'] && $user_info['user_type'] == 'V'
		)
	) {
		if (Registry::get('settings.translation_mode') == 'Y') {
			fn_define('TRANSLATION_MODE', true);
		}
		
		if (Registry::get('settings.customization_mode') == 'Y') {
			if (AREA != 'A') {
				fn_define('PARSE_ALL', true);
			}
			fn_define('CUSTOMIZATION_MODE', true);
		}
	}

	fn_set_hook('user_init', $_SESSION['auth'], $user_info, $first_init);

	Registry::set('user_info', $user_info);
	Registry::get('view')->assign('auth', $_SESSION['auth']);
	Registry::get('view')->assign('user_info', $user_info);

	return true;
}

/**
 * Init localizations
 *
 * @param array $params request parameters
 * @return boolean true if localizations exists, false otherwise
 */
function fn_init_localization($params)
{
	$locs = db_get_hash_array("SELECT localization_id, custom_weight_settings, weight_symbol, weight_unit FROM ?:localizations WHERE status = 'A'", 'localization_id');

	if (empty($locs)) {
		return false;
	}

	if (!empty($_REQUEST['lc']) && !empty($locs[$_REQUEST['lc']])) {
		$cart_localization = $_REQUEST['lc'];

	} elseif (($l = fn_get_session_data('cart_localization')) && !empty($locs[$l])) {
		$cart_localization = $l;

	} else {
		$_ip = fn_get_ip(true);
		$_country = fn_get_country_by_ip($_ip['host']);
		$_lngs = db_get_hash_single_array("SELECT lang_code, 1 as 'l' FROM ?:languages WHERE status = 'A'", array('lang_code', 'l'));
		$_language = fn_get_browser_language($_lngs);

		$cart_localization = db_get_field("SELECT localization_id, COUNT(localization_id) as c FROM ?:localization_elements WHERE (element = ?s AND element_type = 'C') OR (element = ?s AND element_type = 'L') GROUP BY localization_id ORDER BY c DESC LIMIT 1", $_country, $_language);

		if (empty($cart_localization) || empty($locs[$cart_localization])) {
			$cart_localization = db_get_field("SELECT localization_id FROM ?:localizations WHERE status = 'A' AND is_default = 'Y'");
		}
	}

	if (empty($cart_localization)) {
		reset($locs);
		$cart_localization = key($locs);
	}

	if ($cart_localization != fn_get_session_data('cart_localization')) {
		fn_set_session_data('cart_localization', $cart_localization, COOKIE_ALIVE_TIME);
	}

	if ($locs[$cart_localization]['custom_weight_settings'] == 'Y') {
		Registry::set('config.localization.weight_symbol', $locs[$cart_localization]['weight_symbol']);
		Registry::set('config.localization.weight_unit', $locs[$cart_localization]['weight_unit']);
	}

	fn_define('CART_LOCALIZATION', $cart_localization);

	return true;
}

/**
 * Detect user agent
 *
 * @return boolean true always
 */
function fn_init_ua() 
{
	static $crawlers = array(
		'google', 'bot', 'yahoo',
		'spider', 'archiver', 'curl',
		'python', 'nambu', 'twitt',
		'perl', 'sphere', 'PEAR',
		'java', 'wordpress', 'radian',
		'crawl', 'yandex', 'eventbox',
		'monitor', 'mechanize', 'facebookexternal'
	);

	$http_ua = isset($_SERVER['HTTP_USER_AGENT']) ? fn_strtolower($_SERVER['HTTP_USER_AGENT']) : '';
	if (strpos($http_ua, 'shiretoko') !== false || strpos($http_ua, 'firefox') !== false) {
		$ua = 'firefox';
	} elseif (strpos($http_ua, 'chrome') !== false) {
		$ua = 'chrome';
	} elseif (strpos($http_ua, 'safari') !== false) {
		$ua = 'safari';
	} elseif (strpos($http_ua, 'opera') !== false) {
		$ua = 'opera';
	} elseif (strpos($http_ua, 'msie') !== false) {
		$ua = 'ie';
	} elseif (empty($http_ua) || preg_match('/(' . implode('|', $crawlers) . ')/', $http_ua, $m)) {
		$ua = 'crawler';
		if (!empty($m)) {
			fn_define('CRAWLER', $m[1]);
		}
		if (!defined('SKIP_SESSION_VALIDATION')) {
			fn_define('NO_SESSION', true); // do not start session for crawler
		}
	} else {
		$ua = 'unknown';
	}

	fn_define('USER_AGENT', $ua);

	return true;
}

?>