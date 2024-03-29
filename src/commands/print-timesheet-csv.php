<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');

/*                                                 minNumber  maxNumber   projectName

 Print All information without using project name optional
  E.g.: php src/cli-single.php print-timesheet-csv        1      2        timesheet
*/
function printTimesheetCsv($context, $command)
{
    //if (isset($context["openMode"]) && $context["openMode"] == "true") {
        // var_dump($command);
    if (isset($context["user"])) {
     
        $min_id = 0;
        if (count($command) > 2) {
            $min_id = intval($command[1]);
        }
        $max_id = 1000000;
        if (count($command) > 3) {
            $max_id = intval($command[2]);
        }

        $project_name = $command[3];
        $jsondata = getFromMovementAndSync($project_name, $min_id, $max_id);

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=test.csv");
        $fp = fopen('php://output', 'w'); // or use php://stdout
        foreach ($jsondata as $row) {
            echo fputcsv($fp, $row);
        }
    } else {
        return ["User not found or wrong password"];
    }
}
