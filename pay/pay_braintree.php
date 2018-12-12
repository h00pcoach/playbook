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
$conn = null;

if (isset($_GET['uid'])) {
  $user_id = $_GET['uid'];

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

  $clientToken = $gateway->clientToken()->generate();

  // TODO: 
  //  - Determine wether request is for yearly or monthly subscription
  //  - Set the planId (pb_monthly, pb_yearly) based on above
  //  - 

  $result = $gateway->subscription()->create([
    'paymentMethodToken' => 'the_token',
    'planId' => 'silver_plan_gbp',
    'merchantAccountId' => $merchantId
  ]);

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
</head>
<body>
  <div id="dropin-container"></div>
  <button id="submit-button">Request payment method</button>
  <script>
    var button = document.querySelector('#submit-button');

    braintree.dropin.create({
      authorization: "<?= $clientToken ?>",
      container: '#dropin-container'
    }, function (createErr, instance) {
      button.addEventListener('click', function () {
        instance.requestPaymentMethod(function (err, payload) {
          console.log(`payload: ${JSON.parse(payload)}`);
          // Submit payload.nonce to your server
        });
      });
    });
  </script>
</body>
</html>

