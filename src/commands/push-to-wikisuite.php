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
TIMELD_TIMESHEET=fedb/from-pounder-source
PREJOURNAL_ADMIN_PARTY=true
# PREJOURNAL_USERNAME=michiel (username at Prejournal instance)
# PREJOURNAL_PASSWORD=...

Then run: php src/cli-single.php push-to-timeld http://time.pondersource.com/michiel
Note that in admin party you can run this for any worker, doesn't have to match
a specific prejournal user.
*/

// $worker e.g. "http://time.pondersource.com/ismoil"
// $arr an array of associative hashes, each with fields:
// $arr[$i]["amount"] e.g. 8
// $arr[$i]["timestamp_"] e.g. "2022-03-25 00:00:00"
// $arr[$i]["id"] e.g. 123
// $arr[$i]["description"] e.g. "Testing diff propagation" 

// URI,User,Project,Task,Description,"Start Time","End Time",Date,Duration,"Minutes (Calculated)","Hours (Calculated)"
// your-external-uri,victor,federated-timesheets,,"documenting API examples","Friday August 19, 2022 13:30:00 UTC","Friday August 19, 2022 14:00:00 UTC",2022-08-19,"30 minutes",30,0.5
// your-external-uri,victor,federated-timesheets,,"documenting API examples","Friday August 19, 2022 13:30:00 UTC","Friday August 19, 2022 14:00:00 UTC",2022-08-19,"30 minutes",30,0.5

function pushMovementsToTabular($worker, $arr)
{
    if (!isset($_SERVER["TIMELD_PROJECT"])) {
        // echo "TIMELD_PROJECT not set!";
        return;
    }
    $project = $_SERVER["TIMELD_PROJECT"]; // e.g. "fedb/fedt"
    $timesheet = $_SERVER["TIMELD_TIMESHEET"]; // e.g. "fedb/from-pounder-source"

    var_dump([
        "Push movement to wikisuite!",
        $worker,
        $project,
        $timesheet
    ]);
    var_dump($arr);
    $data = array(
        'URI,User,Project,Task,Description,"Start Time","End Time",Date,Duration,"Minutes (Calculated)","Hours (Calculated)"'
    );
    date_default_timezone_set('UTC');
    for ($i = 0; $i < count($arr); $i++) {
        $data[] = '"' . implode('","', [
            "http://time.pondersource.com/movement/" . $arr[$i]["id"], // URI
            $worker, // User
            "federated-timesheets", // Project
            "", // Task 
            $arr[$i]["description"], // Description
            "", // "Start Time"
            "", // "End Time"
            date(DATE_ATOM, strtotime($arr[$i]["timestamp_"])), // Date
            intval($arr[$i]["amount"]) * 60 . " minutes", // Duration
            intval($arr[$i]["amount"]) * 60, // "Minutes (Calculated)"
            intval($arr[$i]["amount"]), // "Hours (Calculated)"'
        ]) . '"';
    }

    $result = importWiki(implode("\n", $data));

    var_dump($result);

    if (isset($result["code"])) {
        if ($result["code"] === "Forbidden") {
            return ["You have forbidden access you need right username"];
        //exit;
        } elseif ($result["code"] === "BadRequest") {
            return ["Malformed domain entity"];
        }
    }

    if ($result  === null) {
        return ["The API timeld was import succesfully"];
    }

}

function pushToWikisuite($context, $command) {
     if($context["adminParty"]) {
        $conn = getDbConn();
        $worker = $command[1];
        $params = [
            "worker" => getComponentId($worker)
        ];
        var_dump($params);
        $query = "SELECT m.id, m.timestamp_, m.amount, c2.name as project, s.description " .
        "FROM movements m INNER JOIN components c2 ON m.tocomponent=c2.id " .
        "INNER JOIN statements s ON s.movementid = m.id " .
        "WHERE m.type_='worked' and m.fromcomponent = :worker";
        var_dump($query);
        $res = $conn->executeQuery($query, $params);
        $arr = $res->fetchAllAssociative();
        var_dump($arr);
        return pushMovementsToTabular($worker, $arr);
     } else {
        return ["This command only works in admin party mode"];
     }
}