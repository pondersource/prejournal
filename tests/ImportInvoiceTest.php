<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/run-command.php');


final class ImportInvoiceTest extends TestCase
{
public function testParseVerifyInvoiceJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123');
        $fixture = __DIR__ . "/fixtures/verifyInvoice-JSON.json";
        $result = runCommand(getContext(), ["import-invoice", "verifyInvoice-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'Test'
            ],
            [
                'id' => 2,
                'name' => 'IRS'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'invoice',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1970-01-01 00:33:42',
                'amount' => '10'            ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 1,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                            ]
        ], getAllStatements());
    }
}