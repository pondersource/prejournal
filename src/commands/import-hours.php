<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../utils.php');
require_once(__DIR__ . '/helpers/createMovement.php');
require_once(__DIR__ . '/../parsers/muze-JSON.php');
require_once(__DIR__ . '/helpers/createStatement.php');
require_once(__DIR__ . '/../parsers/saveMyTime-CSV.php');
require_once(__DIR__ . '/../parsers/scoro-JSON.php');
require_once(__DIR__ . '/../parsers/stratustime-JSON.php');
require_once(__DIR__ . '/../parsers/time-CSV.php');
require_once(__DIR__ . '/../parsers/timeBro-CSV.php');
require_once(__DIR__ . '/../parsers/timecamp-CSV.php');
require_once(__DIR__ . '/../parsers/timeDoctor-CSV.php');
require_once(__DIR__ . '/../parsers/timeld-NDJSON.php');
require_once(__DIR__ . '/../parsers/timely-CSV.php');
require_once(__DIR__ . '/../parsers/timeManager-CSV.php');
require_once(__DIR__ . '/../parsers/timesheet-CSV.php');
require_once(__DIR__ . '/../parsers/timesheetMobile-CSV.php');
require_once(__DIR__ . '/../parsers/timetip-JSON.php');
require_once(__DIR__ . '/../parsers/timetracker-XML.php');
require_once(__DIR__ . '/../parsers/timeTrackerCli-JSON.php');
require_once(__DIR__ . '/../parsers/timeTrackerDaily-CSV.php');
require_once(__DIR__ . '/../parsers/timeTrackerNextcloud-JSON.php');
require_once(__DIR__ . '/../parsers/verifyTime-JSON.php');
require_once(__DIR__ . '/../parsers/wikiApi-JSON.php');
require_once(__DIR__ . '/../parsers/wikiApi-CSV.php');
// E.g.: php src/cli-single.php import-hours time-CSV ./example.csv "2022-03-31 12:00:00"
//                             0             1           2         3


function movementForSourceId($sourceId) {
    $conn = getDbConn();
    $res = $conn->executeQuery("SELECT m.id FROM movements m INNER JOIN statements s ON "
        . "s.movementid = m.id WHERE s.sourcedocumentfilename = :sourceId;", [
            "sourceId" => $sourceId
        ]);
    $arr = $res->fetchAllAssociative();
    // debug($arr);
    if (count($arr) >= 1) {
        return $arr[0]["id"];
    } 
    return NULL;
}

function importHoursInline($context, $format, $contents, $importTime)
{
    $parserFunctions = [
        "muze-JSON" => "parseMuzeJSON",
        "saveMyTime-CSV" => "parseSaveMyTimeCSV",
        "scoro-JSON" => "parseScoroJSON",
        "stratustime-JSON" => "parseStratustimeJSON",
        "time-CSV" => "parseTimeCSV",
        "timeBro-CSV" => "parseTimeBroCSV",
        "timecamp-CSV" =>"parseTimecampCSV",
        "timeDoctor-CSV" => "parseTimeDoctorCSV",
        "timely-CSV" => "parseTimelyCSV",
        "timeManager-CSV" => "parseTimeManagerCSV",
        "timesheet-CSV" => "parseTimesheetCSV",
        "timesheetMobile-CSV" => "parseTimesheetMobileCSV",
        "timetip-JSON" => "parseTimetipJSON",
        "timetracker-XML" => "parseTimeTrackerXML",
        "timeTrackerCli-JSON" => "parseTimeTrackerCliJSON",
        "timeTrackerDaily-CSV" => "parseTimeTrackerDailyCSV",
        "timeTrackerNextcloud-JSON" => "parseTimeTrackerNextcloudJSON",
        "verifyTime-JSON" =>"parseVerifyTimeJSON",
        "wiki-suite-JSON" => "parseWikiApiJSON",
        "wikiApi-CSV" => "parseWikiApiCSV",
        "timeld-NDJSON" => "parseTimeldNDJSON"
    ];


    if (isset($context["user"])) {
        $type_ = "worked";
        $entries = $parserFunctions[$format]($contents);

        for ($i = 0; $i < count($entries); $i++) {
            // debug($entries[$i]);
            $sourceId = (isset($entries[$i]["sourceId"]) ? $entries[$i]["sourceId"] : NULL);
            $existingMovement = movementForSourceId($sourceId);
            if ($existingMovement == NULL) {
                $movementId = intval(createMovement($context, [
                    "create-movement",
                    $context["user"]["id"],
                    $type_,
                    strval(getComponentId($entries[$i]["worker"])),
                    strval(getComponentId($entries[$i]["project"])),
                    $entries[$i]["start"],
                    $entries[$i]["seconds"] / 3600
                ])[0]);
                // debug("Movement created! $movementId");
                $statementId = intval(createStatement($context, [
                    "create-statement",
                    $movementId,
                    $importTime,
                    (isset($entries[$i]["description"]) ? $entries[$i]["description"] : NULL),
                    $format,
                    $sourceId,
                ])[0]);    
            } else {
                updateMovement($context, [
                    $existingMovement,
                    $context["user"]["id"],
                    $type_,
                    strval(getComponentId($entries[$i]["worker"])),
                    strval(getComponentId($entries[$i]["project"])),
                    $entries[$i]["start"],
                    $entries[$i]["seconds"] / 3600
                ]);
            }
        }
        return [strval(count($entries))];
    } else {
        return ["User not found or wrong password"];
    }
}

function importHours($context, $command)
{
    $format = $command[1];
    $fileName = $command[2];
    $contents = file_get_contents($fileName);
    $importTime = strtotime($command[3]);
    return importHoursInline($context, $format, $contents, $importTime);
}
