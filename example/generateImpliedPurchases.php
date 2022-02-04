<?php
require_once("../utils.php");

// ...
$areg = readAreg($argv[1]);
$suppliers = readSuppliers($argv[2]);
foreach ($areg as $entry) {
    $matched = false;
    foreach ($suppliers as $match => $budget) {
      $pos = strpos($entry["description"], $match);
      if ($pos === false) {
        $pos = strpos($entry["contraAccount"], $match);
      }
      if ($pos !== false) {
          printTransaction([
            "date" => $entry["date"],
            "comment" => $entry["description"],
            "account1" => $entry["contraAccount"],
            "account2" => $budget,
            "amount" => $entry["amount"],
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