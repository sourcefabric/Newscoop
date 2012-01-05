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
     * @return void
     */
    public function execute($statement)
    {
        $statement = str_replace(array(
            'INSERT IGNORE',
            'NOW()',
        ), array(
            'INSERT',
            "datetime('now')",
        ), $statement);


        if (preg_match('/^\w*UPDATE /', $statement)) {
            $statement = preg_replace('/LIMIT [0-9]+/', '', $statement);
        }

        try {
            $this->affectedRows = $this->connection->exec($statement);
        } catch (\Exception $e) {
            var_dump($statement, $e->getMessage());
            exit;
        }
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
     * Get one row
     *
     * @param string $statement
     * @return array
     */
    public function GetOne($statement)
    {
        return $this->connection->fetchColumn($statement);
    }
}
