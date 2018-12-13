<?php
session_start();
$affiliate = 0;
if (isset($_GET['affiliate']) && !empty($_GET['affiliate'])) {
	$affiliate = $_GET['affiliate'];
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	include('mydb_pdo.php');

		// Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

		// Retrieve posted values
	$name = $_POST['name'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$affiliteid = $_POST['affiliteid'];

		// set affiliate id
	$_SESSION['affiliate'] = $affiliteid;

	$sql = "SELECT * FROM users WHERE email = :email";
	$st = $conn->prepare($sql);

		// Bind parameters
	$st->bindValue(":email", $email, PDO::PARAM_INT);
	$st->execute();
	$row = $st->fetch();

		// if user doesn't exist
	if (!$row) {
			// save user
		$hash = md5($email . $password);

			// $sql = "insert into users(email, pass, encrypted_password) values('{$email}', '{$password}', '{$hash}');";
			// $status = mysql_query($sql);

		$sql = "INSERT INTO users(email, name, encrypted_password) VALUES(:email, :name, :encrypted_password)";
		$st = $conn->prepare($sql);

			// Bind parameters
		$st->bindValue(":email", $email, PDO::PARAM_STR);
		$st->bindValue(":name", $name, PDO::PARAM_STR);
		$st->bindValue(":encrypted_password", $hash, PDO::PARAM_STR);
		$status = $st->execute();

		if ($status) {
			$_SESSION['user_id'] = $conn->lastInsertId();

			echo '<div class="alert alert-success">Registration success</div>';

			if (!isset($_GET['type'])) {
				echo '<script>alert("Registration success"); location.href="/play.php";</script>';

			} else {

				echo "<script>alert(\"Registration success\"); location.href=\"/premium/index.php?uid={$_SESSION['user_id']}&affiliate={$affiliteid}\";</script>";
			}
			return;

		} else {

			echo '<div class="alert alert-danger">Registration failed.</div>';
		}

	} else {

		echo '<div class="alert alert-danger">Registration failed. The user already exists.</div>';
	}
}

$conn = null;
?>

<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<style type="text/css">
	.form-control{
		margin-top:10px;
	}
</style>

<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript">
	$(function(){
		$('form').validate({
			rules:{
				name:{
					required: true,

				}
				email:{
					required:true,
					email:true
				},
				password: 'required',
				confirm_password:{
					required:true,
					equalTo:'#password'
				}
			}
		})
	})
</script>

<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-offset-3 col-sm-6">
			<div class="panel panel-primary" style="margin-top: 150px;">
				<div class="panel-heading">Register</div>
				<div class="panel-body">
					<form method="POST">
						<div><input type="text" name="name" placeholder="Name" class="form-control" required></div>
						<div><input type="email" name="email" placeholder="Email" class="form-control" required></div>
						<div><input type="password" name="password" id="password" placeholder="Password" class="form-control" required></div>
						<div><input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control" required></div>
			            <input type="hidden" name="affiliteid" value="<?php echo $affiliate; ?>" />
						<button class="btn btn-success" type="submit" style="margin-top:20px; width:200px;">Register</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
