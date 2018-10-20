<?php 
	session_start();
	include 'mydb.php';
	include 'config.php';

	

	$from_id = $_REQUEST['from_id'];
	$to_user = $_REQUEST['to_user'];

	// check plays count limit for free user
	$r = mysql_query("select * from users where id={$_SESSION['user_id']}");
	$user = mysql_fetch_array($r);
	$r = mysql_query("select * from playdata where userid={$_SESSION['user_id']}");
	$plays_count = mysql_num_rows($r);
	if($user['paid'] == 0 && $plays_count >= $play_limit){
		echo json_encode(array('msg'=>"Premium membership needed for creating more than 5 plays. <br><a href=\"/premium/index.php?uid={$_SESSION['user_id']}\">Click here</a> to upgrade"));
		return;
	}

	// check whether the play is already copied
	$r = mysql_query("select * from playdata, (select * from playdata where id={$from_id}) t where playdata.userid={$to_user} and playdata.name=t.name");
	if(mysql_num_rows($r)>0){
		echo json_encode(array('msg'=>'This play has already been copied before.'));
		return;
	}


	// copy play
	mysql_query("create temporary table temp_play select * from playdata where id={$from_id}");
	echo mysql_error();

	$r = mysql_query("select * from temp_play");
	$play = mysql_fetch_array($r);
	copy("users/{$play['userid']}/{$play['file']}.json", "users/{$to_user}/{$play['file']}.json");
	if($dir = opendir("users/{$play['userid']}")){
		if(!file_exists("users/{$to_user}")){
			mkdir("users/{$to_user}");
		}
		while(($entry = readdir($dir)) !== false){
			if(strpos($entry, $play['file']) !== false){
				copy("users/{$play['userid']}/{$entry}", "users/{$to_user}/{$entry}");
			}
		}
	}

	mysql_query("update temp_play set id=0, userid={$to_user}, copied=1");
	mysql_query("insert into playdata select * from temp_play");
	$r = mysql_query("SELECT playdata.id,playdata.name,playdata.userid,category.name AS cat, category.id as catid from playdata JOIN category ON (category.id= playdata.catid), (select * from playdata where id={$from_id}) t where playdata.userid={$to_user} and playdata.name=t.name");
	$mysql_respond = mysql_fetch_assoc($r);
	/*$r = mysql_query("select * from playdata, (select * from playdata where id={$from_id}) t, ");
	*/
	echo mysql_error();
	
	mysql_query("drop table temp_play");

	echo json_encode(array('msg'=>'Copy Done',"data_res"=>$mysql_respond));
 ?>