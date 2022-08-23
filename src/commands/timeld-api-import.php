<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');
require_once(__DIR__ . '/../api/timeld.php');

//E.g.: php src/cli-single.php timeld-api-import "Timesheet" "ismoil/ismoil" 1234
function timeldApiImport($context, $command) {
     if($context["user"]) {
       
        //var_dump($command[1]);

        //$type = "@type" . ":". $command[1];
        //$id = "@id" . ":". $command[2];

        $data = array(
            '@type'      => $command[1],
            '@id'    => $command[2],
            'project'       => [],
            'external' => [
                "@id" => $command[3]
            ],
          );

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
            return ["The API timeld was import succesfully"];
        }

        //var_dump($result["code"]);

     } else {
        return ["User not found"];
     }
}