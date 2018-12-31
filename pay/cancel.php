<?php
require('../mydb_pdo.php');
require_once(__DIR__ . '/braintree_init.php');

$subscription_id = $_POST['subscription_id'];

$user_id = $_POST['user_id'];
$result = $gateway->subscription()->cancel($subscription_id);

if ($result->success) {
  $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
  $conn->exec("set names utf8");

  // Reset user paid, pay_token, pay_id, payment_type, subscription_id
  $sql = "UPDATE users SET paid = 0, pay_id = 0, pay_token = 0, payment_type = NULL, subscription_id = NULL WHERE id = :id";
  $st = $conn->prepare($sql);
  $st->bindValue(":id", $user_id, PDO::PARAM_INT);
  $st->execute();
  $conn = null;

  $data["success"] = $subscription;
  echo json_encode($data);
} else {
  $data["error"] = "There was an error cancelling this subscription. " . $result->message;
  echo json_encode($data);
}

?>