<?php
	require('../mydb_pdo.php');
    // require('../ChromePhp.php');

	$email = $_POST['email'];

	// Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	$sql = "UPDATE users SET paid = 0 WHERE email = :email";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->bindValue( ":email", $email, PDO::PARAM_STR );
	$success = $st->execute();

	//ChromePhp::log('success? ', $success);

	// Return results
    if ($success)
    {

		$sql = "SELECT * FROM users WHERE email = :email";
		$st = $conn->prepare( $sql );

		//ChromePhp::log('demote_member sql: ', $sql);

		// Bind parameters
		$st->bindValue( ":email", $email, PDO::PARAM_STR );
		$st->execute();
		$row = $st->fetch();

		//ChromePhp::log('demote_member returning row: ', $row);

		echo(json_encode(array('success' => 'Success!',
                               'results' => $row)));

        // echo(json_encode(array('success' => 'Success!',
        //                        'results' => $row)));

    } else {

		//ChromePhp::log('demote_member returning error!');

        echo(json_encode(array('error' => 'There was an error cancelling membership for ' . $email . '.')));

    }
	$conn = null;

?>
