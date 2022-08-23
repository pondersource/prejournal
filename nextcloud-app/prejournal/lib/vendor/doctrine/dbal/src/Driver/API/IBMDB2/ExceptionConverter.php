<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\API\IBMDB2;

use Doctrine\DBAM\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use Doctrine\DBAM\Driver\Exception;
use Doctrine\DBAM\Exception\ConnectionException;
use Doctrine\DBAM\Exception\DriverException;
use Doctrine\DBAM\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAM\Exception\InvalidFieldNameException;
use Doctrine\DBAM\Exception\NonUniqueFieldNameException;
use Doctrine\DBAM\Exception\NotNullConstraintViolationException;
use Doctrine\DBAM\Exception\SyntaxErrorException;
use Doctrine\DBAM\Exception\TableExistsException;
use Doctrine\DBAM\Exception\TableNotFoundException;
use Doctrine\DBAM\Exception\UniqueConstraintViolationException;
use Doctrine\DBAM\Query;

/**
 * @internal
 *
 * @link https://www.ibm.com/docs/en/db2/11.5?topic=messages-sql
 */
final class ExceptionConverter implements ExceptionConverterInterface
{
    public function convert(Exception $exception, ?Query $query): DriverException
    {
        switch ($exception->getCode()) {
            case -104:
                return new SyntaxErrorException($exception, $query);

            case -203:
                return new NonUniqueFieldNameException($exception, $query);

            case -204:
                return new TableNotFoundException($exception, $query);

            case -206:
                return new InvalidFieldNameException($exception, $query);

            case -407:
                return new NotNullConstraintViolationException($exception, $query);

            case -530:
            case -531:
            case -532:
            case -20356:
                return new ForeignKeyConstraintViolationException($exception, $query);

            case -601:
                return new TableExistsException($exception, $query);

            case -803:
                return new UniqueConstraintViolationException($exception, $query);

            case -1336:
            case -30082:
                return new ConnectionException($exception, $query);
        }

        return new DriverException($exception, $query);
    }
}
