<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Adapter simulating AdoDB using doctrine
 */
class AdoDbDoctrineAdapter
{
    public $databaseType = 'sqlite';

    protected $orm;

    protected $connection;

    protected $affectedRows = 0;

    /**
     * @param Doctrine\ORM\EntityManager $orm
     */
    public function __construct(\Doctrine\ORM\EntityManager $orm)
    {
        $this->orm = $orm;
        $this->connection = $orm->getConnection();
    }

    /**
     * Escape string
     *
     * @param string $unescaped_string
     * @return string
     */
    public function escape($unescaped_string)
    {
        return sqlite_escape_string($unescaped_string);
    }

    /**
     * Execute query
     *
     * @param string $statement
     * @return bool
     */
    public function execute($statement)
    {
        $ignore = strpos($statement, 'IGNORE') !== false;

        $statement = str_replace(array(
            'INSERT IGNORE',
            'NOW()',
            'LAST_INSERT_ID(ArticleId + 1)',
            'DEFAULT CHARSET=utf8',
        ), array(
            'INSERT',
            "datetime('now')",
            '(SELECT MAX(ArticleId) + 1 FROM AutoId)',
            '',
        ), $statement);


        if (preg_match('/^\w*UPDATE /', $statement)) {
            $statement = preg_replace('/LIMIT [0-9]+/', '', $statement);
        }

        try {
            $this->affectedRows = $this->connection->exec($statement);
        } catch (\Exception $e) {
            if (!$ignore) {
                var_dump($statement, $e->getMessage(), array_slice($e->getTrace(), 0, 5));
                exit;
            }

            return $ignore;
        }

        return true;
    }

    /**
     * Get affected rows
     *
     * @return int
     */
    public function Affected_Rows()
    {
        return $this->affectedRows;
    }

    /**
     * Get last insert id
     *
     * @return int
     */
    public function Insert_ID()
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Get single value
     *
     * @param string $statement
     * @return mixed
     */
    public function GetOne($statement)
    {
        return $this->connection->fetchColumn($statement);
    }

    /**
     * Get all 
     *
     * @param string $statement
     * @return array
     */
    public function GetAll($statement)
    {
        return $this->connection->fetchAll($statement);
    }

    /**
     * Get row
     *
     * @param string $statement
     * @return array
     */
    public function GetRow($statement)
    {
        return $this->connection->fetchAssoc($statement);
    }
}
