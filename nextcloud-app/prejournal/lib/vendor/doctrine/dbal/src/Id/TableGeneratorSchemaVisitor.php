<?php

namespace Doctrine\DBAM\Id;

use Doctrine\DBAM\Schema\Column;
use Doctrine\DBAM\Schema\ForeignKeyConstraint;
use Doctrine\DBAM\Schema\Index;
use Doctrine\DBAM\Schema\Schema;
use Doctrine\DBAM\Schema\Sequence;
use Doctrine\DBAM\Schema\Table;
use Doctrine\DBAM\Schema\Visitor\Visitor;
use Doctrine\Deprecations\Deprecation;

/**
 * @deprecated
 */
class TableGeneratorSchemaVisitor implements Visitor
{
    /** @var string */
    private $generatorTableName;

    /**
     * @param string $generatorTableName
     */
    public function __construct($generatorTableName = 'sequences')
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https://github.com/doctrine/dbal/pull/4681',
            'The TableGeneratorSchemaVisitor class is is deprecated.',
        );

        $this->generatorTableName = $generatorTableName;
    }

    /**
     * {@inheritdoc}
     */
    public function acceptSchema(Schema $schema)
    {
        $table = $schema->createTable($this->generatorTableName);
        $table->addColumn('sequence_name', 'string');
        $table->addColumn('sequence_value', 'integer', ['default' => 1]);
        $table->addColumn('sequence_increment_by', 'integer', ['default' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function acceptTable(Table $table)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptColumn(Table $table, Column $column)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptIndex(Table $table, Index $index)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptSequence(Sequence $sequence)
    {
    }
}
