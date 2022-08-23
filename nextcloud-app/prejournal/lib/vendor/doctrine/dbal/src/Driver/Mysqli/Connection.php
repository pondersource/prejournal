<?php

namespace Doctrine\DBAM\Driver\Mysqli;

use Doctrine\DBAM\Driver\Mysqli\Exception\ConnectionError;
use Doctrine\DBAM\Driver\Result as ResultInterface;
use Doctrine\DBAM\Driver\ServerInfoAwareConnection;
use Doctrine\DBAM\Driver\Statement as DriverStatement;
use Doctrine\DBAM\ParameterType;
use Doctrine\Deprecations\Deprecation;
use mysqli;
use mysqli_sql_exception;

final class Connection implements ServerInfoAwareConnection
{
    /**
     * Name of the option to set connection flags
     */
    public const OPTION_FLAGS = 'flags';

    /** @var mysqli */
    private $connection;

    /**
     * @internal The connection can be only instantiated by its driver.
     */
    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Retrieves mysqli native resource handle.
     *
     * Could be used if part of your application is not using DBAM.
     *
     * @deprecated Call {@see getNativeConnection()} instead.
     */
    public function getWrappedResourceHandle(): mysqli
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https://github.com/doctrine/dbal/pull/5037',
            '%s is deprecated, call getNativeConnection() instead.',
            __METHOD__
        );

        return $this->getNativeConnection();
    }

    public function getServerVersion(): string
    {
        return $this->connection->get_server_info();
    }

    public function prepare(string $sql): DriverStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
        } catch (mysqli_sql_exception $e) {
            throw ConnectionError::upcast($e);
        }

        if ($stmt === false) {
            throw ConnectionError::new($this->connection);
        }

        return new Statement($stmt);
    }

    public function query(string $sql): ResultInterface
    {
        return $this->prepare($sql)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function quote($value, $type = ParameterType::STRING)
    {
        return "'" . $this->connection->escape_string($value) . "'";
    }

    public function exec(string $sql): int
    {
        try {
            $result = $this->connection->query($sql);
        } catch (mysqli_sql_exception $e) {
            throw ConnectionError::upcast($e);
        }

        if ($result === false) {
            throw ConnectionError::new($this->connection);
        }

        return $this->connection->affected_rows;
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId($name = null)
    {
        if ($name !== null) {
            Deprecation::triggerIfCalledFromOutside(
                'doctrine/dbal',
                'https://github.com/doctrine/dbal/issues/4687',
                'The usage of Connection::lastInsertId() with a sequence name is deprecated.'
            );
        }

        return $this->connection->insert_id;
    }

    public function beginTransaction(): bool
    {
        $this->connection->begin_transaction();

        return true;
    }

    public function commit(): bool
    {
        try {
            return $this->connection->commit();
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    public function rollBack(): bool
    {
        try {
            return $this->connection->rollback();
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    public function getNativeConnection(): mysqli
    {
        return $this->connection;
    }
}
