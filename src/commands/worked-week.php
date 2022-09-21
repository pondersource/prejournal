<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/helpers/createMovement.php');
require_once(__DIR__ . '/helpers/createStatement.php');

// E.g.: php src/cli-single.php  worked-week "22 November 2021" "stichting" "ScienceMesh"
// E.g.: php src/cli-single.php  worked-week "22 November 2021" "stichting" "ScienceMesh" "Almost done"

function workedWeek($context, $command)
{
    if (isset($context["user"])) {
        $timestamp = strtotime($command[1]);
        $worker = $context["user"]["username"];
        $project = $command[2].':'.$command[3];
        $type = 'worked';
        $worked_hours = '40';
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
            // pushMovementsToTimesheet("worker", [
            //     [
            //         "amount" => $params["amount"],
            //         "timestamp_" => $params["timestamp_"],
            //         "id" => $conn->lastInsertId()
            //     ]
            // ]);

        return $result;
    } else {
        return ["User not found or wrong password"];
    }
}
