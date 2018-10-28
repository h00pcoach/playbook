<?php 
define('PAYPAL_ACCOUNT_SAND', 'andy_dold-facilitator@comcast.net');
define('PAYPAL_CLIENT_ID_SAND', 'AU6LjAXAm60-wPuMDtjaqzaATFLG_pn2XTLSKHWtXLh0glv9Fjx26OfnVOqs-6MSqtF5wL_Xp_GYc2Y4');
define('PAYPAL_SECRET_SAND', 'EHkgYNjVIjZspksMdZLuHGM2M-VZDSWAbbFeICWTVhSbJvFDwh5SPy4H7ZG-TviX-OTg2EoT0hUf4GfV');

define('PAYPAL_ACCOUNT_PROD', 'admin@hoopcoach.org');
define('PAYPAL_CLIENT_ID_PROD', 'AWkmSO1YdWEBL_2gCnO3WxGtYkOkVGzc4a7Z2utnLnNMaFijTcFE5E_woFrYDKrONT39sMmO8SJdIS35');
define('PAYPAL_SECRET_PROD', 'EEu3e6FQiBxSMo9-d562pPD65JRPBAJl1JLzeS29G-pZTOnI4INCsWjO7f9GnTZ_EqFlemnJMRUTUxq2');

define('EXPRESS_ACCOUNT_SAND', 'andy_dold-facilitator@comcast.net');
define('EXPRESS_TOKEN_SAND', 'access_token$sandbox$q4k7bpjzxf5t2m5q$f39f7111fc3b24774f54488ba5968a7e');

define('YEARLY_PAY_PLAY_SAND', "P-18G006197U856841RXQYJP7Y");
define('MONTHLY_PAY_PLAY_SAND', "P-1UF4317244044801CXQZKVHA");

define('YEARLY_PAY_PLAY_PROD', "");
define('MONTHLY_PAY_PLAY_PROD', "");


// curl -v -X GET https://api.sandbox.paypal.com/v1/payments/billing-plans?page_size=3&status=ALL&page_size=2&page=1&total_required=yes \
// -H "Content-Type: application/json" \
// -H "Authorization: Bearer A21AAF7AW01qNrU52A9tZ8pz5cSHQn618rLoXmHM-NV0U69D-71Y0UgvodSbEu76qoouupDka7A7qdaFBpHclExl9zP8kRXGw"
// "access_token":"A21AAF7AW01qNrU52A9tZ8pz5cSHQn618rLoXmHM-NV0U69D-71Y0UgvodSbEu76qoouupDka7A7qdaFBpHclExl9zP8kRXGw"

// curl -v https://api.sandbox.paypal.com/v1/oauth2/token \
//   -H "Accept: application/json" \
//   -H "Accept-Language: en_US" \
//   -u "AU6LjAXAm60-wPuMDtjaqzaATFLG_pn2XTLSKHWtXLh0glv9Fjx26OfnVOqs-6MSqtF5wL_Xp_GYc2Y4:EHkgYNjVIjZspksMdZLuHGM2M-VZDSWAbbFeICWTVhSbJvFDwh5SPy4H7ZG-TviX-OTg2EoT0hUf4GfV" \
//   -d "grant_type=client_credentials"
?>


<!-- {"scope":"https://api.paypal.com/v1/payments/.* https://uri.paypal.com/services/payments/refund https://uri.paypal.com/services/applications/webhooks https://uri.paypal.com/services/payments/payment/authcapture https://uri.paypal.com/payments/payouts https://api.paypal.com/v1/vault/credit-card/.* https://uri.paypal.com/services/disputes/read-seller https://uri.paypal.com/services/subscriptions https://uri.paypal.com/services/disputes/read-buyer https://api.paypal.com/v1/vault/credit-card openid https://uri.paypal.com/services/disputes/update-seller https://uri.paypal.com/services/payments/realtimepayment","nonce":"2018-10-28T21:38:56ZlZVJrrTxiuMCJDu2LyOH7tiKIHYfT8erM_EOG6ZSvic","access_token":"A21AAF7AW01qNrU52A9tZ8pz5cSHQn618rLoXmHM-NV0U69D-71Y0UgvodSbEu76qoouupDka7A7qdaFBpHclExl9zP8kRXGw","token_type":"Bearer","app_id":"APP-80W284485P519543T","expires_in":32051} -->