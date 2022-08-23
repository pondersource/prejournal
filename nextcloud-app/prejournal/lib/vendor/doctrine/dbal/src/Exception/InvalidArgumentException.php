<?php

namespace Doctrine\DBAM\Exception;

use Doctrine\DBAM\Exception;

/**
 * Exception to be thrown when invalid arguments are passed to any DBAM API
 *
 * @psalm-immutable
 */
class InvalidArgumentException extends Exception
{
    /**
     * @return self
     */
    public static function fromEmptyCriteria()
    {
        return new self('Empty criteria was used, expected non-empty criteria');
    }
}
