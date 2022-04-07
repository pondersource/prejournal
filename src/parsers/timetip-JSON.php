<?php declare(strict_types=1);

function parseTimetipJSON($str) {
  $ret = [];
  $lines = json_decode($str);
  //var_dump($lines->dates[0]->date);

  for ($i = 0; $i < count($lines->dates); $i++) {
        array_push($ret, [
            "date" => $lines->dates[$i]->date,
            "last_type" => $lines->dates[$i]->last->type,
            "last_reason" => $lines->dates[$i]->last->reason,
            "duration" => $lines->dates[$i]->last->duration,
            "total" => $lines->dates[$i]->summary->total,
            "start" => $lines->dates[$i]->summary->start,
            "finish" => $lines->dates[$i]->summary->finish
        ]);
  }
  return $ret;
}