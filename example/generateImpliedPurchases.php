<?php
require_once("../utils.php");

// ...
$areg = readAreg($argv[1]);
$suppliers = readSuppliers($argv[2]);
foreach ($areg as $entry) {
    $matched = false;
    foreach ($suppliers["goods"] as $match => $budget) {
      $pos = strpos($entry["description"], $match);
      if ($pos === false) {
        $pos = strpos($entry["contraAccount"], $match);
      }
      if ($pos !== false) {
        if (isset($suppliers["accounts"][$entry["contraAccount"]])) {
            $trustline = $suppliers["@me"].":".$suppliers["accounts"][$entry["contraAccount"]];
        } else {
          $trustline = $suppliers["@me"].":".$entry["contraAccount"];
        }
        printTransaction([
          "date" => $entry["date"],
          "comment" => "[PURCHASE]:".$entry["description"],
          "account1" => $suppliers["@me"].":".$budget,
          "account2" => $trustline,
          "amount" => -$entry["amount"],
        ]);
        printTransaction([
          "date" => $entry["date"],
          "comment" => "[PAYMENT]:".$entry["description"],
          "account1" => $trustline,
          "account2" => $entry["contraAccount"],
          "amount" => -$entry["amount"],
        ]);
        $matched = true;
        break;
      }
    }
    if (!$matched) {
      echo "Could not match this one!\n";
      var_dump($entry);
      exit();
    }
}