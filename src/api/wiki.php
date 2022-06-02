<?php

require_once (__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../loadenv.php');
require_once(__DIR__ . '/callGetEndpoint.php');


function fetchTabularId(){
    $url = $_SERVER["WIKI_HOST"];

    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer " .$_SERVER['WIKI_TOKEN'],
     );
     $response = callGetEndpoint($headers, $url);
    
     $newArray = [];
        foreach($response->list as $res) {
        
            array_push($newArray, [
                "tabularId" => $res->tabularId
            ]);
        
        }
        return intval($newArray[0]["tabularId"]);

}

function exportWikiFile() {
    $result = fetchTabularId();

    $url = $_SERVER["WIKI_HOST"] . '/' .$result . '/export';
   

    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer " .$_SERVER['WIKI_TOKEN'],
     );
    
     $resp = callGetEndpoint($headers, $url);
     $json_result = json_encode($resp, JSON_PRETTY_PRINT);
     echo '<pre>' . $json_result . '</pre>';

     file_put_contents("tests/fixtures/wiki-suite-JSON.json", $json_result);
}
?>
