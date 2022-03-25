<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/createMovement.php');
  require_once(__DIR__ . '/createStatement.php');

// E.g.: php src/index.php enter "from component" "to component" "1.23" "2021-12-31T23:00:00.000Z" "invoice" "ponder-source-agreement-192"
//                           0              1               2       3                4                 5          6
// create movement:
// "type_" => $command[1],
// "fromComponent" => intval($command[2]),
// "toComponent" => intval($command[3]),
// "timestamp_" => timestampToDateTime(intval($command[4])),
// "amount" => floatval($command[5])
//
// create statement:
// "userId" => $context["user"]["id"],
// "movementId" => intval($command[1]),
// "timestamp_" => timestampToDateTime(intval($command[2]))

function enter($context, $command) {
  if (isset($context["user"])) {
    $userId = $context["user"]["id"];
    $componentFromId = getComponentId($command[1]);
    $componentToId = getComponentId($command[2]);
    $movementId = intval(createMovement($context, [
      "create-movement",
      $command[5],
      strval($componentFromId),
      strval($componentToId),
      $command[4],
      $command[3]
    ])[0]);
    $statementId = intval(createStatement($context, [
      "create-statement",
      $movementId, 
      timestampToDateTime(time())
    ])[0]);
    return [strval($statementId)];
  } else {
    return ["User not found or wrong password"];
  }
}