<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');
require_once(__DIR__ . '/../api/timeld.php');
/*
In .env set:
TIMELD_HOST=https://timeld.org/api
TIMELD_USERNAME=michielbdejong (username at Timeld instance)
TIMELD_PASSWORD=...
TIMELD_PROJECT=fedb/fedt
TIMELD_TIMESHEET=pondersource/federated-timesheet
PREJOURNAL_USERNAME=michiel (username at Prejournal instance)
PREJOURNAL_PASSWORD=...
                                                      worker                           project
Then run: php src/cli-single.php push-to-timeld http://time.pondersource.com/michiel federated-timesheets

{"@id":"fedb/fedt","@type":"Project"}
{"@id":"angus/ts-agm-2022-08-08","project":{"@id":"fedb/fedt"},"@type":"Timesheet"}
```
*/

function pushToTimeld($context, $command) {
     if($context["adminParty"]) {
        $conn = getDbConn();
        $worker = $command[1];
        $project = $command[2];
        $params = [
            "worker" => getComponentId($worker),
            "project" => getComponentId($project)
        ];
        $res = $conn->executeQuery("SELECT id, timestamp_, amount FROM movements WHERE fromcomponent=:worker and tocomponent=:project and type_='worked'", $params);
        $arr = $res->fetchAllAssociative();
        var_dump($arr);
        $data = array(
            '{"@id":"' . $_SERVER["TIMELD_PROJECT"] . '","@type":"Project"}',
            '{"@id":"' . $_SERVER["TIMELD_TIMESHEET"] . '","project":{"@id":"' . $_SERVER["TIMELD_PROJECT"] . '"},"@type":"Timesheet"}',
        );
        date_default_timezone_set('UTC');
        for ($i = 0; $i < count($arr); $i++) {
            $data[] = json_encode([
                "activity" => "Worked",
                "duration" => intval($arr[$i]["amount"]) * 60,
                "start" => [
                    "@value" => date(DATE_ATOM, strtotime($arr[$i]["timestamp_"])),
                    "@type" => "http://www.w3.org/2001/XMLSchema#dateTime"
                ],
                "@type" => "Entry",
                "vf:provider" => [
                    "@id" => $worker
                ],
                "external" => [
                    "@id" => "http://time.pondersource.com/movement/" . $arr[$i]["id"]
                ]
            ]);
        }
        var_dump($data);

        // $result = importTimld($json);

         //var_dump($result);

        // if(isset($result["code"])) {
        //     if($result["code"] === "Forbidden") {
        //         return ["You have forbidden access you need right username"];
        //         //exit;
        //     } else if($result["code"] === "BadRequest") {
        //         return ["Malformed domain entity"];
        //     }
        // }
       
        // if($result  === null) {
    
        //     return ["The API timeld was import succesfully"];
        // }

        //var_dump($result["code"]);

     } else {
        return ["This command only works in admin party mode"];
     }
}