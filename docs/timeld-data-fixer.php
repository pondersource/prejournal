<?php

$arr = explode("\n", file_get_contents("sources/m-ld.ndjson"));
$timesheet = "my-timesheet";
for ($i = 0; $i < count($arr); $i++) {
    if (strlen($arr[$i]) == 0) {
        continue;
    }
    $fields = json_decode($arr[$i], true);
    if ($fields["@type"] == "Timesheet") {
      $parts = explode("/", $fields["@id"]);
      $fields["@id"] = "fedb/" . implode('-', $parts);
      $fields["project"] = [ $fields["project"] ];
      $timesheet = $fields["@id"];
    } else if ($fields["@type"] == "Entry") {
      $fields["external"] = [
          "@id" => $fields["@id"]
      ];
      $fields["session"] = [
          "@id" => $timesheet
      ];
      if ($fields["vf:provider"]["@id"] == "http://timeld.org/angus") {
          $fields["vf:provider"]["@id"] = "http://time.pondersource.com/angus";
      }
      if ($fields["vf:provider"]["@id"] == "http://timeld.org/george") {
          $fields["vf:provider"]["@id"] = "http://time.pondersource.com/george";
      }
      if (!isset($fields["duration"]) || gettype($fields["duration"]) != "integer") {
          $fields["duration"] = 1;
      }
      unset($fields["@id"]);
    }
    echo json_encode($fields, JSON_UNESCAPED_SLASHES) . "\n";
}
