<?php
const ANALYSIS_FIRST_WEEK = "202101";
const ANALYSIS_LAST_WEEK = "202310";
const DEFAULT_HOURS_PER_WEEK = 40;
const BILLABLE_FACTOR = 3;
const OVERHEAD_FACTOR = 1.25;

function timestampToDateTime($timestamp)
{
    $ret = new DateTime();
    $ret->setTimestamp($timestamp);
    return $ret->format('Y-m-d H:i:s');
}

function dateTimeToTimestamp($dateTime)
{
    $parts = explode(' ', $dateTime);
    if (count($parts) != 3) {
        throw new Exception("Could not parse date '$dateTime'");
    }
    $day = $parts[0];
    $month = [
        "jan" => 1,
        "feb" => 2,
        "mar" => 3,
        "apr" => 4,
        "may" => 5,
        "jun" => 6,
        "jul" => 7,
        "aug" => 8,
        "sep" => 9,
        "oct" => 10,
        "nov" => 11,
        "dec" => 12
    ][$parts[1]];
    $year = $parts[2];
    $ret = new DateTime("$year-$month-$day");
    // echo "Interpreting '$dateTime' to '" . $ret->format('j M Y') . "' ('" . $ret->format('Y-m-d H:i:s') . "')\n";
    return $ret->getTimestamp();
}

function dateIsAfter($a, $b) {
    $dA = new DateTime($a);
    $dB = new DateTime($b);
    if ($dA < $dB) {
        // echo "'$a' = " . $dA->format('Y-m-d') . " is before '$b' = " . $dA->format('Y-m-d') . "\n";
        return false;
    } else if ($dA == $dB) {
        // echo "'$a' = " . $dA->format('Y-m-d') . " is equal to '$b' = " . $dA->format('Y-m-d') . "\n";
        return false;
    } else if ($dA == $dB) {
        // echo "'$a' = " . $dA->format('Y-m-d') . " is after '$b' = " . $dA->format('Y-m-d') . "\n";
        return true;
    }
}

function dateIsBefore($a, $b) {
    $dA = new DateTime($a);
    $dB = new DateTime($b);
    if ($dA < $dB) {
        // echo "'$a' = " . $dA->format('Y-m-d') . " is before '$b' = " . $dA->format('Y-m-d') . "\n";
        return true;
    } else if ($dA == $dB) {
        // echo "'$a' = " . $dA->format('Y-m-d') . " is equal to '$b' = " . $dA->format('Y-m-d') . "\n";
        return false;
    } else if ($dA == $dB) {
        // echo "'$a' = " . $dA->format('Y-m-d') . " is after '$b' = " . $dA->format('Y-m-d') . "\n";
        return false;
    }
}

function dateTimeToPta($dateTime)
{
    $obj = new DateTime($dateTime);
    return $obj->format("Y-m-d"); // e.g. 2023-02-15
}

function dateTimeToWeekOfYear($dateTime)
{
    $obj = new DateTime($dateTime);
    if ($obj->format("W") == "52" && $obj->format("M") == "Jan") {
        return $obj->format("Y") . "00";
    } else {
        return $obj->format("YW");
    }
}

function weekOfYearToDateTime($week, $format='d M Y') {
    $dto = new DateTime();
    $year = substr($week, 0, 4);
    $woy = substr($week, 4, 2);

    $dto->setISODate(intval($year), intval($woy));
    return strtolower($dto->format($format));
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