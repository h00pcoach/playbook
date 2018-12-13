<?php

require_once('../mydb_pdo.php');

if (!isset($_POST['user_id']) || !isset($_POST['pay_id'])) {
  return;
}

$id = $_POST['user_id'];
$pay_id = $_POST['pay_id'];

$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

if ($type == 'featured_user') {
  $sql = "UPDATE users SET pay_id = :pay_id WHERE id = :id";
  $st = $conn->prepare($sql);
  $st->bindValue(":pay_id", $pay_id, PDO::PARAM_INT);
  $st->bindValue(":id", $id, PDO::PARAM_INT);
  $st->execute();
  $conn = null;
}
// header('Location: manage-featured.php');

?>
