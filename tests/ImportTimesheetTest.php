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
                'amount' => '5',
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimeBroCsv(): void
    {
        setTestDb();
        $aliceId = intval(register([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeBro-CSV.csv";
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
                'amount' => '5',
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimetipJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timetip-JSON.json";
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
                'amount' => '104580',
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimeTrackerXml(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timetracker-XML.xml";
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
                'timestamp_' => '2022-03-30 00:00:00',
                'amount' => '15',
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimeDoctorCsv(): void
    {
        setTestDb();
        $aliceId = intval(register([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeDoctor-CSV.csv";
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
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseShaveMyTimeCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/saveMyTime-CSV.csv";
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
                'amount' => '560',
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseScoroJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/scoro-JSON.json";
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
                'amount' => '1',
                'description' => null
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
                            ]
        ], getAllStatements());
    }
    public function testParseStratustimeJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/stratustime-JSON.json";
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
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimeManagerCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeManager-CSV.csv";
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
                'amount' => '1',
                'description' => null
           ]
        ], getAllMovements());
        $this->assertEquals([
            [
                'id' => 1,
                'movementid' => 1,
                'userid' => 1,
                'sourcedocumentformat' => null,
                'sourcedocumentfilename' => null,
                'timestamp_' => '2022-04-02 00:00:00',
                            ]
        ], getAllStatements());
    }

    public function testParseTimeTrackerNextcloudJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeTrackerNextcloud-JSON.json";
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
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimeTrackerCliJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeTrackerCli-JSON.json";
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
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseVerifyTimeJson(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/verifyInvoice-JSON.json";
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
                'amount' => '3.75',
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimeTrackerDailyCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timeTrackerDaily-CSV.csv";
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
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimelyCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timely-CSV.csv";
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
                'amount' => '3.75',
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimesheetCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timesheet-CSV.csv";
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
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimecampCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timecamp-CSV.csv";
        $result = runCommand(getContext(), ["import-hours", "timecamp-CSV", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'alice'
            ],
            [
                'id' => 2,
                'name' => '(time without task assigned)'
            ]
        ], getAllComponents());
        $this->assertEquals([
            [
                'id' => 1,
                'type_' => 'worked',
                'fromcomponent' => 1,
                'tocomponent' => 2,
                'timestamp_' => '1970-01-01 00:00:08',
                'amount' => '8',
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testParseTimesheeMobileCsv(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/timesheetMobile-CSV.csv";
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
                'description' => null
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
                            ]
        ], getAllStatements());
    }

    public function testImportApiWikiSuite(): void
    {
        setTestDb();
        $aliceId = intval(runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/wiki-suite-JSON.json";
        $result = runCommand(getContext(), ["import-hours", "wiki-suite-JSON", $fixture,  "2022-03-31 12:00:00" ]);

        $this->assertEquals([
                [
                    'id' => 1,
                    'name' => 'victor'
                ],
                [
                    'id' => 2,
                    'name' => 'Federated timesheets'
                ],
                [
                    'id' => 3,
                    'name' => 'Tiki'
                ],
                [
                    'id' => 4,
                    'name' => 'kroky'
                ],
                [
                    'id' => 5,
                    'name' => 'Timesheet tracking'
                ],
                [
                    'id' => 6,
                    'name' => 'ishifoev'
                ],
                [
                    'id' => 7,
                    'name' => 'Test'
                ]
            ], getAllComponents());
        $this->assertEquals([
                [
                    'id' => 1,
                    'type_' => 'worked',
                    'fromcomponent' => 1,
                    'tocomponent' => 2,
                    'timestamp_' => '1970-01-01 00:00:00',
                    'amount' => '1',
                    'description' => null
                ],
                [
                    'id' => 2,
                    'type_' => 'worked',
                    'fromcomponent' => 1,
                    'tocomponent' => 3,
                    'timestamp_' => '2022-04-21 10:30:00',
                    'amount' => '0',
                    'description' => null
                ],
                [
                    'id' => 3,
                    'type_' => 'worked',
                    'fromcomponent' => 4,
                    'tocomponent' => 5,
                    'timestamp_' => '1970-01-01 00:00:00',
                    'amount' => '1',
                    'description' => null
                ],
                [
                    'id' => 4,
                    'type_' => 'worked',
                    'fromcomponent' => 6,
                    'tocomponent' => 7,
                    'timestamp_' => '1970-01-01 00:00:00',
                    'amount' => '5',
                    'description' => null
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
                ],
                [
                    'id' => 3,
                    'movementid' => 3,
                    'userid' => 1,
                    'sourcedocumentformat' => null,
                    'sourcedocumentfilename' => null,
                    'timestamp_' => '2022-03-31 12:00:00',
                ],
                [
                    'id' => 4,
                    'movementid' => 4,
                    'userid' => 1,
                    'sourcedocumentformat' => null,
                    'sourcedocumentfilename' => null,
                    'timestamp_' => '2022-03-31 12:00:00',
                ]
            ], getAllStatements());
    }

    public function testWikiApiExport(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        $result = runCommand(getContext(), ["wiki-api-export", "wiki"]);
        $this->assertEquals([
            0 => 'Try again to insert data inside sync and movement'
        ],$result);
        //var_dump($result);
        setUser('bob', 'bob123', 'employer');
        //$this->assertEquals([
          //  "1", "2", "3","4","5"
        //], runCommand(getContext(), ["print-timesheet-json", "Test", 2, 100]));
    }

    public function testWikiApiImport(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        $result = runCommand(getContext(), ["wiki-api-import", "wiki"]);
        $this->assertEquals(null,$result);
        //var_dump($result);
        setUser('bob', 'bob123', 'employer');
        //$this->assertEquals([
          //  "1", "2", "3","4","5"
        //], runCommand(getContext(), ["print-timesheet-json", "Test", 2, 100]));
    }

    public function testImportEntry(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        $fixture = __DIR__ . "/fixtures/wiki-suite-JSON.json";
        $result = runCommand(getContext(), ["import-timesheet", "wikiApi-JSON", $fixture, "2022-03-31 12:00:00" ]);
        $this->assertEquals([
            0 => '5'
        ],$result);
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
        $result =  runCommand(getContext(), ["print-timesheet-json", "Test", 2, 4]);
        $this->assertEquals(null, $result);
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
        $result =  runCommand(getContext(), [ "worked-day", "23 August 2021", "stichting", "Peppol for the Masses" ]);
        $this->assertEquals([
          0 => 'Created movement 1',
          1 => 'Created statement 1'
        ], $result);
        //var_dump($result);
    }

    public function testWorkedWeek(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        $result =  runCommand(getContext(), [ "worked-week", "22 November 2021", "stichting", "ScienceMesh", "Almost done"]);
        $this->assertEquals([
          0 => 'Created movement 1',
          1 => 'Created statement 1'
        ], $result);
        //var_dump($result);
    }

    public function testWorkedHours(): void
    {
        setTestDb();
        runCommand([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0];
        runCommand([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0];
        setUser('alice', 'alice123', 'employer');
        $result =  runCommand(getContext(), [ "worked-hours", "20 September 2021", "stichting", "Peppol for the Masses", 4]);
        $this->assertEquals([
          0 => 'Created movement 1',
          1 => 'Created statement 1'
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
