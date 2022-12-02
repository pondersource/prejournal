<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/callGetEndpoint.php');
require_once(__DIR__ . '/callEndpoint.php');

function fetchTabularId()
{
    if (!isset($_SERVER["WIKI_HOST"])) {
        throw new Error("Please set WIKI_HOST env var");
    }
    if (!isset($_SERVER["WIKI_TOKEN"])) {
        throw new Error("Please set WIKI_TOKEN env var");
    }
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




// curl command that works:
// curl -X 'POST' '$WIKI_HOST/6/import' \
//   -H 'accept: application/json' \
//   -H 'Authorization: Bearer $WIKI_TOKEN' \
//   -H 'Content-Type: multipart/form-data' \
//   -F 'file=@test_import.csv;type=text/csv'

// with this test_import.csv file:
// URI,User,Project,Task,Description,"Start Time","End Time",Date,Duration,"Minutes (Calculated)","Hours (Calculated)"
// "http://time.pondersource.com/movement/491","michiel","federated-timesheets","","testing milestone 1b","","","2022-09-23T00:00:00+00:00","60 minutes","60","1"



function importWiki($data)
{

    $result = $_SERVER["WIKI_TABULAR_ID"]; // fetchTabularId();
    $url = $_SERVER["WIKI_HOST"] . '/' .$result . '/import';
    $headers = array(
        "Accept: application/json",
        "Authorization: Bearer " . $_SERVER['WIKI_TOKEN'],
        "Content-Type: multipart/form-data; boundary=------------------------4845dba1f62f55ae"
    );

    $formData = "--------------------------4845dba1f62f55ae\n" .
        "Content-Disposition: form-data; name=\"file\"; filename=\"upload.csv\"\n" .
        "Content-Type: text/csv\n" .
        "\n" .
        $data . 
        "--------------------------4845dba1f62f55ae--\n";
    
    $resp = callEndpoint($headers, $formData, $url);
    if (isset($resp["code"])) {
        if ($resp["code"] === 403) {
            echo $resp["errortitle"] ." ";
            exit;
        }
    }
    return [ json_encode($resp)];
}

