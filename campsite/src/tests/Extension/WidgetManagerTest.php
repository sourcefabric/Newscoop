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
require_once WWW_DIR . '/classes/Extension/WidgetManager.php';

class WidgetManagerTest extends PHPUnit_Framework_TestCase
{
    const USER_ID = 123456789; // something not used

    public function setUp()
    {
        global $g_user;
        $g_user = new User(self::USER_ID);
    }

    public function testGetAvailable()
    {
        $widgets = WidgetManager::GetAvailable();
        $this->assertTrue(is_array($widgets));
        $this->assertGreaterThan(0, sizeof($widgets));
        foreach ($widgets as $widget) {
            $this->assertContains('IWidget', class_implements($widget));
        }
    }
}
