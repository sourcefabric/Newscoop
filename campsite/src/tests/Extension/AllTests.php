<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/../bootstrap.php';

require_once dirname(__FILE__) . '/FileTest.php';
require_once dirname(__FILE__) . '/ExtensionTest.php';
require_once dirname(__FILE__) . '/IndexTest.php';
require_once dirname(__FILE__) . '/WidgetTest.php';
require_once dirname(__FILE__) . '/WidgetRendererDecoratorTest.php';
require_once dirname(__FILE__) . '/WidgetManagerDecoratorTest.php';
require_once dirname(__FILE__) . '/WidgetContextTest.php';
require_once dirname(__FILE__) . '/WidgetManagerTest.php';
require_once dirname(__FILE__) . '/FeedWidgetTest.php';

class Extension_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Extension');

        $suite->addTestSuite('Extension_FileTest');
        $suite->addTestSuite('Extension_ExtensionTest');
        $suite->addTestSuite('Extension_IndexTest');
        $suite->addTestSuite('WidgetTest');
        $suite->addTestSuite('WidgetRendererDecoratorTest');
        $suite->addTestSuite('WidgetManagerDecoratorTest');
        $suite->addTestSuite('WidgetContextTest');
        $suite->addTestSuite('WidgetManagerTest');
        $suite->addTestSuite('FeedWidgetTest');

        return $suite;
    }
}

/**
 * @title Test
 * @description toc
 */
class TestWidget extends Widget
{
    public function render()
    {
        echo $this->getView();
    }
}
