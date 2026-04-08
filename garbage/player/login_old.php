<?php 
	include '../mydb.php';

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		$sql = "select * from student where email='{$email}' and password=md5('{$password}')";
		$r = mysql_query($sql);
		if($item = mysql_fetch_array($r)):
			// login success
			session_start();
			$_SESSION['student_id'] = $item['id'];

			// update log 
			$current_time = date('Y-m-d H:i:s');
			$sql = "insert into student_login_log(student_id, login_time, coach_id) values({$item['id']}, '{$current_time}', {$item['coach_id']})";
			mysql_query($sql);

			// redirect to Playbook page
			header('Location: playbook.php');
		else:
?>
	<script type="text/javascript">alert('Login failed');</script>
<?php
		endif;
	}
 ?>

<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<form method="POST">
<div class="panel panel-primary" style="width:30%; margin:auto;">
	<div class="panel-heading">
		Player Login
	</div>
	<div class="panel-body" style="text-align:center;">
		<input name="email" class="form-control" placeholder="Email">
		<input name="password" class="form-control" placeholder="Password" type="password" style="margin-top:10px;">
		<button class="btn btn-primary" style="margin-top:15px; width:30%;">Login</button>
	</div>
</div>
</form>