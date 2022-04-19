<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

// E.g.: php src/index.php worked-hours "20 September 2021" "stichting" "Peppol for the Masses" 4

function workedHours($context, $command) {
  if (isset($context["user"])) {
 //TODO
  } else {
    return ["User not found or wrong password"];
  }
}