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
final class NonTerminatedStringLiteral extends AbstractException
{
    public static function new(int $offset): self
    {
        return new self(
            sprintf(
                'The statement contains non-terminated string literal starting at offset %d.',
                $offset
            )
        );
    }
}
