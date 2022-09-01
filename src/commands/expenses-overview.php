<?php

declare(strict_types=1);
require_once(__DIR__ . '/../database.php');
function expensesOverview($context, $command)
{
    if (isset($context["user"])) {
        $conn = getDbConn();
        $consumptionRes = $conn->executeQuery('SELECT ' .
            'm.timestamp_, m.amount, m.unit, c1.name as from, c2.name as to, s.description ' .
            'FROM movements m INNER JOIN implications i ON i.movementId = m.id ' .
            'INNER JOIN components c1 ON m.fromComponent = c1.id ' .
            'INNER JOIN components c2 ON m.toComponent = c2.id ' .
            'INNER JOIN statements s ON i.statementId = s.id ' .
            'WHERE i.relation=\'purchase-delivery\'');
        $movements = $consumptionRes->fetchAllAssociative();
        $months = [];
        $columns = [ "month", "total" ];
        for ($i = 0; $i < count($movements); $i++) {
            $month = substr($movements[$i]["timestamp_"], 0, 7);
            // var_dump(["month", $month]);
            if (!isset($months[$month])) {
                $months[$month] = [
                ];
            }
            // var_dump(["budget this month", $movements[$i]["to"]]);
            $budget = $movements[$i]["to"];
            if (!isset($months[$month][$budget])) {
                $months[$month][$budget] = [];
                if (!in_array($budget, $columns)) {
                    $columns[] = $budget;
                }
            }
        array_push($months[$month][$movements[$i]["to"]], $movements[$i]);
        }
        $ret = [];
        $overallSum = 0;
        $overallNum = 0;

        // $ret = [ implode(" | ", $columns), "" ];
        foreach ($months as $month => $budgets) {
            $monthSum = 0;
            foreach ($budgets as $budget => $entries) {
                for ($i = 0; $i < count($entries); $i++) {
                    if (($entries[$i]["to"] != "Decla") && ($entries[$i]["to"] != "Self")) {
                        $monthSum += $entries[$i]["amount"];
                    }
                }
            }
            $overallNum++;
            $overallSum += $monthSum;
            array_push($ret, "### " . $month . " " . $monthSum);

                foreach ($budgets as $budget => $entries) {
                    $sum = 0;
                    for ($i = 0; $i < count($entries); $i++) {
                        if (($entries[$i]["to"] != "Decla") && ($entries[$i]["to"] != "Self")) {
                            $sum += $entries[$i]["amount"];
                        }
                    }
                    array_push($ret, "#### " . $budget . " " . $sum);
                    array_push($ret, "```" );
                    for ($i = 0; $i < count($entries); $i++) {
                        array_push($ret, "  (" .
                            substr($entries[$i]["timestamp_"], 8, 2) . ") [" .
                            $entries[$i]["amount"] . " " .
                            $entries[$i]["unit"] . "] " .
                            $entries[$i]["from"] . " | " .
                            $entries[$i]["description"] . " "
                        );
                    }
                    array_push($ret, "```" );
                    array_push($ret, "" );
                }
            array_push($ret, "" );
        }
        array_push($ret, "$overallSum in $overallNum months is " . ($overallSum / $overallNum) . " on average.");
        return $ret;
    } else {
        return [ "User not found or wrong password" ];
    }
}
