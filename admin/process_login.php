<?php session_start();

    include("password.php");
    require_once('../mydb_pdo.php');

    if (!isset($_POST['username']) || !isset($_POST['password']))
    {
        $results['error'] = 'Please provide a valid username and password.';
        require( 'login.php' );
    }

    $email = $_POST['username'];
    $password = $_POST['password'];

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
	$sql = "SELECT * FROM admin WHERE email = :email LIMIT 1";
	$st = $conn->prepare( $sql );
	$st->bindValue( ":email", $email, PDO::PARAM_STR );
	$st->execute();
	$row = $st->fetch();
	$conn = null;

	$results = array();
	$error = '';
	$ok = true;


	// if there is an account for this email validate password
	if ($row)
	{
		// ////ChromePhp::log('passwordVerify: ', password_verify($password, $row['password']));
		if (password_verify($password, $row['password']))
		{
            $_SESSION['admin'] = 'admin';

		} else {

			$ok = false;
			$error = 'Login error. Please try again.';
		}
	} else { // if no account exists return error

		$ok = false;
        $error = 'Login error. Please try again.';
	}

	if ($ok)
    {
        header('Location: index.php');

	} else {

        $results['error'] = $error;
        require( 'login.php' );

    }

?>
