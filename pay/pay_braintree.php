<?php 
require('../mydb_pdo.php');
include("../pay/credentials.php");
require_once '../vendor/braintree/braintree_php/lib/Braintree.php';

// Initialize PDO
$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

$sql = "SELECT * FROM settings";
$st = $conn->prepare($sql);

// Bind parameters
$st->execute();
$settings = $st->fetch();
// $conn = null;

if (isset($_GET['uid'])) {
  $user_id = $_GET['uid'];
  $affiliate = $_GET['affiliate'];

  $sql = "SELECT * from users WHERE id = :id";
  $st = $conn->prepare($sql);

  // Bind parameters
  $st->bindValue(":id", $user_id, PDO::PARAM_INT);
  $st->execute();
  $user = $st->fetch();

  if ($user["paid"] == 1) {
    header('Location: ../play.php');
  }
  $debug = true;

  $env = 'sandbox';
  $merchantId = BT_MERCH_ID_SAND;
  $publicKey = BT_PUB_KEY_SAND;
  $privateKey = BT_PRIV_KEY_SAND;
  if (!$debug) {
    $env = 'prod';
    $merchantId = BT_MERCH_ID_PROD;
    $publicKey = BT_PUB_KEY_PROD;
    $privateKey = BT_PRIV_KEY_PROD;
  }

  $gateway = new Braintree_Gateway([
    'environment' => $env,
    'merchantId' => $merchantId,
    'publicKey' => $publicKey,
    'privateKey' => $privateKey
  ]);
    
  // Create a clientToken for the user -- only clients who haven't paid should reach this page
  $clientToken = $gateway->clientToken()->generate();
  $payment_type = $_GET["payment_type"];
  $price = $payment_type == 'monthly' ? '$5.00' : '$39.00';

  $plan_id = $payment_type == 'monthly' ? 'pb_monthly' : 'pb_yearly';

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_method_token'])) {
    $result = $gateway->customer()->create([
      'firstName' => $_POST['first_name'],
      'lastName' => $_POST['last_name'],
      'email' => $_POST['email'],
      'phone' => $_POST['phone'],
      'paymentMethodNonce' => $_POST['pay_method_token']
    ]);

    if ($result->success) {

      $pay_token = $result->customer->paymentMethods[0]->token;
      $pay_id = $result->customer->id;

      $subscription = $gateway->subscription()->create([
        'paymentMethodToken' => $pay_token,
        'planId' => $plan_id
      ]);

      $sub_id = $subscription->id;

      if ($subscription->success) {
        $sql = "UPDATE users SET paid = 1, pay_id = :pay_id, pay_token = :pay_token, payment_type = :payment_type, subscription_id = :sub_id WHERE id = :id";
        $st = $conn->prepare($sql);
        $st->bindValue(":pay_id", $pay_id, PDO::PARAM_INT);
        $st->bindValue(":pay_token", $pay_token, PDO::PARAM_STR);
        $st->bindValue(":payment_type", $payment_type, PDO::PARAM_STR);
        $st->bindValue(":subscription_id", $sub_id, PDO::PARAM_STR);
        $st->bindValue(":id", $user_id, PDO::PARAM_INT);
        $st->execute();
        $conn = null;
      } else {
        $data["error"] = "Subscription Error: Please try again later.";
        echo json_encode($data);
      }

    } else {
      # TODO:  These errors aren't displaying
      $data["error"] = "Card Declined: Please contact your financial institution.";
      echo json_encode($data);
    }
  }

} else {
  header('Location: login.php');
}

// CLEANUP
$conn = null;

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
            <form id="pay-form" method="POST">
              <div><input type="text" name="first_name" placeholder="Bruce" class="form-control" required></div>
              <div><input type="text" name="last_name" placeholder="Wayne" class="form-control" required></div>
              <div><input type="email" name="email" placeholder="Email" class="form-control" required></div>
              <div><input type="text" name="phone" placeholder="555-555-5555" class="form-control" required></div>
              <input type="hidden" name="payment_type" value="<?= $payment_type ?>">
			        <input type="hidden" name="affiliteid" value="<?php echo $affiliate; ?>" />

              <div id="dropin-container"></div>
                <button id="submit-button" class="btn btn-success" style="margin-top:20px; width:200px;">Request payment method</button>
              </div>
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
          name:{
            required: true,

          },
          email:{
            required:true,
            email:true
          }
        }
      })
    });
  
    $("#pay-form").submit(function( event )
    {

        console.log('form submit clicked: ', $(this));

        // Stop form from submitting normally
        event.preventDefault();
    });
    // $('#user-btn').click(function() 
    // {
    //   console.log(`user-btn clicked`);
    //   $('.hidden').toggle()
    // });
    
  </script>

  <script>
    var button = document.querySelector('#submit-button');

    braintree.dropin.create({
      authorization: "<?= $clientToken ?>",
      container: '#dropin-container'
    }, function (createErr, instance) {
      button.addEventListener('click', function () {
        instance.requestPaymentMethod(function (err, payload) 
        {

          $("#submit-button").attr('disabled', true)
          // console.log(JSON.parse(payload));
          // Submit payload.nonce to your server

          // Get some values from elements on the page:
          var form = $('#pay-form'),
          last_name = form.find( "input[name='last_name']" ).val(),
          first_name = form.find( "input[name='first_name']" ).val(),
          email = form.find( "input[name='email']" ).val(),
          phone = form.find( "input[name='phone']" ).val(),
          payment_type = form.find( "input[name='payment_type']" ).val();
          
          console.log(`payment_type ${payment_type}`);
          var url = `../pay/pay_braintree.php?uid=<?= $user_id ?>&affiliate=<?= $affiliate ?>&payment_type=${payment_type}`;

          // Send the data using post
          var posting = $.post( url, { last_name: last_name, first_name: first_name, email: email, phone: phone, pay_method_token: payload.nonce} );

          console.log(`payload.nonce? ${payload.nonce}`);
          // Put the results in a div
          posting.done(function( data )
          {
            console.log(`payload? ${JSON.stringify(payload)}`);
              // if data returned no errors
              if (data.error)
              {
                  // TODO: Display error message
                  console.log('ERROR sending paymentForm!', data.error);

                  $('#errors.hidden').toggle();
                  $('#errors').html(`Errors:</ br> ${data.error}`);
                  $("#submit-button").attr('disabled', false)

              } else {
                // TODO: Redirect to success page
                  console.log('SUCCESS sending paymentForm!', data);

              }
          });
        });
      });
    });
  </script>
</body>
</html>

