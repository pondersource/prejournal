<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Schema\Exception;

use Doctrine\DBAM\Schema\SchemaException;

use function sprintf;

/**
 * @psalm-immutable
 */
final class UnknownColumnOption extends SchemaException
{
    public static function new(string $name): self
    {
        return new self(
            sprintf('The "%s" column option is not supported.', $name)
        );
    }
}
