<?php 
	include '../mydb.php';

	$sql = 'select * from (select users.name, users.email, users.paid, count(users.id) as plays_count from users, playdata where playdata.userid=users.id AND users.paid =0 group by playdata.userid) t where t.plays_count>5';
	//$sql = 'select * from (select users.name, users.email, count(users.id) as plays_count from users, playdata where playdata.userid=users.id group by playdata.userid) t where t.plays_count>5';
	$r = mysql_query($sql);
	echo mysql_error();

?>

<?
$i=1;
?>

<table border="1" cellspacing="1">
	<thead>
		<tr>
			<th>SL No.</th>
			<th>Name</th>
			<th>Email</th>
			<th>Plays Count</th>
		</tr>
	</thead>
	<tbody style="text-align:center;">
		
<?php while($user = mysql_fetch_array($r)): ?>
		<tr>
			<td><? echo $i; $i++;?></td>
			<td><?=$user['name']?></td>
			<td><?=$user['email']?></td>
			<td><?=$user['plays_count']?></td>
		</tr>
<?php endwhile; ?>
 	</tbody>
</table>