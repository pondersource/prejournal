<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/run-command.php');

final class WorkedTest extends TestCase
{
    public function testSubmitExpense(): void
    {
        setTestDb();
        $testuserID = intval(runCommand([ 'adminParty' => true ], ['register', 'testusername', 'passwd'])[0]);
        setUser('testusername', 'passwd');
        runCommand(getContext(), ["submit-expensey","23 August 2021","companyname","railway","conference","transport","100","employer"]);
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'testusername'
            ],
            [
                'id' => 2,
                'name' => 'employer'
            ],
            [
                'id' => 3,
                'name' => 'railway'
            ],
            [
                'id' => 4,
                'name' => 'companyname'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'payment',
                'fromcomponent' => 2,
                'tocomponent' => 3,
                'timestamp_' => '1629669600',
                'amount' => 100           
            ],
            [
                'id' => 2,
                'type_' => 'invoice',
                'fromcomponent' => 3,
                'tocomponent' => 4,
                'timestamp_' => '1629669600',
                'amount' => 100           
            ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 1,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '1629669600',
            ],
            [
                'id' => 2,
                'movementid' => 2,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '1629669600',
            ]
        ], getAllStatements());
   
   
   
   
   
    }
}