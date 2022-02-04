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
        "amount" => trim(substr($line, $split)),
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
