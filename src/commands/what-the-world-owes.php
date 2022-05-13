<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

// E.g. "2022-04-24 12:00:00" ->  "2022-04-24"
function toDate($str) {
  $parts = explode(' ', $str);
  return $parts[0];
}

function whatTheWorldOwes($context, $command) {
  if ($context['adminParty']) {
    $componentId = getComponentId($command[1]);
    $movements = getAllMovements();
    // similar to the code in the who-works-when command:
    $days = [];
    $cumm = 0;
    for ($i = 0; $i < count($movements); $i++) {
      $delta = 0;
      if ($movements[$i]["fromcomponent"] == $componentId) {
        $delta = -floatval($movements[$i]["amount"]);
      }
      if ($movements[$i]["tocomponent"] == $componentId) {
        $delta = floatval($movements[$i]["amount"]);
      }
      if ($delta != 0) {
        $date = toDate($movements[$i]["timestamp_"]);
        if (!isset($days[$date])) {
          $days[$date] = [];
        }
        $id = $movements[$i]["id"];
        array_push($days[$date], $delta);
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