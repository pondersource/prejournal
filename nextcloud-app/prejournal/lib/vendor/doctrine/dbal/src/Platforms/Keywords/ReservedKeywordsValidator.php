<?php

namespace Doctrine\DBAM\Platforms\Keywords;

use Doctrine\DBAM\Schema\Column;
use Doctrine\DBAM\Schema\ForeignKeyConstraint;
use Doctrine\DBAM\Schema\Index;
use Doctrine\DBAM\Schema\Schema;
use Doctrine\DBAM\Schema\Sequence;
use Doctrine\DBAM\Schema\Table;
use Doctrine\DBAM\Schema\Visitor\Visitor;

use function count;
use function implode;
use function str_replace;

class ReservedKeywordsValidator implements Visitor
{
    /** @var KeywordList[] */
    private $keywordLists;

    /** @var string[] */
    private $violations = [];

    /**
     * @param KeywordList[] $keywordLists
     */
    public function __construct(array $keywordLists)
    {
        $this->keywordLists = $keywordLists;
    }

    /**
     * @return string[]
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @param string $word
     *
     * @return string[]
     */
    private function isReservedWord($word): array
    {
        if ($word[0] === '`') {
            $word = str_replace('`', '', $word);
        }

        $keywordLists = [];
        foreach ($this->keywordLists as $keywordList) {
            if (! $keywordList->isKeyword($word)) {
                continue;
            }

            $keywordLists[] = $keywordList->getName();
        }

        return $keywordLists;
    }

    /**
     * @param string   $asset
     * @param string[] $violatedPlatforms
     */
    private function addViolation($asset, $violatedPlatforms): void
    {
        if (count($violatedPlatforms) === 0) {
            return;
        }

        $this->violations[] = $asset . ' keyword violations: ' . implode(', ', $violatedPlatforms);
    }

    /**
     * {@inheritdoc}
     */
    public function acceptColumn(Table $table, Column $column)
    {
        $this->addViolation(
            'Table ' . $table->getName() . ' column ' . $column->getName(),
            $this->isReservedWord($column->getName())
        );
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
    public function acceptSchema(Schema $schema)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptSequence(Sequence $sequence)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptTable(Table $table)
    {
        $this->addViolation(
            'Table ' . $table->getName(),
            $this->isReservedWord($table->getName())
        );
    }
}
