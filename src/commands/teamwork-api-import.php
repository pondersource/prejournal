<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');
require_once(__DIR__ . '/../api/teamwork.php');

//                                                              date       time   hours   minutes
//E.g.: php src/cli-single.php teamwork-api-import description "20221020" "05:40" 7       50
function teamworkApiImport($context, $command) {
    if($context["user"]) {
        $description = $command[1];
        $date = $command[2];
        $time = $command[3];
        $hours = $command[4];
        $minutes = $command[5];
       
        $data = [
            "description" => $description,
            "date" => $date,
            "time" => $time,
            "hours" => $hours,
            "minutes" => $minutes
        ]; 
        $result = importTimeTeamWork($data);
        if($result["STATUS"] === "OK") {
            return ["Your time entry is successfully pushed your timeLogId is " . $result["timeLogId"]];
        }
    } else {
        return ["User not found or wrong password"];
    }
}