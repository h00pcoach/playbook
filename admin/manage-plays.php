<?php
    include 'header.php';
    require_once('../mydb_pdo.php');

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $conn->exec("set names utf8");

    /******** GET PLAYS ********/
    $sql = "SELECT playdata.id, playdata.name, playdata.file, category.name AS cat, category.id AS catid,
    playdata.thumbsup, playdata.thumbsdown, playdata.rated, playdata.rate, playdata.ratecount, playdata.comments, playdata.movements, playdata.created_on, users.id AS userid, playdata.id, users.name AS user, playdata.tags FROM playdata JOIN users ON (users.id = playdata.userid) JOIN category ON (category.id = playdata.catid)
    ";

    $app='?';
    $get='';

    $search = '';
    $cat = '';
    $tags = '';
    $user = '';
    $load_featured = true;

    if(isset($_REQUEST['search']) && trim($_REQUEST['search']) != '')
    {
        $search = $_REQUEST['search'];
        // $search = '%'.trim($_REQUEST['search']).'%';

        $get=" AND playdata.name LIKE :search OR category.name LIKE :search OR playdata.tags LIKE :search";
        $app='?search='.trim($_REQUEST['search']).'&';

    }

    if(isset($_REQUEST['page']))
    {
        $page=$_REQUEST['page'];
    }
    else {
        $page=1;
    }
    $sql.= $get." ORDER BY ";
    $sort=2;
    if(isset($_GET['sort']))
    {
        $sort=$_GET['sort'];
        $app.='sort='.$sort.'&';
    }
    $sql.="playdata.name ASC";

    // SET Limits and offset
    $limit = 20;
    $sql .= " LIMIT ".(($page-1)*$limit).",".$limit;

    // Initialize statement for all results fetch
    $stAll = $conn->prepare( "SELECT COUNT(*) FROM playdata JOIN category ON (category.id = playdata.catid) " . $get );

    // Initialize statement for limited fetch
    $st = $conn->prepare( $sql );

    // Bind parameters
    if ($search != '')
    {
        $st->bindValue( ":search", $search, PDO::PARAM_STR );
        $stAll->bindValue( ":search", $search, PDO::PARAM_STR );
    }

    $st->execute();
  	$stAll->execute();
    $plays = array();

    while ( $row = $st->fetch() )
  	{
   		$plays[] = $row;
  	}

    // if(isset($_REQUEST['search']) && trim($_REQUEST['search']) != '')
    // {
    //     echo 'SEARCHING: ' . $sql;
    // }

    $play_count = $stAll->fetch()[0];
    /******** END GET PLAYS ********/

    // echo json_encode($topFeat);
    // echo $sql;
    // echo $play_count[0];
    $conn = null;

    $base_url = 'https://www.hoopcoach.org/playbook/';
?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Manage Plays - Admin</title>
        <meta name="description" content="Free Basketball Plays created with Basketball Playbook">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Bree+Serif|Raleway" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="../css/font-awesome.min.css">
        <link rel="stylesheet" href="../css/bp-main.css">
    </head>

    <body id="mp-body">
        <nav class="navbar navbar-expand-xl navbar-light bg-light mb-4">
            <div class="container">
                <a class="navbar-brand text-secondary" href="/playbook/admin/index.php">admin home</a>
            </div>
        </nav>

        <main role="main">

            <!-- Secondary nav for filtering and searching plays -->
            <div class="container py-2 mb-4">

                <div class="row">
                    <div class="col-12">
                        <h2>Manage Plays</h2>
                        <hr>
                    </div>
                </div>

                <ul class="nav">
                    <form class="form-inline ml-sm-auto order-1 order-sm-2">
                        <input class="form-control mx-sm-1" type="text" class="text" name="search" required placeholder="Search" aria-label="Search" value="<?php if(isset($_REQUEST['search'])) echo $_REQUEST['search']; ?>">

                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                    </form>
                </ul>
            </div><!-- /container Secondary nav -->

            <!-- Play list -->
        	<div class="container">
                <div class="row mb-4">
                    <div class="col-6">
                        <p class="info-text"><strong><?= $play_count ?></strong> Plays Found</p>
                    </div>

                    <div class="col-6 text-right">
                        <a class="btn btn-success ml-auto" href="manage-plays.php">View All Plays</a>
                    </div>

                </div>
        		<div class="row" style="min-height: 55vh;">
                    <div class="col-12">
                        <div class="row">
                            <p class="text-danger hidden">There was an error removing this play!</p>
                            <?php foreach ($plays as $key => $play) { ?>
                                <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card">
                                	<img alt="Card image cap" class="card-img-top" src="<?php echo $base_url.'users/'.$play['userid'].'/'.$play['file'].'_1.jpeg'; ?>">
                                	<div class="card-body">
                                		<h4 class="card-title"><?= $play['name'] ?></h4>
                                		<p class="card-text mb-0"><?= $play['cat'] ?></p>
                                        <p class="card-text mb-0"><span class="text-secondary">tags: </span>
                                        <?php
                                            $tags = explode(',', $play['tags']);
                                            if ($tags)
                                            {
                                                for ($i=0; $i < count($tags); ++$i)
                                                {
                                                    if (strlen($tags[$i]) > 1)
                                                    { ?>
                                                        <a href="manage-plays.php?tags=<?= $tags[$i] ?>"> <?= $tags[$i] ?></a>
                                                        <?php if ($i<count($tags)-1) { ?>
                                                             ,
                                                        <?php } ?>

                                                    <?php } else { ?>

                                    					<a href="manage-plays.php?tags=<?= $play['tags'] ?>"><?= $play['tags'] ?></a>

                                                    <?php }
                                                }
                                            }
                                        ?>
                                        </p>
                                        <p class="card-text"><span class="text-secondary">rating: </span>
                                            <?php
                                                $rating = round($play['rate'], 1, PHP_ROUND_HALF_DOWN);


                                            if ($rating <= 0.0)
                                            {// 0.0
                                                echo '<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i> (' . $rating . ')';
                                            }
                                            elseif ($rating >= 0.1 && $rating < 1.0)
                                            {// 0.1 - 0.9
                                                echo '<i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i> (' . $rating . ')';
                                            }
                                            elseif ($rating == 1.0)
                                            {// 1.0
                                                echo '<i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i> (' . $rating . ')';
                                            }
                                            elseif ($rating <= 1.9)
                                            {// 1.1 - 1.9
                                                echo '<i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i> (' . $rating . ')';
                                            }
                                            elseif ($rating == 2.0)
                                            {// 2.0
                                                echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i> (' . $rating . ')';
                                            }
                                            elseif ($rating <= 2.9)
                                            {// 2.1 - 2.9
                                                 echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i> (' . $rating . ')';
                                            }
                                            elseif ($rating == 3.0)
                                            {// 3.0
                                                echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i> (' . $rating . ')';
                                            }
                                            elseif ($rating <= 3.9)
                                            {// 3.1 - 3.9
                                                echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i> (' . $rating . ')';
                                            }
                                            elseif ($rating == 4.0)
                                            {// 4.0
                                                echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i> (' . $rating . ')';
                                            }
                                            elseif ($rating <= 4.9)
                                            {// 4.1 - 4.9
                                                echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i> (' . $rating . ')';
                                            }
                                            elseif ($rating == 5.0)
                                            {// 5.0
                                                echo '<i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> (' . $rating . ')';
                                            }
                                             ?>
                                        </p>
                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                <a class="btn btn-primary" href="../play.php?id=<?= $play['id'] ?>&user=<?= $play['userid'] ?>&name=<?= $play['name'] ?>&category=<?= $play['cat'] ?>"  target="_blank">View</a>
                                            </li>

                                            <li class="list-inline-item">
                                                <form id="remove-form" class="form" action="remove_play.php" method="POST">
                                                    <input type="hidden" name="id" value="<?= $play['id'] ?>">

                                                    <button type="submit" class="btn btn-danger"><span class="fa fa-trash"></span></button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                			</div>
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
                            <a href="manage-plays.php<?= $app .'page='. $prev ?>" class="btn btn-outline-secondary"><i class="fa fa-chevron-left fa-lg" aria-hidden="true"></i></a>

                        <?php } ?>

                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?= $page ?>
                            </button>
                            <div class="dropdown-menu page-dropdown" aria-labelledby="page-dropdown">
                                <?php
                                    $page_count = round_up($play_count/$limit,0);
                                    // echo $page_count;
                                    for ($i=1; $i <= $page_count; ++$i) { ?>

                                        <a class="dropdown-item" href="manage-plays.php<?= $app .'page='. $i ?>"><?= $i ?></a>

                                <?php } ?>

                            </div>
                      </div>
                      <?php if ($page < $page_count)
                      { $next = $page+1; ?>
                          <a href="manage-plays.php<?= $app .'page='. $next ?>" class="btn btn-outline-secondary"><i class="fa fa-chevron-right fa-lg" aria-hidden="true"></i></a>
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
    <!-- <script src="js/vendor/retina.js"></script> -->

    <script>

        $(document).on('submit', '#remove-form', function(event)
        {
            event.preventDefault();

            if(confirm('Are you sure you want to remove this play?'))
        	{
                var form = $(this),
                url = form.attr('action');

                // $(this).submit();

                // Send the data using post
                var posting = $.post( url, form.serialize(),
                    function( data )
                    {

                        // if data returned no errors
                        if (data.success)
                        {
                            console.log('Successfully loaded data!', data.success);
                            location.reload();

                        } else {
                            console.log('Error loading data!', data.error);
                            // location.reload();
                            $('text-danger').removeClass('hidden');
                        }
                    } ,'json' );

        		// $.ajax({
        		// 	url:'save.php',
        		// 	type:'POST',
        		// 	data: {id:$pl},
        		// 	success:function(e){
        		// 		// location.reload();
        		// 		alert('Your Play has been successfully removed. Please refresh your browser.');
        		// 		$tr.remove();
        		// 		showLoading(true);
        		// 		// reload the page to refresh plays_count variable
        				// location.reload();
        		// 	}
        		// });
        	}
        });
    </script>

</html>
