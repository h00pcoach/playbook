<?php 
	session_start();
	if(!isset($_SESSION['admin'])){
		header('Location: login.php');
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		include '../mydb.php';

		$email = $_POST['email'];
		$sql = "select id, name from users where email='{$email}';";
		$result = mysql_query($sql);
		$no_of_rows = mysql_num_rows($result);
		echo "<ul>";
		if($no_of_rows>0){
			//echo "<table cellpadding='2' cellspacing='2' border='0' width='100%'>";
			//echo "<tr><!--<th>Userid</th>--><td><strong>Name</strong></td><td><strong>Affiliate Url</strong></td></tr>";	
			while($row = mysql_fetch_assoc($result)){
				echo "<li>If you want them to pay <b>regular</b> price, please email the following URL to the user <br> http://www.basketballplaybook.org/register.php?type=new&affiliate={$row['id']}<br><br><br>";
				
				echo "<li>If you want them to pay <b>discounted</b> price, please email the following URL to the user <br> http://www.basketballplaybook.org/register_offer.php?type=new&affiliate={$row['id']}<br><br><br>";
				//echo "<tr><!--<td>{$row['id']}</td>--><td>{$row['name']}</td><td>http://www.basketballplaybook.org/register.php?type=new&affiliate={$row['id']}</td></tr>";	
			}
			echo "</ul>";
			//echo "</table>";
			
			
		}else{
			echo "<div>No user found!</div>";
		}
	}
 ?>

<form method="POST">
	<h2>Show User Affiliate Url</h2>
	<div>
		<input type="email" name="email" placeholder="Email Address">
	</div>
	<button>OK</button>	
</form>

<br>
<a href="http://www.basketballplaybook.org/admin/">Admin Home</a>