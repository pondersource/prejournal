<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../src/run-command.php');


final class ImportTimesheetTest extends TestCase
{
    public function testParseTimeCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/time-CSV.csv";
        $_SERVER["PREJOURNAL_DEFAULT_EMPLOYER"] = "employer";
        runCommand(getContext(), ['claim-component', 'alex.malikov94@gmail.com']);
        $result = runCommand(getContext(), ["import-hours", "time-CSV", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alex.malikov94@gmail.com'
            ],
            [
                'id' => 2,
                'name' => 'any'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-18 09:39:19',
                'amount' => '0.0013888888888889',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'time-CSV',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseTimeBroCsv(): void
    {
        setTestDb();
        $aliceId = intval(register([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeBro-CSV.csv";
        runCommand(getContext(), ['claim-component', 'alex.malikov94@gmail.com']);
        $result = runCommand(getContext(), ["import-hours", "timeBro-CSV", $fixture,  "2022-03-31 12:00:00" ]);
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alex.malikov94@gmail.com'
            ],
            [
                'id' => 2,
                'name' => 'test'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-18 09:39:19',
                'amount' => '0.0013888888888889',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timeBro-CSV',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseTimetipJson(): void
    {
        setTestDb();
        runCommand(getContext(), ['claim-component', 'alice']);
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timetip-JSON.json";
        runCommand(getContext(), ['claim-component', 'alice']);
        $result = runCommand(getContext(), ["import-hours", "timetip-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alice'
            ],
            [
                'id' => 2,
                'name' => 'coffee break'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-27 19:00:00',
                'amount' => '29.05',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timetip-JSON',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseTimeTrackerXml(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timetracker-XML.xml";
        runCommand(getContext(), ["claim-component", "alex"]);
        $result = runCommand(getContext(), ["import-hours", "timetracker-XML", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alex'
            ],
            [
                'id' => 2,
                'name' => 'test'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-30 14:34:00',
                'amount' => '1',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timetracker-XML',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseTimeDoctorCsv(): void
    {
        setTestDb();
        $aliceId = intval(register([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeDoctor-CSV.csv";
        runCommand(getContext(), ['claim-component', 'alex.malikov94@gmail.com']);
        $result = runCommand(getContext(), ["import-hours", "timeDoctor-CSV", $fixture,  "2022-03-31 12:00:00" ]);
        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alex.malikov94@gmail.com'
            ],
            [
                'id' => 2,
                'name' => ' test'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-18 00:00:00',
                'amount' => '0',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timeDoctor-CSV',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseSaveMyTimeCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/saveMyTime-CSV.csv";
        runCommand(getContext(), ['claim-component', 'alice']);
        $result = runCommand(getContext(), ["import-hours", "saveMyTime-CSV", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alice'
            ],
            [
                'id' => 2,
                'name' => 'default-project'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-25 14:09:38',
                'amount' => '0.15555555555556',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'saveMyTime-CSV',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseScoroJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/scoro-JSON.json";
        runCommand(getContext(), [ "claim-component", "alice"]);
        $result = runCommand(getContext(), ["import-hours", "scoro-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alice'
            ],
            [
                'id' => 2,
                'name' => '0'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2017-03-13 16:00:00',
                'amount' => '461593',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'scoro-JSON',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }
    public function testParseStratustimeJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/stratustime-JSON.json";
        runCommand(getContext(), [ "claim-component", "2147483647"]);
        $result = runCommand(getContext(), ["import-hours", "stratustime-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => '2147483647'
            ],
            [
                'id' => 2,
                'name' => 'default-project'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1970-01-01 00:00:00',
                'amount' => '0',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'stratustime-JSON',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseTimeManagerCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeManager-CSV.csv";
        runCommand(getContext(), [ "claim-component", "test"]);
        $result = runCommand(getContext(), ["import-hours", "timeManager-CSV", $fixture,  "2022-04-02 00:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'test'
            ],
            [
                'id' => 2,
                'name' => 'project1'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1970-01-01 00:00:00',
                'amount' => '0.00027777777777778',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timeManager-CSV',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-04-02 00:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseTimeTrackerNextcloudJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeTrackerNextcloud-JSON.json";
        runCommand(getContext(), [ "claim-component", "einstein"]);
        $result = runCommand(getContext(), ["import-hours", "timeTrackerNextcloud-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'einstein'
            ],
            [
                'id' => 2,
                'name' => 'project1'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1970-01-01 00:00:04',
                'amount' => '0',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timeTrackerNextcloud-JSON',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseTimeTrackerCliJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeTrackerCli-JSON.json";
        runCommand(getContext(), [ "claim-component", "alice"]);
        $result = runCommand(getContext(), ["import-hours", "timeTrackerCli-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alice'
            ],
            [
                'id' => 2,
                'name' => 'FINISHED'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1970-01-01 00:00:00',
                'amount' => '0',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timeTrackerCli-JSON',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseVerifyTimeJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/verifyInvoice-JSON.json";
        runCommand(getContext(), [ "claim-component", "Alex Malikov"]);
        $result = runCommand(getContext(), ["import-hours", "verifyTime-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'Alex Malikov'
            ],
            [
                'id' => 2,
                'name' => 'food'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-04-12 09:34:45',
                'amount' => '0.0010416666666667',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'verifyTime-JSON',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseTimeTrackerDailyCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeTrackerDaily-CSV.csv";
        runCommand(getContext(), [ "claim-component", "Alex"]);
        $result = runCommand(getContext(), ["import-hours", "timeTrackerDaily-CSV", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'Alex'
            ],
            [
                'id' => 2,
                'name' => 'test'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-17 00:00:00',
                'amount' => '0',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timeTrackerDaily-CSV',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseTimelyCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timely-CSV.csv";
        runCommand(getContext(), [ "claim-component", "Alex Malikov"]);
        $result = runCommand(getContext(), ["import-hours", "timely-CSV", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'Alex Malikov'
            ],
            [
                'id' => 2,
                'name' => 'Communication'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1970-01-01 00:00:00',
                'amount' => '0.0010416666666667',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timely-CSV',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    public function testParseTimesheetCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timesheet-CSV.csv";
        runCommand(getContext(), [ "claim-component", "alice"]);
        $result = runCommand(getContext(), ["import-hours", "timesheet-CSV", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alice'
            ],
            [
                'id' => 2,
                'name' => 'test'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-29 15:30:00',
                'amount' => '0',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timesheet-CSV',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    // FIXME: it's not parsing Timecamp CSV correctly.
    //
    // public function testParseTimecampCsv(): void
    // {
    //     setTestDb();
    //     $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
    //     setUser('alice', 'alice123', 'employer');
    //     $fixture = __DIR__ . "/fixtures/timecamp-CSV.csv";
    //     runCommand(getContext(), [ "claim-component", "alice"]);
    //     $result = runCommand(getContext(), ["import-hours", "timecamp-CSV", $fixture,  "2022-03-31 12:00:00" ]);

    //     $this->assertEquals([
    //         [
    //             'id' => 1,
    //             'name' => 'alice'
    //         ],
    //         [
    //             'id' => 2,
    //             'name' => '(time without task assigned)'
    //         ]
    //     ], getAllComponents());
    //     $this->assertEquals([
    //         [
    //             'id' => 1,
    //             'type_' => 'worked',
    //             'fromcomponent' => 1,
    //             'tocomponent' => 2,
    //             'timestamp_' => '1970-01-01 00:00:08',
    //             'amount' => '8',
    //             'userid' => 1
    //             ]
    //     ], getAllMovements());
    //     $this->assertEquals([
    //         [
    //             'id' => 1,
    //             'movementid' => 1,
    //             'userid' => 1,
    //             'sourcedocumentformat' => null,
    //             'sourcedocumentfilename' => null,
    //             'timestamp_' => '2022-03-31 12:00:00',
    //             'description' => null,
    //             'internal_type' => null,
    //             'remote_id' => null,
    //             'remote_system' => null
    //             ]
    //     ], getAllStatements());
    // }

    public function testParseTimesheeMobileCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timesheetMobile-CSV.csv";
        runCommand(getContext(), [ "claim-component", "\"Alex Malikov\""]);
        $result = runCommand(getContext(), ["import-hours", "timesheetMobile-CSV", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => '"Alex Malikov"'
            ],
            [
                'id' => 2,
                'name' => 'test'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '2022-03-30 00:00:00',
                'amount' => '0',
                'unit' => 'hours',
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
                'sourcedocumentformat' => 'timesheetMobile-CSV',
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-03-31 12:00:00',
                'description' => null,
                'internal_type' => null,
                'remote_id' => null,
                'remote_system' => null
                ]
        ], getAllStatements());
    }

    // FIXME: These tests rely on a real-world URL
    // to be available, which is not best practice for
    // a unit test.
    //
    // public function testImportApiWikiSuite(): void
    // {
    //     $_SERVER["WIKI_HOST"] = "https://api.wiki.host/v1/";
    //     $_SERVER["WIKI_TOKEN"] = "some-token";
    //     setTestDb();
    //     $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
    //     setUser('alice', 'alice123', 'employer');
    //     $fixture = __DIR__ . "/fixtures/wiki-suite-JSON.json";
    //     runCommand(getContext(), [ "claim-component", "alice"]);
    //     $result = runCommand(getContext(), ["import-hours", "wiki-suite-JSON", $fixture,  "2022-03-31 12:00:00" ]);

    //     $this->assertEquals([
    //         [
    //             'id' => 1,
    //             'name' => 'victor'
    //         ],
    //         [
    //             'id' => 2,
    //             'name' => 'Federated timesheets'
    //         ],
    //         [
    //             'id' => 3,
    //             'name' => 'Tiki'
    //         ],
    //         [
    //             'id' => 4,
    //             'name' => 'kroky'
    //         ],
    //         [
    //             'id' => 5,
    //             'name' => 'Timesheet tracking'
    //         ],
    //         [
    //             'id' => 6,
    //             'name' => 'ishifoev'
    //         ],
    //         [
    //             'id' => 7,
    //             'name' => 'Test'
    //         ]
    //     ], getAllComponents());
    //     $this->assertEquals([
    //         [
    //             'id' => 1,
    //             'type_' => 'worked',
    //             'fromcomponent' => 1,
    //             'tocomponent' => 2,
    //             'timestamp_' => '1970-01-01 00:00:00',
    //             'amount' => '1',
    //             'userid' => 1
    //         ],
    //         [
    //             'id' => 2,
    //             'type_' => 'worked',
    //             'fromcomponent' => 1,
    //             'tocomponent' => 3,
    //             'timestamp_' => '2022-04-21 10:30:00',
    //             'amount' => '0',
    //             'userid' => 1
    //         ],
    //         [
    //             'id' => 3,
    //             'type_' => 'worked',
    //             'fromcomponent' => 4,
    //             'tocomponent' => 5,
    //             'timestamp_' => '1970-01-01 00:00:00',
    //             'amount' => '1',
    //             'userid' => 1
    //         ],
    //         [
    //             'id' => 4,
    //             'type_' => 'worked',
    //             'fromcomponent' => 6,
    //             'tocomponent' => 7,
    //             'timestamp_' => '1970-01-01 00:00:00',
    //             'amount' => '5',
    //             'userid' => 1
    //         ],
    //         [
    //             'id' => 5,
    //             'type_' => 'worked',
    //             'fromcomponent' => 6,
    //             'tocomponent' => 7,
    //             'timestamp_' => '1970-01-01 00:00:00',
    //             'amount' => '5',
    //             'userid' => 1
    //         ]
    //     ], getAllMovements());
    //     $this->assertEquals([
    //         [
    //             'id' => 1,
    //             'movementid' => 1,
    //             'userid' => 1,
    //             'sourcedocumentformat' => null,
    //             'sourcedocumentfilename' => null,
    //             'timestamp_' => '2022-03-31 12:00:00',
    //             'description' => null,
    //             'userid' => 1,
    //             'internal_type' => null,
    //             'remote_id' => null,
    //             'remote_system' => null
    //         ],
    //         [
    //             'id' => 2,
    //             'movementid' => 2,
    //             'userid' => 1,
    //             'sourcedocumentformat' => null,
    //             'sourcedocumentfilename' => null,
    //             'timestamp_' => '2022-03-31 12:00:00',
    //             'description' => null,
    //             'userid' => 1,
    //             'internal_type' => null,
    //             'remote_id' => null,
    //             'remote_system' => null
    //         ],
    //         [
    //             'id' => 3,
    //             'movementid' => 3,
    //             'userid' => 1,
    //             'sourcedocumentformat' => null,
    //             'sourcedocumentfilename' => null,
    //             'timestamp_' => '2022-03-31 12:00:00',
    //             'description' => null,
    //             'internal_type' => null,
    //             'remote_id' => null,
    //             'remote_system' => null
    //         ],
    //         [
    //             'id' => 4,
    //             'movementid' => 4,
    //             'userid' => 1,
    //             'sourcedocumentformat' => null,
    //             'sourcedocumentfilename' => null,
    //             'timestamp_' => '2022-03-31 12:00:00',
    //             'description' => null,
    //             'internal_type' => null,
    //             'remote_id' => null,
    //             'remote_system' => null
    //         ],
    //         [
    //             'id' => 5,
    //             'timestamp_' => '2022-03-31 12:00:00',
    //             'movementid' => 5,
    //             'userid' => 1,
    //             'sourcedocumentformat' => null,
    //             'sourcedocumentfilename' => null,
    //             'description' => null,
    //             'internal_type' => null,
    //             'remote_id' => null,
    //             'remote_system' => null
    //             ]
    //     ], getAllStatements());
    // }

    // public function testWikiApiExport(): void
    // {
    //     setTestDb();
    //     runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
    //     runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
    //     setUser('alice', 'alice123', 'employer');
    //     runCommand(getContext(), [ "claim-component", "alice"]);
    //     $result = runCommand(getContext(), ["wiki-api-export", "wiki"]);
    //     $this->assertEquals([
    //         0 => 'Try again to insert data inside sync and movement'
    //     ], $result);
    //     //var_dump($result);
    //     setUser('bob', 'bob123', 'employer');
    //     //$this->assertEquals([
    //       //  "1", "2", "3","4","5"
    //     //], runCommand(getContext(), ["print-timesheet-json", "Test", 2, 100]));
    // }

    // public function testWikiApiImport(): void
    // {
    //     setTestDb();
    //     runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
    //     runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
    //     setUser('alice', 'alice123', 'employer');
    //     runCommand(getContext(), [ "claim-component", "alice"]);
    //     $result = runCommand(getContext(), ["wiki-api-import", "wiki"]);
    //     $this->assertEquals(null, $result);
    //     //var_dump($result);
    //     setUser('bob', 'bob123', 'employer');
    //     //$this->assertEquals([
    //       //  "1", "2", "3","4","5"
    //     //], runCommand(getContext(), ["print-timesheet-json", "Test", 2, 100]));
    // }

    public function testImportEntry(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/wiki-suite-JSON.json";
        $result = runCommand(getContext(), ["import-timesheet", "wikiApi-JSON", $fixture, "2022-03-31 12:00:00" ]);
        $this->assertEquals([
            0 => '23' // number of entries in ../fixtures/wiki-suite-JSON.json
        ], $result);
        //var_dump($result);
        setUser('bob', 'bob123', 'employer');
        //$this->assertEquals([
          //  "1", "2", "3","4","5"
        //], runCommand(getContext(), ["print-timesheet-json", "Test", 2, 100]));
    }

    public function testExportEntry(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        $context = getContext();
        $context["openMode"] = true;
        $result =  runCommand($context, ["print-timesheet-json", "Test", 2, 4]);
        // var_dump($result);
        // FIXME: this doesn't match the format of run-command
        // because a command should always return an array of strings
        $this->assertEquals('[]', $result);
            // [
            //     "worker" => "alice",
            //     "project" => "stichting:Peppol for the Masses",
            //     "timestamp_" => "2021-09-20 00:00:00",
            //     "amount" => "4",
            //     "description" => "",
            //     "movementId" => 1,
            //     "statementId" => 1
            // ]
        // ], $result);
    }

    public function testRemove(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        $result =  runCommand(getContext(), [ "remove-entry",  "worked",   1 ]);
        $this->assertEquals(null, $result);
        //var_dump($result);
    }

    public function testWorkedDay(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        $context = getContext();
        // $context["openMode"] = true;
        runCommand($context, [ "claim-component", "alice"]);
        $result =  runCommand($context, [ "worked-day", "23 August 2021", "stichting", "Peppol for the Masses" ]);
        $this->assertEquals([
            "Created movement 1",
            "Created statement 1"
        ], $result);
        //var_dump($result);
    }

    public function testWorkedWeek(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        runCommand(getContext(), [ "claim-component", "alice"]);
        $result =  runCommand(getContext(), [ "worked-week", "22 November 2021", "stichting", "ScienceMesh", "Almost done"]);
        $this->assertEquals([
            "Created movement 1",
            "Created statement 1"
        ], $result);
        //var_dump($result);
    }

    public function testWorkedHours(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        runCommand(getContext(), [ "claim-component", "alice"]);
        $result =  runCommand(getContext(), [ "worked-hours", "20 September 2021", "stichting", "Peppol for the Masses", 4]);
        // FIXME: not consistent with worked-week or with worked-day
        // and also not consistent with the run-command interface which
        // expects to get back an array of strings.
        $this->assertEquals([
            '[
    {
        "worker": "alice",
        "project": "stichting:Peppol for the Masses",
        "timestamp_": "2021-09-20 00:00:00",
        "amount": "4",
        "description": "",
        "movementId": 1,
        "statementId": 1
    }
]'
        ], $result);
        //var_dump($result);
    }
}

// in curl commands:
// curl -d'["alice","alice123"]' http://localhost:8080/v1/register
// curl -d'["bob","bob123"]' http://localhost:8080/v1/register
// curl -d'["from component", "to component", "1.23", "2021-12-31T23:00:00.000Z", "invoice", "ponder-source-agreement-192"]' http://alice:alice123@localhost:8080/v1/enter
// curl -d'["bob", "from component"]' http://alice:alice123@localhost:8080/v1/grant
// curl http://bob:bob123@localhost:8080/v1/list-new
