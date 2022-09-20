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
        $entry = json_decode($lines[$i], true);
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

        if (isset($entry["@type"]) && $entry["@type"] == "Project") {
            $projectName = $entry["@id"];
        } else if (isset($entry["@type"]) && $entry["@type"] == "Entry") {
            debug("Entry!");
            if (!isset($entry["duration"])) {
                debug("Setting duration to zero, see https://github.com/pondersource/prejournal/issues/169");
                $entry["duration"] = 0;
            }
            array_push($ret, [
                "worker" => $entry["vf:provider"]["@id"],
                "start" => strtotime($entry["start"]["@value"]),
                "seconds" => $entry["duration"] * 60,
                "description" => $entry["activity"],
                "project" => $projectName,
                "sourceId" => $entry["@id"]
            ]);
        } else {
            debug("Line is not a Project or Entry");
            debug($entry);
        }
    }
    return $ret;
}
