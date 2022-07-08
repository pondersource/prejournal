<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../src/run-command.php');

final class ImportBankStatementTest extends TestCase
{
    private function checkResult($fixture)
    {
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
                'amount' => '60.5'
            ],
            [
                'id' => 2,
                'type_' => 'payment',
                'fromcomponent' => 3,
                'tocomponent' => 1,
                'timestamp_' => '2021-01-01 12:00:00',
                'amount' => '60.5'
            ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 1,
                'userid' => 1,
                'sourcedocumentformat' => 'asnbank-CSV',
                'sourcedocumentfilename' => "$fixture#0",
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => 'outside movement from bank statement: stichting blockchain promotie OVB  \'Fictional transaction\''
            ],
            [
                'id' => 2,
                'movementid' => 2,
                'userid' => 1,
                'sourcedocumentformat' => 'asnbank-CSV',
                'sourcedocumentfilename' => "$fixture#0",
                'timestamp_' => '2022-03-31 12:00:00',
                'description' =>  "inside movement from bank statement: stichting blockchain promotie OVB  'Fictional transaction'"
            ]
        ], getAllStatements());
    }

    public function testParseAsnBankCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/asnbank-CSV.csv";
        runCommand(getContext(), ["import-bank-statement", "asnbank-CSV", $fixture,  "2022-03-31 12:00:00" ]);
        $this->checkResult($fixture);
        // run it again to test idempotency, second run should have no effect:
        runCommand(getContext(), ["import-bank-statement", "asnbank-CSV", $fixture,  "2022-03-31 12:00:00" ]);
        $this->checkResult($fixture);
    }
}
