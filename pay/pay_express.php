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
?>
<!-- <div id="paypal-button"></div> -->
<script src="https://www.paypalobjects.com/api/checkout.js" data-version-4></script>
<script src="https://js.braintreegateway.com/web/3.33.0/js/client.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.33.0/js/paypal-checkout.min.js"></script>
<script>
  paypal.Button.render({
    braintree: braintree,
    client: {
      production: 'CLIENT_TOKEN_FROM_SERVER',
      sandbox: 'EXPRESS_TOKEN_SAND'
    },
    env: 'sandbox', // Or 'sandbox'
    commit: true, // This will add the transaction amount to the PayPal button

    payment: function (data, actions) {
      return actions.braintree.create({
        flow: 'checkout', // Required
        amount: <?= $_SESSION['payment_amt'] ?>, // Required
        currency: 'USD', // Required
        enableShippingAddress: false,
      });
    },

    onAuthorize: function (payload) {
      // Submit `payload.nonce` to your server.
    },
  }, '#paypal-button');

</script>

