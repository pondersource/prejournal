<?php

declare(strict_types=1);

function parseTimeCSV($str)
{
    $ret = [];
    $lines = explode("\n", $str);
    if ($lines[0] !== "User name,Project name,Issue,Time,Start date,End date") {
        throw new Error("Unexpected headers line in time-CSV file!");
    }
    for ($i = 1; $i < count($lines); $i++) {
        $cells = explode(",", $lines[$i]);

        if (count($cells) == 6) {
            array_push($ret, [
                "worker" => $cells[0],
                "project" => $cells[1],
                "start" => strtotime($cells[4]),
                "seconds" => strtotime($cells[5]) - strtotime($cells[4])
            ]);
        }
    }
    return $ret;
}
