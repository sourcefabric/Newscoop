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
    protected $id;
    protected $object;
    protected $default;
    protected $extension;

    public function setUp()
    {
        $this->id = 'w' . substr(sha1(uniqid()), -12);
        $this->object = new WidgetManagerDecorator(array(
            'id' => $this->id,
            'class' => 'TestWidget',
            'path' => dirname(__FILE__) . '/AllTests.php',
        ));

        $this->default = new WidgetManagerDecorator(array(
            'id' => 'wdefault',
            'class' => 'DefaultWidget',
            'path' => __FILE__,
        ));
    }

    public function testGetAuthor()
    {
        $this->assertEquals('sourcefabric', $this->object->getAuthor());
    }

    public function testGetExtension()
    {
        $this->assertTrue(is_a($this->object->getExtension(), 'Extension_Extension'));
    }

    public function testGetDescription()
    {
        $this->assertEquals('toc', $this->object->getDescription());
        $this->assertEquals('tic', $this->default->getDescription());
    }

    public function testGetHomepage()
    {
        $this->assertEquals('http://www.sourcefabric.org',
            $this->object->getHomepage());
    }

    public function testGetId()
    {
        $this->assertEquals($this->id, $this->object->getId());
    }

    public function testGetTitle()
    {
        $this->assertEquals('Test', $this->object->getTitle());
        $this->assertEquals('', $this->default->getTitle());
    }

    public function testGetVersion()
    {
        $this->assertEquals('1.0', $this->object->getVersion());
    }
}

class DefaultWidget extends Widget
{
    public function render()
    {
        echo 'default';
    }
}
