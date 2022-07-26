<?php

declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../src/run-command.php');


final class PtaMeTest extends TestCase
{
    public function testUserNotFound(): void
    {
        setTestDb();
        $this->assertEquals(
            [ "User not found or wrong password" ],
            runCommand([], ['pta-me'])
        );
    }
    public function testUserRegisteredAndFound(): void
    {
        setTestDb();
        $userId = intval(runCommand([ 'adminParty' => true ], ['register', 'someuser', 'somepass'])[0]);
        setUser('someuser', 'somepass', 'someemployer');
        $movementId = intval(createMovement(getContext(), ['create-movement', $userId, 'invoice', 'foo', 'bar', '123456790', '12'])[0]);
        $statementId = intval(createStatement(getContext(), ['create-statement', strval($movementId), '123466790'])[0]);
        $this->assertEquals(
            [
                '1973-11-29 21:33:10',
                'assets  12',
                'income',
                ''
            ],
            runCommand(getContext(), ['pta-me'])
        );
    }
}
