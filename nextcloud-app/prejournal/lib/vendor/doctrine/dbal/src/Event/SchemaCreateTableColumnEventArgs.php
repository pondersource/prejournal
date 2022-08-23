<?php

namespace Doctrine\DBAM\Event;

use Doctrine\DBAM\Platforms\AbstractPlatform;
use Doctrine\DBAM\Schema\Column;
use Doctrine\DBAM\Schema\Table;

use function array_merge;
use function func_get_args;
use function is_array;

/**
 * Event Arguments used when SQL queries for creating table columns are generated inside {@see AbstractPlatform}.
 */
class SchemaCreateTableColumnEventArgs extends SchemaEventArgs
{
    /** @var Column */
    private $column;

    /** @var Table */
    private $table;

    /** @var AbstractPlatform */
    private $platform;

    /** @var string[] */
    private $sql = [];

    public function __construct(Column $column, Table $table, AbstractPlatform $platform)
    {
        $this->column   = $column;
        $this->table    = $table;
        $this->platform = $platform;
    }

    /**
     * @return Column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return AbstractPlatform
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Passing multiple SQL statements as an array is deprecated. Pass each statement as an individual argument instead.
     *
     * @param string|string[] $sql
     *
     * @return SchemaCreateTableColumnEventArgs
     */
    public function addSql($sql)
    {
        $this->sql = array_merge($this->sql, is_array($sql) ? $sql : func_get_args());

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSql()
    {
        return $this->sql;
    }
}
