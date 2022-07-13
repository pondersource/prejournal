<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/createMovement.php');
  require_once(__DIR__ . '/helpers/createSync.php');
  require_once(__DIR__ . '/../parsers/wikiApi-JSON.php');
// E.g.: php src/cli-single.php import-timesheet wikiApi-JSON   wiki-suite-JSON.json "2022-03-31 12:00:00"
//                             0             1                 2                     3

function importTimesheet($context, $command)
{
    $parserFunctions = [
    "wikiApi-JSON" => "parseWikiApiJSON"
  ];
    $conn  = getDbConn();

    if (isset($context["user"])) {
        $format = $command[1];

        $fileName = $command[2];

        $importTime = strtotime($command[3]);
        $type_ = "worked";
        $entries = $parserFunctions[$format](file_get_contents($fileName));

        $res = getAllWorkedMovements();

        foreach ($entries as $result) {
            $fromComponent = intval(getComponentId($result["worker"]));
            $toComponent = intval(getComponentId($result["project"]));
            $timestamp_ = timestampToDateTime(intval($result["start"]));
            $amount = intval($result["seconds"]);

            if (!$res) {
                $result = createMultipleMovement($context["user"]["id"],$type_, $fromComponent, $toComponent, $timestamp_, $amount);
            //var_dump($result);
            } else {
                $res = getAllWorkedMovements();
                //var_dump($res);
            }

            //$movement = "INSERT INTO movements(type_, fromComponent, toComponent,timestamp_, amount,description) VALUES ('".$type_. "',".$from.",'".$to."', '".$timestamp."','".$amount."','".$description."'); ";
            //$conn->exec($movement);
        }
        return [strval(count($entries))];
    } else {
        return ["User not found or wrong password"];
    }
}
