<?php session_start();

    // JSON HANDLING
    function successJson($msg)
    {
        print json_encode(array('success'=>$msg));
        exit();
    }

    function errorJson($msg)
    {
        print json_encode(array('error'=>$msg));
        exit();
    }

    // LOAD THE FILE CONTAINING THE DATABASE CONNNECTION INFO ETC.
    // require( "ChromePhp.php" );
    require( "../mydb_pdo.php" );

    // GRAB THE REQUEST ACTION -- USE THIS TO DIRECT TO THE CORRECT FUNCTION
    $action = isset( $_GET['action'] ) ? $_GET['action'] : "";

    switch ( $action )
    {
        case 'password-support':
    	  	passSupport();
    	  	break;
    	case 'mail-pass-support':
    	  	mailPassSupport();
    	  	break;
    	case 'verification-sent':
    		verificationSent();
    		break;
    	case 'pass-verify':
    		passVerify();
    		break;
    	case 'pass-reset':
    	  	passReset();
    	  	break;
    	case 'update-pass':
    		updatePass();
    		break;
    	case 'confirm-pass-reset':
    		confirmPassReset();
    		break;

        default:
            passSupport();
    }

/************ PASSWORD SUPPORT ************/

    // PASSWORD SUPPORT FUNCTIONS
    function passSupport()
    {
        //ChromePhp::log('passSupport!');
        $results['title'] =  "Password Support | Hoopcoach Playbook";

        require("pass-support.php");
    }


    function mailPassSupport()
    {
        // return results variables
    	$errors = array();

    	if (!isset($_POST['email']))
    	{
            $errors[] = 'Invalid email address.  Email address is required!';
            $results = array(
    			'form_data' => array(
    				'email' => ''
    			),
    			'form_ok' => false,
    			'errors' => $errors,
    		);
    		$_SESSION['passSupport_results'] = $results;

            header('Location: pass-support.php');
    	}

    	$email = $_POST['email'];

        // Initialize PDO
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $conn->exec("set names utf8");

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $st = $conn->prepare( $sql );

        // Bind parameters
        $st->bindValue( ":email", $email, PDO::PARAM_STR );
        $st->execute();
        $user = $st->fetch();

    	if (!$user)
    	{
    		// return results variables
    		$errors[] = 'There is no account associated with this email.';

    	} else {

            $user_id = $user['id'];

    		// create variables to be inserted into the recoveris table
    		$pass_key = getToken(20);

    		$now = strtotime(_date('Y/m/d H:i:s'));
    		$exp = _date('Y/m/d H:i:s', strtotime('+3 hours', $now));

    		// Insert the User into db
    	   	$conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    	  	$sql = "INSERT INTO recoveries ( user_id, expiration, pass_key ) VALUES ( :user_id, :expiration, :pass_key )";
    	   	$st = $conn->prepare ( $sql );
            $st->bindValue( ":user_id", $user_id, PDO::PARAM_INT );
    	   	$st->bindValue( ":expiration", $exp, PDO::PARAM_STR );
    	   	$st->bindValue( ":pass_key", $pass_key, PDO::PARAM_STR );

    	   	if ($st->execute())
    	   	{
    	   		// Grab the mail template and insert replacements
    	   		$mailHTML = getURLContent("passResetMail.php");

       			$myReplacements = array ( '%%PASSKEY%%' => $pass_key,
       			                          '%%EXPDATE%%' => $exp
       			                        );

       			foreach ($myReplacements as $needle => $replacement)
                {
       			   $mailHTML = str_replace($needle, $replacement, $mailHTML);
                }

                // <!-- TODO TODO TODO: USING ADMIN EMAIL ADDRESS -->

                // Prepare email headers
                $to = strip_tags($email);

                $message = $mailHTML;
                $subject = 'Password Reset Verification | Hoopcoach Playbook';

                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                $headers .= 'From: Hoopcoach - Playbook Password Support <admin@hoopcoach.org>' . "\r\n";
                $headers .= 'Reply-To: Hoopcoach - Playbook Password Support <admin@hoopcoach.org>' . "\r\n";

                // Send the email
                $status = mail($to, $subject, $message, $headers);
                if ($status)
                {
                    $_SESSION['reset-email'] = $email;
                    $_SESSION['reset-exp'] = $exp;

                    header('Location: verification-sent.php');
                    return;
                }

    	   	}
    	}

        $conn = null;

    	if ($errors)
    	{
    		$results = array(
    			'form_data' => array(
    				'email' => $email
    			),
    			'form_ok' => false,
    			'errors' => $errors
    		);
    		$_SESSION['passSupport_results'] = $results;

    		require("pass-support.php" );
    	}

    }

    function _date($format="r", $timestamp=false, $timezone=false)
    {
        $userTimezone = new DateTimeZone(!empty($timezone) ? $timezone : 'GMT');
        $gmtTimezone = new DateTimeZone('GMT');
        $myDateTime = new DateTime(($timestamp!=false?date("r",(int)$timestamp):date("r")), $gmtTimezone);
        $offset = $userTimezone->getOffset($myDateTime);
        return date($format, ($timestamp!=false?(int)$timestamp:$myDateTime->format('U')) + $offset);
    }

    function verificationSent()
    {

    	if (!isset($_SESSION['reset-email']) || !isset($_SESSION['reset-exp']))
    	{
    		header('Location: pass-support.php');
    	}
    	$results = array();
    	$results['email'] = $_SESSION['reset-email'];
    	$results['exp'] = $_SESSION['reset-exp'];

    	$results['title'] = "Password Support | Hoopcoach Playbook";

    	require( "verificationSent.php" );

    	// remove any stored variables if needed
    	unset($_SESSION['passSupport_results']);
    	unset($_SESSION['reset-email']);
        unset($_SESSION['reset-exp']);
    }

    function crypto_rand_secure($min, $max)
    {
            $range = $max - $min;
            if ($range < 0) return $min; // not so random...
            $log = log($range, 2);
            $bytes = (int) ($log / 8) + 1; // length in bytes
            $bits = (int) $log + 1; // length in bits
            $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
            do {
                $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
                $rnd = $rnd & $filter; // discard irrelevant bits
            } while ($rnd >= $range);
            return $min + $rnd;
    }

    function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        for($i=0;$i<$length;$i++){
            $token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
        }
        return $token;
    }

    function passVerify()
    {
    	$results['title'] = "Password Support | Hoopcoach Playbook";

    	require( "pass-verify.php" );
    }

    function passReset()
    {
        if (!isset($_POST['email']))
        {
            header('Location: pass-verify.php');
        }

    	// ////// // //// ////ChromePhp::log('passReset!!');
    	// return results variables
    	$results = array();
    	$errors = array();
    	$form_ok = true;

        $email = $_POST['email'];
        $pass_key = $_POST['passKey'];

        // Initialize PDO
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $conn->exec("set names utf8");

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $st = $conn->prepare( $sql );

        // Bind parameters
        $st->bindValue( ":email", $email, PDO::PARAM_STR );
        $st->execute();
        $user = $st->fetch();

        if (!$user)
        {
            $errors[] = 'There is no account associated with this email.';

        } else {

            $user_id = $user['id'];

            // Insert the User into db
            $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );

            $sql = "SELECT *
            FROM recoveries
            WHERE user_id = :user_id
            AND pass_key = :pass_key
            AND expiration > NOW()
            LIMIT 1";

            $st = $conn->prepare( $sql );
            $st->bindValue( ":user_id", $user_id, PDO::PARAM_INT );
            $st->bindValue( ":pass_key", $pass_key, PDO::PARAM_STR );
            $st->execute();
            $row = $st->fetch();

            if ($row)
            {
                // redirect to the enter new password page
                $results = array();
                $results['user_id'] = $user_id;
                $results['pass_key'] = $pass_key;
                $results['email'] = $email;

                $results['title'] = "Password Reset | Hoopcoach Playbook";

                require( "pass-new.php" );

            } else {

                $errors[] = 'Email and passkey combo is invalid or passkey has expired!';
            }
        }

        $conn = null;

    	if ($errors)
    	{
    		$results = array(
    			'form_data' => array(
    				'email' => $email,
    				'pass_key' => $pass_key,
    			),
    			'form_ok' => false,
    			'errors' => $errors
    		);
            $results['title'] = "Password Support | Hoopcoach Playbook";
            $results['pass_key'] = $pass_key;
            $results['email'] = $email;

    		$_SESSION['passVerify_results'] = $results;

    		require( "pass-verify.php" );
    	}
    }

    function updatePass()
    {
        //ChromePhp::log('updatePass!');

    	if (!isset($_POST['user_id']) || !isset($_POST['password']))
    	{
    		header('Location: pass-verify.php');
    		return;
    	}

    	$id = $_POST['user_id'];
        $pass_key = $_POST['pass_key'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        // Initialize PDO
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $conn->exec("set names utf8");

        $sql = "SELECT * FROM users WHERE id = :id AND email = :email LIMIT 1";
        $st = $conn->prepare( $sql );

        // Bind parameters
        $st->bindValue( ":id", $id, PDO::PARAM_INT);
        $st->bindValue( ":email", $email, PDO::PARAM_STR );
        $st->execute();
        $user = $st->fetch();

        //ChromePhp::log('updatePass user? ', $user);

    	// Update the users new_client status to false
    	if ($user)
    	{
            // UPDATE USER MYSQL
            $hash = md5($email.$password);

            //$sql = "UPDATE users SET pass = :pass, encrypted_password = :hash WHERE id = :id";
            $sql = "UPDATE users SET encrypted_password = :hash WHERE id = :id";
            $st = $conn->prepare( $sql );

            // Bind parameters
            $st->bindValue( ":hash", $hash, PDO::PARAM_STR );
            //$st->bindValue( ":pass", $password, PDO::PARAM_STR );
            $st->bindValue( ":id", $id, PDO::PARAM_INT );
            $success = $st->execute();

            //ChromePhp::log('updatePass user passed updated? ', $user);
            // password was updated succesfully
            // $conn = null;
            if ($success)
            {
                // delete recoveries row
                $sql = "DELETE FROM recoveries
                WHERE user_id = :id
                AND pass_key = :pass_key
                LIMIT 1";

                $st = $conn->prepare( $sql );
                $st->bindValue( ":id", $id, PDO::PARAM_INT );
                $st->bindValue( ":pass_key", $pass_key, PDO::PARAM_STR );
                $st->execute();

                $results['title'] = "Password Reset | Hoopcoach Playbook";

                confirmPassReset();
                return;

            } else {

                $errors = array('There was an error updating your password.  Please try again later.');
            }


    	} else {

            //ChromePhp::log('updatePass user was null? ', $user);

    		$errors = array('There was an error updating your password.  Please try again later.');
    	}

        $conn = null;

    	if ($errors)
    	{
    		$results = array(
    			'form_ok' => false,
    			'errors' => $errors
    		);

            // redirect back to the enter new password page with errors
            $results['user_id'] = $id;
            $results['pass_key'] = $pass_key;

            $results['title'] = "Password Reset | Hoopcoach Playbook";

    		$_SESSION['passNew_results'] = $results;

    		require( "pass-new.php" );
    	}
    }

    function confirmPassReset()
    {
        $results['title'] = "Password Support | Hoopcoach Playbook";

    	require( "confirm-pass-reset.php" );
    }

    function getURLContent($url)
    {
        $doc = new DOMDocument;
         $doc->preserveWhiteSpace = FALSE;
         @$doc->loadHTMLFile($url);
         return $doc->saveHTML();
    }

?>
