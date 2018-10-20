<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <?php include('head.php') ?>
        <meta name="robots" content="noindex">

    </head>

	<body class="text-center">
	 	<div class="container full-height d-flex align-items-center">

            <div class="col-sm-4 offset-sm-4">
                <h2 class="white">Password Reset</h2>
                <p class="white">Please enter the email address associated with your account.

                <?php
                    //store returned data and hold variable to flag that the data is for the register form
                    $resLog = array();
                    $lr = false;
                    $selectedType = '';
                    if(isset($_SESSION['passSupport_results']))
                    {
                        $resLog = $_SESSION['passSupport_results'];
                        $lr = true;
                        $selectedType = $resLog['form_data']['type'];
                    }
                ?>

                <ul class="list-unstyled reset_errors <?php echo ($lr && !$resLog['form_ok']) ? '' : 'hidden'; ?>">

                    <li id="subscribe_errors_info">Reset error:</li>

                    <?php if(isset($resLog['errors']) && count($resLog['errors']) > 0) :
                        foreach($resLog['errors'] as $error) :?>

                    <li>- <?php echo $error ?></li>

                    <?php
                        endforeach;
                    endif;
                    ?>
                </ul>

                <form class="form" role="form" action="pass.php?action=mail-pass-support" METHOD='POST'>
                  <div class="form-group">
                    <input type="text" name="email" placeholder="email" class="form-control" value="<?= isset($resLog['email']) ? $resLog['form_data']['email'] : '' ?>" required>
                  </div>
                  <div class="form-group">
                      <!-- <label for="account_type">Account Type:</label> -->

                  </div>
                  <button type="submit" class="btn btn-success">submit</button>
                </form>

            </div>


	 	</div>

        <?php include('footer.php') ?>

		<?php include('scripts.php') ?>

	</body>
