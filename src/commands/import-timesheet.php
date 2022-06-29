<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/createMovement.php');
  require_once(__DIR__ . '/helpers/createSync.php');
  require_once(__DIR__ . '/../parsers/wikiApi-JSON.php');
// E.g.: php src/cli-single.php import-hours wikiApi-JSON   wiki-suite-JSON.json "2022-03-31 12:00:00"
//                             0             1                 2                     3

function importTimesheet($context, $command)
{
    $parserFunctions = [
    "wikiApi-JSON" => "parseWikiApiJSON"
  ];


    if (isset($context["user"])) {
        $format = $command[1];
        
        $fileName = $command[2];

        $importTime = strtotime($command[3]);
        $type_ = "worked";
        $entries = $parserFunctions[$format](file_get_contents($fileName));
        
        foreach($entries as $result) {
            var_dump($result);
        }
        exit;

        for ($i = 0; $i < count($entries); $i++) {
            //var_dump($entries);
            $movementId = intval(createMovement($context, [
        "create-movement",
        $type_,
        strval(getComponentId($entries[$i]["worker"])),
        strval(getComponentId($entries[$i]["project"])),
        $entries[$i]["start"],
        $entries[$i]["seconds"],
        $entries[$i]["description"]
      ])[0]);
            createSync($context, [
                "movement",
                $movementId,
                $format,
                $fileName,
                intval(getComponentId($entries[$i]["worker"]))
            ])[0];
        }
        return [strval(count($entries))];
    } else {
        return ["User not found or wrong password"];
    }
}
