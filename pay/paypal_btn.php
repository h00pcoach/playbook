<?php 
  // ob_start();
	// include '../mydb.php';
	// require('./mydb_pdo.php');
  require('./ChromePhp.php');
  include("./pay/credentials.php");

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
<div id="paypal-button"></div>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script>
  paypal.Button.render({
    // Configure environment
    env: 'sandbox',
    client: {
      sandbox: 'PAYPAL_CLIENT_ID_SAND',
      production: 'PAYPAL_CLIENT_ID_PROD'
    },
    // Customize button (optional)
    locale: 'en_US',
    style: {
      size: 'small',
      color: 'gold',
      shape: 'pill',
    },
    // Set up a payment
    payment: function(data, actions) {
      return actions.payment.create({
        transactions: [{
          amount: {
            total: '0.01',
            currency: 'USD'
          }
        }]
      });
    },
    // Execute the payment
    onAuthorize: function(data, actions) {
      return actions.payment.execute().then(function() {
        // Show a confirmation message to the buyer
        window.alert('Thank you for your purchase!');
      });
    }
  }, '#paypal-button');

</script>