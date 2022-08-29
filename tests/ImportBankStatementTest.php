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
                'name' => 'alice'
            ],
            [
                'id' => 2,
                'name' => 'NL12ASNB1234567890'
            ],
            [
                'id' => 3,
                'name' => 'NL08BUNQ2040937927 stichting blockchain promotie'
            ]
        ], getAllComponents());
        $actual = getAllMovements();
        $expected = [
            [
                'id' => 1,
                'type_' => 'outer',
                'fromcomponent' => 2,
                'tocomponent' => 3,
                'timestamp_' => '2021-01-01 12:00:00',
                'amount' => '60.5',
                'userid' => null
            ],
            [
                'id' => 2,
                'type_' => 'inner',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2021-01-01 12:00:00',
                'amount' => '60.5',
                'userid' => null
            // ],
            // [
            //     'id' => 3,
            //     'type_' => 'outer',
            //     'fromcomponent' => 1,
            //     'tocomponent' => 2,
            //     'timestamp_' => '2021-01-01 12:00:00',
            //     'amount' => '60.5',
            //     'userid' => null
            ]
        ];
        // debug("ACTUAL:");
        // debug($actual);
        // debug("EXPECTED:");
        // debug($expected);
        $this->assertEquals($expected, $actual);

        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 2,
                'userid' => 1,
                'sourcedocumentformat' => 'asnbank-CSV',
                'sourcedocumentfilename' => "$fixture#1",
                'timestamp_' => '2022-03-31 12:00:00',
                'description' =>  "inside movement from bank statement: 9802 OVB   'Fictional transaction'",
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
            // ],
            // [
            //     'id' => 2,
            //     'movementid' => 1,
            //     'userid' => 1,
            //     'sourcedocumentformat' => 'asnbank-CSV',
            //     'sourcedocumentfilename' => "$fixture#1",
            //     'timestamp_' => '2022-03-31 12:00:00',
            //     'description' => 'outside movement from bank statement: 9802 OVB   \'Fictional transaction\'',
            //     'internal_type' => null,
            //     'remote_id' => null,
            //     'remote_system' => null
            ]
        ], getAllStatements());
    }

    public function testParseAsnBankCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/asnbank-CSV.csv";
        runCommand(getContext(), ["claim-component", "alice" ]);
        runCommand(getContext(), ["import-bank-statement", "asnbank-CSV", $fixture,  "2022-03-31 12:00:00" ]);
        $this->checkResult($fixture);
        // // run it again to test idempotency, second run should have no effect:
        // runCommand(getContext(), ["import-bank-statement", "asnbank-CSV", $fixture,  "2022-03-31 12:00:00" ]);
        // $this->checkResult($fixture);
    }
}
