<?php
session_start();
include(__DIR__ . "/credentials.php");
require_once("../vendor/autoload.php");

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