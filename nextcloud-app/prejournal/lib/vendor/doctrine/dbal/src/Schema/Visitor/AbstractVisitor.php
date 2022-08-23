<?php

namespace Doctrine\DBAM\Schema\Visitor;

use Doctrine\DBAM\Schema\Column;
use Doctrine\DBAM\Schema\ForeignKeyConstraint;
use Doctrine\DBAM\Schema\Index;
use Doctrine\DBAM\Schema\Schema;
use Doctrine\DBAM\Schema\Sequence;
use Doctrine\DBAM\Schema\Table;

/**
 * Abstract Visitor with empty methods for easy extension.
 */
class AbstractVisitor implements Visitor, NamespaceVisitor
{
    public function acceptSchema(Schema $schema)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptNamespace($namespaceName)
    {
    }

    public function acceptTable(Table $table)
    {
    }

    public function acceptColumn(Table $table, Column $column)
    {
    }

    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
    {
    }

    public function acceptIndex(Table $table, Index $index)
    {
    }

    public function acceptSequence(Sequence $sequence)
    {
    }
}
