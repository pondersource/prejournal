<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/helpers/createMovement.php');
require_once(__DIR__ . '/helpers/createStatement.php');

// E.g.: php src/cli-single.php  worked-hours "20 September 2021" "stichting" "Peppol for the Masses" 4
// E.g.: php src/cli-single.php  worked-hours "20 September 2021" "stichting" "Peppol for the Masses" 4 "Last hours"

function workedHours($context, $command)
{
    if (isset($context["user"])) {
        $timestamp = strtotime($command[1]);
        $worker = $context["user"]["username"];
        $project = $command[2].':'.$command[3];
        $type = 'worked';
        $worked_hours = $command[4];
        $description = (count($command) >= 6 ? $command[5] : "");
        /* Create Movement */
        $movementId = intval(createMovement($context, [
            "create-movement",
            $context["user"]["id"],
            $type,
            strval(getComponentId($worker)),
            strval(getComponentId($project)),
            $timestamp,
            $worked_hours
        ])[0]);
      $statementId = intval(createStatement($context, [
          "create-statement",
          $movementId,
          $timestamp,
          $description
      ])[0]);

        // return [json_encode($command), "Created movement $movementId", "Created statement $statementId"];
        return [ "Created movement $movementId", "Created statement $statementId"];
    } else {
        return ["User not found or wrong password"];
    }
}
