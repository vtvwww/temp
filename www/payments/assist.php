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

if (!defined('AREA') ) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {

	$order_id = $_REQUEST['order_id'];

	if (!fn_check_payment_script('assist.php', $order_id, $processor_data)) {
		exit;
	}
	
	$order_info = fn_get_order_info($order_id);
	$pp_response = array();
	
	if ($mode == 'place_order') {
		
		header('Content-Type: text/html; charset=windows-1251');

		$view->assign('order_action', fn_get_lang_var('placing_order'));
		$page = $view->fetch('views/orders/components/placing_order.tpl');
		$page = fn_convert_encoding('UTF-8', 'cp1251', $page);
		echo $page;
		fn_flush();
		
		$current_location = Registry::get('config.current_location');
	
		$url = 'https://payments.paysecure.ru/pay/order.cfm';
	
		$post = array();

		$post['TestMode'] = $processor_data['params']['mode'] == 'L' ? 0 : 1;
		$post['Merchant_ID'] = $processor_data['params']['merchant_id'];
		$post['OrderNumber'] = $processor_data['params']['order_prefix'] . $order_id . ($order_info['repaid'] ? "_{$order_info['repaid']}" : '');
		$post['OrderAmount'] = $order_info['total'];
		//We can leave this field blank. In this case currency will be taken from payment admin panel.
		$post['OrderCurrency'] = '';
		$post['Language'] = $processor_data['params']['language'];
		$post['Delay'] = 0;
		$post['URL_RETURN'] = "$current_location/$index_script?dispatch=payment_notification.return&payment=assist&order_id=$order_id";
		$post['URL_RETURN_OK'] = "$current_location/$index_script?dispatch=payment_notification.return_ok&payment=assist&order_id=$order_id";
		$post['URL_RETURN_NO'] = "$current_location/$index_script?dispatch=payment_notification.return_no&payment=assist&order_id=$order_id";
		$post['Firstname'] = $order_info['b_firstname'];
		$post['Lastname'] = $order_info['b_lastname'];
		$post['Middlename'] = '';
		$post['Email'] = $order_info['email'];
		$post['HomePhone'] = $order_info['phone'];
		$post['Address'] = $order_info['b_address'];
		$post['Country'] = db_get_field("SELECT code_A3 FROM ?:countries WHERE code = ?s", $order_info['b_country']);
		$post['State'] = $order_info['b_state'];
		$post['City'] = $order_info['b_city'];
		$post['Zip'] = $order_info['b_zipcode'];
		$post['OrderComment'] = $order_info['notes'];
		$post['CardPayment'] = 1;
		$post['YMPayment'] = 0;
		$post['WMPayment'] = 0;
		$post['QIWIPayment'] = 0;
		$post['QIWIMtsPayment'] = 0;
		$post['QIWIMegafonPayment'] = 0;
		$post['QIWIBeelinePayment'] = 0;
		$post['AssistIDPayment'] = 0;
		$post['AssistIDPayment'] = 0;

		$page = <<<EOT
<html>
<body onLoad="javascript:document.process.submit();">
<form method="post" action="{$url}" name="process">
EOT;

		foreach ($post as $name => &$value) {
			$page .=  "<input type=\"hidden\" name=\"$name\" value=\"$value\" />\n";
		}

		$msg = fn_get_lang_var('text_cc_processor_connection');
		$msg = str_replace('[processor]', 'Assist server', $msg);
		$page .= <<<EOT
</form>
<p><div align=center>{$msg}</div></p>
</body>
</html>
EOT;
		$page = fn_convert_encoding('UTF-8', 'cp1251', $page);
		echo $page;
		exit;
	} elseif ($mode == 'return_ok') {
		$request_url = 'https://payments.paysecure.ru/orderstate/orderstate.cfm';
		$post = array();
		$post['Ordernumber'] = $_REQUEST['ordernumber'];
		$post['Merchant_ID'] = $processor_data['params']['merchant_id'];
		$post['Login'] = $processor_data['params']['login'];
		$post['Password'] = $processor_data['params']['password'];
		$post['Format'] = 3;

		$headers = array();
		$headers[] = 'Accept: text/xml ';
		$headers[] = 'User-Agent: Mozilla/4.5 [en]';
		list($a, $return) = fn_https_request('POST', $request_url, $post, '', '', '', '', '', '', $headers);

		$xml = @simplexml_load_string($return);
		$approved = false;
		$order_amount = 0;
		if (is_array($xml->order)) {
			foreach ($xml->order as $order_info) {
				if ($order_info->orderstate == 'Approved') {
					$approved = true;
					$order_amount = $order_info->orderamount;
				}
			}
		} else {
			if ($xml->order->orderstate == 'Approved') {
				$approved = true;
				$order_amount = $xml->order->orderamount;
			}
		}

		if ($approved && $order_amount  == $order_info['total']) {
			$pp_response['order_status'] = 'P';
			$pp_response['reason_text'] = fn_get_lang_var('transaction_approved');
		} else {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = fn_get_lang_var('transaction_declined');
		}
	} elseif ($mode == 'return_no') {
		$pp_response['order_status'] = 'F';
		$pp_response['reason_text'] = fn_get_lang_var('transaction_declined');
	}

	fn_finish_payment($order_id, $pp_response);
	fn_order_placement_routines($order_id);
	
	exit;
} else {
	// making redirect for right encoding (cp1251)
	$current_location = Registry::get('config.current_location');
	$url = "$current_location/$index_script?dispatch=payment_notification.place_order&payment=assist&order_id=$order_id";
	echo <<<EOT
<html>
<body onLoad="document.process.submit();">
	<form action="{$url}" method="POST" name="process">
	</form>
</html>
EOT;
	exit;
}
?>