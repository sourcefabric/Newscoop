<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

/**
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $root;

    /** @var Newscoop\Storage */
    protected $storage;

    public function setUp()
    {
        $this->root = sys_get_temp_dir() . '/' . uniqid('phpunit_', TRUE);
        mkdir($this->root);
        $this->storage = new Storage($this->root);
    }

    public function tearDown()
    {
        exec("rm -r $this->root");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testStorage()
    {
        $storage = new Storage('/');
        $this->assertInstanceOf('Newscoop\Storage', $storage);

        new Storage('abc');
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
        $storage = new Storage('/dev');
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
        // invalid from
        $this->assertFalse($this->storage->moveItem('from', 'to'));

        $this->storage->storeItem('item', 'data');

        // invalid to
        $this->assertFalse($this->storage->moveItem('item', '../'));

        // file to file
        $this->assertTrue($this->storage->moveItem('item', 'file'));
        $this->assertTrue($this->storage->moveItem('file', 'dir/file'));
        $this->assertTrue($this->storage->moveItem('dir/file', 'item'));

        // file to folder
        $this->assertTrue($this->storage->moveItem('item', 'dir'));
        $this->assertTrue($this->storage->moveItem('dir/item', ''));
        $this->assertEquals('data', $this->storage->fetchItem('item'));

        // dir to dir
        $this->storage->storeItem('dir2/placeholder', 'data');
        $this->assertTrue($this->storage->moveItem('dir', 'newdir'));
        $this->assertTrue($this->storage->moveItem('newdir', 'dir2'));

        // dir to file
        $this->assertFalse($this->storage->moveItem('dir2', 'item'));

        // valid to self
        $this->assertTrue($this->storage->moveItem('item', 'item'));

        $this->storage->storeItem('from', 'data');

        // invalid tree (file/...)
        $this->assertFalse($this->storage->moveItem('from', 'from/to'));
    }

    public function testRenameItem()
    {
        // invalid from
        $this->assertFalse($this->storage->renameItem('invalid', ''));

        $this->storage->storeItem('item', 'data');

        // invalid to
        $this->assertFalse($this->storage->renameItem('item', ''));
        $this->assertFalse($this->storage->renameItem('item', '../'));

        // file to dir
        $this->assertFalse($this->storage->renameItem('item', 'dir/item'));

        // file to file
        $this->assertTrue($this->storage->renameItem('item', 'newitem'));
        $this->assertEquals('data', $this->storage->fetchItem('newitem'));

        $this->storage->storeItem('dir/item', 'data');

        // dir to dir
        $this->assertTrue($this->storage->renameItem('dir', 'newdir'));
        $this->assertEquals('data', $this->storage->fetchItem('newdir/item'));

        $this->storage->storeItem('dir/item', 'data');
        $this->assertFalse($this->storage->renameItem('newdir', 'dir'));

        // dir to file
        $this->assertFalse($this->storage->moveItem('newdir', 'newitem'));
    }

    public function testListItems()
    {
        // test empty
        $items = $this->storage->listItems('');
        $this->assertEmpty($items);

        $this->storage->storeItem('dir/file', 'data');
        $this->assertEquals(array(), $this->storage->listItems('dir/file'));

        $items = $this->storage->listItems('dir');
        $this->assertEquals(1, sizeof($items));
        $item = current($items);
        $this->assertInstanceOf('Newscoop\Storage\Item', current($items));
        $this->assertEquals('dir/file', current($items)->getKey());
    }

    public function testIsDir()
    {
        $this->storage->storeItem('dir/file', 'data');
        $this->assertFalse($this->storage->isDir('dir/file'));
        $this->assertTrue($this->storage->isDir('dir'));
    }

    public function testFetchMetadata()
    {
        $ctime = time();
        $this->storage->storeItem('key', 'data');
        $metadata = $this->storage->fetchMetadata('key');
        $this->assertInternalType('array', $metadata);
        $this->assertEquals(4, $metadata['size']);
        $this->assertLessThan(2, $metadata['change_time'] - $ctime);
    }

    public function testIsWritable()
    {
        $this->assertTrue($this->storage->isWritable('/'));

        $storage = new Storage('/dev');
        $this->assertFalse($storage->isWritable('/'));
    }

    public function testCreateDir()
    {
        $this->assertFalse(is_dir("$this->root/newdir"));
        $this->storage->createDir('newdir');
        $this->assertTrue(is_dir("$this->root/newdir"));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateDirException()
    {
        $this->storage->createDir('newdir');
        $this->storage->createDir('newdir');
    }

    public function testCreateFile()
    {
        $this->assertFalse(realpath("$this->root/newfile"));
        $this->storage->createFile('newfile');
        $this->assertTrue(is_file("$this->root/newfile"));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateFileException()
    {
        $this->storage->createFile('newfile');
        $this->storage->createFile('newfile');
    }

    public function testIsUsed()
    {
        $this->assertFalse($this->storage->isUsed('key.tpl'));

        $this->storage->storeItem('item', 'key.tpl');
        $this->assertFalse($this->storage->isUsed('key.tpl'));

        $this->storage->storeItem('item.tpl', 'key.tpl');
        $this->assertTrue($this->storage->isUsed('key.tpl'));

        chmod("$this->root/item.tpl", 0204);
        $this->assertEquals(CAMP_ERROR_READ_FILE, $this->storage->isUsed('key.tpl'));
        chmod("$this->root/item.tpl", 0644);
    }
}
