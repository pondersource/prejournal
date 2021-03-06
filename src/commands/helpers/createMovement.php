<?php

declare(strict_types=1);
require_once(__DIR__ . '/../../platform.php');
require_once(__DIR__ . '/../../utils.php');

function createMovement($context, $command)
{
    if (!isset($context["user"])) {
        return ["User not found or wrong password"];
    }

    // var_dump($command);
    $conn  = getDbConn();
    $query = "INSERT INTO movements (userId, type_, fromComponent, toComponent, timestamp_, amount) "
        . "VALUES (:userId, :type_, :fromComponent, :toComponent, :timestamp_, :amount);";

    $ret = $conn->executeStatement($query, [
        "userId" => $command[1],
        "type_" => $command[2],
        "fromComponent" => intval($command[3]),
        "toComponent" => intval($command[4]),
        "timestamp_" => timestampToDateTime(intval($command[5])),
        "amount" => floatval($command[6])
    ]);
    return [ strval($conn->lastInsertId()) ];
}

// HAVOC: there is no check at all that the user who is logged in has anything
// to say about the components they edit.
// Maybe the only way forward is to say each movement and each component belongs
// to a user, and then voluntarily you can observe the statements from others?
// The situation where each local edit is automatically accepted globally doesn't work
// Neither is the opposite situation, where each edit needs to be approved.
// Maybe a federated application automatically has complex access control.

// UNUSED:
function ensureMovementsLookalikeGroup($context, $movement, $numNeeded)
{
    if (!isset($context["user"])) {
        return ["User not found or wrong password"];
    }
    // for instance,
    // importing a lookalike group from for instance a savings account
    // make sure the number of movements is correct
    // make sure the right statements exist.
    $conn  = getDbConn();
    $query = "SELECT m.*, s.* FROM "
        . "movements m INNER JOIN statements s ON m.id = s.movementid WHERE "
        . "type_ = :type_ AND fromComponent = :fromComponent AND toComponent = :toComponent AND "
        . "m.timestamp_ >= :mintimestamp_ AND m.timestamp_ <= :maxtimestamp_ AND amount = :amount;";
    $fields = [
        "type_" => $movement["type_"],
        "fromComponent" => $movement["fromComponent"],
        "toComponent" => $movement["toComponent"],
        "mintimestamp_" => timestampToDateTime($movement["timestamp_"] - 12 * 3600),
        "maxtimestamp_" => timestampToDateTime($movement["timestamp_"] + 12 * 3600),
        "amount" => $movement["amount"]
    ];
    $ret = $conn->executeQuery($query, $fields);
    $ass = $ret->fetchAllAssociative();
    $arr = [];
    for ($i = 0; $i < count($ass); $i++) {
        array_push($arr, $ass[$i]["id"]);
    }
    if (count($arr) > $numNeeded) {
        echo "Weird, queried for:";
        echo $query;
        var_dump($fields);
        echo "Have " . count($arr) . " need $numNeeded";
        var_dump($arr);
        throw new Error('Too many entries already for this lookalike group, don\'t know what to do!');
    } elseif (count($arr) == $numNeeded) {
        // echo ("Already have $numNeeded movements with these details!\n");
    } else {
        $query = "INSERT INTO movements (type_, fromComponent, toComponent, timestamp_, amount) "
        . "VALUES (:type_, :fromComponent, :toComponent, :timestamp_, :amount);";
        $numToAdd = $numNeeded - count($arr);
        for ($i = 0; $i < $numToAdd; $i++) {
            $conn->executeStatement($query, [
                "type_" => $movement["type_"],
                "fromComponent" => $movement["fromComponent"],
                "toComponent" => $movement["toComponent"],
                "timestamp_" => timestampToDateTime($movement["timestamp_"]),
                "amount" => $movement["amount"]
            ]);
            $created = $conn->lastInsertId();
            // echo("Movement $created was created\n");
            array_push($arr, $created);
        }
    }
    return $arr;
}

function createMultipleMovement($userId, $type_, $fromComponent, $toComponent, $timestamp_, $amount)
{
    $conn  = getDbConn();
    $conn->executeQuery(
        "INSERT INTO movements (userId, type_, fromComponent, toComponent,timestamp_, amount) VALUES (:userId, :type_, :fromComponent, :toComponent, :timestamp_,:amount) ",
        [ "userId" => $userId, "type_" => $type_, "fromComponent" => $fromComponent, "toComponent" => $toComponent, "timestamp_" => $timestamp_, "amount" => $amount]
    );
    return [ strval($conn->lastInsertId()) ];
}
