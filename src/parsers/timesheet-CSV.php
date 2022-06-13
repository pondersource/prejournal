<?php

declare(strict_types=1);

function parseTimesheetCSV($str)
{
    $ret = [];
    $lines = explode("\n", $str);
    //var_dump($lines);
    //exit;

    if ($lines[0] !== "Start,End,Number of hours,Number of hours with percentage,Break,Comment,Overtime,Distance travelled,Compensation,Compensation total  ") {
        throw new Error("Unexpected headers line in timelsheet-CSV file!");
    }
    for ($i = 1; $i < count($lines); $i++) {
        $cells = explode(",", $lines[$i]);
        //var_dump($cells);
        if (count($cells) == 10) {
            array_push($ret, [
        "worker" => $_SERVER["PREJOURNAL_USERNAME"],
        "project" => "test",
        "start" => strtotime($cells[0]),
        "seconds" => strtotime($cells[1]) - strtotime($cells[0])
      ]);
        }
    }
    return $ret;
}
