<?php 
use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

// Create new agreement
$agreement = new Agreement();
$agreement->setName('Yearly Subscription Agreement')
  ->setDescription('Hoopcoach Pro Yearly subsription agreement.')
  ->setStartDate(date("c"));

// Set plan id
$plan = new Plan();
$plan->setId('P-18G006197U856841RXQYJP7Y');
$agreement->setPlan($plan);

// Add payer type
$payer = new Payer();
$payer->setPaymentMethod('paypal');
$agreement->setPayer($payer);
?>