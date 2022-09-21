<?php

$arr = explode("\n", file_get_contents("sources/m-ld.ndjson"));
$timesheetNamespace = $_SERVER["argv"][1];
$outputting = true;
for ($i = 0; $i < count($arr); $i++) {
    if (strlen($arr[$i]) == 0) {
        continue;
    }
    $fields = json_decode($arr[$i], true);
    if ($fields["@type"] == "Timesheet") {
        $timesheet = $fields["@id"];
        $parts = explode("/", $timesheet);
        $outputting = ($parts[0] == $timesheetNamespace);
    }
    if ($outputting) {
        echo $arr[$i] . "\n";
    }
}
