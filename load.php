<?php
require_once('mydb_pdo.php');
// require_once('ChromePhp.php');
session_start();
if(isset($_REQUEST['id']))
{
	$conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $conn->exec("set names utf8");

    // $sql = "SELECT * FROM playdata WHERE id = :id";

	if(isset($_SESSION['user_id']))
	{
		$getAllPlaysQuery = "SELECT id, userid, name,file, thumbsup,thumbsdown, (CASE WHEN playdata.upid LIKE '%; :user_id ;%' THEN 1 else 0 end) AS `uped`, (CASE WHEN playdata.downid LIKE  :user_id THEN 1 else 0 end) AS `downed`, tags FROM playdata WHERE  id =:id";

		$user_id = "%".$_REQUEST['user_id']."%";

		$st = $conn->prepare( $getAllPlaysQuery );
		$st->bindValue( ":id", $_REQUEST['id'], PDO::PARAM_INT );
	    $st->bindValue( ":user_id", $user_id , PDO::PARAM_INT );
		$st->execute();
	    $getAllPlaysResult = $st->fetch();
	    $conn = null;
	}
	else {

		$getAllPlaysQuery = "SELECT id, userid, name,file, thumbsup,thumbsdown, -1 AS `uped`, -1 AS `downed`, tags FROM playdata WHERE  `id`= :id";

		$st = $conn->prepare( $getAllPlaysQuery );
		$st->bindValue( ":id", $_REQUEST['id'], PDO::PARAM_INT );
		$st->execute();
	    $getAllPlaysResult = $st->fetch();
	    $conn = null;
	}

	// $st = $conn->prepare( $getAllPlaysQuery );
	// $st->bindValue( ":id", $_REQUEST['id'], PDO::PARAM_INT );
    // $st->bindValue( ":user_id", $_REQUEST['user_id'], PDO::PARAM_INT );
    // $st->execute();
    // $getAllPlaysResult = $st->fetch();
    // $conn = null;
	// ChromePhp::log('getAllPlaysResult? ', $getAllPlaysResult);
	// $getAllPlaysResult = mysql_fetch_assoc($getAllPlaysQuery);
	// ChromePhp::log('movement path? users/'. $getAllPlaysResult['userid']."/".$getAllPlaysResult['file'].".json");
	$fx=(file_get_contents("users/".$getAllPlaysResult['userid']."/".$getAllPlaysResult['file'].".json"));
	// ChromePhp::log('fx? ', $fx);

	// ob_clean();
	if (ob_get_contents()) ob_clean();
	$f=array();
	$f['id']=$getAllPlaysResult['id'];
	$f['thumbsup']=$getAllPlaysResult['thumbsup'];
	$f['thumbsdown']=$getAllPlaysResult['thumbsdown'];
	$f['uped']=$getAllPlaysResult['uped'];
	$f['downed']=$getAllPlaysResult['downed'];
	$f['tags']=$getAllPlaysResult['tags'];
	$f['play']=json_decode($fx,true);

	// ChromePhp::log('$f? ', $f);
	echo (json_encode($f));
	//echo ('{"id":'.$getAllPlaysResult['id'].',"thumbsup":'.$getAllPlaysResult['thumbsup'].',"thumbsdown":'.$getAllPlaysResult['thumbsdown'].',"uped":'.$getAllPlaysResult['uped'].',"downed":'.$getAllPlaysResult['downed'].',"tags":'.$getAllPlaysResult['tags'].',"play":['.$fx.']}');
}

?>
