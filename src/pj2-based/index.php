<?php

require_once(__DIR__ . '/../utils.php');

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

function getContractHours($worker, $week) {
  return 40;
}

function checkHoursPerWeek($entries) {
  $workers = [];
  for ($i = 0; $i < count($entries); $i++) {
    if ($entries[$i]["type"] == "worked") {
      $week = dateTimeToWeekOfYear($entries[$i]["date"]);
      if (!isset($workers[$entries[$i]["worker"]])) {
        $workers[$entries[$i]["worker"]] = [];
      }
      if (!isset($workers[$entries[$i]["worker"]][$week])) {
        $workers[$entries[$i]["worker"]][$week] = 0;
      }
      $workers[$entries[$i]["worker"]][$week] += $entries[$i]["hours"];
      // $fullProjectId = $entries[$i]["organization"] . ":" . $entries[$i]["project"];
      // if (!isset($projects[$fullProjectId])) {
      //   $projects[$fullProjectId] = 0;
      // }
      // $projects[$fullProjectId] += $entries[$i]["hours"];
    }
  }
  foreach($workers as $worker => $weeks) {
    foreach ($weeks as $week => $hours) {
      $contractHours = getContractHours($worker, $week);
      if ($hours != $contractHours) {
        echo "In week $week, $worker wrote $hours hours instead of $contractHours!\n";
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