<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{
    private $key;
    private $template;

    public function setUp()
    {
        $this->key = basename(__FILE__);
        $this->template = new Template($this->key);
    }

    public function testTemplate()
    {
        $template = new Template("test");
        $this->assertType('Newscoop\Entity\Template', $template);
    }

    public function testGetId()
    {
        $this->assertInternalType('int', $this->template->getId());
    }

    public function testSetKey()
    {
        $key = uniqid();
        $this->template->setKey($key);
        $this->assertEquals($key, $this->template->getKey());
    }

    public function testGetKey()
    {
        $this->assertEquals($this->key, $this->template->getKey());
    }

    public function testGetCacheLifetime()
    {
        $this->assertEquals(0, $this->template->getCacheLifetime());
    }

    public function testSetCacheLifetime()
    {
        $lifetime = mt_rand();
        $this->template->setCacheLifetime($lifetime);
        $this->assertEquals($lifetime, $this->template->getCacheLifetime());
    }
}
