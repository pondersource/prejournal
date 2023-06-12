<?php

require_once(__DIR__ . '/Journal.php');
require_once(__DIR__ . '/Timesheet.php');

if (count($_SERVER['argv']) < 3) {
  echo "Not enough arguments. Usage e.g.:\nphp ./src/pj2-based/index.php validate-working-hours ../../pondersource-books/stichting/build/\n";
  exit();
}
$cmd = $_SERVER['argv'][1];
$folderPath = $_SERVER['argv'][2];


if ($cmd === "validate-working-hours") {
  $timesheet = new Timesheet();
  $timesheet->loadSources($folderPath);
  $timesheet->checkHoursPerWeek();
} else if ($cmd === "report-costs") {
  $timesheet = new Timesheet();
  $timesheet->loadSources($folderPath);
  $timesheet->reportCosts();
} else if ($cmd === "pta") {
  $journal = new Journal();
  $journal->loadSources($folderPath);
  $journal->toPta();
} else {
  echo "Unknown command: '$cmd'\n";
  exit();
}
