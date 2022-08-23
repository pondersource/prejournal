<?php

namespace PonderSource\GoogleApi;

use Sabre\Xml\XmlSerializable;
use Sabre\Xml\Writer;
use Sabre\Xml\XmlDeserializable;
use Sabre\Xml\Reader;
use PonderSource\GoogleApi\Billing;

class CloudBillings implements XmlSerializable {
    private $billings = [];

     /**
     * @param CloudBillings[] $billings
     * @return CloudBilling
     */
    public function setBillings(array $billings): CloudBillings
    {
        $this->billings = $billings;
        return $this;
    }

    public function xmlSerialize(Writer $writer)
    {
        $ns = '{http://example.org/billings}';
        foreach ($this->billings as $billing) {
            $writer->write([
                $ns. 'BillingItem' => $billing
            ]);
        }
    }
}