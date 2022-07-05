<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/../database.php');
  /*                                         type     id
  E.g.: php src/cli-single.php remove-entry  worked   1 
*/
function removeEntry($context, $command)
{
    if (isset($context["user"])) {
      
       $type_ = strval($command[1]);
       $id = intval($command[2]);
       $result = deleteDataFromMovement($type_, $id);
       
       return $result;

    }  else {
        return ["User not found or wrong password"];
    }
}