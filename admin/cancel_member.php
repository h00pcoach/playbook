<?php include 'header.php'; ?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Cancel Members - Admin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css?family=Bree+Serif|Raleway" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="../css/font-awesome.min.css">
        <link rel="stylesheet" href="../css/bp-main.css?version=1.2">
    </head>

    <body class="pb-4" style="background-color: #f1f1f1">
        <nav class="navbar navbar-expand-xl navbar-light bg-light mb-4">
            <div class="container">
                <a class="navbar-brand text-secondary" href="/playbook/admin/index.php">admin home</a>
            </div>
        </nav>

        <div class="container">
            <h2>Cancel Members</h2>
            <hr>
            <div class="row">

                <div class="offset-md-3 col-md-6">

                    <form id="search-users-form" class="form" action="search_users.php" method="POST">

                        <div class="input-group">
                            <input id="email-input" type="email" class="form-control" name="email"  autocomplete="off" placeholder="email" required/>

                            <span class="input-group-btn">
                                <button id="search-user-btn" class="btn btn-success" type="submit">search</i></button>
                            </span>
                        </div>
                    </form>

					<p id="search-error" class="text-danger hidden">No account associated with entered email address.</p>
					<p id="email-error" class="text-danger hidden">Please enter a valid email address</p>
                </div>

            </div>

			<div id="user-card" class="card hidden mx-auto mt-4" style="width: 20rem;">
				<div id="user-body" class="card-body">

				</div>
				<p id="cancel-error" class="text-danger hidden">There was an issue cancelling this member.  Please try again later.</p>
			</div>
        </div>
    </body>

    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

    <script>
        $('#search-users-form').submit(function(event)
		{
			event.preventDefault();

			console.log('Search users form submit!');

			var value = $('#email-input').val();
			var valid = isValidEmailAddress(value);

			if (!valid)
			{
				// show error message
				$('#email-error').addClass('visible');
				$('#email-error').removeClass('hidden');

			} else {

				// hide error message
				$('#email-error').addClass('hidden');
				$('#email-error').removeClass('visible');

				// Grab the forms attributes
				var form = $(this),
				url = form.attr('action');

				// Send the data using post
				var posting = $.post( url, form.serialize(),
				    function( data )
				    {
				        // if data returned no errors
				        if (data.success)
				        {
							// hide search error message
							$('#search-error').addClass('hidden');

							console.log('Successfully loaded data!', data.success);
				            console.log('Loaded data!', data.results);

							var $body = $('#user-body');
							var active = data.results['paid'] == 1 ? 'Active' : 'Inactive';
							var disabled = data.results['paid'] == 1 ? '' : 'disabled';

							var html = '<h4 class="card-title">'+ data.results['name'] +'</h4><h6 class="card-subtitle mb-2 text-muted">'+ data.results['email'] +'</h6><p class="card-text">Membership Status: '+ active +'</p><p class="card-text">Payment Type: '+ data.results['payment_type'] +'</p><form id="demote-form" class="form" action="demote_member.php" method="POST"><input type="hidden" name="email" value="'+ data.results['email'] +'"><button id="cancel-btn" type="submit" class="btn btn-danger"'+ disabled +'>Cancel Membership</button></form>';

							$body.html(html);

							$('#user-card').removeClass('hidden');

				        } else {

				            console.log('Error loading data!', data.error);

							// hide card
							$('#user-card').addClass('hidden');

							// show search error message
							$('#search-error').removeClass('hidden');
				        }
				    } ,'json' );
			}


		});

		$(document).on('submit', '#demote-form', function(event)
		{
			event.preventDefault();

			console.log('demote-form submit!');

			var form = $(this),
			url = form.attr('action');

			console.log('Form action? ', url);

			// Send the data using post
			var posting = $.post( url, form.serialize(),
			    function( data )
			    {

			        // if data returned no errors
			        if (data.success)
			        {
			            console.log('Successfully demoted user!', data.success);

						// hide error message
						$('#cancel-error').addClass('hidden');

						var $body = $('#user-body');

						var active = data.results['paid'] == 1 ? 'Active' : 'Inactive';

						var html = '<h4 class="card-title">'+ data.results['name'] +'</h4><h6 class="card-subtitle mb-2 text-muted">'+ data.results['email'] +'</h6><p class="card-text">Membership Status: '+ active +'</p><p class="card-text">Payment Type: '+ data.results['payment_type'] +'</p>';

						$body.html(html);

			        } else {

			            console.log('Error demoting user!', data.error);

						// show error message
						$('#cancel-error').removeClass('hidden');
			        }
			    } ,'json' );

				console.log('Posting? ', posting);
		});


		// $('#email-input').on('change', function()
		// {
		// 	var value = $(this).val();
        //
		// 	console.log('email-input value changed: ', value);
		// 	var valid = isValidEmailAddress(value);
        //
		// 	console.log('valid email? ' + value + ' valid: '+ valid);
        //
		// 	$('#search-user-btn').prop('disabled', !valid);
        //
		// 	if (!valid)
		// 	{
		// 		$('#email-error').addClass('visible');
		// 		$('#email-error').removeClass('hidden');
        //
		// 	} else {
        //
		// 		$('#email-error').addClass('hidden');
		// 		$('#email-error').removeClass('visible');
		// 	}
        //
		// });

		function isValidEmailAddress(emailAddress)
		{
		    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
		    return pattern.test(emailAddress);
		};
    </script>

</html>
