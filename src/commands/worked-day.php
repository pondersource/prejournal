<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

// E.g.: php src/index.php  worked-day "23 August 2021" "stichting" "Peppol for the Masses"

function workedDay($context, $command) {
  if (isset($context["user"])) {
    //TODO
  } else {
    return ["User not found or wrong password"];
  }
}