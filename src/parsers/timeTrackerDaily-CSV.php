<?php

declare(strict_types=1);

function parseTimeTrackerDailyCSV($str)
{
    $ret = [];
    $lines = explode("\n", $str);
    //var_dump($lines);
    if ($lines[0] !== "Category,Worker,Start Date,Start Time,End Date,End Time,Total Hours,Status") {
        throw new Error("Unexpected headers line in timeBro-CSV file!");
    }
    for ($i = 1; $i < count($lines); $i++) {
        $cells = explode(",", $lines[$i]);

        //var_dump($cells);
        if (count($cells) == 8) {
            array_push($ret, [
          "worker" => trim($cells[1]),
          "project" => $cells[0],
          "start" => strtotime($cells[2]),
          "seconds" => strtotime($cells[4]) - strtotime($cells[2])
        ]);
        }
    }
    return $ret;
}
