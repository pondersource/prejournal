<?php

function timestampToDateTime($timestamp) {
  $ret = new DateTime();
  $ret->setTimestamp($timestamp);
  return $ret->format('Y-m-d H:i:s');
}

function reconcileQuotes($x) {
  // var_dump($x);
  $ret = [];
  $reconciled = null;
  for ($i = 0; $i < count($x); $i++) {
    if (strlen($x[$i]) == 0) {
      // print("zero-length word\n");
      array_push($ret, $x[$i]);
    } else if ($x[$i][0] == '"') {
      if ($x[$i][strlen($x[$i]) - 1] == '"') {
        // print("solo quoted '$xi[$i]'\n");
        array_push($ret, substr($x[$i], 1, strlen($x[$i]) - 2));
      } else {
        $reconciled = substr($x[$i], 1);
        // print("new reconciled '$reconciled'\n");
      }
    } else if ($x[$i][strlen($x[$i]) - 1] == '"') {
      $reconciled .= " " . substr($x[$i], 0, strlen($x[$i]) - 1);
      // print("finish reconciled '$reconciled'\n");
      array_push($ret, $reconciled);
      $reconciled = null;
    } else {
      if ($reconciled == null) {
        array_push($ret, $x[$i]);
        // print("unquoted '$x[$i]'\n");
      } else {
        $reconciled .= " " . $x[$i];
        // print("quoted '$x[$i]'\n");
      }
    }
  }
  return $ret;
}
