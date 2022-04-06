<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../src/utils.php');


final class UtilsTest extends TestCase
{
    public function testReconcileQuotes(): void
    {
        $words = [ "a", "\"hello", "world\""];
        $result = reconcileQuotes($words);
        $this->assertEquals(
            $result,
            [ "a", "hello world"]
        );
    }
}
