<?php 
	session_start();
	unset($_SESSION['student_id']);
	unset($_SESSION['user_id']);
	header('Location: login.php');
 ?>