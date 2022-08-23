<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver;

use Doctrine\DBAM\Driver;

interface Middleware
{
    public function wrap(Driver $driver): Driver;
}
