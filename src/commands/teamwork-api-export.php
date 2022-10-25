<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');
require_once(__DIR__ . '/../api/teamwork.php');

//E.g.: php src/cli-single.php teamwork-api-export teamwork
function teamworkApiExport($context, $command) {

     if($context["user"]) {
        $remote_system = $command[1];
        $result = (array) exportTimeTeamWork();
       
    
        $array = json_decode(json_encode($result), true);
    
        $type = 'worked';
        foreach($array as $teamwork) {
            if (is_array($teamwork) || is_object($teamwork))
            {
                    usort($teamwork, function($a, $b) {
                        return strtotime($a["createdAt"]) < strtotime($b["createdAt"]) ? -1 : 1;
                    });
                    $last_information  = last($teamwork);
                    
                    $timestamp = strtotime($last_information["updated-date"]);
                    $worked_hours = $last_information["hours"] . ":" .$last_information["minutes"];
                    $worked_name = $last_information["person-first-name"] . " " . $last_information["person-last-name"];
                    $movementId = intval(createMovement($context, [
                        "create-movement",
                        $context["user"]["id"],
                        $type,
                        strval(getComponentId($worked_name)),
                        strval(getComponentId($last_information["project-name"])),
                        $timestamp,
                        $worked_hours
                    ])[0]);
    
                    $statementId = intval(createSync($context, [
                        "movement",
                        $movementId,
                        $last_information["id"],
                        $remote_system,
                        json_encode($result)
                    ])[0]);
                    $result = getMovementAndSync($movementId, $statementId);
                    return $result;
            }
        }
     } else {
        return ["User not found or wrong password"];
    }
}