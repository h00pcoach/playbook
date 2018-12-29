<?php 
require('../mydb_pdo.php');
require_once('../pay/braintree_init.php');

if (isset($_GET['uid'])) {
  $user_id = $_GET['uid'];

  // Initialize PDO
  $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
  $conn->exec("set names utf8");

  $sql = "SELECT * from users WHERE id = :id";
  $st = $conn->prepare($sql);

  // Bind parameters
  $st->bindValue(":id", $user_id, PDO::PARAM_INT);
  $st->execute();
  $user = $st->fetch();
  $conn = null;
  if ($user["paid"] == 1) {
    header('Location: ../play.php');
  }
  $affiliate = $_GET['affiliate'];
  $payment_type = $_GET["payment_type"];
  $price = $payment_type == 'monthly' ? '$5.00' : '$39.00';
  $plan_id = $payment_type == 'monthly' ? 'pb_monthly' : 'pb_yearly';
} else {
  header('Location: login.php');
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
  <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

  <style type="text/css">
    .form-control{
      margin-top:10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-offset-3 col-sm-6">
        <h2 class="text-center text-primary" style="margin-top: 40px;">Purchase <?= $payment_type ?> Subscription</h2>
        <h4 class="text-center text-muted"><?= $price ?> billed <?= $payment_type ?></h4>
        <div id="errors" class="hidden"></div>
        <div class="panel panel-primary" style="margin-top: 20px;">
          <div class="panel-heading">Card Holder Information</div>
          <div class="panel-body">
            <form id="pay-form" method="POST" action="../pay/subscribe.php">
              <div><input type="text" name="first_name" placeholder="Bruce" class="form-control" required></div>
              <div><input type="text" name="last_name" placeholder="Wayne" class="form-control" required></div>
              <div><input type="email" name="email" placeholder="Email" class="form-control" required></div>
              <div><input type="text" name="phone" placeholder="555-555-5555" class="form-control" required></div>
              <input type="hidden" name="payment_type" value="<?= $payment_type ?>">
              <input type="hidden" name="price" value="<?= $price ?>">
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
  
    // $("#pay-form").submit(function( event )
    // {

    //     console.log('form submit clicked: ', $(this));

    //     // Stop form from submitting normally
    //     event.preventDefault();
    // });
    // $('#user-btn').click(function() 
    // {
    //   console.log(`user-btn clicked`);
    //   $('.hidden').toggle()
    // });
    
  </script>

  <script>
    // var button = document.querySelector('#submit-button');
    var form = document.querySelector('form');
    var client_token = "<?php echo ($gateway->ClientToken()->generate()); ?>";
    
    braintree.dropin.create({
      authorization: "<?= $gateway->ClientToken()->generate() ?>",
      container: '#dropin-container',
      paypal: {
        flow: 'vault'
      }
    }, function (createErr, instance) {
      form.addEventListener('submit', function (event) {
        event.preventDefault();

        instance.requestPaymentMethod(function (err, payload) {
            console.log(`Request Payment Method ${JSON.stringify(payload)}`);
            console.log(`Payload nonce? ${payload.nonce}`);
          if (err) {
            console.log('Request Payment Method Error', err);
            return;
          } 
          // Add the nonce to the form and submit
          $('#nonce').val(payload.nonce);
          form.submit();
        });
      });
      // button.addEventListener('click', function () {
        // instance.requestPaymentMethod(function (err, payload) 
        // {

        //   $("#submit-button").attr('disabled', true)
        //   // console.log(JSON.parse(payload));
        //   // Submit payload.nonce to your server

        //   // Get some values from elements on the page:
        //   var form = $('#pay-form'),
        //   last_name = form.find( "input[name='last_name']" ).val(),
        //   first_name = form.find( "input[name='first_name']" ).val(),
        //   email = form.find( "input[name='email']" ).val(),
        //   phone = form.find( "input[name='phone']" ).val(),
        //   payment_type = form.find( "input[name='payment_type']" ).val();
          
        //   console.log(`payment_type ${payment_type}`);

        //   // Send the data using post
        //   var posting = $.post( url, { last_name: last_name, first_name: first_name, email: email, phone: phone, pay_method_token: payload.nonce} );

        //   console.log(`payload.nonce? ${payload.nonce}`);
        //   // Put the results in a div
        //   posting.done(function( data )
        //   {
        //     console.log(`payload? ${JSON.stringify(payload)}`);
        //     console.log(`data? ${JSON.stringify(data)}`);
        //       // if data returned no errors
        //       if (data.error)
        //       {
        //           // TODO: Display error message
        //           console.log('ERROR sending paymentForm!', data.error);

        //           $('#errors.hidden').toggle();
        //           $('#errors').html(`Errors:</ br> ${data.error}`);
        //           $("#submit-button").attr('disabled', false)

        //       } else {
        //         // TODO: Redirect to success page
        //           console.log('SUCCESS sending paymentForm!', data);

        //       }
        //   });
        // });
      // });
    });
  </script>
</body>
</html>

