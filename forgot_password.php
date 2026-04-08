<?php
session_start();
require_once('mydb_pdo.php');
require_once('env.php');
require_once('csrf.php');

/**
 * Generate a signed, expiring reset token.
 * Format: base64url(user_id:expires).HMAC
 * No extra DB column required.
 */
function make_reset_token(int $user_id): string {
    $expires  = time() + 3600; // 1 hour
    $payload  = base64_encode($user_id . ':' . $expires);
    $secret   = env('APP_SECRET');
    $sig      = hash_hmac('sha256', $payload, $secret);
    return $payload . '.' . $sig;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    $email = trim($_POST['email']);

    $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $conn->exec("set names utf8");

    $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
    $st  = $conn->prepare($sql);
    $st->bindValue(":email", $email, PDO::PARAM_STR);
    $st->execute();
    $row  = $st->fetch();
    $conn = null;

    // Always show success to prevent email enumeration
    if ($row) {
        $token     = make_reset_token((int)$row['id']);
        $reset_url = 'https://' . $_SERVER['HTTP_HOST'] . '/change_password.php?token=' . urlencode($token);

        mail(
            $email,
            'Basketball Playbook — Password Reset',
            "Click the link below to reset your password (expires in 1 hour):\n\n$reset_url\n\nIf you did not request this, ignore this email."
        );
    }
?>
    <script>alert('If that email exists, a reset link has been sent.');</script>
<?php
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
                        <?php echo csrf_field(); ?>
                        <input type="email" class="form-control" name="email" placeholder="Email">
                        <button class="btn btn-primary" style="width:100%; margin-top:20px;">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-offset-3 col-sm-6">
            <a href="/play.php">Return to Basketball Playbook</a>
        </div>
    </div>
</div>
