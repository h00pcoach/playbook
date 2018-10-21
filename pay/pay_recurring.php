<?php  
  // ob_start();
	// require('../mydb_pdo.php');
include("../pay/credentials.php");

$client_id_sand = PAYPAL_CLIENT_ID_SAND;
$client_id_prod = PAYPAL_CLIENT_ID_PROD;

$client_secret_sand = PAYPAL_SECRET_SAND;
$client_secret_prod = PAYPAL_SECRET_PROD;

    // Initialize PDO
$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
$conn->exec("set names utf8");

$sql = "SELECT * FROM settings";
$st = $conn->prepare($sql);

    // Bind parameters
$st->execute();
$settings = $st->fetch();
$conn = null;
?>
<body>
  <!-- <div id="paypal-button-container"></div> -->
  <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
  <script src="https://www.paypalobjects.com/api/checkout.js"></script>
  <script>
    var payment_type = $('input[name=payment_type]:checked').val();
    var payment_amt = payment_type == "monthly" ? "<?= $settings['monthly_amount'] ?>" : "<?= $settings['yearly_amount'] ?>";
    var frequency = payment_type == "monthly" ? "MONTHLY" : "YEARLY";
    var cycles = payment_type == "monthly" ? "12" : "1";

    var paypal = require('paypal-rest-sdk');

    var clientId = 'YOUR CLIENT ID';
    var secret = 'YOUR SECRET';

    paypal.configure({
      'mode': 'sandbox', //sandbox or live
      'client_id': "<?= $client_id_sand ?>",
      'client_secret': "<?= $client_id_prod ?>"
    });

    console.log(`frequency ${frequency}`);
    console.log(`payment_amt ${payment_amt}`);
    console.log(`payment_type ${payment_type}`);
    
    var billPlan = {
      name: `Hoopcoach Playbook Pro`,
      description: `Playbook Pro ${payment_type} subscription`,
      type: "fixed",
      payment_definitions: [{
        name: `${payment_type} Subscription`,
        type: "REGULAR",
        frequency_interval: "1",
        frequency: frequency,
        cycles: cycles,
        amount: {
          currency: "USD",
          value: payment_amt
        }
      }],
      merchant_preferences: {
        setup_fee: {
          currency: "USD",
          value: "1"
        },
        max_fail_attempts: "0",
        auto_bill_amount: "YES",
        initial_fail_amount_action: "CONTINUE"
      }
    };
    var billingPlanAttribs = JSON.stringify(billPlan);
    console.log(`billingPlanAtrribs? ${billingPlanAttribs}`);
    var billingPlanUpdateAttributes = [{
        "op": "replace",
        "path": "/",
        "value": {
            "state": "ACTIVE"
        }
    }];

    paypal.billingPlan.create(billingPlanAttribs, function (error, billingPlan){
      if (error){
          console.log(error);
          throw error;
      } else {
          // Activate the plan by changing status to Active
          paypal.billingPlan.update(billingPlan.id, billingPlanUpdateAttributes, 
              function(error, response){
              if (error) {
                  console.log(error);
                  throw error;
              } else {
                  console.log(billingPlan.id);
              }
          });
      }
    });
    
  </script>
</body>