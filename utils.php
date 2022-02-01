<?php

function printOpeningBalance($params) {
  echo($params["date"] . "  Opening balance\n");
  echo("  (" . $params["account1"] . ")  " . $params["balance"] . "\n\n");
}

function printTransaction($params) {
  echo($params["date"] . "  " . $params["comment"] . "\n");
  echo("  " . $params["account1"] . "  " . $params["amount"] . "  =" . $params["balanceAfter"] . "\n");
  echo("  " . $params["account2"] . "\n\n");
}