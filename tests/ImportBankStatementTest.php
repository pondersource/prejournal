<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/run-command.php');


final class ImportBankStatementTest extends TestCase
{
    public function testParseTimeCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
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
            ],
            [
                'id' => 3,
                'name' => 'alice'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'payment',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2021-01-01 12:00:00',
                'amount' => '60.5',     
                'description' => 'outside movement from bank statement'    
            ],
            [
                'id' => 2,
                'type_' => 'payment',
                'fromcomponent' => 3,
                'tocomponent' => 1,
                'timestamp_' => '2021-01-01 12:00:00',
                'amount' => '60.5',     
                'description' => 'inside movement from bank statement'    
             ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 1,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
            ],
            [
                'id' => 2,
                'movementid' => 2,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                            ]
        ], getAllStatements());
    }
}
