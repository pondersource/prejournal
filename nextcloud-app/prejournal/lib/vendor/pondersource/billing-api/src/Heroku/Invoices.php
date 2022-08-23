<?php

namespace PonderSource\HerokuApi;

use Sabre\Xml\XmlSerializable;
use Sabre\Xml\Writer;
use Sabre\Xml\XmlDeserializable;
use Sabre\Xml\Reader;
use PonderSource\HerokuApi\Invoice;

class Invoices implements XmlSerializable {
    private $invoices;

    /**
     * @param Invoices[] $invoices
     * @return Invoice
     */
    public function setInvoices(array $invoices): Invoices
    {
        $this->invoices = $invoices;
        return $this;
    }

    public function xmlSerialize(Writer $writer)
    {
        $ns = '{http://example.org/invoices}';
        foreach ($this->invoices as $invoice) {
            $writer->write([
                $ns. 'InvoiceTeamItem' => $invoice
            ]);
        }
    }
}