<?php
require_once('mydb.php');

if(isset($_REQUEST['id'])){
	if(isset($_SESSION['user_id']))
		$getAllPlaysQuery = mysql_query("SELECT id, userid, name,file, thumbsup,thumbsdown, (CASE WHEN playdata.upid LIKE '%;".$_SESSION['user_id'].";%' THEN 1 else 0 end) AS `uped`, (CASE WHEN playdata.downid LIKE '%;".$_SESSION['user_id'].";%' THEN 1 else 0 end) AS `downed`, tags FROM playdata WHERE  `id`='".$_REQUEST['id']."';");
	else
		$getAllPlaysQuery = mysql_query("SELECT id, userid, name,file, thumbsup,thumbsdown, -1 AS `uped`, -1 AS `downed`, tags FROM playdata WHERE  `id`='".$_REQUEST['id']."';");
	$getAllPlaysResult = mysql_fetch_assoc($getAllPlaysQuery);
	$fx=(file_get_contents("users/".$getAllPlaysResult['userid']."/".$getAllPlaysResult['file'].'.json'));
	ob_clean();
	$f=array();
	$f['id']=$getAllPlaysResult['id'];
	$f['thumbsup']=$getAllPlaysResult['thumbsup'];
	$f['thumbsdown']=$getAllPlaysResult['thumbsdown'];
	$f['uped']=$getAllPlaysResult['uped'];
	$f['downed']=$getAllPlaysResult['downed'];
	$f['tags']=$getAllPlaysResult['tags'];
	$f['play']=json_decode($fx,true);
	echo (json_encode($f));
	//echo ('{"id":'.$getAllPlaysResult['id'].',"thumbsup":'.$getAllPlaysResult['thumbsup'].',"thumbsdown":'.$getAllPlaysResult['thumbsdown'].',"uped":'.$getAllPlaysResult['uped'].',"downed":'.$getAllPlaysResult['downed'].',"tags":'.$getAllPlaysResult['tags'].',"play":['.$fx.']}');
}

?>