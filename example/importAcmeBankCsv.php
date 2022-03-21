<?php
require_once("../utils.php");

function parseDate($obj) {
  $parts = explode("-", $obj["journalDate"]);
  $day = $parts[0];
  $month = $parts[1];
  $year = $parts[2];
 
  return date("Y/m/d", mktime(0, 0, 0, $month, $day, $year));
}

function normalizeAccountName($str) {
  return preg_replace('/\s+/', ' ', str_replace("*", " ", trim($str)));
}
function parseAccount2($obj) {
  if (strlen($obj["contraAccountNumber"]) > 0) {
    return $obj["contraAccountNumber"];
  }
  if (strlen($obj["contraAccountName"]) > 0) {
    return $obj["contraAccountName"];
  }
  if ($obj["globalTransactionCode"] == "BEA") {
    return normalizeAccountName(substr($obj["description"], 1, 22));
  }
  if ($obj["globalTransactionCode"] == "COR") {
    return normalizeAccountName(substr($obj["description"], 1, 22));
  }
  if ($obj["globalTransactionCode"] == "RNT") {
    return "Acme Bank Rente";
  }
  if ($obj["globalTransactionCode"] == "BTL") {
    $descriptionParts = explode(" ", substr($obj["description"], 1, strlen($obj["description"]) - 2));
    if (($descriptionParts[0] == "EUR") &&
      ($descriptionParts[2] == "van") &&
      ($descriptionParts[4] == "van")) {
      return $descriptionParts[3];
    }
  }
  if ($obj["globalTransactionCode"] == "GEA") {
    return normalizeAccountName("Geldautomaat " . normalizeAccountName(substr($obj["description"], 1, 22)));
  }
  if ($obj["globalTransactionCode"] == "KST") {
    return normalizeAccountName("Kosten " . substr($obj["description"], 1, strlen($obj["description"]) - 2));
  }
  if ($obj["globalTransactionCode"] == "DIV") {
    return normalizeAccountName("Diversen " . substr($obj["description"], 1, strlen($obj["description"]) - 2));
  }
  var_dump($obj);
  exit();
  return "UNKNOWN " . $obj["globalTransactionCode"];
}

function parseDescription($obj) {
  return str_replace("*", " ", $obj["globalTransactionCode"] . "  " . $obj["description"]);
}

function importAcmeCsv($filename) {
  $ACME_BANK_CSV_COLUMNS = [
    'journalDate',
    'clientAccount',
    'contraAccountNumber',
    'contraAccountName',
    'amount',
    'globalTransactionCode',
    'description',
  ];
  $lines = explode("\n", file_get_contents($filename));
  foreach($lines as $line) {
    if (strlen($line) > 0) {
      $cells = explode(",", $line);
      $obj = [];
      for ($i = 0; $i < count($cells); $i++) {
        $obj[$ACME_BANK_CSV_COLUMNS[$i]] = trim($cells[$i]);
      }
      printTransaction([
        "date" => parseDate($obj),
        "comment" => parseDescription($obj),
        "account1" => $obj["clientAccount"],
        "account2" => parseAccount2($obj),
        "amount" => floatval($obj["amount"]),
      ]);
    }
  }
}


// ...
importAcmeCsv($argv[1]);