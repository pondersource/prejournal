<?php

namespace PonderSource\HerokuApi;

use Sabre\Xml\Service;
use PonderSource\HerokuApi\Invoice;
use PonderSource\HerokuApi\Invoices;

class GenerateInvoice
{
    public static function invoice(Invoices $invoice)
    {
        $xmlService = new Service();

        return $xmlService->write('{http://example.org/invoices}invoice', [
            $invoice
        ]);
    }
}