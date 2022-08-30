<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');
require_once(__DIR__ . '/helpers/createMovement.php');
require_once(__DIR__ . '/helpers/createStatement.php');
require_once(__DIR__ . '/../parsers/asnbank-CSV.php');
require_once(__DIR__ . '/../parsers/ingbank-CSV.php');

// E.g.: php src/cli-single.php import-bank-statement asnbank-CSV ./example.csv "2022-03-31 12:00:00"
//                             0                    1           2             3

function implyMovement($params) {
    $conn = getDbConn();
    $conn->executeStatement("INSERT INTO movements "
        . "(fromComponent, toComponent, timestamp_, amount, unit, type_) VALUES "
        . "(:fromComponent, :toComponent, :timestamp_, :amount, :unit, NULL)", [
        "fromComponent" => getComponentId($params["from"]),
        "toComponent" => getComponentId($params["to"]),
        "timestamp_" => strtotime($params["date"]),
        "amount" => $params["amount"],
        "unit" => $params["unit"]
    ]);
    $movementId = $conn->lastInsertId();
    $conn->executeStatement("INSERT INTO implications "
        . "(statementId, movementId, relation) VALUES "
        . "(:statementId, :movementId, relation)", [
        "statementId" => $params["statementId"],
        "movementId" => $movementId,
        "relation" => $params["relation"]
    ]);
}

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
        $documentId = (isset($command[4]) ? $command[4] : $fileName);
        $otherSystemId = (isset($command[5]) ? $command[5] : $fileName);
        $entries = $parserFunctions[$format](file_get_contents($fileName), $context["user"]["username"]);
        for ($i = 0; $i < count($entries); $i++) {
            // $objectId = $otherSystemId . $entries[$i]["remoteId"];
            $statementIdStr = $documentId . "#" . $entries[$i]["lineNum"];
            $command = ['create-statement', NULL, $importTime, $entries[$i]["comment"], $format, $statementIdStr];
            $statementId = createStatement($context, $command);
            if ($entries[$i]["amount"] > 0) {
                implyMovement([
                    "from" => $entries[$i]["otherComponent"],
                    "to" => $entries[$i]["bankAccountComponent"],
                    "date" => $entries[$i]["date"],
                    "amount" => $entries[$i]["amount"],
                    "unit" => $entries[$i]["unit"],
                    "statementId" => $statementId,
                    "relation" => "outer"
                ]);
                implyMovement([
                    "from" => $entries[$i]["bankAccountComponent"],
                    "to" => $context["user"]["username"],
                    "date" => $entries[$i]["date"],
                    "amount" => $entries[$i]["amount"],
                    "unit" => $entries[$i]["unit"],
                    "statementId" => $statementId,
                    "relation" => "inner"
                ]);
                implyMovement([
                    "from" => $context["user"]["username"],
                    "to" => $entries[$i]["otherComponent"],
                    "date" => $entries[$i]["date"],
                    "amount" => $entries[$i]["amount"],
                    "unit" => $entries[$i]["unit"],
                    "statementId" => $statementId,
                    "relation" => "delivery"
                ]);
            } else {
                implyMovement([
                    "from" => $context["user"]["username"],
                    "to" => $entries[$i]["bankAccountComponent"],
                    "date" => $entries[$i]["date"],
                    "amount" => -$entries[$i]["amount"],
                    "unit" => $entries[$i]["unit"],
                    "statementId" => $statementId,
                    "relation" => "inner"
                ]);
                implyMovement([
                    "from" => $entries[$i]["bankAccountComponent"],
                    "to" => $entries[$i]["otherComponent"],
                    "date" => $entries[$i]["date"],
                    "amount" => -$entries[$i]["amount"],
                    "unit" => $entries[$i]["unit"],
                    "statementId" => $statementId,
                    "relation" => "outer"
                ]);
                implyMovement([
                    "from" => $entries[$i]["otherComponent"],
                    "to" => $context["user"]["username"],
                    "date" => $entries[$i]["date"],
                    "amount" => -$entries[$i]["amount"],
                    "unit" => $entries[$i]["unit"],
                    "statementId" => $statementId,
                    "relation" => "delivery"
                ]);
            }
            // "otherComponent" => parseAccount2($obj),
            // "bankAccountComponent" => $obj["Rekening"],
            // "date" => parseIngDate($obj["Datum"]),
            // "comment" => parseIngDescription($obj),
            // "amount" => $amount, // may be pos or neg!
            // "balanceAfter" => parseIngAmount($obj["Saldo na mutatie"]),
            // "lineNum" => $i + 1
        
            // var_dump($entries[$i]);
            $movementIdsOutside = ensureMovementsLookalikeGroup($context, [
                "type_" => "outer",
                "fromComponent" => strval(getComponentId($entries[$i]["from"])),
                "toComponent" => strval(getComponentId($entries[$i]["to"])),
                "timestamp_" => $entries[$i]["date"],
                "amount" => $entries[$i]["amount"],
                "unit" => "EUR"
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
                "amount" => $entries[$i]["amount"],
                "unit" => "EUR"
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
