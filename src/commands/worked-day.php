<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

// E.g.: php src/index.php  worked-day "23 August 2021" "stichting" "Peppol for the Masses"

function workedDay($context, $command) {
  if (isset($context["user"])) {
  

    $timestamp = strtotime($command[1]);
    $worker = $_SERVER['PREJOURNAL_USERNAM'];
    $project = $command[2].':'.$command[3];
    $type = 'worked';
    $worked_hours = '8';

    
  /* Create Movement */
  $movementId = intval(createMovement($context, [
    "create-movement",
    $type[0],
    strval(getComponentId($worker)),
    strval(getComponentId($project)),
    $timestamp,
    $worked_hours
  ])[0]);
  $statementId = intval(createStatement($context, [
    "create-statement",
    $movementId,
    $timestamp
  ])[0]);

  } else {
    return ["User not found or wrong password"];
  }
}