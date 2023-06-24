<?php

require_once(__DIR__ . '/../utils.php');

const DAYS_PER_YEAR = 365.24;
// echo "DAYS_PER_YEAR days per year\n";
const WEEKS_PER_YEAR = DAYS_PER_YEAR / 7;
// echo "WEEKS_PER_YEAR weeks per year\n";
const WEEKS_PER_MONTH = WEEKS_PER_YEAR / 12;
// echo "WEEKS_PER_MONTH weeks per month\n";

function dateFromEntry($entry) {
    if (isset($entry["date"])) {
        return $entry["date"];
    } else if (isset($entry["from"])) {
        return $entry["from"];
    } else {
        throw new Exception('no date and no from in entry ' . var_export($entry, true));
    }
}

abstract class PrejournalEntry {
  // private $type;

  // private $worker;
  // private $user;
  // private $payer;

  // private $date;
  // private $from;
  // private $to;
  // private $paid;
  // private $reimbursed;

  // private $organization;
  // private $project;
  // private $supplier;

  // private $amount;
  // private $hours;
  // private $description;

  protected $fields;
  function __construct($obj) {
    $this->fields = $obj;
  }

  abstract function toJournalEntries($contract);
}

class WorkedPrejournalEntry extends PrejournalEntry {
  function toJournalEntries($contract) {
    $hoursPerWeek = $contract["hours"];
    // echo "$hoursPerWeek hours per week\n";/
    $salaryPerMonth = $contract["amount"];
    // echo "$salaryPerMonth salary per month\n";
    $salaryPerWeek = $salaryPerMonth / WEEKS_PER_MONTH;
    // echo "$salaryPerWeek salary per week\n";
    $salaryPerHour = $salaryPerWeek / $hoursPerWeek;
    // echo "$salaryPerHour salary per hour\n";
    return [new JournalEntry([
      "date" => $this->fields["date"],
      "account1" => $this->fields["worker"],
      "account2" => $this->fields["organization"] . ":" . $this->fields["project"],
      "amount" => $this->fields["hours"] * $salaryPerHour,
      "description" => "worked"
    ])];
  }
}

class SalaryPrejournalEntry extends PrejournalEntry {
  function toJournalEntries($contract) {
    return [];
  }
}
class ContractPrejournalEntry extends PrejournalEntry {
  function toJournalEntries($contract) {
    return [];
  }
}
class ExpensePrejournalEntry extends PrejournalEntry {
  function toJournalEntries($contract) {
    return [];
  }
}
class LoanPrejournalEntry extends PrejournalEntry {
  function toJournalEntries($contract) {
    return [];
  }
}
class IncomePrejournalEntry extends PrejournalEntry {
  function toJournalEntries($contract) {
    return [];
  }
}

function makePrejournalEntry($obj) {
  switch ($obj["type"]) {
    case "worked": return new WorkedPrejournalEntry($obj);
    case "salary": return new SalaryPrejournalEntry($obj);
    case "contract": return new ContractPrejournalEntry($obj);
    case "expense": return new ExpensePrejournalEntry($obj);
    case "loan": return new LoanPrejournalEntry($obj);
    case "income": return new IncomePrejournalEntry($obj);
    default: throw new Exception("Unknown PJ2 entry type $obj[type]");
  }
}

class JournalEntry {
  public $date;
  public $account1;
  public $account2;
  public $amount;
  public $description;
  function __construct($obj) {
    $this->date = dateTimeToPta($obj["date"]);
    $this->account1 = $obj["account1"];
    $this->account2 = $obj["account2"];
    $this->amount = $obj["amount"];
    $this->description = $obj["description"];
  }
}

function formatJournalEntry($entry) {
   return "$entry->date $entry->description\n"
    . "    $entry->account1  $entry->amount\n"
    . "    $entry->account2\n";
}

function entryToPta($entry, $contract) {
  $JournalEntries = $entry->toJournalEntries($contract);
  $arr = array_map("formatJournalEntry", $JournalEntries);
  return implode("\n\n", $arr);
}

function entrySortCb($a, $b) {
    // echo "comparing " . var_export($a, true) . " to " . var_export($b, true) . "\n";
    $aDateStr = dateFromEntry($a);
    $bDateStr = dateFromEntry($b);
    
    if (gettype($aDateStr) != "string") {
        throw new Exception("no string date on a");
    }
    if (gettype($bDateStr) != "string") {
        throw new Exception("no string date on b");
    }
    $adate = new DateTime($aDateStr);
    $astamp = $adate->getTimestamp();
    $bdate = new DateTime($bDateStr);
    $bstamp = $bdate->getTimestamp();
    
    return ($astamp < $bstamp) ? -1 : 1;
}

// $test  = [
//     [ "date" => '24 jan 2022'],
//     [ "date" => '1 nov 2021']
// ];
// var_export($test);
// usort($test, 'entrySortCb');
// var_export($test);
// process.exit();

class Journal {

  private $entries = [];
  private $knownContracts = [];

  function __construct() {
    $this->knownContracts = [
      // FIXME: for now you'll have to hard-code contracts here
    ];
  }

  function addEntries($newEntries) {
    for ($i = 0; $i < count($newEntries); $i++) {
        $this->entries[] = $newEntries[$i];
        if ($newEntries[$i]["type"] == "contract") {
          echo "Got contract for " . $newEntries[$i]["worker"];
          if (!isset($this->knownContracts[$newEntries[$i]["worker"]])) {
            $this->knownContracts[$newEntries[$i]["worker"]] = [];
          }
          array_push($this->knownContracts[$newEntries[$i]["worker"]], $newEntries[$i]);
        }
    }
  }

  function toPta() {
    usort($this->entries, 'entrySortCb');
    $highestDateYet = 0;
    // FIXME: $contract = $this->knownContracts["some-worker-name"][0];
    for ($i = 0; $i < count($this->entries); $i++) {
        $obj = makePrejournalEntry($this->entries[$i]);
        echo entryToPta($obj, $contract);
        $thisDate = new DateTime(dateFromEntry($this->entries[$i]));
        $thisStamp = $thisDate->getTimestamp();
        if ($thisStamp < $highestDateYet) {
            throw new Exception("not sorted!");
        }
        $highestDateYet = $thisStamp;
    }
  }
  function loadSources($folderPath) {
    // ignore the warning, we'll check for return value false below.
    set_error_handler(function() { /* ignore errors */ });
    $fileNames = scandir($folderPath);
    restore_error_handler();
    if ($fileNames == false) {
      echo "Folder not found: '$folderPath'\n";
      exit();
    }
  
    for ($i = 0; $i < count($fileNames); $i++) {
      if (($fileNames[$i] == ".") ||  ($fileNames[$i] == "..")) {
        continue;
      }
      if (!str_ends_with($fileNames[$i], ".pj2")) {
        echo "Filename '" . $fileNames[$i] . "' in '" . $folderPath . "' does not have a .pj2 extension .\n";
        exit(1);
      }
      $contents = file_get_contents($folderPath . $fileNames[$i]);
      try {
        $pj2Entries =  json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        // var_dump($pj2Entries);
       debug("Contents of " . $folderPath . $fileNames[$i] . " parsed as " . count($pj2Entries) . " PJ2 flat.\n");
  
      } catch (Exception $e) {
        echo "Contents of " . $folderPath . $fileNames[$i] . " is not JSON.\n";
        exit(1);
      }
      $this->addEntries($pj2Entries);
    }
  }
}