<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');

// E.g.: php src/cli-single.php claim-component "admin"
function claimComponent($context, $command)
{
    if (isset($context["user"])) {
        $userId = $context["user"]["id"];
        $componentId = getComponentId($command[1]);
        // var_dump($command);
        // var_dump("User ID $userId");
        // var_dump("Component ID $componentId");
        $query = "INSERT INTO accessControl (componentid, userid) VALUES (:componentId, :userId)";
        $params = [
            "componentId" => $componentId,
            "userId" => $userId
        ];
        // var_dump($query);
        // var_dump($params);
        $conn = getDbConn();
        $conn->executeStatement($query, $params);
        return [ "Claimed component $componentId for user $userId"];
    } else {
        return ["User not found or wrong password"];
    }
}
