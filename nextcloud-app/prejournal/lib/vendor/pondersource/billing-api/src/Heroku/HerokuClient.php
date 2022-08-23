<?php

namespace PonderSource\HerokuApi;

use PonderSource\HerokuApi\Heroku;
use PonderSource\HerokuApi\Invoice;
use Sabre\Xml\Service;
use PonderSource\HerokuApi\GenerateInvoice;
use PonderSource\HerokuApi\DeserializeInvoice;
use PonderSource\HerokuApi\Invoices;
use GuzzleHttp\Psr7\Request;
use PonderSource\BadHttpStatusException;
use PonderSource\JsonDecodingException;
use PonderSource\JsonEncodingException;
use PonderSource\MissingApiKeyException;
use Http\Client\Curl\Client as CurlHttpClient;
use Http\Client\HttpClient;
use Http\Factory\Guzzle\ResponseFactory;
use Http\Factory\Guzzle\StreamFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HerokuClient {
    protected $baseUrl = 'https://api.heroku.com/';
    protected $apiKey;
    protected $curlOptions = [];
    protected $httpClient;
    protected $lastHttpRequest;
    protected $lastHttpResponse;

    public function __construct(array $config) {
        foreach($config as $property => $value) {
            $this->$property = $value;
        }

        if(!($this->apiKey instanceof MissingApiKeyException)) {
        }

         // Configure a default HTTP client if none was provided.
         if (!$this->httpClient) {
            $this->httpClient = $this->buildHttpClient();
        }
    }


    public function getHerokuInvoice(){
      
        //$TUTORIAL_KEY=`(echo -n; heroku auth:token)` ; 
        $invoice = new Invoice;

         //Account information
         $account = $this->get('account/invoices');
         //var_dump($account);
         $invoiceLines = [];
         foreach($account as $res) {

             $invoiceLines[] = (new  Invoice())
             ->setId($res->id)
             ->setChargeTotal($res->charges_total)
             ->setCreatedAt($res->created_at)
             ->setPeriodStart($res->period_start)
             ->setPeriodEnd($res->period_end)
             ->setNumber($res->number)
             ->setState($res->state)
             ->setTotal($res->total)
             ->setUpdatedAt($res->updated_at)
             ;
            
             $invoice = (new  Invoices())
             ->setInvoices($invoiceLines);
          
             $generateInvoice = new GenerateInvoice();
             $outputXMLString = $generateInvoice->invoice($invoice);

             $dom = new \DOMDocument;
             $dom->loadXML($outputXMLString);
             $dom->save('heroku_invoice.xml');
         }
        file_put_contents("heroku_invoice.json", json_encode($account, JSON_PRETTY_PRINT)); 
        return $invoice;
    }

    public function getHerokuTeamInvoices() {
        //$TUTORIAL_KEY=`(echo -n; heroku auth:token)` ; 
        $invoice = new Invoice;
        

        $teams = $this->get('teams');
        //var_dump($teams);
        $invoiceLines = [];
        foreach($teams as $team) {
            $team_invoices = $this->get("teams/" .$team->id. "/invoices");
        
        foreach($team_invoices as $res) {

            $invoiceLines[] = (new  Invoice())
            ->setId($res->id)
            ->setChargeTotal($res->charges_total)
            ->setCreatedAt($res->created_at)
            ->setPeriodStart($res->period_start)
            ->setPeriodEnd($res->period_end)
            ->setNumber($res->number)
            ->setState($res->state)
            ->setTotal($res->total)
            ->setUpdatedAt($res->updated_at)
            ->setAddonsTotal($res->addons_total)
            ->setDatabaseTotal($res->database_total)
            ->setDynoUnits($res->dyno_units)
            ->setPlatformTotal($res->platform_total)
            ->setPaymentStatus($res->payment_status)
            ->setWeightedDynoHours($res->weighted_dyno_hours)
            ;
           
            $invoice = (new  Invoices())
            ->setInvoices($invoiceLines);
         
            $generateInvoice = new GenerateInvoice();
            $outputXMLString = $generateInvoice->invoice($invoice);

            $dom = new \DOMDocument;
            $dom->loadXML($outputXMLString);
            $dom->save('heroku_invoice_team.xml');
        }
    }
        file_put_contents("heroku_team_invoices.json", json_encode($team_invoices, JSON_PRETTY_PRINT));
        return $team_invoices;
       
    }

    public function deserializeHerokuInvoice($outputXMLString) {
        $deserializeInvoice = new DeserializeInvoice();
        $deserialize = $deserializeInvoice->deserializeInvoice($outputXMLString);
        return $deserialize;
    }
    
    public function get($path, array $headers = [])
    {
        return $this->execute('GET', $path, null, $headers);
    }

    public function getLastHttpRequest()
    {
        return $this->lastHttpRequest;
    }

    public function getLastHttpResponse()
    {
        return $this->lastHttpResponse;
    }

    protected function execute($method, $path, $body = null, array $customHeaders = [])
    {
        // Clear state from the last call.
        $this->lastHttpRequest = null;
        $this->lastHttpResponse = null;

        // Build the request.
        $request = $this->buildRequest($method, $path, $body, $customHeaders);

        $this->lastHttpRequest = $request->withHeader('Authorization', 'Bearer {REDACTED}');

        // Make the API call.
        $response = $this->httpClient->sendRequest($request);

        $this->lastHttpResponse = $response;

        return $this->processResponse($response);
    }

    protected function buildRequest($method, $path, $body = null, array $customHeaders = [])
    {
        $headers = [];

        // If a body was included, add it to the request.
        if (isset($body)) {
            $headers['Content-Type'] = 'application/json';
            $body = json_encode($body);
            // Check for JSON encoding errors.
            if (json_last_error() !== JSON_ERROR_NONE) {
                if($body instanceof JsonEncodingException){}
            }
        }

        // Add required headers.
        $headers['Accept'] = 'application/vnd.heroku+json; version=3'; // Heroku specifies this.
        $headers['Authorization'] = 'Bearer ' . $this->apiKey;

        // Incorporate any custom headers, preferring them over our defaults.
        $headers = $customHeaders + $headers;

        return new Request($method, $this->baseUrl . $path, $headers, $body);
    }

    protected function processResponse(ResponseInterface $httpResponse)
    {
        // Attempt to build the API response from the HTTP response body.
        $apiResponse = json_decode($httpResponse->getBody()->getContents());
        $httpResponse->getBody()->rewind(); // Rewind the stream to make future access easier.

        if ($httpResponse instanceof BadHttpStatusException && $httpResponse->getStatusCode() >= 400) {
        }

        // Check for JSON decoding errors.
        if (json_last_error() !== JSON_ERROR_NONE) {
            if($apiResponse instanceof JsonDecodingException) {}
        }

        return $apiResponse;
    }

    protected function buildHttpClient()
    {
        return new CurlHttpClient(
            new ResponseFactory(),
            new StreamFactory(),
            $this->curlOptions
        );
    }
}