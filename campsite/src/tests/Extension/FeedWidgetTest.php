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
require_once WWW_DIR . '/classes/Extension/FeedWidget.php';

class FeedWidgetTest extends PHPUnit_Framework_TestCase
{
    public $ok, $error;

    public function setUp()
    {
        $this->ok = new WidgetRendererDecorator(array(
            'id' => 'wok',
            'class' => 'OkFeedWidget',
            'path' => __FILE__,
        ));

        $this->error = new WidgetRendererDecorator(array(
            'id' => 'werror',
            'class' => 'ErrorFeedWidget',
            'path' => __FILE__,
        )); 
    }

    public function testRender()
    {
        ob_start();
        $this->ok->render();
        $content = ob_get_clean();

        $matches = array();
        $this->assertEquals($this->ok->getCount(), preg_match_all('#<li[^>]*><a#', $content, $matches));

        ob_start();
        $this->error->render();
        $content = ob_get_clean();

        $this->assertFalse((bool) preg_match('#<li[^>]*><a#', $content));
    }
}

class OkFeedWidget extends FeedWidget
{
    protected $url = 'http://www.sourcefabric.org/en/?tpl=259'; // pick source with > count feeds
    protected $count = 5;
}

class ErrorFeedWidget extends FeedWidget
{
    protected $url = 'http://www.example.com';
}
