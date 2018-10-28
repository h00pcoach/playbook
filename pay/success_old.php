<?php
session_start();
// include '../mydb.php';
require('../mydb_pdo.php');

include 'paypal_config.php';

function callAPI($url, $params)
{
	$ch = curl_init();

	// SANDBOX
	// curl_setopt($ch, CURLOPT_URL, "https://api-3t.sandbox.paypal.com/nvp");

	// LIVE
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

// escape token and PayerID
$token = $_GET['token'];
$token = $token;
$payer_id = $_GET['PayerID'];
$payer_id = $payer_id;

if(empty($token))
{
    echo 'Invalid token.';
    return;
}

// Initialize PDO
$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

// get username
$sql = "SELECT * FROM users WHERE pay_token = :pay_token";
$st = $conn->prepare( $sql );

// Bind parameters
$st->bindValue( ":pay_token", $token, PDO::PARAM_STR );
$st->execute();
$user = $st->fetch();

// $user = mysql_fetch_array(mysql_query($sql));

// $sql = "select * from settings";
$sql = "SELECT * FROM settings";
$st = $conn->prepare( $sql );

// Bind parameters
$st->execute();
$settings = $st->fetch();
$conn = null;
// $settings = mysql_fetch_array(mysql_query($sql));

$params = array(
'method'=>'GetExpressCheckoutDetails',
'version'=>'104.0',
'user'=>$username,
'pwd'=>$password,
'signature'=>$signature,
'TOKEN'=>$token
);
$content = callAPI($api_endpoint, $params);
// echo $content;
// echo '<br />';

// do checkout
$amount = 0.01;
$params = array(
'method'=>'DoExpressCheckoutPayment',
'version'=>'104.0',
'PAYMENTREQUEST_0_AMT'=> $amount,
'PAYMENTREQUEST_0_CURRENCYCODE'=>'USD',
'PAYMENTREQUEST_0_PAYMENTACTION'=>'Sale',
'PAYMENTREQUEST_0_NOTIFYURL'=>'https://basketballplaybook.org/pay/notify.php',
'user'=>$username,
'pwd'=>$password,
'signature'=>$signature,
'TOKEN'=>$token,
'PAYERID'=>$payer_id,
);
// $content = callAPI($api_endpoint, $params);
// echo $content;
// echo '<br />';

if(isset($_GET['affiliate']) && $_GET['affiliate'] != 0)
{
	// Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	$sql = "SELECT id FROM users WHERE pay_token = :pay_token LIMIT 1";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":pay_token", $token, PDO::PARAM_STR );
	$st->execute();
	$row = $st->fetch();
	$conn = null;

	// $userQuery = "SELECT id FROM users WHERE pay_token = :pay_token";
	// $userQuery = "SELECT id FROM users WHERE pay_token='{$token}'";
	// $resultUser = mysql_query($userQuery);
	if($row)
	{
		// Initialize PDO
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->exec("set names utf8");

		// $rowUser = mysql_fetch_assoc($resultUser);
		$org_user_id = $_GET['affiliate'];
		$new_user_id = $row['id'];

		$sql = "INSERT INTO user_affiliate ( org_user_id , new_user_id , date_created ) VALUES ( :org_user_id , :new_user_id, NOW() )";
		$st = $conn->prepare( $sql );

		// Bind parameters
		$st->bindValue( ":org_user_id", $org_user_id, PDO::PARAM_INT );
		$st->bindValue( ":new_user_id", $new_user_id, PDO::PARAM_INT );
		$st->execute();
		$conn = null;
		// $insertAffiliate = "INSERT INTO user_affiliate ( org_user_id , new_user_id , date_created ) VALUES ( :org_user_id , :new_user_id, NOW() )";
		// mysql_query($insertAffiliate);
	}
}

// Initialize PDO
$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

$sql = "UPDATE users SET paid = 1 WHERE pay_token = :pay_token";
$st = $conn->prepare( $sql );

// Bind parameters
$st->bindValue( ":pay_token", $token, PDO::PARAM_STR );
$st->execute();
$conn = null;
// mysql_query($sql);
// echo mysql_error();

// create recurring payment profile
$current_time = date('Y-m-d H:i:s');
$params = array(
	'METHOD' => 'CreateRecurringPaymentsProfile',
	'PROFILESTARTDATE' => $current_time,
	'DESC' => '$'.$settings['monthly_amount'].'/month or $'.$settings['yearly_amount'].' per year',
	'BILLINGPERIOD' => 'Year',
	'BILLINGFREQUENCY' => 1,
	'AMT' => 39.99,
	'CURRENCYCODE' => 'USD',
	// 'PAYERID' => $payer_id,
	'EMAIL' => 'gbstack08-personal2@gmail.com',
	'TOKEN'=> $token,
	'VERSION' => '104.0',
	'user' => $username,
	'pwd' => $password,
	'signature' => $signature,
	// 'L_PAYMENTREQUEST_0_ITEMCATEGORY0' => 'Digital',
	// 'L_PAYMENTREQUEST_0_NAME0' => '',
	// 'L_PAYMENTREQUEST_0_AMT0' => 49.99,
	// 'L_PAYMENTREQUEST_0_QTY0' => 1
);
if($user['payment_type'] == 'monthly'){
	$params['BILLINGPERIOD'] = 'Month';
	$params['AMT'] = $settings['monthly_amount'];
	$params['DESC'] = '$'.$settings['monthly_amount'].'/month';
	$amount = $settings['monthly_amount'];
}else if($user['payment_type'] == 'yearly'){
	$params['BILLINGPERIOD'] = 'Year';
	$params['AMT'] = $settings['yearly_amount'];
	$params['DESC'] = '$'.$settings['yearly_amount'].' per year';
	$amount = $settings['yearly_amount'];
}
$content = callAPI($api_endpoint, $params);
// echo $content;


 ?>



 <h1>Payment Success</h1>



<style type="text/css">

	span{

		border-bottom: 1px solid black;

		padding: 0px 10px 2px 10px;

		position: absolute;

		bottom: 0px;

		text-align: center;

	}

	div.row{

		position:relative;

		margin-bottom: 10px;

	}

</style>



<div style="width:50%; background:#ddd; padding:5px 20px 20px;">

	<h1>Payment Receipt</h1>



	<div>

		<div class="row">

			Received From <span style="width:30%;"><?=$user['email']?></span>



		</div>

		<div class="row">

			Amount of <span style="width:30%">$<?=$amount?></span>

		</div>

		<div class="row" style="margin-right:30%; text-align:right; margin-top:50px;">

			Date <span style="width:30%"><?=date('m/d/Y')?></span>

		</div>

	</div>

</div>

 Go back to <a href="https://basketballplaybook.org/play.php">Plays Page</a>
