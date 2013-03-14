<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Install;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

/**
 */
class ConnectionManagerTest extends \TestCase
{
    public function setUp()
    {
        $this->db = array(
            'driver' => 'pdo_mysql',
            'dbname' => 'phpunit_' . sha1(__FILE__),
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'root',
            'password' => '',
        );
    }

    public function tearDown()
    {
        try {
            $dbal = DriverManager::getConnection($this->db);
            $sm = $dbal->getSchemaManager();
            $sm->dropDatabase($this->db['dbname']);
        } catch (\PDOException $e) {
            // ignore missing db
        }
    }

    public function testGetConnection()
    {
        $conn = ConnectionManager::getConnection($this->db);
        $this->assertInstanceOf('Doctrine\DBAL\Connection', $conn);
        $this->assertTrue($conn->isConnected());
    }

    public function testGetConnectionWithExistingDb()
    {
        ConnectionManager::getConnection($this->db);
        $conn = ConnectionManager::getConnection($this->db);
        $this->assertTrue($conn->isConnected());
    }

    /**
     * @expectedException Newscoop\Exception
     */
    public function testGetConnectionWithExistingDbWithTables()
    {
        $col = new Column('id', Type::getType('integer'));
        $table = new Table('test', array($col));
        ConnectionManager::getConnection($this->db)->getSchemaManager()->createTable($table);
        ConnectionManager::getConnection($this->db);
    }

    public function testGetConnectionWithExistingDbWithTablesForce()
    {
        $col = new Column('id', Type::getType('integer'));
        $table = new Table('test', array($col));
        ConnectionManager::getConnection($this->db)->getSchemaManager()->createTable($table);
        $connection = ConnectionManager::getConnection($this->db, true);
        $this->assertTrue($connection->isConnected());
    }
}
