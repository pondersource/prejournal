<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');

function parseSaveMyTimeCSV($str)
{
    $ret = [];
    $lines = explode("\n", $str);
    if ($lines[0] !== "activityName,activityCategoryName,activityStartDate [ms],activityStartDate,activityEndDate [ms],activityEndDate,activityDuration [ms],activityDuration") {
        throw new Error("Invalid CSV headers for the 'Save my Time' Timesheet file.");
    }
    #echo "Please specify the project name you worked on? ";
    #$project_name = rtrim(fgets(STDIN));
    $project_name = 'default-project';
    for ($i = 1; $i < count($lines); $i++) {
        $cells = explode(",", $lines[$i]);

        if (count($cells) == 8) {
            array_push($ret, [
                "worker" => $_SERVER['PREJOURNAL_USERNAME'],
                "project" => $project_name,
                "start" => strtotime($cells[3]),
                "seconds" => strtotime($cells[5]) - strtotime($cells[3])
            ]);
        }
    }
    return $ret;
}
