<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/run-command.php');


final class ImportTimetipJsonTest extends TestCase
{
    public function testParseTimetipJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123');
        $fixture = __DIR__ . "/fixtures/timetip-JSON.json";
        $result = runCommand(getContext(), ["import-timetip", "timetip-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => '2022-03-27T19:00:00.000Z'
            ],
            [
                'id' => 2,
                'name' => 'break'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1970-01-02 05:03:00',
                'amount' => '0'           ]
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