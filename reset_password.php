<?php
	require('mydb_pdo.php');
	session_start();
	if(isset($_SESSION['user_id'])){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$user_id = $_SESSION['user_id'];
			$old_password = $_POST['old_password'];
			$new_password = $_POST['new_password'];
			$confirm_password = $_POST['confirm_password'];

			// Initialize PDO
			$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
			$conn->exec("set names utf8");

			$sql = "SELECT * FROM users WHERE id = :user_id LIMIT 1";
			$st = $conn->prepare( $sql );

			// Bind parameters
			$st->bindValue( ":user_id", $user_id, PDO::PARAM_INT );
			$st->execute();
			$row = $st->fetch();
			$conn = null;

			// $res = mysql_query("SELECT * FROM users WHERE id = :user_id");
			// $row = mysql_fetch_array($res);

			// check whether old password is matched
			if(md5($row['email'].$old_password) != $row['encrypted_password'])
			{
?>
	<script type="text/javascript">alert('Old password not matched');</script>
<?php
			}
			// check whether new password matched
			else if($new_password != $confirm_password)
			{
?>
	<script type="text/javascript">alert('New password not matched');</script>
<?php
			}
			else if(empty($new_password))
			{
?>
	<script type="text/javascript">alert('Password is empty');</script>
<?php
			}
			// update password
			else
			{
				// Initialize PDO
				$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
				$conn->exec("set names utf8");

				$new_password_hash = md5($row['email'].$new_password);
				$sql = "UPDATE users SET encrypted_password = :new_password_hash WHERE id = :user_id";
				$st = $conn->prepare( $sql );

				// Bind parameters
				$st->bindValue( ":new_password_hash", $new_password_hash, PDO::PARAM_STR );
				$st->bindValue( ":user_id", $user_id, PDO::PARAM_INT );
				$success = $st->execute();

				$conn = null;
				if($success):
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
	else {
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
