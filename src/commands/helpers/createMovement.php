<?php

declare(strict_types=1);
require_once(__DIR__ . '/../../platform.php');
require_once(__DIR__ . '/../../utils.php');

function createMovement($context, $command)
{
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
      "description" => $command[6] ?? null
    ]);
        return [ strval($conn->lastInsertId()) ];
    } else {
        return ["User not found or wrong password"];
    }
}

function createMultipleMovement($type_, $fromComponent, $toComponent, $timestamp_, $amount, $description) {
    $conn  = getDbConn();
    $conn->executeQuery(
        "INSERT INTO movements (type_, fromComponent, toComponent,timestamp_, amount,description) VALUES (:type_, :fromComponent, :toComponent, :timestamp_,:amount, :description) ",
        [ "type_" => $type_, "fromComponent" => $fromComponent, "toComponent" => $toComponent, "timestamp_" => $timestamp_, "amount" => $amount, "description" => $description]
    );
    return [ strval($conn->lastInsertId()) ];
}
