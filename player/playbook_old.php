<?php 
	session_start();
	include '../mydb.php';	
	
	
	// Get the id of a student of this coach
	function getOneStudentOfTheCoach ($coach_id)
	{
		$sql = "select * from student where coach_id={$coach_id}";
		$r = mysql_query($sql);
		while($item = mysql_fetch_array($r))
			$student_id = $item['id'];	
		
		return $student_id;		
	}
	
	if (isset($_SESSION['user_id']))
	{
		$coach_id = $_SESSION['user_id'];
		// coach came here ... so get his student_id		
		$student_id = getOneStudentOfTheCoach ($coach_id);
		
		if ($student_id == "") {		
			$sql = "insert into student(name, email, password, coach_id) values('dummy@dummy.dummy', 'dummy@dummy.dummy', 'dummy@dummy.dummy', {$coach_id});";
			mysql_query($sql);
			$student_id = getOneStudentOfTheCoach ($coach_id);			
		}		
		$_SESSION['student_id'] = $student_id;		
	}
	
	if (!isset($_SESSION['student_id']))
		header('Location: login.php');
		
	
	$user_id = $_SESSION['student_id'];
	$sql = "select * from users where id={$user_id}";
	$r = mysql_query($sql);
	$user = mysql_fetch_array($r);

	function getScouts(){
		$student_id = $_SESSION['student_id'];
		$sql = "select distinct scout from playdata join (select coach_id from student where id={$student_id}) s on playdata.userid = s.coach_id";
		$r = mysql_query($sql);
		
		$scouts = array();
		while($item = mysql_fetch_array($r)){
			$scouts[] = $item['scout'];	
		}
		$scouts = array_unique($scouts);

		return $scouts;
	}

	function getTags(){
		$student_id = $_SESSION['student_id'];
		$sql = "select distinct tags from playdata join (select coach_id from student where id={$student_id}) s on playdata.userid = s.coach_id order by tags";
		$r = mysql_query($sql);
		
		$tags = array();
		while($item = mysql_fetch_array($r)){
			$tags[] = $item['tags'];	
		}
		$tags = array_unique($tags);

		return $tags;
	}
	
	function getCoachName ($student_id)
	{
		$coach_name = "";
		
		$sql = "select coach_id from student where id= " . $student_id . " LIMIT 1";
		// echo $sql . "<br>";
		$r = mysql_query($sql);
		while($item = mysql_fetch_array($r)) {
			$coach_id = $item['coach_id'];			
		}
		
		$sql = "select email from users where id= " . $coach_id . " LIMIT 1";
		// echo $sql . "<br>";
		$r = mysql_query($sql);
		while($item = mysql_fetch_array($r)) {
			$coach_name = $item['email'];			
		}
	
		return $coach_name;
	}

	$scouts = getScouts();
	$tags = getTags();
	
	$student_id = $_SESSION['student_id'];
	$coach_name = getCoachName ($student_id);
	//echo "Coach is " . $coach_id;
?>
<title>Hoop Coach Playbook Pro - Team Playbook</title>
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.11.1/themes/ui-lightness/jquery-ui.css">
<script type="text/javascript">
	$(function(){
		$('table').hide();
		$('tbody tr').click(function(){
			if($(this).attr('play-id')){
//				window.open('/playbook/play.php?id=' + $(this).attr('play-id'));

			}
		});
		$('h2').click(function(){
			$(this).parent().next().toggle('blind');
		})
<?php if($user['paid'] == 79): ?>
  $('<div title="Premium Membership Feature">This is a Pro Feature. <a href="/premium/index.php?uid=3200">Click here</a> to upgrade.</div>')
    .dialog({
      modal: true
    });
<?php endif; ?>
	})
</script>
<style type="text/css">
	h2{
		cursor: pointer;
	}
	.ui-dialog-titlebar-close{
		display:none;
	}
	#tags_section{
		border-top: 5px solid #aaa;
		margin-top: 30px;
	}
    .playlist-item{
        padding: 10px;
        border-radius: 8px;
    }
    .scoutlist-item{
        background: #007;
    }
    .scoutlist-item-without-item{
        
    }
    .taglist-item{
/*        background: orange;*/
        background: #5a5;
        color: white;
    }
    .taglist-item-without-item{
        
    }
</style>
<div class="container">
	<div class="row">
<center><br><img src="http://www.hoopcoach.org/playbook/basketball-plays-logo5.png"></center>
		<h1 class="col-md-10 text-info">Team Playbook</h1>
		<div class="col-md-2">
			<a href="logout.php" class="btn btn-danger" style="margin-top:20px;">Logout</a>
		</div>
	</div>
	
	<div id="scouts_section">
		<?php 
			foreach($scouts as $scout):
		?>
		<div style="margin-top:30px;">
		<?php if(empty($scout)): ?>
			<h2 class="bg-primary playlist-item scoutlist-item-without-item">Plays without any opponent<i class="fa fa-caret-down" style="margin-left:20px;"></i></h2>
		<?php else: ?>
			<h2 class="bg-primary playlist-item scoutlist-item">Opponent: <?=$scout?><i class="fa fa-caret-down" style="margin-left:20px;"></i></h2>
		<?php endif; ?>
		</div>
		<table class="table table-hover table-striped" style="margin-bottom:35px;">
		<thead>
			<tr>
				<th width="50%">Name</th>
				<th width="25%">Tags</th>
				<!-- <th>Opponent</th> -->
				<th width="25%">Created at</th>
			</tr>
		</thead>
		<tbody>
		<?php
				$sql = "select * from playdata join (select coach_id from student where id={$student_id}) s on playdata.userid = s.coach_id where scout='{$scout}'";
				if(empty($scout)){
					$sql = "select * from playdata join (select coach_id from student where id={$student_id}) s on playdata.userid = s.coach_id where scout='' or scout is null";
				}
				$r = mysql_query($sql);
				while($item = mysql_fetch_array($r)):
		 ?>
					<tr play-id="<?=$item['id']?>" style="cursor:pointer;">
						<td style="text-decoration:underline; font-weight:bold;">
							<a href="/playbook/play.php?id=<?=$item['id']?>" target="_blank"><?=$item['name']?></a>
						</td>
						<td style="opacity:0.8;"><?=$item['tags']?></td>
						<!-- <td><?=$item['scout']?></td> -->
						<!-- <td>< ?=date('m-d-Y H:i', strtotime($item['created_on']))?></td> -->
						<td style="opacity:0.8;"><?=date('m/d/Y', strtotime($item['created_on']))?></td>
					</tr>
				<?php endwhile; ?>
		</tbody>
		 </table>
		<?php endforeach; ?>
	</div>
	
	<div id="tags_section">
		<?php 
			foreach($tags as $tag):
		?>
		<div style="margin-top:30px;">
		<?php if(empty($tag)): ?>
			<h2 class="bg-success playlist-item taglist-item-without-item">Plays without any tag<i class="fa fa-caret-down" style="margin-left:20px;"></i></h2>
		<?php else: ?>
			<h2 class="bg-success playlist-item taglist-item">Tag: <b><i><?=$tag?></i></b><i class="fa fa-caret-down" style="margin-left:20px;"></i></h2>
		<?php endif; ?>
		</div>
		<table class="table table-hover table-striped" style="margin-bottom:35px;">
		<thead>
			<tr>
				<th width="50%">Name</th>
				<th width="25%">Opponent</th>
				<!-- <th>Opponent</th> -->
				<th width="25%">Created at</th>
			</tr>
		</thead>
		<tbody>
		<?php
				$sql = "select * from playdata join (select coach_id from student where id={$student_id}) s on playdata.userid = s.coach_id where tags='{$tag}'";
				if(empty($tag)){
					$sql = "select * from playdata join (select coach_id from student where id={$student_id}) s on playdata.userid = s.coach_id where tags='' or tags is null";
				}
				$r = mysql_query($sql);
				while($item = mysql_fetch_array($r)):
		 ?>
					<tr play-id="<?=$item['id']?>" style="cursor:pointer;">
						<td style="text-decoration:underline; font-weight:bold;">
							<a href="/playbook/play.php?id=<?=$item['id']?>"><?=$item['name']?></a>
						</td>
						<td style="opacity:0.8;"><?=$item['scout']?></td>
						<!-- <td><?=$item['scout']?></td> -->
						<!-- <td>< ?=date('m-d-Y H:i', strtotime($item['created_on']))?></td> -->
						<td style="opacity:0.8;"><?=date('m/d/Y', strtotime($item['created_on']))?></td>
					</tr>
				<?php endwhile; ?>
		</tbody>
		 </table>
		<?php endforeach; ?>
	</div>
	
	 	
</div>
	
