<?php 
	session_start();
	if(!isset($_SESSION['admin'])){
		header('Location: login.php');
	}
 ?>

<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
	$(function(){
		$('tbody tr').click(function(){
			location.href = '/play.php?id=' + $(this).attr('play-id');
		});
	})
</script>

<?php 
	include('../mydb.php');

	$sql = "select playdata.id, playdata.name, playdata.movements, playdata.created_on, playdata.rate, playdata.private, users.email from playdata join users on playdata.userid=users.id where paid=1";
	$r = mysql_query($sql);
	$total = mysql_num_rows ($r);

?>
<div class="container">
	<h1>Paid Plays (<?=$total?>)</h1>
	<table class="table table-hover table-striped">
		<thead>
			<tr>
				<th style="width:200px;">Name</th>
				<th>Author</th>
				<th>Movements</th>
				<th style="width:150px;">Created at</th>
				<th>Rate</th>
				<th>Private</th>
			</tr>
		</thead>
		<tbody>
	<?php while($item = mysql_fetch_array($r)): ?>
			<tr play-id="<?=$item['id']?>">
				<td><?=$item['name']?></td>
				<td><?=$item['email']?></td>
				<td><?=$item['movements']?></td>
				<!-- <td>< ?=date('m-d-Y H:i', strtotime($item['created_on']))?></td> -->
				<td><?=date('m/d/Y', strtotime($item['created_on']))?></td>
				<td>			
					<?=$item['rate']==0?'&nbsp;':number_format($item['rate'], 2, '.', '');?>
				</td>
				<td>
					<?php if($item['private']): ?>
						Y
					<?php else: ?>
						N
					<?php endif; ?>
				</td>
			</tr>
	<?php endwhile; ?>
	 	</tbody>
	 </table>
</div>