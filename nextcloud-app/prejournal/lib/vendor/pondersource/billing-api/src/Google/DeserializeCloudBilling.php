<?php

namespace PonderSource\GoogleApi;

use Sabre\Xml\Service;
use PonderSource\GoogleApi\CloudBilling;
use PonderSource\GoogleApi\CloudBillings;

class DeserializeBilling
{
    public function deserializeInvoice($outputXMLString) {
        $service = new Service();
        $service->elementMap = [
            // handle a collection of books
            '{http://example.org/billings}billings' => function(\Sabre\Xml\Reader $reader) {
                $billings = new CloudBillings();
                $children = $reader->parseInnerTree();
                foreach($children as $child) {
                    if ($child['value'] instanceof CloudBillings) {
                        $billings->billings[] = $child['value'];
                    }
                }
                return $billings;
            },
            // handle a single book
            '{http://example.org/billings}billing' => function(\Sabre\Xml\Reader $reader) {
                $billing = new CloudBilling();
                // Borrowing a parser from the KeyValue class.
                $keyValue = \Sabre\Xml\Deserializer\keyValue($reader, 'http://example.org/billings');
        
                if (isset($keyValue['charges_total'])) {
                    $billing->charges_total = $keyValue['charges_total'];
                }
                if (isset($keyValue['created_at'])) {
                    $billing->created_at = $keyValue['created_at'];
                }

                if (isset($keyValue['created_at'])) {
                   $billing->created_at = $keyValue['created_at'];
               }
               if (isset($keyValue['id'])) {
                   $billing->id = $keyValue['id'];
               }

               if (isset($keyValue['credits_total'])) {
                   $billing->credits_total = $keyValue['credits_total'];
               }
               if (isset($keyValue['number'])) {
                   $billing->number = $keyValue['number'];
               }

               if (isset($keyValue['period_start'])) {
                   $billing->period_start = $keyValue['period_start'];
               }

               if (isset($keyValue['period_end'])) {
                   $invoice->period_end = $keyValue['period_end'];
               }

               if (isset($keyValue['state'])) {
                   $billing->state = $keyValue['state'];
               }

               if (isset($keyValue['total'])) {
                   $billing->total = $keyValue['total'];
               }

               if (isset($keyValue['updated_at'])) {
                   $billing->updated_at = $keyValue['updated_at'];
               }
        
                return $billing;
        
            },
        ];
        return  $service->parse($outputXMLString);
    }
}