<?php declare(strict_types=1);
require_once(__DIR__ . '/../database.php');
$ret = [];
function ptaMe($context) {
  if ($context->user) {
    $movementsIn = getMovementsToComponent($context->user['username']);
    for ($i = 0; $i < count($movementsIn); $i++) {
      ret.push($movementsIn[$i]["timestamp_"]);
      ret.push("assets  " . $movementsIn[$i]["amount"]);
      ret.push("income");
      ret.push("");
    }
    for ($i = 0; $i < count($movementsIn); $i++) {
      ret.push($movementsIn[$i]["timestamp_"]);
      ret.push("liabilities  -" . $movementsIn[$i]["amount"]);
      ret.push("expenses");
      ret.push("");
    }
    $movementsOut = getMovementsFromComponent($context->user['username']);
  } else {
    ret.push("User not found or wrong password");
  }
  return ret;
}