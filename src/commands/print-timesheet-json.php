<?php

declare(strict_types=1);
  require_once(__DIR__ . '/helpers/dataAccessObject.php');
  /*
  E.g.: php src/cli-single.php print-timesheet-json 270e0144-8085-4366-9f7c-8aae59a3f11e wiki 0 100
*/
function printTimesheetJson($context, $command)
{
    if (isset($context["user"])) {

        $uuid = $command[1];

        if($context["user"]["uuid"] === $uuid) {
            $project_name = $command[2];
            $min_id = intval($command[3]);
            $max_id = intval($command[4]);
    
    
            $jsondata = getFromMovementAndSync($context["user"]["id"], $project_name, $min_id, $max_id);
    
            $result = json_encode($jsondata, JSON_PRETTY_PRINT);
            echo $result;
        }
       

      
    } else {
        return ["User not found or wrong password"];
    }
}
