<?php

    require_once('../mydb_pdo.php');
   //require('../ChromePhp.php');

    //ChromePhp::log('Search Users!');

    if (!isset($_POST['email']))
    {
        print json_encode(array('error'=>'Required values are missing!'));
        exit();
    }

    $email = $_POST['email'];

    //ChromePhp::log('search_featured: ', $email);

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $conn->exec("set names utf8");

    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    // $sql = "SELECT * FROM users WHERE email = :email AND paid = 1 LIMIT 1";

    $st = $conn->prepare( $sql );
    $st->bindValue( ":email", $email, PDO::PARAM_STR );
    $st->execute();


    $row = $st->fetch();

    // Return results
    if ($row)
    {
        //ChromePhp::log('returning row: ', $row);

        echo(json_encode(array('success' => 'Success!',
                               'results' => $row)));
    } else {

        echo(json_encode(array('error' => 'No account found for ' . $email . '.')));

    }

    $conn = null;

?>
