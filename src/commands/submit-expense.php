<?php declare(strict_types=1);
  require_once(__DIR__ . '/../platform.php');

  /*
  E.g.: php src/index.php submit-expense "28 August 2021" "stichting" "Dutch railways" "Degrowth Conference train tickets" "transport" 100 "michiel"

  1st transaction michiel -> Dutch railways (PAYMENT)
  2nd transaction Dutch railways->stichting (INVOICE)
*/
function submitExpense($context, $command) {
  if (isset($context["user"])) {

    $timestamp = strtotime($command[1]);
    $amount = $command[7];
    $payer = $command[8]; /* michiel  */
    $shop = $command[2]; /* stichting  */
    $receiver = $command[4]; /* Dutch railway  */

    $components = array(
      $payer,
      $shop,
      $receiver
    );

    $componentsIDs = array(
      "payer_id",
      "shop_id",
      "receiver_id"
    );

    /* Create 3 Components*/
    for($i = 0; $i < count($components); $i++){
      $componentId = intval(createComponent($context, [
        "create-component",
        $components[$i],
      ])[0]);
      $componentsIDs[$i] = $componentId;
    }

    /* We have two types of transactions  */
    $type = array(
      "payment",
      "invoice"
    );

    /* Create 2 Movements*/
    $movementId_payment = intval(createMovement($context, [
      "create-movement",
      $type[0],
      strval($payer),
      strval($receiver),
      $timestamp,
      $amount
    ])[0]);
  
    $movementId_invoice = intval(createMovement($context, [
      "create-movement",
      $type[1],
      strval($receiver),
      strval($shop),
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

  } else {
    return ["User not found or wrong password"];
  }
}




