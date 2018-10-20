<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <?php include("head.php") ?>
        <meta name="robots" content="noindex">
    </head>

	<body>
        <?php //include("navbar.php") ?>

	 	<div class="container full-height d-flex align-items-center">

			<!-- <div class="row"> -->

				<div class="col-md-6 offset-md-3 reset-form-container">
					<h2 class="text-primary text-center">Password Reset</h2>

					<?php

						//store returned data and hold variable to flag that the data is for the register form
						$resLog = array();
						$lr = false;
						if(isset($_SESSION['passNew_results']))
						{
						    $resLog = $_SESSION['passNew_results'];
						    $lr = true;
						}
					?>

					<ul class="list-unstyled <?php echo ($lr && !$resLog['form_ok']) ? '' : 'hidden'; ?>">

					    <li id="subscribe_errors_info" class="text-danger">Reset error:</li>

					    <?php if(isset($resLog['errors']) && count($resLog['errors']) > 0) :
					        foreach($resLog['errors'] as $error) :?>

					    <li class="text-danger">- <?php echo $error ?></li>

					    <?php
					        endforeach;
					    endif;
					    ?>
					</ul>


					<form id="passNew-form" role="form" action="pass.php?action=update-pass" METHOD='POST'>

						<input type="hidden" name="user_id" value="<?= $results['user_id'] ?>" />
                        <input type="hidden" name="pass_key" value="<?= $results['pass_key'] ?>" />
                        <input type="hidden" name="email" value="<?= $results['email'] ?>" />

					  	<div id="password-new-fg" class="form-group">
					  		<label for="password">Enter a new password:</label>
					    	<input id="password-new" type="password" name="password" class="form-control" required>
                            <p class="hidden red text-right text-danger">Enter a password longer than 8 characters</p>
					  	</div>
					  	<div id="confirm-new-fg" class="form-group">
					  		<label for="verifypass">Verify your new password:</label>
					    	<input id="confirm-new-password" type="password" name="verifypass" class="form-control" required>
                            <p class="hidden red text-right text-danger">Please confirm your password</p>
					  	</div>
					  	<button id="new-submit" type="submit" class="btn btn-success">submit</button>
					</form>

				</div>

			<!-- </div> -->

	 	</div>

		<?php include("footer.php") ?>
		<?php include("scripts.php"); ?>

        <script>
            var $password = $('#password-new');
            var $passFG = $('#password-new-fg');
            var $confirmPassword = $('#confirm-new-password');
            var $confirmFG = $('#confirm-new-fg');
            var $submit = $('#new-submit');

            // Hide Hints
            $('.text-danger').addClass('hidden');
            $('.text-success').addClass('hidden');

            function isPasswordValid()
            {

                return $password.val().length > 8;
            }

            function arePasswordsMatching()
            {

                return $confirmPassword.val() === $password.val();
            }

            function canSubmit()
            {

                return isPasswordValid() && arePasswordsMatching();
            }

            function passwordEvent()
            {
                // Find out if password is valid (less than 8 characters)

                if (isPasswordValid())
                {
                    // Hide hint if valid
                    $passFG.find('.text-danger').addClass('hidden');
                } else {

                    // Else show hint
                    $passFG.find('.text-danger').removeClass('hidden');
                }
            }

            function confirmPasswordEvent()
            {
                // Find out if password and confirmation match
                if (arePasswordsMatching())
                {
                    // Hide hint if valid
                    $confirmFG.find('.text-danger').addClass('hidden');

                } else {

                    // Else show hint
                    $confirmFG.find('.text-danger').removeClass('hidden');
                }
            }

            function enableSubmitEvent()
            {
                $submit.prop('disabled', !canSubmit());
            }

            // When event happens on password input
            $password.focus(passwordEvent).keyup(passwordEvent).keyup(confirmPasswordEvent).keyup(enableSubmitEvent);

            // When event happens on confirmation input
            $confirmPassword.focus(confirmPasswordEvent).keyup(confirmPasswordEvent).keyup(enableSubmitEvent);

            enableSubmitEvent();
        </script>

	</body>
