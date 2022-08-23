<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\API;

use Doctrine\DBAM\Driver\Exception;
use Doctrine\DBAM\Exception\DriverException;
use Doctrine\DBAM\Query;

interface ExceptionConverter
{
    /**
     * Converts a given driver-level exception into a DBAM-level driver exception.
     *
     * Implementors should use the vendor-specific error code and SQLSTATE of the exception
     * and instantiate the most appropriate specialized {@see DriverException} subclass.
     *
     * @param Exception  $exception The driver exception to convert.
     * @param Query|null $query     The SQL query that triggered the exception, if any.
     *
     * @return DriverException An instance of {@see DriverException} or one of its subclasses.
     */
    public function convert(Exception $exception, ?Query $query): DriverException;
}
