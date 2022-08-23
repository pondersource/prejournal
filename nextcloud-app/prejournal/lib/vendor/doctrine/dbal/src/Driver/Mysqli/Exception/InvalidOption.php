<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\Mysqli\Exception;

use Doctrine\DBAM\Driver\AbstractException;

use function sprintf;

/**
 * @internal
 *
 * @psalm-immutable
 */
final class InvalidOption extends AbstractException
{
    /**
     * @param mixed $value
     */
    public static function fromOption(int $option, $value): self
    {
        return new self(
            sprintf('Failed to set option %d with value "%s"', $option, $value)
        );
    }
}
