<?php

namespace Doctrine\DBAM\Driver;

use Doctrine\DBAM\Connection;
use Doctrine\DBAM\Driver;
use Doctrine\DBAM\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use Doctrine\DBAM\Driver\API\SQLSrv\ExceptionConverter;
use Doctrine\DBAM\Platforms\AbstractPlatform;
use Doctrine\DBAM\Platforms\SQLServer2012Platform;
use Doctrine\DBAM\Schema\SQLServerSchemaManager;

use function assert;

/**
 * Abstract base implementation of the {@see Driver} interface for Microsoft SQL Server based drivers.
 */
abstract class AbstractSQLServerDriver implements Driver
{
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new SQLServer2012Platform();
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        assert($platform instanceof SQLServer2012Platform);

        return new SQLServerSchemaManager($conn, $platform);
    }

    public function getExceptionConverter(): ExceptionConverterInterface
    {
        return new ExceptionConverter();
    }
}
