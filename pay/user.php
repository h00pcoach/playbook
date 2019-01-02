<?php

// if (!isset($_GET['uid']) && !isset($_POST['user_id'])) {
//   header('Location: ../play.php');
// } else if (isset($_GET['uid'])) {
//   $user_id = $_GET['uid'];
// } else {
//   $user_id = $_POST['user_id'];
// }

function get_user($user_id)
{
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
  return $user;
}