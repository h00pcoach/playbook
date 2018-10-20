<?php 
	include '../mydb.php';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$yearly_amount = $_POST['yearly_amount'];
		$monthly_amount = $_POST['monthly_amount'];
		$yearly_discounted_amount = $_POST['yearly_discounted_amount'];
		$monthly_discounted_amount = $_POST['monthly_discounted_amount'];
		$sql = "update settings set yearly_amount={$yearly_amount}, monthly_amount={$monthly_amount}, yearly_discounted_amount={$yearly_discounted_amount}, monthly_discounted_amount={$monthly_discounted_amount}";
		mysql_query($sql);
		
?>
	<script type="text/javascript">alert('Amount Saved'); location.href='http://www.basketballplaybook.org/admin';</script>
<?php
	}
	$sql = "select * from settings";
	$item = mysql_fetch_array(mysql_query($sql));
 ?>

<h1>Payment Amount</h1>
<form method="POST">

	<div>
		Yearly Amount
		<input name="yearly_amount" value="<?=$item['yearly_amount']?>">
	</div>
	<div>
		Monthly Amount
		<input name="monthly_amount" value="<?=$item['monthly_amount']?>">
	</div>
	
	<br>
	
	<div>
		Yearly Discounted Amount
		<input name="yearly_discounted_amount" value="<?=$item['yearly_discounted_amount']?>">
	</div>
	<div>
		Monthly Discounted Amount
		<input name="monthly_discounted_amount" value="<?=$item['monthly_discounted_amount']?>">
	</div>
	
	<div>
		<button type="submit">Save</button>
	</div>
	
	<br>
</form>

