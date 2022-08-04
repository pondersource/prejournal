<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/../database.php');
  /*                                         uuid                                        type     id
  E.g.: php src/cli-single.php remove-entry   270e0144-8085-4366-9f7c-8aae59a3f11e      worked   1
*/
function removeEntry($context, $command)
{
    if (isset($context["user"])) {
        $uuid = $command[1];

        if($context["user"]["uuid"] === $uuid) {
            $type_ = strval($command[2]);
            $id = intval($command[3]);
            $result = deleteDataFromMovement($type_, $id);
    
            return $result;
        }
        
    } else {
        return ["User not found or wrong password"];
    }
}
