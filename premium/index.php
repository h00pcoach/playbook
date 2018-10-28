<?php
session_start();

$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
$affiliate = isset($_GET['affiliate']) ? $_GET['affiliate'] : 0;

require('../mydb_pdo.php');

// Initialize PDO
$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

$sql = "SELECT * FROM settings";
$st = $conn->prepare($sql);

// Bind parameters
$st->execute();
$settings = $st->fetch();
$conn = null;

// include("../pay/pay_checkout.php")/*  */
include("../pay/pay_recurring.php")
?>

<!DOCTYPE HTML>
<!--
	Miniport by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Hoop Coach Playbook Pro</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--[if lte IE 8]><script src="css/ie/html5shiv.js"></script><![endif]-->
		
	</head>
	<body>
		<!-- Nav -->
		<nav id="nav">
			<ul class="container">
				<li><a href="http://hoopcoach.org/playbook/blog/wp-content/uploads/2014/08/Pick-and-Roll-motion-offense.pdf">PDF Demo</a></li>
				<li><a href="https://docs.google.com/document/d/1SEBDNNozg8mcicFeR6EZUE-iGukZvQYhT1gZy2VlkNE/pub">Using the Playbook </a></li>
				<li><a href="http://hoopcoach.org/playbook/basketball-plays.php">Plays Library</a></li>
				<li><a href="http://www.hoopcoach.org/contact-us">Contact</a></li>
			</ul>
		</nav>

		<!-- Home -->
		<div class="wrapper style1 first">
			<article class="container" id="top">
				<div class="row">
				<div class="4u">
						<span class="image fit"><img src="images/pic00.png" alt="" /></span>
				</div>
				<div class="8u">
					<header>
						<h1>Hoop Coach Playbook <strong>Pro</strong>.</h1>
					</header>
					<ul>
						<b>
							<li>Save plays directly to your devices for easy texting or offline viewing - <a href="http://www.hoopcoach.org/wp-content/uploads/2015/07/Michigan-State-Man-Set.gif">see example</a>
							<li>Create Unlimited Plays</li>
							<li>Print your Plays to PDF</li>
							<li>Make your Plays Private or Share them in the Plays Library
							<li>Private Playbook page where players can login to learn your plays</li>
							<li>Scouting Reports: Add your opponents playbook for you team to study. Get an edge</li>
							<li>Track which players are logging in</li>
							<li>Unlimited Play Copying from our Plays Library. Build your playbook quickly!</li>
						</b>
						<br>
						<h3><font color="#000099">Save Gym Time.  Teach your Players at Home.</font></h3>
					</ul>
					<h5>$<?= $settings['monthly_amount'] ?>/month or $<?= $settings['yearly_amount'] ?> per year -  <i>(Paypal or Debit/Credit)</i></h5>
					<div>
						Choose Payment Plan:
						<input type="radio" name="payment_type" value="yearly" checked>Yearly
						<input type="radio" name="payment_type" value="monthly">Monthly
					</div>
					<!-- <a href="#" id="submit-button" class="button big scrolly">Upgrade Now</a> -->
					<!-- <div id="paypal-button" class="button big scrolly">Upgrade Now</div> -->
  						<div id="paypal-button-container"></div>
					<br>
					<div>
						<a href="../play.php"><u><i>Later</i></u></a> | Back to <a href="https://www.hoopcoach.org">HoopCoach.org</a>
					</div>

				</div>
			</article>
		</div>
		<footer>
			<!-- <ul id="copyright">
				<li>&copy; Untitled. All rights reserved.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
			</ul> -->
		</footer>

		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.scrolly.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/init.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
		</noscript>
		<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->
		<!--[if lte IE 9]><link rel="stylesheet" href="css/ie/v9.css" /><![endif]-->
		<script type="text/javascript">
			// $(function(){
        // $("input[name='payment_type']").click(function() 
        // {
        //     var pay_type = this.value;
        //     console.log(`pay_type: ${pay_type}`);
        // });

        // $('#paypal-button').click(function() {
        //   var payment_type = $('input[name=payment_type]:checked').val()
        //   console.log(`payment_type: ${payment_type}`);
        // })
				// $('#payment_btn').click(function(){
				// 	location.href = '../pay/pay.php?uid=<?= $uid ?>&affiliate=<?= $affiliate ?>&payment_type='+$('input[name=payment_type]:checked').val();
				// 	return false;
				// })
			// })
		</script>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', 'UA-1535786-5', 'auto');
			ga('send', 'pageview');

		</script>
		<!-- Google Code for Playbook Purchase Conversion Page -->
		<script type="text/javascript">
			/* <![CDATA[ */
			var google_conversion_id = 1067762637;
			var google_conversion_language = "en";
			var google_conversion_format = "3";
			var google_conversion_color = "ffffff";
			var google_conversion_label = "JrVQCPnP1GEQzYeT_QM";
			var google_conversion_value = 29.00;
			var google_conversion_currency = "USD";
			var google_remarketing_only = false;
			/* ]]> */
		</script>
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
		<noscript>
			<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1067762637/?value=29.00&amp;currency_code=USD&amp;label=JrVQCPnP1GEQzYeT_QM&amp;guid=ON&amp;script=0"/>
			</div>
		</noscript>

	</body>
</html>
