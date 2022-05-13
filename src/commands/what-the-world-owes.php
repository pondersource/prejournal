<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

function whatTheWorldOwes($context, $command) {
  if ($context['adminParty']) {
    $componentId = getComponentId($command[1]);
    $movements = getAllMovements();
    $ret = [];
    $cumm = 0;
    for ($i = 0; $i < count($movements); $i++) {
      if ($movements[$i]["fromcomponent"] == $componentId) {
        $delta = -floatval($movements[$i]["amount"]);
        $cumm += $delta;
        array_push($ret, "delta $delta balance $cumm");
      }
      if ($movements[$i]["tocomponent"] == $componentId) {
        $delta = floatval($movements[$i]["amount"]);
        $cumm += $delta;
        array_push($ret, "delta $delta balance $cumm");
      }
    }
    return $ret;
  } else {
      return ["This command disregards access checks so it only works in admin party mode"];
  }
}