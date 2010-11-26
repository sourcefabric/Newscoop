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
require_once WWW_DIR . '/classes/Extension/Index.php';

class Extension_IndexTest extends PHPUnit_Framework_TestCase
{

    public function testAddDirectory()
    {
        $index = new Extension_Index;

        $this->assertEquals(array(), $index->getDirs());

        $this->assertEquals($index, $index->addDirectory(dirname(__FILE__)));
        $this->assertEquals($index, $index->addDirectory(dirname(__FILE__)));

        $this->assertEquals(array(dirname(__FILE__)), $index->getDirs());

        return $index;
    }

    /**
     * @depends testAddDirectory
     * @expectedException InvalidArgumentException
     */
    public function testAddDirException($index)
    {
        // throws exception
        $index->addDirectory(dirname(__FILE__) . '/asdf');
    }

    /**
     * @depends testAddDirectory
     */
    public function testGetFiles($index)
    {
        $this->assertEquals(array(), $index->getFiles('*.asdf'));
        $this->assertContains(__FILE__, $index->getFiles('*.php'));
    }

    /**
     * @depends testAddDirectory
     */
    public function testFind($index)
    {
        $this->assertEquals(array(), $index->find('IAsdf', '*.php'));

        $extensions = $index->find('ITest', '*.php');
        $this->assertEquals(2, sizeof($extensions));
    }
}

interface ITest
{
}

abstract class BaseTestClass implements ITest
{
}

class TestClass extends BaseTestClass
{
    public $tic = 'toc';
}

class AnotherTestClass implements ITest
{
}
