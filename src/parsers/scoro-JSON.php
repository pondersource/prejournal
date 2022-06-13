<?php

declare(strict_types=1);

function parseScoroJSON($str)
{
    $ret = [];
    $response = json_decode($str);

    for ($i = 0; $i < count($response->data); $i++) {
        array_push($ret, [
            "worker" => $_SERVER["PREJOURNAL_USERNAME"],
            "project" => $response->data[$i]->activity_id,
            "start" => strtotime($response->data[$i]->start_datetime),
            "seconds" => $response->data[$i]->duration
        ]);
    }
    return $ret;
}
