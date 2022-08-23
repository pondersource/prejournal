<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\Mysqli\Exception;

use Doctrine\DBAM\Driver\AbstractException;
use mysqli;
use mysqli_sql_exception;
use ReflectionProperty;

/**
 * @internal
 *
 * @psalm-immutable
 */
final class ConnectionError extends AbstractException
{
    public static function new(mysqli $connection): self
    {
        return new self($connection->error, $connection->sqlstate, $connection->errno);
    }

    public static function upcast(mysqli_sql_exception $exception): self
    {
        $p = new ReflectionProperty(mysqli_sql_exception::class, 'sqlstate');
        $p->setAccessible(true);

        return new self($exception->getMessage(), $p->getValue($exception), (int) $exception->getCode(), $exception);
    }
}
