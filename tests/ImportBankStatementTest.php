<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/run-command.php');


final class ImportBankStatementTest extends TestCase
{
    public function testParseTimeCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123');
        $fixture = __DIR__ . "/fixtures/asnbank-CSV.csv";
        $result = runCommand(getContext(), ["import-bank-statement", "asnbank-CSV", $fixture,  "2022-03-31 12:00:00" ]);
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'NL12ASNB1234567890'
            ],
            [
                'id' => 2,
                'name' => 'NL08BUNQ2040937927'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'payment',
                'fromComponent' => 1,
                'toComponent' => 2,
                'timestamp_' => '2021-01-01 12:00:00',
                'amount' => 60.5            ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementId' => 1,
                'userId' => 1,
                'sourceDocumentFormat' => null,
                'sourceDocumentFilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                            ]
        ], getAllStatements());
    }
}
