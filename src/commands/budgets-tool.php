<?php

declare(strict_types=1);
require_once(__DIR__ . '/../database.php');
function budgetsTool($context, $command)
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
        function cmp($a, $b)
        {
            if ($a["amount"] == $b["amount"]) {
                return 0;
            }
            return ($a["amount"] < $b["amount"]) ? -1 : 1;
        }
        usort($movements, "cmp");

        $ret = [];
        for ($i = 0; $i < count($movements); $i++) {
            if ((!isset($command[1])) || ($command[1] == $movements[$i]["to"])) {
                array_push($ret, $movements[$i]["timestamp_"] . "  " . $movements[$i]["amount"] . "  " . $movements[$i]["to"] . "  " . $movements[$i]["from"] . "  " . $movements[$i]["description"]);
            }
        }
        return $ret;
    } else {
        return [ "User not found or wrong password" ];
    }
}
