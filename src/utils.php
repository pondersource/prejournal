<?php

function timestampToDateTime($timestamp)
{
    $ret = new DateTime();
    $ret->setTimestamp($timestamp);
    return $ret->format('Y-m-d H:i:s');
}

function dateTimeToTimestamp($dateTime)
{
    $ret = new DateTime($dateTime);
    // echo "Interpreting '$dateTime' to '" . $ret->format('j M Y') . "' ('" . $ret->format('Y-m-d H:i:s') . "')\n";
    return $ret->getTimestamp();
}

function dateTimeToWeekOfYear($dateTime)
{
    $obj = new DateTime($dateTime);
    return $obj->format("YW");
}

function weekOfYearToDateTime($week) {
    $dto = new DateTime();
    $year = substr($week, 0, 4);
    $woy = substr($week, 4, 2);

    $dto->setISODate(intval($year), intval($woy));
    return $dto->format('d F Y');
  }
  
function reconcileQuotes($x)
{
    // var_dump($x);
    $ret = [];
    $reconciled = null;
    for ($i = 0; $i < count($x); $i++) {
        if (strlen($x[$i]) == 0) {
            if (is_null($reconciled)) {
                // print("zero-length word outside quotes\n");
                array_push($ret, $x[$i]);
            } else {
                // print("zero-length word inside quotes\n");
                $reconciled .= " ";
            }
        } elseif ($x[$i][0] == '"') {
            if (strlen($x[$i]) == 1) {
                if (is_null($reconciled)) {
                    // print("quote starts with space '$x[$i]'\n");
                    $reconciled = '';
                } else {
                    $reconciled .= " ";
                    // print("finish reconciled with space '$reconciled'\n");
                    array_push($ret, $reconciled);
                    $reconciled = null;
                }
            } elseif ($x[$i][strlen($x[$i]) - 1] == '"') {
                // print("solo quoted '$x[$i]'\n");
                array_push($ret, substr($x[$i], 1, strlen($x[$i]) - 2));
            } else {
                $reconciled = substr($x[$i], 1);
                // print("new reconciled '$reconciled'\n");
            }
        } elseif ($x[$i][strlen($x[$i]) - 1] == '"') {
            $reconciled .= " " . substr($x[$i], 0, strlen($x[$i]) - 1);
            // print("finish reconciled '$reconciled'\n");
            array_push($ret, $reconciled);
            $reconciled = null;
        } else {
            if (is_null($reconciled)) {
                array_push($ret, $x[$i]);
            // print("unquoted '$x[$i]'\n");
            } else {
                $reconciled .= " " . $x[$i];
                // print("quoted '$x[$i]'\n");
            }
        }
    }
    // echo "quotes reconciled!";
    // var_dump($x);
    // var_dump($ret);
    return $ret;
}

// function debug($x) {
//     error_log(var_export($x, true));
// }

const LEVEL_DEBUG = 0;
const LEVEL_OUTPUT = 1;
function debug($str, $level = LEVEL_DEBUG) {
  if (isset($_SERVER["DEBUG"]) || $level == LEVEL_OUTPUT) {
    echo $str;
  }
}