<?php 
require('../mydb_pdo.php');
require_once(__DIR__ . '/braintree_init.php');
require_once(__DIR__ . '/user.php');
require('../ChromePhp.php');

ChromePhp::log('session user_id? ' . $_SESSION['user_id']);
if (isset($_SESSION['user_id']) && isset($_GET['uid'])) {
  $user_id = $_GET['uid'];
  $user = get_user($user_id);

  if ($user["paid"] == 1) {
    header('Location: ../play.php');
  }
  $affiliate = $_GET['affiliate'];
  $payment_type = $_GET["payment_type"];
  $price = $payment_type == 'monthly' ? '$5.00' : '$39.00';
  $plan_id = $payment_type == 'monthly' ? 'pb_monthly' : 'pb_yearly';
} else {
  header('Location: ../play.php');
}
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


  <style type="text/css">
    .form-control{
      margin-top: 10px;
    }
    #errors {
      padding: 15px;
      font-weight: bold;
      display: none;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="#">
      <img class="" src="../pb_logo.png" srcset="../pb_logo@2x.png" alt="Logo" width="auto" height="30">
    </a>
  </nav>
  <div class="container">
    
    <div class="row">
      <div class="col-12 mt-5">
        <p id="errors" class="bg-danger"></p>
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title text-capitalize text-primary">Purchase <?= $payment_type ?> Subscription</h3>
            <div class="card-subtitle text-muted"><?= $price ?> billed <?= $payment_type ?></div>
          </div>
          <div class="card-body">
            <div class="card-subtitle">Card Holder Information</div>
            <form id="pay-form" method="POST" action="https://www.hoopcoach.org/playbook/pay/subscribe.php">
              <div><input type="text" name="first_name" placeholder="First Name" class="form-control" required></div>
              <div><input type="text" name="last_name" placeholder="Last Name" class="form-control" required></div>
              <div><input type="email" name="email" placeholder="Email" class="form-control" required></div>
              <div><input type="text" name="phone" placeholder="Phone" class="form-control" required></div>
              <input type="hidden" name="payment_type" value="<?= $payment_type ?>">
              <input type="hidden" name="planid" value="<?= $plan_id ?>">
			        <input type="hidden" name="affiliteid" value="<?= $affiliate; ?>" />
			        <input type="hidden" name="userid" value="<?= $user_id; ?>" />
			        <input type="hidden" id="nonce" name="nonce" value="" />

              <div id="dropin-container" style="margin-top: 20px"></div>
                
              <button id="submit-button" class="btn btn-success" style="margin-top:20px; width:200px;">Create Subscription</button>
            </form>
            
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery.validate.min.js"></script>
  <script type="text/javascript">
    $(function(){
      $('form').validate({
        rules:{
          first_name:{
            required: true,
          },
          last_name:{
            required: true,
          },
          email:{
            required: true,
            email: true
          },
          phone:{
            required: true,
          }
        }
      })
    });
  </script>

  <script>
    // var button = document.querySelector('#submit-button');
    var form = document.querySelector('form');
    var url = "../pay/subscribe.php";
    
    braintree.dropin.create({
      authorization: "<?= $gateway->ClientToken()->generate() ?>",
      container: '#dropin-container',
      paypal: {
        flow: 'vault'
      }
    }, function (createErr, instance) {
      form.addEventListener('submit', function (event) {
        event.preventDefault();

        console.log(`submit clicked!!`);

        // Disable button to prevent submit
        $("#submit-button").attr('disabled', true);

        instance.requestPaymentMethod(function (err, payload) {
          if (err) {
            console.log('Request Payment Method Error', err);
            $('#errors').toggle();
            $('#errors').html(`Errors: ${err}`);
            $("#submit-button").attr('disabled', false);
            return;
          }

          // TEST FAILURE
          // $('#nonce').val('fake-processor-declined-visa-nonce');

          // TEST SUCCESS
          // $('#nonce').val('fake-valid-nonce');
                    
          // Add the nonce to the form and submit
          $('#nonce').val(payload.nonce);

          // Send the data using post
          var posting = $.post( url, $('#pay-form').serialize(),
          function( data )
          {
            console.log(`data? ${JSON.stringify(data)}`);
            // if data returned no errors
            if (data.error)
            {
              // console.log('Error loading data!', data.error);
              $('#errors').toggle();
              $('#errors').html(`Errors: ${data.error}`);
              $("#submit-button").attr('disabled', false);

            } else {
              // console.log('Successfully loaded data!', data);
              window.location.href = 'https://www.hoopcoach.org/playbook/pay/success_new.php';
            }
          } ,'json' );
        });
      });
    });
  </script>
</body>
</html>

