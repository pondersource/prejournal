<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/commands/hello.php');
require_once(__DIR__ . '/../src/commands/register.php');


final class RegisterAndHelloTest extends TestCase
{
    public function testUserNotFound(): void
    {
        setTestDb();
        $this->assertEquals(
            [ "User not found or wrong password" ],
            hello([], ['hello'])
        );
    }
    public function testUserRegisteredAndFound(): void
    {
        setTestDb();
        $this->assertEquals(['created user'], register([ 'adminParty' => true ], ['register', 'someuser', 'somepass']));
        // right user and pass
        setUser('someuser', 'somepass');
        $this->assertEquals([
                'user' => [
                    'id' => 0,
                    'username' => 'someuser',
                ],
                'adminParty' => true
            ],
            getContext());
        setUser('someuser', 'wrongpass');
        // wrong pass
        $this->assertEquals([
                'user' => null,
                'adminParty' => true
            ],
            getContext());
        // wrong user
        setUser('wronguser', 'somepass');
        $this->assertEquals([
                'user' => null,
                'adminParty' => true
            ],
            getContext());
  
    }
}




