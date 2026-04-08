<?php
    include('download_image.php');
    require_once('mydb_pdo.php');
    require_once('csrf.php');

    session_start();
    if (ob_get_contents()) ob_clean();

    verify_csrf();

    $loginEmail    = $_REQUEST['mail'];
    $loginPassword = $_REQUEST['pwd'];

    echo "<serverResponse>\n";

    // Initialize PDO
    $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $conn->exec("set names utf8");

    // Fetch user by email only; verify password hash separately
    $sql = "SELECT * FROM users WHERE email = :email";
    $st  = $conn->prepare($sql);
    $st->bindValue(":email", $loginEmail, PDO::PARAM_STR);
    $st->execute();
    $row = $st->fetch();

    $authenticated = false;

    if ($row) {
        if (password_verify($loginPassword, $row['encrypted_password'])) {
            // Already on bcrypt — normal path
            $authenticated = true;
        } elseif ($row['encrypted_password'] === md5($loginEmail . $loginPassword)) {
            // Legacy MD5 hash — re-hash with bcrypt on the way in
            $authenticated    = true;
            $newHash          = password_hash($loginPassword, PASSWORD_BCRYPT);
            $upd = $conn->prepare("UPDATE users SET encrypted_password = :hash WHERE id = :id");
            $upd->bindValue(":hash", $newHash,    PDO::PARAM_STR);
            $upd->bindValue(":id",   $row['id'],  PDO::PARAM_INT);
            $upd->execute();
        }
    }

    $conn = null;

    if ($authenticated) {
        echo "<status>1</status>";
        $_SESSION['user_id'] = $row['id'];
    } else {
        echo "<status>0</status>";
    }

    echo "</serverResponse>\n";
?>
