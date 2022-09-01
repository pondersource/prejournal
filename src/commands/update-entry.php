<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');
/*                                               timestamp             worker   project       amount   descritpion    movementId
    E.g.: php src/cli-single.php update-entry    "23 August 2021"      "test"    "test"        2        "test"              2
*/
function updateEntry($context, $command)
{
    if (isset($context["user"])) {
        $timestamp = strtotime($command[1]);

        //$worker = $context["user"]["username"];
        $worker = $command[2];
        $project = $command[3];
        $amount = floatval($command[4]);

        $description = $command[5];
        $id = intval($command[6]);
        $result = updateDataFromMovement($timestamp, $worker, $project, $amount, $description, $id);

        return $result;
    } else {
        return ["User not found or wrong password"];
    }
}
