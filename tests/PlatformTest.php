<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/platform.php');

final class PlatformTest extends TestCase
{
    public function testSetUser(): void
    {
        $result = setUser('alice', 'alice123');

        $this->assertEquals(
            $result,
            null
        );
    }
    public function testGetUser(): void
    {
        $result = getUser();
        $this->assertEquals(
            $result,
            ['id' => 1,'username' => 'alice']
        );
    }
}
