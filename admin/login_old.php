<?php 
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$username = $_POST['username'];
		$password = $_POST['password'];
		if($username == 'admin' && $password == 'admin'){
			session_start();
			$_SESSION['admin'] = 'admin';
			header('Location: index.php');
		}else{
?>
	<script>alert('Username or Password is incorrect');</script>
<?php
		}
	}
 ?>

<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<form method="POST">
<div class="panel panel-primary" style="width:30%; margin:auto;">
	<div class="panel-heading">
		Login
	</div>
	<div class="panel-body" style="text-align:center;">
		<input name="username" class="form-control" placeholder="Username">
		<input name="password" class="form-control" placeholder="Password" type="password" style="margin-top:10px;">
		<button class="btn btn-primary" style="margin-top:15px; width:30%;">Login</button>
	</div>
</div>
</form>