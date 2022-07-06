<?php

declare(strict_types=1);
  require_once(__DIR__ . '/helpers/dataAccessObject.php');
  /*
  E.g.: php src/cli-single.php print-timesheet-json wiki 0 100
*/
function printTimesheetJson($context, $command)
{
    if (isset($context["user"])) {
        $project_name = $command[1];
        $min_id = intval($command[2]);
        $max_id = intval($command[3]);
      

        $jsondata = getFromMovementAndSync($context["user"]["id"], $project_name, $min_id, $max_id);
       
        $result = json_encode($jsondata, JSON_PRETTY_PRINT);

        echo $result;
       
    } else {
        return ["User not found or wrong password"];
    }
}

