<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/callGetEndpoint.php');
require_once(__DIR__ . '/callEndpoint.php');

function fetchTabularId()
{
    $url = $_SERVER["WIKI_HOST"];

    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer " .$_SERVER['WIKI_TOKEN'],
     );
    $response = callGetEndpoint($headers, $url);

    $newArray = [];
    foreach ($response->list as $res) {
        array_push($newArray, [
                "tabularId" => $res->tabularId
            ]);
    }
    return intval($newArray[0]["tabularId"]);
}

function exportWikiFile()
{
    $result = fetchTabularId();

    $url = $_SERVER["WIKI_HOST"] . '/' .$result . '/export';


    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer " .$_SERVER['WIKI_TOKEN']
     );

    $resp = callGetEndpoint($headers, $url);

    if (isset($resp->code)) {
        if ($resp->code === 403) {
            echo $resp->errortitle ." ";
            exit;
        }
    }
    $json_result = json_encode($resp, JSON_PRETTY_PRINT);

    file_put_contents("tests/fixtures/wiki-suite-JSON.json", $json_result);
    return $resp;
}

function importWikiFile()
{
    $result = fetchTabularId();
    $url = $_SERVER["WIKI_HOST"] . '/' .$result . '/import';
    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer " . $_SERVER['WIKI_TOKEN'],
        "Content-Type: multipart/form-data"
     );
    $txt_curlfile = new \CURLFile('tests/fixtures/wiki-suite-JSON.json', 'application/json', 'tests/fixtures/wiki-suite-JSON.json');
    $data = [
      'file' => $txt_curlfile
     ];

    $resp = callEndpoint($headers, $data, $url);
    if (isset($resp["code"])) {
        if ($resp["code"] === 403) {
            echo $resp["errortitle"] ." ";
            exit;
        }
    }
    return $resp["feedback"];
}
