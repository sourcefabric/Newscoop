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

class WidgetRendererDecoratorTest extends PHPUnit_Framework_TestCase
{
    protected $content;
    protected $object;

    public function setUp()
    {
        $this->content = sha1(uniqid());
        $widget = new TestWidget;
        $this->object = new WidgetRendererDecorator(array(
            'id' => 'w12345',
            'class' => 'TestWidget',
            'path' => dirname(__FILE__) . '/AllTests.php',
        ));
    }

    public function testRender()
    {
        $this->assertEquals($this->content,
            $this->object->render($this->content, TRUE));

        ob_start();
        $this->object->render($this->content);
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertRegExp("/{$this->content}/", $content);
    }

    public function testRenderMeta()
    {
        ob_start();
        $this->object->renderMeta();
        $meta = ob_get_clean();

        // info form Extension.ini
        $this->assertRegExp('/sourcefabric/', $meta);
        $this->assertRegExp('/1.0/', $meta);
        $this->assertRegExp('/www.sourcefabric.org/', $meta);
    }
}
