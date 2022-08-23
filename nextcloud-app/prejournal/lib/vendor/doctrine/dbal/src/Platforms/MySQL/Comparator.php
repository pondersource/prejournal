<?php

namespace Doctrine\DBAM\Platforms\MySQL;

use Doctrine\DBAM\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAM\Schema\Comparator as BaseComparator;
use Doctrine\DBAM\Schema\Table;

use function array_diff_assoc;
use function array_intersect_key;

/**
 * Compares schemas in the context of MySQL platform.
 *
 * In MySQL, unless specified explicitly, the column's character set and collation are inherited from its containing
 * table. So during comparison, an omitted value and the value that matches the default value of table in the
 * desired schema must be considered equal.
 */
class Comparator extends BaseComparator
{
    /**
     * @internal The comparator can be only instantiated by a schema manager.
     */
    public function __construct(AbstractMySQLPlatform $platform)
    {
        parent::__construct($platform);
    }

    /**
     * {@inheritDoc}
     */
    public function diffTable(Table $fromTable, Table $toTable)
    {
        return parent::diffTable(
            $this->normalizeColumns($fromTable),
            $this->normalizeColumns($toTable)
        );
    }

    private function normalizeColumns(Table $table): Table
    {
        $defaults = array_intersect_key($table->getOptions(), [
            'charset'   => null,
            'collation' => null,
        ]);

        if ($defaults === []) {
            return $table;
        }

        $table = clone $table;

        foreach ($table->getColumns() as $column) {
            $options = $column->getPlatformOptions();
            $diff    = array_diff_assoc($options, $defaults);

            if ($diff === $options) {
                continue;
            }

            $column->setPlatformOptions($diff);
        }

        return $table;
    }
}
