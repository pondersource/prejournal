<?php

declare(strict_types=1);

function parseWikiApiJSON($str)
{
    $ret = [];
    $lines = json_decode($str);
    //var_dump($lines);

    for ($i = 0; $i < count($lines); $i++) {
        array_push($ret, [
            "worker" => $lines[$i]->tsUser,
            "project" => $lines[$i]->tsProject,
            "start" => strtotime($lines[$i]->tsStartTime),
            "seconds" => $lines[$i]->tsDuration
        ]);
    }
    return $ret;
}
