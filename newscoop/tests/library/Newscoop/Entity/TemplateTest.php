<?php

namespace Newscoop\Entity;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    private $key;
    private $template;

    public function setUp()
    {
        $this->key = basename(__FILE__);
        $this->template = new Template($this->key);
        $this->template->setFileInfo(new \SplFileInfo(__FILE__));
    }

    public function testTemplate()
    {
        $template = new Template("test");
        $this->assertType('Newscoop\Entity\Template', $template);
    }

    public function testToString()
    {
        $this->assertEquals(basename(__FILE__), (string) $this->template);
    }

    public function testGetId()
    {
        $this->assertType('int', $this->template->getId());
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

    public function testGetSize()
    {
        $this->assertEquals(filesize(__FILE__), $this->template->getSize());
    }

    public function testGetChangeTime()
    {
        $this->assertType('DateTime', $this->template->getChangeTime());
        $this->assertEquals(filectime(__FILE__), $this->template->getChangeTime()->getTimestamp());
    }

    public function testType()
    {
        $template = new Template('test.jpg');
        $this->assertEquals('jpg', $template->getType());

        $template = new Template('t/test.png');
        $this->assertEquals('png', $template->getType());

        $template = new Template('test');
        $this->assertEquals('file', $template->getType());
    }
}
