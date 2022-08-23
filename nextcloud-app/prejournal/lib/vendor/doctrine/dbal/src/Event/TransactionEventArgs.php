<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Event;

use Doctrine\Common\EventArgs;
use Doctrine\DBAM\Connection;

abstract class TransactionEventArgs extends EventArgs
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
