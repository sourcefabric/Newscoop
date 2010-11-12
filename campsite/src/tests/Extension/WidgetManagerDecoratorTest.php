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
require_once WWW_DIR . '/classes/Extension/WidgetRendererDecorator.php';

class WidgetManagerDecoratorTest extends PHPUnit_Framework_TestCase
{
    protected $object;

    public function setUp()
    {
        $this->object = $this->getInstance('TestWidget');
    }

    public function testGetAuthor()
    {
        $this->assertEquals('sourcefabric', $this->object->getAuthor());
    }

    public function testGetClass()
    {
        $this->assertEquals('TestWidget', $this->object->getClass());
    }

    public function testGetDescription()
    {
        $this->assertEquals('toc', $this->object->getDescription());

        $default = $this->getInstance('DefaultWidget');
        $this->assertEquals('tic', $default->getDescription());
    }

    public function testGetHomepage()
    {
        $this->assertEquals('http://www.sourcefabric.org',
            $this->object->getHomepage());
    }

    public function testGetId()
    {
        $this->assertEquals(0, $this->object->getId());
        //$this->assertGreaterThan(0, $this->object->getId(TRUE));
    }

    public function testGetPath()
    {
        $this->assertEquals(realpath(dirname(__FILE__) . '/AllTests.php'),
            $this->object->getPath());
    }

    public function testGetTitle()
    {
        $this->assertEquals('Test', $this->object->getTitle());

        $default = $this->getInstance('DefaultWidget');
        $this->assertEquals('', $default->getTitle());
    }

    public function testGetVersion()
    {
        $this->assertEquals('1.0', $this->object->getVersion());
    }

    public function getInstance($class)
    {
        $widget = new $class;
        return new WidgetRendererDecorator($widget);
    }
}

class DefaultWidget extends Widget
{
    public function render()
    {
        echo 'default';
    }
}
