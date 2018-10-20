<?php 
	session_start();
	if(!isset($_SESSION['user_id'])){
		header('Location: /play.php');
		return;
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		include('mydb.php');
		$coach_id = $_SESSION['user_id'];
		$name = $_POST['name'];
		$email = $_POST['email'];

		// check whether this coach has other students
		$sql = "select * from student where coach_id={$coach_id}";
		$res = mysql_query($sql);
		if($row = mysql_fetch_array($res)){
			// use his other students' password for this new student
			$password = $row['password'];
		}
		// use his own password
		else{
			$sql = "select * from users where id={$coach_id}";
			$res = mysql_query($sql);
			$row = mysql_fetch_array($res);
			$password = md5($row['pass']);
		}

		$sql = "insert into student(name, email, password, coach_id) values('{$name}', '{$email}', '{$password}', {$coach_id});";
		if(mysql_query($sql)):
?>
	<script type="text/javascript">
		alert('Add Student Success');
		location.href = 'coach_student_list.php';
	</script>
<?php
		endif;
	}
 ?>

<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<div class="container">
	<div class="panel panel-primary" style="width:40%; margin:auto;">
		<div class="panel-heading">Add Player</div>
		<div class="panel-body">
			<form method="POST">
				<input name="name" class="form-control" placeholder="Name">
				<input name="email" class="form-control" placeholder="Email" style="margin-top:10px;">
				<div style="text-align:center;">
					<button class="btn btn-success" style="width:30%; margin-top:20px;">Save</button>
				</div>
			</form>
		</div>
	</div>
	
</div>
