<?php
require_once("./utils.php");

// ...
$journal = readJournal($argv[1]);
$suppliers = readSuppliers($argv[2]);
foreach ($journal["entries"] as $entry) {
    $matched = false;
    foreach ($suppliers as $match => $budget) {
      $pos = strpos($entry["comment"], $match);
      if ($pos === false) {
        $pos = strpos($entry["account2"], $match);
      }
      if ($pos !== false) {
          printTransaction([
            "date" => $entry["date"],
            "comment" => "Implied transaction (" . $match . ")",
            "account1" => $entry["account2"],
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