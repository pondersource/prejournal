<?php

require_once(__DIR__ . '/../utils.php');

const ANALYSIS_FIRST_WEEK = "202101";
const ANALYSIS_LAST_WEEK = "202310";
const DEFAULT_HOURS_PER_WEEK = 40;

class Timesheet {
  private $entries = [];
  private $structured = [];
  private $expense = [];
  function __construct() {

  }
  function addEntries($newEntries) {
    for ($i = 0; $i < count($newEntries); $i++) {
      $this->entries[] = $newEntries[$i];
      if ($newEntries[$i]["type"] == "worked") {
        $organization = $newEntries[$i]["organization"];
        $worker = $newEntries[$i]["worker"];
        $week = dateTimeToWeekOfYear($newEntries[$i]["date"]);
        $project = $newEntries[$i]["project"];
        $this->ensureDataStructure($organization, $worker, $week);
        $this->structured[$organization][$worker][$week]["hoursWorked"] += $newEntries[$i]["hours"];
        $this->structured[$organization][$worker][$week]["details"] .= "" . $newEntries[$i]["date"] . ": " .$newEntries[$i]["hours"] . "\n";

        $this->ensureExpenseStructure($organization, $project);
        $hours = $newEntries[$i]["hours"];
        // error_log("Worker $worker, Organisation $organization, Week $week, Project $project, Hours $hours");
        // error_log(var_export($this->structured[$organization][$worker][$week], true));
        if ($this->structured[$organization][$worker][$week]["hoursContracted"] != 0) {
          $rate = $this->structured[$organization][$worker][$week]["hourlyRate"];
          // error_log("Cost for $project: $hours * $rate");
          $this->expense[$organization][$project] += $hours * $rate;
        }

        // $fullProjectId = $newEntries[$i]["organization"] . ":" . $newEntries[$i]["project"];
        // if (!isset($projects[$fullProjectId])) {
        //   $projects[$fullProjectId] = 0;
        // }
        // $projects[$fullProjectId] += $newEntries[$i]["hours"];
      } else if ($newEntries[$i]["type"] == "contract") {
        if (isset(($newEntries[$i]["from"]))) {
          $fromWeek = dateTimeToWeekOfYear(($newEntries[$i]["from"]));
        } else {
          $fromWeek = ANALYSIS_FIRST_WEEK;
        }
        if (isset(($newEntries[$i]["to"]))) {
          $toWeek = dateTimeToWeekOfYear(($newEntries[$i]["to"]));
        } else {
          $toWeek = ANALYSIS_LAST_WEEK;
        }
        if (isset(($newEntries[$i]["hours"]))) {
          $hours = $newEntries[$i]["hours"];
        } else {
          $hours = DEFAULT_HOURS_PER_WEEK;
        }
        $hoursPerYear = $newEntries[$i]["hours"] * (365/7);
        $hoursPerMonth = $hoursPerYear / 12;
        $hourlyRate = $newEntries[$i]["amount"] / $hoursPerMonth;
        error_log("contract $hoursPerYear, $hoursPerMonth, $hourlyRate");
        $this->setHoursContracted($newEntries[$i]["organization"], $newEntries[$i]["worker"], $fromWeek, $toWeek, $hours, $hourlyRate);
      }
    }
  }

  function ensureDataStructure($organization, $worker, $week) {
    if(!$this->weekExists($week)) {
      throw new Exception("Week $week does not exist! Please use e.g. '202243' as the week id.\n");
    }
    if (strlen($week) < 6) {
      throw new Exception("Why is this week id so short? $week\n");
    }
    if (!isset($this->structured[$organization])) {
      $this->structured[$organization] = [];
    }
    if (!isset($this->structured[$organization][$worker])) {
      $this->structured[$organization][$worker] = [];
    }
    if (!isset($this->structured[$organization][$worker][$week])) {
      $this->structured[$organization][$worker][$week] = [
        "hoursWorked" => 0,
        "hoursContracted" => 0,
        "hourlyRate" => "help!",
        "details" => ""
      ];
    }
  }
  function ensureExpenseStructure($organization, $project) {
    if (!isset($this->expense[$organization])) {
      $this->expense[$organization] = [];
    }
    if (!isset($this->expense[$organization][$project])) {
      $this->expense[$organization][$project] = 0;
    }
  }

  function weekExists($week) {
    $numWeeks = [
      "2020" => 53,
      "2021" => 52,
      "2022" => 53,
      "2023" => 53,
      "2024" => 53,
      "2025" => 53,
      "2026" => 53
    ];
    $year = substr($week, 0, 4);
    $woy = substr($week, 4, 2);
    if (intval($year) < 2020 || intval($year) > 2026) {
      return false;
    }
    return (intval($woy) >= 0 && intval($woy) <= $numWeeks[$year]);
  }
  
  function setHoursContracted($organization, $worker, $fromWeek, $toWeek, $hours, $hourlyRate) {
    echo "setHoursContracted($organization, $worker, $fromWeek, $toWeek, $hours)\n";
    if(!$this->weekExists($fromWeek)) {
      throw new Exception("Week $fromWeek does not exist! Please use e.g. '202243' as the week id.\n");
    }
    if(!$this->weekExists($toWeek)) {
      throw new Exception("Week $toWeek does not exist! Please use e.g. '202243' as the week id.\n");
    }
    for ($week = $fromWeek; $week <= $toWeek; $week++) {
      // echo "Considering week $week for contract of $worker with $organization from $fromWeek to $toWeek, for $hours hours per week.\n";
      if ($this->weekExists($week)) {
        $this->ensureDataStructure($organization, $worker, $week);
        if (strlen($week) < 6) {
          throw new Exception("Why is this week id so short? $week\n");
        }
        $this->structured[$organization][$worker][$week]["hoursContracted"] = $hours;
        $this->structured[$organization][$worker][$week]["hourlyRate"] = $hourlyRate;
        debug("Organization $organization contracted  $worker for $hours hours in week of " . weekOfYearToDateTime($week) . ", hourly rate $hourlyRate\n");
      }
    }
  }
  function checkHoursPerWeek() {
    foreach($this->structured as $organization => $workers) {
      foreach($workers as $worker => $weeks) {
        // echo "Unsorted\n";
        // var_dump($weeks);
        ksort($weeks);
        // echo "Sorted\n";
        // var_dump($weeks);
        foreach ($weeks as $week => $data) {
          $hours = $data["hoursWorked"];
          $contractHours = $data["hoursContracted"];
          if ($hours != $contractHours) {
            debug("In week $week (starting " . weekOfYearToDateTime($week) . "), $worker wrote $hours hours for $organization instead of $contractHours!\n", LEVEL_OUTPUT);
            var_dump($data["details"]);
          } else {
            debug("In the week $week (starting " . weekOfYearToDateTime($week) . "), $worker wrote $hours hours for $organization which matches $contractHours!\n");
          }
        }
      }
    }
  }
  function reportCosts() {
    $total = 0;
    $totalStr = "";
    foreach($this->expense as $organization => $org) {
      foreach($org as $project => $amount) {
        $kEUR = floor($amount / 1000);
        if ($kEUR > 0) {
          error_log("Cost of $project for $organization: $kEUR kEUR");
          $totalStr .= " $kEUR +";
        }
        $total += $amount;
      }
    }
    error_log("Total salary costs in books: " . $total);
    error_log($totalStr);
  }
  function toPta() {
    for ($i = 0; $i < count($this->entries); $i++) {
      if ($this->entries[$i]["type"] == "worked") {
        $organization = $this->entries[$i]["organization"];
        $worker = $this->entries[$i]["worker"];
        $week = dateTimeToWeekOfYear($this->entries[$i]["date"]);
        $project = $this->entries[$i]["project"];
        $hours = $this->entries[$i]["hours"];
        echo "$week\n$worker  $hours\n$organization:$project\n\n";
      }
    }
  }
  function loadSources($folderPath) {
    // ignore the warning, we'll check for return value false below.
    set_error_handler(function() { /* ignore errors */ });
    $fileNames = scandir($folderPath);
    restore_error_handler();
    if ($fileNames == false) {
      echo "Folder not found: '$folderPath'\n";
      exit();
    }
  
    for ($i = 0; $i < count($fileNames); $i++) {
      if (($fileNames[$i] == ".") ||  ($fileNames[$i] == "..")) {
        continue;
      }
      if (!str_ends_with($fileNames[$i], ".pj2")) {
        echo "Filename '" . $fileNames[$i] . "' in '" . $folderPath . "' does not have a .pj2 extension .\n";
        exit(1);
      }
      $contents = file_get_contents($folderPath . $fileNames[$i]);
      try {
        $pj2Entries =  json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        // var_dump($pj2Entries);
       debug("Contents of " . $folderPath . $fileNames[$i] . " parsed as " . count($pj2Entries) . " PJ2 flat.\n");
  
      } catch (Exception $e) {
        echo "Contents of " . $folderPath . $fileNames[$i] . " is not JSON.\n";
        exit(1);
      }
      $this->addEntries($pj2Entries);
    }
  }  
}
