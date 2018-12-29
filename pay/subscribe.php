<?php 
require('../mydb_pdo.php');
// include('../pay/credentials.php');
require_once('../pay/braintree_init.php');
require_once '../vendor/braintree/braintree_php/lib/Braintree.php';

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
  if (isset($user["pay_id"])) {
    $customer = $gateway->customer()->find($user["pay_id"]);
  } else {
    $customer = $gateway->customer()->create([
      'firstName' => $_POST['first_name'],
      'lastName' => $_POST['last_name'],
      'email' => $_POST['email'],
      'phone' => $_POST['phone'],
      'paymentMethodNonce' => $nonce
    ]);
  }

  if ($customer->customer->id) {

    $payment_type = $_POST["payment_type"];
    $plan_id = $_POST['planid'];

    $pay_token = $result->customer->paymentMethods[0]->token;
    $pay_id = $result->customer->id;

    $subscription = $gateway->subscription()->create([
      'paymentMethodToken' => $pay_token,
      'planId' => $plan_id
    ]);

    if ($subscription->success) {
      $sql = "UPDATE users SET paid = 1, pay_id = :pay_id, pay_token = :pay_token, payment_type = :payment_type, subscription_id = :subscription_id WHERE id = :id";
      $st = $conn->prepare($sql);
      $st->bindValue(":pay_id", $pay_id, PDO::PARAM_INT);
      $st->bindValue(":pay_token", $pay_token, PDO::PARAM_STR);
      $st->bindValue(":payment_type", $payment_type, PDO::PARAM_STR);
      $st->bindValue(":subscription_id", $subscription->id, PDO::PARAM_STR);
      $st->bindValue(":id", $user_id, PDO::PARAM_INT);
      $st->execute();
      $conn = null;
    } else {
      foreach ($result->errors->deepAll() as $error) {
        echo ($error->code . ": " . $error->message . "\n");
      }
      // $data["error"] = "Subscription Error: Please try again later.";
      // echo json_encode($data);
    }

  } else {
      # TODO:  These errors aren't displaying
    foreach ($subscription->errors->deepAll() as $error) {
      echo ($error->code . ": " . $error->message . "\n");
    }
    // $data["error"] = "Card Declined: Please contact your financial institution.";
    // echo json_encode($data);
  }

} else {
  header('Location: login.php');
}

// CLEANUP
$conn = null;

?>