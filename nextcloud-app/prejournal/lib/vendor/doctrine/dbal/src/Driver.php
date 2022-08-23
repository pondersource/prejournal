<?php

namespace Doctrine\DBAM;

use Doctrine\DBAM\Driver\API\ExceptionConverter;
use Doctrine\DBAM\Driver\Connection as DriverConnection;
use Doctrine\DBAM\Driver\Exception;
use Doctrine\DBAM\Platforms\AbstractPlatform;
use Doctrine\DBAM\Schema\AbstractSchemaManager;

/**
 * Driver interface.
 * Interface that all DBAM drivers must implement.
 */
interface Driver
{
    /**
     * Attempts to create a connection with the database.
     *
     * @param mixed[] $params All connection parameters.
     *
     * @return DriverConnection The database connection.
     *
     * @throws Exception
     */
    public function connect(array $params);

    /**
     * Gets the DatabasePlatform instance that provides all the metadata about
     * the platform this driver connects to.
     *
     * @return AbstractPlatform The database platform.
     */
    public function getDatabasePlatform();

    /**
     * Gets the SchemaManager that can be used to inspect and change the underlying
     * database schema of the platform this driver connects to.
     *
     * @return AbstractSchemaManager
     */
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform);

    /**
     * Gets the ExceptionConverter that can be used to convert driver-level exceptions into DBAM exceptions.
     */
    public function getExceptionConverter(): ExceptionConverter;
}
