<?php

namespace Doctrine\DBAM\Driver\OCI8;

use Doctrine\DBAM\Driver\AbstractOracleDriver;
use Doctrine\DBAM\Driver\OCI8\Exception\ConnectionFailed;

use function oci_connect;
use function oci_pconnect;

use const OCI_NO_AUTO_COMMIT;

/**
 * A Doctrine DBAM driver for the Oracle OCI8 PHP extensions.
 */
final class Driver extends AbstractOracleDriver
{
    /**
     * {@inheritdoc}
     *
     * @return Connection
     */
    public function connect(array $params)
    {
        $username    = $params['user'] ?? '';
        $password    = $params['password'] ?? '';
        $charset     = $params['charset'] ?? '';
        $sessionMode = $params['sessionMode'] ?? OCI_NO_AUTO_COMMIT;

        $connectionString = $this->getEasyConnectString($params);

        if (! empty($params['persistent'])) {
            $connection = @oci_pconnect($username, $password, $connectionString, $charset, $sessionMode);
        } else {
            $connection = @oci_connect($username, $password, $connectionString, $charset, $sessionMode);
        }

        if ($connection === false) {
            throw ConnectionFailed::new();
        }

        return new Connection($connection);
    }
}
