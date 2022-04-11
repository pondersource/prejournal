<?php declare(strict_types=1);

function parseTimeManagerCSV($str) {
  $ret = [];
  $lines = explode("\n",$str);
  if ($lines[0] !== "start,end,note,status,duration,client,project,task") {
    throw new Error("Unexpected headers line in timeManager-CSV file!");
  }
  for ($i = 1; $i < count($lines); $i++) {
    $cells = explode(",", $lines[$i]);
    //var_dump($cells[0]);
    if (count($cells) == 8) {
      array_push($ret, [
        "worker" => $cells[5],
        "project" => $cells[6],
        "start" => $cells[0],
        "seconds" => $cells[4]
      ]);
    }
  }
  return $ret;
}