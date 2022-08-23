<?php

namespace PonderSource\GoogleApi;

require 'vendor/autoload.php';
use Google\Cloud\Billing\V1\CloudBillingClient;
use Google\Cloud\Billing\V1\CloudCatalogClient;
use PonderSource\MissingApiKeyException;
use PonderSource\GoogleApi\CloudBilling;
use PonderSource\GoogleApi\CloudBillings;
use PonderSource\GoogleApi\GenerateCloudBilling;

class Google {
    //protected $apiKey;
    public function __construct(array $config) {
        foreach($config as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * Lists the billing accounts that the current authenticated user has
     * Lists the projects associated with a billing account. The current
     * permission to
     * [view](https://cloud.google.com/billing/docs/how-to/billing-access).
     */
    public function getCloudBillingAccount() {
        $client = new CloudBillingClient();
        $myArray = [];
        try {
            $response = $client->listBillingAccounts();
            foreach ($response->iterateAllElements() as $element) {
                $result = $client->listProjectBillingInfo($element->getName());
                foreach($result->iterateAllElements() as $project) {
                    array_push($myArray, (object)[
                        'project_name' => $project->getName(),
                        'project_id' => $project->getProjectId(),
                        'billing_account_name' => $project->getBillingAccountName(),
                        'display_billing_name' => $element->getDisplayName(),
                        'billing_ouput' => $element->getOpen(),
                    ]);
                }
            }

            file_put_contents('google_billing_accounts.json', json_encode($myArray, JSON_PRETTY_PRINT));
            return $myArray;

        } finally {
             $client->close();
        }
        
    }

    /**
     * Lists all public cloud services.
     */
    public function getCloudBillingServices() {
    //putenv('GOOGLE_APPLICATION_CREDENTIALS='.realpath("service-account-file.json"));
            $catalog = new CloudCatalogClient();
            $myArray = [];
            try {
            $response = $catalog->listServices();
            foreach ($response->iterateAllElements() as $services) {
                    array_push($myArray, [
                        "service_name" => $services->getName(),
                        "service_id" => $services->getServiceId(),
                        "display_name" => $services->getDisplayName(),
                        "business_entity_name" => $services->getBusinessEntityName()
                     ]);
                }

                file_put_contents('google_services.json', json_encode($myArray, JSON_PRETTY_PRINT));
                return $myArray;

            } finally {
                $catalog->close();
           }
           
       }

       /**
        * Lists all publicly available SKUs for a given cloud service.
        */
       public function getCloudbillingSkus() {
        $catalog = new CloudCatalogClient();
        $myArray = [];
        try {
        $result = $catalog->listSkus("services/0069-3716-5463");
        foreach ($result->iterateAllElements() as $listSkus) {
            array_push($myArray, [
                "sku_name" => $listSkus->getName(),
                "sku_id" => $listSkus->getSkuId(),
                "sku_description" => $listSkus->getDescription(),
                "sku_provider_name" => $listSkus->getServiceProviderName(),
                "sku_service_name" =>  $listSkus->getCategory()->getServiceDisplayName(),
                "sku_resource" => $listSkus->getCategory()->getResourceFamily(),
                "sku_group" => $listSkus->getCategory()->getResourceGroup(),
                "sku_usage_type" => $listSkus->getCategory()->getUsageType(),
                "sku_effective_time" => gmdate("H:i:s",$listSkus->getPricingInfo()->offsetGet(0)-> getEffectiveTime()->getSeconds()),
                "sku_usage_unit" => $listSkus->getPricingInfo()->offsetGet(0)->getPricingExpression()->getUsageUnit(),
                "sku_usage_unit_description" => $listSkus->getPricingInfo()->offsetGet(0)->getPricingExpression()->getUsageUnitDescription(),
                "sku_base_unit" => $listSkus->getPricingInfo()->offsetGet(0)->getPricingExpression()->getBaseUnit(),
                "sku_base_unit_description" => $listSkus->getPricingInfo()->offsetGet(0)->getPricingExpression()->getBaseUnitDescription(),
                "sku_base_unit_conversion_factor" => $listSkus->getPricingInfo()->offsetGet(0)->getPricingExpression()->getBaseUnitConversionFactor(),
                "sku_display_quantity" => $listSkus->getPricingInfo()->offsetGet(0)->getPricingExpression()->getDisplayQuantity(),
                "sku_start_usage_amount" => $listSkus->getPricingInfo()->offsetGet(0)->getPricingExpression()->getTieredRates()->offsetGet(0)->getStartUsageAmount(),
                "sku_unit_price" => $listSkus->getPricingInfo()->offsetGet(0)->getPricingExpression()->getTieredRates()->offsetGet(0)->getUnitPrice()->getCurrencyCode()
            ]);
            }

            $billingItems = []; 
            foreach($myArray as $res) {
                //var_dump($res["sku_name"]);
                $billingItems[] = (new  CloudBilling())
                    ->setSkuName($res["sku_name"])
                    ->setSkuId($res["sku_id"])
                    ->setSkuDescription($res["sku_description"])
                    ->setSkuProviderName($res["sku_provider_name"])
                    ->setSkuServiceName($res["sku_service_name"])
                    ->setSkuResouce($res["sku_resource"])
                    ->setSkuGroup($res["sku_group"])
                    ->setSkuUsageType($res["sku_usage_type"])
                    ->setSkuEffectiveTime($res["sku_effective_time"])
                    ->setSkuUsageUnit($res["sku_usage_unit"])
                    ->setSkuUsageUnitDescription($res["sku_usage_unit_description"])
                    ->setSkuBaseUnit($res["sku_base_unit"])
                    ->setSkuBaseUnitDescription($res["sku_base_unit_description"])
                    ->setSkuBaseUnitConversionDescription($res["sku_base_unit_conversion_factor"])
                    ->setSkuDisplayQuantity($res["sku_display_quantity"])
                    ->setSkuStartUsageAmount($res["sku_start_usage_amount"])
                    ;

                $billing = (new  CloudBillings())
                ->setBillings($billingItems);

                $generateBilling = new GenerateCloudBilling();
                $outputXMLString = $generateBilling->billing($billing);

                $dom = new \DOMDocument;
                $dom->loadXML($outputXMLString);
                $dom->save('./api_responses_xml/'. 'google_billing.xml');
            }

            file_put_contents('./api_responses_json/google_skus.json', json_encode($myArray, JSON_PRETTY_PRINT));
            return $myArray;

        } finally {
            $catalog->close();
       }
       }

       public function getCloudbillingSkusUBL() {
        $catalog = new CloudCatalogClient();
        $myArray = [];
        try {
        $result = $catalog->listSkus("services/0069-3716-5463");
        foreach ($result->iterateAllElements() as $listSkus) {
            //serialize ubl invoice
         } 

        }finally {
            $catalog->close();
       }
    }
}