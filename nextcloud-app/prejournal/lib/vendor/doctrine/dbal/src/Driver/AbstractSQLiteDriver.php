<?php

namespace Doctrine\DBAM\Driver;

use Doctrine\DBAM\Connection;
use Doctrine\DBAM\Driver;
use Doctrine\DBAM\Driver\API\ExceptionConverter;
use Doctrine\DBAM\Driver\API\SQLite;
use Doctrine\DBAM\Platforms\AbstractPlatform;
use Doctrine\DBAM\Platforms\SqlitePlatform;
use Doctrine\DBAM\Schema\SqliteSchemaManager;

use function assert;

/**
 * Abstract base implementation of the {@see Doctrine\DBAM\Driver} interface for SQLite based drivers.
 */
abstract class AbstractSQLiteDriver implements Driver
{
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new SqlitePlatform();
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        assert($platform instanceof SqlitePlatform);

        return new SqliteSchemaManager($conn, $platform);
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        return new SQLite\ExceptionConverter();
    }
}
