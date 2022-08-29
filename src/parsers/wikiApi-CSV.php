<?php

declare(strict_types=1);

require_once(__DIR__ . '/../utils.php');

//  0   1     2      3     4              5         6         7    8            9                     10
// URI,User,Project,Task,Description,"Start Time","End Time",Date,Duration,"Minutes (Calculated)","Hours (Calculated)"
// https://timesheet.dev3.evoludata.com/api/trackers/2/items/45,
//      victor,
//           "Federated timesheets",,
//                        "Meeting with Michiel, George and others to discuss initial steps for defining the data model of the federated timesheets and other complexities",
//                                       "Thursday March 10, 2022 00:00:00 UTC",,
//                                                                2022-03-10,
//                                                                 "1 hour, 9 minutes",
//                                                                                69,
//                                                                                                         1.15

function parseWikiApiCSV($str)
{
    debug("HELLO!");
    $EXPECTED_HEADER_LINE = 'URI,User,Project,Task,Description,"Start Time","End Time",Date,Duration,"Minutes (Calculated)","Hours (Calculated)"';
    $ret = [];
    // return $ret;
    $lines = explode("\n", $str);
    // var_dump($lines);
    if ($lines[0] !== $EXPECTED_HEADER_LINE) {
        debug(json_encode($lines[0]));
        debug(json_encode($EXPECTED_HEADER_LINE));
        debug('Header line is not what we were expecting!');
        throw new Error('Header line is not what we were expecting!');
    }
    for ($i = 1; $i < count($lines); $i++) {
        $entry = str_getcsv($lines[$i]);
        if (count($entry) != 11) {
            debug($entry);
            debug("ignoring incomplete line");
            continue;
        }
        array_push($ret, [
            "worker" => $entry[1], // e.g. "victor"
            "project" => $entry[2], // e.g. "Federated timesheets"
            "start" => strtotime($entry[5]), // e.g. strtotime("Thursday March 10, 2022 00:00:00 UTC")
            "seconds" => intval($entry[9]) * 60, // e.g. 69 * 60
            "description" => $entry[4], // e.g. "Meeting with Michiel, George and others to discuss ..."
            "sourceId" => $entry[0] // e.g. "https://timesheet.dev3.evoludata.com/api/trackers/2/items/45"
        ]);
    }
    return $ret;
}
