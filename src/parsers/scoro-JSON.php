<?php declare(strict_types=1);

function parseScoroJSON($str) {
    $ret = [];
    $response = json_decode($str);

    array_push($ret, [
        "worker" => $_SERVER["PREJOURNAL_USERNAME"],
        "project" => $response->data->activity_id,
        "start" => strtotime($response->data->start_datetime),
        "seconds" => $response->data->duration
    ]);
    return $ret;
}