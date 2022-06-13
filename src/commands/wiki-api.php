<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/services/getWiki.php');
  require_once(__DIR__ . '/helpers/createSync.php');
  require_once(__DIR__ . '/../database.php');
  /*
  E.g.: php src/cli-single.php wiki-api wiki
*/
function wikiApi($context, $command)
{
    if (isset($context["user"])) {
        $remote_system = $command[1];
        //var_dump($remote_system);
        //exit;
        $type = "worked";
        $remote_id = getWiki();

        if ($remote_system == "wiki") {
            $fromComponent = getComponentId($remote_id[0]->tsProject);
            $toComponent = getComponentId($remote_id[0]->tsUser);
            $timestamp = strtotime($remote_id[0]->tsDate);
            //var_dump($timestamp);
            //exit;
            $amount = $remote_id[0]->tsMinutesCalculated;
            $description = $remote_id[0]->tsDescription;

            $result = addMovement($type, $fromComponent, $toComponent, $timestamp, $amount, $description);

            $internal_type = 'movement';
            $remote_system = 'wiki';

            $remote_url = stripslashes($remote_id[0]->tsURI);
            $res = createSync($context, [
       $internal_type,
        $result,
        $remote_url,
        "wiki"
    ]);
            return ["Prejournal create a new data from Wiki"];
        }
    } else {
        return ["User not found or wrong password"];
    }
}
