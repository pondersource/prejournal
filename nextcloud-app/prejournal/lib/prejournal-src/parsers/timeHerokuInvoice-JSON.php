<?php

declare(strict_types=1);

function parsetimeHerokuInvoiceJSON($str)
{
    $ret = [];
    $lines = json_decode($str);
    for ($i = 0; $i < count($lines);$i++) {
        //var_dump($lines[$i]);
        array_push($ret, [
            "date" => $lines[$i]->created_at,
            "from" => $_SERVER["PREJOURNAL_USERNAME"],
            "to" =>$lines[$i]->id,
            "amount" => floatval($lines[$i]->charges_total),
            "total" => floatval($lines[$i]->total)
        ]);
    }

    return $ret;
}
