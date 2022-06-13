<?php

declare(strict_types=1);

function parseTimetipJSON($str)
{
    $ret = [];
    $lines = json_decode($str);
    //var_dump($lines->dates[0]->date);

    for ($i = 0; $i < count($lines->dates); $i++) {
        array_push($ret, [
        "worker" => $_SERVER["PREJOURNAL_USERNAME"],
        "project" => $lines->dates[$i]->last->reason,
        "start" => strtotime($lines->dates[$i]->date),
        "seconds" => $lines->dates[$i]->last->duration
    ]);
    }
    return $ret;
}
