<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <?php include("head.php"); ?>
        <meta name="robots" content="noindex">

    </head>

	<body>
        <?php //include("navbar.php") ?>

        <div class="container full-height d-flex align-items-center">

			<!-- <div class="row"> -->

                <div class="col-md-6 offset-md-3 text-center">
					<h2 class="text-primary">Password Reset</h2>
					<p class="text-secondary">Please, enter the email address associated with your account.</p>

					<?php

						//store returned data and hold variable to flag that the data is for the register form
						$resLog = array();
						$lr = false;
						if(isset($_SESSION['passVerify_results']))
						{
						    $resLog = $_SESSION['passVerify_results'];
						    $lr = true;
						}
					?>

					<ul class="list-unstyled <?php echo ($lr && !$resLog['form_ok']) ? '' : 'hidden'; ?>">

					    <li class="text-danger">Reset error:</li>

					    <?php if(isset($resLog['errors']) && count($resLog['errors']) > 0) :
					        foreach($resLog['errors'] as $error) :?>

					    <li class="text-danger">- <?php echo $error ?></li>

					    <?php
					        endforeach;
					    endif;
					    ?>
					</ul>

					<form class="form" role="form" action="pass.php?action=pass-reset" METHOD='POST'>
					  	<div class="form-group">
					    	<input type="text" name="email" placeholder="email" class="form-control" value="<?= isset($resLog['form_data']) ? $resLog['form_data']['email'] : '' ?>" required>
					  	</div>
					  	<div class="form-group">
					    	<input type="text" name="passKey" placeholder="password reset key" class="form-control" value="<?= isset($resLog['form_data']) ? $resLog['form_data']['pass_key'] : '' ?>" required size="40">
					  	</div>
					  	<button type="submit" class="btn btn-success">submit</button>
					</form>

				</div>

			<!-- </div> -->
	 	</div>

		<?php include("footer.php") ?>

		<!--SCRIPTS-->
		<?php include("scripts.php"); ?>

	</body>
