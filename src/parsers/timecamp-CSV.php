<?php

declare(strict_types=1);

function parseTimecampCSV($str)
{
    $ret = [];
    $lines = explode("\n", $str);
    if ($lines[0] !== "Name,Hours with subtasks,Hours without subtasks") {
        throw new Error("Unexpected headers line in timecamp-CSV file!");
    }
    for ($i = 1; $i < count($lines); $i++) {
        $cells = explode(",", $lines[$i]);

        if (count($cells) == 3) {
            array_push($ret, [
          "worker" =>  $_SERVER['PREJOURNAL_USERNAME'],
          "project" => $cells[0],
          "start" => $cells[1],
          "seconds" => $cells[2]
        ]);
        }
    }
    return $ret;
}
