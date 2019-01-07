<?php
require('../mydb_pdo.php');
require_once(__DIR__ . '/braintree_init.php');
require_once(__DIR__ . '/user.php');

if (!isset($_POST['user_id'])) {
  $data['error'] = 'User Id is required!';
  echo json_encode($data);
}

$user_id = $_POST['user_id'];
$user = get_user($user_id);
$customer = $gateway->customer()->find($user["pay_id"]);
$pay_token = $customer->paymentMethods[0]->token;

$result = $gateway->subscription()->update($_POST['subscription_id'], [
  // 'paymentMethodToken' => $pay_token,
  'paymentMethodNonce' => $_POST['nonce']
]);

if ($result->success) {
  $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
  $conn->exec("set names utf8");

  // Update user pay_token
  $sql = "UPDATE users SET pay_token = :pay_token WHERE id = :id";
  $st = $conn->prepare($sql);
  $st->bindValue(":id", $user_id, PDO::PARAM_INT);
  $st->bindValue(":pay_token", $pay_token, PDO::PARAM_STR);
  $st->execute();
  $conn = null;

  $data['success'] = "Payment method was updated successfully";
  echo json_encode($data);
} else {
  $data['error'] = 'There was an error updating this subscription. ' . $result->message;
  echo json_encode($data);
}
?>