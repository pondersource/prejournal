<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

// loan "21 January 2022" stichting 5000

function loan($context, $command)
{
    if (isset($context["user"])) {
        $payer = $context["user"]["username"];
        $timestamp = strtotime($command[1]);
        $receiver = $command[2];
        $amount = $command[3];

        /* Create Movement */
        $movementId = intval(createMovement($context, [
      "create-movement",
      "payment",
      strval(getComponentId($payer)),
      strval(getComponentId($receiver)),
      $timestamp,
      $amount,
      "payment related to loan"
    ])[0]);

        /* Create Statement */
        $statementId = intval(createStatement($context, [
      "create-statement",
      $movementId,
      $timestamp,
    ])[0]);
        return ["Added loan, movement $movementId, statement $statementId"];
    } else {
        return ["User not found or wrong password"];
    }
}
