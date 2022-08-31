<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/callGetEndpoint.php');
require_once(__DIR__ . '/callEndpoint.php');


function importTimld($data = array()) {
    $url = $_SERVER["TIMELD_HOST"] . '/import';


    $headers = array(
        "Accept: Content-Type: application/x-ndjson",
        'Authorization: Basic '. base64_encode($_SERVER["TIMELD_USERNAME"].':'.$_SERVER["TIMELD_PASSWORD"]),
     );

    // $data = [];
  
    $resp = callEndpoint($headers, $data, $url);

   
    return $resp;
}

function exportTimeLd() {
    $url = $_SERVER["TIMELD_HOST"] . "/rpt/" . $_SERVER["TIMELD_USERNAME"] . "/own/" . $_SERVER["TIMELD_USERNAME"];
    

    $headers = array(
        "Accept: Content-Type: application/x-ndjson",
        'Authorization: Basic '. base64_encode($_SERVER["TIMELD_USERNAME"].':'.$_SERVER["TIMELD_PASSWORD"]),
     );

     $resp = callGetEndpoint($headers, $url);

    return $resp;
}