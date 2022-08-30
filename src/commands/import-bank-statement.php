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
        "timestamp_" => timestampToDateTime($params["date"]),
        "amount" => $params["amount"],
        "unit" => $params["unit"]
    ]);
    $movementId = $conn->lastInsertId();
    $conn->executeStatement("INSERT INTO implications "
        . "(statementId, movementId, relation) VALUES "
        . "(:statementId, :movementId, :relation)", [
        "statementId" => $params["statementId"],
        "movementId" => $movementId,
        "relation" => $params["relation"]
    ]);
}

function importBankStatement($context, $command)
{
    var_dump('importBankStatement');
    var_dump($context);
    var_dump($command);
    $parserFunctions = [
        "asnbank-CSV" => "parseAsnBankCSV",
        "ingbank-CSV" => "parseIngBankCSV"
    ];

    if (!isset($context["user"])) {
        return ["User not found or wrong password"];
    }

    $format = $command[1];
    $fileName = $command[2];
    $importTime = strtotime($command[3]);
    $documentId = $fileName;
    // $otherSystemId = (isset($command[5]) ? $command[5] : $fileName);
    $rules = json_decode(file_get_contents($command[4]), true);
    $entries = $parserFunctions[$format](file_get_contents($fileName), $context["user"]["username"]);
    for ($i = 0; $i < count($entries); $i++) {
        // $objectId = $otherSystemId . $entries[$i]["remoteId"];
        $statementIdStr = $documentId . "#" . $entries[$i]["lineNum"];
        $command = ['create-statement', null, $importTime, $entries[$i]["comment"], $format, $statementIdStr];
        $statementId = intval(createStatement($context, $command)[0]);
        if (!isset($rules[$entries[$i]["bankAccountComponent"]])) {
            throw new Error("have no rules!");
        }

        $submap = $rules[$entries[$i]["bankAccountComponent"]];
        $budget = "Other";
        foreach ($submap as $searchString => $impliedBudget) {
            if (str_contains($entries[$i]["otherComponent"], $searchString)) {
                $budget = $impliedBudget;
                break;
            }
        }
        if ($budget == "Self") {
            if ($entries[$i]["amount"] > 0) {
                // transfer to self, only process it once
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
            }
        } else {
            echo "$budget\n";
            if ($entries[$i]["amount"] > 0) {
                // income
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
                // purchase
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
        }
    }
    return [strval(count($entries))];
}
