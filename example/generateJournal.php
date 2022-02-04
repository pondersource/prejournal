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

function multiplyPeriod($amount, $period) {
  if ($period == "1 month") {
    return $amount;
  }
  if ($period == "1 year") {
    return 12 * $amount;
  }
  if ($period == "1 week") {
    return 12 * $amount / 52;
  }
  if ($period == "1 day") {
    return 12 * $amount / 365;
  }
  if ($period == "1 hour") {
    return 12 * $amount / (365 * 24);
  }
  if ($period == "1 minute") {
    return 12 * $amount / (365 * 24 * 60);
  }
  if ($period == "1 second") {
    return 12 * $amount / (365 * 24 * 3600);
  }
}
function generateDepreciationTransactions($dateStr, $asset, $total, $step, $depreciationPeriod, $reportPeriod) {
  $thisDate = date('Y-m-d', strtotime("+".$depreciationPeriod, strtotime($dateStr)));
  $left = $total;
  do {
    printTransaction([
      "date" => $thisDate,
      "account1" => "expenses:depreciation",
      "account2" => $asset,
      "amount" => min($step, $left),
      "comment" => "Depreciation"
    ]);
    $thisDate = date('Y-m-d', strtotime("+".$depreciationPeriod, strtotime($thisDate)));
    $left -= $step;
  } while ($left > 0 && str_starts_with($thisDate, $reportPeriod));
}
foreach($chains as $date => $d) {
  foreach($chains[$date] as $amount => $a) {
    foreach ($chains[$date][$amount] as $sequence) {
      // date - string
      // comment - string
      // account1 - string
      // account2 - string
      // amount - float
      // balanceAfter? - float
      $transaction = [
        "date" => $date,
        "comment" => "Generated",
        "amount" => $amount
      ];
      $from = $sequence[0];

      $to = $sequence[count($sequence)-1];
      if(!isset($report["positive"][$from])) {
        $transaction["account2"] = "income:".getLabel($from, $report);
      } else if($report["positive"][$from] === true) {
        $transaction["account2"] = "assets:".getLabel($from, $report);
      } else if($report["positive"][$from] === false) {
        $transaction["account2"] = "liabilities:".getLabel($from, $report);
      }
      if(!isset($report["positive"][$to])) {
        $transaction["account1"] = "expenses:".getLabel($to, $report);
      } else if ($report["positive"][$to] === true) {
        if (isset($config["monthlyDepreciation"][$to])) {
          if ($amount < multiplyPeriod($config["monthlyDepreciation"][$to], $report["depreciationPeriod"])) {
            $transaction["account1"] = "expenses:".getLabel($to, $report);
          } else {
            $transaction["account1"] = "assets:".getLabel($to, $report);
            if($config["monthlyDepreciation"][$to] > 0) {
              generateDepreciationTransactions($date, "assets:".getLabel($to, $report), $amount, $config["monthlyDepreciation"][$to], $report["depreciationPeriod"], $report["reportPeriod"]);
            }
          }
        }
      } else if ($report["positive"][$to] === false) {
        $transaction["account1"] = "liabilities:".getLabel($to, $report);
      }
      printTransaction($transaction);
    }
  }
}