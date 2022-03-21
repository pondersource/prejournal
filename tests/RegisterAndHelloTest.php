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
        $this->assertEquals(
            [
                'user' => [
                    'id' => 0,
                    'username' => 'someuser',
                ],
                'adminParty' => true
            ],
            getContext()
        );

        $this->assertEquals(
            [ "Hello someuser, your userId is 0" ],
            hello(getContext(), ['hello'])
        );
    }
    public function testUserRegisteredLoginWrongPass(): void
    {
        setTestDb();
        $this->assertEquals(['created user'], register([ 'adminParty' => true ], ['register', 'someuser', 'somepass']));

        // wrong pass
        setUser('someuser', 'wrongpass');
        $this->assertEquals([
                'user' => null,
                'adminParty' => true
            ],
            getContext()
        );

        $this->assertEquals(
            [ "User not found or wrong password" ],
            hello(getContext(), ['hello'])
        );
    }
    public function testUserRegisteredLoginWrongUsername(): void
    {
        setTestDb();
        $this->assertEquals(['created user'], register([ 'adminParty' => true ], ['register', 'someuser', 'somepass']));

        // wrong user
        setUser('wronguser', 'somepass');
        $this->assertEquals([
                'user' => null,
                'adminParty' => true
            ],
            getContext()
        );

        $this->assertEquals(
            [ "User not found or wrong password" ],
            hello(getContext(), ['hello'])
        );

    }
}




