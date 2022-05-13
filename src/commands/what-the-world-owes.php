<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

// E.g. "2022-04-24 12:00:00" ->  "2022-04-24"
function toDate($str) {
  $parts = explode(' ', $str);
  return $parts[0];
}

// E.g. isBefore("2022-04-24", "2023-01-01") -> true
function isBefore($d1, $d2) {
  $parts1 = explode('-', $d1);
  $parts2 = explode('-', $d2);
  if (count($parts1) != 3) {
    throw new Error("First date is not in 1970-01-01 format: '$d1'");
  }
  if (count($parts2) != 3) {
    throw new Error("Second date is not in 1970-01-01 format: '$d2'");
  }
  for ($i = 0; $i < 3; $i++) {
    if (intval($parts1[$i]) < intval($parts2[$i])) {
      return true;
    }
    if (intval($parts1[$i]) > intval($parts2[$i])) {
      return false;
    }
  }
  return false;
}

// start and end date are optional, so:
// what-the-world-owes michiel
// what-the-world-owes michiel 2022-01-01
// what-the-world-owes michiel 2022-01-01 2023-01-01
function whatTheWorldOwes($context, $command) {
  if ($context['adminParty']) {
    $componentId = getComponentId($command[1]);
    if (count($command) >= 3) {
      $startDate = $command[2];
    } else {
      $startDate = "1970-01-01";
    }
    if (count($command) >= 4) {
      $endDate = $command[3];
    } else {
      $endDate = "2070-01-01";
    }
    $movements = getAllMovements();
    // similar to the code in the who-works-when command:
    $days = [];
    $cumm = 0;
    for ($i = 0; $i < count($movements); $i++) {
      $date = toDate($movements[$i]["timestamp_"]);
      if (isBefore($startDate, $date) && isBefore($date, $endDate)) {
        $delta = 0;
        if ($movements[$i]["fromcomponent"] == $componentId) {
          $delta = -floatval($movements[$i]["amount"]);
        }
        if ($movements[$i]["tocomponent"] == $componentId) {
          $delta = floatval($movements[$i]["amount"]);
        }
        if ($delta != 0) {
          if (!isset($days[$date])) {
            $days[$date] = [];
          }
          $id = $movements[$i]["id"];
          array_push($days[$date], $delta);
        }
      }
    }
    ksort($days);
    $ret = [];
    $cumm = 0;
    foreach ($days as $date => $arr) {
      array_push($ret, formatDate($date) . " $cumm");
      foreach ($days[$date] as $val) {
        $cumm += $val;
      }
    }
    return $ret;
  } else {
      return ["This command disregards access checks so it only works in admin party mode"];
  }
}