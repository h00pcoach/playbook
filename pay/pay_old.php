<?php
	ob_start();
	// include '../mydb.php';
	require('../mydb_pdo.php');
	include 'paypal_config.php';

	// Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	$sql = "SELECT * FROM settings";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->execute();
	$settings = $st->fetch();
	$conn = null;
	// $sql = "select * from settings";

	// $r = mysql_query($sql);
	// $settings = mysql_fetch_array($r);

	function getToken($str)
	{
		preg_match('/TOKEN=([^\&]+)\&/', $str, $matches);
		return urldecode($matches[1]);
	}

	function callAPI($url, $params)
	{
		$ch = curl_init();

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
	$affiliate = isset($_GET['affiliate']) ? $_GET['affiliate'] : 0;


	// $amount = 0.01;
	$params = array(
		'method'=>'SetExpressCheckout',
		'L_BILLINGTYPE0' => 'RecurringPayments',
		'L_BILLINGAGREEMENTDESCRIPTION0' => '$'.$settings['monthly_amount'].'/month or $'.$settings['yearly_amount'].' per year or $'.$settings['monthly_discounted_amount'].'/month or $'.$settings['yearly_discounted_amount'].' per year',
		'returnUrl'=>'https://hoopcoach.org/playbook/pay/success.php?affiliate='.$affiliate,
		'cancelUrl'=>'https://hoopcoach.org/playbook/pay/cancel.php',
		'USERSELECTEDFUNDINGSOURCE' => 'CreditCard',
		'SOLUTIONTYPE' => 'Sole',
		'LANDINGPAGE' => 'Billing',
		'NOSHIPPING' => 1,
		'version'=>'104.0',
		'user'=>$username,
		'pwd'=>$password,
		'signature'=>$signature
	);
	if($payment_type == 'monthly') {
		$params['L_BILLINGAGREEMENTDESCRIPTION0'] = '$'.$settings['monthly_amount'].'/month';
	} else if($payment_type == 'yearly') {
		$params['L_BILLINGAGREEMENTDESCRIPTION0'] = '$'.$settings['yearly_amount'].' per year';
	} else if($payment_type == 'monthly_discounted') {
		$params['L_BILLINGAGREEMENTDESCRIPTION0'] = '$'.$settings['monthly_discounted_amount'].'/month';
	} else if($payment_type == 'yearly_discounted') {
		$params['L_BILLINGAGREEMENTDESCRIPTION0'] = '$'.$settings['yearly_discounted_amount'].' per year';
	}

	$content = callAPI($api_endpoint, $params);

	$token = getToken($content);

	// save token for user
	// include '../mydb.php';

	$user_id = $_GET['uid'];

	// Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	$sql = "UPDATE users SET pay_token = :pay_token, payment_type = :payment_type WHERE id = :id";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":pay_token", $token, PDO::PARAM_INT );
	$st->bindValue( ":payment_type", $payment_type, PDO::PARAM_INT );
	$st->bindValue( ":id", $user_id, PDO::PARAM_INT );
	$st->execute();
	$conn = null;

	// echo $sql;

	// mysql_query($sql);

	$payment_url = "{$paypal_url}/cgi-bin/webscr?cmd=_express-checkout&token={$token}";
	// $payment_url = "{$paypal_url}/cgi-bin/webscr?cmd=_express-checkout&token={$token}";

	header("Location: {$payment_url}");
 ?>
