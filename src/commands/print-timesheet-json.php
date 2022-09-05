<?php

declare(strict_types=1);
require_once(__DIR__ . '/helpers/dataAccessObject.php');
/*                                                    min  max  projectName
    E.g.: php src/cli-single.php print-timesheet-json 0    100   wiki

    Print All information without using project name optional
     php src/cli-single.php print-timesheet-json 0    100
*/
function printTimesheetJson($context, $command)
{
    // var_dump($context);
    if (isset($context["openMode"]) && $context["openMode"] == "true") {
        // var_dump($command);
    //if (isset($context["user"])) {
        //var_dump($command[3]);
        
        $min_id = 0;
        if (count($command) > 2) {
            $min_id = intval($command[1]);
        }
        $max_id = 1000000;
        if (count($command) > 3) {
            $max_id = intval($command[2]);
        }

        $project_name = $command[3] ?? null;

        $jsondata = getFromMovementAndSync($project_name, $min_id, $max_id);

        $result = [ json_encode($jsondata, JSON_PRETTY_PRINT) ];

        return $result;
    } else {
        //return ["User not found or wrong password"];
       return ["This command only works if the server is running in open mode! See https://github.com/pondersource/prejournal/issues/133"];
    }
}
