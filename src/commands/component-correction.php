<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');

function ComponentCorrection($context, $command)
{
    if ($context["adminParty"]) {
        $wrong = $command[1];
        $right = $command[2];        
        $params = [
            "wrong" => getComponentId($wrong),
            "right" => getComponentId($right)
        ];
        $conn = getDbConn();
        $conn->executeStatement("UPDATE movements SET fromcomponent = :right WHERE fromcomponent = :wrong", $params);
        $conn->executeStatement("UPDATE movements SET tocomponent = :right WHERE tocomponent = :wrong", $params);
        return ["Corrected component $wrong to $right"];
    } else {
        return ["User not found or wrong password"];
    }
}
