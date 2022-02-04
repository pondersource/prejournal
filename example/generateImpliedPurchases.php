<?php
require_once("../utils.php");

// ...
$journal = readJournal($argv[1]);
$suppliers = readJson($argv[2]);
$clientAccount = $argv[3];
foreach ($journal as $transaction) {
  if (count($transaction["entries"]) != 2) {
    echo "Panic! Not exactly two entries\n";
    var_dump($transaction);
    exit();
  }
  foreach($transaction["entries"] as $entry) {
    if ($entry["account"] != $clientAccount) {
      $contraAccount = $entry["account"];
      $amount = $entry["amount"];
      break;
    }
  }

  $matched = false;
  foreach ($suppliers["goods"] as $match => $budget) {
      $pos = strpos($transaction["description"], $match);
      if ($pos === false) {
          $pos = strpos($contraAccount, $match);
      }
      if ($pos !== false) {
        if (isset($suppliers["accounts"][$contraAccount])) {
            $trustline = $suppliers["@me"].":".$suppliers["accounts"][$contraAccount];
        } else {
            $trustline = $suppliers["@me"].":".$contraAccount;
        }
        printTransaction([
          "date" => $transaction["date"],
          "comment" => "[PURCHASE]:".$transaction["description"],
          "account1" => $suppliers["@me"].":".$budget,
          "account2" => $trustline,
          "amount" => $amount,
        ]);
        printTransaction([
          "date" => $transaction["date"],
          "comment" => "[PAYMENT]:".$transaction["description"],
          "account1" => $trustline,
          "account2" => $contraAccount,
          "amount" => $amount,
        ]);
        $matched = true;
        break;
      }
  }
  if (!$matched) {
    echo "Could not match this one!\n";
    var_dump($transaction);
    exit();
  }
}