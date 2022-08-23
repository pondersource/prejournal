<?php

namespace PonderSource\HerokuApi;

use Sabre\Xml\Service;
use PonderSource\HerokuApi\Invoice;
use PonderSource\HerokuApi\Invoices;

class DeserializeInvoice
{
    public function deserializeInvoice($outputXMLString) {
        $service = new Service();
        $service->elementMap = [
            // handle a collection of books
            '{http://example.org/invoices}invoices' => function(\Sabre\Xml\Reader $reader) {
                $invoices = new Invoices();
                $children = $reader->parseInnerTree();
                foreach($children as $child) {
                    if ($child['value'] instanceof Invoices) {
                        $invoices->invoices[] = $child['value'];
                    }
                }
                return $invoices;
            },
            // handle a single book
            '{http://example.org/invoices}invoice' => function(\Sabre\Xml\Reader $reader) {
                $invoice = new Invoice();
                // Borrowing a parser from the KeyValue class.
                $keyValue = \Sabre\Xml\Deserializer\keyValue($reader, 'http://example.org/invoices');

                if (isset($keyValue['charges_total'])) {
                    $invoice->charges_total = $keyValue['charges_total'];
                }
                if (isset($keyValue['created_at'])) {
                    $invoice->created_at = $keyValue['created_at'];
                }

                if (isset($keyValue['created_at'])) {
                   $invoice->created_at = $keyValue['created_at'];
               }
               if (isset($keyValue['id'])) {
                   $invoice->id = $keyValue['id'];
               }

               if (isset($keyValue['credits_total'])) {
                   $invoice->credits_total = $keyValue['credits_total'];
               }
               if (isset($keyValue['number'])) {
                   $invoice->number = $keyValue['number'];
               }

               if (isset($keyValue['period_start'])) {
                   $invoice->period_start = $keyValue['period_start'];
               }

               if (isset($keyValue['period_end'])) {
                   $invoice->period_end = $keyValue['period_end'];
               }

               if (isset($keyValue['state'])) {
                   $invoice->state = $keyValue['state'];
               }

               if (isset($keyValue['total'])) {
                   $invoice->total = $keyValue['total'];
               }

               if (isset($keyValue['updated_at'])) {
                   $invoice->updated_at = $keyValue['updated_at'];
               }

                return $invoice;

            },
        ];
        return  $service->parse($outputXMLString);
    }
}
