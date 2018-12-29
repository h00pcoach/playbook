<?php 
require('../mydb_pdo.php');
require('../ChromePhp.php');

// include('../pay/credentials.php');
require_once('../pay/braintree_init.php');
require_once '../vendor/braintree/braintree_php/lib/Braintree.php';

function errorJson($msg)
{
  print json_encode(array('error' => $msg));
  exit();
}

if (isset($_POST['userid'])) {
  $user_id = $_POST['userid'];
  $affiliate = $_POST['affiliteid'];

  // Initialize PDO
  $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
  $conn->exec("set names utf8");

  $sql = "SELECT * from users WHERE id = :id";
  $st = $conn->prepare($sql);

  // Bind parameters
  $st->bindValue(":id", $user_id, PDO::PARAM_INT);
  $st->execute();
  $user = $st->fetch();

  if ($user["paid"] == 1) {
    header('Location: ../play.php');
  }


  $nonce = $_POST['nonce'];

  $customer = null;
  if (isset($user["pay_id"]) && $user["pay_id"] != 0) {
    $customer = $gateway->customer()->find($user["pay_id"]);
  } else {
    $results = $gateway->customer()->create([
      'firstName' => $_POST['first_name'],
      'lastName' => $_POST['last_name'],
      'email' => $_POST['email'],
      'phone' => $_POST['phone'],
      'paymentMethodNonce' => $nonce
    ]);
    if ($results->success) {
      $customer = $results->customer;
    } else {
      $message = $results->verification->_processorResponseCode . ' ' . $results->verification->_processorResponseText;
      return errorJson($message);
    }
  }

  $payment_type = $_POST["payment_type"];

  // PLAN ID: TEST FAILURE
  // $plan_id = 'pb_weekly';
  $plan_id = $_POST['planid'];

  $pay_token = $customer->paymentMethods[0]->token;
  $pay_id = $customer->id;

  $results = $gateway->subscription()->create([
    'paymentMethodToken' => $pay_token,
    'planId' => $plan_id
  ]);

  if ($results->success) {
    $subscription = $results->subscription;
    $sql = "UPDATE users SET paid = 1, pay_id = :pay_id, pay_token = :pay_token, payment_type = :payment_type, subscription_id = :subscription_id WHERE id = :id";
    $st = $conn->prepare($sql);
    $st->bindValue(":pay_id", $pay_id, PDO::PARAM_INT);
    $st->bindValue(":pay_token", $pay_token, PDO::PARAM_STR);
    $st->bindValue(":payment_type", $payment_type, PDO::PARAM_STR);
    $st->bindValue(":subscription_id", $subscription->id, PDO::PARAM_STR);
    $st->bindValue(":id", $user_id, PDO::PARAM_INT);
    $st->execute();
    $conn = null;

    $data["success"] = $subscription;
    echo json_encode($data);

  } else {
    ChromePhp::log('Subscription failed: ' . $results);
    $message = $results->message;
    return errorJson($message);
  }

} else {
  echo ("User isn't logged in.");
}

// CLEANUP
$conn = null;

?>