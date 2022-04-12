<?php declare(strict_types=1);

function parseVerifyInvoiceJSON($str) {
  $ret = [];
  $lines = json_decode($str);
        array_push($ret, [
            "date" => $lines->due_date,
            "from" => $lines->line_items[0]->description,
            "to" =>$lines->vendor->raw_name,
            "amount" => floatval($lines->subtotal),
            "total" => floatval($lines->total)
        ]);
  return $ret;
}