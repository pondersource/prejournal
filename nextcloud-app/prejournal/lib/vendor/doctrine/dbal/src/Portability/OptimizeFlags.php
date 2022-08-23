<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Portability;

use Doctrine\DBAM\Platforms\AbstractPlatform;
use Doctrine\DBAM\Platforms\DB2Platform;
use Doctrine\DBAM\Platforms\OraclePlatform;
use Doctrine\DBAM\Platforms\PostgreSQLPlatform;
use Doctrine\DBAM\Platforms\SqlitePlatform;
use Doctrine\DBAM\Platforms\SQLServerPlatform;

final class OptimizeFlags
{
    /**
     * Platform-specific portability flags that need to be excluded from the user-provided mode
     * since the platform already operates in this mode to avoid unnecessary conversion overhead.
     *
     * @var array<string,int>
     */
    private static $platforms = [
        DB2Platform::class        => 0,
        OraclePlatform::class     => Connection::PORTABILITY_EMPTY_TO_NULL,
        PostgreSQLPlatform::class => 0,
        SqlitePlatform::class     => 0,
        SQLServerPlatform::class  => 0,
    ];

    public function __invoke(AbstractPlatform $platform, int $flags): int
    {
        foreach (self::$platforms as $class => $mask) {
            if ($platform instanceof $class) {
                $flags &= ~$mask;

                break;
            }
        }

        return $flags;
    }
}
