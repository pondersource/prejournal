<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/createMovement.php');
  require_once(__DIR__ . '/createStatement.php');
  require_once(__DIR__ . '/../parsers/time-CSV.php');
  require_once(__DIR__ . '/../parsers/timeBro-CSV.php');
  require_once(__DIR__ . '/../parsers/timeDoctor-CSV.php');
  require_once(__DIR__ . '/../parsers/timetip-JSON.php');
// E.g.: php src/index.php import-hours time-CSV ./example.csv "2022-03-31 12:00:00"
//                             0             1           2         3

function importHours($context, $command) {
  $parserFunctions = [
    "time-CSV" => "parseTimeCSV",
    "timeBro-CSV" => "parseTimeBroCSV",
    "timeDoctor-CSV" => "parseTimeDoctorCSV",
    "timetip-JSON" => "parseTimetipJSON",
  ];
  
  if (isset($context["user"])) {
    $format = $command[1];
    $fileName = $command[2];
    
    $importTime = strtotime($command[3]);
    $type_ = "worked";
    $entries = $parserFunctions[$format](file_get_contents($fileName));
    //var_dump($entries);
    for ($i = 0; $i < count($entries); $i++) {
      //var_dump($entries);
        $movementId = intval(createMovement($context, [
        "create-movement",
        $type_,
        strval(getComponentId($entries[$i]["worker"])),
        strval(getComponentId($entries[$i]["project"])),
        $entries[$i]["start"],
        $entries[$i]["seconds"]
      ])[0]);
        $statementId = intval(createStatement($context, [
        "create-statement",
        $movementId,
        $importTime
      ])[0]);
    }
    return [strval(count($entries))];
  } else {
    return ["User not found or wrong password"];
  }
}