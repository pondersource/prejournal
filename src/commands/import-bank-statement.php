<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/createMovement.php');
  require_once(__DIR__ . '/helpers/createStatement.php');
  require_once(__DIR__ . '/../parsers/asnbank-CSV.php');

// E.g.: php src/cli-single.php import-bank-statement asnbank-CSV ./example.csv "2022-03-31 12:00:00"
//                             0                    1           2             3

function importBankStatement($context, $command)
{
    $parserFunctions = [
    "asnbank-CSV" => "parseAsnBankCSV",
  ];

    if (isset($context["user"])) {
        $format = $command[1];
        $fileName = $command[2];
        $importTime = strtotime($command[3]);
        $type_ = "payment";
        $entries = $parserFunctions[$format](file_get_contents($fileName), $context["user"]["username"]);
        for ($i = 0; $i < count($entries); $i++) {
            $movementIdOutside = intval(createMovement($context, [
        "create-movement",
        $type_,
        strval(getComponentId($entries[$i]["from"])),
        strval(getComponentId($entries[$i]["to"])),
        $entries[$i]["date"],
        $entries[$i]["amount"],
        "outside movement from bank statement: " .$entries[$i]["comment"]
      ])[0]);
            intval(createStatement($context, [
        "create-statement",
        $movementIdOutside,
        $importTime
      ])[0]);
            $movementIdInside = intval(createMovement($context, [
        "create-movement",
        $type_,
        strval(getComponentId($entries[$i]["insideFrom"])),
        strval(getComponentId($entries[$i]["insideTo"])),
        $entries[$i]["date"],
        $entries[$i]["amount"],
        "inside movement from bank statement: " .$entries[$i]["comment"]
      ])[0]);
            intval(createStatement($context, [
        "create-statement",
        $movementIdInside,
        $importTime
      ])[0]);
        }
        return [strval(count($entries))];
    } else {
        return ["User not found or wrong password"];
    }
}
