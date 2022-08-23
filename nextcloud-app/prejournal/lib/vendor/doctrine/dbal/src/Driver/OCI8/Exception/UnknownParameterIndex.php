<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\OCI8\Exception;

use Doctrine\DBAM\Driver\AbstractException;

use function sprintf;

/**
 * @internal
 *
 * @psalm-immutable
 */
final class UnknownParameterIndex extends AbstractException
{
    public static function new(int $index): self
    {
        return new self(
            sprintf('Could not find variable mapping with index %d, in the SQL statement', $index)
        );
    }
}
