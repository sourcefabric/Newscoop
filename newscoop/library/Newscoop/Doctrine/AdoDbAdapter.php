<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Doctrine;

use InvalidArgumentException;
use Doctrine\DBAL\Connection;

/**
 * AdoDb Adapter
 */
class AdoDbAdapter
{
    /**
     * @var Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var Newscoop\Doctrine\AdoDbStatement
     */
    private $statement;

    /**
     * @var int
     */
    private $affectedRows = 0;

    /**
     * @param Doctrine\DBAL\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Test if is connected
     *
     * @param bool $forceConnection
     * @return bool
     */
    public function isConnected($forceConnection = false)
    {
        if ($forceConnection) {
            $this->connection->connect();
        }

        return $this->connection->isConnected();
    }

    /**
     * Execute sql statement
     *
     * @param string $sql
     * @return null
     */
    public function execute($sql)
    {
        $this->affectedRows = 0;
        return $this->statement = new AdoDbStatement($this->connection->executeQuery($sql));
    }

    /**
     * Execute INSERT/UPDATE/DELETE statement and return number of affected rows
     *
     * @param string $sql
     * @return int
     */
    public function executeUpdate($sql)
    {
        return $this->affectedRows = $this->connection->executeUpdate($sql);
    }

    /**
     * Escape identifier
     *
     * @param string $identifier
     * @return string
     */
    public function escapeIdentifier($identifier)
    {
        return $this->connection->quoteIdentifier($identifier);
    }

    /**
     * Escape value
     *
     * @param mixed $value
     * @return string
     */
    public function escape($value)
    {
        return $this->connection->quote($value);
    }

    /**
     * Escape column equals value statement
     *
     * @param string $key
     * @param string $val
     * @return string
     */
    public function escapeKeyVal($key, $val)
    {
        if (empty($key)) {
            throw new InvalidArgumentException("'key' param invalid.");
        }

        return sprintf('%s = %s', $this->escapeIdentifier($key), $this->escape($val));
    }

    /**
     * Execute sql query and return first row of the result as an associative array.
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function getRow($sql, array $params = array())
    {
        return $this->connection->fetchAssoc($sql);
    }

    /**
     * Execute sql query and return the result as an array
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function getAll($sql, array $params = array())
    {
        try {
            return $this->connection->fetchAll($sql, $params);
        } catch (\Exception $e) {
            print_r($sql);
            print_r($e->getTraceAsString());
            exit;
        }
    }

    /**
     * Execute sql query and returns the value of first column
     * of the first row.
     *
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    public function getOne($sql, array $params = array())
    {
        return $this->connection->fetchColumn($sql, $params);
    }

    /**
     * Get affected rows count
     *
     * @return int
     */
    public function affected_rows()
    {
        return $this->affectedRows;
    }

    /**
     * Get last insert id
     *
     * @return int
     */
    public function insert_id()
    {
        return $this->connection->lastInsertId();
    }
}
