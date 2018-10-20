<?php 
	session_start();
	include 'mydb.php';
	
	if(isset($_SESSION['user_id'])){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$user_id = $_SESSION['user_id'];
			$old_password = $_POST['old_password'];
			$new_password = $_POST['new_password'];
			$confirm_password = $_POST['confirm_password'];
			$res = mysql_query("select * from users where id={$user_id}");
			$row = mysql_fetch_array($res);

			// check whether old password is matched
			if(md5($row['email'].$old_password) != $row['encrypted_password']){
?>
	<script type="text/javascript">alert('Old password not matched');</script>
<?php
			}
			// check whether new password matched
			else if($new_password != $confirm_password){
?>
	<script type="text/javascript">alert('New password not matched');</script>
<?php
			}
			else if(empty($new_password)){
?>
	<script type="text/javascript">alert('Password is empty');</script>
<?php
			}
			// update password
			else{
				$new_password_hash = md5($row['email'].$new_password);
				$sql = "update users set pass='{$new_password}', encrypted_password='{$new_password_hash}' where id={$user_id}";
				if(mysql_query($sql)):
?>
	<script type="text/javascript">alert('Update password success');</script>
<?php
				else:
?>
	<script type="text/javascript">alert('Update password failed');</script>
<?php
				endif;
			}
		}
	}
	// redirect to login page if user is not logged in
	else{
		header('Location: play.php');
	}
?>


<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<div class="panel panel-primary" style="width:500px; text-align:center; margin:150px auto 0px auto;">
    <div class="panel-heading">Change Your Password</div>
    <div class="panel-body">
        <form method="POST">
            <input type="password" class="form-control" name="old_password" placeholder="Old Password">
            <input type="password" class="form-control" name="new_password" placeholder="New Password" style="margin-top:10px;">
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" style="margin-top:10px;">
            <button class="btn btn-primary" style="width:200px; margin-top:20px;">OK</button>
        </form>
    </div>
</div>