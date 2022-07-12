<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/services/getWiki.php');
  require_once(__DIR__ . '/helpers/createSync.php');
  require_once(__DIR__ . '/../database.php');
  /*
  E.g.: php src/cli-single.php wiki-api-export wiki
*/
function wikiApiExport($context, $command)
{
    if (isset($context["user"])) {
        $remote_system = $command[1];
        //var_dump($remote_system);
        //exit;

        $sync = getAllStatementsWiki($remote_system);

        if ($remote_system == "wiki") {
            $result = addMovementForWiki();
            //var_dump($result);
            // exit;
            if ($result === null) {
                return ["Try again to insert data inside sync and movement"];
            }

            $internal_type = 'movement';
            $remote_system = 'wiki';

            if ($sync == null || !$sync && !$result) {
                foreach ($result as $syn) {
                    createSync($context, [
                            $internal_type,
                                $syn["id"],
                                $syn["url"],
                                $remote_system
                            ]);
                }
            }
            return ["Prejournal create a new data from Wiki"];
        }
    } else {
        return ["User not found or wrong password"];
    }
}

function addMovementForWiki()
{
    $remote_id = getWiki();
    $result = getAllWorkedMovements();

    $newArray = [];
    $conn  = getDbConn();
    $type = "worked";

    if (!$result && empty($result)) {
        foreach ($remote_id as $remote) {
            $fromComponent = intval(getComponentId($remote->tsUser));
            $toComponent = intval(getComponentId($remote->tsProject));
            $timestamp = timestampToDateTime(intval($remote->tsDate));
            $amount = intval($remote->tsMinutesCalculated);



            $movement = "INSERT INTO movements(type_, fromComponent, toComponent,timestamp_, amount) VALUES ('".$type. "',".intval(getComponentId($remote->tsUser)).",'".$toComponent."', '".$timestamp."','".$amount."'); ";

            $conn->exec($movement);
        }
    } else {
        foreach ($remote_id as $remote) {
            foreach ($result as $res) {
                //var_dump($res["amount"]);
                //var_dump($remote->tsMinutesCalculated);
                //exit;
                if ($res["amount"] == $remote->tsMinutesCalculated) {
                    array_push($newArray, [
                'id' => $res["id"],
                "url" =>stripslashes($remote->tsURI)
            ]);
                }
            }
        }
        return $newArray;
    }
}
