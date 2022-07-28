<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/../database.php');
  /*                                        amount type     id
  E.g.: php src/cli-single.php update-entry    2    worked   2
*/
function updateEntry($context, $command)
{
    if (isset($context["user"])) {
        $amount = floatval($command[1]);
        $type_ = strval($command[2]);
        $id = intval($command[3]);
        $result = updateDataFromMovement($amount, $type_, $id);

        return $result;
    } else {
        return ["User not found or wrong password"];
    }
}
