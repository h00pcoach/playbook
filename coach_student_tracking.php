<?php
	session_start();
	if(!isset($_SESSION['user_id']))
	{
		header('Location: play.php');
	}

	require('mydb_pdo.php');

	// Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	$coach_id = $_SESSION['user_id'];

	$sql = "SELECT *, DATE_FORMAT( login_time,  '%Y-%m-%d %h:%i %p' ) AS login_time FROM student_login_log log JOIN student ON student.id = log.student_id WHERE log.coach_id = :coach_id ORDER BY login_time DESC";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":coach_id", $coach_id, PDO::PARAM_INT );
	$st->execute();

	$logs = array();
	while ( $row = $st->fetch() )
	{
		$logs[] = $row;
	}

	$conn = null;

	//echo $sql;
	// $r = mysql_query($sql);
 ?>

 <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<div class="container">
	<h2>Student Login Logs</h2>
	<table class="table">
		<thead>
			<tr>
				<th>Student</th>
				<th>Login Time (UTC)</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($logs as $key => $log) { ?>
				<tr>
					<td><?=$log['email']?></td>
					<?php date_default_timezone_set('UTC'); ?>
					<td><?=date_format(date_create($log['login_time']), 'm/d/Y H:i')?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
