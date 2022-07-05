<?php

declare(strict_types=1);
require_once(__DIR__ . '/../../platform.php');
require_once(__DIR__ . '/../../utils.php');

function createMovement($context, $command)
{
    if (!isset($context["user"])) {
        return ["User not found or wrong password"];
    }

    $conn  = getDbConn();
    $query = "INSERT INTO movements (type_, fromComponent, toComponent, timestamp_, amount) "
        . "VALUES (:type_, :fromComponent, :toComponent, :timestamp_, :amount);";

    $ret = $conn->executeStatement($query, [
        "type_" => $command[1],
        "fromComponent" => intval($command[2]),
        "toComponent" => intval($command[3]),
        "timestamp_" => timestampToDateTime(intval($command[4])),
        "amount" => floatval($command[5])
    ]);
    return [ strval($conn->lastInsertId()) ];
}

function ensureMovementsAndStatements($context, $movements, $statements)
{
    if (!isset($context["user"])) {
        return ["User not found or wrong password"];
    }
    // FIXME:
    $command = $movements;
    $conn  = getDbConn();
    $query = "SELECT m.*, s.* FROM "
        . "movements m INNER JOIN statements s ON m.id = s.movementid WHERE "
        . "type_ = :type_ AND fromComponent = :fromComponent AND toComponent = :toComponent AND "
        . "timestamp_ >= :mintimestamp_ AND timestamp_ <= :maxtimestamp_ AND amount = :amount;";
    $fields = [
        "type_" => $command[1],
        "fromComponent" => intval($command[2]),
        "toComponent" => intval($command[3]),
        "mintimestamp_" => timestampToDateTime(intval($command[4]) - 12 * 3600),
        "maxtimestamp_" => timestampToDateTime(intval($command[4]) + 12 * 3600),
        "amount" => floatval($command[5])
    ];
    $ret = $conn->executeQuery($query, $fields);
    $arr = $ret->fetchAllAssociative();
    if (count($arr) > 1) {
        throw new Error("multiple movements match this!");
    }
    if (count($arr) >= 1) {
        echo ("\nexists!\n");
        var_dump($command);
        var_dump($fields);
        var_dump($arr);
        return [ strval($arr[0]["id"]) ];
    }
    // CAREFUL, this SELECT-check-INSERT sequence is not thread-safe
    // Another process may have inserted inbetween the SELECT time and the INSERT time
    // and that would still lead to duplicates
    $query = "INSERT INTO movements (type_, fromComponent, toComponent, timestamp_, amount) "
        . "VALUES (:type_, :fromComponent, :toComponent, :timestamp_, :amount);";

    $ret = $conn->executeStatement($query, [
        "type_" => $command[1],
        "fromComponent" => intval($command[2]),
        "toComponent" => intval($command[3]),
        "timestamp_" => timestampToDateTime(intval($command[4])),
        "amount" => floatval($command[5])
    ]);
    return [ strval($conn->lastInsertId()) ];
}
