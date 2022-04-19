<?php declare(strict_types=1);

function parseTimesheetMobileCSV($str) {
  $ret = [];
  $lines = explode("\n",$str);
  //var_dump($lines);
  //exit;
 

  if ($lines[0] !== '"SITE REPORT","WORKER #",WORKER,"SITE NAME","TASK NAME",WORKGROUP,LOGIN,"LOGIN TIME","LOCATION IN",LOGOUT,"LOGOUT TIME","LOCATION OUT",TOTAL,REGULAR,OVERTIME,"SERVICE ITEM","LOGIN NOTES","LOGOUT NOTES","PHONE NUMBER",MILEAGE') {
    throw new Error("Unexpected headers line in timelsheet-CSV file!");
  }
  //var_dump($lines);
  //exit;
  for ($i = 2; $i < count($lines); $i++) {
    $cells = explode(",", $lines[$i]);
    //var_dump($cells);
    if (count($cells) == 20) {
      array_push($ret, [
        "worker" => $cells[2],
        "project" => "test",
        "start" => strtotime($cells[6]),
        "seconds" => strtotime($cells[9]) - strtotime($cells[6])
      ]);
    }
  }
  return $ret;
}