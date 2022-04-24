<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');
  require_once(__DIR__ . '/helpers/createMovement.php');
  require_once(__DIR__ . '/helpers/createStatement.php');
  /*
  E.g.: php src/index.php submit-expense "28 August 2021" "stichting" "Dutch railways" "Degrowth Conference train tickets" "transport" 100 "michiel"

  1st transaction michiel -> Dutch railways (PAYMENT)
  2nd transaction Dutch railways->stichting (INVOICE)
*/
function submitExpense($context, $command) {
  if (isset($context["user"])) {
  
    $timestamp = strtotime($command[1]);
    $amount = $command[6];
    $payer = $command[7]; /* michiel  */
    $shop = $command[2]; /* stichting  */
    $receiver = $command[3]; /* Dutch railway  */

    /* We have two types of movements  */
    $type = array(
      "payment",
      "invoice"
    );

    /* Create 2 Movements */
    $movementId_payment = intval(createMovement($context, [
      "create-movement",
      $type[0],
      strval(getComponentId($payer)),
      strval(getComponentId($receiver)),
      $timestamp,
      $amount
    ])[0]);
  
    $movementId_invoice = intval(createMovement($context, [
      "create-movement",
      $type[1],
      strval(getComponentId($receiver)),
      strval(getComponentId($shop)),
      $timestamp,
      $amount
    ])[0]);

    /* Create 2 Statements*/
    $statementId_payment = intval(createStatement($context, [
      "create-statement",
      $movementId_payment ,
      $timestamp,
    ])[0]);

    $statementId_invoice = intval(createStatement($context, [
      "create-statement",
      $movementId_invoice ,
      $timestamp,
    ])[0]);

    return [
      "Created movements $movementId_payment and $movementId_invoice",
      "Created statements $statementId_payment and $statementId_invoice"
    ];
  } else {
    return ["User not found or wrong password"];
  }
}




