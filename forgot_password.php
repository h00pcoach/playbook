<?php 
	include 'mydb.php';
	session_start();
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $email = $_POST['email'];
        $sql = "select * from users where email='{$email}'";
        $res = mysql_query($sql);
        if($item = mysql_fetch_array($res)) {
            $password = $item['pass'];
            mail($email, 'Forgotten Password Recovery', "Your password is {$password}");
?>
        <script>alert('Your password has been sent to your email box');</script>
<?php
			// header('Location: http://www.basketballplaybook.org/play.php');
		}

        else {
?>
        <script>alert('Email not existed');</script>
<?php
        }
    }
?>


<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">


<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-offset-3 col-sm-6">
            <div class="panel panel-primary" style="margin-top:150px;">
                <div class="panel-heading">Forgot Password</div>
                <div class="panel-body">
                    <form method="POST">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                        <button class="btn btn-primary" style="width:100%; margin-top:20px;">Send Password</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-offset-3 col-sm-6">
            <a href="http://www.basketballplaybook.org/play.php">Return to Basketball Playbook</a><br>
        </div>
    </div>
</div>





