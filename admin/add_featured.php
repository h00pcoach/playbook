<?php

    require_once('../mydb_pdo.php');

    $type = $_POST['type'];
    $id = $_POST['id'];

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $conn->exec("set names utf8");

    if ($type == 'featured_user')
    {
        $sql = "UPDATE users SET featured = 1 WHERE id = :id";
        $st = $conn->prepare( $sql );
		$st->bindValue( ":id", $id, PDO::PARAM_INT );
		$st->execute();
        $conn = null;
    }
    elseif ('featured_play')
    {
        $sql = "UPDATE playdata SET featured = 1 WHERE id = :id";
        $st = $conn->prepare( $sql );
		$st->bindValue( ":id", $id, PDO::PARAM_INT );
		$st->execute();
        $conn = null;
    }

    header('Location: manage-featured.php');


?>
