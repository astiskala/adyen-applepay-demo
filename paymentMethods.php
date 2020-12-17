<?php

require('log.php');

$merchantAccount = getenv('MERCHANT_ACCOUNT');
$apikey = getenv('CHECKOUT_APIKEY');

$request = new \stdClass();
$request->merchantAccount = $merchantAccount;
$request->countryCode = "AU";
$request->amount = new \stdClass();
$request->amount->currency = "AUD";
$request->amount->value = 100;
$request->channel = "Web";
$request->shopperLocale = "en-US";
$request->allowedPaymentMethods = [ "applepay" ];

$url = "https://checkout-test.adyen.com/v65/paymentMethods";
$json_data = json_encode($request);
$curlAPICall = curl_init();

$method = "POST";
curl_setopt($curlAPICall, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($curlAPICall, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curlAPICall, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($curlAPICall, CURLOPT_URL, $url);

$headers = array(
    "X-Api-Key: " . $apikey,
    "Content-Type: application/json",
    "Content-Length: " . strlen($json_data)
);

curl_setopt($curlAPICall, CURLOPT_HTTPHEADER, $headers);

logApiCall($method, $url, $headers, $json_data);

$result = curl_exec($curlAPICall);
if ($result === false){
  throw new Exception(curl_error($curlAPICall), curl_errno($curlAPICall));
}

curl_close($curlAPICall);
logApiResponse($result);

echo $result;
