<?php

namespace Doctrine\DBAM\Driver;

use Doctrine\DBAM\Connection;
use Doctrine\DBAM\Driver;
use Doctrine\DBAM\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use Doctrine\DBAM\Driver\API\IBMDB2\ExceptionConverter;
use Doctrine\DBAM\Platforms\AbstractPlatform;
use Doctrine\DBAM\Platforms\DB2Platform;
use Doctrine\DBAM\Schema\DB2SchemaManager;

use function assert;

/**
 * Abstract base implementation of the {@see Driver} interface for IBM DB2 based drivers.
 */
abstract class AbstractDB2Driver implements Driver
{
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new DB2Platform();
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        assert($platform instanceof DB2Platform);

        return new DB2SchemaManager($conn, $platform);
    }

    public function getExceptionConverter(): ExceptionConverterInterface
    {
        return new ExceptionConverter();
    }
}
