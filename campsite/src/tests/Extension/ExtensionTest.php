<?php

require_once dirname(__FILE__) . '/../bootstrap.php';

require_once WWW_DIR . '/classes/Extension/Extension.php';

class Extension_ExtensionTest extends PHPUnit_Framework_TestCase
{
    public $file;
    public $object;

    public function setUp()
    {
        $this->file = new Extension_File(dirname(__FILE__) . '/IndexTest.php');
        $this->object = new Extension_Extension('ITest', 'TestClass', $this->file);
    }

    public function testGetFile()
    {
        $this->assertEquals($this->file, $this->object->getFile());
    }

    public function testHasInterface()
    {
        $this->assertTrue($this->object->hasInterface('ITest'));
        $this->assertFalse($this->object->hasInterface('IAsdf'));
        $this->assertFalse($this->object->hasInterface(NULL));
    }

    public function testGetClass()
    {
        $this->assertEquals('TestClass', $this->object->getClass());
    }

    public function testGetPath()
    {
        $this->assertEquals($this->file->getPath(), $this->object->getPath());
    }

    public function testGetInstance()
    {
        $instance = $this->object->getInstance();
        $this->assertEquals('TestClass', get_class($instance));
        $this->assertEquals('toc', $instance->tic);
    }
}
