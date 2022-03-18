<?php
require_once(__DIR__ . '/../database.php');

function ptaMe() {
  $user = getUser();
  if ($user) {
    $movementsIn = getMovementsToComponent($user['username']);
    for ($i = 0; $i < count($movementsIn); $i++) {
      output($movementsIn[$i]["timestamp_"]);
      output("assets  " . $movementsIn[$i]["amount"]);
      output("income");
      output("");
    }
    for ($i = 0; $i < count($movementsIn); $i++) {
      output($movementsIn[$i]["timestamp_"]);
      output("liabilities  -" . $movementsIn[$i]["amount"]);
      output("expenses");
      output("");
    }
    $movementsOut = getMovementsFromComponent($user['username']);
  } else {
    output("User not found or wrong password");
  }
}