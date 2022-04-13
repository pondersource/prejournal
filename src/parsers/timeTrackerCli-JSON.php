<?php declare(strict_types=1);

function parseTimeTrackerCliJSON($str) {
    $ret = [];
    $response = json_decode($str);
    //var_dump($response);
    
        array_push($ret, [
            "worker" =>  $_SERVER["PREJOURNAL_USERNAME"],
            "project" => $response->username->status,
            "start" =>  $response->username->log[0],
            "seconds" => strtotime($response->username->log[1]) - strtotime($response->username->log[0])
        ]); 
    //var_dump($ret);
   
    return $ret;
}

