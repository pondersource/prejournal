<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/../database.php'); 
  /*                                         uuid                                           timestamp          project       amount       id
  E.g.: php src/cli-single.php update-entry    270e0144-8085-4366-9f7c-8aae59a3f11e       "23 August 2021"      test            2         2
*/
function updateEntry($context, $command)
{
    if (isset($context["user"])) {
        $uuid = $command[1];

        if($context["user"]["uuid"] === $uuid) {
            $timestamp = strtotime($command[2]);

            //$worker = $context["user"]["username"];
            $project = $command[3];
    
            $amount = floatval($command[4]);
            $description = $command[5];
            $id = intval($command[6]);
            $result = updateDataFromMovement($timestamp, $project, $amount, $description, $id);
    
            return $result;
        }
        
    } else {
        return ["User not found or wrong password"];
    }
}
