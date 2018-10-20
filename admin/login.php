<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Admin - Playbook</title>
    <meta name="description" content="Free Basketball Plays created with Basketball Playbook">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Bree+Serif|Raleway" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/bp-main.css">
</head>


    <body>
        <div class="container my-4" style="min-height: 100vh;">
        	<div class="row">
        	    <div class="col-sm-4 mx-auto">
					<?php if (isset($results['error']))
					{ ?>
						<p class="text-danger"><?= $results['error'] ?></p>
					<?php } ?>

					<form method="POST" action="process_login.php">
						<div class="card card-primary">
							<div class="card-header">
								Login
							</div>
							<div class="card-body" style="text-align:center;">
								<input name="username" class="form-control" placeholder="Username">
								<input name="password" class="form-control" placeholder="Password" type="password" style="margin-top:10px;">
								<button class="btn btn-primary" style="margin-top:15px; width:30%;">Login</button>
							</div>
						</div>
					</form>
        	    </div>
        	</div>
        </div>

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
