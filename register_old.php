<?php 
	session_start();
	$affiliate = 0;
	if(isset($_GET['affiliate']) && !empty($_GET['affiliate'])){
		$affiliate = $_GET['affiliate'];
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		include('mydb.php');
		$email = mysql_escape_string($_POST['email']);
		$password = mysql_escape_string($_POST['password']);
		$affiliteid = mysql_escape_string($_POST['affiliteid']);
		$_SESSION['affiliate'] = $affiliteid;
		// check whether user is already existed
		$sql = "select * from users where email='{$email}'";
		$r = mysql_query($sql);
		if(mysql_num_rows($r) == 0){
			// save user
			$hash = md5($email.$password);
			$sql = "insert into users(email, pass, encrypted_password) values('{$email}', '{$password}', '{$hash}');";
			$status = mysql_query($sql);
			if($status == true){
				$_SESSION['user_id'] = mysql_insert_id();
				echo '<div class="alert alert-success">Registration success</div>';
				if(!isset($_GET['type'])){
					echo '<script>alert("Registration success"); location.href="/play.php";</script>';
				}else{
					echo "<script>alert(\"Registration success\"); location.href=\"/premium/index.php?uid={$_SESSION['user_id']}&affiliate={$affiliteid}\";</script>";
				}
				return;
			}else{
				echo '<div class="alert alert-danger">Registration failed.</div>';
			}
		}else{
			echo '<div class="alert alert-danger">Registration failed. The user already exists.</div>';
		}
	}
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
						<div><input name="email" placeholder="Email" class="form-control" required></div>
						<div><input type="password" name="password" id="password" placeholder="Password" class="form-control" required></div>
						<div><input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control" required></div>
			            <input type="hidden" name="affiliteid" value="<?php echo $affiliate; ?>" />
						<button class="btn btn-success" style="margin-top:20px; width:200px;">Register</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
