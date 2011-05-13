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
require_once WWW_DIR . '/classes/Extension/WidgetContext.php';

class WidgetContextTest extends PHPUnit_Framework_TestCase
{
    private $id;
    private $object;

    public function setUp()
    {
        global $g_user;
        $g_user = new User(1); 
        $this->id = sha1(uniqid());
        $this->object = new WidgetContext($this->id);
    }

    public function testGetId()
    {
        $this->assertEquals(0, $this->object->getId());
    }

    public function testGetName()
    {
        $this->assertEquals($this->id, $this->object->getName());
    }

    public function testGetWidgets()
    {
        $this->assertEquals(array(), $this->object->getWidgets());
    }

    public function testRender()
    {
        ob_start();
        $this->object->render();
        $content = ob_get_clean();

        $this->assertRegExp("/^<ul id=\"{$this->id}\"/", $content);
        $this->assertRegExp('#</ul>$#', $content);
    }
}
