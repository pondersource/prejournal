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
        . "timestamp_ >= :mintimestamp_ AND timestamp_ <= :maxtimestamp_ AND amount = :amount;";
    $fields = [
        "type_" => $movement["type_"],
        "fromComponent" => $movement["fromComponent"],
        "toComponent" => $movement["toComponent"],
        "mintimestamp_" => timestampToDateTime($movement["timestamp_"] - 12 * 3600),
        "maxtimestamp_" => timestampToDateTime($movement["timestamp_"] + 12 * 3600),
        "amount" => $movement["amount"]
    ];
    $ret = $conn->executeQuery($query, $fields);
    $arr = $ret->fetchAllAssociative();
    if (count($arr) > 1) {
        throw new Error("multiple movements match this!");
    }
    if (count($arr) > $numNeeded) {
        throw new Error('Too many entries already for this lookalike group, don\'t know what to do!');
    } else if (count($arr) == $numNeeded) {
        echo ("Already have $numNeeded movements with these details!");
    } else {
        $query = "INSERT INTO movements (type_, fromComponent, toComponent, timestamp_, amount) "
        . "VALUES (:type_, :fromComponent, :toComponent, :timestamp_, :amount);";
        $numToAdd = $numNeeded - count($arr);
        echo ("Have " . count($arr) . " movements with these details, adding $numToAdd!");

        for ($i = 0; $i < $numToAdd; $i++) {
            $conn->executeStatement($query, [
                "type_" => $movement["type_"],
                "fromComponent" => $movement["fromComponent"],
                "toComponent" => $movement["toComponent"],
                "timestamp_" => timestampToDateTime($movement["timestamp_"]),
                "amount" => $movement["amount"]
            ]);
            array_push($arr, $conn->lastInsertId());
        }
    }
    return $arr;
}

function createMultipleMovement($type_, $fromComponent, $toComponent, $timestamp_, $amount, $description) {
    $conn  = getDbConn();
    $conn->executeQuery(
        "INSERT INTO movements (type_, fromComponent, toComponent,timestamp_, amount,description) VALUES (:type_, :fromComponent, :toComponent, :timestamp_,:amount, :description) ",
        [ "type_" => $type_, "fromComponent" => $fromComponent, "toComponent" => $toComponent, "timestamp_" => $timestamp_, "amount" => $amount, "description" => $description]
    );
    return [ strval($conn->lastInsertId()) ];
}
