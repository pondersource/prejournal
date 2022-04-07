<?php declare(strict_types=1);

function parseTimeBroCSV($str) {
  $ret = [];
  $lines = explode("\n",$str);
  if ($lines[0] !== "User name,Start time,End time,Project,Task,Duration,Comment") {
    throw new Error("Unexpected headers line in timeBro-CSV file!");
  }
  for ($i = 1; $i < count($lines); $i++) {
    $cells = explode(",", $lines[$i]);
    
    if (count($cells) == 7) {
      array_push($ret, [
        "worker" => $cells[0],
        "project" => $cells[3],
        "start" => strtotime($cells[1]),
        "seconds" => strtotime($cells[2]) - strtotime($cells[1])
      ]);
    }
  }
  return $ret;
}