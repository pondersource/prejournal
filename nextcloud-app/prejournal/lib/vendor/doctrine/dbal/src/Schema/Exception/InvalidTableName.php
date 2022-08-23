<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Schema\Exception;

use Doctrine\DBAM\Schema\SchemaException;

use function sprintf;

/**
 * @psalm-immutable
 */
final class InvalidTableName extends SchemaException
{
    public static function new(string $tableName): self
    {
        return new self(sprintf('Invalid table name specified "%s".', $tableName));
    }
}
