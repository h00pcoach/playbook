<?php 
	session_start();
	if(!isset($_SESSION['user_id'])){
		header('Location: play.php');
	}

	include 'mydb.php';
	$sql = "select * from student_login_log log join student on student.id=log.student_id where log.coach_id={$_SESSION['user_id']} order by login_time desc";
	//echo $sql;
	$r = mysql_query($sql);
 ?>

 <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<div class="container">
	<h2>Student Login Logs</h2>
	<table class="table">
		<thead>
			<tr>
				<th>Student</th>
				<th>Login Time</th>
			</tr>
		</thead>
		<tbody>
			<?php while($item = mysql_fetch_array($r)): ?>
				<tr>
					<td><?=$item['email']?></td>
					<td><?=date('m/d/Y H:i', strtotime($item['login_time']))?></td>
				</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
</div>
