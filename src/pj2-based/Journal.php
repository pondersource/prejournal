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
  function getField($name) {
    if (isset($this->fields[$name])) {
      return $this->fields[$name];
    }
    throw new Exception("Field '$name' not set! Have: " . var_export($this->fields, true));
  }
  abstract function toJournalEntries($journal);
}

class WorkedPrejournalEntry extends PrejournalEntry {
  function getAmount($journal) {
    $contract = $journal->getContract([
      "organization" => $this->getField("organization"),
      "worker" => $this->getField("worker"),
      "date" => $this->getField("date")
    ]);
    $hoursPerWeek = $contract["hours"];
    // echo "$hoursPerWeek hours per week\n";/
    $salaryPerMonth = $contract["amount"];
    // echo "$salaryPerMonth salary per month\n";
    $salaryPerWeek = $salaryPerMonth / WEEKS_PER_MONTH;
    // echo "$salaryPerWeek salary per week\n";
    $salaryPerHour = $salaryPerWeek / $hoursPerWeek;
    // echo "$salaryPerHour salary per hour\n";
    return $this->fields["hours"] * $salaryPerHour;
  }

  function getMilestone($journal) {
    $income = $journal->getIncome([
      "organization" => $this->getField("organization"),
      "project" => $this->getField("project"),
      "date" => $this->getField("date")
    ]);
    return $this->fields["organization"] . ":" . $this->fields["project"] . ":" . $income["description"];
  }

  function toJournalEntries($journal) {
    return [
      // one entry for what the project owes the worker (at their salary hourly rate)
      new JournalEntry([
        "date" => $this->fields["date"],
        "account1" => "assets:billable work done:" . $this->getMilestone($journal),
        "account2" => "liabilities:accounts payable:" . $this->fields["worker"],
        "amount" => $this->getAmount($journal),
        "description" => "worked"
      ])
    ];
  }
}

class SalaryPrejournalEntry extends PrejournalEntry {
  function toJournalEntries($journal) {
    return [
      new JournalEntry([
        "date" => $this->getField("paid"),
        "account1" => "liabilities:accounts payable:" . $this->getField("worker"),
        "account2" => "assets:bank",
        "amount" => $this->getField("amount"),
        "description" => "worked"
      ]),
    ];
  }
}
class ContractPrejournalEntry extends PrejournalEntry {
  function toJournalEntries($journal) {
    return [];
  }
}
class ExpensePrejournalEntry extends PrejournalEntry {
  function toJournalEntries($journal) {
    return [];
  }
}
class LoanPrejournalEntry extends PrejournalEntry {
  function toJournalEntries($journal) {
    return [];
  }
}
class IncomePrejournalEntry extends PrejournalEntry {
  function toJournalEntries($journal) {
    $assigned =  $journal->getAssigned($this);
    $profit = $this->fields["amount"] - $assigned;
    // echo "FOUND ASSIGNED $assigned\n";
    return [
      // one entry to book away the "ready-product stock" that was built up for this milestone
      new JournalEntry([
        "date" => $this->fields["paid"],
        "account1" => "assets:bank",
        "account2" => "assets:billable work done:" . $this->fields["organization"] . ":" . $this->fields["project"] . ":" . $this->fields["description"],
        "amount" => $assigned,
        "description" => "worked"
      ]),
      // one entry for what the customer additionally owes the project (at the agreed billable hourly rate)
      new JournalEntry([
        "date" => $this->fields["paid"],
        "account1" => "assets:bank",
        "account2" => "income:profit",
        "amount" => $profit,
        "description" => "worked"
      ]),
    ];
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
  private $knownIncomes = [];
  private $assigned = [];

  function __construct() {
  }

  private function entryToPta($entry) {  
    $JournalEntries = $entry->toJournalEntries($this);
    $arr = array_map("formatJournalEntry", $JournalEntries);
    return implode("\n\n", $arr);
  }
  
  public function getAssigned($incomeEntry) {
    $incomeId = $this->getIncomeId([
      "organization" => $incomeEntry->getField("organization"),
      "project" => $incomeEntry->getField("project"),
      "description" => $incomeEntry->getField("description"),
    ]);
    if (!isset($this->assigned[$incomeId])) {
      return 0;
    }
    return $this->assigned[$incomeId];
  }

  public function getContract($query) {
    if (!isset($this->knownContracts[$query["worker"]])) {
      throw new Exception("No contracts found at all for worker '$worker'");
    }
    foreach($this->knownContracts[$query["worker"]] as $contract) {
      // echo "Checking query '" . var_export($query, true) . "' against contract '" . var_export($contract, true) . "'\n";
      if ($contract["organization"] !== $query["organization"]) {
        // echo "Contract organization  " . $contract["organization"] . " !== " . $query["organization"] . "\n";
        continue;
      }
      if (dateIsAfter($contract["from"], $query["date"])) {
        // echo "Contract started at '" . $contract["from"] . "' which is after '" . $query["date"] . "'\n";
        continue;
      }
      if (isset($contract["to"]) && dateIsBefore($contract["to"], $query["date"])) {
        // echo "Contract ended at  '" . $contract["to"] . "' which is before '" . $query["date"] . "'\n";
        continue;
      }
      // echo "Contract found! " . var_export($contract, true) . "\n";
      return $contract;
    }
    throw new Exception("Contract not found!");
  }

  private function getIncomeId($income) {
    return $income["organization"] . " : " . $income["project"] . " : " . $income["description"];
  }

  public function getIncome($query) {
    $fullProject = $query["organization"] . " : " . $query["project"];
    if (!isset($this->knownIncomes[$fullProject])) {
      throw new Exception("No incomes found at all for full project '$fullProject'");
    }
    foreach($this->knownIncomes[$fullProject] as $income) {
      // echo "Considering income '" . var_export($income, true) . "'\n";
      if ($income["organization"] !== $query["organization"]) {
        // echo "income organization  " . $income["organization"] . " !== " . $query["organization"] . "\n";
        continue;
      }
      if ($income["project"] !== $query["project"]) {
        // echo "income project  " . $income["project"] . " !== " . $query["project"] . "\n";
        continue;
      }
      if (dateIsAfter($income["from"], $query["date"])) {
        // echo "income started at  " . $income["from"] . " which is after " . $query["date"] . "\n";
        continue;
      }
      if (dateIsBefore($income["to"], $query["date"])) {
        // echo "income ended at  " . $income["to"] . " which is before " . $query["date"] . "\n";
        continue;
      }
      // echo "income found! " . var_export($income, true) . "\n";
      return $income;
    }
    throw new Exception("Income not found!");
  }

  function addEntries($newEntries) {
    for ($i = 0; $i < count($newEntries); $i++) {
        $this->entries[] = $newEntries[$i];
        if ($newEntries[$i]["type"] == "contract") {
          // echo "Got contract for " . $newEntries[$i]["worker"] . "\n";
          if (!isset($this->knownContracts[$newEntries[$i]["worker"]])) {
            $this->knownContracts[$newEntries[$i]["worker"]] = [];
          }
          array_push($this->knownContracts[$newEntries[$i]["worker"]], $newEntries[$i]);
        }
        if ($newEntries[$i]["type"] == "income") {
          $fullProject = $newEntries[$i]["organization"] . " : " . $newEntries[$i]["project"];
          // echo "Got income for '" . $fullProject . "'\n";
          if (!isset($this->knownIncomes[$fullProject])) {
            $this->knownIncomes[$fullProject] = [];
          }
          array_push($this->knownIncomes[$fullProject], $newEntries[$i]);
        }
    }
  }
  
  function assignWorkedToIncome() {
    foreach($this->entries as $entry) {
      if ($entry["type"] == 'worked') {
        $pjEntry = new WorkedPrejournalEntry($entry);
        $income = $this->getIncome([
          "organization" => $pjEntry->getField("organization"),
          "project" => $pjEntry->getField("project"),
          "date" => $pjEntry->getField("date")
        ]);
        $incomeId = $this->getIncomeId($income);
        if (!isset($this->assigned[$incomeId])) {
          $this->assigned[$incomeId] = 0;
        }
        $pjEntry = new WorkedPrejournalEntry($entry);
        $this->assigned[$incomeId] += $pjEntry->getAmount($this);
      }
    }
  }

  function toPta() {
    usort($this->entries, 'entrySortCb');
    $highestDateYet = 0;
    for ($i = 0; $i < count($this->entries); $i++) {
        $obj = makePrejournalEntry($this->entries[$i]);    
        echo $this->entryToPta($obj);
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
    $this->assignWorkedToIncome();
  }
}