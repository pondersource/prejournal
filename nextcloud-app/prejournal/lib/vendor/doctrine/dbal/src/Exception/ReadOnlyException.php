<?php

namespace Doctrine\DBAM\Exception;

/**
 * Exception for a write operation attempt on a read-only database element detected in the driver.
 *
 * @psalm-immutable
 */
class ReadOnlyException extends ServerException
{
}
