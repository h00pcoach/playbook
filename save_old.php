<?php

	require_once('mydb.php');
session_start();
ob_clean();
if(isset($_POST['data'])){
	$nm='';$imm='';$id='';$err='';

	$tags = $_POST['tags'];
	$scout = $_POST['scout'];

	if(!isset($_POST['id'])){
		// unpaid user can have up-to-5 plays
		$uid = $_SESSION['user_id'];
		$sql = "select * from playdata where userid={$uid}";
		$r = mysql_query($sql);
		$count = mysql_num_rows($r);
		if($count >= 100){
			echo '<server><status>0</status><msg>Unpaid user can only have up to 5 plays.</msg></server>';
			return;
		}

		$time=time();
		$imm=$time;
		$nm="users/".$_SESSION['user_id']."/".$time.'.json';
		if (!file_exists("users/".$_SESSION['user_id']."/"))
	        mkdir("users/".$_SESSION['user_id'], 0755);
			if(intval($_POST['cat'])==0) $_POST['cat']=9;

		$query_data="INSERT INTO playdata (name, movements, comments, file, catid, userid, upid,downid,created_on,tags, scout, `private`) VALUES ('".$_POST['name']."','".$_POST['names']."','".$_POST['comment']."','".$time."','".$_POST['cat']."','".$_SESSION['user_id']."','`','`',NOW(),'{$tags}', '{$scout}', ".$_POST['private'].")";

		$row_cat=mysql_query($query_data);
		$err=mysql_error();
		$id=mysql_insert_id();
	}
	else{
		$query=mysql_query("SELECT file FROM playdata WHERE id='".$_POST['id']."'");
		$q=mysql_fetch_assoc($query);

		$imm=$q['file'];

		$nm="users/".$_SESSION['user_id']."/".$q['file'].'.json';
		$query_data="UPDATE playdata SET movements='".$_POST['names']."', comments='".$_POST['comment']."', tags='{$tags}', scout='{$scout}', `private`=".$_POST['private']." WHERE id='".$_POST['id']."'";

		$row_cat=mysql_query($query_data);
		$err=mysql_error();
		$id=$_POST['id'];
	}

	chmod("users/{$_SESSION['user_id']}", 0777);
	$write=file_put_contents($nm,json_encode($_POST['data']));
	chmod($nm, 0644);
	if(isset($_POST['img'])){
		$img=explode(',',$_POST['img']);
		$j=1;
		foreach($img as $i){
			$im=imagecreatefromstring(base64_decode($i));
			imagejpeg($im,"users/".$_SESSION['user_id']."/".$imm.'_'.($j++).'.jpeg');
		}
	}
	if($write && $err=='')
		echo '<server><status>1</status><id>'.$id.'</id><msg>Saved successfully.</msg></server>';
	else
		echo '<server><status>0</status><msg>There is an error while saving. Please try again later.'.$err.'</msg></server>';
}
if(isset($_POST['img'])){
	$img=explode(',',$_POST['img']);
	$j=$_POST['no'];
	foreach($img as $i){
		$im=imagecreatefromstring(base64_decode($i));
		imagejpeg($im,"users/".$_SESSION['user_id']."/".$_POST['path'].'_'.($j++).'.jpeg');
	}
}
if(isset($_POST['rate1'])){
	$query=mysql_query("SELECT upid,downid FROM playdata WHERE id='".$_POST['id']."'");
	$q=mysql_fetch_assoc($query);
	if($_POST['rate']=='ThumbsUp')
		$query_data="UPDATE playdata SET thumbsup=thumbsup+1, upid='".$q.$_SESSION['user_id'].'`'."' WHERE id='".$_POST['id']."'";
	else
		$query_data="UPDATE playdata SET thumbsdown=thumbsdown+1, downid='".$q.$_SESSION['user_id'].'`'."' WHERE id='".$_POST['id']."'";
	$row_cat=mysql_query($query_data);
	echo 'Rated '.$_POST['rate'].'.';
}
if(isset($_POST['rate'])){
	$query=mysql_query("SELECT rate,rated,ratecount FROM playdata WHERE id='".$_POST['id']."'");
	$q=mysql_fetch_assoc($query);
	$qq=$q['rated'];
	if(intval($q['ratecount'])==0){ $qq=';'; $rate=$_POST['rate']; }
	else{
		$rate=($_POST['rate']+$q['rate'])/2;
	}
	if(isset($_SESSION['user_id']))
		$query_data="UPDATE playdata SET rate='".$rate."', ratecount=ratecount+1, rated='".$qq.$_SESSION['user_id'].';'."' WHERE id='".$_POST['id']."'";
	else
		$query_data="UPDATE playdata SET rate='".$rate."', ratecount=ratecount+1, rated=';' WHERE id='".$_POST['id']."'";
	$row_cat=mysql_query($query_data);
	echo 'Rated '.$_POST['rate'].'.';
}
if(isset($_POST['remove'])){
	$query_data=mysql_query("SELECT * FROM playdata WHERE id=".$_POST['id']." AND userid='".$_SESSION['user_id']."'");
	$res=mysql_fetch_assoc($query_data);
	if($res){
		$file="users/".$res['userid']."/".$res['file'];
		$nm=explode('`',$res['movements']);
		while(file_exists($file.'_'.$i.'.jpeg')){
			unlink($file.'_'.$i.'.jpeg');
		}
		unlink($file.'.json');
		$query_data="DELETE FROM playdata WHERE id=".$_POST['id']." AND userid='".$_SESSION['user_id']."'";
		$row_cat=mysql_query($query_data);
	}
	if($row_cat)
		echo '<server><status>1</status><msg>Successfully removed.</msg></server>';
	else
		echo '<server><status>0</status><msg>You may not have the permisssion to remove this file or the server has genrated an error.</msg></server>';

}
?>
