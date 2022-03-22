<?php declare(strict_types=1);
require_once(__DIR__ . '/../database.php');
function ptaMe($context) {
  if (isset($context["user"])) {
    $movements = getMovementsForUser($context["user"]["id"]);
    $ret = [];
    for ($i = 0; $i < count($movements); $i++) {
      array_push($ret, $movements[$i]["timestamp_"]);
      array_push($ret, "assets  " . $movements[$i]["amount"]);
      array_push($ret, "income");
      array_push($ret, "");
    }
    return $ret;
  } else {
    return [ "User not found or wrong password" ];
  }
}