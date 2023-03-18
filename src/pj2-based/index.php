<?php

require_once(__DIR__ . '/../utils.php');

const ANALYSIS_FIRST_WEEK = "202101";
const ANALYSIS_LAST_WEEK = "202310";
const DEFAULT_HOURS_PER_WEEK = 40;

function loadSources($folderPath) {
  $ret = [];
  // ignore the warning, we'll check for return value false below.
  set_error_handler(function() { /* ignore errors */ });
  $entries = scandir($folderPath);
  restore_error_handler();
  if ($entries == false) {
    echo "Folder not found: '$folderPath'\n";
    exit();
  }

  for ($i = 0; $i < count($entries); $i++) {
    if (($entries[$i] == ".") ||  ($entries[$i] == "..")) {
      continue;
    }
    if (!str_ends_with($entries[$i], ".pj2")) {
      echo "Filename '" . $entries[$i] . "' in '" . $folderPath . "' does not have a .pj2 extension .\n";
      exit(1);
    }
    $contents = file_get_contents($folderPath . $entries[$i]);
    try {
      $pj2Entries =  json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
      // var_dump($pj2Entries);
     debug("Contents of " . $folderPath . $entries[$i] . " parsed as " . count($pj2Entries) . " PJ2 entries.\n");

    } catch (Exception $e) {
      echo "Contents of " . $folderPath . $entries[$i] . " is not JSON.\n";
      exit(1);
    }
    $ret = array_merge($ret, $pj2Entries);
  }
  return $ret;
}

$records = [];

function ensureDataStructure($organization, $worker, $week) {
  global $timesheets;
  if (!isset($timesheets[$organization])) {
    $timesheets[$organization] = [];
  }
  if (!isset($timesheets[$organization][$worker])) {
    $timesheets[$organization][$worker] = [];
  }
  if (!isset($timesheets[$organization][$worker][$week])) {
    $timesheets[$organization][$worker][$week] = [
      "hoursWorked" => 0,
      "hoursContracted" => 0
    ];
  }
}
function weekExists($week) {
  $numWeeks = [
    "2020" => 52,
    "2021" => 52,
    "2022" => 52,
    "2023" => 52,
    "2024" => 52,
    "2025" => 52,
    "2026" => 52
  ];
  $year = substr($week, 0, 4);
  $woy = substr($week, 4, 2);
  return (intval($woy) <= $numWeeks[$year]);
}

function setHoursContracted($organization, $worker, $fromWeek, $toWeek, $hours) {
  global $timesheets;
  for ($week = $fromWeek; $week <= $toWeek; $week++) {
    if (weekExists($week)) {
      ensureDataStructure($organization, $worker, $week);
      $timesheets[$organization][$worker][$week]["hoursContracted"] = $hours;
      debug("Organization $organization contracted  $worker for $hours hours in week of " . weekOfYearToDateTime($week) . "\n");
    }
  }
}
function checkHoursPerWeek($entries) {
  global $timesheets;
  for ($i = 0; $i < count($entries); $i++) {
    if ($entries[$i]["type"] == "worked") {
      $organization = $entries[$i]["organization"];
      $worker = $entries[$i]["worker"];
      $week = dateTimeToWeekOfYear($entries[$i]["date"]);
      ensureDataStructure($organization, $worker, $week);
      $timesheets[$organization][$worker][$week]["hoursWorked"] += $entries[$i]["hours"];

      // $fullProjectId = $entries[$i]["organization"] . ":" . $entries[$i]["project"];
      // if (!isset($projects[$fullProjectId])) {
      //   $projects[$fullProjectId] = 0;
      // }
      // $projects[$fullProjectId] += $entries[$i]["hours"];
    } else if ($entries[$i]["type"] == "contract") {
      if (isset(($entries[$i]["from"]))) {
        $fromWeek = dateTimeToWeekOfYear(($entries[$i]["from"]));
      } else {
        $fromWeek = ANALYSIS_FIRST_WEEK;
      }
      if (isset(($entries[$i]["to"]))) {
        $toWeek = dateTimeToWeekOfYear(($entries[$i]["to"]));
      } else {
        $toWeek = ANALYSIS_LAST_WEEK;
      }
      if (isset(($entries[$i]["hours"]))) {
        $hours = $entries[$i]["hours"];
      } else {
        $hours = DEFAULT_HOURS_PER_WEEK;
      }
      
      setHoursContracted($entries[$i]["organization"], $entries[$i]["worker"], $fromWeek, $toWeek, $hours);
    }
  }
  foreach($timesheets as $organization => $workers) {
    foreach($workers as $worker => $weeks) {
      foreach ($weeks as $week => $data) {
        $hours = $data["hoursWorked"];
        $contractHours = $data["hoursContracted"];
        if ($hours != $contractHours) {
          debug("In the week of " . weekOfYearToDateTime($week) . ", $worker wrote $hours hours for $organization instead of $contractHours!\n", LEVEL_OUTPUT);
        } else {
          debug("In the week of " . weekOfYearToDateTime($week) . ", $worker wrote $hours hours for $organization which matches $contractHours!\n");
        }
      }
    }
  }
}

if (count($_SERVER['argv']) < 3) {
  echo "Not enough arguments. Usage e.g.:\nphp ./src/pj-based/index.php validate-working-hours ../../pondersource-books/stichting/source-docs/\n";
  exit();
}
$cmd = $_SERVER['argv'][1];
$folderPath = $_SERVER['argv'][2];

if ($cmd !== "validate-working-hours") {
  echo "Unknown command: '$cmd'\n";
  exit();
}

$pj2Entries = loadSources($folderPath);

checkHoursPerWeek($pj2Entries);