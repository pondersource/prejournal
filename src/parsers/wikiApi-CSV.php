<?php

declare(strict_types=1);

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

function debug($x) {
    error_log(var_export($x, true));
}

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
        $cells = str_getcsv($lines[$i]);
        if (count($cells) != 11) {
            debug($cells);
            debug("ignoring incomplete line");
            continue;
        }
        array_push($ret, [
            "worker" => $cells[1], // e.g. "victor"
            "project" => $cells[2], // e.g. "Federated timesheets"
            "start" => strtotime($cells[5]), // e.g. strtotime("Thursday March 10, 2022 00:00:00 UTC")
            "seconds" => intval($cells[9]) * 60, // e.g. 69 * 60
            "description" => $cells[4], // e.g. "Meeting with Michiel, George and others to discuss ..."
        ]);
    }
    return $ret;
}
