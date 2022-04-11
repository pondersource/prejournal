<?php declare(strict_types=1);

function parseTimeTrackerJSON($str) {
    $ret = [];
    $response = json_decode($str);
    
    for($i  = 0; $i < count($response);$i++) {
        array_push($ret, [
            "worker" => $response[$i]->userUid,
            "project" => $response[$i]->project,
            "start" => $response[$i]->time,
            "seconds" => strtotime($response[$i]->totalDuration)
        ]);
    } 
    //var_dump($ret);
   
    return $ret;
}
