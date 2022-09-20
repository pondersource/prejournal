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
TIMELD_TIMESHEET=fedb/fedt
PREJOURNAL_USERNAME=michiel (username at Prejournal instance)
PREJOURNAL_PASSWORD=...

Then run: php src/cli-single.php push-to-timeld
*/

function pushToTimeld($context, $command) {
     if($context["adminParty"]) {
        $conn = getDbConn();
        $res = $conn->executeQuery("SELECT m.id, m.timestamp_, m.amount, c1.name as worker, c2.name as project FROM movements m INNER JOIN components c1 ON m.fromcomponent=c1.id INNER JOIN components c2 ON m.tocomponent=c2.id WHERE m.type_='worked'");
        $arr = $res->fetchAllAssociative();
        var_dump($arr);
        $data = array(
            '{"@id":"' . $_SERVER["TIMELD_PROJECT"] . '","@type":"Project"}',
            '{"@id":"' . $_SERVER["TIMELD_TIMESHEET"] . '","project":[{"@id":"' . $_SERVER["TIMELD_PROJECT"] . '"}],"@type":"Timesheet"}',
        );
        date_default_timezone_set('UTC');
        for ($i = 0; $i < count($arr); $i++) {
            $data[] = json_encode([
                "activity" => "Worked",
                "session" => [
                    "@id" =>  $_SERVER["TIMELD_TIMESHEET"]
                ],
                "duration" => intval($arr[$i]["amount"]) * 60,
                "start" => [
                    "@value" => date(DATE_ATOM, strtotime($arr[$i]["timestamp_"])),
                    "@type" => "http://www.w3.org/2001/XMLSchema#dateTime"
                ],
                "@type" => "Entry",
                "vf:provider" => [
                    "@id" => $arr[$i]["worker"]
                ],
                "external" => [
                    "@id" => "http://time.pondersource.com/movement/" . $arr[$i]["id"]
                ]
            ]);
        }

        $result = importTimld(implode("\n", $data));

         var_dump($result);

        if(isset($result["code"])) {
            if($result["code"] === "Forbidden") {
                return ["You have forbidden access you need right username"];
                //exit;
            } else if($result["code"] === "BadRequest") {
                return ["Malformed domain entity"];
            }
        }
       
        if($result  === null) {
    
            return ["The API timeld was import succesfully"];
        }

        var_dump($result["code"]);

     } else {
        return ["This command only works in admin party mode"];
     }
}