<?php
require_once("./utils.php");

// ...
$journals = [
  readJournal($argv[1]),
  readJournal($argv[2]),
];
// var_dump($journals);

$matched = [];
for ($i=0; $i < 2; $i++) {
  $thisDate = null;
  for ($j=0; $j < count($journals[$i]["entries"]); $j++) {
    if ($journals[$i]["entries"][$j]["account2"] == $journals[1 - $i]["accountName"]) {
      if ($journals[$i]["entries"][$j]["date"] != $thisDate) {
        $thisDate = $journals[$i]["entries"][$j]["date"];
        if (!isset($matched[$thisDate])) {
          $matched[$thisDate] = [];
        }
        $matched[$thisDate][$i] = 0;
      }  
      $matched[$thisDate][$i] += $journals[$i]["entries"][$j]["amount"];
    }
  }
}
// var_dump($matched);
foreach ($matched as $date => $pair) {
  if (isset($matched[$date][0]) && isset($matched[$date][1]) && $matched[$date][0] != -$matched[$date][1]) {
    var_dump($date);
    var_dump($pair);
    exit();
  } else {
    echo "OK " . $date . " " . (isset($matched[$date][0]) ? $matched[$date][0] : "null") . " " . (isset($matched[$date][1]) ? $matched[$date][1] : "null") . "\n";
  }
   
}