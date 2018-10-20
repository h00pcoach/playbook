<?php
	error_reporting(0);
	include('hoopcoach_secret.php');
	include('download_image.php');
    // TODO TODO TODO: UNCOMMENT FOLLOWING LINE (Ning) BEFORE PUSHING LIVE ********************************************
	// require_once('NingApi.php');
	require_once('mydb.php');

	$subdomain = 'hoopcoach';
    // TODO TODO TODO: UNCOMMENT FOLLOWING LINE (Ning) BEFORE PUSHING LIVE ********************************************
	// $ningApi = new NingApi();
	session_start();

	$paid = false;
	if(isset($_SESSION['user_id'])){
		$r = mysql_query("select * from users where id={$_SESSION['user_id']}");
		$user = mysql_fetch_array($r);
		$paid = $user['paid'];
	}


?>
<!doctype html>
<html>
<head>
<script type="text/javascript">
window.google_analytics_uacct = "UA-1535786-5";
</script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Free Basketball Plays created with Basketball Playbook</title>
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.11.1/themes/ui-lightness/jquery-ui.css">
<script type="text/javascript" src="js/jquery.raty.min.js"></script>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<style type="text/css">
@charset "UTF-8";
a, a img {
	outline: none;
	border: none
}
html, body {
	padding: 0;
	margin: 0;
	position: relative;
	letter-spacing: 1px;
	font-family: Georgia, "Times New Roman", Times, serif;
}
.spacer a {
	margin: auto 7px;
}
.spacer a:first-child {
	margin-left: 0px;
	margin-right: 8px
}
.spacer a:last-child {
	margin-right: 0px
}
.caption {
	font-size: 20px;
	font-weight: normal;
	background: url(Model/Img/balls/ball01.png);
	background-repeat: no-repeat;
	background-position: 13px center;
	padding-top: 20px;
	height: 50px;
}
#container {
	padding-top: 20px;
	width: 600px;
	margin: 0 auto;
	float: left
}
.ad {
	padding-top: 20px;
	float: right;
	width: 336px;
}
.side {
	padding-top: 20px;
	float: left;
	width: 160px;
}
table {
	border-collapse: collapse;
	border-spacing: 0;
	width: 100%;
	-webkit-box-shadow: 0px 2px 1px 5px rgba(242, 242, 242, 0.1);
	box-shadow: 0px 2px 1px 5px rgba(242, 242, 242, 0.1);
}
.zebra td {
	padding: 10px;
	text-align: left;
}
.category:hover {
	background: #DDD;
	background: rgba(200,200,200,.5);
	color: #000;
}
.zebra tr {
	background: transparent;
	color: #000;
	margin-bottom: 20px
}
.category {
	border-left: 4px black solid;
}
.spacer {
	border: none;
	background: transparent
}
.zebra>tbody>tr:nth-child(2n+2):hover, .spacer:hover {
	border: none;
	background: transparent
}
.subcategory {
	display: none
}
h1, h2, h3, h4, p, h5 {
	margin: 0
}
a {
	-webkit-transition: all 0.2s ease-in-out;
	-moz-transition: all 0.2s ease-in-out;
	-ms-transition: all 0.2s ease-in-out;
	-o-transition: all 0.2s ease-in-out;
	transition: all 0.2s ease-in-out;
	text-decoration: none
}
#topbar {
	background: #DDD;
	background: rgba(227,227,227,0.5);
	height: 39px;
	font-size: 10px;
}
.social-icons {
	float: right;
	width: 50%;
}
.social-icons ul {
	list-style: none;
	margin: -1px 0 -1px 0;
	float: right;
}
.social-icons ul li {
	display: block;
	float: left;
	margin: 0;
	padding: 0;
}
.container {
	width: 960px;
	margin: auto;
	padding: 0px
}
.social-twitter a {
	background: url(Model/Img/twitter.png) no-repeat 0 0;
}
.subcategory tr {
	border: none
}
.social-icons ul li a {
	-webkit-transition: all 0.2s ease 0s;
	-moz-transition: all 0.2s ease 0s;
	-o-transition: all 0.2s ease 0s;
	transition: all 0.2s ease 0s;
	display: block;
	width: 40px;
	height: 40px;
	text-indent: -9999px;
	background-position: 0px 0px;
	background-repeat: no-repeat;
	opacity: 0.6;
}
.social-icons a:hover {
	background-color: #CCC;
	background-position: 0px -40px !important;
	opacity: 1;
}
.social-twitter a:hover {
	background-color: #48C4D2;
}
.social-facebook a:hover {
	background-color: #3B5998;
}
.social-googleplus a:hover {
	background-color: #D94A39;
}
.social-youtube a:hover {
	background-color: #1a85bc;
}
.social-googleplus a {
	background: url(Model/Img/googleplus.png) no-repeat 0 0;
}
.social-facebook a {
	background: url(Model/Img/facebook.png) no-repeat 0 0;
}
.social-youtube a {
	background: url(Model/Img/linkedin.png) no-repeat 0 0;
}
header .logo {
	margin-top: 32px;
	width: 200px;
	float: left;
	margin-bottom: 0 !important;
	-webkit-transition: all .3s ease-in-out;
	-moz-transition: all .3s ease-in-out;
	-ms-transition: all .3s ease-in-out;
	-o-transition: all .3s ease-in-out;
	transition: all .3s ease-in-out;
}
header .logo img {
	max-width: 100%;
	height: auto;
}
header .logo:hover {
	opacity: .7;
}
nav#main {
	float: right;
	width: 700px;
}
nav#main ul {
	margin: 0;
	margin-top: 56px;
	float: right;
	list-style: none;
}
nav#main ul li {
	display: block;
	float: left;
	position: relative;
	margin: 0;
	line-height: 1;
}
nav#main ul li a {
	display: block;
	float: left;
	margin: 0 0 0 0px;
	padding: 11px 15px;
	font-family: Montserrat, Arial, Helvetica, sans-serif;
	font-size: 16px;
	font-weight: normal;
	color: #000;
	padding-left: 20px !important;
	border-radius: 6px 6px 6px 6px;
}
nav#main ul li:hover > a {
	background-color: #151515 !important;
	background-image: -webkit-gradient(linear, left top, left bottom, from(#151515), to(#404040)) !important;
	background-image: -webkit-linear-gradient(top, #151515, #404040) !important;
	background-image: -moz-linear-gradient(top, #151515, #404040) !important;
	background-image: -ms-linear-gradient(top, #151515, #404040) !important;
	background-image: -o-linear-gradient(top, #151515, #404040) !important;
	background-image: linear-gradient(top, #151515, #404040) !important;
	color: white !important;/*background: #EA3024 url(Model/Img/bullet_white.png) no-repeat 8px center !important;
color: white;*/
}
.content {
	width: 960px;
	margin: auto
}
.search {
	float: left;
	margin-right: -350px;
	margin-top: -10px;
}
.search input.text {
	outline: none;
	width: 180px;
	margin: 0px;
	border-right: 0px;
	border-top-right-radius: 0px;
	border-bottom-right-radius: 0px;
}
.search input.text, .search button {
	border-radius: 1em;
	border: 1px solid #CCC;
	height: 2em;
	padding: 0em 1em;
	display: block;
	float: left;
	font-size: 100%;
	-webkit-appearance: none;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	box-shadow: none;
}
.search select {
	border-radius: 1em;
	border: 1px solid #CCC;
	height: 2em;
	padding: 0em 1em;
	display: block;
	float: left;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	box-shadow: none;
	float: left;
	font-size: 60%;
	margin-top: 10px;
}
.search button {
	width: 50px;
	background: #F3F3F3 url(Model/search.png) no-repeat 45% 50%;
	background-size: auto 60%;
	border-top-left-radius: 0px;
	border-bottom-left-radius: 0px;
	text-indent: 200%;
	white-space: nowrap;
	overflow: hidden;
	margin-left: -20px;
}
.star img {
	width: 25px;
}
.star {
	float: left;
}
</style>
<script type="text/javascript">
$(document).ready(function(e){
	$('.star').raty({
		size:12,
		width:150,
		space:false,
		halfShow:false,
		hints: ['bad', 'poor', 'average', 'good', 'superb'],
		readOnly:true,
	  starOff: 'Model/Img/balls/ball04.png',
	  starOn : 'Model/Img/balls/ball03.png',
	  score: function() {
		return $(this).attr('data-score');
	  }
	});

	$('.copy_play_btn').click(function(){
		console.log($(this).attr('from-id'), $(this).attr('to-user'));
		$.post('copy_play.php', {from_id: $(this).attr('from-id'), to_user: $(this).attr('to-user')}, function(data){
			if(typeof(data.data_res) != "undefined")
			{
				$('#new_play_warning_dialog').html(data.msg+"</br>"+"<a href='play.php?id="+data.data_res['id']+"&user="+data.data_res['userid']+"&name="+data.data_res['name']+"&category="+data.data_res['cat']+"'>Click this link to go to copied</a>");
			}
			else
			{
				$('#new_play_warning_dialog').html(data.msg);
			}
			$('#new_play_warning_dialog').dialog('open');
		}, 'json')
		return false;
	});

	$('#pdf_warning_dialog').dialog({
		modal: true,
		autoOpen: false,
		width: 350
	})
	<?php if(!$paid): ?>
	$('.download_pdf_btn').click(function(){
		$('#pdf_warning_dialog').dialog('open');
		return false;
	})
	<?php endif; ?>


	$('#new_play_warning_dialog').dialog({
		modal: true,
		autoOpen: false,
		width: 350
	})
});
</script>
</head>

<body>

<div id="pdf_warning_dialog" title="Pro Membership Feature" style="display:none;">
	PDF Printouts are a Pro Feature. <br><a href="premium/index.php?uid=<?=$_SESSION['user_id']?>">Click here</a> to upgrade
</div>

<div id="new_play_warning_dialog" title="Pro Membership Feature" style="display:none;">

</div>
<div id="topbar" class="cf">
  <div class="container">
    <div class="social-icons cf">
      <ul>
        <li class="social-twitter"><a href="http://www.twitter.com/hoopcoach" target="_blank" data-original-title="Twitter">Twitter</a></li>
        <li class="social-facebook"><a href="https://www.facebook.com/hoopcoach" target="_blank" data-original-title="Facebook">Facebook</a></li>
        <li class="social-googleplus"><a href="https://plus.google.com/u/0/108448710531579117272" target="_blank" data-original-title="Google">Google+</a></li>
      </ul>
    </div>
  </div>
</div>
<header class="container cf">
  <div class="logo"><a href="http://www.hoopcoach.org/"><img style="border: 0px;" src="Model/hoopcoach120.png" alt="Hoopcoach"></a></div>
  <nav id="main" role="navigation">
    <ul id="nav">
      <li><a href="basketball-plays.php">Find Basketball Plays</a> </li>
      <li><a href="play.php">Draw Basketball Plays</a></li>
      <li><a href="http://www.hoopcoach.org/video/basketball-playbook-tutorial" rel="nofollow">Tutorial</a> </li>
      <li><a href="register.php" rel="nofollow"><font color="blue"><b>SIGN UP!</b></font></a> </li>
    </ul>
    <span style="float:right;padding-right:15px"> </span> </nav>
  <div style="clear:both;"><center><br><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- HC Plays Library Header -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-2281684961690945"
     data-ad-slot="8820728158"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></center></div>
</header>
<?php	$getAll = "SELECT playdata.id,playdata.name,playdata.file,category.name AS cat, category.id as catid,
	playdata.thumbsup, playdata.thumbsdown,playdata.rated,playdata.rate,playdata.ratecount, playdata.comments, playdata.movements,playdata.created_on, users.id as userid, playdata.id, users.name as user, playdata.tags from playdata JOIN users ON (users.id=playdata.userid) JOIN category ON (category.id= playdata.catid)  WHERE playdata.`private`='0' and playdata.copied=0";
	$app='?';$get='';
	if(isset($_REQUEST['search']) && trim($_REQUEST['search'])!=''){
		$get=" AND playdata.name LIKE '%".trim($_REQUEST['search'])."%'"." OR category.name LIKE '%".trim($_REQUEST['search'])."%'"." OR playdata.tags LIKE '%".trim($_REQUEST['search'])."%'";
		$app='?search='.trim($_REQUEST['search']).'&';
	}
	elseif(isset($_REQUEST['cat']) && trim($_REQUEST['cat'])!=''){
		$get=" AND catid= '".trim($_REQUEST['cat'])."'";
		$app='?cat='.trim($_REQUEST['cat']).'&';
	}
	elseif(isset($_REQUEST['tags']) && trim($_REQUEST['tags'])!=''){
		$get=" AND playdata.tags LIKE '%".trim($_REQUEST['tags'])."%'";
		$app='?tags='.trim($_REQUEST['tags']).'&';
	}
	elseif(isset($_REQUEST['user']) && trim($_REQUEST['user'])!=''){
		$get=" AND playdata.userid = '".$_REQUEST['user']."'";
		$app='?user='.trim($_REQUEST['user']).'&';
	}
	if(isset($_REQUEST['page']))
		$page=$_REQUEST['page'];
	else
		$page=1;
	$getAll.= $get." ORDER BY ";
	$sort=2;
	if(isset($_GET['sort'])){
		$sort=$_GET['sort'];
		$app.='sort='.$sort.'&';
	}
	switch($sort){
		case 1:
			$getAll.="playdata.rate ASC";break;
		case 2:
			$getAll.="playdata.rate DESC";break;
		case 3:
			$getAll.="playdata.name ASC";break;
		case 4:
			$getAll.="playdata.name DESC";break;
		case 5:
			$getAll.="category.name ASC";break;
		case 6:
			$getAll.="category.name DESC";break;
		case 7:
			$getAll.="playdata.created_on ASC";break;
		case 8:
			$getAll.="playdata.created_on DESC";break;
		case 9:
			$getAll.="users.name ASC";break;
		case 10:
			$getAll.="users.name DESC";break;

	}
	$getAll.=" LIMIT ".(($page-1)*10).",10";/*
		echo $getAll;
	else
		$getAll.= " ORDER BY playdata.created_on DESC LIMIT 0,10";*/
	$noDataq=mysql_query("SELECT COUNT(*) FROM playdata JOIN category ON (category.id= playdata.catid) ".$get);
	$noData=mysql_fetch_array($noDataq);
	//echo $getAll;
    $getAllResult = mysql_query($getAll);?>
<div class="content">
<div class="caption">
  <div style="float:left; margin-left:60px">Basketball Plays - <span style="font-size:11px">Total <?php echo $noData[0]; ?> Plays Found, PAGE <?php echo $page; ?> OF <?php echo ceil($noData[0]/10); ?> sorted by</span> </div>
  <div class="search">
    <form name="searchForm" id="search_form" method="get" action="basketball-plays.php">
      <select name="sort" onChange="$('#search_form').submit();">
        <option value="1" <?php if($sort==1) echo 'selected="selected"'; ?>>Not Rated</option>
        <option value="2" <?php if($sort==2) echo 'selected="selected"'; ?>>Top Rated</option>
        <option value="3" <?php if($sort==3) echo 'selected="selected"';?>>Name A-Z</option>
        <option value="4" <?php if($sort==4) echo 'selected="selected"';?>>Name Z-A</option>
        <option value="5" <?php if($sort==5) echo 'selected="selected"';?>>Categories A-Z</option>
        <option value="6" <?php if($sort==6) echo 'selected="selected"';?>>Categories Z-A</option>
        <option value="7" <?php if($sort==7) echo 'selected="selected"';?>>Date Added - Oldest</option>
        <option value="8" <?php if($sort==8) echo 'selected="selected"';?>>Date Added - Newest</option>
        <option value="9" <?php if($sort==9) echo 'selected="selected"';?>>Coach Name A-Z</option>
        <option value="10" <?php if($sort==10) echo 'selected="selected"';?>>Coach Name Z-A</option>
      </select>
      <input type="text" id="search_terms" class="text" name="search" required placeholder="search" value="<?php if(isset($_REQUEST['search'])) echo $_REQUEST['search']; ?>">
      <button id="search_button"></button>
    </form>
  </div>
</div>
<div id="container">
  <table class="zebra">
    <tbody>
      <?php
		$do=false;

	while ($current = mysql_fetch_assoc($getAllResult)) {
		if($do)
			echo '<tr class="spacer"><td colspan="3">&nbsp;</td></tr>';
		$do=true;
    ?>
      <tr class="category">
		  <!-- // TODO TODO TODO: UNCOMMENT FOLLOWING LINES (baseUrl) BEFORE PUSHING LIVE ******************************************** -->
		  <?php $base_url = 'https://www.hoopcoach.org/playbook/'; ?>
        <td><img src="<?php echo $base_url.'users/'.$current['userid'].'/'.$current['file'].'_1.jpeg'; ?>" height="117" /></td>
        <td width="50%"><div>
            <h2><?php echo $current['name']; ?></h2>
            <h4><a href="basketball-plays.php?user=<?php echo $current['userid']; ?>"><?php echo $current['user']; ?></a> &bull; <?php echo date('jS M \'y',strtotime($current['created_on'])); ?></h4>
            <h4><a href="basketball-plays.php?cat=<?php echo $current['catid']; ?>"><?php echo $current['cat']; ?></a></h4>
            <h5><i>tags: </i>
              <?php
				$tags=explode(',',$current['tags']);
				if($tags){
					for($i=0;$i<count($tags);++$i){
						if(strlen($tags[$i])>1){
							echo '<a href="basketball-plays.php?tags='.$tags[$i].'">'.$tags[$i].'</a>';
							if($i<count($tags)-1)
								echo ',';
						}
					}
				}
				else
					echo '<a href="basketball-plays.php?tags='.$current['tags'].'">'.$current['tags'].'</a>';
			?>
            </h5>
            <p></p>
            <div style="float:left">Rating: </div>
            <div class="star" data-score="<?php if(intval($current['ratecount'])>0) echo $current['rate']; ?>"></div>
            <div style="float:left"><?php if(intval($current['ratecount'])>0) echo $current['rate']; ?></div>
            <?php
			$comm=explode('`',$current['comments']);
if($comm[0])
			echo '<p>'.substr($comm[0],0,20).'...'.'</p>'; ?>
          </div></td>
        <td>
        	<p><a class="download_pdf_btn" href="pdf/reports.php?id=<?php echo $current['id'];?>&user=<?php echo $current['userid']; ?>"><img src="http://cdn.marketplaceimages.windowsphone.com/v8/images/8cb03ba5-7f5c-4a72-a8fc-b497d59f95d7?imageType=ws_icon_small" width="20" style="vertical-align:middle" /> Download</a></p>
          	<p><a href="play.php?id=<?php echo $current['id'];?>&user=<?php echo $current['userid']; ?>&name=<?php echo $current['name']; ?>&category=<?php echo $current['cat']; ?>"><img src="http://shamsbd.com/shamsgroup/wp-content/uploads/2010/08/downloadIconOver.png" style="vertical-align:middle" /> View</a></p>
          	<td>

				<?php if(isset($_SESSION['user_id'])): ?>
	          		<p><a href="#" class="copy_play_btn" from-id="<?=$current['id']?>" to-user="<?=$_SESSION['user_id']?>">Copy</a></p>
	          	<?php endif; ?>

          	</td>
        </td>
      </tr>
      <?php } ?>
      <tr class="spacer">
        <td colspan="3"><div style="width: 492px; height: 38px; margin: auto;padding-top: 8px;background: url(Model/page.png) no-repeat;padding-left: 26px;position: relative;"> <a href="<?php if($page>1) echo $app.'page='.($page-1); else echo '#'; ?>">&lt;&lt;</a>
            <?php
			$p=$page;
			switch($p){
				case 1:
				case 2:
				case 3:
				case 4:
				case 5:
					$pages=array(1,2,3,4,5,6,ceil($noData[0]/10)-2,ceil($noData[0]/10)-1,ceil($noData[0]/10));break;
				case ceil($noData[0]/10):
				case ceil($noData[0]/10)-1:
				case ceil($noData[0]/10)-2:
				case ceil($noData[0]/10)-3:
					$pages=array(1,2,3,ceil($noData[0]/10)-3,ceil($noData[0]/10)-2,ceil($noData[0]/10)-1,ceil($noData[0]/10));break;
				default:
					$pages=array(1,2,3,$p-1,$p,$p+1,ceil($noData[0]/10)-2,ceil($noData[0]/10)-1,ceil($noData[0]/10));
			}
			for($p=0;$p<count($pages);++$p){
				if($pages[$p]==$page) {
				?>
            <a href="#"><img src="Model/Img/balls/ball01.png" style="vertical-align: middle;" /> </a>
            <?php
				}
				elseif($pages[$p]<=ceil($noData[0]/10) && $p<count($pages) && $pages[$p]<$pages[$p+1] ){
				 	if($p>0 && $pages[$p]>$pages[$p-1]+1)
						echo '...';
				?>
            <a href="basketball-plays.php<?php echo $app.'page='.($pages[$p]); ?>"><?php echo ($pages[$p]); ?></a>
            <?php
				}
				else
					break;
			}


			?>
            <a href="<?php if($page<ceil($noData[0]/10)) echo $app.'page='.($page+1); else echo '#'; ?>" style="position: absolute;right: 26px;">&gt;&gt;</a> </div></td>
      </tr>
    </tbody>
  </table>
</div>
<div class="ad">
<div>
<b>Categories:</b>
<ul>
<li><a href="http://www.hoopcoach.org/playbook/basketball-plays.php?cat=19">Basketball Drills</a></li>
<li><a href="http://www.hoopcoach.org/playbook/basketball-plays.php?cat=6">Man to Man Offenses</a></li>
<li><a href="http://www.hoopcoach.org/playbook/basketball-plays.php?cat=5">Man to Man Sets</a></li>
<li><a href="http://www.hoopcoach.org/playbook/basketball-plays.php?cat=14">Zone Plays</a></li>
<li><a href="http://www.hoopcoach.org/playbook/basketball-plays.php?cat=15">Zone Baseline Out of Bounds</a></li>
<li><a href="http://www.hoopcoach.org/playbook/basketball-plays.php?cat=7">Sideline Out of Bounds</a></li>
<li><a href="http://www.hoopcoach.org/playbook/basketball-plays.php?cat=16">Man Baseline Out of Bounds</a></li>
<li><a href="http://www.hoopcoach.org/playbook/basketball-plays.php?cat=24">Defenses and Presses</a></li>
<li><a href="http://www.hoopcoach.org/playbook/basketball-plays.php?cat=22">Last Second Plays</a></li>
</div>

<div>
<a href="http://www.hoopcoach.org/playbook/play.php"><img src="https://www.hoopcoach.org/playbook/draw_plays.png"></a>
</div>
<br>

<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Plays Lib 300by600 -->
<ins class="adsbygoogle"
     style="display:inline-block;width:300px;height:600px"
     data-ad-client="ca-pub-2281684961690945"
     data-ad-slot="6476114157"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>

</div>
</div>
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
<!-- Start of StatCounter Code for Default Guide -->
<script type="text/javascript">
var sc_project=9286366;
var sc_invisible=1;
var sc_security="8033a67e";
var scJsHost = (("https:" == document.location.protocol) ?
"https://secure." : "http://www.");
document.write("<sc"+"ript type='text/javascript' src='" +
scJsHost+
"statcounter.com/counter/counter.js'></"+"script>");
</script>
<noscript>
<div class="statcounter"><a title="web stats"
href="http://statcounter.com/" target="_blank"><img
class="statcounter"
src="http://c.statcounter.com/9286366/0/8033a67e/0/"
alt="web stats"></a></div>
</noscript>
<!-- End of StatCounter Code for Default Guide -->
</body>

</html>
