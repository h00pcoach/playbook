<?php 
use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

// Create new agreement
$agreement = new Agreement();
$agreement->setName('Monthly Subscription Agreement')
  ->setDescription('Hoopcoach Pro Monthly subsription agreement.')
  ->setStartDate(date("c"));

// Set plan id
$plan = new Plan();
$plan->setId('P-1UF4317244044801CXQZKVHA');
$agreement->setPlan($plan);

// Add payer type
$payer = new Payer();
$payer->setPaymentMethod('paypal');
$agreement->setPayer($payer);

try {
  // Create agreement
  $agreement = $agreement->create($apiContext);

  // Extract approval URL to redirect user
  $approvalUrl = $agreement->getApprovalLink();

  if (isset($_GET['success']) && $_GET['success'] == 'true') {
    $token = $_GET['token'];
    $agreement = new \PayPal\Api\Agreement();

    try {
    // Execute agreement
      $agreement->execute($token, $apiContext);
    } catch (PayPal\Exception\PayPalConnectionException $ex) {
      echo $ex->getCode();
      echo $ex->getData();
      die($ex);
    } catch (Exception $ex) {
      die($ex);
    }
  } else {
    echo "user canceled agreement";
  }
} catch (PayPal\Exception\PayPalConnectionException $ex) {
  echo $ex->getCode();
  echo $ex->getData();
  die($ex);
} catch (Exception $ex) {
  die($ex);
}
?>