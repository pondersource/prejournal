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
PREJOURNAL_ADMIN_PARTY=true
# PREJOURNAL_USERNAME=michiel (username at Prejournal instance)
# PREJOURNAL_PASSWORD=...

Then run: php src/cli-single.php push-to-timeld http://time.pondersource.com/michiel
Note that in admin party you can run this for any worker, doesn't have to match
a specific prejournal user.
*/

function pushToTimeld($context, $command) {
     if($context["adminParty"]) {
        $conn = getDbConn();
        $worker = $command[1];
        $params = [
            "worker" => getComponentId($worker)
        ];
        var_dump($params);
        $query = "SELECT m.id, m.timestamp_, m.amount, c2.name as project " .
        "FROM movements m INNER JOIN components c2 ON m.tocomponent=c2.id " .
        "WHERE m.type_='worked' and m.fromcomponent = :worker";
        var_dump($query);
        $res = $conn->executeQuery($query, $params);
        $arr = $res->fetchAllAssociative();
        var_dump($arr);
        $timesheet = $_SERVER["TIMELD_PROJECT"] . "-" . str_replace(["/", ":", ".", "--"], ["-", "", "-", "-"], $worker);
        $data = array(
            '{"@id":"' . $_SERVER["TIMELD_PROJECT"] . '","@type":"Project"}',
            '{"@id":"' . $timesheet . '","project":[{"@id":"' . $_SERVER["TIMELD_PROJECT"] . '"}],"@type":"Timesheet"}',
        );
        date_default_timezone_set('UTC');
        for ($i = 0; $i < count($arr); $i++) {
            $data[] = json_encode([
                "activity" => "Worked",
                "session" => [
                    "@id" =>  $timesheet
                ],
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
            ], JSON_UNESCAPED_SLASHES);
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