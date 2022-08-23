<?php

namespace Doctrine\DBAM\Event;

use Doctrine\Common\EventArgs;
use Doctrine\DBAM\Connection;

/**
 * Event Arguments used when a Driver connection is established inside Doctrine\DBAM\Connection.
 */
class ConnectionEventArgs extends EventArgs
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
