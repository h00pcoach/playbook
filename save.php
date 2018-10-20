<?php

	require_once('mydb_pdo.php');
	//( "ChromePhp.php" );

session_start();
if (ob_get_contents()) ob_clean();

//ChromePhp::log('SAVE PHP!');

// Initialize PDO
$conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );


//ChromePhp::log('post data? ', isset($_POST['data']));
// //ChromePhp::log('conn? ', $conn);

if(isset($_POST['data']))
{
	$nm='';$imm='';$id='';$err='';

	$tags = $_POST['tags'];
	$scout = $_POST['scout'];


	// if this is a new play
	if(!isset($_POST['id']))
	{

		// unpaid user can have up-to-5 plays
		$userid = $_SESSION['user_id'];

		// //ChromePhp::log('Save new play for user: ', $userid);

		// $sql = "select * from playdata where userid = :uid";
		// $r = mysql_query($sql);
		// $count = mysql_num_rows($r);

		$sql = "SELECT COUNT(*) FROM playdata WHERE userid = :userid";

		$st = $conn->prepare( $sql );
		$st->bindValue( ":userid", $userid, PDO::PARAM_INT );
		$st->execute();
		$count = $st->fetch();

		// $sql = "SELECT FOUND_ROWS() AS totalRows";
		// $count = $conn->query( $sql )->fetch();

		// //ChromePhp::log('save playdata results? ', $results);
		// //ChromePhp::log('save playdata count? ', $count);

		if($count[0] >= 100)
		{
			echo '<server><status>0</status><msg>Unpaid user can only have up to 5 plays.</msg></server>';
			return;
		}

		$time=time();
		$imm=$time;
		$nm="users/".$_SESSION['user_id']."/".$time.'.json';
		if (!file_exists("users/".$_SESSION['user_id']."/"))
		{
	        mkdir("users/".$_SESSION['user_id'], 0755);
			if(intval($_POST['cat']) == 0)
			{
				$_POST['cat'] = 9;
			}
		}

		// Insert playdata into database
		$query_data = "INSERT INTO playdata
		(name, movements, comments, file, catid, userid, upid, downid, created_on, tags, scout, private)
		VALUES
		(:name, :names, :comment, :the_time, :cat, :userid, 0, 0, NOW(), :tags, :scout, :private)";

		$st = $conn->prepare( $query_data );

		// Bind parameters
		$st->bindValue( ":name", $_POST['name'], PDO::PARAM_STR );
		$st->bindValue( ":names", $_POST['names'], PDO::PARAM_STR );
		$st->bindValue( ":comment", $_POST['comment'], PDO::PARAM_STR );
		$st->bindValue( ":the_time", $time, PDO::PARAM_STR );
		$st->bindValue( ":cat", $_POST['cat'], PDO::PARAM_INT );
		$st->bindValue( ":userid", $_SESSION['user_id'], PDO::PARAM_INT );
		$st->bindValue( ":tags", $tags, PDO::PARAM_STR );
		$st->bindValue( ":scout", $scout, PDO::PARAM_STR );
		$st->bindValue( ":private", $_POST['private'], PDO::PARAM_INT );

		$row_cat = $st->execute();
		if (!$row_cat)
		{
			$err = true;
		}
		$id = $conn->lastInsertId();

	}
	else { // This is an existing play that needs to be updated

		// Grab the file from the database
		$query = "SELECT file FROM playdata WHERE id = :id";

		$st = $conn->prepare( $query );
		$st->bindValue( ":id", $_POST['id'], PDO::PARAM_INT );
		$st->execute();

		$q = $st->fetch();

		// $q = mysql_fetch_assoc($query);

		$imm = $q['file'];

		// Create to the json file
		$nm = "users/".$_SESSION['user_id']."/".$q['file'].'.json';

		// Update playdata in the database
		$query_data="UPDATE playdata
		SET movements = :names,
		comments = :comment,
		tags = :tags,
		scout = :scout,
		private = :private
		WHERE id = :id";

		$st = $conn->prepare( $query_data );

		// Bind parameters
		$st->bindValue( ":names", $_POST['names'], PDO::PARAM_STR );
		$st->bindValue( ":comment", $_POST['comment'], PDO::PARAM_STR );
		$st->bindValue( ":tags", $tags, PDO::PARAM_STR );
		$st->bindValue( ":scout", $scout, PDO::PARAM_STR );
		$st->bindValue( ":private", $_POST['private'], PDO::PARAM_INT );
		$st->bindValue( ":id", $_POST['id'], PDO::PARAM_INT );

		$row_cat = $st->execute();
		if (!$row_cat)
		{
			$err = true;
		}
		$id = $_POST['id'];
	}

	chmod("users/{$_SESSION['user_id']}", 0777);
	$write=file_put_contents($nm,json_encode($_POST['data']));
	chmod($nm, 0644);
	if(isset($_POST['img']))
	{
		$img=explode(',',$_POST['img']);
		$j=1;
		foreach($img as $i)
		{
			$im=imagecreatefromstring(base64_decode($i));
			imagejpeg($im,"users/".$_SESSION['user_id']."/".$imm.'_'.($j++).'.jpeg');
		}
	}
	if($write && $err=='') {
		echo '<server><status>1</status><id>'.$id.'</id><msg>Saved successfully.</msg></server>';
	}
	else {
		echo '<server><status>0</status><msg>There is an error while saving. Please try again later.'.$err.'</msg></server>';
	}
}
if(isset($_POST['img']))
{
	$img = explode(',',$_POST['img']);
	if (isset($_POST['no']) && isset($_POST['path']))
	{
		$j = $_POST['no'];
		foreach($img as $i)
		{
			$im = imagecreatefromstring(base64_decode($i));
			imagejpeg($im,"users/".$_SESSION['user_id']."/".$_POST['path'].'_'.($j++).'.jpeg');
		}
	}
}
if(isset($_POST['rate1']))
{
	$query = "SELECT upid,downid FROM playdata WHERE id = :id";
	$st = $conn->prepare( $query );
	$st->bindValue( ":id", $_POST['id'], PDO::PARAM_INT );
	$st->execute();

	$q = $st->fetch();

	if($_POST['rate'] == 'ThumbsUp')
	{
		// $q.$_SESSION['user_id']

		$updownid = $q.$_SESSION['user_id'].'`';

		// $_POST['id']
		$query_data = "UPDATE playdata SET thumbsup = thumbsup+1, upid = :updownid WHERE id = :id";

	} else {

		$query_data = "UPDATE playdata SET thumbsdown = thumbsdown+1, downid = = :updownid WHERE id = :id";
	}

	$st = $conn->prepare( $query_data );
	$st->bindValue( ":updownid", $updownid, PDO::PARAM_INT );
	$st->bindValue( ":id", $_POST['id'], PDO::PARAM_INT );

	$row_cat = $st->execute();
	echo 'Rated '.$_POST['rate'].'.';
}
if(isset($_POST['rate']))
{
	$query = "SELECT rate,rated,ratecount FROM playdata WHERE id = :id";
	$st = $conn->prepare( $query );
	$st->bindValue( ":id", $_POST['id'], PDO::PARAM_INT );
	$st->execute();

	$q = $st->fetch();
	$qq = $q['rated'];

	if(intval($q['ratecount']) == 0)
	{
		$qq = ';';
		$rate = $_POST['rate'];
	}
	else {
		$rate = ($_POST['rate']+$q['rate'])/2;
	}

	if(isset($_SESSION['user_id']))
	{
		$rated = $qq.$_SESSION['user_id'].';';

	}
	else {

		$rated = ';';
	}

	$query_data = "UPDATE playdata SET rate = :rate, ratecount = ratecount+1, rated = :rated WHERE id = :id";

	$st = $conn->prepare( $query_data );
	$st->bindValue( ":rate", $rate, PDO::PARAM_INT );
	$st->bindValue( ":rated", $rated, PDO::PARAM_INT );
	$st->bindValue( ":id", $_POST['id'], PDO::PARAM_INT );

	$row_cat = $st->execute();
	if (!$row_cat)
	{
		$err = true;
	}
	echo 'Rated '.$_POST['rate'].'.';
}
if(isset($_POST['remove']))
{
	// //ChromePhp::log('Remove play: ' . $_POST['id'] . ' userid: ' . $_SESSION['user_id']);

	$query_data = "SELECT * FROM playdata WHERE id = :id AND userid = :userid";

	$st = $conn->prepare( $query_data );

	$st->bindValue( ":id", $_POST['id'], PDO::PARAM_INT );
	$st->bindValue( ":userid", $_SESSION['user_id'], PDO::PARAM_INT );
	$st->execute();

	$res = $st->fetch();

	// //ChromePhp::log('Remove play: ' . $_POST['id'] . ' userid: ' . $_SESSION['user_id']);
	//ChromePhp::log('Remove response? ', $res);
	//ChromePhp::log('Remove response? ', $res['file']);

	if($res)
	{
		$file="users/".$res['userid']."/".$res['file'];
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
		$query_data = "DELETE FROM playdata WHERE id = :id AND userid = :userid";

		$st = $conn->prepare( $query_data );

		$st->bindValue( ":id", $_POST['id'], PDO::PARAM_INT );
		$st->bindValue( ":userid", $_SESSION['user_id'], PDO::PARAM_INT );

		$row_cat = 	$st->execute();
	}
	if($row_cat)
	{
		echo '<server><status>1</status><msg>Successfully removed.</msg></server>';
	}
	else {

		echo '<server><status>0</status><msg>You may not have the permisssion to remove this file or the server has genrated an error.</msg></server>';
	}

}
$conn = null;
?>
