<?php

namespace PonderSource\GitHubApi;

use Sabre\Xml\Service;
use PonderSource\GitHubApi\Billing;

class GenerateBilling
{
    public static function billing(Billing $billing)
    {
        $xmlService = new Service();

        return $xmlService->write('{http://example.org/billings}billing', [
            $billing
        ]);
    }
}
