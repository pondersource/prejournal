<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

// E.g.: php src/index.php   submit-expense "28 August 2021" "Degrowth Conference train tickets" "transport" 100 "michiel"
function submitExpense($context, $command) {
  if (isset($context["user"])) {

  } else {
    return ["User not found or wrong password"];
  }
}