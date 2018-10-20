<?php
	session_start();
	require('../mydb_pdo.php');

	// Get the id of a student of this coach
	function getOneStudentOfTheCoach($coach_id)
	{
		// Initialize PDO
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->exec("set names utf8");

		$sql = "SELECT * FROM student WHERE coach_id = :coach_id LIMIT 1";
		$st = $conn->prepare( $sql );

		// Bind parameters
		$st->bindValue( ":coach_id", $coach_id, PDO::PARAM_INT );
		$st->execute();
		$row = $st->fetch();
		$conn = null;

		$student_id = '';
		if ($row)
		{
			$student_id = $row['id'];
		}

		return $student_id;
	}

	if (isset($_SESSION['user_id']))
	{
		$coach_id = $_SESSION['user_id'];

		// coach came here ... so get his student_id
		$student_id = getOneStudentOfTheCoach($coach_id);

		if ($student_id == "")
		{
			// Initialize PDO
			$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
			$conn->exec("set names utf8");

			$sql = "INSERT into student(name, email, password, coach_id) VALUES('dummy@dummy.dummy', 'dummy@dummy.dummy', 'dummy@dummy.dummy', :coach_id)";
			$st = $conn->prepare( $sql );

			// Bind parameters
			$st->bindValue( ":coach_id", $coach_id, PDO::PARAM_INT );
			$st->execute();
			$result = $st->fetch();
			$conn = null;

			// $sql = "INSERT into student(name, email, password, coach_id) VALUES('dummy@dummy.dummy', 'dummy@dummy.dummy', 'dummy@dummy.dummy', :coach_id);";
			// mysql_query($sql);
			$student_id = getOneStudentOfTheCoach($coach_id);
		}
		$_SESSION['student_id'] = $student_id;
	}

	if (!isset($_SESSION['student_id']))
	{
		header('Location: login.php');

	} else {

		// Initialize PDO
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->exec("set names utf8");

		$user_id = $_SESSION['student_id'];
		$sql = "SELECT * FROM users WHERE id = :user_id";
		$st = $conn->prepare( $sql );

		// Bind parameters
		$st->bindValue( ":user_id", $user_id, PDO::PARAM_INT );
		$st->execute();
		$user = $st->fetch();
		$conn = null;
	}

	function getScouts()
	{
		$student_id = $_SESSION['student_id'];

		// Initialize PDO
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->exec("set names utf8");

		$sql = "SELECT DISTINCT scout FROM playdata JOIN (SELECT coach_id FROM student WHERE id = :student_id) s ON playdata.userid = s.coach_id";
		$st = $conn->prepare( $sql );

		// Bind parameters
		$st->bindValue( ":student_id", $student_id, PDO::PARAM_INT );
		$st->execute();

		$scouts = array();
		while($item = $st->fetch())
		{
			$scouts[] = $item['scout'];
		}
		$scouts = array_unique($scouts);

		$conn = null;
		// echo 'getScouts: ' . json_encode($scouts);

		return $scouts;
	}

	function getTags()
	{
		$student_id = $_SESSION['student_id'];

		// Initialize PDO
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->exec("set names utf8");

		$sql = "SELECT DISTINCT tags FROM playdata JOIN (SELECT coach_id FROM student WHERE id = :student_id) s on playdata.userid = s.coach_id ORDER BY tags";
		$st = $conn->prepare( $sql );

		// Bind parameters
		$st->bindValue( ":student_id", $student_id, PDO::PARAM_INT );
		$st->execute();

		$tags = array();
		while($item = $st->fetch())
		{
            $itemTags = explode(',', $item['tags']);

            foreach ($itemTags as $key => $itemTag)
            {
                $tags[] = $itemTag;
            }
			// $tags[] = $item['tags'];
		}
		$tags = array_unique($tags);

		// echo json_encode($tags);

		$conn = null;

		// echo 'Tags? ' . json_encode($tags);
		return $tags;
	}

	function getCoachName($student_id)
	{
		$coach_name = "";

		// Initialize PDO
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->exec("set names utf8");

		$sql = "SELECT coach_id FROM student WHERE id = :student_id LIMIT 1";
		$st = $conn->prepare( $sql );

		// Bind parameters
		$st->bindValue( ":student_id", $student_id, PDO::PARAM_INT );
		$st->execute();
		$row = $st->fetch();

		if ($row)
		{
			$coach_id = $row['coach_id'];

			$sql = "SELECT email FROM users where id = :coach_id LIMIT 1";
			$st = $conn->prepare( $sql );

			// Bind parameters
			$st->bindValue( ":coach_id", $coach_id, PDO::PARAM_INT );
			$st->execute();
			$result = $st->fetch();
			$conn = null;

			if ($result)
			{
				$coach_name = $result['email'];
			}
		}

		// echo 'coach name? ' . $coach_name;

		return $coach_name;
	}

	$scouts = getScouts();
	$tags = getTags();

	$student_id = $_SESSION['student_id'];
	$coach_name = getCoachName ($student_id);

    $limit = 8;
    if(isset($_REQUEST['page']))
    {
        $page=$_REQUEST['page'];
    }
    else {
        $page=1;
    }

    // Initialize PDO
    $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
    $conn->exec("set names utf8");

    $sql = "SELECT * FROM playdata JOIN (SELECT coach_id FROM student WHERE id = :student_id) s ON playdata.userid = s.coach_id";


    $app='?';
    $tag = '';
    $scout = '';
    $get = '';
    if (isset($_REQUEST['tag']))
    {
        $tag = trim($_REQUEST['tag']);
        // $tag = '%'.trim($_REQUEST['tag']).'%';
        // $tags = '%'.trim($_REQUEST['tags']).'%';
        // $tag = $_REQUEST['tag'];


        // $app='?tag='.$tag.'&';
        // $displayTitle = $tag;

        // Initialize PDO
        // Initialize PDO
        // $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        // $conn->exec("set names utf8");

        // $sql = "SELECT * FROM playdata JOIN (SELECT coach_id FROM student WHERE id = :student_id) s ON playdata.userid = s.coach_id";
        // $st = $conn->prepare( $sql );

        if(empty($tag))
        {
            // $tag = $_REQUEST['tags'];
            $get = ' WHERE playdata.tags = :tag OR tags IS NULL';
			$displayTitle = 'No Tags';

        } else {

            $tag = '%'.$tag.'%';
            $get = ' WHERE playdata.tags LIKE :tag';

			$displayTitle = trim($_REQUEST['tag']); // use requested value to avoid LIKE query %
            // echo 'tag? ' . $tag;
        }

        $app='?tag='.$tag.'&';
        // $displayTitle = trim($_REQUEST['tag']); // use requested value to avoid LIKE query %

        // SELECT * FROM playdata JOIN (SELECT coach_id FROM student WHERE id = 1396) s ON playdata.userid = s.coach_id WHERE tags LIKE '%Defense%' LIMIT 0,8

    } elseif (isset($_REQUEST['scout'])) {

        $scout = trim($_REQUEST['scout']);

        $app='?scout='.$scout.'&';
        $displayTitle = $scout;

        // $sql = "SELECT * FROM playdata JOIN (SELECT coach_id FROM student WHERE id = :student_id) s ON playdata.userid = s.coach_id";

        if(empty($scout))
        {
            $get = ' WHERE scout = :scout OR scout IS NULL';
			$displayTitle = 'No Opponents';
        } else {

            $get = ' WHERE scout = :scout';
        }

    } else {

        $app='?all=true&';
        $displayTitle = $scout;

        if (!isset($_REQUEST['all']))
        {
            $_REQUEST['all'] = true;
        }

        // $sql = "SELECT * FROM playdata JOIN (SELECT coach_id FROM student WHERE id = :student_id) s ON playdata.userid = s.coach_id";
    }


    $sql .= $get . " ORDER BY playdata.name LIMIT ".(($page-1)*$limit).",".$limit;

    // echo $sql;
    //
    // echo $tag;

    $st = $conn->prepare( $sql );
    $stAll = $conn->prepare( "SELECT COUNT(*) FROM playdata JOIN (SELECT coach_id FROM student WHERE id = :student_id) s ON playdata.userid = s.coach_id" . $get );

    if (isset($_REQUEST['tag']))
    {
        $st->bindValue( ":tag", $tag, PDO::PARAM_STR );
        $stAll->bindValue( ":tag", $tag, PDO::PARAM_STR );

    } elseif (isset($_REQUEST['scout'])) {

        $st->bindValue( ":scout", $scout, PDO::PARAM_STR );
        $stAll->bindValue( ":scout", $scout, PDO::PARAM_STR );
    }
    $st->bindValue( ":student_id", $student_id, PDO::PARAM_INT );
    $stAll->bindValue( ":student_id", $student_id, PDO::PARAM_INT );

    $st->execute();

    $plays = array();
    while($row = $st->fetch())
    {
        $plays[] = $row;
    }

    $stAll->execute();
    $play_count = $stAll->fetch()[0];

    $conn = null;
    // $base_url = 'https://www.hoopcoach.org/playbook/';
	$base_url = '/';
	$img_url = 'https://www.hoopcoach.org/playbook/';
    // $img_url = '/';

?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->

    <head>
        <script type="text/javascript">
            window.google_analytics_uacct = "UA-1535786-5";
        </script>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Hoopcoach Playbook Pro - Team Playbook</title>
        <meta name="description" content="Free Basketball Plays created with Basketball Playbook">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Bree+Serif|Raleway" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="../css/font-awesome.min.css">
        <link rel="stylesheet" href="../css/bp-main.css?version=1.0">
    </head>

    <body id="tpb-body">
        <nav class="navbar navbar-expand-xl navbar-light bg-light">
            <!-- <a class="navbar-brand" href="#">Navbar</a> -->
            <a class="navbar-brand" href="#"><img class="img-fluid" src="../Model/hoopcoach120.png" alt="Hoopcoach"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <div class="nav ml-auto">
                    <a class="nav-item my-sm-2 ml-md-2 btn btn-danger" href="logout.php" rel="nofollow">Logout</a>
                </div>
            </div>
        </nav>
        <main role="main" class="full-height">

            <!-- Secondary nav for filtering and searching plays -->
            <div class="container py-2 mb-4">

                <div class="row">
                    <div class="col-12">
                        <h2>Team Playbook</h2>
                        <hr>
                    </div>
                </div>
            </div><!-- /container Secondary nav -->

            <!-- Play list -->
        	<div class="container">
                <div class="row">
                    <div class="col-12">
                        <p class="info-text"><strong><?= $play_count ?></strong> <em><?= $displayTitle ?></em> Plays Found</p>
                    </div>
                </div>
        		<div class="row">

                    <div class="col-md-6 col-lg-9 order-2 order-md-1">
                        <div class="row">

                            <?php foreach ($plays as $key => $play) { ?>
                                <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card">
                                	<img class="card-img-top" src="<?php echo $img_url.'users/'.$play['userid'].'/'.$play['file'].'_1.jpeg'; ?>" alt="Basketball Play <?= $play['name'] . ' ' . $play['tags'] ?>">
                                	<div class="card-body">
                                		<h4 class="card-title"><?= $play['name'] ?></h4>
                                        <p class="card-text mb-2"><span class="text-secondary">tags: </span>
                                        <?php
                                            $playsTags = explode(',', $play['tags']);
                                            if ($playsTags)
                                            {
                                                for ($i=0; $i < count($playsTags); ++$i)
                                                {
                                                    if (strlen($playsTags[$i]) > 1)
                                                    {  ?>
                                                        <?php if ($i == 0) { ?>

                                                        <?php } ?>
                                                        <a href="playbook_new.php?tag=<?= $playsTags[$i] ?>"> <?= $playsTags[$i] ?></a>
                                                        <?php if ($i<count($playsTags)-1) { ?>
                                                             ,
                                                        <?php } ?>

                                                    <?php } else { ?>

                                    					<a href="playbook_new.php?tag=<?= $play['tags'] ?>"><?= $play['tags'] ?></a>

                                                    <?php }
                                                }
                                            }
                                        ?>
                                        </p>

                                        <a class="btn btn-primary" href="../play.php?id=<?= $play['id'] ?>&user=<?= $play['userid'] ?>&name=<?= $play['name'] ?>">View</a>
                                	</div>
                                </div>
                			</div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3 mb-4 mb-md-0 order-1 order-md-2">
                        <div class="list-group mb-4">
                            <?php $active = isset($_REQUEST['all']) ? 'list-group-item-secondary' : ''; ?>
                            <a class="list-group-item list-group-item-action <?= $active ?>" href="playbook_new.php?all=true">All plays</a>
                        </div>

                        <h4 class="text-secondary mt-0">Opponents</h4>
                        <div class="list-group">

                            <?php foreach ($scouts as $scout) {
                                $active = isset($_REQUEST['scout']) && $scout == $_REQUEST['scout'] ? 'list-group-item-secondary' : '';
                                ?>
                                <?php if (!empty($scout)) { ?>

                                    <a class="list-group-item list-group-item-action <?= $active ?>" href="playbook_new.php?scout=<?= trim($scout) ?>"><?= $scout ?></a>

                                <?php } else { ?>

                                    <a class="list-group-item list-group-item-action <?= $active ?>" href="playbook_new.php?scout=">Plays without any opponent</a>

                                <?php }?>

                            <?php } ?>

                        </div>

                        <h4 class="text-secondary mt-4">Tags</h4>
                        <div class="list-group">

                            <?php foreach ($tags as $thisTag) {
                                $active = isset($_REQUEST['tag']) && $thisTag == $_REQUEST['tag'] ? 'list-group-item-secondary' : '';
                                ?>
                                <?php if (empty($thisTag)) { ?>

                                    <a class="list-group-item list-group-item-action <?= $active ?>" href="playbook_new.php?tag=">Plays without a tag</a>

                                <?php } elseif(strlen($thisTag) > 1) { ?>

                                    <a class="list-group-item list-group-item-action <?= $active ?>" href="playbook_new.php?tag=<?= trim($thisTag) ?>"><?= $thisTag ?></a>

                                <?php }?>

                            <?php } ?>

                        </div>
                    </div>

        		</div>
        		<!-- <hr> -->
        	</div><!-- /container play list -->

            <!-- Tertiary nav for selecting results page index -->
            <div class="container py-2 mt-2 mb-2">
                <div class="btn-toolbar justify-content-center">
                    <div class="btn-group" role="group" aria-label="Results navigation">
                        <?php if ($page > 1)
                        {  $prev = $page-1;?>
                            <a href="playbook_new.php<?= $app .'page='. $prev ?>" class="btn btn-outline-secondary"><i class="fa fa-chevron-left fa-lg" aria-hidden="true"></i></a>

                        <?php } ?>

                        <div class="btn-group" role="group">
                            <button id="page-dropdown" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?= $page ?>
                            </button>
                            <div id="bb-page-dropdown" class="dropdown-menu" aria-labelledby="page-dropdown">
                                <?php
                                    $page_count = round_up($play_count/$limit,0);
                                    // echo $page_count;
                                    for ($i=1; $i <= $page_count; ++$i) { ?>

                                        <a class="dropdown-item" href="playbook_new.php<?= $app .'page='. $i ?>"><?= $i ?></a>

                                <?php } ?>

                            </div>
                      </div>
                      <?php if ($page < $page_count)
                      { $next = $page+1; ?>
                          <a href="playbook_new.php<?= $app .'page='. $next ?>" class="btn btn-outline-secondary"><i class="fa fa-chevron-right fa-lg" aria-hidden="true"></i></a>
                      <?php } ?>

                    </div>
                </div>
            </div><!-- /container Tertiary nav -->

        </main>

        <?php

        function round_up($number, $precision = 2)
        {
            $fig = (int) str_pad('1', $precision, '0');
            return (ceil($number * $fig) / $fig);
        }

         ?>

        <!-- Footer for copyright and social media -->
        <footer>
            <div class="container-fluid">
                <div class="nav">
                    <p class="mr-auto mb-0 info-text">&copy; 2017 HoopCoach</p>
                    <div class="social-icons cf">
                        <ul>
                            <li class="social-twitter"><a href="http://www.twitter.com/hoopcoach" target="_blank" data-original-title="Twitter">Twitter</a></li>
                            <li class="social-facebook"><a href="https://www.facebook.com/hoopcoach" target="_blank" data-original-title="Facebook">Facebook</a></li>
                            <li class="social-googleplus"><a href="https://plus.google.com/u/0/108448710531579117272" target="_blank" data-original-title="Google">Google+</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </body>

    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>


</html>
