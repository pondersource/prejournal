<?php

declare(strict_types=1);

function parseTimeDoctorCSV($str)
{
    $ret = [];
    $lines = explode("\n", $str);
    if ($lines[0] !== "Name,Email,Employee ID,User groups,Total time,Manual time,Manual time %,Mobile time,Mobile time %") {
        throw new Error("Unexpected headers line in timeBro-CSV file!");
    }
    for ($i = 1; $i < count($lines); $i++) {
        $cells = explode(",", $lines[$i]);

        //var_dump($cells);
        if (count($cells) == 7) {
            array_push($ret, [
          "worker" => trim($cells[1]),
          "project" => $cells[3],
          "start" => strtotime($cells[4]),
          "seconds" => strtotime($cells[5]) - strtotime($cells[4])
        ]);
        }
    }
    return $ret;
}
