<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');
/*                                               timestamp          project       amount       id
    E.g.: php src/cli-single.php update-entry    "23 August 2021"      test            2         2
*/
function updateEntry($context, $command)
{
    if (isset($context["user"])) {
        $timestamp = strtotime($command[1]);

        //$worker = $context["user"]["username"];
        $project = $command[2];

        $amount = floatval($command[3]);
        $description = $command[4];
        $id = intval($command[5]);
        $result = updateDataFromMovement($timestamp, $project, $amount, $description, $id);

        return $result;
    } else {
        return ["User not found or wrong password"];
    }
}
