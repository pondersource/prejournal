<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/../database.php');
require_once(__DIR__ . '/../api/teamwork.php');

//E.g.: php src/cli-single.php teamwork-api-projects teamwork description 20221019 20221020
function teamworkApiProjects($context, $command) {

     if($context["user"]) {
        $name = $command[1];
        $description = $command[2];
        $start_date = $command[3];
        $end_date = $command[4];
        $data = [
            "name" => $name,
            "description" => $description,
            "start_date" => $start_date,
            "end_date" => $end_date
        ]; 
        $result = createProjectFromTeamwork($data);

        if(isset($result["MESSAGE"])) {
            return ["The project is already taken"];
        }
        echo "The project id " . $result["id"] ." successfully created";
     } else {
        return ["User not found or wrong password"];
    }
}