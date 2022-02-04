<?php

function printOpeningBalance($params) {
  echo($params["date"] . "  Opening balance\n");
  echo("  (" . $params["account1"] . ")  " . $params["balance"] . "\n\n");
}


// date - string
// comment - string
// account1 - string
// account2 - string
// amount - float
// balanceAfter? - float
function printTransaction($params) {
  echo($params["date"] . "  " . $params["comment"] . "\n");
  echo("  " . $params["account1"] . "  " . $params["amount"] . (isset($params["balanceAfter"]) ? $params["balanceAfter"] : "") . "\n");
  echo("  " . $params["account2"] . "\n\n");
}

function readJournal($filename) {
  $lines = explode("\n", file_get_contents($filename));
  $cursor = -1;
  $ret = [];
  for ($i=0; $i < count($lines); $i++) {
    if (strlen($lines[$i]) == 0) {
      continue;
    }
    if ($lines[$i][0] == " " || $lines[$i][0] == "\t") {
      $line = trim($lines[$i]);
      $split = strpos($line, "  ");
      array_push($ret[$cursor]["entries"], [
        "account" => trim(substr($line, 0, $split)),
        "amount" => floatval(trim(substr($line, $split))),
      ]);
    } else {
      $cursor++;
      $split = strpos($lines[$i], " ");
      $ret[$cursor] = [
        "date" => trim(substr($lines[$i], 0, $split)),
        "description" => trim(substr($lines[$i], $split)),
        "entries" => []
      ];
    }
  }
  return $ret;
}

function readJson($filename) {
  return json_decode(file_get_contents($filename), true);
}

function findChains($journal) {
  $chains = [];
  foreach($journal as $transaction) {
    if (!isset($chains[$transaction["date"]])) {
      $chains[$transaction["date"]] = [];
    }
    if (count($transaction["entries"]) != 2) {
      echo "\nPANIC: Not a two-entry transaction!\n";
      var_dump($transaction);
      exit();
    }
    if ($transaction["entries"][0]["amount"] == 0) {
      echo "\nPANIC: Not a non-zero transaction!\n";
      var_dump($transaction);
      exit();
    }
    if ($transaction["entries"][0]["amount"] + $transaction["entries"][1]["amount"] != 0) {
      echo "\nPANIC: Not a balanced transaction!\n".($transaction["entries"][0]["amount"] + $transaction["entries"][1]["amount"]);
      var_dump($transaction);
      exit();
    }
    if ($transaction["entries"][0]["amount"] < 0) {
      $from = $transaction["entries"][0]["account"];
      $to = $transaction["entries"][1]["account"];
      $amount = $transaction["entries"][1]["amount"];
    } else {
      $from = $transaction["entries"][1]["account"];
      $to = $transaction["entries"][0]["account"];
      $amount = $transaction["entries"][0]["amount"];
    }
    if (!isset($chains[$transaction["date"]])) {
      $chains[$transaction["date"]] = [];
    }
    if (!isset($chains[$transaction["date"]][strval($amount)])) {
      $chains[$transaction["date"]][strval($amount)] = [];
    }
    array_push($chains[$transaction["date"]][strval($amount)], [$from, $to]);
  }
  foreach($chains as $date => $d) {
    // echo "\nDate: ".$date."\n";
    foreach($chains[$date] as $amountStr => $da) {
      // echo "\nAmount: ".$amountStr."\n";
      // var_dump($chains[$date][$amountStr]);
      do {
        $reduced = false;
        for ($i = 0; $i < count($chains[$date][$amountStr]); $i++) {
          if($reduced) {
            break;
          }
          // echo "\ni: ".$i."\n";
          for ($j = 0; $j < count($chains[$date][$amountStr]); $j++) {
            if($reduced) {
              break;
            }
            // echo "\nj: ".$j."\n";
            if ($chains[$date][$amountStr][$i][count($chains[$date][$amountStr][$i]) - 1] == $chains[$date][$amountStr][$j][0]) {
                // echo "Reduce! ".$i." - ".$j."\n";
                $reduced = true;
                $new = [];
                for ($k = 0; $k < count($chains[$date][$amountStr]); $k++) {
                    if ($k == $i) {
                        array_push($new, array_merge($chains[$date][$amountStr][$i], array_slice($chains[$date][$amountStr][$j], 1)));
                    } elseif ($k != $j) {
                        array_push($new, $chains[$date][$amountStr][$k]);
                    }
                }
                // echo "Old:\n";
                // var_dump($chains[$date][$amountStr]);
                // echo "New:\n";
                // var_dump($new);
                $chains[$date][$amountStr] = $new;
            }
          }
        }
      } while($reduced);
      // echo "Sequences for ".$date." - ".$amountStr.":\n";
      // var_dump($chains[$date][$amountStr]);
    }
  }
  return $chains;
}
