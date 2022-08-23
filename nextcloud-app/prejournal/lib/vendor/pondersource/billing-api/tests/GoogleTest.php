<?php

use Google\ApiCore\ApiException;
use Google\ApiCore\CredentialsWrapper;
use Google\ApiCore\Testing\GeneratedTest;
use Google\ApiCore\Testing\MockTransport;
use Google\Cloud\Billing\V1\BillingAccount;
use Google\Cloud\Billing\V1\CloudBillingClient;
use Google\Cloud\Billing\V1\CloudCatalogClient;
use Google\Cloud\Billing\V1\ListServicesResponse;
use Google\Cloud\Billing\V1\ListSkusResponse;
use Google\Cloud\Billing\V1\Service;
use Google\Cloud\Billing\V1\Sku;
use Google\Rpc\Code;

class GoogleTest extends GeneratedTest
{
     /**
     * @return TransportInterface
     */
    private function createTransport($deserialize = null)
    {
        return new MockTransport($deserialize);
    }

    /**
     * @return CredentialsWrapper
     */
    private function createCredentials()
    {
        return $this->getMockBuilder(CredentialsWrapper::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @return CloudBillingClient
     */
    private function createClient(array $options = [])
    {
        $options += [
            'credentials' => $this->createCredentials(),
        ];
        return new CloudBillingClient($options);
    }

     /**
     * @return CloudCatalogClient
     */
    private function createClientCatalog(array $options = [])
    {
        $options += [
            'credentials' => $this->createCredentials(),
        ];
        return new CloudCatalogClient($options);
    }

   /**
     * @test
     */
    public function getBillingAccountTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClient([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $name2 = 'name2-1052831874';
        $open = true;
        $displayName = 'displayName1615086568';
        $masterBillingAccount = 'masterBillingAccount1503143052';
        $expectedResponse = new BillingAccount();
        $expectedResponse->setName($name2);
        $expectedResponse->setOpen($open);
        $expectedResponse->setDisplayName($displayName);
        $expectedResponse->setMasterBillingAccount($masterBillingAccount);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedName = $client->billingAccountName('[BILLING_ACCOUNT]');
        $response = $client->getBillingAccount($formattedName);
        $this->assertEquals($expectedResponse, $response);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.billing.v1.CloudBilling/GetBillingAccount', $actualFuncCall);
        $actualValue = $actualRequestObject->getName();
        $this->assertProtobufEquals($formattedName, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }

      /**
     * @test
     */
    public function listServicesTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClientCatalog([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $nextPageToken = '';
        $servicesElement = new Service();
        $services = [
            $servicesElement,
        ];
        $expectedResponse = new ListServicesResponse();
        $expectedResponse->setNextPageToken($nextPageToken);
        $expectedResponse->setServices($services);
        $transport->addResponse($expectedResponse);
        $response = $client->listServices();
        $this->assertEquals($expectedResponse, $response->getPage()->getResponseObject());
        $resources = iterator_to_array($response->iterateAllElements());
        $this->assertSame(1, count($resources));
        $this->assertEquals($expectedResponse->getServices()[0], $resources[0]);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.billing.v1.CloudCatalog/ListServices', $actualFuncCall);
        $this->assertTrue($transport->isExhausted());
    }

     /**
     * @test
     */
    public function listSkusTest()
    {
        $transport = $this->createTransport();
        $client = $this->createClientCatalog([
            'transport' => $transport,
        ]);
        $this->assertTrue($transport->isExhausted());
        // Mock response
        $nextPageToken = '';
        $skusElement = new Sku();
        $skus = [
            $skusElement,
        ];
        $expectedResponse = new ListSkusResponse();
        $expectedResponse->setNextPageToken($nextPageToken);
        $expectedResponse->setSkus($skus);
        $transport->addResponse($expectedResponse);
        // Mock request
        $formattedParent = $client->serviceName('[SERVICE]');
        $response = $client->listSkus($formattedParent);
        $this->assertEquals($expectedResponse, $response->getPage()->getResponseObject());
        $resources = iterator_to_array($response->iterateAllElements());
        $this->assertSame(1, count($resources));
        $this->assertEquals($expectedResponse->getSkus()[0], $resources[0]);
        $actualRequests = $transport->popReceivedCalls();
        $this->assertSame(1, count($actualRequests));
        $actualFuncCall = $actualRequests[0]->getFuncCall();
        $actualRequestObject = $actualRequests[0]->getRequestObject();
        $this->assertSame('/google.cloud.billing.v1.CloudCatalog/ListSkus', $actualFuncCall);
        $actualValue = $actualRequestObject->getParent();
        $this->assertProtobufEquals($formattedParent, $actualValue);
        $this->assertTrue($transport->isExhausted());
    }
}