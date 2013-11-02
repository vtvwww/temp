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

function fn_get_ems_rates($code, $weight_data, $location, &$auth, $shipping_settings, $package_info, $origination, $service_id, $allow_multithreading = false)
{
	static $cached_rates = array();

	if ($shipping_settings['ems_enabled'] != 'Y') {
		return false;
	}

	if ($origination['country'] != 'RU') {
		if (defined('SHIPPING_DEBUG')) {
			return array('error' => fn_get_lang_var('ems_country_error'));
		}
	}
	$ruble = Registry::get('currencies.RUB');

	if (empty($ruble) || $ruble['is_primary'] == 'N') {
		if (defined('SHIPPING_DEBUG')) {
			return array('error' => fn_get_lang_var('ems_activation_error'));
		}
	}

	$cached_rate_id = fn_generate_cached_rate_id($weight_data, $origination);

	if (!empty($cached_rates[$cached_rate_id])) {
		return array('cost' => $cached_rates[$cached_rate_id]);
	}

	$weight = $weight_data['full_pounds'] * 0.4536;

	$origination_point = '';
	$destination_point = '';

	if ($shipping_settings['ems']['mode'] == 'regions') {
		$origination_point = fn_ems_convert_state($origination['state']);
		$destination_point = fn_ems_convert_state($location['state']);
	} else {
		$url = 'http://www.emspost.ru/api/rest';
		$post = array (
			'method' => 'ems.get.locations',
			'type' => 'cities',
			'plain' => 'true'
		);
		list($headers, $result) = fn_https_request('GET', $url, $post);
		$cities = fn_from_json($result, true);
		if (!empty($cities)) {
			foreach($cities['rsp']['locations'] as $i => $loc_data) {

				if (fn_strtolower($loc_data['name']) == fn_strtolower($origination['city']) || fn_strtolower(str_replace('city--', '', $loc_data['value'])) == fn_strtolower($origination['city'])) {
					$origination_point = $loc_data['value'];
				}
				if ($location['country'] == 'RU') {
					if (fn_strtolower($loc_data['name']) == fn_strtolower($location['city']) || fn_strtolower(str_replace('city--', '', $loc_data['value'])) == fn_strtolower($location['city'])) {
						$destination_point = $loc_data['value'];
					}
				} else {
					$destination_point = $location['country'];
				}

				if (!empty($destination_point) && !empty($origination_point)) {
					break;
				}
			}
		}
	}

	if (!empty($destination_point) && !empty($origination_point)) {
		$url = 'http://www.emspost.ru/api/rest';
		$post = array (
			'method' => 'ems.calculate',
			'from' => $origination_point,
			'to' => $destination_point,
			'weight' => $weight,
			'type' => ($weight <= 2) ? 'doc' : 'att'
		);
		if ($allow_multithreading) {
			$h_req = fn_cm_register_request('GET', $url, $post, '', '', 'text/xml');
			return array($h_req, 'fn_ems_process_result', array($ruble));
		} else {
			list($headers, $_result) = fn_https_request('GET', $url, $post);
			return fn_ems_process_result($header, $_result, $ruble, $cached_rates, $cached_rate_id);
		}
	}

	return false;
}

function fn_ems_process_result($header, $result, $ruble, &$cached_rates = null, $cached_rate_id = null)
{
	$cost = fn_from_json($result, true);

	if (!empty($cost['rsp']['price'])) {
		if (empty($cached_rates[$cached_rate_id]) && !empty($cost['rsp']['price'])) {
			$cached_rates[$cached_rate_id] = $cost['rsp']['price'];
		}
		return array('cost' => $cost['rsp']['price']);
	} else {
		if (defined('SHIPPING_DEBUG')) {
			return array('error' => ((!empty($cost['rsp']['err'])) ? $cost['rsp']['err']['code'] . ' ' . $cost['rsp']['err']['msg'] : fn_get_lang_var('service_not_available')));
		}
	}

	return false;
}

function fn_ems_convert_state($state)
{
	$convert = array (
		'ALT' => 'region--altajskij-kraj',
		'AMU' => 'region--amurskaja-oblast',
		'ARK' => 'region--arhangelskaja-oblast',
		'ARK' => 'region--astrahanskaja-oblast',
		'BEL' => 'region--belgorodskaja-oblast',
		'BRY' => 'region--brjanskaja-oblast',
		'CE' => 'region--chechenskaja-respublika',
		'CHE' => 'region--cheljabinskaja-oblast',
		'CHU' => 'region--chukotskij-ao',
		'CU' => 'region--chuvashskaja-respublika',
		'YEV' => 'region--evrejskaja-ao',
		'KHA' => 'region--khabarovskij-kraj',
		'KHM' => 'region--khanty-mansijskij-ao',
		'IRK' => 'region--irkutskaja-oblast',
		'IVA' => 'region--ivanovskaja-oblast',
		'YAN' => 'region--yamalo-neneckij-ao',
		'YAR' => 'region--yaroslavskaja-oblast',
		'KB' => 'region--kabardino-balkarskaja-respublika',
		'KGD' => 'region--kaliningradskaja-oblast',
		'KLU' => 'region--kaluzhskaja-oblast',
		'KAM' => 'region--kamchatskij-kraj',
		'KC' => 'region--karachaevo-cherkesskaja-respublika',
		'KEM' => 'region--kemerovskaja-oblast',
		'KIR' => 'region--kirovskaja-oblast',
		'KOS' => 'region--kostromskaja-oblast',
		'KDA' => 'region--krasnodarskij-kraj',
		'KIA' => 'region--krasnojarskij-kraj',
		'KGN' => 'region--kurganskaja-oblast',
		'KRS' => 'region--kurskaja-oblast',
		'LEN' => 'region--leningradskaja-oblast',
		'LIP' => 'region--lipeckaja-oblast',
		'MAG' => 'region--magadanskaja-oblast',
		'MOS' => 'region--moskovskaja-oblast',
		'MOW' => 'region--moskovskaja-oblast',//the same as for moskovskaja oblast because ems does not provide a different code for moscow
		'MUR' => 'region--murmanskaja-oblast',
		'NEN' => 'region--neneckij-ao',
		'NIZ' => 'region--nizhegorodskaja-oblast',
		'NGR' => 'region--novgorodskaja-oblast',
		'NVS' => 'region--novosibirskaja-oblast',
		'OMS' => 'region--omskaja-oblast',
		'ORE' => 'region--orenburgskaja-oblast',
		'ORL' => 'region--orlovskaja-oblast',
		'PNZ' => 'region--penzenskaja-oblast',
		'PER' => 'region--permskij-kraj',
		'PRI' => 'region--primorskij-kraj',
		'PSK' => 'region--pskovskaja-oblast',
		'AD' => 'region--respublika-adygeja',
		'AL' => 'region--respublika-altaj',
		'BA' => 'region--respublika-bashkortostan',
		'BU' => 'region--respublika-burjatija',
		'DA' => 'region--respublika-dagestan',
		'KK' => 'region--respublika-khakasija',
		'IN' => 'region--respublika-ingushetija',
		'KL' => 'region--respublika-kalmykija',
		'KR' => 'region--respublika-karelija',
		'KO' => 'region--respublika-komi',
		'ME' => 'region--respublika-marij-el',
		'MO' => 'region--respublika-mordovija',
		'SA' => 'region--respublika-saha-yakutija',
		'SE' => 'region--respublika-sev.osetija-alanija',
		'TA' => 'region--respublika-tatarstan',
		'TY' => 'region--respublika-tyva',
		'RYA' => 'region--rjazanskaja-oblast',
		'ROS' => 'region--rostovskaja-oblast',
		'SAK' => 'region--sahalinskaja-oblast',
		'SAM' => 'region--samarskaja-oblast',
		'SPE' => 'region--leningradskaja-oblast',//the same as for leningradskaya oblast because ems does not provide a different code for st. petersburg
		'SAR' => 'region--saratovskaja-oblast',
		'SMO' => 'region--smolenskaja-oblast',
		'STA' => 'region--stavropolskij-kraj',
		'SVE' => 'region--sverdlovskaja-oblast',
		'TAM' => 'region--tambovskaja-oblast',
		'TYU' => 'region--tjumenskaja-oblast',
		'TOM' => 'region--tomskaja-oblast',
		'TUL' => 'region--tulskaja-oblast',
		'TVE' => 'region--tverskaja-oblast',
		'UD' => 'region--udmurtskaja-respublika',
		'ULY' => 'region--uljanovskaja-oblast',
		'VLA' => 'region--vladimirskaja-oblast',
		'VGG' => 'region--volgogradskaja-oblast',
		'VLG' => 'region--vologodskaja-oblast',
		'VOR' => 'region--voronezhskaja-oblast',
		'ZAB' => 'region--zabajkalskij-kraj',
	);

	return !empty($convert[$state]) ? $convert[$state] : '';
}
?>