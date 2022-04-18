<?php declare(strict_types=1);

function parseTimelyCSV($str) {
  $ret = [];
  $lines = explode("\n",$str);
  if ($lines[0] !== "Client,Project,Budget Type,Budget Interval,Total Budget,Budget Spent,Budget Spent (%),Budget Remaining,Budget Remaining (%),Date,Name,Email,Logged Hours,Planned Hours,Logged Money,Planned Money,Tags,Billed,Notes,From,To,External ID,Billable Hours,Non-Billable Hours") {
    throw new Error("Unexpected headers line in timely-CSV file!");
  }

  for ($i = 1; $i < count($lines); $i++) {
    $cells = explode(",", $lines[$i]);
    if (count($cells) == 24) {
      array_push($ret, [
        "worker" => $cells[0],
        "project" => $cells[1],
        "start" => $cells[12],
        "seconds" => $cells[14]
      ]);
    }
  }
  return $ret;
}