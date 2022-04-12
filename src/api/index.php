<?php
    $CLIENT_ID = "vrf9VRVOe2I2Kc38RkEj3Bzmgi6kKy12EPHu7yC";
    $ENVIRONMENT_URL = "https://api.veryfi.com/";

    $username = "tt3009117";
    $api_key = "0469cd5a13ede3bfed1590b3b39207f1";

    $document_id = "65474055";

    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json',
        "AUTHORIZATION: apikey $username:$api_key",
        "CLIENT-ID: $CLIENT_ID"
    );

    $url = "{$ENVIRONMENT_URL}api/v7/partner/documents/$document_id/";

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
    file_put_contents("../../tests/fixtures/verifyInvoice-JSONjson", $json_result);
   

    //$file = fopen(__DIR__ . '/news.json','w');
?>