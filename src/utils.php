<?php

function timestampToDateTime($timestamp) {
  $ret = new DateTime();
  $ret->setTimestamp($timestamp);
  return $ret->format('Y-m-d H:i:s');
}

function reconcileQuotes($x) {
  $ret = [];
  $reconciled = null;
  for ($i = 0; $i < count($x); $i++) {
    if ($x[$i][0] == '"') {
      $reconciled = substr($x[$i], 1);
    } else if ($x[$i][strlen($x[$i]) - 1] == '"') {
      $reconciled .= " " . substr($x[$i], 0, strlen($x[$i]) - 1);
      array_push($ret, $reconciled);
      $reconciled = null;
    } else {
      if ($reconciled == null) {
        array_push($ret, $x[$i]);
      } else {
        $reconciled .= $x[$i];
      }
    }
  }
  return $ret;
}
