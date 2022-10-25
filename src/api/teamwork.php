<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
//require_once(__DIR__. '/../../loadenv.php');
require_once(__DIR__ . '/callGetEndpoint.php');
require_once(__DIR__ . '/callEndpoint.php');


function exportTimeTeamWork() {
    $url = $_SERVER["TEAMWORK_HOST"] . '/time_entries.json';


    $headers = array(
        "Content-Type: application/json",
        'Authorization: Basic '. base64_encode($_SERVER["TEAMWORK_USERNAME"].':'.$_SERVER["TEAMWORK_PASSWORD"]),
     );

     $resp = callGetEndpoint($headers, $url);
     return $resp;
}

function importTimeTeamWork(array $request) {
    $result = (array) exportTimeTeamWork();
       
    
    $array = json_decode(json_encode($result), true);
    foreach($array as $project) {
        if (is_array($project) || is_object($project))
        {
            usort($project, function($a, $b) {
                return strtotime($a["createdAt"]) < strtotime($b["createdAt"]) ? -1 : 1;
            });
            $last_information  = last($project);
            
            $url = $_SERVER["TEAMWORK_HOST"] . '/projects/'.$last_information["project-id"].'/time_entries.json';
            $headers = array(
                "Content-Type: application/json",
                'Authorization: Basic '. base64_encode($_SERVER["TEAMWORK_USERNAME"].':'.$_SERVER["TEAMWORK_PASSWORD"]),
             );
             $data = [
                "time-entry" =>[
                    "description" => $request["description"],
                    "person-id" => $last_information["person-id"],
                    "date" => $request["date"], //YYYYMMDD
                    "time" => $request["time"], //HH:MM,
                    "hours" => $request["hours"],
                    "minutes" => $request["minutes"],
                    "isbillable" => true,
                ]
             ];
             $result = json_encode($data);
             
             $resp = callEndpoint($headers, $result, $url);
             return $resp;
           
        }
    }
}

function createProjectFromTeamwork(array $request) {
    $url = $_SERVER["TEAMWORK_HOST"] . '/projects.json';

    $headers = array(
        "Content-Type: application/json",
        'Authorization: Basic '. base64_encode($_SERVER["TEAMWORK_USERNAME"].':'.$_SERVER["TEAMWORK_PASSWORD"]),
     );

    $data = [
      "project" => [
        "name" => $request["name"],
        "description" => $request["description"],
        "use-tasks" => true,
        "use-milestones" => true,
        "use-messages" => true,
        "use-files" => true,
        "use-time" => true,
        "use-notebook" => true,
        "use-riskregister" => true,
        "use-links" => true,
        "use-billing" => true,
        "use-comments" => true,
        "category-id" => 0,
        "start-date" => $request["start_date"],//YYYYMMDD
        "end-date" => $request["end_date"],//YYYYYMMDD
        "tagIds" => "string",
        "onboarding" => true,
        "grant-access-to" => "string",
        "private" => true,
        "projectOwnerId" => 0,
        "companyId" => 12718
      ]
    ];
    $result = json_encode($data);
    //var_dump($result);
    //exit;

    $resp = callEndpoint($headers, $result, $url);
    return $resp;
}