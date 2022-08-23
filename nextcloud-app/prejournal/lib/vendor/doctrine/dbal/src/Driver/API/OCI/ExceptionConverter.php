<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\API\OCI;

use Doctrine\DBAM\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use Doctrine\DBAM\Driver\Exception;
use Doctrine\DBAM\Exception\ConnectionException;
use Doctrine\DBAM\Exception\DatabaseDoesNotExist;
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
 */
final class ExceptionConverter implements ExceptionConverterInterface
{
    /**
     * @link http://www.dba-oracle.com/t_error_code_list.htm
     */
    public function convert(Exception $exception, ?Query $query): DriverException
    {
        switch ($exception->getCode()) {
            case 1:
            case 2299:
            case 38911:
                return new UniqueConstraintViolationException($exception, $query);

            case 904:
                return new InvalidFieldNameException($exception, $query);

            case 918:
            case 960:
                return new NonUniqueFieldNameException($exception, $query);

            case 923:
                return new SyntaxErrorException($exception, $query);

            case 942:
                return new TableNotFoundException($exception, $query);

            case 955:
                return new TableExistsException($exception, $query);

            case 1017:
            case 12545:
                return new ConnectionException($exception, $query);

            case 1400:
                return new NotNullConstraintViolationException($exception, $query);

            case 1918:
                return new DatabaseDoesNotExist($exception, $query);

            case 2289:
            case 2443:
            case 4080:
                return new DatabaseObjectNotFoundException($exception, $query);

            case 2266:
            case 2291:
            case 2292:
                return new ForeignKeyConstraintViolationException($exception, $query);
        }

        return new DriverException($exception, $query);
    }
}
