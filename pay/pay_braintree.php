<?php 
  // ob_start();
	// require('../mydb_pdo.php');
  include("../pay/credentials.php");

  // Initialize PDO
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$conn->exec("set names utf8");

	$sql = "SELECT * FROM settings";
	$st = $conn->prepare( $sql );

	// Bind parameters
	$st->execute();
	$settings = $st->fetch();
  $conn = null;

  $gateway = new Braintree_Gateway([
    'environment' => 'sandbox',
    'merchantId' => EXPRESS_ACCOUNT_SAND,
    'publicKey' => 'use_your_public_key',
    'privateKey' => 'use_your_private_key'
]);
?>
<head>
  <meta charset="utf-8">
  <script src="https://js.braintreegateway.com/web/dropin/1.13.0/js/dropin.min.js"></script>
</head>
<body>
  <div id="dropin-container"></div>
  <button id="submit-button">Request payment method</button>
  <script>
    var button = document.querySelector('#submit-button');

    braintree.dropin.create({
      authorization: EXPRESS_TOKEN_SAND,
      container: '#dropin-container'
    }, function (createErr, instance) {
      button.addEventListener('click', function () {
        instance.requestPaymentMethod(function (err, payload) {
          // Submit payload.nonce to your server
        });
      });
    });
  </script>
</body>

