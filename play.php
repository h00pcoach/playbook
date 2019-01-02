<?php
// TODO TODO TODO:  REMOVE BEFORE UPLOADING
 // $_SESSION['user_id'] = 6912; //brandon@tindle
//$_SESSION['user_id'] = 6884; //bjtactor

    // TODO TODO TODO: UPDATE PATH BEFORE UPLOADING
    // $path = 'https://www.hoopcoach.org/playbook/';
$path = 'http://pb.local:8888/';

if (!isset($_COOKIE["visited"])) {
		// Save a cookie for 1 day
	$showOverlay = true; // this will be used to show the overlay one time in 24hrs

	setcookie("visited", true, time() + 3600 * 24);
} else {
	$showOverlay = false;
}
$url = $_SERVER["SERVER_NAME"];
$page = $_SERVER["REQUEST_URI"];
if ($url == "hoopcoach.org") {
	header("Location: https://www.hoopcoach.org$page");
}
session_start();
require_once('mydb_pdo.php');

	// Initialize PDO
$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

$premium_user = 0;
$is_student = false;

	// Free user or paid user?
unset($_SESSION['student_id']);

if (isset($_GET['name'])) {
	$name = $_GET['name'] . ' ';
} else {
	$name = 'customplaybook';
}

if (isset($_SESSION['user_id']) && !isset($_SESSION['student_id'])) {
	$sql = "SELECT * from users WHERE id = :id";
	$st = $conn->prepare($sql);

	    // Bind parameters
	$st->bindValue(":id", $_SESSION['user_id'], PDO::PARAM_INT);
	$st->execute();
	$result = $st->fetch();
		// $conn = null;

	if ($result['paid'] == 1) {
		$premium_user = 1;
	}

} else { ?>

	<style>
		#actions, #actions-xs
		{
			display: none;
		}
	</style>
   <?php 
	}

	if (isset($_GET['id'])) {

		// Admin users
		if (isset($_SESSION['admin'])) {
			$sql = "SELECT * FROM playdata WHERE id = :id";
			$st = $conn->prepare($sql);

			// Bind parameters
			$st->bindValue(":id", $_GET['id'], PDO::PARAM_INT);
			$st->execute();
			$item = $st->fetch();
			// $conn = null;

			$_SESSION['user_id'] = $item['userid'];
		}

		// Free users or paid users?
		if (isset($_SESSION['user_id'])) {
			// echo $_SESSION['user_id'];

			$sql = "SELECT * FROM users WHERE id = :id";
			$st = $conn->prepare($sql);

		    // Bind parameters
			$st->bindValue(":id", $_SESSION['user_id'], PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetch();
			// $conn = null;

			if ($result['paid'] == 1) {
				$premium_user = 1;
			}
		}

		// Student user
		if (isset($_SESSION['student_id'])) {
			// $_SESSION['student_id']
			$sql = "SELECT * FROM student WHERE id = :id";

			// Bind parameters
			$st->bindValue(":id", $_GET['id'], PDO::PARAM_INT);
			$st->execute();
			$item = $st->fetch();
			// $conn = null;

			$_SESSION['user_id'] = $item['coach_id'];

			$is_student = true;
		}

	}


	$result = '';

	$plays_count = 0;
	if (isset($_SESSION['user_id'])) {
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$conn->exec("set names utf8");

		$sql = "SELECT * from users WHERE id = :id";
		$st = $conn->prepare($sql);

	    // Bind parameters
		$st->bindValue(":id", $_SESSION['user_id'], PDO::PARAM_INT);
		$st->execute();

		$result = $st->fetch();

		$sql = "SELECT * FROM playdata WHERE userid = :userid";
		$st = $conn->prepare($sql);

		$st->bindValue(":userid", $_SESSION['user_id'], PDO::PARAM_INT);
		$st->execute();

		$getAllPlaysResult = array();
		while ($row = $st->fetch()) {
			$getAllPlaysResult[] = $row;
		}

		// Now get the total number of episodes that matched the criteria
		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $conn->query($sql)->fetch();

		$plays_count = $totalRows[0];
	  	// $conn = null;
	}

	$conn = null;
	?>

<!DOCTYPE html>
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- stylesheets -->
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Ubuntu:light&subset=Latin">
		<link rel="stylesheet" href="css/main.css?version=1.2">
		<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.11.2/themes/ui-lightness/jquery-ui.css">
		<link rel="stylesheet" href="css/bootstrap.min.css?version=1.3">

		<?php

			// MOVEMENTS  MOVEMENTS  MOVEMENTS  MOVEMENTS  MOVEMENTS  MOVEMENTS  MOVEMENTS  MOVEMENTS
	$name = '';
	$rate = '""';
	$rated = 0;
	$priv = 0;

	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	if (isset($_REQUEST['id'])) {
		if (isset($_SESSION['user_id'])) {
			$sql = "SELECT  id, name, file, rate, (CASE WHEN playdata.rated LIKE :userid THEN 1 else 0 end) AS `raed`, tags, scout, `private`  from playdata WHERE id = :id";

			$st = $conn->prepare($sql);

			$userid = '%' . $_SESSION['user_id'] . '%';

				    // Bind parameters
			$st->bindValue(":id", $_REQUEST['id'], PDO::PARAM_INT);
			$st->bindValue(":userid", $userid, PDO::PARAM_INT);
			$st->execute();
			$res = $st->fetch();
		} else {

			$sql = "SELECT  *  FROM playdata WHERE id = :id";
			$st = $conn->prepare($sql);

			$st->bindValue(":id", $_REQUEST['id'], PDO::PARAM_INT);
			$st->execute();
			$res = $st->fetch();
		}

		$i = 1;
		if (isset($res['movements'])) {
			$nm = explode('`', $res['movements']);
		}
		if (isset($res['userid'])) {
			$file = "users/" . $res['userid'] . "/" . $res['file'];
		} else
			$file = "users/" . "/" . $res['file'];
		while (file_exists($file . '_' . $i . '.jpeg') && $i < 10) {

			echo '<meta property="og:image" content="https://basketballplaybook.org/' . $file . '_' . ($i) . '.jpeg' . '" />';

			if ($i < 4) {
				echo '<meta property="twitter:image' . $i . ':src" content="https://basketballplaybook.org/' . $file . '_' . ($i) . '.jpeg' . '" />';
			}

			++$i;
		}

		$name = $res['name'];
		$rate = $res['rate'];

		$priv = $res['private'];

		if (isset($_SESSION['user_id'])) {
			$rated = $res['raed'];
		}
	}
	?>

		<!-- page details -->
		<meta name="keywords" content="basketball,playbook, basketball coach, basketball planner, coaching apps, basketball plays and drills, basketball plays, basketball drills."/>
		<meta name="description" content="Hoop Coach Playbook is a web based tool for basketball coaches that saves time so you can focus on developing your players. Coaching apps and basketball plays and drills."/>

        <?php
							$title = ' created on Hoopcoach Playbook';
							if ($name != '') {
								$title = $name . $title;
							}
							?>
		<title><?= $title ?></title>

		<!-- social -->
		<meta property="og:image" content="https://basketballplaybook.org/Model/hoopcoach120.png" />
		<meta property="twitter:image0:src" content="https://basketballplaybook.org/Model/hoopcoach120.png" />
		<meta property="og:title" content="<?= $title ?>"/>
		<meta property="og:site_name" content="BasketballPlaybook.org"/>

		<meta name="twitter:card" content="gallery">
		<meta name="twitter:description" content="Up than 200 characters.">
		<meta name="twitter:domain" content="BasketballPlaybook.org">
		<meta name="twitter:description" content="Basketball Playbook is a web based tool for basketball coaches that saves time so you can focus on developing your players. Coaching apps and basketball plays and drills." />
		<meta name="twitter:title" content="<?= $title ?>" />


		<!-- scripts -->
		<script type="text/javascript">
		      var switchTo5x=false;
		</script>

		<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<script type="text/javascript" src="https://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/jquery.ui.touch-punch.min.js"></script>
		<script type="text/javascript" src="js/kinetic-v4.5.5.min.js"></script>
		<script type="text/javascript" src="js/jquery.raty.min.js"></script>
		<!--<script src="http://underscorejs.org/underscore-min.js"></script>-->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
		<script src="js/lz-string.js"></script>
		<script src="js/superfish.js"></script>
		<script src="js/gifshot_test.js"></script>
		<script src="js/acetrik1b_test.js?version=1.3"></script>
		<script src="js/download.js"></script>

		<script type="text/javascript">
			var user={id:'',lastname:'',email:'',ismale:'',role:'',profileurl:'',avatarurl:'',hoopid:'',uped:-1,downed:-1};
			var lastimage = [];
			var record = false;

			function b64toBlob(b64Data, contentType, sliceSize)
			{
			    contentType = contentType || '';
			    sliceSize = sliceSize || 512;

			    var byteCharacters = atob(b64Data);
			    var byteArrays = [];

			    for (var offset = 0; offset < byteCharacters.length; offset += sliceSize)
			    {
			        var slice = byteCharacters.slice(offset, offset + sliceSize);

			        var byteNumbers = new Array(slice.length);
			        for (var i = 0; i < slice.length; i++)
			        {
			            byteNumbers[i] = slice.charCodeAt(i);
			        }

			        var byteArray = new Uint8Array(byteNumbers);

			        byteArrays.push(byteArray);
			    }

			    var blob = new Blob(byteArrays, {type: contentType});
			    return blob;
			}

			function createlastimage()
			{
				for(var i=0;i<16;i++)
				{

						var sprite = new Image();
						sprite.className = 'filmstrip';
						sprite.src = 'Model/LastFrameImage/lastimage'+i+'.jpg';
						lastimage.push(sprite)
				}
			}
			createlastimage();
			$(document).ready(function(e)
			{
				//console.log('Title tag text: ', $('title').text());
				$("#start").click(function()
				{
					record = true;
					////console.log(record);
					if(checkuser())
					{
						// $('#record_warning_dialog').dialog()
						$('#pro-body').html('GIF exporting is a Pro Feature.');
						$('#pro-feature-modal').modal('show');
						return false;
					}
				    playNow(1, false);
					showDownLoading(false);
					var run=setInterval(function(){if(playingStill==false){stoptherecord();record = false;clearInterval(run);}},2200);
				    document.getElementById('start').disabled = true;

				    storage = [];
				    stop = false;
					//console.log('stop? ', stop);
				    storeCanvas();

				});
				function connectingDownload(ele)
				{
					//console.log('connectingDownload: ', ele);
					$(ele).animate({opacity:0.5},500,function()
					{
						if($(ele).next('img').length>0)
							connectingDownload($(this).next());
						else
							connectingDownload('#download>img:first');
						$(this).animate({opacity:1},500,function(){ $(this).animate({opacity:0},900);});
					});
				}
			  	function showDownLoading(show)
			  	{
			  		//console.log('showDownLoading: ', show);

					if(!show)
					{
						// connectingDownload('#download>img:first');
						// $('#largedownload').show();
						// $('#downloading').show();
						$('#downloading-modal').modal('show');
					}
					else{
						// $('#download>img').stop().css({'opacity':0});
						// $('#largedownload').hide();
						// $('#downloading').hide();
						$('#downloading-modal').modal('hide');

					}
				}

			  	function stoptherecord()
				{
					//console.log('stoptherecord!');
				    stopRecord();
				    loadImages();
				    //window.//console.log(allImages);
					document.getElementById('start').disabled = false;
				    timer = false;
				}

				function loadImages()
				{
			  		//console.log('loadImages!');
			  		allImages = [];
			  		var promises = [];
			  		var images = [];
			  		for(var i=0;i<storage.length;i=i+1)
			  		{

			    		var img = storage[i];

			    		promises.push(imageLoaded(img));
			  		}

				  	$.when.apply($, promises).done(function ()
				  	{

					    $("#loader").css("display","none");
					    $(".totalProgress").attr("max",allImages.length);
						for(var i = 0;i<16;i++)
						{
							allImages.push(lastimage[i]);
						}
						gifshot.createGIF({
					    'images': allImages,
						'interval': 0.25,
						 'gifWidth': 306,
						 'gifHeight': 509,
						 'numFrames': 100,
						 'numWorkers': 2
						},
						function(obj)
						{
					    	if(!obj.error)
					    	{
						        var image = obj.image;
								var dataname;

								var blob = b64toBlob(image.replace(/^data:image\/(png|jpg|gif);base64,/, ""),"image/gif");
								var blobUrl = URL.createObjectURL(blob);
								$('#download-hidden').attr('href',blobUrl)
								showDownLoading(true);

								//console.log('blobUrl: ', blobUrl);


								document.getElementById('download-hidden').click();

							}
						});
					});
			  		//return images;
				}

				function imageLoaded(src)
				{
				 	var deferred = $.Deferred();
				  	var sprite = new Image();
				  	sprite.className = 'filmstrip';
				  	sprite.onload = function() {
						allImages.push(sprite);
					    deferred.resolve();
					};
				  	sprite.src = LZString.decompress(src);
				  	return deferred.promise();
				}

				function stopRecord()
				{
					//console.log('stopRecord');
					stop = true;
				  	window.cancelAnimationFrame(requestID);
				}

				function storeCanvas()
				{
					//console.log('storeCanvas! stop? ', stop);

					if(stop === false)
					{
						//console.log('storeCanvas stop is false: ', stop);
				  		stage.toDataURL({
					  		mimeType: "image/png",
				      		quality: 0,
					    callback: function(dataUrl) {
					      var data = LZString.compress(dataUrl);
					      //console.log('storeCanvas dataUrl: ', dataUrl);
					      //console.log('storeCanvas data: ', data);
					      //window.//console.log(dataUrl.length, data.length);
					      storage.push(data);
					      //console.log('storeCanvas almost last stop: ', stop === false);
					      if(stop === false) {
					      	//console.log('storeCanvas last stop: ', stop === false);
					        var lazyLayout = _.debounce(storeCanvas,0.05,true);
					        //console.log('storeCanvas lazyLayout: ', lazyLayout);
					        requestID = requestAnimationFrame(lazyLayout);
					        //console.log('storeCanvas requestID: ', requestID);
					        //window.//console.log(storage);
					      }
				    	}
					});
				  }
				}

				<?php if (isset($_SESSION['user_id'])) { ?>
					user={id:'<?php echo $result['id']; ?>',lastname:'<?php echo $result['name']; ?>',email:'<?php echo $result['email']; ?>',ismale:'<?php echo $result['ismale']; ?>',role:'<?php echo $result['role']; ?>',profileurl:'<?php echo $result['hoopcoachpage']; ?>',avatarurl:'<?php echo $result['avatar']; ?>',hoopid:'<?php echo $result['hoopcoachid']; ?>'};

					if(user.avatarurl.length<5)
						if(user.ismale=='1')
							user.avatarurl='Model/Img/male_avatar_icon.png';
						else
							user.avatarurl='Model/Img/female_avatar_icon.png';

			        setUser(true);
				<?php 
		} ?>

				$('#star, #star-xs').raty({

					size:12,

					width:180,

					space:false,

					hints: ['low success', 'below avg', 'average', 'above avg', 'hi success'],

					readOnly:<?php if (intval($rated) == 0) echo 'false';
													else echo 'true'; ?>,

					starOff: 'Model/Img/balls/ball04.png',

					  starOn : 'Model/Img/balls/ball03.png',

					 score: <?php if ($rate == '') echo '""';
												else echo $rate; ?>,

					 click: function(score, evt){

					<?php if (isset($_GET['id'])) { ?>

				  		$.post('save.php',{id:<?php echo $_GET['id']; ?>,rate:score},function(d){ alert(d);});

					<?php 
			} ?>

				  }

				});
		        <?php
									if (!isset($result['paid']) || $result['paid'] == 0) {
										echo "$('#pdf').click(function()
						{
							$('#pro-body').html('PDF Printouts are a Pro Feature.');
							$('#pro-feature-modal').modal('show');
							return false;
						});";
									}

									?>
				function checkuser()
				{
					<?php
				if (!isset($result['paid']) || $result['paid'] == 0)
					echo 'return true;';
				else
					echo 'return false;';
				?>
				}
				<?php if (isset($_SESSION['user_id']) && $plays_count >= 5 && $result['paid'] == 0) {

				echo "
							$('#new_play_btn').click(function() {
								console.log('new_play_btn clicked!');

								$('#pro-body').html('Pro membership needed for creating more than 5 plays.');
								$('#pro-feature-modal').modal('show');
								return false;
							 });";
			} ?>

		        stLight.options({
		        	publisher:'5a1ef2ea-5e37-4530-bada-46b16219791a'
		      	});
		    });
	    </script>

		<script src="js/main_test.js?version=1.0"></script>
		<script src="js/bootstrap.min.js"></script>
	</head>

	 <!-- style="margin: 0 0 0 0;" -->
	<body>

		<!--<script>(function(d, s, id) {

		  var js, fjs = d.getElementsByTagName(s)[0];

		  if (d.getElementById(id)) return;

		  js = d.createElement(s); js.id = id;

		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=185990164786767";

		  fjs.parentNode.insertBefore(js, fjs);

		}(document, 'script', 'facebook-jssdk'));</script>
		<script type="text/javascript">

		</script>-->


    	<!-- LOGO -->
		<div id="logo-container" class="container-fluid hidden-xs">
			<div class="row">
		   		<div class="col-xs-12">
		        	<a href="/">
		        		<img class="img-responsive pull-left hidden-xs" src="pb_logo.png" srcset="pb_logo@2x.png" alt="Logo">
		        		<!-- <img class="img-responsive center-block visible-xs-block" src="pb_logo.png" srcset="pb_logo@2x.png" alt="Logo"> -->
		      		</a>
		    	</div>
		    </div>
    	</div>

    	<!-- NAVBAR -->
    	<nav class="navbar" role="navigation">
				<div class="container-fluid">
						<div class="navbar-header">

							<!-- <a class="btn btn-hc tools-toggle visible-xs-inline-block" data-target="#play-tools" style="padding: 6px;">Tools</a> -->

							<div class="navbar-brand-xs visible-xs-block center-block">
								<img class="img-responsive center-block" src="<?= $path ?>basketball-plays-logo5.png" alt="Logo">
							</div>

							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#menu-nav" aria-expanded="false" aria-controls="navbar">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
						</div>
						<div id="menu-nav" class="collapse navbar-collapse">
							<ul class="nav navbar-nav navbar-right">
								<li>
							<!-- // TODO TODO TODO: UNCOMMENT FOLLOWING LINE BEFORE PUSHING LIVE ******************************************** -->
							<!-- <a class="nav-link" href="<?= $path ?>basketball-plays.php" target= "_blank" style="color:black;">Plays Library</a> -->
										<a class="nav-link" href="basketball-plays.php" target= "_blank" style="color:black;">Plays Library</a>
									</li>

									<li>
										<a class="nav-link" href="https://www.hoopcoach.org/quick-tips-for-using-hoop-coach-playbook/" target= "_blank" style="color:black;">Quick Tips</a>
									</li>

									<li>
										<a class="nav-link" href="https://www.hoopcoach.org/hoop-coach-playbook-faqs/" rel="nofollow" style="color:black;">Help</a>
									</li>

								<li id="actions" class="dropdown">
										<div class="hidden-xs visible-sm-inline-block visible-md-inline-block visible-lg-inline-block">
											<a class="nav-link" id="menu-dd" data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Menu <span class="caret"></span></a>
											<ul class="dropdown-menu" aria-labelledby="menu-dd">
													<li>
														<a href="#" rel="nofollow" class="myplay" data-toggle="modal" data-target="#myplays-modal">My Plays</a>
													</li>
													<li>
														<a class="more78" href="<?= $path ?>add_student.php">Add Player</a>
													</li>
													<li>
														<a class="sign78" href="<?= $path ?>player/playbook.php" rel="nofollow">Playbook</a>
													</li>
													<li>
														<a class="pass78" href="<?= $path ?>coach_set_password.php" rel="nofollow">Playbook Password</a>
													</li>
													<li>
														<a class="playerList" href="/coach_student_list.php" rel="nofollow">Roster</a>
													</li>
													<li>
														<a class="loginLog" href="<?= $path ?>coach_student_tracking.php" rel="nofollow">Login Log</a>
													</li>
													<li>
														<a class="loginLog" href="<?= $path ?>my_affiliates.php" rel="nofollow">My Affiliates</a>
													</li>
													<li>
														<a href="reset_password.php">Change Password</a>
													</li>
													<li>
														<a href="<?= $path ?>basketball-plays.php" rel="nofollow" style="color:black;" id="logout_btn">Logout</a>
													</li>
											</ul>
										</div>

										<div class="visible-xs-block">
											<a class="nav-link" role="button" data-toggle="collapse" href="#collapseMenu" aria-expanded="false" aria-controls="collapseMenu">
												Menu
													<span class='caret'></span>
											</a>
											<ul id="collapseMenu" class="collapse list-unstyled" aria-labelledby="dLabel">
													<li>
														<a href="#" rel="nofollow" class="myplay" data-toggle="modal" data-target="#myplays-modal">My Plays</a>
													</li>
													<li>
														<a class="more78" href="<?= $path ?>add_student.php">Add Player</a>
													</li>
													<li>
														<a class="sign78" href="<?= $path ?>player/playbook.php" rel="nofollow">Playbook</a>
													</li>
													<li>
														<a class="pass78" href="<?= $path ?>coach_set_password.php" rel="nofollow">Playbook Password</a>
													</li>
													<li>
														<a class="playerList" href="<?= $path ?>coach_student_list.php" rel="nofollow">Roster</a>
													</li>
													<li>
														<a class="loginLog" href="<?= $path ?>coach_student_tracking.php" rel="nofollow">Login Log</a>
													</li>
													<li>
														<a class="loginLog" href="<?= $path ?>my_affiliates.php" rel="nofollow">My Affiliates</a>
													</li>
													<li>
														<a href="reset_password.php">Change Password</a>
													</li>
													<li>
															<a href="<?= $path ?>basketball-plays.php" rel="nofollow" style="color:black;" id="logout_btn">Logout</a>
													</li>
											</ul>
										</div>

									</li>

								<li id="logreglinks-container">
									<div id="logreglinks" style="display:inline-block">
										<a class="nav-link" href="#" data-toggle="modal" data-target="#login-modal" rel="nofollow" id="log">Login</a>
										<span style="color: white">|</span>
										<a class="nav-link" href="<?= $path ?>register.php?type=new" rel="nofollow" id="log">Register</a>
									</div>
								</li>
							</ul>
						</div>
					</div>
			</nav>

		<!-- CONTAINER -->
		<div class="container">

			<a id="download-hidden" href="" download="<?php echo $name . '.gif' ?>" hidden></a>

			<!-- <div id="largedownload"></div> -->
			<!-- <div id="new_movement_dialog" title="New Movement" style="display:none;">
				<div>Movement Name
			    	<input id="new_movement_name">
			  	</div>
			  	<div style="margin-top:15px;"> <b style="color:#a30;">Comments</b>
			    	<input id="new_movement_comments">
			  	</div>
			</div> -->

            <?php $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>
			<div id="pdf_warning_dialog" title="Pro Membership Feature" style="display:none;">
				PDF Printouts are a Pro Feature. <br>
				<a href="/playbook/premium/index.php?uid=<?= $user_id ?>">Click here</a> to upgrade
			</div>

			<div id="record_warning_dialog" title="Pro Membership Feature" style="display:none;">
				This is for Pro Users <br>
			  	<a href="/playbook/premium/index.php?uid=<?= $user_id ?>">Click here</a> to upgrade
			</div>

			<div id="new_play_warning_dialog" title="Pro Membership Feature" style="display:none;">
				Pro membership needed for creating more than 5 plays. <br>
				<a href="/playbook/premium/index.php?uid=<?= $user_id ?>">Click here</a> to upgrade
			</div>
			<div id="fb-root"></div>


			<div class="row">
				<!-- TODO: UNCOMMENT BELOW BEFORE UPLOADING AND REMOVE HEIGHT: 90PX -->
				<!-- <div class="col-xs-12 ad_container">

				    <?php if ($premium_user == 0) { ?>
					    <div class="ad_content center-block">
							<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
							<ins class="adsbygoogle"
							     style="display:block"
							     data-ad-client="ca-pub-2281684961690945"
							     data-ad-slot="3206579751"
							     data-ad-format="auto"></ins>
							<script>
							(adsbygoogle = window.adsbygoogle || []).push({});
							</script>

							<a class="a2a_button_sms"></a>
							<a class="a2a_button_facebook"></a>
							<a class="a2a_button_twitter"></a>
							<a class="a2a_button_google_plus"></a>

					    </div>
				    <?php 
						} ?>

			  	</div> -->
		  	</div>


		    <div class="row game-row">

				<!-- PROCESSING MODAL -->
				<div id="loading-modal" class="modal" tabindex="-1" role="dialog">
				  	<div class="modal-dialog" role="document">
				    	<div class="modal-content">

				      		<div class="modal-body">
				        		<h4 style="text-align: center;">Processing <br /> <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span></h4>
				      		</div>

				    	</div>
				 	</div>
				</div>

				<!-- DOWNLOADING MODAL -->
				<div id="downloading-modal" class="modal" tabindex="-1" role="dialog">
				  	<div class="modal-dialog" role="document">
				    	<div class="modal-content">

				      		<div class="modal-body">
				        		<h4 style="text-align: center;">Your play is being exported. <br /> Based on the size of the play this could take a minute. <br /> <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate" style="margin-top: 15px;"></span></h4>
				      		</div>

				    	</div>
				 	</div>
				</div>


				<!-- LOGIN MODAL -->
	      		<div id="login-modal" class="modal fade" tabindex="-1" role="dialog">
	      		  	<div class="modal-dialog" role="document">
	      		    	<div class="modal-content">
	      		      		<div class="modal-header">
	      		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      		        		<h4 class="modal-title">Login</h4>
	      		      		</div>
	      		      		<div class="modal-body">
	      		        		<div class="form-group">
	      		        			<label for="email">Email:</label>
	      		        			<input type="text" id="email" class="form-control" name="email" />
	      		        		</div>

	      		        		<div class="form-group">
	      		        			<label for="password">Password:</label>
	      		        			<input type="password" id="pass" class="form-control" name="password" />
	      		        			<a id="forgot_pass" class="pull-right" href="https://www.hoopcoach.org/playbook/pass/pass-support.php">forgot password?</a>
	      		        			<div class="clearfix"></div>
	      		        		</div>
	      		      		</div>
	      		      		<div class="modal-footer">
	      				        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	      				        <button id="login_btn" type="button" class="btn btn-primary" onClick="return false;">
	      				        	Login
	      				        	<span class="glyphicon glyphicon-refresh"></span>
	      				        </button>
	      		      		</div>
	      		    	</div>
	      		 	</div>
	      		</div>

				<!-- <div id="new_movement_dialog" title="New Movement" style="display:none;">
				<div>Movement Name
			    	<input id="new_movement_name">
			  	</div>
			  	<div style="margin-top:15px;"> <b style="color:#a30;">Comments</b>
			    	<input id="new_movement_comments">
			  	</div>
			</div> -->
				<!-- NEW MOVEMENT MODAL -->
	      		<div id="newmove-modal" class="modal fade" tabindex="-1" role="dialog">
	      		  	<div class="modal-dialog" role="document">
	      		    	<div class="modal-content">
	      		      		<div class="modal-header">
	      		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      		        		<h4 class="modal-title">New Movement</h4>
	      		      		</div>
	      		      		<div class="modal-body">
	      		        		<div class="form-group">
	      		        			<label for="new_movement_name">Name:</label>
	      		        			<input type="text" id="new_movement_name" class="form-control" name="new_movement_name" />
	      		        		</div>

	      		        		<div class="form-group">
	      		        			<label for="new_movement_comments">Comments:</label>
	      		        			<textarea id="new_movement_comments" class="form-control" name="new_movement_comments"></textarea>
	      		        		</div>
	      		      		</div>
	      		      		<div class="modal-footer">
	      				        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	      				        <button id="savmove_btn" type="button" class="btn btn-primary" onClick="return false;">
	      				        	Save
	      				        	<span class="glyphicon glyphicon-refresh"></span>
	      				        </button>
	      		      		</div>
	      		    	</div><!-- /.modal-content -->
	      		 	</div><!-- /.modal-dialog -->
	      		</div><!-- /.modal -->

				<!-- MY PLAYS MODAL -->
		    	<div id="myplays-modal" class="modal fade" tabindex="-1" role="dialog">
		    	  	<div class="modal-dialog" role="document">
		    	    	<div class="modal-content">
		    	      		<div class="modal-header">
		    	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		    	        		<h4 class="modal-title">My Plays</h4>
		    	      		</div>
		    	      		<div class="modal-body">
		    	        		<div class="table-responsive">
		    	        			<table id="playList" width="100%" class="table table-hover table-condensed">
		    	        				<thead>
							            	<tr>
								              <th width="50%">Name</th>
								              <th></th><!-- Play/Delete play -->
								            </tr>
							            </thead>
							          	<tbody>

						                    <?php
																									if (isset($_SESSION['user_id'])) {
																										foreach ($getAllPlaysResult as $key => $currentPlay) {
																											?>
						                    <tr>
						                    	<td width="50%" style="vertical-align: middle;"><?php echo $currentPlay['name']; ?></td>
						                      	<td data-play="<?php echo $currentPlay['id']; ?>" data-path="<?php echo $currentPlay['file']; ?>">
						                      		<a href="play.php?id=<?php echo $currentPlay['id']; ?>" class="btn btn-default btn-pb">
						                      			<!-- <img src="Model/Img/play.jpg" height="19" class="loadPlay" /> -->
						                      			<span class="loadPlay glyphicon glyphicon-play" style="color: red"></span>
						                      		</a>
						                      		<button class="btn btn-default btn-pb" onClick="remPlay(this)">
						                      			<span class="glyphicon glyphicon-trash" style="color: red"></span>
						                      		</button>
						                      		<!-- <img src="Model/Img/del.jpg" class="remPlay" onClick="remPlay(this)" /> -->
						                      	</td>
						                    </tr>
						                    <?php 
																								}
																							} ?>

							          	</tbody>
							        </table>
		    	        		</div>
		    	      		</div>
		    	      		<div class="modal-footer">
		    			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		    			        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
		    	      		</div>
		    	    	</div><!-- /.modal-content -->
		    	 	</div><!-- /.modal-dialog -->
		    	</div><!-- /.modal -->

				<!-- SAVE GAME MODAL -->
				<div id="savegame-modal" class="modal fade" tabindex="-1" role="dialog">
				  	<div class="modal-dialog" role="document">
				    	<div class="modal-content">
				      		<div class="modal-header">
				        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        		<h4 class="modal-title">Save Game</h4>
				      		</div>
				      		<div class="modal-body">

						         <div class="form-group">
						          	<select id="cat" size="9" class="form-control" required>
							            <?php
																		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
																		$conn->exec("set names utf8");

																		$sql = "SELECT * FROM category WHERE ispublic=1";
																		$st = $conn->prepare($sql);
																		$st->execute();

																		$getAllCategoriesInfoQueryResult = array();
																		while ($row = $st->fetch()) {
																			$getAllCategoriesInfoQueryResult[] = $row;
																		}

																		foreach ($getAllCategoriesInfoQueryResult as $key => $currentCategoryInfo) {
																			$curr_id = $currentCategoryInfo['id'];

																			$selected = '';
																			if ($curr_id == 9) {
																				$selected = 'selected';
																			}
																			echo "<option value='" . $curr_id . "' " . $selected . ">" . $currentCategoryInfo['name'] . "</option>";
																		}

																		?>
							        </select>
						         </div>

						         <div class="from-group">
						         	<label for="savename">Name:</label>
						         	<input type="text" id="savename" class="form-control" required>
						         </div>

						         <div class="from-group">
						         	<label for="save_tag">Tags:</label>
			            			<input type="text" id="save_tag" class="form-control" placeholder="Offense, Defense, Motion etc">
						         </div>
				      		</div>
				      		<div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						        <button id="saveg" type="button" class="btn btn-primary">
						        	Save
	      				        	<span class="glyphicon glyphicon-refresh"></span>
						        </button>
				      		</div>
				    	</div><!-- /.modal-content -->
				 	</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->

				<div id="pro-feature-modal" class="modal fade" tabindex="-1" role="dialog">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title">Pro Membership Feature</h4>
				      </div>
				      <div class="modal-body" style="text-align: center;">
				        <p id="pro-body">This is a Pro Feature.</p>
						<a href="/playbook/premium/index.php?uid=<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '' ?>" target="_blank" class="btn btn-success text-white">Upgrade Now</a>
				      </div>
				    </div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /.modal -->

				<div id="copy-play-modal" class="modal fade" tabindex="-1" role="dialog">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        <h4 class="modal-title">Play Copied</h4>
				      </div>
				      <div class="modal-body" style="text-align: center;">

				      </div>
				    </div><!-- /.modal-content -->
				  </div><!-- /.modal-dialog -->
				</div><!-- /.modal -->

				<div class="col-sm-12">
					<div class="pay_box" style="margin-top:10px;">
			          	<?php if (isset($_SESSION['user_id']) && $result['paid'] == 0) : ?>
				          <div style="font-weight:bold; font-size:135%; padding-left:5px; color:#0000ff; text-align:center;">Get Playbook Pro!</div>
				          <div style="text-align:center;">
				          	<a href="https://hoopcoach.org/playbook/premium/index.php?uid=<?= $_SESSION['user_id'] ?>"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="center" style="margin-right:7px;"></a>
				          </div>
				        <?php endif; ?>

				        <?php if (!isset($_SESSION['user_id'])) : ?>
				          <div style="font-weight:bold; font-size:135%; padding-left:5px; color:#0000ff; text-align:center;">Get Playbook Pro!</div>
				          <div style="text-align:center;">
				          	<a href="https://hoopcoach.org/playbook/register.php?type=new"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="center" style="margin-right:7px;margin-top:-"></a>
				          </div>
				        <?php endif; ?>
			    	</div>

				</div>

			    <!-- court -->
		      	<div class="col-sm-6 court">

		        	<div id="name-text" style="text-align:center"><?php echo $name; ?>&nbsp;</div>

		        	<div id="play-container">
		        		<button id="main-play" class="play-court play-court-hide btn btn-large"><span class="glyphicon glyphicon-play"></span></button>
		        	</div>

		        	<div id="container" style="text-align: center;"></div>

		        	<div class="col-xs-12 hidden-xs">
			        	<div class="bot col-sm-12">
				        	<ul id="rate-list" class="list-inline center-block" style="display: table;">
					            <li style="display: table-cell; vertical-align: top; width: 100%;">Rate this play<div id="star"></div></li>
							</ul>
				        </div>

				        <div class="bot col-sm-12">
				        	<div>
				        		Comment
					            <textarea id="comment" class="form-control" style="height: 67px; width: 100%;" placeholder="Comments will appear on PDF print outs"></textarea>
								<table style="margin-top:15px">
									<tr><td colspan="6"><em>Playbook Tagging for Pro Users</em></td></tr>
						          	<tr>
							        	<td style="text-align: left">Tags: <br /><input name="tags" type="text" id="tags" class="form-control" style="width:100px;border-color: gray;"></td>
							            <td style="text-align: left">Scout: <br /><input name="scout" id="scout" class="form-control" style="width:100px;border-color: gray;" value="<?php if (isset($res['scout'])) echo $res['scout']; ?>"></td>
							            <td></td>
							        </tr>
					        	</table>
				          	</div>
				        </div>
			        </div>

		      	</div>
		      	<!-- end court -->

		      	<!-- tools -->
		      	<div id="play-tools" class="col-xs-12 col-sm-6 tools">
		      		<div class="col-xs-12 tools-content center-block">

			        	<!-- COURT SIZE -->
			        	<div class="col-sm-5 tools-section tools-clip">
			            	<h2><strong>Court Size</strong></h2>

			            	<div class="form-group">
			            		<label class="text-info"><em>select court size</em></label>
							    <select id="court" class="form-control">
				                	<option value="0">Full Court</option>
				                  	<option value="1">Half Court</option>
			                	</select>
							</div>
			            </div>

			            <!-- ADD -->

			            <!-- offense -->
			            <div class="quickadd col-sm-offset-1 col-sm-6 tools-section tools-clip">
			            	<h2><strong>Quick Add</strong></h2>

			            	<div class="col-xs-6 col-sm-12 col-md-12 col-lg-6 quick" style="text-align: center;">
			            		<button id="autoadd" class="tool btn btn-default btn-pb center-block" data-target=".tools" data-element="#Player" data-type="players"><span class="glyphicon glyphicon-plus"></span> 5 Offense</button>
			            	</div>

			            	<div class="col-xs-6 col-sm-12 col-md-12 col-lg-6 quick" style="text-align: center;">
			            		<button id="autoadd_def" class="tool btn btn-default btn-pb center-block" data-target=".tools" data-element="#Forward" data-type="forwards"><span class="glyphicon glyphicon-plus"></span> 5 Defense</button>
			            	</div>
			            </div>

			            <!-- COURT OBJECTS -->
			            <div id="court-objects" class="col-xs-12 tools-section">
			            	<h2><strong>Court Objects</strong></h2>
			            	<h4 class="text-info"><em>tap or drag object to add to court</em></h4>
			            	<!-- Offense -->
				            <div id="offense-container" class="col-xs-6 col-sm-6 col-md-3 tools-section">

				            	<div class="btn-group" id="Player">
								  	<button type="button" class="btn btn-default btn-pb dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								    	Offense <span class="caret"></span>
								  	</button>
								  	<ul class="dropdown-menu list-inline" style="text-align:center;">
								    	<li> <img src="Model/Img/players/01.png" id="p1" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="125" data-y="353" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/02.png" id="p2" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="84" data-y="421" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/03.png" id="p3" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="186" data-y="410" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/04.png" id="p4" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="108" data-y="460" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/05.png" id="p5" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="176" data-y="463" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/06.png" id="p6" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="125" data-y="353" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/07.png" id="p7" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="84" data-y="421" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/08.png" id="p8" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="186" data-y="410" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/09.png" id="p9" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="108" data-y="460" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/10.png" id="p10" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="176" data-y="463" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/11.png" id="p11" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="125" data-y="353" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/12.png" id="p12" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="84" data-y="421" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/13.png" id="p13" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="186" data-y="410" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/14.png" id="p14" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="108" data-y="460" alt="Basketball Playbook Offense"> </li>
					                    <li> <img src="Model/Img/players/15.png" id="p15" class="ui-draggable clickable tool" data-category="Player" data-target=".tools" data-x="176" data-y="463" alt="Basketball Playbook Offense"> </li>
								  	</ul>
								</div>

				            </div>

				            <!-- Defense -->
				            <div id="defense-container" class="col-xs-6 col-sm-6 col-md-3 tools-section">

				            	<div class="btn-group" id="Forward">
								  	<button type="button" class="btn btn-default btn-pb dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								    	Defense <span class="caret"></span>
								  	</button>
								  	<ul class="dropdown-menu list-inline" style="text-align:center;">
								    	<li> <img src="Model/Img/forwards/x1.png" id="x1" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="82" data-y="90" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x2.png" id="x2" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="90" data-y="82" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x3.png" id="x3" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="166" data-y="155" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x4.png" id="x4" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="240" data-y="169" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x5.png" id="x5" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="169" data-y="240" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x6.png" id="x6" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="82" data-y="90" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x7.png" id="x7" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="90" data-y="82" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x8.png" id="x8" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="166" data-y="155" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x9.png" id="x9" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="240" data-y="169" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x10.png" id="x10" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="169" data-y="240" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x11.png" id="x11" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="82" data-y="90" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x12.png" id="x12" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="90" data-y="82" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x13.png" id="x13" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="166" data-y="155" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x14.png" id="x14" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="240" data-y="169" alt="Basketball Playbook Defense"> </li>
					                    <li> <img src="Model/Img/forwards/x15.png" id="x15" class="ui-draggable clickable tool" data-category="Forward" data-target=".tools" data-x="169" data-y="240" alt="Basketball Playbook Defense"> </li>
								  	</ul>
								</div>

				            </div>

				            <!-- Ball -->
				            <div id="ball-container" class="col-xs-6 col-sm-6 col-md-3 tools-section">

								<div class="btn-group" id="Ball">
								  	<button type="button" class="btn btn-default btn-pb dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								    	Ball <span class="caret"></span>
								  	</button>
								  	<ul class="dropdown-menu list-inline" style="text-align:center;">
										<li> <img src="Model/Img/balls/ball01.png" id="b0" data-category="Ball" class="ui-draggable ui-draggable-handle clickable tool" /> </li>
						                <li> <img src="Model/Img/balls/ball02.png" id="b1" data-category="Ball" class="ui-draggable ui-draggable-handle clickable tool" /> </li>
										<li style="display: none"> <img src="Model/Img/balls/ball02.png" id="b2" data-category="Ball" class="ui-draggable ui-draggable-handle clickable tool" /> </li>
								  	</ul>
								</div>

				            </div>

				            <!-- Cone -->
				            <div id="cone-container" class="col-xs-6 col-sm-6 col-md-3 tools-section">

				            	<div class="btn-group" id="Cone">
								  	<button type="button" class="btn btn-default btn-pb dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								    	Cone <span class="caret"></span>
								  	</button>
								  	<ul class="dropdown-menu list-inline" style="text-align:center;">
										<li> <img src="Model/Img/forwards/cone01.png" id="c1" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone02.png" id="c2" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone03.png" id="c3" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone04.png" id="c4" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone05.png" id="c5" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone06.png" id="c6" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone07.png" id="c7" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone08.png" id="c8" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone09.png" id="c9" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone10.png" id="c10" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone11.png" id="c11" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone12.png" id="c12" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone13.png" id="c13" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone14.png" id="c14" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
					                    <li> <img src="Model/Img/forwards/cone15.png" id="c15" class="ui-draggable clickable tool" data-category="Cone" data-target=".tools" /> </li>
								  	</ul>
								</div>
				            </div>

				            <div class="col-xs-offset-3 col-xs-6">
			              		<!-- <td width="20%" class="tool" data-target=".tools"><img src="img/images/recycle.png" id="del"></td> -->
			              		<h4 class="text-info" style="margin-bottom: 5px;"><em>double click object on court then tap below to remove</em></h4>
				            	<button id="del" class="btn btn-default btn-pb btn-lg center-block"><span class="glyphicon glyphicon-trash"></span></button>
				            </div>
			            </div>

				        <div class="clearfix" />


						<!-- MOVEMENT OPTIONS -->
				        <div id="record" class="col-xs-12 col-sm-5 tools-section tools-clip">
				        	<h2><strong>Movement Options</strong></h2>

				          	<ul class="list-inline" style="text-align: center;">
					            <li class="tool" data-target=".tools">
					              	<button id="ren_move" class="btn btn-default btn-pb btn-lg center-block">
					              		<span class="glyphicon glyphicon-edit"></span>
					              	</button>
					              	rename
					            </li>

					            <?php if (!$is_student) : ?>
					              	<li class="tool" data-target=".tools">
						              	<button id="del_move" class="btn btn-default btn-pb btn-lg center-block">
						              		<span class="glyphicon glyphicon-trash"></span>
						              	</button>
						              	delete
					              	</li>
					            <?php endif; ?>

					            <li class="tool" data-target=".tools">
					              	<button id="add_movement" data-toggle="modal" data-target="#newmove-modal" class="btn btn-default btn-pb btn-lg center-block">
					              		<span class="glyphicon glyphicon-plus"></span>
					              	</button>
					              	add
					            </li>

								<div class="clearfix visible-xs-block"></div>

								<?php if (isset($_SESSION['user_id']) && $result['paid'] == 1) { ?>
					            	<li>
						                <input name="private" type="checkbox" value="1" id="private" <?php if ($priv == 1) echo 'checked="checked"'; ?>>
						                <br />
						                <label style="font-size: 14px;">Set Private</label>
					            	</li>
					             <?php 
																} ?>

					            <li>
				            		<select id="moves" class="form-control center-block">
				                  		<option value="0" selected="selected">Initial Set</option>
				                	</select>
				               	</li>
				          	</ul>
				        </div>


				        <!-- ANIMATION CONTROLS -->
			        	<div id="controls" class="col-xs-12 col-sm-offset-1 col-sm-6 tools-section tools-clip">
				            <h2><strong>Animation Controls</strong></h2>

							<ul class="list-inline" style="text-align: center;">
				              	<li>
				        			<button class="nxtFrame btn btn-default btn-pb">
				        				<span class="glyphicon glyphicon-fast-forward"></span>
				        				<!-- <br />skip -->
				        			</button>
				        		</li>

				              	<li>
				              		<button id="play" class="play-court btn btn-default btn-pb">
				        				<span class="glyphicon glyphicon-play"></span>
				        				<!-- <br />play -->
				        			</button>
				              	</li>

				               	<li>
							    	<button id="start" class="tool btn btn-default btn-pb" contenteditable="false"><strong>Export.gif</strong></button>
							    </li>

				            	<li>
				            		<label for="speed" class="text-info"><em>play speed</em></label>
					                <select id="speed" class="form-control">
					                  <option value="500">Very Fast</option>
					                  <option value="1000">Fast</option>
					                  <option value="1500" selected="">Normal</option>
					                  <option value="2000">Slow</option>
					                </select>
				                </li>
				            </ul>
				        </div>

						<div class="clearfix"></div>


						<!-- ARROW DRAWING TOOLS -->
				        <div id="drawing_tools" class="col-sm-5 tools-section tools-clip">
				        	<h2><strong>Drawing Tools</strong></h2>
				        	<ul class="list-inline" style="text-align: center;">
				        		<li>
				        			<button id="wcut" class="btn btn-default btn-control center-block" onClick="return sline();"><img src="img/images/cut.png" srcset="img/images/cut.png 2x" alt="Cut Playbook Tool"></button>
				        			cut
				                </li>

				                <li>
				                	<button id="wpass" class="btn btn-default btn-control center-block" onClick="return sline(true);"><img src="img/images/pass.png" srcset="img/images/pass.png 2x" alt="Pass Playbook Tool"></button>
				                	pass
				                </li>

				                <li>
				                	<button id="wdribble" class="btn btn-default btn-control center-block" onClick="return sline(false,false,true);"><img src="img/images/dribble.png" srcset="img/images/dribble.png 2x" alt="Dribble Playbook Tool"></button>
				                	dribble
				                </li>

				                <li>
				                	<button id="wscreen" class="btn btn-default btn-control center-block" onClick="return sline(false,true);"><img src="img/images/screen.png" srcset="img/images/screen.png 2x" alt="Screen Playbook Tool"></button>
				                	screen
				                </li>

				                <li>
				                	<button id="wcurved" class="btn btn-default btn-control center-block" onClick="return s_pline();"><img src="img/images/curve.png" srcset="img/images/curve.png 2x" alt="Curved Playbook Tool"></button>
				                	curved
				                </li>
				        	</ul>
				        </div>

				        <!-- PLAY MANAGEMENT -->
				        <div class="col-xs-12 col-sm-offset-1 col-sm-6 tools-section tools-clip">
				        	<h2><strong>Play Management</strong></h2>

				        	<ul class="list-inline" style="text-align: center;">
				        		<li>
				        			<form id="pdf_form" target="_blank" action="pdf/reports.php" method="POST">
					                  	<input type="hidden" id="user_id" class="form-control" name="userid" val="">
					                  	<input type="hidden" name="id" id="id" class="form-control" val="" value="">
					                  	<!-- <input type="image" class="form-control" src="img/images/pdf_dwnld.png" id="pdf"> -->
					                  	<button id="pdf" class="tool btn btn-default btn-pb btn-lg center-block" data-target=".tools">
					                  		<span class="glyphicon glyphicon-file"></span>
					                  	</button>
					                  	download pdf
					                </form>
				        		</li>
				        		<li>
				        			<a href="play.php" id="new_play_btn" class="pro-button tool btn btn-default btn-pb btn-lg center-block" data-target=".tools" style="font-size: 18px;" data-type="plays" data-desc="Pro membership needed for creating more than 5 plays.">
					        			<span class="glyphicon glyphicon-plus"></span>
				        			</a>
				        			new
				        		</li>
								<?php $disabled = false;
							if (isset($_SESSION['user_id']) && $plays_count >= 5 && $result['paid'] == 0) {
								$disabled = true;
							} ?>
				        		<?php if (!$is_student) : ?>
				              		<li class="tool" data-target=".tools">
				                		<button id="save_btn" class="tool btn btn-default btn-pb btn-lg center-block" data-toggle=".tools" <?= $disabled ? 'disabled' : '' ?>>
					        				<span class="glyphicon glyphicon-floppy-save"></span>
					        			</button>
					        			save
				            		</li>
				                <?php endif; ?>

								<?php if (isset($_SESSION['user_id']) && isset($_REQUEST['id'])) : ?>
									<li>
										<a href="#" class="copy_play_btn pro-button tool btn btn-default btn-pb btn-lg center-block" from-id="<?= $_REQUEST['id'] ?>" to-user="<?= $_SESSION['user_id'] ?>"><span class="glyphicon glyphicon-duplicate"></span></a>
										Copy
									</li>
					          	<?php endif; ?>
				        	</ul>
				        </div>

				        <div class="col-xs-12 visible-xs-block">
				        	<div class="bot col-sm-12">
					        	<h2><strong>Rate this play</strong></h2>

					        	<ul id="rate-list" class="list-inline" style="display: table;">
						            <li style="display: table-cell; vertical-align: top;">

						            	<div id="star-xs" style="height: 28px;"></div>
						            </li>
								</ul>
					        </div>

					        <div class="col-sm-12">
								<div class="pay_box" style="">

						          	<?php if (isset($_SESSION['user_id']) && $result['paid'] == 0) : ?>
							          <div style="font-weight:bold; font-size:135%; padding-left:5px; color:#0000ff; text-align:center;">Get Playbook Pro!</div>
							          <div style="text-align:center;">
							          	<a href="/premium/index.php?uid=<?= $_SESSION['user_id'] ?>"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="center" style="margin-right:7px;"></a>
							          </div>
							        <?php endif; ?>

							        <?php if (!isset($_SESSION['user_id'])) : ?>
							          <div style="font-weight:bold; font-size:135%; padding-left:5px; color:#0000ff; text-align:center;">Get Playbook Pro!</div>
							          <div style="text-align:center;">
							          	<a href="https://hoopcoach.org/playbook/register.php?type=new"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="center" style="margin-right:7px;margin-top:-"></a>
							          </div>
							        <?php endif; ?>
						    	</div>
							</div>

					        <div class="bot col-sm-12">
					          <div>Comment
					            <textarea id="comment" class="form-control" style="height: 67px; width: 100%;" placeholder="Comments will appear on PDF print outs"></textarea>
								<table style="margin-top:15px">
								<tr><td colspan="6"><em>Playbook Tagging for Pro Users</em></td></tr>
						          	<tr>
						            <!-- <td style="font-weight: bold;">Tags:</td> -->
						            <td style="text-align: left">Tags: <br /><input name="tags" type="text" id="tags-xs" class="form-control" style="width:100px;border-color: gray;"></td>
						            <!-- <td style="font-weight: bold;"></td> -->
						            <td style="text-align: left">Scout: <br /><input name="scout" id="scout-xs" class="form-control" style="width:100px; border-color: gray;" value="<?php if (isset($res['scout'])) echo $res['scout']; ?>"></td>
						          </tr>
						        </table>
					          </div>
					        </div>
				        </div>


			    	</div>
			    	<!-- end tools content -->
		      	</div>
	      		<!-- end tools -->
		    </div>
		</div>

		<div class="overlay container-fluid visible-xs-block" style="display: <?= $showOverlay ? 'block' : 'none!important' ?>">
			<div class="row">
				<div class="col-xs-6">
					<img src="img/tools_overlay.png" srcset="img/tools_overlay@2x.png 2x" alt="basketball playbook tools" style="margin-top: 45px; margin-left: 15px;">
				</div>

				<div class="col-xs-6" style="text-align: right; height: 50vh; padding-right: 0;">
					<img src="img/scroll_overlay.png" srcset="img/scroll_overlay@2x.png 2x" alt="basketball playbook plays" class="pull-right" style="position: absolute; bottom: 0; right: 0;">
				</div>

				<div class="col-xs-12">
						<img src="img/flip_overlay.png" srcset="img/flip_overlay@2x.png 2x" alt="basketball playbook" class="center-block">
				</div>

				<div class="col-xs-6" id="tap_overlay">
					<img src="img/tap_overlay.png" srcset="img/tap_overlay@2x.png 2x" alt="basketball playbook" class="pull-left">
				</div>

				<div class="form-group" id="never_show">
					<div class="col-xs-12">
				    	<div class="checkbox">
					      	<label>
					          <input id="overCheck" type="checkbox"> Never show this again
					        </label>
				      </div>
				    </div>
				</div>

				<!-- <div class="col-xs-6" id="never_show">
					<div class="checkbox">
					  <label>
					    <input id="overCheck" type="checkbox" class="form-control" value="">
					    Never show this again
					  </label>
					</div>
				</div> -->

			</div>
		</div>

	<!-- TITLE TAG ISSUE WORKAROUND -->
	<script>
		$(window).load(function()
		{
		 	// executes when complete page is fully loaded, including all frames, objects and images
	      	//console.log('meta title: ', $('meta[property="og:title"]').attr('content'));

	      	// workaround for page title isssue
	      	$('title').text($('meta[property="og:title"]').attr('content'));
		});

	 	// COPY PLAY BUTTON
		$('.copy_play_btn').click(function()
		{
			// console.log('copy_play_btn click from: ' + $(this).attr('from-id') + ' to: ' + $(this).attr('to-user'));

			$.post('copy_play.php', {from_id: $(this).attr('from-id'), to_user: $(this).attr('to-user')}, function(data)
			{
				if(typeof(data.data_res) != "undefined")
				{
					// console.log('not undefined!');
					$('#copy-play-modal .modal-body').html(data.msg+"</br>"+"<a href='play.php?id="+data.data_res['id']+"&user="+data.data_res['userid']+"&name="+data.data_res['name']+"&category="+data.data_res['cat']+"'>Click this link to go to copied</a>");
				} else {
					// console.log('not undefined!');

					$('#copy-play-modal .modal-body').html(data.msg);
				}
				$('#copy-play-modal').modal('show');

				// $('#new_play_warning_dialog').dialog('open');
			}, 'json')
			return false;
		});
	</script>

	<!-- TOOLS/OVERLAY ANIMATION -->
	<script>
		$(".tools-toggle, .tool").click(function()
		{
			var tools = $($(this).attr('data-target'));


			if ($(tools).hasClass('showing'))
			{
				// remove css class which will animate the tools offscreen
				$(tools).removeClass('showing');

				// allow html scrolling
				$('html').removeClass('tools-showing');
				// remove content shadow to body
				$('body').removeClass('tools-showing');

			} else {

				// add css class which will animate the tools onscreen
				$(tools).addClass('showing');

				// prevent html/body scrolling so that the page doesn't scroll only the tools will scroll
				$('html').addClass('tools-showing');
				// add content shadow to body
				$('body').addClass('tools-showing');
			}
		});


		// OVERLAY CLICK
		$('.overlay').click(function()
		{
//			//console.log('cookie set? ', document.cookie);
			$(this).remove();
		});

		$('#overCheck').bind('change', function()
		{
//			//console.log('overCheck changed: ', $(this).is(':checked'));

			if ($(this).is(':checked'))
			{
//				//console.log('checkbox is checked! ', $(this).is(':checked'));
				// This will set the exp date to several years in the future
				// setcookie("visited", true, $time);
				setCookie('visited', true, 10000);

				// trigger overlay click
				$('.overlay').click();
			}
		});

		function setCookie(cname, cvalue, exdays)
		{
		    var d = new Date();
		    d.setTime(d.getTime() + (exdays*24*60*60*1000));
		    var expires = "expires="+d.toUTCString();
		    document.cookie = cname + "=" + cvalue + "; " + expires;
		};

	</script>
	<!-- END TOOLS/OVERLAY ANIMATION -->

	<!-- MODALS -->
	<script>
		$('#loading-modal').modal(
		{
			backdrop: 'static',
			keyboard: false
		});
		$('#login-modal').on('hidden.bs.modal', function (e)
		{
			$('#login_btn').find('.glyphicon-refresh').removeClass('glyphicon-refresh-animate');
		});
	</script>
	<!-- END MODALS -->

	<!-- Start of StatCounter Code for Default Guide -->
	<script type="text/javascript">
		/*
		var sc_project=9286366;

		var sc_invisible=1;

		var sc_security="8033a67e";

		var scJsHost = (("https:" == document.location.protocol) ?

		"https://secure." : "http://www.");

		document.write("<sc"+"ript type='text/javascript' src='" +

		scJsHost+

		"statcounter.com/counter/counter.js'></"+"script>");
		*/
	</script>
	<!--<noscript>
	<div class="statcounter"><a title="web stats"

	href="http://statcounter.com/" target="_blank"><img

	class="statcounter"

	src="http://c.statcounter.com/9286366/0/8033a67e/0/"

	alt="web stats"></a></div>
	</noscript>-->

	<!-- End of StatCounter Code for Default Guide -->
	<script type="text/javascript">
		/*
		  var _gaq = _gaq || [];

		  _gaq.push(['_setAccount', 'UA-1535786-5']);

		  _gaq.push(['_trackPageview']);


		  (function() {

		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;

		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';

		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);

		  })();

		*/
	</script>


	<div style="clear:both;"></div>
</body>
</html>

<?php

	// remove fake user session for admin user at the end of page

	// if(isset($_SESSION['admin']) || isset($_SESSION['student_id'])){

	// 	unset($_SESSION['user_id']);

	// }

?>
