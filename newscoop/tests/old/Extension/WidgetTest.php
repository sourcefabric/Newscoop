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
require_once WWW_DIR . '/classes/Extension/Widget.php';

class WidgetTest extends PHPUnit_Framework_TestCase
{
    protected $view;
    protected $object;

    public function setUp()
    {
        $this->view = sha1(uniqid());
        $this->object = new TestWidget();
    }

    public function testSetGetView()
    {
        $widget = $this->object->SetView($this->view);
        $this->assertEquals($widget, $this->object);
        $this->assertEquals($this->view, $widget->getView());

        $widget->setView(NULL);
        $this->assertEquals('', $widget->getView()); 

        $num = mt_rand();
        $widget->setView($num);
        $this->assertEquals((string) $num, $widget->getView());
    }

    public function testIsFullscreen()
    {
        $this->assertFalse($this->object->isFullscreen());

        $this->object->setView($this->view);
        $this->assertFalse($this->object->isFullscreen());

        $this->object->setView(Widget::FULLSCREEN_VIEW);
        $this->assertTrue($this->object->isFullscreen());
    }
}
