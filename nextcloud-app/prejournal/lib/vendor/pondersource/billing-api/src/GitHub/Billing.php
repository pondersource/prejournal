<?php

namespace PonderSource\GitHubApi;

use Sabre\Xml\XmlSerializable;
use Sabre\Xml\Writer;

class Billing implements XmlSerializable {
    public $days_left_in_billing_cycle;
    public $estimated_paid_storage_for_month;
    public $estimated_storage_for_month;

    function xmlSerialize(Writer $writer) {
        $ns = '{http://example.org/billings}';

        $writer->write([
            $ns . 'days_left_in_billing_cycle' => $this->days_left_in_billing_cycle,
            $ns . 'estimated_paid_storage_for_month' => $this->estimated_paid_storage_for_month,
            $ns . 'estimated_storage_for_month' => $this->estimated_storage_for_month,
        ]);
        return $ns;
    }
}
