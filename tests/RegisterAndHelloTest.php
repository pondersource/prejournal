<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/commands/hello.php');
require_once(__DIR__ . '/../src/commands/register.php');


final class RegisterAndHelloTest extends TestCase
{
    public function testUserNotFound(): void
    {
        $this->assertEquals(
            [ "User not found or wrong password" ],
            hello(['hello'])
        );
    }
}




