<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../src/run-command.php');


final class ImportInvoiceTest extends TestCase
{
    public function testImportInvoice(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        $bobId = intval(runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0]);
        setUser('alice', 'alice123', 'employer');
        runCommand(getContext(), ["claim-component", "ismoil" ]);
        runCommand(getContext(), ["enter", "ismoil", "alex", "4.00", "1123211312", "invoice", "ponder-source" ]);
        setUser('bob', 'bob123', 'employer');
        $this->assertEquals([
            'timestamp, from, to, amount, observer',
        ], runCommand(getContext(), ['list-new']));
    }
    public function testParseVerifyInvoiceJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/verifyInvoice-JSON.json";
        runCommand(getContext(), ['claim-component', 'Alex Malikov']);
        $result = runCommand(getContext(), ["import-invoice", "verifyInvoice-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'Alex Malikov'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'invoice',
                'fromcomponent' => 1,
                'tocomponent' => 1,
                'timestamp_' => '1970-01-01 00:00:00',
                'amount' => '0',
                'unit' => 'EUR',
                'subindex' => 0,
                'deleted' => false,
                'userid' => 1
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
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseHerokuInvoiceJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeHerokuInvoice-JSON.json";
        runCommand(getContext(), ['claim-component', 'alice']);
        $result = runCommand(getContext(), ["import-invoice", "timeHerokuInvoice-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alice'
            ],
            [
                'id' => 2,
                'name' => '56ae3c14-906b-4858-a8ff-a6347b9bb183'
            ],
            [
                'id' => 3,
                'name' => '9aa53ffa-ce4f-487d-ba74-2fffb3c98e04'
            ],
            [
                'id' => 4,
                'name' => 'c1f7f8b3-b063-4001-aebd-7a7600ebf206'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'invoice',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1970-01-01 00:33:42',
                'amount' => '0',
                'unit' => 'EUR',
                'subindex' => 0,
                'deleted' => false,
                'userid' => 1
            ],
            [
                'id' => 2,
                'type_' => 'invoice',
                'fromcomponent' => 1,
                'tocomponent' => 3,
                'timestamp_' => '1970-01-01 00:33:42',
                'amount' => '0',
                'unit' => 'EUR',
                'subindex' => 0,
                'deleted' => false,
                'userid' => 1
            ],
            [
                'id' => 3,
                'type_' => 'invoice',
                'fromcomponent' => 1,
                'tocomponent' => 4,
                'timestamp_' => '1970-01-01 00:33:42',
                'amount' => '0',
                'unit' => 'EUR',
                'subindex' => 0,
                'deleted' => false,
                'userid' => 1
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
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
            ],
            [
                'id' => 2,
                'movementid' => 2,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
            ],
            [
                'id' => 3,
                'movementid' => 3,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
            ]
        ], getAllStatements());
    }
}
