<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Doctrine;

/**
 * AdoDb interface
 */
interface AdoDbInterface
{
    /**
     * Get all rows for a given query
     *
     * @param string $query
     * @return array
     */
    public function getAll($query);

    /**
     * Get one row for a given query
     *
     * @param string $query
     * @return mixed
     */
    public function getOne($query);

    /**
     * Escape string
     *
     * @param string $string
     * @return string
     */
    public function escape($string);

    /**
     * Execute sql statement
     *
     * @param string $sql
     * @param array $data
     * @return mixed
     */
    public function execute($sql, array $data = null);

    /**
     * Test if adapter is connected to db
     *
     * @return bool
     */
    public function isConnected();

    /**
     * Get single row
     *
     * @param string $query
     * @return array
     */
    public function getRow($query);

    /**
     * Get affected rows number
     *
     * @return int
     */
    public function Affected_Rows();

    /**
     * Get last generated id
     *
     * @return int
     */
    public function Insert_ID();

    /**
     * Get col
     *
     * @return array
     */
    public function getCol();
}

