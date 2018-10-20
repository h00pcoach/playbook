<?php
	session_start();
	require('mydb_pdo.php');

	if(!isset($_SESSION['user_id']))
	{
		header('Location: /play.php');
		return;
	}

	// Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	$coach_id = $_SESSION['user_id'];

	$sql = "SELECT * FROM student WHERE coach_id = :coach_id AND email != 'dummy@dummy.dummy'";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":coach_id", $coach_id, PDO::PARAM_INT );
	$st->execute();

	$students = array();
	while ( $row = $st->fetch() )
	{
		$students[] = $row;
	}

	$conn = null;
?>
	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">

<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">
			Students List
			<button class="btn btn-primary" onclick="location.href='add_student.php';" style="margin-left:80%;">Add Student</button>
		</div>
		<div class="panel-body">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Email</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
			<?php
				foreach($students as $key => $student)
				{
			 ?>
					<tr>
						<td><?=$student['id']?></td>
						<td><?=$student['name']?></td>
						<td><?=$student['email']?></td>
						<td>
							<form action="https://www.hoopcoach.org/playbook/app/index.php?action=removeStudent" METHOD='POST' onsubmit="return confirmSubmit();">
	    						<input type="hidden" name="id" value="<?=$student['id']?>"/>
	    						<button type="submit" class="btn btn-danger">remove</button>
	    					</form>
						</td>
					</tr>
			<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

</div>
