<?php

namespace Doctrine\DBAM\Schema\Visitor;

use Doctrine\DBAM\Schema\Column;
use Doctrine\DBAM\Schema\ForeignKeyConstraint;
use Doctrine\DBAM\Schema\Index;
use Doctrine\DBAM\Schema\Schema;
use Doctrine\DBAM\Schema\SchemaException;
use Doctrine\DBAM\Schema\Sequence;
use Doctrine\DBAM\Schema\Table;

/**
 * Schema Visitor used for Validation or Generation purposes.
 */
interface Visitor
{
    /**
     * @return void
     *
     * @throws SchemaException
     */
    public function acceptSchema(Schema $schema);

    /**
     * @return void
     */
    public function acceptTable(Table $table);

    /**
     * @return void
     */
    public function acceptColumn(Table $table, Column $column);

    /**
     * @return void
     *
     * @throws SchemaException
     */
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint);

    /**
     * @return void
     */
    public function acceptIndex(Table $table, Index $index);

    /**
     * @return void
     */
    public function acceptSequence(Sequence $sequence);
}
