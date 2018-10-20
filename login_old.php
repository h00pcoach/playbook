<?php
	session_start();
	ob_clean();
	include('hoopcoach_secret.php');
	include('download_image.php');
	require_once('NingApi.php');
	require_once('mydb.php');
	$subdomain = 'hoopcoach';
	$ningApi = new NingApi();
	$loginEmail = $_REQUEST['mail'];
	$loginPassword = $_REQUEST['pwd'];
	/*echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";*/
	echo "<serverResponse>\n";
	$password_hash = md5($loginEmail.$loginPassword);
	$r = mysql_query("select * from users where email='{$loginEmail}' and encrypted_password='{$password_hash}'");
	if($row = mysql_fetch_array($r)){
		echo "<status>1</status>";
		$_SESSION['user_id'] = $row['id'];
	}else{
		echo "<status>0</status>";
	}
	/*
	try {
		$ningApi->setSubdomain($subdomain);
		$result = $ningApi->login($loginEmail, $loginPassword);
		$query = mysql_query("SELECT * FROM users WHERE email='". $loginEmail . "'");
		$result = mysql_fetch_assoc($query);
		$userData = $ningApi->user->fetch($userDataArgs);
		$userDataItself = $userData["entry"];
		$gender = $userDataItself["gender"];
		$isMale = ($gender == "m") ? 1 : 0;
		$authorID = $userDataItself["author"];
		$avatarPath = $userDataItself["iconUrl"];
		$outputFilePath = $authorID . ".jpg";
		$userAdditionalResources = $userData["resources"];
		$userAdditionalData = $userAdditionalResources[$authorID];
		$userHoopcoachPageURL = $userAdditionalData["url"];
		$userFullName = $userAdditionalData["fullName"];
		# If not, let's add it to the database
		if (mysql_num_rows($query)==0) {
			$creationDate = time();
			$query = mysql_query("INSERT INTO users (name, pass, email, role, ismale, hoopcoachid, avatar, hoopcoachpage) VALUES ('$userFullName', '$loginPassword', '$loginEmail', 1, $isMale, '$authorID', '$avatarPath', '$userHoopcoachPageURL');");
			$lastInsertID = mysql_insert_id();//mysql_query("SELECT MAX(ID) AS LastID FROM Users");
			//$lastInsertID = mysql_fetch_assoc($getLastInsertIDQuery);
			$query = mysql_query("SELECT * FROM users WHERE id=" . $lastInsertID);
			$result = mysql_fetch_assoc($query);
		}
		echo "<status>1</status>";
		echo "<user>";
		echo "<id>" . $result['id'] . "</id>";
		echo "<lastname>" . $result['name'] . "</lastname>";
		echo "<email>" . $result['email'] . "</email>";
		echo "<ismale>" . $result['ismale'] . "</ismale>";
		echo "<role>" . $result['role'] . "</role>";
		echo "<profileurl>" . $result['hoopcoachpage'] . "</profileurl>";
		echo "<avatarurl>" . $result['avatar'] . "</avatarurl>";
		echo "<hoopcoachid>" . $result['hoopcoachid'] . "</hoopcoachid>";
		echo "</user>";
		$_SESSION['user_id']=$result['id'];
		$getAllCategoriesInfoQuery = "SELECT * FROM category WHERE ispublic=1;";
		$getAllCategoriesInfoQueryResult = mysql_query($getAllCategoriesInfoQuery);
		echo "\t<categories>\n";
		while ($currentCategoryInfo = mysql_fetch_assoc($getAllCategoriesInfoQueryResult)) {
			echo "\t\t<category>\n";
			echo "\t\t\t<id>" . $currentCategoryInfo['id'] . "</id>\n";
			echo "\t\t\t<name>" . $currentCategoryInfo['name'] . "</name>\n";
			echo "\t\t</category>\n";
		}
		echo "\t</categories>\n";
		$getAllPlaysQuery = "SELECT * FROM playdata WHERE userid='".$result['id']."';";
		$getAllPlaysResult = mysql_query($getAllPlaysQuery);
		echo "<plays>\n";
		while ($currentPlay = mysql_fetch_assoc($getAllPlaysResult)) {
			echo "<play>\n";
			echo "<id>".$currentPlay['id']."</id>\n";
			echo "<name>".$currentPlay['name']."</name>\n";
			echo "<path>".$currentPlay['file']."</path>\n";
			echo "<category>".$currentPlay['catid']."</category>\n";
			echo "<up>".$currentPlay['thumbsup']."</up>\n";
			echo "<up>".$currentPlay['thumbsdown']."</up>\n";
			echo "</play>\n";
		}
		echo "</plays>\n";
	} catch(Exception $e) {
		// LOGIN FAILED -> RETURN LOGIN FAILED XML
		echo "<exception>" . $e . "</exception>";
		echo "<status>0</status>";
	}
	*/
	echo "</serverResponse>\n";
?>