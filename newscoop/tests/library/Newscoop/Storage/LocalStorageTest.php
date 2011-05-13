<?php

namespace Newscoop\Storage;

class LocalStorageTest extends \PHPUnit_Framework_TestCase
{
    protected $root;
    protected $storage;

    public function setUp()
    {
        $this->root = sys_get_temp_dir() . '/' . uniqid('phpunit_');
        mkdir($this->root);
        $this->storage = new LocalStorage($this->root);
    }

    public function tearDown()
    {
        $this->rmDir($this->root);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLocalStorage()
    {
        $storage = new LocalStorage(sys_get_temp_dir());
        $this->assertType('Newscoop\Storage\LocalStorage', $storage);

        // throws
        new LocalStorage(uniqid());
    }

    public function testStoreItem()
    {
        // test valid
        $this->assertTrue($this->storage->storeItem('test', 'data'));
        $this->assertEquals('data', $this->storage->fetchItem('test'));

        // test newdir/file
        $this->assertTrue($this->storage->storeItem('testdir/first', 'data'));
        $this->assertEquals('data', $this->storage->fetchItem('testdir/first'));

        // test olddir/file
        $this->assertTrue($this->storage->storeItem('testdir/second', 'data'));
        $this->assertEquals('data', $this->storage->fetchItem('testdir/second'));

        // test file/file
        $this->assertFalse($this->storage->storeItem('test/test', 'data'));

        // test outside
        $this->assertFalse($this->storage->storeItem('../out', 'data'));

        // test inside
        $this->assertTrue($this->storage->storeItem('./in', 'data'));
        $this->assertEquals('data', $this->storage->fetchItem('test'));

        // test root
        $this->assertFalse($this->storage->storeItem('.', 'data'));

        // test without permissions
        $storage = new LocalStorage('/dev');
        $this->assertFalse($storage->storeItem('sub/dir', 'data'));
    }

    public function testFetchItem()
    {
        $this->assertFalse($this->storage->fetchItem('empty'));
        $this->assertFalse($this->storage->fetchItem('.'));
        $this->storage->storeItem('full', 'data');
        $this->assertEquals('data', $this->storage->fetchItem('full'));
    }

    public function testDeleteItem()
    {
        // test invalid
        $this->assertFalse($this->storage->deleteItem('empty'));
        $this->assertFalse($this->storage->deleteItem('../test'));
        $this->assertFalse($this->storage->deleteItem('.'));

        // test valid
        $this->storage->storeItem('test', 'data');
        $this->assertTrue($this->storage->deleteItem('test'));
        $this->assertFalse($this->storage->fetchItem('test'));

        // test dir
        $this->storage->storeItem('test/first/file', 'data');
        $this->storage->storeItem('test/second/file', 'data');
        $this->assertTrue($this->storage->deleteItem('test'));
        $this->assertFalse($this->storage->fetchItem('test/first/file'));
        $this->assertFalse($this->storage->fetchItem('test/second/file'));
    }

    public function testCopyItem()
    {
        // invalid to invalid
        $this->assertFalse($this->storage->copyItem('from', 'to'));

        // valid to valid
        $this->storage->storeItem('from', 'data');
        $this->assertTrue($this->storage->copyItem('from', 'to'));
        $this->assertEquals('data', $this->storage->fetchItem('to'));

        // valid to invalid
        $this->assertFalse($this->storage->copyItem('to', '../'));

        // valid to subfolder
        $this->assertTrue($this->storage->copyItem('to', 'test/subdir'));

        // folder to valid
        $this->assertFalse($this->storage->copyItem('test', 'folder'));

        // valid to self
        $this->assertTrue($this->storage->copyItem('to', 'to'));

        // tree
        $this->storage->storeItem('from', 'data');
        $this->assertFalse($this->storage->copyItem('from', 'from/to'));
    }

    public function testMoveItem()
    {
        // invalid to invalid
        $this->assertFalse($this->storage->moveItem('from', 'to'));

        // valid to valid
        $this->storage->storeItem('from', 'data');
        $this->assertTrue($this->storage->moveItem('from', 'to'));
        $this->assertEquals('data', $this->storage->fetchItem('to'));

        // valid to invalid
        $this->assertFalse($this->storage->moveItem('to', '../'));

        // valid to subfolder
        $this->assertTrue($this->storage->moveItem('to', 'test/subdir'));

        // valid to self
        $this->assertTrue($this->storage->moveItem('test/subdir', 'test/subdir'));

        // tree
        $this->storage->storeItem('from', 'data');
        $this->assertFalse($this->storage->moveItem('from', 'from/to'));
    }

    public function testListItems()
    {
        // test empty
        $items = $this->storage->listItems('');
        $this->assertEquals(array(), $items);

        // test bad
        $items = $this->storage->listItems('nonsense');
        $items = $this->storage->listItems('./../');

        // test file
        $this->storage->storeItem('file', 'data');
        $this->assertEquals(array(), $this->storage->listItems('file'));

        // test one
        $this->assertEquals(array('file'), $this->storage->listItems(''));

        // test more
        $this->storage->storeItem('dir/file', 'data');
        $items = $this->storage->listItems('');
        $this->assertEquals(2, sizeof($items));
        $this->assertContains('file', $items);
        $this->assertContains('dir', $items);

        // test subdir
        $this->assertEquals(array('file'), $this->storage->listItems('dir'));
    }

    protected function rmDir($dir)
    {
        foreach (glob("$dir/*") as $file) {
            if (is_dir($file)) {
                $this->rmDir($file);
            } else {
                unlink($file);
            }
        }

        rmdir($dir);
    }
}
