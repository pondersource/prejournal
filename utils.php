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

function readAreg($filename) {
  $ret = [];
  $lines = explode("\n", file_get_contents($filename));
  $first = true;
  foreach ($lines as $line) {
    if ($first) {
      $first = false;
      continue;
    }
    if (strlen($line) === 0) {
      continue;
    }
    $cursor = strpos($line, " ");
    $dateStr = substr($line, 0, $cursor);

    $rest = trim(substr($line, $cursor));
    $cursor = strrpos($rest, " ");
    $newBalanceStr = substr($rest, $cursor + 1);

    $rest = trim(substr($rest, 0, $cursor));
    $cursor = strrpos($rest, " ");
    $amountStr = substr($rest, $cursor + 1);

    $rest = trim(substr($rest, 0, $cursor));
    $cursor = strrpos($rest, "  ");
    $contraAccountStr = substr($rest, $cursor + 2);

    $descriptionStr = trim(substr($rest, 0, $cursor));
    array_push($ret, [
      "date" => $dateStr,
      "newBalance" => $newBalanceStr,
      "amount" => $amountStr,
      "contraAccount" => $contraAccountStr,
      "description" => $descriptionStr,
    ]);
  }
  return $ret;
}

function readJournal($filename, $openingBalance = true) {
  $lines = explode("\n", file_get_contents($filename));
  $line1Parts = explode("  ", trim($lines[1]));
  $i = 0;
  $ret = [];
  if ($openingBalance) {
      $ret = [
        "openingBalanceDate" => explode(" ", $lines[0])[0],
        "accountName" => substr($line1Parts[0], 1, strlen($line1Parts[0]) -2),
        "openingBalance" => floatval($line1Parts[1]),
        "entries" => [],
      ];
      $i=3;
  }
  for (; $i < count($lines) - 2;) {
    $headLine = trim($lines[$i]);
    $lineOneParts = explode("  ", trim($lines[$i + 1]));
    var_dump($lineOneParts);
    $lineTwo = trim($lines[$i + 2]);
    $splitComment = strpos($headLine, " ");
    array_push($ret["entries"], [
      "date" => substr($headLine, 0, $splitComment),
      "comment" => trim(substr($headLine, $splitComment)),
      "account1" => $lineOneParts[0],
      "account2" => $lineTwo,
      "amount" => floatval($lineOneParts[1]),
      "balanceAfter" => (count($lineOneParts) >= 3 ? floatval(substr($lineOneParts[2], 1)) : null),
    ]);
    $i += 3;
    while ($i < count($lines) && strlen($lines[$i]) == 0) {
      $i++;
    }
  }
  return $ret;
}

function readSuppliers($filename) {
  return json_decode(file_get_contents($filename), true);
}
