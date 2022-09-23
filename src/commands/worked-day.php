<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/helpers/createMovement.php');
require_once(__DIR__ . '/helpers/createStatement.php');
require_once(__DIR__ . '/../platform.php');

// E.g.: php src/cli-single.php  worked-day "23 August 2021" "stichting" "Peppol for the Masses"
// E.g.: php src/cli-single.php  worked-day "23 August 2021" "stichting" "Peppol for the Masses" "Last task completed"

function workedDay($context, $command)
{
    if (isset($context["user"])) {
        $timestamp = strtotime($command[1]);
        $worker = $context["user"]["username"];
        $project = $command[2].':'.$command[3];
        $type = 'worked';
        $worked_hours = '8';
        $description = (count($command) >= 5 ? $command[4] : "");
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
        $result = getMovementAndStatement($movementId, $statementId);
        $rows = json_decode($result[0], true);
        if (isset($rows[0]["worker"])) {
            propagateDiff($rows[0]["worker"], [
                [
                    "amount" => intval($rows[0]["amount"]),
                    "timestamp_" => $rows[0]["timestamp_"],
                    "id" => $rows[0]["movementId"],
                    "description" => $rows[0]["description"]
                ]
            ]);
        }
        return $result;
        // return [json_encode($command), "Created movement $movementId", "Created statement $statementId"];
        //return ["Created movement $movementId", "Created statement $statementId"];
    } else {
        return ["User not found or wrong password"];
    }
}
