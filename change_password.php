<?php
session_start();
require_once('mydb_pdo.php');
require_once('env.php');
require_once('csrf.php');

$error   = '';
$success = false;

function verify_reset_token(string $token): ?int {
    $parts = explode('.', $token, 2);
    if (count($parts) !== 2) return null;
    [$payload, $sig] = $parts;

    $secret       = env('APP_SECRET');
    $expected_sig = hash_hmac('sha256', $payload, $secret);
    if (!hash_equals($expected_sig, $sig)) return null;

    $decoded = base64_decode($payload);
    [$user_id, $expires] = array_pad(explode(':', $decoded, 2), 2, 0);
    if (time() > (int)$expires) return null;

    return (int)$user_id;
}

$token   = $_GET['token'] ?? '';
$user_id = verify_reset_token($token);

if (!$user_id) {
    $error = 'This reset link is invalid or has expired.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password)) {
        $error = 'Password cannot be empty.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $conn->exec("set names utf8");

        $hash = password_hash($new_password, PASSWORD_BCRYPT);
        $sql  = "UPDATE users SET encrypted_password = :hash WHERE id = :id";
        $st   = $conn->prepare($sql);
        $st->bindValue(":hash", $hash,    PDO::PARAM_STR);
        $st->bindValue(":id",   $user_id, PDO::PARAM_INT);
        $st->execute();
        $conn    = null;
        $success = true;
    }
}
?>
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-offset-3 col-sm-6">
            <div class="panel panel-primary" style="margin-top:150px;">
                <div class="panel-heading">Set New Password</div>
                <div class="panel-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success">Password updated. <a href="/play.php">Log in</a></div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php else: ?>
                        <form method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="password" class="form-control" name="new_password" placeholder="New Password" required>
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" style="margin-top:10px;" required>
                            <button class="btn btn-primary" style="width:100%; margin-top:20px;">Set Password</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
