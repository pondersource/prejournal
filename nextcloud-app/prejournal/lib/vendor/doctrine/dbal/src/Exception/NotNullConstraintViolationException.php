<?php

namespace Doctrine\DBAM\Exception;

/**
 * Exception for a NOT NULL constraint violation detected in the driver.
 *
 * @psalm-immutable
 */
class NotNullConstraintViolationException extends ConstraintViolationException
{
}
