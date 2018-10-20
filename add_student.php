<?php
	session_start();
	if (!isset($_SESSION['user_id']))
	{
		header('Location: /play.php');
		return;
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		require('mydb_pdo.php');

		// Initialize PDO
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->exec("set names utf8");

		$coach_id = $_SESSION['user_id'];
		$name = $_POST['name'];
		$email = $_POST['email'];

		$sql = "SELECT * FROM student WHERE coach_id = :coach_id";
		$st = $conn->prepare( $sql );

		// Bind parameters
		$st->bindValue( ":coach_id", $coach_id, PDO::PARAM_INT );
		$st->execute();
		$row = $st->fetch();

		// check whether this coach has other students
		// $sql = "SELECT * FROM student WHERE coach_id={$coach_id}";
		// $res = mysql_query($sql);

		if ($row)
		{
			// use his other students' password for this new student
			$password = $row['password'];
		}
		else { // use his own password

			$sql = "SELECT * FROM users WHERE id = :coach_id}";
			// $res = mysql_query($sql);
			// $row = mysql_fetch_array($res);

			$st = $conn->prepare( $sql );

			// Bind parameters
			$st->bindValue( ":coach_id", $coach_id, PDO::PARAM_INT );
			$st->execute();
			$row = $st->fetch();

			$password = md5($row['pass']);
		}

		$sql = "INSERT INTO student(name, email, password, coach_id) VALUES(:name, :email, :password, :coach_id);";
		$st = $conn->prepare( $sql );

		// Bind parameters
		$st->bindValue( ":name", $name, PDO::PARAM_STR );
		$st->bindValue( ":email", $email, PDO::PARAM_STR );
		$st->bindValue( ":password", $password, PDO::PARAM_STR );
		$st->bindValue( ":coach_id", $coach_id, PDO::PARAM_INT );
		$success = $st->execute();

		$conn = null;

		if($success):
?>
	<script type="text/javascript">
		alert('Add Student Success');
		location.href = 'coach_student_list.php';
	</script>
<?php
		endif;
	}
 ?>

<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<div class="container">
	<div class="panel panel-primary" style="width:40%; margin:auto;">
		<div class="panel-heading">Add Student</div>
		<div class="panel-body">
			<form method="POST">
				<input name="name" class="form-control" placeholder="Name">
				<input name="email" class="form-control" placeholder="Email" style="margin-top:10px;">
				<div style="text-align:center;">
					<button class="btn btn-success" style="width:30%; margin-top:20px;">Save</button>
				</div>
			</form>
		</div>
	</div>

</div>
