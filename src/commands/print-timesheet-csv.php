<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/../database.php');

/*
  E.g.: php src/cli-single.php print-timesheet-csv timesheet
*/
function printTimesheetCsv($context, $command)
{
    if (isset($context["user"])) {
        $remote_system = $command[1];
        $jsondata = getFromMovementAndSync();
        if($remote_system === "timesheet") {
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=test.csv");
            $fp = fopen('php://output', 'w'); // or use php://stdout
            foreach ($jsondata as $row) {
                echo fputcsv($fp, $row);
            }
        }
       
    } else {
        return ["User not found or wrong password"];
    }
}