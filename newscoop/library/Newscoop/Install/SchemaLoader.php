<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Install;

use Doctrine\DBAL\Connection;

/**
 * Schema Loader
 */
class SchemaLoader
{
    /**
     * @var Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var Doctrine\DBAL\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Load schema from file
     *
     * @param string $filepath
     */
    public function load($filepath)
    {
        $sql = file_get_contents($filepath);
        $this->connection->exec($sql);
    }
}
