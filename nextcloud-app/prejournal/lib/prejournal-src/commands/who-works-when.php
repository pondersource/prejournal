<?php

declare(strict_types=1);
require_once(__DIR__ . '/../platform.php');

function formatDate($date)
{
    $parts = explode(" ", $date);
    return $parts[0];
}
// Notice this circumvenes the statements and component grants
// and therefore it only works in adminParty mode:
function whoWorksWhen($context)
{
    if ($context['adminParty']) {
        // similar to the code in the what-the-world-owes command:
        $days = [];
        $movements = getAllMovements();
        $componentNames = getAllComponentNames();
        for ($i = 0; $i < count($movements); $i++) {
            if ($movements[$i]["type_"] == 'worked') {
                $from = $componentNames[$movements[$i]["fromcomponent"]];
                $to = $componentNames[$movements[$i]["tocomponent"]];
                $amount = $movements[$i]["amount"];
                $date = $movements[$i]["timestamp_"];
                if (!isset($days[$date])) {
                    $days[$date] = [];
                }
                array_push($days[$date], "$from $to $amount");
            }
        }
        ksort($days);
        $ret = [];
        foreach ($days as $date => $arr) {
            array_push($ret, "");
            array_push($ret, formatDate($date));
            foreach ($days[$date] as $val) {
                array_push($ret, $val);
            }
        }
        // var_dump($componentNames);
        return $ret;
    } else {
        return ["This command disregards access checks so it only works in admin party mode"];
    }
}
