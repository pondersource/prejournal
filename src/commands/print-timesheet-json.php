<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/../database.php');
  /*
  E.g.: php src/cli-single.php print-timesheet-json timesheet
*/
function printTimesheetJson($context, $command)
{
    if (isset($context["user"])) {
        $remote_system = $command[1];
        $jsondata = getFromMovementAndSync();
        if($remote_system === "timesheet") {
            $result = json_decode(json_encode($jsondata, JSON_PRETTY_PRINT), false);

            var_dump($result);
        }
       
    } else {
        return ["User not found or wrong password"];
    }
}

