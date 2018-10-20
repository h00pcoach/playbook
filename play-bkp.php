<?php 
	session_start();
	require_once('mydb.php');
	
	$premium_user=0;
    $is_student = false;

	// Free user or paid user?
	
	unset($_SESSION['student_id']);
	
	if(isset($_SESSION['user_id']) && !isset($_SESSION['student_id'])) {		
		$query = mysql_query("SELECT * from users WHERE id='" . $_SESSION['user_id'] . "'");
		$result = mysql_fetch_assoc($query);
		if ($result['paid'] == 1)
			$premium_user=1;
	}else{
?>
   <style>
        #actions{display:none};
   </style>
<?php
    }

	/*
	if($premium_user ==0) { ?>
	<style>
	#leftbox-panel { top: 805px; }
	#more78 { display:none; }
	#sign78 { display:none; }
	#pass78 { display:none; }
	#playerList { display:none; }
	#loginLog { display:none; }
	</style>
	
	<?php }
	*/
	
	if(isset($_GET['id'])){

		// Admin users
		if(isset($_SESSION['admin'])){
			$sql = "select * from playdata where id={$_GET['id']}";
			$r = mysql_query($sql);
			$item = mysql_fetch_array($r);
			$_SESSION['user_id'] = $item['userid'];
		}
		
		// Free users or paid users?
		if(isset($_SESSION['user_id'])) {
			$query = mysql_query("SELECT * from users WHERE id='" . $_SESSION['user_id'] . "'");
			$result = mysql_fetch_assoc($query);
			if ($result['paid'] == 1)
				$premium_user=1;
		}

		// Student user
		if(isset($_SESSION['student_id'])){
			$sql = "select * from student where id={$_SESSION['student_id']}";
			$r = mysql_query($sql);
			$item = mysql_fetch_array($r);
			$_SESSION['user_id'] = $item['coach_id'];
            
            $is_student = true;
		}

	}


	$result='';

	if(isset($_SESSION['user_id'])){

		$query=mysql_query("SELECT * from users WHERE id='".$_SESSION['user_id']."'");

		$result = mysql_fetch_assoc($query);

	}

	$plays_count = 0;
   if(isset($_SESSION['user_id'])){ 
		$getAllPlaysQuery = "SELECT * FROM playdata WHERE userid='".$_SESSION['user_id']."';";

		$getAllPlaysResult = mysql_query($getAllPlaysQuery);
		$plays_count = mysql_num_rows($getAllPlaysResult);
	}

	

?>

<!doctype html>


<html>

    <head>

    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Ubuntu:light&subset=Latin">
    <link rel="stylesheet" href="css/main.css">

    <script type="text/javascript">

window.google_analytics_uacct = "UA-1535786-5";

</script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

     <meta property="og:image" content="http://basketballplaybook.org/Model/hoopcoach120.png" />

    <meta property="twitter:image0:src" content="http://basketballplaybook.org/Model/hoopcoach120.png" />

    <meta name="twitter:card" content="gallery">

    <meta name="twitter:description" content="Up than 200 characters.">

    <meta name="twitter:domain" content="BasketballPlaybook.org">

		

    <?php

		$name='';$rate='""';$rated=0;$priv=0;

		if(isset($_REQUEST['id'])){

			if(isset($_SESSION['user_id'])){

				$query=mysql_query("SELECT  id,name,file, rate, (CASE WHEN playdata.rated LIKE '%;".$_SESSION['user_id'].";%' THEN 1 else 0 end) AS `raed`, tags, scout, `private`  from playdata WHERE id='".$_REQUEST['id']."'");

				

			}

			else

				$query=mysql_query("SELECT  *  from playdata WHERE id='".$_REQUEST['id']."'");

			$res = mysql_fetch_assoc($query);

			$i=1;

			$nm=explode('`',$res['movements']);

			$file="users/".$res['userid']."/".$res['file'];

			while(file_exists($file.'_'.$i.'.jpeg') && $i<10){

				echo '<meta property="og:image" content="http://basketballplaybook.org/'.$file.'_'.($i).'.jpeg'.'" />';

				if($i<4)

					echo '<meta property="twitter:image'.$i.':src" content="http://basketballplaybook.org/'.$file.'_'.($i).'.jpeg'.'" />';

				++$i;

			}

			$name=$res['name'];$rate=$res['rate'];

			$priv=$res['private'];

			if(isset($_SESSION['user_id']))

				$rated=$res['raed'];

		}

	?>

    <meta name="keywords" content="basketball,playbook, basketball coach, basketball planner, coaching apps, basketball plays and drills, basketball plays, basketball drills."/>
    <meta name="description" content="Basketball Playbook is a web based tool for basketball coaches that saves time so you can focus on developing your players. Coaching apps and basketball plays and drills."/>
    <title><?php if($name!='') echo $name; ?> - <?php if(isset($_GET['category'])) echo ' a '.$_GET['category'].' created on '; ?>BasketballPlaybook.org</title>
    <meta property="og:title" content="<?php if($name!='') echo $name; ?><?php if(isset($_GET['category'])) echo ' a '.$_GET['category'].' created on '; ?>BasketballPlaybook.org"/>
    <meta property="og:site_name" content="BasketballPlaybook.org"/>
    <meta name="twitter:description" content="Basketball Playbook is a web based tool for basketball coaches that saves time so you can focus on developing your players. Coaching apps and basketball plays and drills." />
    <meta name="twitter:title" content="<?php if($name!='') echo $name; ?><?php if(isset($_GET['category'])) echo ' a '.$_GET['category'].' created on '; ?>BasketballPlaybook.org" />

    <script type="text/javascript">
      var switchTo5x=false;
    </script>
    <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.11.1/themes/ui-lightness/jquery-ui.css">
    <script type="text/javascript" src="js/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript" src="js/kinetic-v4.5.5.min.js"></script>
	<script type="text/javascript" src="js/jquery.raty.min.js"></script>
    <script src="js/acetrik11.js"></script>
    
    <script src="js/superfish.js"></script>
    <link rel="stylesheet" href="css/superfish.css">

    <script type="text/javascript">
	var user={id:'',lastname:'',email:'',ismale:'',role:'',profileurl:'',avatarurl:'',hoopid:'',uped:-1,downed:-1};
	$(document).ready(function(e) {
        
	<?php if(isset($_SESSION['user_id'])){ ?>
		user={id:'<?php echo $result['id']; ?>',lastname:'<?php echo $result['name']; ?>',email:'<?php echo $result['email']; ?>',ismale:'<?php echo $result['ismale']; ?>',role:'<?php echo $result['role']; ?>',profileurl:'<?php echo $result['hoopcoachpage']; ?>',avatarurl:'<?php echo $result['avatar']; ?>',hoopid:'<?php echo $result['hoopcoachid']; ?>'};

		if(user.avatarurl.length<5)
			if(user.ismale=='1')
				user.avatarurl='Model/Img/male_avatar_icon.png';
			else
				user.avatarurl='Model/Img/female_avatar_icon.png';

        setUser(true);
	<?php } ?>

	$('#star').raty({

		size:12,

		width:180,

		space:false,

		hints: ['low success', 'below avg', 'average', 'above avg', 'hi success'],

		readOnly:<?php if(intval($rated)==0) echo 'false'; else echo 'true'; ?>,

		starOff: 'Model/Img/balls/ball04.png',

		  starOn : 'Model/Img/balls/ball03.png',

		 score: <?php if($rate=='') echo '""'; else echo $rate; ?>,

		 click: function(score, evt){

		  <?php if(isset($_GET['id'])){ ?>

	  	$.post('save.php',{id:<?php echo $_GET['id']; ?>,rate:score},function(d){ alert(d);});

		<?php } ?>

	  }

	});
        
        <?php if($result['paid']==0):?>
		$('#pdf').click(function(){
			$('#pdf_warning_dialog').dialog('open');
			return false;
		})
		<?php endif; ?>

		<?php if(isset($_SESSION['user_id']) && $plays_count>=5 && $result['paid']==0): ?>
		$('#new_play_btn').click(function(){
			$('#new_play_warning_dialog').dialog('open');
			return false;
		 })
		<?php endif; ?>
        
        
    });

      stLight.options({
        publisher:'5a1ef2ea-5e37-4530-bada-46b16219791a'
      });

    </script>
    <script src="js/main.js"></script>

    </head>

    <body style="margin: 0 0 0 0;">

    <div id="new_movement_dialog" title="New Movement" style="display:none;">

    			<div>Movement Name<input id="new_movement_name"></div>

    			<div style="margin-top:15px;">
                    <b style="color:#a30;">Comments</b>
                    <input id="new_movement_comments">
    			</div>
 
    </div>

    <div id="pdf_warning_dialog" title="Premium Membership Feature" style="display:none;">
    	PDF Printouts are a Premium Feature. <br><a href="/premium/index.php?uid=<?=$_SESSION['user_id']?>">Click here</a> to upgrade
    </div>

    <div id="new_play_warning_dialog" title="Premium Membership Feature" style="display:none;">
    	Premium membership needed for creating more than 5 plays. <br><a href="/premium/index.php?uid=<?=$_SESSION['user_id']?>">Click here</a> to upgrade
    </div>

   

    <div id="fb-root"></div>

<script>(function(d, s, id) {

  var js, fjs = d.getElementsByTagName(s)[0];

  if (d.getElementById(id)) return;

  js = d.createElement(s); js.id = id;

  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=185990164786767";

  fjs.parentNode.insertBefore(js, fjs);

}(document, 'script', 'facebook-jssdk'));</script>


<script type="text/javascript">

	

</script>



<div style="margin:auto; width:1020px;">



<div id="header"> 



<div style="width:950px; height:125px; margin-left:8%; margin-top:10px; color:#ffffff; font-size: 28px; font-family: verdana; vertical-align:baseline; " >

<div style="padding:7px;"><!--<a href="http://www.sidelineinteractive.com/products/interactive-scoring-tables/?utm_source=playbook&utm_medium=banner&utm_campaign=playbook" rel="nofollow">

      <img src="http://api.ning.com:80/files/q34XLvoT5YiU*On1W4*X9*4EHlzr359izr15BluP8whOz10z*fLxhMMZxi7XJfVEcKP59h8pI8xqLUT1YWqhwzkB7yUQj0Tt/bottomleaderboard.gif">

      </a>--><img src="http://www.basketballplaybook.org/blog/wp-content/uploads/2014/08/basketball-plays-logo5.png" style="margin-left:-33px;" alt="Logo">
<?php
if($premium_user==0)
{
?>
<img style="width: 616px;margin-left: 41px;" src="http://api.ning.com:80/files/q34XLvoT5YiU*On1W4*X9*4EHlzr359izr15BluP8whOz10z*fLxhMMZxi7XJfVEcKP59h8pI8xqLUT1YWqhwzkB7yUQj0Tt/bottomleaderboard.gif"> 
<?php } ?>

</div>

	 <div style="width:868px;border:1px black solid; height:42px; margin-top:10px; color:#ffffff; font-size: 28px; font-family: verdana; vertical-align:baseline;background:#9D9179;text-align:center; " >
<ul id="menu">
    
    <li>
        <a href="http://www.basketballplaybook.org/basketball-plays.php" target= "_blank" style="color:black;">Plays Library</a> |
    </li>
    <li><a href="http://www.basketballplaybook.org/blog/sample-page/" target= "_blank" style="color:black;">Tips</a> | </li>
    <li><a href="http://www.basketballplaybook.org/blog/faq/" rel="nofollow" style="color:black;">Help</a> | </li>
	 <li id="actions">
        <a href="#">Menu</a> |
        <ul>
            <li>
                <a href="#" rel="nofollow" id="myplay">My Plays</a>
            </li>
             <li>
                 <a id="more78" href="http://www.basketballplaybook.org/add_student.php">Add Player</a>
             </li>

            <li>
                <a id="sign78" href="http://www.basketballplaybook.org/player/playbook.php" rel="nofollow">Playbook</a>
            </li>

            <li>
                <a id="pass78" href="http://www.basketballplaybook.org/coach_set_password.php" rel="nofollow">Playbook Password</a>
            </li>

            <li>
                <a id="playerList" href="http://www.basketballplaybook.org/coach_student_list.php" rel="nofollow">Roster</a>
            </li>

            <li>
                <a id="loginLog" href="http://www.basketballplaybook.org/coach_student_tracking.php" rel="nofollow">Login Log</a>
            </li>
            <li>
                <a href="reset_password.php">Change Password</a>
            </li>
            <li>
                <a href="#" rel="nofollow" style="color:black;" id="logout_btn">Logout</a>
            </li>
        </ul>
    </li>
  
    <li>
        <div id="logreglinks" style="display:inline"> 
            <a href="#" rel="nofollow" style="color:black;" id="log">Login</a> |  
            <a href="http://www.basketballplaybook.org/register.php?type=new" rel="nofollow" style="color:black;" id="log">Register</a> 
        </div> 
    </li>
    
</ul>
<!--<a href="http://www.hoopcoach.org/video/basketball-playbook-tutorial" target= "_blank" style="color:black;">How to Use</a> | -->
	  
</div>
 
	  
	  
</div>

    </div>









<div id="left"> 

<div style="margin:auto; background-color: #ffffff; width: 160px; float:left">

      <div id="" style="position:absolute;margin-left:55px; background-color:#ffffff; width:165px; height: 100px; padding: 5px; font-size: 17px; color: black; font-family:verdana;margin-top:-40px">

    	 <div style="width:170px;">

		 <center>

		 <br>

    <span  class='st_linkedin_large' > </span> <span  class='st_twitter_large' > </span> <span  class='st_facebook_large' > </span> <span  class='st_email_large' > </span></center>


 </div> 

 <!--<a href="#" onClick="MyWindow=window.open('http://www.basketballplaybook.org/tips.html','MyWindow','toolbar=no,location=no,directories=no,status=no, menubar=no,scrollbars=no,resizable=no,width=600,height=375'); return false;"><img src="img/tips.png" style="width:167px;" border="0"></a>-->
<!--
 <a id="more78" href="http://www.basketballplaybook.org/add_student.php"><img src="img/AddPlayer.png" border="0" style="width:167px;padding-top: 7px;"></a>

<a id="sign78" href="http://www.basketballplaybook.org/player/playbook.php" rel="nofollow"><img src="img/Playbook.png" border="0" alt="draw basketball plays" style="width:167px;padding-top: 7px;"></a>

<a id="pass78" href="http://www.basketballplaybook.org/coach_set_password.php" rel="nofollow"><img src="img/Password.png" border="0" style="width:167px;padding-top: 7px;"></a>

<a href="#" rel="nofollow" id="myplay"><img src="img/myplays.png" border="0" style="width: 172px;padding-top: 7px;margin-left: -3px;"></a>

<a id="playerList" href="http://www.basketballplaybook.org/coach_student_list.php" rel="nofollow"><img src="img/playerlist.png" border="0" style="width: 172px;padding-top: 7px;margin-left: -3px;"></a>

<a id="loginLog" href="http://www.basketballplaybook.org/coach_student_tracking.php" rel="nofollow"><img src="img/loginlog.png" border="0" style="width: 172px;padding-top: 7px;margin-left: -3px;"></a>
-->


    <div class="pay_box" style="margin-top:10px;">
        
        	
			
         	<?php
	
	//var_dump($_SESSION);
	
	//var_dump($result['paid']);
	
	
			if(isset($_SESSION['user_id']) && $result['paid']==0): ?>
                <div style="font-weight:bold; padding-left:5px;">Upgrade</div>
	         	<div>

	         		<a href="/premium/index.php?uid=<?=$_SESSION['user_id']?>"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;"></a>

	         	</div>

	         <?php endif; ?>
			 
			 <?php if(!isset($_SESSION['user_id'])): ?>
                <div style="font-weight:bold; padding-left:5px;">Upgrade</div>
	         	<div>

	         		<a href="http://www.basketballplaybook.org/register.php?type=new"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;margin-top:-"></a>

	         	</div>

	         <?php endif; ?>
    </div>
			 
			 
			
        </div>

      </div>
	  	  
	  
	  <?php 
	  if($premium_user == 0) { ?>
<div style="margin-left:64px;margin-top:100px">
	
    	<script type="text/javascript">

        <!--

          google_ad_client = "ca-pub-2281684961690945";

        /* BPB Plays Sky */

        google_ad_slot = "9653494386";

        google_ad_width = 160;

        google_ad_height = 600;

        //-->

      </script> 

                <script type="text/javascript"src="http://pagead2.googlesyndication.com/pagead/show_ads.js">

                </script>
</div>

<?php } ?>



    </div>

<div style="float:left;width:650px;margin-left:124px;"> 

      <!----------- ---------->

      <div id="contents" style="margin-top:0px">

    <div id="logged">

        <div> 
	        <img src="Model/Img/balls/circle.png" height="35">
	        <div>Welcome,</div>
	        <div id="name" style="overflow: hidden;height: 20px;">Debdeep SIkdar</div>
        </div>

<!--          <img src="Model/Img/MyPlaysButton.png" id="myplay" style="display:inline">-->
		  <img src="Model/Img/button/logout_active.jpg" id="logout_btn" /> 
    </div>

    <div id="lrg"></div>

    <div id="large"></div>

    <div id="loading"> <span id="loadtxt">Processing...</span>

          <div id="load"> <img src="Model/Img/balls/ball01.png" style="left: 50%;margin-left: 0;"> <img src="Model/Img/balls/ball01.png" style="top: 50%;margin-top: -15px;left: 50%;margin-left: 25px;"> <img src="Model/Img/balls/ball01.png" style="bottom: 0px;left: 50%;margin-left: 0;"> <img src="Model/Img/balls/ball01.png" style="bottom: 0px;left: 50%;margin-left: -35px;"> <img src="Model/Img/balls/ball01.png" style="left: 50%;margin-left: -60px;top: 50%;margin-top: -15px;"> <img src="Model/Img/balls/ball01.png" style="left: 50%;margin-left: -35px;"> </div>

        </div>

    <div id="login">

          <table width="100%">

        <tbody>

              <tr>

            <td><table width="100%" align="right">

                <tbody>

                  <tr>

                    <td style="text-align: right; color:white" width="30%">E-mail:</td>

                    <td><input type="text" id="email" style="height: 16px;" name="email" /></td>

                  </tr>

                  <tr>

                    <td style="text-align: right;color:white">Password:</td>

                    <td><input type="password" id="pass" name="password" style="height: 16px;" /></td>

                  </tr>

                </tbody>

              </table></td>

          </tr>

              <tr>

            <td>
                <a href="forgot_password.php" style="color:white; font-weight:bold;">Forgot Password?</a>
            </td>

          </tr>

              <tr>

            <td style="

    padding: 0;

"><table width="100%" align="right">

                <tbody>

                  <tr>

                    <td width="50%"><img src="Model/Img/login.jpg" id="login_btn" onclick="return false;" /></td>

                    <td><img src="Model/Img/cancel.jpg" id="canc" /></td>

                  </tr>

                </tbody>

              </table></td>

          </tr>

            </tbody>

      </table>

        </div>

    <div id="plays"> <img src="Model/Img/regBox/close_active.png" id="playClose" />

          <table width="100%">

        <tbody>

              <tr>

            <th width="50%">Name</th>

            <th>Play/Delete play </th>

          </tr>

              <tr>

            <td colspan="2"><div id="lists">

                <table width="100%">

                  <?php
                   $plays_count = 0;
                   if(isset($_SESSION['user_id'])){ 

					$getAllPlaysQuery = "SELECT * FROM playdata WHERE userid='".$_SESSION['user_id']."';";

					$getAllPlaysResult = mysql_query($getAllPlaysQuery);
					$plays_count = mysql_num_rows($getAllPlaysResult);

					while ($currentPlay = mysql_fetch_assoc($getAllPlaysResult)) {

					?>

                  <tr>

                    <td width="50%"><?php echo $currentPlay['name']; ?></td>

                    <td data-play="<?php echo $currentPlay['id']; ?>" data-path="<?php echo $currentPlay['file']; ?>"><a href="play.php?id=<?php echo $currentPlay['id']; ?>"><img src="Model/Img/play.jpg" height="19" class="loadPlay" /></a><img src="Model/Img/del.jpg" class="remPlay" onClick="remPlay(this)" /></td>

                  </tr>

                  <?php } } ?>

                </table>

              </div></td>

          </tr>

            </tbody>

      </table>

        </div>

    <div id="saveGame">

          <div>Save</div>

          <div>

            <select id="cat" size="9">

            <?php

                $getAllCategoriesInfoQuery = "SELECT * FROM category WHERE ispublic=1;";

                $getAllCategoriesInfoQueryResult = mysql_query($getAllCategoriesInfoQuery);

                while ($currentCategoryInfo = mysql_fetch_assoc($getAllCategoriesInfoQueryResult))

                    echo "<option value='".$currentCategoryInfo['id']."'>" . $currentCategoryInfo['name'] . "</option>";

				?>

            </select>

      </div>

          <div style="margin: 25px 10px;">
          	<div>
          		Name :
	        	<input type="text" id="savename" style="width: 60%;">
          	</div> 
          	<div>
          		Tags: 
          		<input type="text" id="save_tag" style="width: 60%;" placeholder="Offense, Defense, Motion etc">
          	</div>
	      </div>

          <div style="text-align: right;"><img src="Model/Img/button/ok_active.jpg" id="saveg"><img src="Model/Img/button/cancel_active.jpg" id="cang"></div>

        </div>

    <div style="float:left; width:328px">

    <div style="text-align:center"><?php echo $name; ?>&nbsp;</div>

          <div id="container"></div>

          <table style="padding:14px" cellpadding="0" cellspacing="0">

              <tbody>

            <tr>

                  <td style=" padding-top:9px">Rated</td>

                  <td><div id="star"></div></td>

                </tr>

          </tbody>

            </table>

          <div class="bot">

          <div style="width: 300px;height: 36px;margin-top: 6px;font-size: 10px;">Comment

        <textarea id="comment" style="height: 67px; width: 300px;" placeholder="Comments appear on PDF print outs"></textarea>

      </div>

        

      </div>

        </div>

    <div style="float:left; width:290px"> <img src="Model/Img/hoopcoach_logo.png" />

          <table width="100%" id="options">

        <tbody><tr>

              <td rowspan="4" width="40%" style="vertical-align:middle" id="autoadd"><span>Auto add players</span> <img src="Model/Img/five_offense.jpg"></td>

              <td width="20%" class="button prev"><div></div><!--<img src="Model/Img/navigation/arrow_left_passive.jpg" />--></td>

              <td width="20%"><div class="cont" id="Player">

                  <ul>

                  <li> <img src="Model/Img/players/01.png" id="p1" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/02.png" id="p2" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/03.png" id="p3" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/04.png" id="p4" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/05.png" id="p5" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/06.png" id="p6" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/07.png" id="p7" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/08.png" id="p8" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/09.png" id="p9" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/10.png" id="p10" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/11.png" id="p11" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/12.png" id="p12" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/13.png" id="p13" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/14.png" id="p14" class="ui-draggable"> </li>

                  <li> <img src="Model/Img/players/15.png" id="p15" class="ui-draggable"> </li>

                </ul>

                </div>

            Offense </td>

              <td class="button next"><div></div></td>

              </tr>

        <tr>

              <td width="20%" class="button prev"><div></div><!--<img src="Model/Img/navigation/arrow_left_passive.jpg" />--></td>

              <td><div class="cont" id="Forward">

                  <ul>

                  <li> <img src="Model/Img/forwards/x1.png" id="x1" /> </li>

                  <li> <img src="Model/Img/forwards/x2.png" id="x2" /> </li>

                  <li> <img src="Model/Img/forwards/x3.png" id="x3" /> </li>

                  <li> <img src="Model/Img/forwards/x4.png" id="x4" /> </li>

                  <li> <img src="Model/Img/forwards/x5.png" id="x5" /> </li>

                  <li> <img src="Model/Img/forwards/x6.png" id="x6" /> </li>

                  <li> <img src="Model/Img/forwards/x7.png" id="x7" /> </li>

                  <li> <img src="Model/Img/forwards/x8.png" id="x8" /> </li>

                  <li> <img src="Model/Img/forwards/x9.png" id="x9" /> </li>

                  <li> <img src="Model/Img/forwards/x10.png" id="x10" /> </li>

                  <li> <img src="Model/Img/forwards/x11.png" id="x11" /> </li>

                  <li> <img src="Model/Img/forwards/x12.png" id="x12" /> </li>

                  <li> <img src="Model/Img/forwards/x13.png" id="x13" /> </li>

                  <li> <img src="Model/Img/forwards/x14.png" id="x14" /> </li>

                  <li> <img src="Model/Img/forwards/x15.png" id="x15" /> </li>

                </ul>

                </div>

            Defense </td>

               <td class="button next"><div></div></td>

            </tr>

        <tr>

              <td width="20%" class="button prev"><div></div><!--<img src="Model/Img/navigation/arrow_left_passive.jpg" />--></td>

              <td><div class="cont" id="Ball">

                  <ul>

                  <li> <img src="Model/Img/balls/ball01.png" /> </li>

                  <li> <img src="Model/Img/balls/ball02.png" /> </li>

                  <li> <img src="Model/Img/balls/ball03.png" /> </li>

                  <li> <img src="Model/Img/balls/ball04.png" /> </li>

                </ul>

                </div>

            Ball </td>

               <td class="button next"><div></div></td>

            </tr>

        <tr>

              <td width="20%" class="button prev"><div></div><!--<img src="Model/Img/navigation/arrow_left_passive.jpg" />--></td>

              <td><div class="cont" id="Cone">

                  <ul>

                  <li> <img src="Model/Img/forwards/cone01.png" id="c1" /> </li>

                  <li> <img src="Model/Img/forwards/cone02.png" id="c2" /> </li>

                  <li> <img src="Model/Img/forwards/cone03.png" id="c3" /> </li>

                  <li> <img src="Model/Img/forwards/cone04.png" id="c4" /> </li>

                  <li> <img src="Model/Img/forwards/cone05.png" id="c5" /> </li>

                  <li> <img src="Model/Img/forwards/cone06.png" id="c6" /> </li>

                  <li> <img src="Model/Img/forwards/cone07.png" id="c7" /> </li>

                  <li> <img src="Model/Img/forwards/cone08.png" id="c8" /> </li>

                  <li> <img src="Model/Img/forwards/cone09.png" id="c9" /> </li>

                  <li> <img src="Model/Img/forwards/cone10.png" id="c10" /> </li>

                  <li> <img src="Model/Img/forwards/cone11.png" id="c11" /> </li>

                  <li> <img src="Model/Img/forwards/cone12.png" id="c12" /> </li>

                  <li> <img src="Model/Img/forwards/cone13.png" id="c13" /> </li>

                  <li> <img src="Model/Img/forwards/cone14.png" id="c14" /> </li>

                  <li> <img src="Model/Img/forwards/cone15.png" id="c15" /> </li>

                </ul>

                </div>

            Cone </td>

               <td class="button next"><div></div></td>

          

            </tr>

      </table>

          <table width="100%" id="Arrow">

        <tbody><tr>

              <td colspan="6" style="">Drawing Tools: </td>

            </tr>

        <tr id="draw">

              <td width="20%"><div id="wcut" onclick="return sline();"></div><!--<img src="Model/Img/arrows/1.jpg" />-->

            cut</td>

              <td width="20%"><div id="wpass" onclick="return sline(true);"></div><!--<img src="Model/Img/arrows/2.jpg" onClick="return sline(true);" />-->pass</td>

              <td width="20%"><div id="wdribble" onclick="return sline(false,false,true);"></div><!--<img src="Model/Img/arrows/3.jpg"/>-->dribble</td>

              <td width="20%"><div id="wscreen" onclick="return sline(false,true);"></div><!--<img src="Model/Img/arrows/4.jpg" onClick="return sline(false,true);"  />-->screen</td>

              <td><div id="wcurved" onclick="return s_pline();"></div><!--<img src="Model/Img/arrows/6.jpg"  />-->curved</td>

            </tr>

      </tbody></table>

          <table width="100%" id="controls">

        <tbody><tr>

              <td><div id="nxtFrame"></div></td>

              <td><div id="play"></div></td>

              <td width="50%">Play Animation:

            <select id="speed">

                  <option value="500">Very Fast</option>

                  <option value="1000">Fast</option>

                  <option value="1500" selected="">Normal</option>

                  <option value="2000">Slow</option>

                </select></td>

            </tr>

      </tbody></table>

          <table width="100%" id="record">

        <tbody><tr>

              <td rowspan="2" width="50%"><select style="width:130px; margin-top:45px" id="moves"><option value="0" selected="selected">Initial Set</option></select></td>

              <td><img src="img/images/rename_button.png" id="ren_move"></td>

            </tr>
        
        <tr>
           <?php if(!$is_student): ?>
            <td><img src="img/images/delete_button.png" id="del_move"></td>
           <?php endif; ?>
        </tr>
        

        <tr>
            
             <td>
                  <?php if(isset($_SESSION['user_id']) && $result['paid']==1){?>
                  <input name="private" type="checkbox" value="1" id="private" <?php if($priv==1) echo 'checked="checked"'; ?>><label style="font-size: 120%;font-weight: bold;">Set Private</label>
                  <?php } ?>
              </td>
              

              <td><img src="img/images/add_button.png" id="add_movement"></td>

            </tr>

        <tr>

              <td>Select Court:</td>

              <td><select id="court">

                  <option value="0">Full Court</option>

                  <option value="1">Half Court</option>

                </select></td>

            </tr>

      </tbody></table>

          <table width="100%" id="saved">

        <tbody><tr>

              <td width="20%"><img src="img/images/recycle.png" id="del"></td>

              <td width="30%">

              	<form id="pdf_form" target="_blank" action="pdf/reports.php" method="POST">

              		<input type="hidden" id="user_id" name="userid" val="">

              		<input type="hidden" name="id" id="id" val="" value="">

              		<input type="image" src="img/images/pdf_dwnld.png" id="pdf">

              	</form>

              </td>

              <td width="25%">
              	<a href="play.php" id="new_play_btn">
              		<img src="img/images/newplay.png" id="new1">
              	</a>
              </td>

              <td>
              <?php if(!$is_student): ?>
              <img src="img/images/save1.png" id="save_btn">
              <?php endif; ?>
              </td>

            </tr>

      </tbody></table>

      <table width="100%" style="margin-top:-5px">

      	<tr>

                  <td style="font-weight: bold;">Tags:</td>

                <td><input name="tags" type="text" id="tags" style="width:100px;border-color: gray;"></td>

                <td style="font-weight: bold;">Scout</td>
        		<td>
        			<input name="scout" id="scout" style="width:100px;border-color: gray;" value="<?=$res['scout']?>">
        		</td>
        </tr>

      </table>

        </div>

  </div>

      

      <!----------- ----------> 

    </div>



        <div style="width:900px; margin-left:120px;">



 

     </div>

    </div>

  

</div>



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

<noscript><div class="statcounter"><a title="web stats"

href="http://statcounter.com/" target="_blank"><img

class="statcounter"

src="http://c.statcounter.com/9286366/0/8033a67e/0/"

alt="web stats"></a></div></noscript>

<!-- End of StatCounter Code for Default Guide -->

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

</body>

 



</html>



<?php 

	// remove fake user session for admin user at the end of page

	// if(isset($_SESSION['admin']) || isset($_SESSION['student_id'])){

	// 	unset($_SESSION['user_id']);

	// }

 ?>