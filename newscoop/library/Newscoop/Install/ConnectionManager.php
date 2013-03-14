<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Install;

use Doctrine\DBAL\DriverManager;

/**
 * Connection Manager
 */
class ConnectionManager
{
    /**
     * Get database connection
     *
     * @param array $config
     * @param bool $force
     * @return Doctrine\DBAL\Connection
     */
    public static function getConnection(array $config, $force = false)
    {
        $tmpConnection = DriverManager::getConnection(array_merge($config, array('dbname' => null)));
        $sm = $tmpConnection->getSchemaManager();
        if (!in_array($config['dbname'], $sm->listDatabases())) {
            $sm->createDatabase($config['dbname']);
        }

        $connection = DriverManager::getConnection($config);
        $sm = $connection->getSchemaManager();
        if (count($sm->listTableNames()) && !$force) {
            throw new InstallException("Database with given name exists and is not empty");
        }

        return $connection;
    }
}
