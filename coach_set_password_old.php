<?php 
	include 'mydb.php';

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$password = $_POST['password'];
		$confirm_password = $_POST['confirm_password'];
		if($password == $confirm_password){
			session_start();
			$coach_id = $_SESSION['user_id'];
			
			$sql = "update student set password=md5('{$password}') where coach_id={$coach_id}";
			mysql_query($sql);
?>
	<script type="text/javascript">
		alert('Password Reset Success.');
		location.href = 'play.php';
	</script>
<?php
		}else{
?>
	<script>alert('Password not match!');</script>
<?php
		}
	}
 ?>

<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<form method="POST">
<div class="panel panel-primary" style="width:30%; margin:auto;">
	<div class="panel-heading">
		Change Playbook Password
	</div>
	<div class="panel-body" style="text-align:center;">
		<input name="password" class="form-control" placeholder="Password" type="password" style="margin-top:10px;">
		<input name="confirm_password" class="form-control" placeholder="Confirm Password" type="password" style="margin-top:10px;">
		<button class="btn btn-primary" style="margin-top:15px; width:30%;">Save</button>
	</div>
</div>
</form>