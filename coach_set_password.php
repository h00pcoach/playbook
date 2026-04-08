<?php
    require('mydb_pdo.php');
    require_once('csrf.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        verify_csrf();

        $password         = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password == $confirm_password) {
            session_start();

            // Initialize PDO
            $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            $conn->exec("set names utf8");

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $coach_id        = $_SESSION['user_id'];

            $sql = "UPDATE student SET password = :password WHERE coach_id = :coach_id";
            $st  = $conn->prepare($sql);
            $st->bindValue(":password",  $hashed_password, PDO::PARAM_STR);
            $st->bindValue(":coach_id",  $coach_id,        PDO::PARAM_INT);
            $st->execute();
            $conn = null;
?>
    <script type="text/javascript">
        alert('Password Reset Success.');
        location.href = 'play.php';
    </script>
<?php
        } else {
?>
            <script>alert('Password not match!');</script>
<?php
        }
    } else {
        session_start();
    }
?>

<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<form method="POST">
<?php echo csrf_field(); ?>
<div class="panel panel-primary" style="width:30%; margin:auto;">
    <div class="panel-heading">
        Change Playbook Password
    </div>
    <div class="panel-body" style="text-align:center;">
        <input name="password" class="form-control" placeholder="Password" type="password" style="margin-top:10px;">
        <input name="confirm_password" class="form-control" placeholder="Confirm Password" type="password" style="margin-top:10px;">
        <button class="btn btn-primary" style="margin-top:15px; width:30%;">Save</button>
    </div>
</div>
</form>
