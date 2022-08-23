<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\Mysqli\Exception;

use Doctrine\DBAM\Driver\AbstractException;

/**
 * @internal
 *
 * @psalm-immutable
 */
final class HostRequired extends AbstractException
{
    public static function forPersistentConnection(): self
    {
        return new self('The "host" parameter is required for a persistent connection');
    }
}
