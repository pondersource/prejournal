<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');
require_once(__DIR__ . '/../api/timeld.php');

//E.g.: php src/cli-single.php timeld-api-export timeld
function timeldApiExport($context, $command) {
    $remote_system = $command[1];
    $result = exportTimeLd();
     if($context["user"]) {
     } else {
        return ["User not found or wrong password"];
    }
}