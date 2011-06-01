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

        $this->storage->createDir('dir');
    }

    public function tearDown()
    {
        exec("rm -r $this->root");
    }

    public function testStorage()
    {
        $storage = new Storage('/');
        $this->assertInstanceOf('Newscoop\Storage', $storage);
    }

    /**
     * @expectedException InvalidArgumentException abc
     */
    public function testStorageNotFound()
    {
        new Storage('abc');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testStorageNotDir()
    {
        $this->storage->storeItem('file', 'data');
        new Storage("$this->root/file");
    }

    public function testStoreItem()
    {
        // test valid
        $this->assertEquals(4, $this->storage->storeItem('test', 'data'));
        $this->assertEquals('data', $this->storage->fetchItem('test'));
    }

    /**
     * @expectedException InvalidArgumentException dir
     */
    public function testStoreItemInvalidDestIsDir()
    {
        $this->storage->storeItem('dir', 'data');
    }

    /**
     * @expectedException InvalidArgumentException notfound
     */
    public function testStoreItemDirNotFound()
    {
        $this->storage->storeItem('notfound/item', 'data');
    }

    public function testFetchItem()
    {
        $this->assertNull($this->storage->fetchItem('not-found'));

        $this->storage->storeItem('item', 'data');
        $this->assertEquals('data', $this->storage->fetchItem('item'));
    }

    /**
     * @expectedException InvalidArgumentException invalid
     */
    public function testFetchItemException()
    {
        $this->storage->fetchItem('../invalid');
    }

    /**
     * @expectedException InvalidArgumentException dir
     */
    public function testFetchItemDir()
    {
        $this->storage->fetchItem('dir');
    }

    public function testDeleteItem()
    {
        $this->storage->storeItem('item', 'data');
        $this->assertFileExists("$this->root/item");
        $this->storage->deleteItem('item');
        $this->assertFileNotExists("$this->root/item");

        $this->assertFileExists("$this->root/dir");
        $this->storage->deleteItem('dir');
        $this->assertFileNotExists("$this->root/dir");
    }

    /**
     * @expectedException InvalidArgumentException dir
     */
    public function testDeleteDirNotEmpty()
    {
        $this->storage->storeItem('dir/item', 'data');
        $this->storage->deleteItem('dir');
    }

    /**
     * @expectedException InvalidArgumentException notfound
     */
    public function testDeleteNotFound()
    {
        $this->storage->deleteItem('notfound');
    }

    /**
     * @expectedException InvalidArgumentException notfound
     */
    public function testCopyItemNotFound()
    {
        $this->storage->copyItem('notfound', 'dest');
    }

    /**
     * @expectedException InvalidArgumentException dir
     */
    public function testCopyItemDir()
    {
        $this->storage->copyItem('dir', 'dest');
    }

    /**
     * @expectedException InvalidArgumentException item
     */
    public function testCopyItemConflict()
    {
        $this->storage->storeItem('item', 'data');
        $this->storage->copyItem('item', 'item');
    }

    public function testCopyItem()
    {
        $this->storage->storeItem('item', 'data');
        $this->storage->copyItem('item', 'copy');
        $this->assertEquals('data', $this->storage->fetchItem('copy'));
        $this->assertEquals('data', $this->storage->fetchItem('item'));
    }

    /**
     * @expectedException InvalidArgumentException notfound
     */
    public function testMoveItemSrcNotFound()
    {
        $this->storage->moveItem('notfound', 'dest');
    }

    /**
     * @expectedException InvalidArgumentException dir
     */
    public function testMoveItemSrcIsDir()
    {
        $this->storage->storeItem('dir/file', 'data');
        $this->storage->moveItem('dir', 'dest');
    }

    /**
     * @expectedException InvalidArgumentException notfound
     */
    public function testMoveItemDestNotFound()
    {
        $this->storage->storeItem('file', 'data');
        $this->storage->moveItem('file', 'notfound');
    }

    /**
     * @expectedException InvalidArgumentException dir/file
     */
    public function testMoveItemConflict()
    {
        $this->storage->storeItem('dir/file', 'data');
        $this->storage->moveItem('dir/file', 'dir');
    }

    public function testMoveItem()
    {
        $this->storage->storeItem('src.tpl', 'srcdata');
        $this->storage->storeItem('dir/placeholder.tpl', 'src.tpl');
        $this->storage->moveItem('src.tpl', 'dir');

        $this->assertEquals('srcdata', $this->storage->fetchItem('dir/src.tpl'));

        // test replace
        $this->assertEquals('dir/src.tpl', $this->storage->fetchItem('dir/placeholder.tpl'));
    }

    /**
     * @expectedException InvalidArgumentException notfound
     */
    public function testRenameItemNotFound()
    {
        $this->storage->renameItem('notfound', 'new');
    }

    /**
     * @expectedException InvalidArgumentException dir
     */
    public function testRenameItemDir()
    {
        $this->storage->renameItem('dir', 'newdir');
    }

    /**
     * @expectedException InvalidArgumentException item
     */
    public function testRenameItemConflict()
    {
        $this->storage->storeItem('item', 'data');
        $this->storage->renameItem('item', 'item');
    }

    public function testRenameItem()
    {
        $this->storage->storeItem('item', 'data');
        $this->storage->renameItem('item', 'renamed');
        $this->assertEquals('data', $this->storage->fetchItem('renamed'));
    }

    public function testListItems()
    {
        // test empty
        $this->assertEmpty($this->storage->listItems('dir'));

        $items = $this->storage->listItems('');
        $this->assertEquals(1, sizeof($items));
        $this->assertEquals('dir', current($items));
    }

    /**
     * @expectedException InvalidArgumentException item
     */
    public function testListItemsNotDir()
    {
        $this->storage->storeItem('item', 'data');
        $this->storage->listItems('item');
    }

    /**
     * @expectedException InvalidArgumentException notfound
     */
    public function testListItemsNotFound()
    {
        $this->storage->listItems('notfound');
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
    public function testCreateFileSame()
    {
        $this->storage->createFile('newfile');
        $this->storage->createFile('newfile');
    }

    /**
     * @expectedExceptio InvalidArgumentException
     */
    public function testGetItemException()
    {
        $this->storage->getItem('key');
    }

    public function testGetItem()
    {
        $item = $this->storage->getItem('dir');
        $this->assertInstanceOf('Newscoop\Storage\Item', $item);
    }

    public function testGetMimeType()
    {
        $mime = $this->storage->getMimeType('dir');
        $this->assertEquals('directory', $mime);
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

    public function testGetRealpath()
    {
        $this->assertEquals(realpath("$this->root/dir"), $this->storage->getRealpath('dir'));
    }
}
