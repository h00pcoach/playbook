<?php
	include '../mydb.php';
	$sql = "SELECT * from settings";
	$r = mysql_query($sql);
	$settings = mysql_fetch_array($r);

	include 'paypal_config.php';

	function getToken($str){
		preg_match('/TOKEN=([^\&]+)\&/', $str, $matches);
		return urldecode($matches[1]);
	}

	function callAPI($url, $params){
		$ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, "https://api-3t.sandbox.paypal.com/nvp");

		curl_setopt($ch, CURLOPT_URL, $url);

		$post_string = http_build_query($params);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$content = curl_exec($ch);
		curl_close($ch);

		return $content;
	}

	$payment_type = $_GET['payment_type'];

	// $amount = 0.01;
	$params = array(
		'method'=>'SetExpressCheckout',
		'L_BILLINGTYPE0' => 'RecurringPayments',
		'L_BILLINGAGREEMENTDESCRIPTION0' => '$'.$settings['monthly_amount'].'/month or $'.$settings['yearly_amount'].' per year or $'.$settings['monthly_discounted_amount'].'/month or $'.$settings['yearly_discounted_amount'].' per year',
		// 'L_PAYMENTREQUEST_0_NAME0' => 'Member Fee (First Year)',
		// 'L_PAYMENTREQUEST_0_QTY0' => 1,
		// 'L_PAYMENTREQUEST_0_AMT0' => $amount,
		// // 'L_PAYMENTREQUEST_0_NAME1' => 'Future Years',
		// // 'L_PAYMENTREQUEST_0_QTY1' => 1,
		// // 'L_PAYMENTREQUEST_0_AMT1' => 39.99,
		// 'PAYMENTREQUEST_0_AMT'=>$amount,
		// 'PAYMENTREQUEST_0_CURRENCYCODE'=>'USD',
		// 'PAYMENTREQUEST_0_PAYMENTACTION'=>'Sale',

		'returnUrl'=>'http://basketballplaybook.org/pay/success.php',
		'cancelUrl'=>'http://basketballplaybook.org/pay/cancel.php',
		'USERSELECTEDFUNDINGSOURCE' => 'CreditCard',
		'SOLUTIONTYPE' => 'Sole',
		'LANDINGPAGE' => 'Billing',
		'NOSHIPPING' => 1,
		'version'=>'104.0',
		'user'=>$username,
		'pwd'=>$password,
		'signature'=>$signature
	);
	if($payment_type == 'monthly'){
		$params['L_BILLINGAGREEMENTDESCRIPTION0'] = '$'.$settings['monthly_amount'].'/month';
	}else if($payment_type == 'yearly'){
		$params['L_BILLINGAGREEMENTDESCRIPTION0'] = '$'.$settings['yearly_amount'].' per year';
	} else if($payment_type == 'monthly_discounted'){
		$params['L_BILLINGAGREEMENTDESCRIPTION0'] = '$'.$settings['monthly_discounted_amount'].'/month';
	} else if($payment_type == 'yearly_discounted'){
		$params['L_BILLINGAGREEMENTDESCRIPTION0'] = '$'.$settings['yearly_discounted_amount'].' per year';
	}

	$content = callAPI($api_endpoint, $params);

	$token = getToken($content);

	// save token for user

	include '../mydb.php';

	$user_id = $_GET['uid'];

	$sql = "update users set pay_token='{$token}', payment_type='{$payment_type}' where id={$user_id}";

	echo $sql;

	mysql_query($sql);



	// echo curl_error($ch);







	// $payment_url = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token={$token}";

	$payment_url = "{$paypal_url}/cgi-bin/webscr?cmd=_express-checkout&token={$token}";



	header("Location: {$payment_url}");



 ?>
