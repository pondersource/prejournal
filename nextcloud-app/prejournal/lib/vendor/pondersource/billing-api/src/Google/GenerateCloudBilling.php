<?php

namespace PonderSource\GoogleApi;

use Sabre\Xml\Service;
use PonderSource\GoogleApi\CloudBilling;
use PonderSource\GoogleApi\CloudBillings;

class GenerateCloudBilling
{
    public static function billing(CloudBillings $billing)
    {
        $xmlService = new Service();

        return $xmlService->write('{http://example.org/billings}billing', [
            $billing
        ]);
    }
}