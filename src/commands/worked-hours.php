<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/helpers/createMovement.php');
require_once(__DIR__ . '/helpers/createStatement.php');

//                                                 date              client       project             numHours description worker    
// E.g.: php src/cli-single.php  worked-hours "20 September 2021" "stichting" "Peppol for the Masses" 4
// E.g.: php src/cli-single.php  worked-hours "20 September 2021" "stichting" "Peppol for the Masses" 4        "Last hours"
// E.g.: php src/cli-single.php  worked-hours "20 September 2021" "stichting" "Peppol for the Masses" 4        "Last hours" george

function workedHours($context, $command)
{
    if (isset($context["user"])) {
        $conn  = getDbConn();
        $timestamp = strtotime($command[1]);
        // Note that you cannot just enter any worker's name here,
        // you will still have to go through the `hasAccess` check
        // which checks whether the authenticated user has claimed
        // the worker component.
        // In the current setup, claiming components is first-come-first-server
        // For instance, if the user `m-ld` claims components `angus` and `george`,
        // it means that from then on, only user `m-ld` will be able to edit
        // the timesheets of Angus and George.
        // Tip: if a user is only used for one user, make the user name and the
        // worker component name match, then you can leave off the 7th argument
        // and it will just default to that.
        // For instance user `michiel`
        // can claim the component of worker `michiel`.
        $worker = (count($command) >= 7 ? $command[6] : $context["user"]["username"]);
        $project = $command[2].':'.$command[3];
        $type = 'worked';
        $worked_hours = $command[4];
        $description = (count($command) >= 6 ? $command[5] : "");
        /* Create Movement */
        $movementId = intval(createMovement($context, [
            "create-movement",
            $context["user"]["id"],
            $type,
            strval(getComponentId($worker)),
            strval(getComponentId($project)),
            $timestamp,
            $worked_hours
        ])[0]);
        $statementId = intval(createStatement($context, [
          "create-statement",
          $movementId,
          $timestamp,
          $description
      ])[0]);

       $result = getMovementAndStatement($movementId, $statementId);
       return $result;

        // return [json_encode($command), "Created movement $movementId", "Created statement $statementId"];
        //return [ "Created movement $movementId", "Created statement $statementId"];
    } else {
        return ["User not found or wrong password"];
    }
}
