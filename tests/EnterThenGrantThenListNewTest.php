<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/commands/register.php');
require_once(__DIR__ . '/../src/commands/enter.php');
require_once(__DIR__ . '/../src/commands/grant.php');
require_once(__DIR__ . '/../src/commands/list-new.php');


final class EnterThenGrantThenListNewTest extends TestCase
{
    public function testEnterThenGrantThenListNew(): void
    {
        setTestDb();
        $aliceId = intval(register([ 'adminParty' => true ], ['register', 'alice', 'alice123'])[0]);
        $bobId = intval(register([ 'adminParty' => true ], ['register', 'bob', 'bob123'])[0]);
        setUser('alice', 'alice123');
        enter(getContext(), ["enter", "from component", "to component", "1.23", "1234567890", "invoice", "ponder-source-agreement-192" ]);
        grant(getContext(), ["grant", "bob", "from component"]);
        setUser('bob', 'bob123');
        $this->assertEquals([
            '1234567890, from component, to component, 1.23, alice'
        ], listNew(getContext(), ['list-new']));
    }
}

// in curl commands:
// curl -d'["alice","alice123"]' http://localhost:8080/v1/register
// curl -d'["bob","bob123"]' http://localhost:8080/v1/register
// curl -d'["from component", "to component", "1.23", "2021-12-31T23:00:00.000Z", "invoice", "ponder-source-agreement-192"]' http://alice:alice123@localhost:8080/v1/enter
// curl -d'["bob", "from component"]' http://alice:alice123@localhost:8080/v1/grant
// curl http://bob:bob123@localhost:8080/v1/list-new
