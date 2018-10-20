<?php 
	session_start();
	if(!isset($_SESSION['user_id'])){
		header('Location: playbook/play.php');
		exit();
	}

	include 'mydb.php';
	$sql = "SELECT u.email, ua.date_created FROM user_affiliate ua LEFT JOIN users u ON ua.new_user_id = u.id WHERE ua.org_user_id ={$_SESSION['user_id']} ORDER BY date_created DESC";
	$r = mysql_query($sql);
 ?>

 <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<div class="container">
	<h2>My Affiliates</h2>
	<table class="table">
		<thead>
			<tr>
				<th>Affiliate Email</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			<?php while($item = mysql_fetch_array($r)): ?>
				<tr>
					<td><?=$item['email']?></td>
					<td><?=date('m/d/Y', strtotime($item['date_created']))?></td>
				</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
</div>
