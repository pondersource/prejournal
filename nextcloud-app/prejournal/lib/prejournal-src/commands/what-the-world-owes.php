<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');

// E.g. "2022-04-24 12:00:00" ->  "2022-04-24"
function toDate($str)
{
    $parts = explode(' ', $str);
    return $parts[0];
}

// E.g. isBefore("2022-04-24", "2023-01-01") -> true
function isBefore($d1, $d2)
{
    $parts1 = explode('-', $d1);
    $parts2 = explode('-', $d2);
    if (count($parts1) != 3) {
        throw new Error("First date is not in 1970-01-01 format: '$d1'");
    }
    if (count($parts2) != 3) {
        throw new Error("Second date is not in 1970-01-01 format: '$d2'");
    }
    for ($i = 0; $i < 3; $i++) {
        if (intval($parts1[$i]) < intval($parts2[$i])) {
            return true;
        }
        if (intval($parts1[$i]) > intval($parts2[$i])) {
            return false;
        }
    }
    return false;
}

// start and end date and start balance are optional, so:
// what-the-world-owes michiel
// what-the-world-owes michiel 2022-01-01
// what-the-world-owes michiel 2022-01-01 2023-01-01
// what-the-world-owes michiel 2022-01-01 2023-01-01 1000.0
function whatTheWorldOwes($context, $command)
{
    if ($context['adminParty']) {
        $triangleMembers = getCycles(getComponentId($context["user"]["username"]), 3);
        $squareMembers = getCycles(getComponentId($context["user"]["username"]), 4);
        $cycleMembers = array_merge($triangleMembers, $squareMembers);

        $componentId = getComponentId($command[1]);
        if (count($command) >= 3) {
            $startDate = $command[2];
        } else {
            $startDate = "1970-01-01";
        }
        if (count($command) >= 4) {
            $endDate = $command[3];
        } else {
            $endDate = "2070-01-01";
        }
        if (count($command) >= 5) {
            $cumm = floatval($command[4]);
        } else {
            $cumm = 0;
        }
        $movements = getAllMovements();

        // similar to the code in the who-works-when command:
        $days = [];
        for ($i = 0; $i < count($movements); $i++) {
            if (in_array($movements[$i]["id"], $cycleMembers)) {
                // echo "Skipping cycle member movement " . $movements[$i]["id"] . "\n";
                continue;
            }
            $date = toDate($movements[$i]["timestamp_"]);
            if (isBefore($startDate, $date) && isBefore($date, $endDate)) {
                $delta = 0;
                if ($movements[$i]["fromcomponent"] == $componentId) {
                    echo("outgoing value: "
                        . getComponentName($movements[$i]["fromcomponent"])
                        . " to "
                        . getComponentName($movements[$i]["tocomponent"])
                        . ": "
                        . floatval($movements[$i]["amount"])
                        . " " . getDescriptionFromStatement($movements[$i]["id"]) . "\n");
                    // var_dump($movements[$i]);
                    $delta = -floatval($movements[$i]["amount"]);
                }
                if ($movements[$i]["tocomponent"] == $componentId) {
                    echo("incoming value:"
                        . getComponentName($movements[$i]["fromcomponent"])
                        . " to "
                        . getComponentName($movements[$i]["tocomponent"])
                        . ": "
                        . floatval($movements[$i]["amount"])
                        /* . " " . $movements[$i]["description"] */ . "\n");
                    // var_dump($movements[$i]);
                    $delta = floatval($movements[$i]["amount"]);
                }
                if ($delta != 0) {
                    if (!isset($days[$date])) {
                        $days[$date] = [];
                    }
                    $id = $movements[$i]["id"];
                    array_push($days[$date], $delta);
                }
            }
        }
        ksort($days);
        $ret = [];
        foreach ($days as $date => $arr) {
            array_push($ret, formatDate($date) . " $cumm");
            echo "\n" . formatDate($date) . "\n";
            foreach ($days[$date] as $val) {
                echo " $cumm + $val\n";
                $cumm += $val;
            }
        }
        array_push($ret, "end $cumm");
        return $ret;
    } else {
        return ["This command disregards access checks so it only works in admin party mode"];
    }
}
