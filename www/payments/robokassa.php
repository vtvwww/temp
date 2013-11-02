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

if (defined('PAYMENT_NOTIFICATION')) {
	if (empty($_REQUEST['InvId']) || empty($_REQUEST['OutSum']) || empty($_REQUEST['SignatureValue'])) {
		die('Access denied');
	}
	$order_id = (int) $_REQUEST['InvId'];
	if ($mode == 'result') {
		$order_info = fn_get_order_info($order_id);
		$processor_data = $order_info['payment_method']; 
		$crc = strtoupper(md5($_REQUEST['OutSum'] . ':' . $_REQUEST['InvId'] . ':' . $processor_data['params']['password2']));
		if (strtoupper($_REQUEST['SignatureValue']) == $crc) {
			$pp_response['order_status'] = 'P';
			$pp_response['reason_text'] = fn_get_lang_var('approved');
		} else {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = fn_get_lang_var('control_summ_wrong');
		}
		fn_finish_payment($order_id, $pp_response);
		die('OK' . $order_id);
	} elseif ($mode == 'return') {
		$order_info = fn_get_order_info($order_id);
		if ($order_info['status'] == 'O') {
			$pp_response = array();
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = fn_get_lang_var('merchant_response_was_not_received');
			$pp_response['transaction_id'] = '';
			fn_finish_payment($order_id, $pp_response);
		}
		fn_order_placement_routines($order_id, false);
	} elseif ($mode == 'cancel') {
		$pp_response['order_status'] = 'N';
		$pp_response['reason_text'] = fn_get_lang_var('text_transaction_cancelled');
		fn_finish_payment($order_id, $pp_response, false);
		fn_order_placement_routines($order_id);
	}
} else {

	$crc = strtoupper(md5($processor_data['params']['merchantid'] . ':' . $order_info['total']. ':' . $order_id . ':' . $processor_data['params']['password1']));
	$lang_code = CART_LANGUAGE;
	$url = ($processor_data['params']['mode'] == 'live') ? 'https://merchant.roboxchange.com/Index.aspx' : 'http://test.robokassa.ru/Index.aspx';

echo <<<EOT
<html>
<body onLoad="document.process.submit();">
<form method="post" action="{$url}" name="process">
	<input type=hidden name=MrchLogin value="{$processor_data['params']['merchantid']}">
	<input type=hidden name=OutSum value="{$order_info['total']}">
	<input type=hidden name=InvId value="{$order_id}">
	<input type=hidden name=Desc value="{$processor_data['params']['details']}">
	<input type=hidden name=SignatureValue value="{$crc}">
	<input type=hidden name=Culture value="{$lang_code}">
	<input type=hidden name=IncCurrLabel value="{$processor_data['params']['currency']}">
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'Robokassa', $msg);
echo <<<EOT
	</form>
	<div align=center>{$msg}</div>
</body>
</html>
EOT;

}
exit;
?>