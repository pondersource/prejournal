<?php declare(strict_types=1);

function parseTimeTrackerCliJSON($str) {
    $ret = [];
    $response = json_decode($str);
    //var_dump($response);
    
        array_push($ret, [
            "worker" =>  $_SERVER["PREJOURNAL_USERNAME"],
            "project" => $response->ismoil->status,
            "start" =>  $response->ismoil->log[0],
            "seconds" => strtotime($response->ismoil->log[1]) - strtotime($response->ismoil->log[0])
        ]); 
    //var_dump($ret);
   
    return $ret;
}

