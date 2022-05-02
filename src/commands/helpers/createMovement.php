<?php declare(strict_types=1);
require_once(__DIR__ . '/../../platform.php');
require_once(__DIR__ . '/../../utils.php');

function createMovement($context, $command) {
  if (isset($context["user"])) {
    $conn  = getDbConn();
    $query = "INSERT INTO movements (type_, fromComponent, toComponent, timestamp_, amount, description) "
       . "VALUES (:type_, :fromComponent, :toComponent, :timestamp_, :amount, :description);";

    $ret = $conn->executeStatement($query, [
      "type_" => $command[1],
      "fromComponent" => intval($command[2]),
      "toComponent" => intval($command[3]),
      "timestamp_" => timestampToDateTime(intval($command[4])),
      "amount" => floatval($command[5]),
      "description" => $command[6]
    ]);
    return [ strval($conn->lastInsertId()) ];
  } else {
    return ["User not found or wrong password"];
  }
}