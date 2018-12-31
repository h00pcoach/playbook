<?php
require('../mydb_pdo.php');
require_once(__DIR__ . '/user.php');

if (!isset($user['paid']) || $user['subscription_id'] == 0) {
  header('Location: ../play.php');
}

$payment_type = $user["payment_type"];
$price = $payment_type == 'monthly' ? '$5.00' : '$39.00';

?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-12 mt-5">
        <h1 class="text-primary">Manage Playbook Pro Subscription</h1>
        <hr class="mb-5">
        <div class="d-flex justify-content-left">
          <div class="card" style="width: 18rem;">
            <div class="card-body">
              <h5 class="card-title text-capitalize"><?= $payment_type ?> Subscription</h5>
              <h6 class="card-subtitle mb-2 text-muted"><?= $price ?></h6>
              <p class="card-text">You are a <?= $payment_type ?> Playbook Pro subscriber.</p>
              <a href="#" class="btn btn-danger">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

  <script>
    // TODO: 
    // - Catch cancel click an verify cancellation
    // - Build cancel via Braintree docs: https://developers.braintreepayments.com/reference/request/subscription/cancel/php
  </script>
</body>
</html>