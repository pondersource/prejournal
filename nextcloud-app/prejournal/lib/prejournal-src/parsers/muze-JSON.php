<?php

declare(strict_types=1);

// Example:
// {
//     "klantdesc" : "Some Customer",
//     "id" : "12345",
//     "projectdesc" : "Some Project",
//     "minuten" : "60",
//     "persname" : "Yvo",
//     "time" : "2021-12-13 00:00:00",
//     "desc" : "Fixed the last nasty bugs"
// }


function parseMuzeJSON($str)
{
    $ret = [];
    $response = json_decode($str);

    for ($i = 0; $i < count($response); $i++) {
        array_push($ret, [
            "worker" => strtolower($response[$i]->persname) . "@muze.nl",
            "project" => $response[$i]->klantdesc . ":" . $response[$i]->projectdesc,
            "start" => strtotime($response[$i]->time),
            "seconds" => $response[$i]->minuten * 60
        ]);
    }
    return $ret;
}
