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
  <script src="https://www.paypalobjects.com/api/checkout.js"></script>
  <script>
  // Render the PayPal button
  paypal.Button.render({
    // Set your environment
    env: 'sandbox', // sandbox | production

    // Specify the style of the button
    style: {
      layout: 'vertical',  // horizontal | vertical
      size:   'medium',    // medium | large | responsive
      shape:  'rect',      // pill | rect
      color:  'gold'       // gold | blue | silver | white | black
    },
    
    // Specify allowed and disallowed funding sources
    //
    // Options:
    // - paypal.FUNDING.CARD
    // - paypal.FUNDING.CREDIT
    // - paypal.FUNDING.ELV
    funding: {
      allowed: [
        paypal.FUNDING.CARD,
        paypal.FUNDING.CREDIT
      ],
      disallowed: []
    },

    // PayPal Client IDs - replace with your own
    // Create a PayPal app: https://developer.paypal.com/developer/applications/create
    client: {
      sandbox: "<?= $client_id_sand ?>",
      production: "<?= $client_id_prod ?>"
    },

    payment: function (data, actions) {
      payment_type = $('input[name=payment_type]:checked').val();
      payment_amt = payment_type == "monthly" ? "<?= $settings['monthly_amount'] ?>" : "<?= $settings['yearly_amount'] ?>";
      console.log(`payment_amt ${payment_amt}`);
      console.log(`payment_type ${payment_type}`);
      return actions.payment.create({
        payment: {
          transactions: [
            {
              amount: {
                total: payment_amt,
                currency: 'USD'
              },
              description: 'Hoop Coach Playbook Pro',
              item_list: {
              items: [
                {
                  name: "Playbook Pro",
                  price: payment_amt,
                  currency: "USD",
                  quantity: "1"
                }
              ],
            }
          ]
        },
        experience: {
          input_fields: {
            no_shipping: 1
          }
        }
      });
    },
    onAuthorize: function (data, actions) {
      return actions.payment.execute()
        .then(function () {
          // TODO: Insert data into the database
          // TODO: Show Success page
          console.log(`data: ${data}`);
          console.log(`actions: ${actions}`);
          // window.alert('Payment Complete!');
        });
    }
  }, '#paypal-button-container');
  </script>
</body>

<!-- paypal.Button.render({
    // Set your environment
    env: 'sandbox', // sandbox | production

    // Specify the style of the button
    style: {
      layout: 'vertical',  // horizontal | vertical
      size:   'medium',    // medium | large | responsive
      shape:  'rect',      // pill | rect
      color:  'gold'       // gold | blue | silver | white | black
    },
    
    // Specify allowed and disallowed funding sources
    //
    // Options:
    // - paypal.FUNDING.CARD
    // - paypal.FUNDING.CREDIT
    // - paypal.FUNDING.ELV
    funding: {
      allowed: [
        paypal.FUNDING.CARD,
        paypal.FUNDING.CREDIT
      ],
      disallowed: []
    },

    // PayPal Client IDs - replace with your own
    // Create a PayPal app: https://developer.paypal.com/developer/applications/create
    client: {
      sandbox: "<?= $client_id_sand ?>",
      production: "<?= $client_id_prod ?>"
    },

    payment: function (data, actions) {
      payment_type = $('input[name=payment_type]:checked').val();
      payment_amt = payment_type == "monthly" ? "<?= $settings['monthly_amount'] ?>" : "<?= $settings['yearly_amount'] ?>";
      console.log(`payment_amt ${payment_amt}`);
      console.log(`payment_type ${payment_type}`);
      return actions.payment.create({
        payment: {
          transactions: [
            {
              amount: {
                total: `${payment_amt}`,
                currency: 'USD'
              },
              description: 'Hoop Coach Playbook Pro',
              item_list: [
                {
                  name: `Playbook Pro`,
                  description: `Playbook Pro ${payment_type} subscription`,
                  price: `${payment_amt}`
                }
              ]
            }
          ],
          redirect_urls: {
            // return_url: "https://www.hoopcoach.org/playbook/",
            // cancel_url: "https://www.hoopcoach.org/playbook/pay/cancel.php"
            // TODO: CHANGE TO PRODUCTION BEFORE UPLOADING
            return_url: "http://pb.local:8888",
            cancel_url: "http://pb.local:8888/pay/cancel.php"
          }
        }
      });
    },
    onAuthorize: function (data, actions) {
      return actions.payment.execute()
        .then(function () {
          // TODO: Insert data into the database
          console.log(`data: ${data}`);
          console.log(`actions: ${actions}`);
          window.alert('Payment Complete!');
        });
    }
  }, '#paypal-button-container'); -->