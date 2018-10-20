<?php 
	session_start();
	include('mydb.php');

	if(!isset($_SESSION['user_id'])){
		header('Location: /play.php');
		return;
	}
	
	
	$user_id = $_SESSION['user_id'];
	$sql = "select * from student where coach_id={$user_id} AND email!='dummy@dummy.dummy'";
	//echo $sql;
	$r = mysql_query($sql);
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
				while($item = mysql_fetch_array($r)):
			 ?>
					<tr>
						<td><?=$item['id']?></td>
						<td><?=$item['name']?></td>
						<td><?=$item['email']?></td>
						<td>
							<form id="cancelARB-form" action="https://www.hoopcoach.org/playbook/app/index.php?action=removeStudent" METHOD='POST' onsubmit="return confirmSubmit();">
	    						<input type="hidden" name="id" value="<?=$item['id']?>"/>
	    						<button type="submit" class="btn btn-danger">remove</button>
	    					</form>
						</td>
					</tr>
			<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
	
</div>
