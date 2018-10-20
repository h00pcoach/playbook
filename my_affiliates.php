<?php
	session_start();
	if(!isset($_SESSION['user_id'])){
		header('Location: play.php');
		exit();
	}

	require('mydb_pdo.php');

	// Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	$org_user_id = $_SESSION['user_id'];

	$sql = "SELECT u.email, ua.date_created FROM user_affiliate ua LEFT JOIN users u ON ua.new_user_id = u.id WHERE ua.org_user_id = :org_user_id ORDER BY date_created DESC";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":org_user_id", $org_user_id, PDO::PARAM_INT );
	$st->execute();

	$affiliates = array();
	while ( $row = $st->fetch() )
	{
		$affiliates[] = $row;
	}

	$conn = null;

	// $sql = "SELECT u.email, ua.date_created FROM user_affiliate ua LEFT JOIN users u ON ua.new_user_id = u.id WHERE ua.org_user_id = :org_user_id ORDER BY date_created DESC";
	// $r = mysql_query($sql);
 ?>

 <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<div class="container">
	<h2>My Affiliates</h2>
	<table class="table">
		<thead>
			<tr>
				<th>Affiliate Email</th>
				<th>Date (UTC)</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($affiliates as $key => $affiliate) { ?>
				<tr>
					<td><?=$affiliate['email']?></td>
					<?php date_default_timezone_set('UTC'); ?>
					<td><?=date('m/d/Y', strtotime($affiliate['date_created']))?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
