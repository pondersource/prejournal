<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');
require_once(__DIR__ . '/../api/timeld.php');
/*
                                                  type        id               project   remote_url                 timestamp       amount
  E.g.: php src/cli-single.php timeld-api-import "Timesheet" "ismoil/ismoil" "fedb/fedb" http://ex.org/timesheet/1 "22 August 2021" 8
*/
function timeldApiImport($context, $command) {
     if($context["user"]) {
    
        $data = array(
            '@type'      => $command[1],
            '@id'    => $command[2],
            'project'       => [
                ["@id" => $command[3]]
            ],
            'external' => [
                "@id" => $command[4]
            ],
            
          );
        $timestamp = strtotime($command[5]);
        $amount = $command[6];
        $json = json_encode($data);

        $result = importTimld($json);

         //var_dump($result);

        if(isset($result["code"])) {
            if($result["code"] === "Forbidden") {
                return ["You have forbidden access you need right username"];
                //exit;
            } else if($result["code"] === "BadRequest") {
                return ["Malformed domain entity"];
            }
        }
       
        if($result  === null) {
    
            $movementId = intval(createMovement($context, [
                "create-movement",
                $context["user"]["id"],
                $data["@type"],
                strval(getComponentId($data["@id"])),
                strval(getComponentId($data["project"][0]["@id"])),
                $timestamp,
                $amount
            ])[0]);

            $statementId =  createSync($context, [
                "movement",
                $movementId,
                $data["external"]["@id"],
                "timeld"
            ])[0];
            //var_dump($movementId);
            return ["The API timeld was import succesfully". "Created movement $movementId", "Created statement $statementId"];
        }

        //var_dump($result["code"]);

     } else {
        return ["User not found"];
     }
}