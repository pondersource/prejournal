<?php declare(strict_types=1);

function parseTimeBroCSV($str) {
  $ret = [];
  $lines = explode("\n",$str);
  if ($lines[0] !== "Date,Start time, End time,Project,Task,Duration,Comment") {
    throw new Error("Unexpected headers line in timeBro-CSV file!");
  }
  for ($i = 1; $i < count($lines); $i++) {
    $cells = explode(",", $lines[$i]);
    if (count($cells) == 6) {
      array_push($ret, [
        "worker" => $cells[0],
        "project" => $cells[1],
        "start" => strtotime($cells[4]),
        "seconds" => strtotime($cells[5]) - strtotime($cells[4])
      ]);
    }
  }
  return $ret;
}