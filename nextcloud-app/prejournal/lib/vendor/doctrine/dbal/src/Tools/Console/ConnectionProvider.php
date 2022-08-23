<?php

namespace Doctrine\DBAM\Tools\Console;

use Doctrine\DBAM\Connection;

interface ConnectionProvider
{
    public function getDefaultConnection(): Connection;

    /**
     * @throws ConnectionNotFound in case a connection with the given name does not exist.
     */
    public function getConnection(string $name): Connection;
}
