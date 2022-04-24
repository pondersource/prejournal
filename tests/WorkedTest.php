<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/run-command.php');

final class WorkedTest extends TestCase
{
    public function testWorkedDay(): void
    {
        setTestDb();
        $testuserID = intval(runCommand([ 'adminParty' => true ], ['register', 'testusername', 'passwd'])[0]);
        setUser('testusername', 'passwd','employer');
        runCommand(getContext(), ["worked-day","23 August 2021","stichting","Peppol for the Masses"]);
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'testusername'
            ],
            [
                'id' => 2,
                'name' => 'stichting:Peppol for the Masses'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1629669600',
                'amount' => 8           
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
            ]
        ], getAllStatements());
    }

    public function testWorkedHours(): void
    {
        setTestDb();
        $testuserID = intval(runCommand([ 'adminParty' => true ], ['register', 'testusername', 'passwd'])[0]);
        setUser('testusername', 'passwd','employer');
        runCommand(getContext(), ["worked-hours","23 August 2021","stichting","Peppol for the Masses","4"]);
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'testusername'
            ],
            [
                'id' => 2,
                'name' => 'stichting:Peppol for the Masses'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1629669600',
                'amount' => 4 
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
            ]
        ], getAllStatements());
    }

    public function testWorkedWeek(): void
    {
        setTestDb();
        $testuserID = intval(runCommand([ 'adminParty' => true ], ['register', 'testusername', 'passwd'])[0]);
        setUser('testusername', 'passwd','employer');
        runCommand(getContext(), ["worked-week","23 August 2021","stichting","Peppol for the Masses"]);
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'testusername'
            ],
            [
                'id' => 2,
                'name' => 'stichting:Peppol for the Masses'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1629669600',
                'amount' => 40           
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
            ]
        ], getAllStatements());
    }
}