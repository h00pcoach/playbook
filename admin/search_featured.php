<?php

    require_once('../mydb_pdo.php');
    //require('../ChromePhp.php');

     //ChromePhp::log('SearchFeatured!');

    if (!isset($_POST['searchString']) || !isset($_POST['type']))
    {
        print json_encode(array('error'=>'Required values are missing!'));
        exit();
    }

    // $searchString = '%'. $_POST['searchString'] .'%';
    // $searchString = $_POST['searchString'];
    $type = $_POST['type'];

     //ChromePhp::log('search_featured: ', $searchString);

    $search_limit = 20;

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $conn->exec("set names utf8");

    if ($type == 'featured_user')
    {
        $searchString = '%'. $_POST['searchString'] .'%';
        $sql = "SELECT * FROM users WHERE featured = 0 AND name LIKE :searchString OR email LIKE :searchString ORDER BY name LIMIT :search_limit";
    }
    elseif ('featured_play')
    {
        $searchString = $_POST['searchString'];
        $sql = "SELECT * FROM playdata WHERE name LIKE :searchString AND featured = 0 AND copied = 0 ORDER BY name LIMIT :search_limit";
    }

    $st = $conn->prepare( $sql );
    $st->bindValue( ":searchString", $searchString, PDO::PARAM_STR );
    $st->bindValue( ":search_limit", $search_limit, PDO::PARAM_INT );
    $st->execute();
    $conn = null;

    $results = array();
    // Loop through results and insert them into an array
    while ( $row = $st->fetch() )
    {
        $results[] = $row;
	}

     //ChromePhp::log('returning results: ', $results);

    // Return results
    echo(json_encode(array('success' => 'Success!',
                           'type' => $type,
                           'results' => $results)));

?>
