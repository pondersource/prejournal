<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/../database.php');
  /*                                             timestamp          project       amount  type     id
  E.g.: php src/cli-single.php update-entry    "23 August 2021"   ismoil:test      2      worked    2
*/
function updateEntry($context, $command)
{
    if (isset($context["user"])) {
        $timestamp = strtotime($command[1]);

        //$worker = $context["user"]["username"];
        $project = $command[2].':'.$command[3];

        $amount = floatval($command[4]);
        $type_ = strval($command[5]);
        $id = intval($command[6]);
        $result = updateDataFromMovement($timestamp, $project, $amount, $type_, $id);

        return $result;
    } else {
        return ["User not found or wrong password"];
    }
}
