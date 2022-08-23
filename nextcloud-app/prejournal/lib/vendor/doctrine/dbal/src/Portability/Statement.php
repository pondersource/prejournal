<?php

namespace Doctrine\DBAM\Portability;

use Doctrine\DBAM\Driver\Middleware\AbstractStatementMiddleware;
use Doctrine\DBAM\Driver\Result as ResultInterface;
use Doctrine\DBAM\Driver\Statement as DriverStatement;

/**
 * Portability wrapper for a Statement.
 */
final class Statement extends AbstractStatementMiddleware
{
    /** @var Converter */
    private $converter;

    /**
     * Wraps <tt>Statement</tt> and applies portability measures.
     */
    public function __construct(DriverStatement $stmt, Converter $converter)
    {
        parent::__construct($stmt);

        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($params = null): ResultInterface
    {
        return new Result(
            parent::execute($params),
            $this->converter
        );
    }
}
