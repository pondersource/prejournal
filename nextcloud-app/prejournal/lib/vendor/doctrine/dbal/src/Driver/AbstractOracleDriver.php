<?php

namespace Doctrine\DBAM\Driver;

use Doctrine\DBAM\Connection;
use Doctrine\DBAM\Driver;
use Doctrine\DBAM\Driver\AbstractOracleDriver\EasyConnectString;
use Doctrine\DBAM\Driver\API\ExceptionConverter;
use Doctrine\DBAM\Driver\API\OCI;
use Doctrine\DBAM\Platforms\AbstractPlatform;
use Doctrine\DBAM\Platforms\OraclePlatform;
use Doctrine\DBAM\Schema\OracleSchemaManager;

use function assert;

/**
 * Abstract base implementation of the {@see Driver} interface for Oracle based drivers.
 */
abstract class AbstractOracleDriver implements Driver
{
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new OraclePlatform();
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        assert($platform instanceof OraclePlatform);

        return new OracleSchemaManager($conn, $platform);
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        return new OCI\ExceptionConverter();
    }

    /**
     * Returns an appropriate Easy Connect String for the given parameters.
     *
     * @param mixed[] $params The connection parameters to return the Easy Connect String for.
     *
     * @return string
     */
    protected function getEasyConnectString(array $params)
    {
        return (string) EasyConnectString::fromConnectionParameters($params);
    }
}
