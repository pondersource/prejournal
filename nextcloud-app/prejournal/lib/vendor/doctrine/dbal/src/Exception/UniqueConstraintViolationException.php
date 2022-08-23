<?php

namespace Doctrine\DBAM\Exception;

/**
 * Exception for a unique constraint violation detected in the driver.
 *
 * @psalm-immutable
 */
class UniqueConstraintViolationException extends ConstraintViolationException
{
}
