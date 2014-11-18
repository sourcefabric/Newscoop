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
    protected $connection;

    /**
     * @var Newscoop\Doctrine\AdoDbStatement
     */
    protected $statement;

    /**
     * @var int
     */
    protected $affectedRows = 0;

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
     * @return AdoDbStatement
     */
    public function execute($sql, $params = array())
    {
        $this->affectedRows = 0;
        return $this->statement = new AdoDbStatement($this->connection->executeQuery($sql, $params));
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
     * Escape comparison operation
     *
     * @param array $operation
     * @return string
     */
    public function escapeOperation(array $operation)
    {
        return sprintf(
            '%s %s %s',
            $operation['left'],
            $operation['symbol'],
            $this->escape($operation['right'])
        );
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
            print_r($e->getMessage());
            print_r($e->getTraceAsString());
            exit;
        }
    }

    /**
     * Execute sql query and returns the value of first column of the first row.
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
     * Execute sql query and returns all elements of the first column.
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function getCol($sql, array $params = array())
    {
        $return = array();
        $rows = $this->connection->fetchAll($sql, $params);
        foreach ($rows as $row) {
            $return[] = reset($row);
        }

        return $return;
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

    /**
     * Fetch extended error information associated with the last database operation.
     *
     * @return string
     */
    public function errorMsg()
    {
        return json_encode($this->connection->errorInfo());
    }

    /**
     * Test if there is a database with given name
     *
     * @param string $database
     * @return bool
     */
    public function hasDatabase($database)
    {
        return in_array($database, $this->connection->getSchemaManager()->listDatabases());
    }

    /**
     * Test if there is a table with given name
     *
     * @param string $table
     * @return bool
     */
    public function hasTable($table)
    {
        return $this->connection->getSchemaManager()->tablesExist($table);
    }

    /**
     * Create a new database
     *
     * @param string $database
     * @return void
     */
    public function createDatabase($database)
    {
        $this->connection->getSchemaManager()->createDatabase($database);
    }

    /**
     * Get driver name
     *
     * @return string
     */
    public function getDriverName()
    {
        return (string) $this->connection->getDriver()->getName();
    }

    /**
     * Performs select with given limit and offset params
     *
     * @param string $sql
     * @param int $limit
     * @param int $offset
     * @return AdoDbStatement
     */
    public function selectLimit($sql, $limit = -1, $offset = -1)
    {
        if ($limit > -1) {
            $sql .= sprintf(' LIMIT %d', $limit);
        }

        if ($offset > -1) {
            $sql .= sprintf(' OFFSET %d',  $offset);
        }

        return $this->execute($sql);
    }
}
