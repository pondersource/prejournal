<?php

namespace PonderSource\GitHubApi;

use Sabre\Xml\Service;
use PonderSource\GitHubApi\Billing;
use PonderSource\GitHubApi\Billings;

class DeserializeBilling
{
    public function deserializeBilling($outputXMLString) {
        $service = new Service();
        $service->elementMap = [
            '{http://example.org/billings}billings' => function(\Sabre\Xml\Reader $reader) {
                $billings = new Billings();
                $children = $reader->parseInnerTree();
                foreach($children as $child) {
                    if ($child['value'] instanceof Billings) {
                        $billings->billings[] = $child['value'];
                    }
                }
                return $billings;
            },
            '{http://example.org/billings}billing' => function(\Sabre\Xml\Reader $reader) {
                $billing = new Billing();
                // Borrowing a parser from the KeyValue class.
                $keyValue = \Sabre\Xml\Deserializer\keyValue($reader, 'http://example.org/billings');

                if (isset($keyValue['days_left_in_billing_cycle'])) {
                    $billing->days_left_in_billing_cycle = $keyValue['days_left_in_billing_cycle'];
                }
                if (isset($keyValue['estimated_paid_storage_for_month'])) {
                    $billing->estimated_paid_storage_for_month = $keyValue['estimated_paid_storage_for_month'];
                }
                if (isset($keyValue['estimated_storage_for_month'])) {
                    $billing->estimated_storage_for_month = $keyValue['estimated_storage_for_month'];
                }
                return $billing;
            },
        ];
        return  $service->parse($outputXMLString);
    }
}
