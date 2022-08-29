<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/helpers/createMovement.php');
require_once(__DIR__ . '/helpers/createStatement.php');
require_once(__DIR__ . '/../parsers/asnbank-CSV.php');
require_once(__DIR__ . '/../parsers/ingbank-CSV.php');

// E.g.: php src/cli-single.php import-bank-statement asnbank-CSV ./example.csv "2022-03-31 12:00:00"
//                             0                    1           2             3

function importBankStatement($context, $command)
{
    $parserFunctions = [
        "asnbank-CSV" => "parseAsnBankCSV",
        "ingbank-CSV" => "parseIngBankCSV"
    ];

    if (isset($context["user"])) {
        $format = $command[1];
        $fileName = $command[2];
        $importTime = strtotime($command[3]);
        $entries = $parserFunctions[$format](file_get_contents($fileName), $context["user"]["username"]);
        for ($i = 0; $i < count($entries); $i++) {
            // var_dump($entries[$i]);
            $movementIdsOutside = ensureMovementsLookalikeGroup($context, [
                "type_" => "outer",
                "fromComponent" => strval(getComponentId($entries[$i]["from"])),
                "toComponent" => strval(getComponentId($entries[$i]["to"])),
                "timestamp_" => $entries[$i]["date"],
                "amount" => $entries[$i]["amount"]
            ], 1);
            // for ($j = 0; $j < count($movementIdsOutside); $j++) {
            //     ensureStatement($context, [
            //         "create-statement",
            //         intval($movementIdsOutside[$j]),
            //         $importTime,
            //         "outside movement from bank statement: " .$entries[$i]["comment"],
            //         $format,
            //         // FIXME: statement is about a message
            //         // remoteID is about the subject of that message
            //         // so maybe we need an extra table for tracking
            //         // data object at neighbouring systems?
            //         // 
            //         "$fileName#" . $entries[$i]["lineNum"] . " " . $entries[$i]["remoteID"]
            //     ]);
            // }

            $movementIdsInside = ensureMovementsLookalikeGroup($context, [
                "type_" => "inner",
                "fromComponent" => strval(getComponentId($entries[$i]["insideFrom"])),
                "toComponent" => strval(getComponentId($entries[$i]["insideTo"])),
                "timestamp_" => $entries[$i]["date"],
                "amount" => $entries[$i]["amount"]
            ], 1);
            for ($j = 0; $j < count($movementIdsInside); $j++) {
                ensureStatement($context, [
                    "create-statement",
                    intval($movementIdsInside[$j]),
                    $importTime,
                    "inside movement from bank statement: " .$entries[$i]["comment"],
                    $format,
                    "$fileName#" . $entries[$i]["lineNum"] . 
                        (isset($entries[$i]["remoteID"]) ? " " . $entries[$i]["remoteID"] : "")
                ]);
            }
        }
        return [strval(count($entries))];
    } else {
        return ["User not found or wrong password"];
    }
}
