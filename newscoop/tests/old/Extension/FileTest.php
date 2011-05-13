<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
 
require_once dirname(__FILE__) . '/AllTests.php';
require_once WWW_DIR . '/classes/Extension/File.php';

class Extension_FileTest extends PHPUnit_Framework_TestCase
{
    protected $path;
    protected $file;

    public function setUp()
    {
        $this->path = dirname(__FILE__) . '/IndexTest.php';
        $this->file = new Extension_File($this->path);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructException()
    {
        new Extension_File('asdf.ghj');
    }

    public function testGetPath()
    {
        $this->assertEquals($this->path, $this->file->getPath());
    }

    public function testGetChecksum()
    {
        $this->assertEquals(sha1_file($this->path),
            $this->file->getChecksum());

        $other = new Extension_File(__FILE__);
        $this->assertEquals(sha1_file(__FILE__), $other->getChecksum());
    }

    public function testFind()
    {
        $this->assertEquals(array(), $this->file->find('IAsdf'));

        $extensions = $this->file->find('ITest');
        $this->assertEquals(2, sizeof($extensions));
    }
}
