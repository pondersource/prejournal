<?php

declare(strict_types=1);

function parseVerifyTimeJSON($str)
{
    $ret = [];
    $lines = json_decode($str);
    array_push($ret, [
        "worker" =>  $lines->bill_to_name,
        "project" => $lines->line_items[0]->type,
        "start" =>  strtotime($lines->created),
        "seconds" => $lines->total
    ]);
    return $ret;
}
