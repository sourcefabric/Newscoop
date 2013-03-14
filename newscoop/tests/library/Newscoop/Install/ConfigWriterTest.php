<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Install;

/**
 */
class ConfigWriterTest extends \TestCase
{
    protected $writer;

    public function setUp()
    {
        $this->dest = tempnam('/tmp', 'config');
        $this->writer = new ConfigWriter();

        global $Campsite;
        $this->campsite = $Campsite;
        $Campsite = array();
    }

    public function tearDown()
    {
        if (file_exists($this->dest)) {
            unlink($this->dest);
        }

        global $Campsite;
        $Campsite = $this->campsite;
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\\Install\\ConfigWriter', $this->writer);
    }

    public function testWrite()
    {
        $config = new InstallConfig();
        $config->db = array(
            'dbname' => uniqid('dbname'),
            'host' => uniqid('host'),
            'port' => mt_rand(100, 1000),
            'user' => uniqid('user'),
            'password' => uniqid('password'),
        );

        $this->writer->write($config, $this->dest);
        $this->assertFileExists($this->dest);

        global $Campsite;
        include $this->dest;

        $this->assertEquals($config->db['dbname'], $Campsite['DATABASE_NAME']);
        $this->assertEquals($config->db['host'], $Campsite['DATABASE_SERVER_ADDRESS']);
        $this->assertEquals($config->db['port'], $Campsite['DATABASE_SERVER_PORT']);
        $this->assertEquals($config->db['user'], $Campsite['DATABASE_USER']);
        $this->assertEquals($config->db['password'], $Campsite['DATABASE_PASSWORD']);
    }

    public function testWriteEscape()
    {
        $strings = array(
            'dbname',
            'host',
            'user',
            'password',
        );

        $config = new InstallConfig();
        $config->db['dbname'] = $dbname = "test'dbname";
        $config->db['host'] = $dbname;
        $config->db['user'] = $dbname;
        $config->db['password'] = $dbname;

        $this->writer->write($config, $this->dest);

        global $Campsite;
        include $this->dest;

        $this->assertEquals($dbname, $Campsite['DATABASE_NAME']);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testWriteNotWritable()
    {
        $config = new InstallConfig();
        $this->writer->write($config, '/dev/config');
    }
}
