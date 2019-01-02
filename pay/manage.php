<?php
require('../mydb_pdo.php');
require_once(__DIR__ . '/braintree_init.php');
require_once(__DIR__ . '/user.php');

if (!isset($_GET['uid'])) {
  header('Location: ../play.php');
}

$user = get_user($_GET['uid']);

if ($user['paid'] != 1 || $user['subscription_id'] == null) {
  header('Location: ../play.php');
}

$payment_type = $user["payment_type"];
$price = $payment_type == 'monthly' ? '$5.00' : '$39.00';

?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <script src="https://js.braintreegateway.com/web/dropin/1.14.1/js/dropin.min.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
</head>
<body>
  <nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="#">
      <img class="" src="../pb_logo.png" srcset="../pb_logo@2x.png" alt="Logo" width="auto" height="30">
    </a>
  </nav>
  <div class="container">
    <div class="row mt-5">
      
      <div class="col-12">
        <h1 class="text-primary">Manage Playbook Pro Subscription</h1>
        <hr class="mb-5">
      </div>
    </div>
    
    <div class="row">
      <div id="update-pay-col" class="col-12 mb-5" style="display: none">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title text-capitalize">Update Payment Method</h5>
            <p id="update-errors" class="badge badge-danger" style="display: none"></p>
          </div>
          <div class="card-body">
            <form id="update-form" action="#">
              <input type="hidden" name="user_id" value="<?= $user["id"] ?>">
              <input type="hidden" name="subscription_id" value="<?= $user["subscription_id"] ?>">
              <input type="hidden" id="update-nonce" name="nonce">
              <div id="update-dropin-container"></div>
              <button id="update-submit-button" class="btn btn-success" style="margin-top:20px; width:200px;">Update</button>
            </form>
          </div>
        </div>
      </div>
      
      <div class="col-12">
        <div class="d-flex justify-content-left">
          <div class="card" style="width: 18rem;">
            <div class="card-header">
              <h5 class="card-title text-capitalize"><?= $payment_type ?> Subscription</h5>
              <div class="card-subtitle text-muted"><?= $price ?></div>
            </div>
            <div class="card-body">
              <p class="card-text">You are a <?= $payment_type ?> Playbook Pro subscriber.</p>
              <p id="update-success" class="badge badge-success d-none">Successfully updated.<br></p>
              <a id="update-btn" href="#" class="btn btn-warning">Update</a>
              <a id="cancel-btn" href="#" class="btn btn-danger">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

  <script>
    // - Catch cancel click an verify cancellation
    $('#cancel-btn').click(() => {
      if (confirm("Are you sure you want to cancel your subscription?")) 
      {
        $("#cancel-btn").attr('disabled', true);
        var url = '../pay/cancel.php';
        
        // Send the data using post
        var posting = $.post( url, {user_id: '<?= $user['id'] ?>', subscription_id: '<?= $user['subscription_id'] ?>'});
        posting.done(function( data )
        {
            console.log(`cancel data? ${JSON.stringify(data)}`);

          // if data returned no errors
          if (data.error)
          {
            console.log(`cancel data error!`);
            $("#cancel-btn").attr('disabled', false);
          } else {
            console.log(`cancel data success!`);
            $('#update-success').removeClass("d-none").addClass("d-block");
          }
        });
      }
    });

    $('#update-btn').click(() => {
      $(this).attr('disabled', true);
      
      // Show the update payment section
      $('#update-pay-col').toggle();
    });
  </script>

  <script>
    var form = document.querySelector('form');
    var url = "../pay/update.php";
    
    braintree.dropin.create({
      authorization: "<?= $gateway->ClientToken()->generate(['customerId' => $user['pay_id']]) ?>",
      container: '#update-dropin-container',
      paypal: {
        flow: 'vault'
      }
    }, function (createErr, instance) {
      form.addEventListener('submit', function (event) {
        event.preventDefault();

        console.log(`submit clicked!!`);

        // Disable button to prevent submit
        $("#update-submit-button").attr('disabled', true);

        instance.requestPaymentMethod(function (err, payload) {
          if (err) {
            console.log('Request Payment Method Error', err);
            $('#update-errors').html(`Errors: ${err}`);
            $('#update-errors').toggle();
            $("#update-submit-button").attr('disabled', false);
            return;
          }

          // TEST FAILURE
          // $('#nonce').val('fake-processor-declined-visa-nonce');

          // TEST SUCCESS
          // $('#nonce').val('fake-valid-nonce');
          console.log(`updated nonce? ${payload.nonce}`);
          // Add the nonce to the form and submit
          $('#update-nonce').val(payload.nonce);

          // Send the data using post
          var posting = $.post( url, $('#update-form').serialize(),
          function( data )
          {
            console.log(`data? ${JSON.stringify(data)}`);
            // if data returned no errors
            if (data.error)
            {
              // console.log('Error loading data!', data.error);
              $('#update-errors').html(`Errors: ${data.error}`);
              $('#update-errors').toggle();
              $("#update-submit-button").attr('disabled', false);

            } else {
              $('#update-pay-col').toggle();
              $('#update-success').removeClass("d-none").addClass("d-block");
            }
          } ,'json' );
        });
      });
    });
  </script>
</body>
</html>