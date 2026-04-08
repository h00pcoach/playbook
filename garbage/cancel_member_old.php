<?php
	session_start();
	if(!isset($_SESSION['admin'])){
		header('Location: login.php');
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		include '../mydb.php';

		$email = $_POST['email'];
		$sql = "update users set paid=0 where email='{$email}';";
		if(mysql_query($sql)):
?>
	<script type="text/javascript">alert('Operation Success');</script>
<?php
		else:
?>
	<script type="text/javascript">alert('Operation Failed');</script>
<?php
		endif;
	}
 ?>

<form method="POST">
	<h2>Cancel Member</h2>
	<div>
		<input type="email" name="email" placeholder="Email Address">
	</div>
	<button>OK</button>
</form>
