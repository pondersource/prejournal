<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;


final class TeamWorkApiIntegrationTest extends TestCase
{
    private $http;

    public function setUp() : void
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'https://pondersource.teamwork.com/']);
    }

    public function testGetTimeEntriesNotAuthenticate()
    {
        $response = $this->http->request('GET', 'time-entries.json', ['http_errors' => false]);

    
        $this->assertEquals(401, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json; charset=utf-8", $contentType);
    }

}