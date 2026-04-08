<?php
    require('mydb_pdo.php');
    require_once('csrf.php');
    session_start();

    if (isset($_SESSION['user_id'])) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            verify_csrf();

            $user_id         = $_SESSION['user_id'];
            $old_password    = $_POST['old_password'];
            $new_password    = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Initialize PDO
            $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            $conn->exec("set names utf8");

            $sql = "SELECT * FROM users WHERE id = :user_id LIMIT 1";
            $st  = $conn->prepare($sql);
            $st->bindValue(":user_id", $user_id, PDO::PARAM_INT);
            $st->execute();
            $row  = $st->fetch();
            $conn = null;

            // verify old password with bcrypt
            if (!$row || !password_verify($old_password, $row['encrypted_password'])) {
?>
    <script type="text/javascript">alert('Old password not matched');</script>
<?php
            } elseif (empty($new_password)) {
?>
    <script type="text/javascript">alert('Password is empty');</script>
<?php
            } elseif ($new_password !== $confirm_password) {
?>
    <script type="text/javascript">alert('New password not matched');</script>
<?php
            } else {
                $conn             = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
                $conn->exec("set names utf8");
                $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

                $sql = "UPDATE users SET encrypted_password = :new_password_hash WHERE id = :user_id";
                $st  = $conn->prepare($sql);
                $st->bindValue(":new_password_hash", $new_password_hash, PDO::PARAM_STR);
                $st->bindValue(":user_id",            $user_id,           PDO::PARAM_INT);
                $success = $st->execute();
                $conn    = null;

                if ($success): ?>
    <script type="text/javascript">alert('Update password success');</script>
<?php           else: ?>
    <script type="text/javascript">alert('Update password failed');</script>
<?php           endif;
            }
        }
    } else {
        header('Location: play.php');
    }
?>

<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<div class="panel panel-primary" style="width:500px; text-align:center; margin:150px auto 0px auto;">
    <div class="panel-heading">Change Your Password</div>
    <div class="panel-body">
        <form method="POST">
            <?php echo csrf_field(); ?>
            <input type="password" class="form-control" name="old_password" placeholder="Old Password">
            <input type="password" class="form-control" name="new_password" placeholder="New Password" style="margin-top:10px;">
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" style="margin-top:10px;">
            <button class="btn btn-primary" style="width:200px; margin-top:20px;">OK</button>
        </form>
    </div>
</div>
