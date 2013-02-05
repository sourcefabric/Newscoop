<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Smarty;

/**
 */
class FunctionAssetTest extends TestCase
{
    public function setUp()
    {
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir(__DIR__);
        $this->smarty->setCacheDir(sys_get_temp_dir());
        $this->smarty->setConfigDir(sys_get_temp_dir());
        $this->smarty->setCompileDir(sys_get_temp_dir());

        define('STATIC_DIR', sys_get_temp_dir() . '/static');
    }

    public function tearDown()
    {
    }

    public function testAssetFileWithTarget()
    {
        $baseUrl = $this->getMock('Zend_View_Helper_BaseUrl');
        $this->smarty->assign('view', $baseUrl);
        $baseUrl->expects($this->once())
            ->method('baseUrl')
            ->with('/static/test.css')
            ->will($this->returnValue('/public/static/test.css'));

        $url = $this->smarty->fetch('asset.test.tpl');
        $this->assertEquals('/public/static/test.css', trim($url));
        $this->assertFileExists(STATIC_DIR . '/' . 'test.css');
    }
}
