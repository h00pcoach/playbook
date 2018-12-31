<?php

if (!isset($_GET['uid'])) {
  header('Location: ../play.php');
}

$user_id = $_GET['uid'];

  // Initialize PDO
$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

$sql = "SELECT * from users WHERE id = :id";
$st = $conn->prepare($sql);

  // Bind parameters
$st->bindValue(":id", $user_id, PDO::PARAM_INT);
$st->execute();
$user = $st->fetch();
$conn = null;