<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/helpers/createMovement.php');
require_once(__DIR__ . '/helpers/createStatement.php');

// E.g.: php src/cli-single.php enter "from component" "to component" "1.23" "2021-12-31T23:00:00.000Z" "invoice" "ponder-source-agreement-192"
//                           0              1               2       3                4                 5          6

function enter($context, $command)
{
    if (isset($context["user"])) {
        $userId = $context["user"]["id"];
        $componentFromId = getComponentId($command[1]);
        $componentToId = getComponentId($command[2]);
        $amountStr = $command[3];
        $dateStr = $command[4];
        $type_ = $command[5];
        // unused: $command[6]
        $movementId = intval(createMovement($context, [
            "create-movement",
            $userId,
            $type_,
            strval($componentFromId),
            strval($componentToId),
            $dateStr,
            $amountStr,
            'EUR'
        ])[0]);
        $statementId = intval(createStatement($context, [
            "create-statement",
            $movementId,
            timestampToDateTime(time())
        ])[0]);
        return [strval($statementId)];
    } else {
        return ["User not found or wrong password"];
    }
}
