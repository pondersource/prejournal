<?php

function printBooks() {
  $seriesLiquid = [1,2,3];
  $seriesLiquidCredit = [4,5,6];
  $seriesLiquidCreditAssets = [7,8,9];
  
  echo 'Books = ' . json_encode([
    "seriesLiquid" => $seriesLiquid,
    "seriesLiquidCredit" => $seriesLiquidCredit,
    "seriesLiquidCreditAssets" => $seriesLiquidCreditAssets,
    "step" => 5,
    "startDate" => "30 jun 2020",
    "endDate" => "30 jun 2022",
  ]) . "\n";
}


// ...
printBooks();
