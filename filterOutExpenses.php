<?php
require_once("./utils.php");

// ...
$journals = [
  readJournal($argv[1]),
  readJournal($argv[2]),
];
// var_dump($journals);

// for each expense, find the transaction with the same date and amount
$matched = [];
for ($j=0; $j < count($journals[1]["entries"]); $j++) {
  $thisDate = $journals[1]["entries"][$j]["date"];
  if (!isset($matched[$thisDate])) {
    $matched[$thisDate] = [];
  }
  array_push($matched[$thisDate], $journals[1]["entries"][$j]);
}

var_dump($matched);
