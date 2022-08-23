<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\API\SQLSrv;

use Doctrine\DBAM\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use Doctrine\DBAM\Driver\Exception;
use Doctrine\DBAM\Exception\ConnectionException;
use Doctrine\DBAM\Exception\DatabaseObjectNotFoundException;
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
 * @link https://docs.microsoft.com/en-us/sql/relational-databases/errors-events/database-engine-events-and-errors
 */
final class ExceptionConverter implements ExceptionConverterInterface
{
    public function convert(Exception $exception, ?Query $query): DriverException
    {
        switch ($exception->getCode()) {
            case 102:
                return new SyntaxErrorException($exception, $query);

            case 207:
                return new InvalidFieldNameException($exception, $query);

            case 208:
                return new TableNotFoundException($exception, $query);

            case 209:
                return new NonUniqueFieldNameException($exception, $query);

            case 515:
                return new NotNullConstraintViolationException($exception, $query);

            case 547:
            case 4712:
                return new ForeignKeyConstraintViolationException($exception, $query);

            case 2601:
            case 2627:
                return new UniqueConstraintViolationException($exception, $query);

            case 2714:
                return new TableExistsException($exception, $query);

            case 3701:
            case 15151:
                return new DatabaseObjectNotFoundException($exception, $query);

            case 11001:
            case 18456:
                return new ConnectionException($exception, $query);
        }

        return new DriverException($exception, $query);
    }
}
