<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Driver\Mysqli;

use Doctrine\DBAM\Driver\Exception;
use mysqli;

interface Initializer
{
    /**
     * @throws Exception
     */
    public function initialize(mysqli $connection): void;
}
