<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

// E.g.: php src/index.php worked-week "22 November 2021" "stichting" "ScienceMesh"

function workedWeek($context, $command) {
  if (isset($context["user"])) {
 //TODO
  } else {
    return ["User not found or wrong password"];
  }
}