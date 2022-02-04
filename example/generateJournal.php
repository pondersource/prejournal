<?php
require_once("../utils.php");

// ...
$journal = readJournal($argv[1]);
$config = readJson($argv[2]);
$report = readJson($argv[3]);
$chains = findChains($journal);
// var_dump($journal);
// var_dump($config);
// var_dump($report);
// var_dump($chains);

function stripSystem($accountName, $report) {
  if (substr($accountName, 0, strlen($report["system"])) == $report["system"]) {
    return substr($accountName, strlen($report["system"]) + 1);
  }
  return $accountName;
}

function getLabel($accountName, $report) {
  $tmp = stripSystem($accountName, $report);
  if (isset($report["labels"][$tmp])) {
    return $report["labels"][$tmp];
  }
  return $tmp;
}

foreach($chains as $date => $d) {
  foreach($chains[$date] as $amount => $a) {
    foreach ($chains[$date][$amount] as $sequence) {
      $from = $sequence[0];

      $to = $sequence[count($sequence)-1];
      echo "Date:".$date."\n";
      if(!isset($report["positive"][$from])) {
        echo "From income:".getLabel($from, $report)."\n";
      } else if($report["positive"][$from] === true) {
          echo "From assets:".getLabel($from, $report)."\n";
      } else if($report["positive"][$from] === false) {
        echo "From liabilities:".getLabel($from, $report)."\n";
      }
      if(!isset($report["positive"][$to])) {
        echo "To expenses:".getLabel($to, $report)."\n";
      } else if ($report["positive"][$to] === true) {
        echo "To assets:".getLabel($to, $report)."\n";
      } else if ($report["positive"][$to] === false) {
        echo "To liabilities:".getLabel($to, $report)."\n";
      }
      echo "Amount:".$amount."\n";
    }
  }
}