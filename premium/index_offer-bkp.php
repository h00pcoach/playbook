<?php 
session_start(); 
// var_dump($_SESSION['user_id']);
$uid = $_GET['uid'];

include '../mydb.php';
$sql = 'select * from settings';
$settings = mysql_fetch_array(mysql_query($sql));
?>

<!DOCTYPE HTML>
<!--
	Miniport by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Basketball Playbook Premium</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--[if lte IE 8]><script src="css/ie/html5shiv.js"></script><![endif]-->
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
			$(function(){
				$('#payment_btn').click(function(){
					location.href = '/pay/pay.php?uid=<?=$uid?>&payment_type='+$('input[name=payment_type]:checked').val();
					return false;
				})
			})
		</script>
	</head>
	<body>
      <!-- Nav -->
			<nav id="nav">
				<ul class="container">
					<li><a href="http://www.basketballplaybook.org/blog/wp-content/uploads/2014/08/Pick-and-Roll-motion-offense.pdf">PDF Demo</a></li>
					<li><a href="https://docs.google.com/document/d/1SEBDNNozg8mcicFeR6EZUE-iGukZvQYhT1gZy2VlkNE/pub">Using the Playbook </a></li>
					<li><a href="http://www.basketballplaybook.org/basketball-plays.php">Plays Library</a></li>
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
								<h1>Basketball Playbook <strong>Premium</strong>.</h1>
							</header>
							<ul><b>
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
<h5>$<?=$settings['monthly_discounted_amount']?>/month or $<?=$settings['yearly_discounted_amount']?> per year -  <i>(Paypal or Debit/Credit)</i></h5>
                          </p>
	<div>
                       			Choose Payment Plan:
                       			<input type="radio" name="payment_type" value="yearly_discounted" checked>Yearly
                       			<input type="radio" name="payment_type" value="monthly_discounted">Monthly
                       		</div>
							<a href="#" id="payment_btn" class="button big scrolly">Upgrade Now</a><br>
<div style="margin-left:75px;"><a href="../play.php"><u><i>Later</i></u></a></div>
							
						</div>
					</div>
				</article>
			</div>

		
					<footer>
						<ul id="copyright">
							<li>&copy; Untitled. All rights reserved.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
						</ul>
					</footer>
				</article>
			</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-1535786-5', 'auto');
  ga('send', 'pageview');

</script>
	</body>
</html>
