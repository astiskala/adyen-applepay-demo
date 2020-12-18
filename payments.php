<?php

require('log.php');

$merchantAccount = getenv('MERCHANT_ACCOUNT');
$apikey = getenv('CHECKOUT_APIKEY');

if (file_get_contents('php://input') != '') {
    $clientRequest = json_decode(file_get_contents('php://input'), true);
} else {
    $clientRequest = array();
}

$serverRequest = new \stdClass();
$serverRequest->merchantAccount = $merchantAccount;
$serverRequest->amount = new \stdClass();
$serverRequest->amount->currency = "EUR";
$serverRequest->amount->value = 101;
$serverRequest->reference = strtolower(md5(uniqid(rand(), true)));
$serverRequest->shopperInteraction = "Ecommerce";
$serverRequest->recurringProcessingModel = "CardOnFile";
$serverRequest->storePaymentMethod = true;

$url = "https://checkout-test.adyen.com/v66/payments";

$json_data = json_encode(array_merge(json_decode($request, true),json_decode($serverRequest, true)));
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

$json = json_decode($result, true);
if (isset($json["action"])) {
  $paymentData = $json["action"]["paymentData"];
  $tmp = sys_get_temp_dir() . "/paymentData";
  error_log("Persisting paymentData '" . $paymentData . "' to " . $tmp);
  file_put_contents($tmp, $paymentData);
}

echo $result;
