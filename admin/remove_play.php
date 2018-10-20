<?php

    include "header.php";

    // //ChromePhp::log('Remove play: ' . $_POST['id'] . ' userid: ' . $_SESSION['user_id']);
    require('../mydb_pdo.php');
    // Initialize PDO
    $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $conn->exec("set names utf8");

    $sql = "SELECT * FROM playdata WHERE id = :id";

    $st = $conn->prepare( $sql );

    $st->bindValue( ":id", $_POST['id'], PDO::PARAM_INT );
    $st->execute();

    $res = $st->fetch();

    // //ChromePhp::log('Remove play: ' . $_POST['id'] . ' userid: ' . $_SESSION['user_id']);
    //ChromePhp::log('Remove response? ', $res);
    //ChromePhp::log('Remove response? ', $res['file']);

    if($res)
    {
        $file="../users/".$res['userid']."/".$res['file'];
        $nm=explode('`',$res['movements']);

        //ChromePhp::log('movements? ', $nm);
        //ChromePhp::log('movement count? ', count($nm));
        $i = 1;
        while(file_exists($file.'_'.$i.'.jpeg'))
        {
            //ChromePhp::log('unlinking file: ', $file.'_'.$i.'.jpeg');
            unlink($file.'_'.$i.'.jpeg');
            ++$i;
        }
        unlink($file.'.json');
        $sql = "DELETE FROM playdata WHERE id = :id AND userid = :userid";

        $st = $conn->prepare( $sql );

        $st->bindValue( ":id", $_POST['id'], PDO::PARAM_INT );
        $st->bindValue( ":userid", $_SESSION['user_id'], PDO::PARAM_INT );

        $success = $st->execute();


        // header('Location: manage-plays.php');
        if($success)
        {
            echo(json_encode(array('success' => 'Success!')));
        }
        else {

            echo(json_encode(array('error' => 'There was an error removing play!')));
        }
    }


?>
