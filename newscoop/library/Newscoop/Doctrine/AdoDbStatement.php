<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Doctrine;

use Doctrine\DBAL\Driver\Statement;

/**
 * AdoDb Statement
 */
class AdoDbStatement
{
    /**
     * @var Doctrine\DBAL\Statement
     */
    protected $statement;

    /**
     * @param Doctrine\DBAL\Statement $statement
     */
    public function __construct(Statement $statement)
    {
        $this->statement = $statement;
    }

    /**
     * Fetch row
     *
     * @return array
     */
    public function fetchRow()
    {
        return $this->statement->fetch();
    }

    /**
     * Get record count
     *
     * @return int
     */
    public function recordCount()
    {
        return $this->rowCount();
    }

    /**
     * Get row count
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }
}
