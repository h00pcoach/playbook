<?php
	session_start();
	require('mydb_pdo.php');
	require_once('csrf.php');
	verify_csrf();

	include 'config.php';

	$from_id = $_REQUEST['from_id'];
	$to_user = $_REQUEST['to_user'];

	// Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	// grab user info from database
	$sql = "SELECT * from users WHERE id = :id";
	$st = $conn->prepare( $sql );
	$st->bindValue( ":id", $_SESSION['user_id'], PDO::PARAM_INT );
	$st->execute();

	$user = $st->fetch();

	// check plays count limit for free user
	$sql = "SELECT COUNT(*)
			FROM playdata
			WHERE userid = :userid";

	$st = $conn->prepare( $sql );
	$st->bindValue( ":userid", $_SESSION['user_id'], PDO::PARAM_INT );
	$st->execute();

	$totalRows = $st->fetch();

	$plays_count = $totalRows[0];

	// Check if user has reached play limit
	if($user['paid'] == 0 && $plays_count >= $play_limit)
	{
		echo json_encode(array('msg'=>"Premium membership needed for creating more than 5 plays. <br><a href=\"/premium/index.php?uid={$_SESSION['user_id']}\">Click here</a> to upgrade"));
		return;
	}

	// check if user has already copied the play
	$sql = "SELECT * FROM playdata, (SELECT * FROM playdata WHERE id = :from_id) t WHERE playdata.userid = :to_user AND playdata.name = t.name";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":from_id", $from_id, PDO::PARAM_INT );
	$st->bindValue( ":to_user", $to_user, PDO::PARAM_INT );
	$st->execute();
	$row = $st->fetch();

	if($row)
	{
		echo json_encode(array('msg'=>'This play has already been copied before.'));
		return;
	}

	// copy play
	$sql = "CREATE TEMPORARY TABLE temp_play SELECT * FROM playdata WHERE id = :from_id";

	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":from_id", $from_id, PDO::PARAM_INT );
	$create_temp = $st->execute();

	// ChromePhp::log('create_temp? ', $create_temp);
	// echo mysql_error();

	// Grab the newly inserted temp_play
	$sql = "SELECT * FROM temp_play";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->execute();
	$play = $st->fetch();

	// ChromePhp::log('play copied? ', $play);

	// Grab the json files and copy them to the users directory
	copy("users/{$play['userid']}/{$play['file']}.json", "users/{$to_user}/{$play['file']}.json");

	if($dir = opendir("users/{$play['userid']}"))
	{
		if(!file_exists("users/{$to_user}"))
		{
			mkdir("users/{$to_user}");
		}
		while(($entry = readdir($dir)) !== false)
		{
			if(strpos($entry, $play['file']) !== false)
			{
				copy("users/{$play['userid']}/{$entry}", "users/{$to_user}/{$entry}");
			}
		}
	}

	// Update the temp_play to show it was copied
	$sql = "UPDATE temp_play SET id=0, userid = :to_user, copied=1";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":to_user", $to_user, PDO::PARAM_INT );
	$update_temp = $st->execute();

	// ChromePhp::log('update_temp? ', $update_temp);

	$sql = "INSERT INTO playdata SELECT * FROM temp_play";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$insert_playdata = $st->execute();

	// ChromePhp::log('insert_playdata? ', $insert_playdata);

	// Select the newly inserted play
	$sql = "SELECT playdata.id, playdata.name, playdata.userid, category.name AS cat, category.id AS catid FROM playdata JOIN category ON (category.id = playdata.catid), (SELECT * FROM playdata WHERE id = :from_id) t WHERE playdata.userid = :to_user AND playdata.name = t.name";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":from_id", $from_id, PDO::PARAM_INT );
	$st->bindValue( ":to_user", $to_user, PDO::PARAM_INT );
	$st->execute();
	$select_playdata = $st->fetch();

	// ChromePhp::log('select_playdata? ', $select_playdata);

	// Drop the temporary table
	$sql = "DROP TABLE temp_play";
	$st = $conn->prepare( $sql );
	$drop_table = $st->execute();
	// ChromePhp::log('drop_table? ', $drop_table);

	echo json_encode(array('msg'=>'Copy Done',"data_res"=>$select_playdata));

	$conn = null;
 ?>
