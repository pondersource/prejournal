<?php

declare(strict_types=1);

function parseVerifyInvoiceJSON($str)
{
    $ret = [];
    $lines = json_decode($str);
    array_push($ret, [
        "date" => $lines->due_date,
        "from" => $lines->bill_to_name,
        "to" =>$lines->ship_to_name,
        "amount" => floatval($lines->subtotal),
        "total" => floatval($lines->total)
    ]);
    return $ret;
}
