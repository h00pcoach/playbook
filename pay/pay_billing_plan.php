<?php 

require('../ChromePhp.php');

use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;

// require('../mydb_pdo.php');
include("credentials.php");

$client_id_sand = PAYPAL_CLIENT_ID_SAND;
$client_id_prod = PAYPAL_CLIENT_ID_PROD;

$client_secret_sand = PAYPAL_SECRET_SAND;
$client_secret_prod = PAYPAL_SECRET_PROD;

    // Initialize PDO
// $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
// $conn->exec("set names utf8");

// $sql = "SELECT * FROM settings";
// $st = $conn->prepare($sql);

// Bind parameters
// $st->execute();
// $settings = $st->fetch();
// $conn = null;

$payment_type = $_SESSION('payment_type');
$payment_amt = payment_type == "monthly" ? $settings['monthly_amount'] : $settings['yearly_amount'];
$frequency = payment_type == "monthly" ? "Month" : "Year";
$cycles = payment_type == "monthly" ? "12" : "1";

ChromePhp::log("payment_type" . $payment_type);

// Create a new billing plan
$plan = new Plan();
$plan->setName('Hoopcoach Playbook Pro')
  ->setDescription('Playbook Pro' . $payment_type . 'subscription')
  ->setType('fixed');

// Set billing plan definitions
$paymentDefinition = new PaymentDefinition();
$paymentDefinition->setName($payment_type . ' Subscription')
  ->setType('REGULAR')
  ->setFrequency($frequency)
  ->setFrequencyInterval('1')
  ->setCycles($cycles)
  ->setAmount(new Currency(array('value' => $payment_amt, 'currency' => 'USD')));

// Set charge models
$chargeModel = new ChargeModel();
$chargeModel->setType('SHIPPING')
  ->setAmount(new Currency(array('value' => 10, 'currency' => 'USD')));
$paymentDefinition->setChargeModels(array($chargeModel));

// Set merchant preferences
$merchantPreferences = new MerchantPreferences();
$merchantPreferences
// ->setReturnUrl('http://localhost:3000/processagreement')
  // ->setCancelUrl('http://localhost:3000/cancel')
  ->setAutoBillAmount('yes')
  ->setInitialFailAmountAction('CONTINUE')
  ->setMaxFailAttempts('0')
  ->setSetupFee(new Currency(array('value' => 1, 'currency' => 'USD')));

$plan->setPaymentDefinitions(array($paymentDefinition));
$plan->setMerchantPreferences($merchantPreferences);

//create plan
try {
  $createdPlan = $plan->create($apiContext);

  try {
    $patch = new Patch();
    $value = new PayPalModel('{"state":"ACTIVE"}');
    $patch->setOp('replace')
      ->setPath('/')
      ->setValue($value);
    $patchRequest = new PatchRequest();
    $patchRequest->addPatch($patch);
    $createdPlan->update($patchRequest, $apiContext);
    $plan = Plan::get($createdPlan->getId(), $apiContext);

    // Output plan id
    echo $plan->getId();
  } catch (PayPal\Exception\PayPalConnectionException $ex) {
    echo $ex->getCode();
    echo $ex->getData();
    die($ex);
  } catch (Exception $ex) {
    die($ex);
  }
} catch (PayPal\Exception\PayPalConnectionException $ex) {
  echo $ex->getCode();
  echo $ex->getData();
  die($ex);
} catch (Exception $ex) {
  die($ex);
}

?>