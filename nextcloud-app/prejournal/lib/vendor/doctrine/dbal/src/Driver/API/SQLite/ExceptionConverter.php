<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\API\SQLite;

use Doctrine\DBAM\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use Doctrine\DBAM\Driver\Exception;
use Doctrine\DBAM\Exception\ConnectionException;
use Doctrine\DBAM\Exception\DriverException;
use Doctrine\DBAM\Exception\InvalidFieldNameException;
use Doctrine\DBAM\Exception\LockWaitTimeoutException;
use Doctrine\DBAM\Exception\NonUniqueFieldNameException;
use Doctrine\DBAM\Exception\NotNullConstraintViolationException;
use Doctrine\DBAM\Exception\ReadOnlyException;
use Doctrine\DBAM\Exception\SyntaxErrorException;
use Doctrine\DBAM\Exception\TableExistsException;
use Doctrine\DBAM\Exception\TableNotFoundException;
use Doctrine\DBAM\Exception\UniqueConstraintViolationException;
use Doctrine\DBAM\Query;

use function strpos;

/**
 * @internal
 */
final class ExceptionConverter implements ExceptionConverterInterface
{
    /**
     * @link http://www.sqlite.org/c3ref/c_abort.html
     */
    public function convert(Exception $exception, ?Query $query): DriverException
    {
        if (strpos($exception->getMessage(), 'database is locked') !== false) {
            return new LockWaitTimeoutException($exception, $query);
        }

        if (
            strpos($exception->getMessage(), 'must be unique') !== false ||
            strpos($exception->getMessage(), 'is not unique') !== false ||
            strpos($exception->getMessage(), 'are not unique') !== false ||
            strpos($exception->getMessage(), 'UNIQUE constraint failed') !== false
        ) {
            return new UniqueConstraintViolationException($exception, $query);
        }

        if (
            strpos($exception->getMessage(), 'may not be NULL') !== false ||
            strpos($exception->getMessage(), 'NOT NULL constraint failed') !== false
        ) {
            return new NotNullConstraintViolationException($exception, $query);
        }

        if (strpos($exception->getMessage(), 'no such table:') !== false) {
            return new TableNotFoundException($exception, $query);
        }

        if (strpos($exception->getMessage(), 'already exists') !== false) {
            return new TableExistsException($exception, $query);
        }

        if (strpos($exception->getMessage(), 'has no column named') !== false) {
            return new InvalidFieldNameException($exception, $query);
        }

        if (strpos($exception->getMessage(), 'ambiguous column name') !== false) {
            return new NonUniqueFieldNameException($exception, $query);
        }

        if (strpos($exception->getMessage(), 'syntax error') !== false) {
            return new SyntaxErrorException($exception, $query);
        }

        if (strpos($exception->getMessage(), 'attempt to write a readonly database') !== false) {
            return new ReadOnlyException($exception, $query);
        }

        if (strpos($exception->getMessage(), 'unable to open database file') !== false) {
            return new ConnectionException($exception, $query);
        }

        return new DriverException($exception, $query);
    }
}
