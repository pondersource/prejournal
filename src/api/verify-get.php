<?php declare(strict_types=1);
require_once('../platform.php');

    $CLIENT_ID = $_SERVER["VERIFY_CLIENT_ID"];
   
    $ENVIRONMENT_URL = $_SERVER["VERIFY_ENVIROMENT_URL"];

    $username = $_SERVER["VERIFY_USERNAME"];
    $api_key = $_SERVER["VERIFY_API_KEY"];

    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json',
        "AUTHORIZATION: apikey $username:$api_key",
        "CLIENT-ID: $CLIENT_ID"
    );

    $url = "{$ENVIRONMENT_URL}api/v7/partner/documents";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $json_response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    //print("json_response = " . $json_response);

    $json_result = json_encode(json_decode($json_response), JSON_PRETTY_PRINT);
    echo '<pre>' . $json_result . '</pre>';

    //var_dump(__DIR__);
    file_put_contents("api_responses/verifyInvoice-JSON.json", $json_result);
   

    //$file = fopen(__DIR__ . '/news.json','w');
?>