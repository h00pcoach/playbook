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
    $row  = $st->fetch();
    $conn = null;

    if ($row && password_verify($loginPassword, $row['encrypted_password'])) {
        echo "<status>1</status>";
        $_SESSION['user_id'] = $row['id'];
    } else {
        echo "<status>0</status>";
    }

    echo "</serverResponse>\n";
?>
