<?php
require_once("./utils.php");

// ...
$journal = readJournal($argv[1]);
$suppliers = readSuppliers($argv[2]);
foreach ($journal["entries"] as $entry) {
    foreach ($suppliers as $match => $budget) {
      $pos = strpos($entry["comment"], $match);
        if ($pos) {
            printTransaction([
              "date" => $entry["date"],
              "comment" => "Implied transaction (" . $match . ")",
              "account1" => $entry["account2"],
              "account2" => $budget,
              "amount" => $entry["amount"],
            ]);
        }
    }
}