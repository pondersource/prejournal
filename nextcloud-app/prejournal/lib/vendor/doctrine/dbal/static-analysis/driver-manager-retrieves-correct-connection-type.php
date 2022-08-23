<?php

declare(strict_types=1);

namespace Doctrine\StaticAnalysis\DBAM;

use Doctrine\DBAM\Connection;
use Doctrine\DBAM\DriverManager;

final class MyConnection extends Connection
{
}

function makeMeACustomConnection(): MyConnection
{
    return DriverManager::getConnection([
        'wrapperClass' => MyConnection::class,
    ]);
}
