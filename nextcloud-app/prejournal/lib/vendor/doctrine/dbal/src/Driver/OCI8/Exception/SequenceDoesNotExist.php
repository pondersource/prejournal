<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\OCI8\Exception;

use Doctrine\DBAM\Driver\AbstractException;

/**
 * @internal
 *
 * @psalm-immutable
 */
final class SequenceDoesNotExist extends AbstractException
{
    public static function new(): self
    {
        return new self('lastInsertId failed: Query was executed but no result was returned.');
    }
}
