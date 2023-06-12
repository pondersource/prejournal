<?php

require_once(__DIR__ . '/../utils.php');

function dateFromEntry($entry) {
    if (isset($entry["date"])) {
        return $entry["date"];
    } else if (isset($entry["from"])) {
        return $entry["from"];
    } else {
        throw new Exception('no date and no from in entry ' . var_export($entry, true));
    }
}

function entryToPta($entry) {
    return dateFromEntry($entry) . " " . $entry["type"] . "\n";
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
  function __construct() {
  }

  function addEntries($newEntries) {
    for ($i = 0; $i < count($newEntries); $i++) {
        $this->entries[] = $newEntries[$i];
    }
  }

  function toPta() {
    usort($this->entries, 'entrySortCb');
    $highestDateYet = 0;    
    for ($i = 0; $i < count($this->entries); $i++) {
        echo entryToPta($this->entries[$i]);
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