<?php

require_once "src/utils.php";

class Books {
  private $earliest = 9999999999;
  private $latest = 0;
  private $seriesLiquid = [];
  private $seriesLiquidCredit = [];
  private $seriesLiquidCreditAssets = [];
  private $step = 5;

  private function moveIntoView($timestamp) {
    if ($this->earliest > $timestamp) {
      $this->earliest = $timestamp;
    }
    if ($this->latest < $timestamp) {
      $this->latest = $timestamp;
    }
  }
  
  public function getSourceDoc() {
    $this->seriesLiquid = [1,2,3];
    $this->seriesLiquidCredit = [4,5,6];
    $this->seriesLiquidCreditAssets = [7,8,9];
    
    // var_dump($_SERVER['argv']);
    $fileName =  $_SERVER['argv'][1];
    $pj2JSON = file_get_contents($fileName);
    $entries = json_decode($pj2JSON, true);
    $this->moveIntoView(dateTimeToTimestamp($entries[0]["from"]));
    $this->moveIntoView(dateTimeToTimestamp($entries[0]["to"])); 
  }
  
  public function printBooks() {
    echo 'Books = ' . json_encode([
      "seriesLiquid" => $this->seriesLiquid,
      "seriesLiquidCredit" => $this->seriesLiquidCredit,
      "seriesLiquidCreditAssets" => $this->seriesLiquidCreditAssets,
      "step" => $this->step,
      "startDate" => timestampToDateTime($this->earliest),
      "endDate" => timestampToDateTime($this->latest),
    ]) . "\n";
  }
}
  
  
// ...
$data = new Books();
$data->getSourceDoc();
$data->printBooks();
  