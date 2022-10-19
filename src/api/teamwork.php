<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
//require_once(__DIR__. '/../../loadenv.php');
require_once(__DIR__ . '/callGetEndpoint.php');
require_once(__DIR__ . '/callEndpoint.php');


function exportTimeTeamWork() {
    $url = $_SERVER["TEAMWORK_HOST"] . '/time_entries.json';


    $headers = array(
        "Content-Type: application/json",
        'Authorization: Basic '. base64_encode($_SERVER["TEAMWORK_USERNAME"].':'.$_SERVER["TEAMWORK_PASSWORD"]),
     );

     $resp = callGetEndpoint($headers, $url);
     return $resp;
}