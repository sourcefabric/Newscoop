<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Storage;

/**
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    protected $storage;

    public function setUp()
    {
        $this->storage = $this->getMock('Newscoop\Storage', null, array(''));
    }

    public function testItem()
    {
        $item = new Item('key', $this->storage);
        $this->assertInstanceOf('Newscoop\Storage\Item', $item);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testItemException()
    {
        $item = new Item('', $this->storage);
    }

    public function testGetKey()
    {
        $item = new Item('key', $this->storage);
        $this->assertEquals('key', $item->getKey());
    }

    public function testGetName()
    {
        $item = new Item('key', $this->storage);
        $this->assertEquals('key', $item->getName());

        $item = new Item('collection/item', $this->storage);
        $this->assertEquals('item', $item->getName());
    }

    public function testToString()
    {
        $item = new Item('collection/item', $this->storage);
        $this->assertEquals('item', (string) $item);
    }

    public function testIsDir()
    {
        $storage = $this->getMock('Newscoop\Storage', array('isDir'), array(''));
        $storage->expects($this->once())
            ->method('isDir')
            ->with($this->equalTo('item'))
            ->will($this->returnValue(TRUE));
        $item = new Item('item', $storage);
        $this->assertTrue($item->isDir());
    }

    public function testGetSize()
    {
        $storage = $this->getMock('Newscoop\Storage', array('fetchMetadata'), array(''));
        $storage->expects($this->once())
            ->method('fetchMetadata')
            ->with($this->equalTo('item'))
            ->will($this->returnValue(array(
                'size' => 10,
            )));

        $item = new Item('item', $storage);
        $this->assertEquals(10, $item->getSize());
    }

    public function testGetChangeTime()
    {
        $ctime = time();
        $storage = $this->getMock('Newscoop\Storage', array('fetchMetadata'), array(''));
        $storage->expects($this->once())
            ->method('fetchMetadata')
            ->with($this->equalTo('item'))
            ->will($this->returnValue(array(
                'change_time' => $ctime,
            )));

        $item = new Item('item', $storage);
        $this->assertEquals($ctime, $item->getChangeTime());
    }

    public function testGetType()
    {
        $item = new Item('item.tpl', $this->storage);
        $this->assertEquals('tpl', $item->getType());
    }

    public function testGetTypeDir()
    {
        $storage = $this->getMock('Newscoop\Storage', array('isDir'), array(''));
        $storage->expects($this->once())
            ->method('isDir')
            ->with($this->equalTo('item'))
            ->will($this->returnValue(TRUE));

        $item = new Item('item', $storage);
        $this->assertEquals('dir', $item->getType());
    }
}
