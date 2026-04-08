
<?php
    require_once('mydb_pdo.php');

    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $conn->exec("set names utf8");

    /******** GET PLAYS ********/
    $sql = "SELECT playdata.id, playdata.name, playdata.file, category.name AS cat, category.id AS catid,
    playdata.thumbsup, playdata.thumbsdown, playdata.rated, playdata.rate, playdata.ratecount, playdata.comments, playdata.movements, playdata.created_on, users.id AS userid, playdata.id, users.name AS user, playdata.tags FROM playdata JOIN users ON (users.id = playdata.userid) JOIN category ON (category.id = playdata.catid)  WHERE playdata.`private`='0' AND playdata.copied=0
    AND (CHAR_LENGTH(COALESCE(playdata.movements,'')) - CHAR_LENGTH(REPLACE(COALESCE(playdata.movements,''), CHAR(96), '')) + 1) >= 3
    ";

    $app='?';
    $get='';

    $search = '';
    $cat = '';
    $tags = '';
    $user = '';
    $displayTitle = '';
    $load_featured = true;

    if(isset($_REQUEST['search']) && trim($_REQUEST['search']) != '')
    {
        $search = '%'.trim($_REQUEST['search']).'%';

        $get=" AND playdata.name LIKE :search OR category.name LIKE :search OR playdata.tags LIKE :search";
        $app='?search='.trim($_REQUEST['search']).'&';

        $displayTitle = '<em>'.$_REQUEST['search'].'</em>';
    }
    elseif (isset($_REQUEST['cat']) && trim($_REQUEST['cat']) != '')
    {
        $cat = trim($_REQUEST['cat']);
        $get=" AND catid = :cat";

        $app='?cat='.trim($_REQUEST['cat']).'&';

    }
    elseif (isset($_REQUEST['tags']) && trim($_REQUEST['tags']) != '')
    {
        $tags = '%'.trim($_REQUEST['tags']).'%';
        $get=" AND playdata.tags LIKE :tags";
        $app='?tags='.trim($_REQUEST['tags']).'&';

        $displayTitle = $tags;

    }
    elseif (isset($_REQUEST['user']) && trim($_REQUEST['user']) != '')
    {
        $user = $_REQUEST['user'];

        $get=" AND playdata.userid = :user";
        $app='?user='.trim($_REQUEST['user']).'&';

        $displayTitle = 'User';

    }
    else { // INITIAL LOAD - Load all the featured plays

        $get=" AND playdata.featured = 1";
        $load_featured = false;

        $displayTitle = 'Featured';

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
    switch($sort)
    {
        case 1:
            $sql.="playdata.rate ASC";break;
        case 2:
            $sql.="playdata.rate DESC";break;
        case 3:
            $sql.="playdata.name ASC";break;
        case 4:
            $sql.="playdata.name DESC";break;
        case 5:
            $sql.="category.name ASC";break;
        case 6:
            $sql.="category.name DESC";break;
        case 7:
            $sql.="playdata.created_on ASC";break;
        case 8:
            $sql.="playdata.created_on DESC";break;
        case 9:
            $sql.="users.name ASC";break;
        case 10:
            $sql.="users.name DESC";break;

        default:
            $sql.="playdata.rate DESC";break;
    }

    // SET Limits and offset
    $limit = 8;
    $sql .= " LIMIT ".(($page-1)*$limit).",".$limit;

    // Initialize statement for all results fetch
    $stAll = $conn->prepare( "SELECT COUNT(*) FROM playdata JOIN category ON (category.id= playdata.catid) " . $get );

    // Initialize statement for limited fetch
    $st = $conn->prepare( $sql );

    // Bind parameters
    if ($search != '')
    {
        $st->bindValue( ":search", $search, PDO::PARAM_STR );
        $stAll->bindValue( ":search", $search, PDO::PARAM_STR );
    }
    elseif ($cat != '')
    {
        $st->bindValue( ":cat", $cat, PDO::PARAM_STR );
        $stAll->bindValue( ":cat", $cat, PDO::PARAM_STR );
    }
    elseif ($tags != '')
    {
        $st->bindValue( ":tags", $tags, PDO::PARAM_STR );
        $stAll->bindValue( ":tags", $tags, PDO::PARAM_STR );
    }
    elseif ($user != '')
    {
        $st->bindValue( ":user", $user, PDO::PARAM_STR );
        $stAll->bindValue( ":user", $user, PDO::PARAM_STR );
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


    /******** GET FEATURED PLAY JUBMOTRON ********/
    // load random from featured list to display in jubmtron
    $sql = "SELECT playdata.id, playdata.name, playdata.file, category.name AS cat, category.id AS catid,
    playdata.thumbsup, playdata.thumbsdown, playdata.rated, playdata.rate, playdata.ratecount, playdata.comments, playdata.movements, playdata.created_on, users.id AS userid, playdata.id, users.name AS user, playdata.tags FROM playdata JOIN users ON (users.id = playdata.userid) JOIN category ON (category.id = playdata.catid)  WHERE playdata.`private`='0' AND playdata.copied=0
    AND (CHAR_LENGTH(COALESCE(playdata.movements,'')) - CHAR_LENGTH(REPLACE(COALESCE(playdata.movements,''), CHAR(96), '')) + 1) >= 3
    AND playdata.featured = 1 ORDER BY RAND() LIMIT 1";

    $st = $conn->prepare( $sql );
    $st->execute();

    // Select a play to feature in the jumbotron
    // $topFeatKey = array_rand($plays);
    $topFeat = $st->fetch();
    /******** END GET FEATURED PLAY JUBMOTRON ********/


    /******** GET FEATURED USERS ********/
    $feat_users_limit = 10000000;
    $sql = "SELECT * FROM users WHERE featured = 1 LIMIT :feat_users_limit";

    $st = $conn->prepare($sql);
    $st->bindValue( ":feat_users_limit", $feat_users_limit, PDO::PARAM_INT );
    $st->execute();

    $feat_users = array();

    while ( $row = $st->fetch() )
  	{
   		$feat_users[] = $row;
  	}
    /******** END GET FEATURED USERS ********/

    /******** GET CATEGORIES ********/
    $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
    $conn->exec("set names utf8");

    $sql = "SELECT * FROM category WHERE ispublic=1";
    $st = $conn->prepare( $sql );
    $st->execute();

    $categories = array();
    while ( $row = $st->fetch() )
    {
        $categories[] = $row;
    }
    /******** END GET CATEGORIES ********/

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
        <script type="text/javascript">
            window.google_analytics_uacct = "UA-1535786-5";
        </script>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Free Basketball Plays created with Basketball Playbook</title>
        <meta name="description" content="Free Basketball Plays created with Basketball Playbook">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Bree+Serif|Raleway" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/bp-main.css">
    </head>

    <body id="bp-body">
        <nav class="navbar navbar-expand-xl navbar-light bg-light">
            <!-- <a class="navbar-brand" href="#">Navbar</a> -->
            <a class="navbar-brand" href="http://www.hoopcoach.org/"><img class="img-fluid" src="Model/hoopcoach120.png" alt="Hoopcoach"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="basketball-plays.php">Find Basketball Plays</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="play.php">Draw Basketball Plays</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://www.hoopcoach.org/video/basketball-playbook-tutorial" rel="nofollow">Tutorial</a>
                    </li>
                </ul>
                <div class="nav">
                    <a class="nav-item my-sm-2 ml-md-2 btn btn-info" href="register.php" rel="nofollow">SIGN UP</a> 
                </div>

            </div>
        </nav>
        <main role="main">

        	<!-- Main jumbotron for a featured play -->
        	<div id="bp-jumbotron" class="jumbotron jumbotron-fluid">
        		<div class="container">
        			<h1 class="display-3"><?= $topFeat['name'] ?></h1>
        			<p class="info-text"><?= $topFeat['cat'] . ' <em>' . $topFeat['tags'] . '</em>'?></p>
        			<p><a class="btn btn-outline-light btn-lg" href="play.php?id=<?= $topFeat['id'] ?>&user=<?= $topFeat['userid'] ?>&name=<?= $topFeat['name'] ?>&category=<?= $topFeat['cat'] ?>" role="button" target="_blank">View</a></p>
        		</div>
        	</div>

            <!-- Featured user section -->
            <div class="container py-2 mb-4">
                <div class="row">
                    <div class="col-12">
                        <h2>Featured Contributors</h2>
                        <hr>
                    </div>
                    <?php foreach ($feat_users as $key => $feat_user)
                    { ?>

                        <div class="col-sm-6 col-md-3 col-xl-2 mb-4">
                            <div class="card">

                                <div class="card-body">
                                    <?php
                                        $first = '';
                                        $last = '';
                                        if (isset($feat_user['name']))
                                        {
                                            $fullName = explode(' ', $feat_user['name']);
                                            $first = $fullName[0];
                                            if (isset($fullName[1]))
                                            {
                                                $last = $fullName[1];
                                            }

                                        } ?>

                                    <h4 class="card-title text-center text-sm-left"><?= $first ?><br /><?= $last ?></h4>
                                    <!-- <a class="btn btn-primary mb-2" href="#">View Plays</a> -->

                                    <form class="form" method="GET">
                                        <input type="hidden" name="user" value="<?= $feat_user['id'] ?>">

                                        <button class="btn btn-primary" href="#">View Plays</button>
                                    </form>

                                    <?php if (isset($feat_user['extra_url'])) { ?>
                                        <a class="btn btn-info mt-2" href="<?= $feat_user['extra_url'] ?>" target="_blank">Bio</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </div><!-- /container Featured user -->

            <!-- Secondary nav for filtering and searching plays -->
            <div class="container py-2 mb-4">

                <div class="row">
                    <div class="col-12">
                        <h2>Basketball Plays</h2>
                        <hr>
                    </div>
                </div>

                <ul class="nav">
                    <form class="form-inline mt-3 mt-sm-0 order-2 order-sm-1" name="searchForm" id="search_form" method="GET" action="basketball-plays.php">
                        <label class="info-text mr-1 d-sm-none d-md-block" for="sort">Sort By</label>
                        <select class="form-control custom-select" name="sort" onChange="$('#search_form').submit();">
                            <!-- <option value="0" <?= $sort == 0 ? 'selected' : '' ?>>Featured</option> -->
                            <option value="1" <?= $sort == 1 ? 'selected' : '' ?>>Not Rated</option>
                            <option value="2" <?= $sort == 2 ? 'selected' : '' ?>>Top Rated</option>
                            <option value="3" <?= $sort == 3 ? 'selected' : '' ?>>Name A-Z</option>
                            <option value="4" <?= $sort == 4 ? 'selected' : '' ?>>Name Z-A</option>
                            <option value="5" <?= $sort == 5 ? 'selected' : '' ?>>Categories A-Z</option>
                            <option value="6" <?= $sort == 6 ? 'selected' : '' ?>>Categories Z-A</option>
                            <option value="7" <?= $sort == 7 ? 'selected' : '' ?>>Date Added - Oldest</option>
                            <option value="8" <?= $sort == 8 ? 'selected' : '' ?>>Date Added - Newest</option>
                            <option value="9" <?= $sort == 9 ? 'selected' : '' ?>>Coach Name A-Z</option>
                            <option value="10" <?= $sort == 10 ? 'selected' : '' ?>>Coach Name Z-A</option>
                        </select>
                    </form>

                    <form class="form-inline ml-sm-auto order-1 order-sm-2">
                        <!-- <input class="form-control mx-sm-1" type="text" placeholder="Search" aria-label="Search"> -->
                        <input class="form-control mx-sm-1" type="text" id="search_terms" class="text" name="search" required placeholder="Search" aria-label="Search" value="<?php if(isset($_REQUEST['search'])) echo $_REQUEST['search']; ?>">

                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                    </form>
                </ul>
            </div><!-- /container Secondary nav -->

            <!-- Play list -->
        	<div class="container">
                <div class="row">
                    <div class="col-12">
                        <p class="info-text"><strong><?= $play_count ?></strong> <?= $displayTitle ?> Plays Found</p>
                    </div>
                </div>
        		<div class="row">
                    <div class="col-md-6 col-lg-9">
                        <div class="row">

                            <?php foreach ($plays as $key => $play) { ?>
                                <div class="col-sm-6 col-lg-3 mb-4">
                                <div class="card">
                                	<img class="card-img-top" src="<?php echo $base_url.'users/'.$play['userid'].'/'.$play['file'].'_1.jpeg'; ?>" alt="Basketball Play <?= $play['name'] . ' ' . $play['cat'] . ' ' . $play['tags'] ?>">
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
                                                        <a href="basketball-plays.php?tags=<?= $tags[$i] ?>"> <?= $tags[$i] ?></a>
                                                        <?php if ($i<count($tags)-1) { ?>
                                                             ,
                                                        <?php } ?>

                                                    <?php } else { ?>

                                    					<a href="basketball-plays.php?tags=<?= $play['tags'] ?>"><?= $play['tags'] ?></a>

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
                                        <a class="btn btn-primary" href="play.php?id=<?= $play['id'] ?>&user=<?= $play['userid'] ?>&name=<?= $play['name'] ?>&category=<?= $play['cat'] ?>">View</a>
                                	</div>
                                </div>
                			</div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <h4 class="text-secondary mt-0">categories</h4>
                        <div class="list-group">
                            <?php $featured_active = !isset($_REQUEST['cat']) && !isset($_REQUEST['search']) ? 'list-group-item-secondary' : '' ?>
                            <a class="list-group-item list-group-item-action <?= $featured_active ?>" href="basketball-plays.php">Featured</a>

                            <!-- <?php if (isset($_REQUEST['user'])) { ?>
                                <a class="list-group-item list-group-item-action <?= $featured_active ?>" href="basketball-plays.php?user=<?= $_REQUEST['user'] ?>">Featured Contributor</a>
                            <?php } ?> -->
                            <?php foreach ($categories as $key => $category) {
                                $active = isset($_REQUEST['cat']) && $category['id'] == $_REQUEST['cat'] ? 'list-group-item-secondary' : '';
                                ?>
                                <a class="list-group-item list-group-item-action <?= $active ?>" href="basketball-plays.php?cat=<?= $category['id'] ?>"><?= $category['name'] ?></a>
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
                            <a href="basketball-plays.php<?= $app .'page='. $prev ?>" class="btn btn-outline-secondary"><i class="fa fa-chevron-left fa-lg" aria-hidden="true"></i></a>

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

                                        <a class="dropdown-item" href="basketball-plays.php<?= $app .'page='. $i ?>"><?= $i ?></a>

                                <?php } ?>

                            </div>
                      </div>
                      <?php if ($page < $page_count)
                      { $next = $page+1; ?>
                          <a href="basketball-plays.php<?= $app .'page='. $next ?>" class="btn btn-outline-secondary"><i class="fa fa-chevron-right fa-lg" aria-hidden="true"></i></a>
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

    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-1535786-5']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>

    <!-- <script type="text/javascript">
        window.google_analytics_uacct = "UA-1535786-5";
    </script> -->

</html>
