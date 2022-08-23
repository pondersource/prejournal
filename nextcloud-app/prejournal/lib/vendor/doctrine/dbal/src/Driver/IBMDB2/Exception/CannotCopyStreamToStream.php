<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\IBMDB2\Exception;

use Doctrine\DBAM\Driver\AbstractException;

/**
 * @internal
 *
 * @psalm-immutable
 */
final class CannotCopyStreamToStream extends AbstractException
{
    /**
     * @psalm-param array{message: string}|null $error
     */
    public static function new(?array $error): self
    {
        $message = 'Could not copy source stream to temporary file';

        if ($error !== null) {
            $message .= ': ' . $error['message'];
        }

        return new self($message);
    }
}
