<?php session_start(); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <?php include("head.php") ?>
        <meta name="robots" content="noindex">
        
    </head>

    <!-- TODO TODO TODO: USING ADMIN EMAIL ADDRESS -->
	<body>

	 	<div class="container full-height d-flex align-items-center">

			<div class="row">
                <?php   $date = new DateTime($_SESSION['reset-exp']);
                        $date = date_format($date, 'Y-m-d g:ia');
                    // $date->format('Y-m-d g:ia');
                 ?>
				<div class="offset-sm-3 col-sm-6 reset-form-container white text-center">
					<h2 class="text-success">Verification Sent</h2>
					<p>Check your inbox at <strong><?= $_SESSION['reset-email'] ?></strong> to continue resetting your password.  Your passkey will expire in <br>3 hours (<em><?= $date ?></em> EST).</p>
					<p class="text-secondary"><em>If you don't see the email, check your spam folder.  Adding admin@hoopcoach.com to your contacts and whitelist will ensure you receive this email.</em></p>
				</div>

			</div>

	 	</div>

		<?php include("footer.php") ?>
		<?php include("scripts.php"); ?>

	</body>
