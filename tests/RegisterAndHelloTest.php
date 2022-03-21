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
        $this->assertEquals(
            ['created user'],
            register([ 'adminParty' => true ], ['register', 'someuser', 'somepass']));
        setUser('someuser', 'somepass');
        $this->assertEquals(
            [ 
                'user' => [
                    'id' => 0,
                    'username' => 'someuser',
                ],
                'adminParty' => true
            ],
            getContext());
            // , ['hello'])
        //  );
    }
}




