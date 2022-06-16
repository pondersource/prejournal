<?php

declare(strict_types=1);
  require_once(__DIR__ . '/../database.php');

function listPayments($context)
{
    if ($context['adminParty']) {
        $movements = getAllPaymentMovements();
        $ret = ["timestamp, from, to, amount"];
        foreach ($movements as $row) {
            // var_dump($row);
            foreach (["fromComponent", "toComponent"] as $columnName) {
                // sqlite preserves case in column names but
                // pg returns lower case column name
                if (!isset($row[$columnName])) {
                    $row[$columnName] = $row[strtolower($columnName)];
                }
            }
            $timestamp_ = strtotime($row['timestamp_']);
            $fromComponentName = getComponentName($row['fromComponent']);
            $toComponentName = getComponentName($row['toComponent']);
            $amount = $row['amount'];
            array_push($ret, "$timestamp_, $fromComponentName, $toComponentName, $amount");
        }
        return $ret;
    } else {
        return ["This command disregards access checks so it only works in admin party mode"];
    }
}
