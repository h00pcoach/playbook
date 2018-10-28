# PAYMENT PLAY READ ME
```
Rember to change the urls below based on enviroment.
```

## To get Access-Token:

1. Run the following command in terminal.  Replace: `<client_id>` and `<secret>` with values in the `credentials` file.

```
curl -v https://api.sandbox.paypal.com/v1/oauth2/token \
  -H "Accept: application/json" \
  -H "Accept-Language: en_US" \
  -u "<client_id>:<secret>" \
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

## List Billing Plans:

1. Run the following bash script with the `access_token` retrieved in ***To Get Access Token***:

```
curl -v -X GET https://api.sandbox.paypal.com/v1/payments/billing-plans?page_size=3&status=ALL&page_size=2&page=1&total_required=yes \
-H "Content-Type: application/json" \
-H "Authorization: Bearer ***Access-Token***"

```


