<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils.php');
 
function parseTimeldNDJSON($str)
{
    $ret = [];

    // See http://ndjson.org/

    $lines =  explode("\n", $str);
    debug($lines);

    $projectName = "Project Unknown";
    for ($i = 0; $i < count($lines); $i++) {
        $cells = json_decode($lines[$i], true);
        // e.g.
        // {
        //   "@id":"tes3V6N73CboUyHzvHY7TE/2",
        //   "activity":"FedT - test timeld features and log Issues",
        //   "duration":45,
        //   "session":{
            //     "@id":"tes3V6N73CboUyHzvHY7TE"
        //   },
        //   "start":{
            //     "@value":"2022-08-12T16:15:00.000Z",
            //     "@type":"http://www.w3.org/2001/XMLSchema#dateTime"
        //   },
        //   "@type":"Entry",
        //   "vf:provider":{
            //     "@id":"http://timeld.org/angus"
        //   }
        // }

        debug($cells);
        if ($cells["@type"] == "Project") {
            $projectName = $cells["@id"];
        } else if ($cells["@type"] == "Entry") {
                array_push($ret, [
                "worker" => $cells["vf:provider"]["@id"],
                "start" => strtotime($cells["start"]["@value"]),
                "seconds" => $cells["duration"] * 60,
                "description" => $cells["activity"],
                "project" => $projectName
            ]);
        } else {
            debug("Line is not a Project or Entry");
            debug($cells);
        }
    }
    return $ret;
}
