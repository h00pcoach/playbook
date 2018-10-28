# PAYMENT PLAY READ ME
```
Rember to change the urls below based on enviroment.
```

## To get Access-Token:

1. Run the following command in terminal.  Replace: `client_id` and `secret` with values in the `credentials` file.

```
curl -v https://api.sandbox.paypal.com/v1/oauth2/token \
  -H "Accept: application/json" \
  -H "Accept-Language: en_US" \
  -u "AU6LjAXAm60-wPuMDtjaqzaATFLG_pn2XTLSKHWtXLh0glv9Fjx26OfnVOqs-6MSqtF5wL_Xp_GYc2Y4:EHkgYNjVIjZspksMdZLuHGM2M-VZDSWAbbFeICWTVhSbJvFDwh5SPy4H7ZG-TviX-OTg2EoT0hUf4GfV" \
  -d "grant_type=client_credentials"
```
2. Get `access_token` from the response.  You will need this in the next section.

## To create payment plan:
1. Replace ***Access-Token*** with `access_token` retrieved above

2. Replace ***INSERT PAYMENT PLAN DETAILS JSON*** with the json object from either `create_yearly_payment_plan.json` or `create_yearly_payment_plan.json`

3. In `terminal` run:


```
curl -v -X POST https://api.sandbox.paypal.com/v1/payments/billing-plans/ \
-H "Content-Type: application/json" \
-H "Authorization: Bearer ***Access-Token***" \
-d '***INSERT PAYMENT PLAN DETAILS JSON***'
```


curl -v -X POST https://api.sandbox.paypal.com/v1/payments/billing-plans/ \
-H "Content-Type: application/json" \
-H "Authorization: Bearer A21AAHu-XNmgSZYJ_-yLlc8PQk_5SzZ01O-CpEixsXEO4mjGvtgcMoTNV2VsjqKe18xBHA-U2kkhOCJtvyWXiTrfrf6y_jWww" \
-d '{
  "name": "Hoopcoach Playbook Pro",
  "description": "Playbook Pro Monthly subscription",
  "type": "FIXED",
  "payment_definitions": [{
    "name": "Monthly Subscription",
    "type": "REGULAR",
    "frequency": "MONTH",
    "frequency_interval": "1",
    "amount": {
      "value": "5",
      "currency": "USD"
    },
    "cycles": "12"
  }],
  "merchant_preferences": {
    "setup_fee": {
      "value": "1",
      "currency": "USD"
    },
    "return_url": "http://pb.local:8888/pay/success.php",
    "cancel_url": "http://pb.local:8888/pay/cancel.php",
    "auto_bill_amount": "YES",
    "initial_fail_amount_action": "CONTINUE",
    "max_fail_attempts": "0"
  }
}'

curl -v -X POST https://api.sandbox.paypal.com/v1/payments/billing-plans/ \
-H "Content-Type: application/json" \
-H "Authorization: Bearer A21AAHu-XNmgSZYJ_-yLlc8PQk_5SzZ01O-CpEixsXEO4mjGvtgcMoTNV2VsjqKe18xBHA-U2kkhOCJtvyWXiTrfrf6y_jWww" \
-d '{
  "name": "Hoopcoach Playbook Pro",
  "description": "Playbook Pro Yearly subscription",
  "type": "FIXED",
  "payment_definitions": [{
    "name": "Yearly Subscription",
    "type": "REGULAR",
    "frequency": "YEAR",
    "frequency_interval": "1",
    "amount": {
      "value": "39",
      "currency": "USD"
    },
    "cycles": "1"
  }],
  "merchant_preferences": {
    "setup_fee": {
      "value": "1",
      "currency": "USD"
    },
    "return_url": "http://pb.local:8888/pay/success.php",
    "cancel_url": "http://pb.local:8888/pay/cancel.php",
    "auto_bill_amount": "YES",
    "initial_fail_amount_action": "CONTINUE",
    "max_fail_attempts": "0"
  }
}'


