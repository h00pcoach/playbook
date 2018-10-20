<?php

	include('hoopcoach_secret.php');
	include('download_image.php');
	require_once('mydb_pdo.php');

	session_start();
	if (ob_get_contents()) ob_clean();
	$loginEmail = $_REQUEST['mail'];
	$loginPassword = $_REQUEST['pwd'];
	echo "<serverResponse>\n";

	$password_hash = md5($loginEmail.$loginPassword);

	// Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	$sql = "SELECT * FROM users WHERE email = :email AND encrypted_password = :encrypted_password";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":email", $loginEmail, PDO::PARAM_STR );
	$st->bindValue( ":encrypted_password", $password_hash, PDO::PARAM_STR );
	$st->execute();

	$row = $st->fetch();

	$conn = null;

	// $r = mysql_query("select * from users where email='{$loginEmail}' and encrypted_password='{$password_hash}'");
	if($row)
	{
		echo "<status>1</status>";
		$_SESSION['user_id'] = $row['id'];
	}else{
		echo "<status>0</status>";
	}
	echo "</serverResponse>\n";
?>
