<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Storage */
    protected $storage;

    /** @var Newscoop\Entity\Repository\TemplateRepository */
    protected $repository;

    /** @var Newscoop\Service\Template */
    protected $service;

    public function setUp()
    {
        $this->storage = $this->getMockBuilder('Newscoop\Storage')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Newscoop\Entity\Repository\TemplateRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new Template($this->storage, $this->repository);
    }

    public function testTemplate()
    {
        $service = new Template($this->storage, $this->repository);
        $this->assertInstanceOf('Newscoop\Service\Template', $service);
    }

    public function testListItemsEmpty()
    {
        $this->storage->expects($this->once())
            ->method('listItems')
            ->with($this->equalTo(''))
            ->will($this->returnValue(array()));

        $items = $this->service->listItems('');
        $this->assertEmpty($items);
    }

    public function testListItems()
    {
        $dir = $this->getStorageItem(true, 'dir');
        $file = $this->getStorageItem(false, 'file');

        $this->storage->expects($this->once())
            ->method('listItems')
            ->with($this->equalTo('path'))
            ->will($this->returnValue(array('dir', 'file')));

        $this->storage->expects($this->any())
            ->method('getItem')
            ->will($this->onConsecutiveCalls($dir, $file));

        $template = $this->getMock('Newscoop\Entity\Template', null, array('key'));
        $this->repository->expects($this->once())
            ->method('getTemplate')
            ->with($this->equalTo('path/file'))
            ->will($this->returnValue($template));

        $items = $this->service->listItems('path');

        $this->assertEquals(2, sizeof($items));
        $this->assertObjectHasAttribute('key', $items[0]);
        $this->assertObjectHasAttribute('name', $items[0]);
        $this->assertObjectHasAttribute('type', $items[0]);
        $this->assertObjectHasAttribute('realpath', $items[0]);

        $this->assertObjectHasAttribute('key', $items[1]);
        $this->assertObjectHasAttribute('name', $items[1]);
        $this->assertObjectHasAttribute('type', $items[1]);
        $this->assertObjectHasAttribute('size', $items[1]);
        $this->assertObjectHasAttribute('ctime', $items[1]);
        $this->assertObjectHasAttribute('id', $items[1]);
        $this->assertObjectHasAttribute('ttl', $items[1]);
        $this->assertObjectHasAttribute('realpath', $items[1]);
    }

    /**
     * @expectedException InvalidArgumentException notfound
     */
    public function testListItemsException()
    {
        $this->storage->expects($this->once())
            ->method('listItems')
            ->with('notfound')
            ->will($this->throwException(new \InvalidArgumentException));

        $this->service->listItems('notfound');
    }

    public function testFetchItem()
    {
        $this->storage->expects($this->once())
            ->method('fetchItem')
            ->with($this->equalTo('key'));

        $this->service->fetchItem('key');
    }

    public function testStoreItem()
    {
        $this->storage->expects($this->once())
            ->method('storeItem')
            ->with($this->equalTo('key'), $this->equalTo('data'));

        $this->service->storeItem('key', 'data');
    }

    public function testCopyItem()
    {
        $this->storage->expects($this->once())
            ->method('copyItem')
            ->with('from', 'to');
        $this->service->copyItem('from', 'to');
    }

    /**
     * @expectedException InvalidArgumentException to
     */
    public function testCopyItemException()
    {
        $this->storage->expects($this->once())
            ->method('copyItem')
            ->with('from', 'to')
            ->will($this->throwException(new \InvalidArgumentException));
        $this->service->copyItem('from', 'to');
    }

    public function testMoveItem()
    {
        $this->storage->expects($this->once())
            ->method('moveItem')
            ->with('src', 'dest');

        $this->repository->expects($this->once())
            ->method('updateKey')
            ->with('src', 'dest/src');

        $this->service->moveItem('src', 'dest');
    }

    /**
     * @expectedException InvalidArgumentException src
     */
    public function testMoveItemException()
    {
        $this->storage->expects($this->once())
            ->method('moveItem')
            ->with('src', 'dest')
            ->will($this->throwException(new \InvalidArgumentException));

        $this->service->moveItem('src', 'dest');
    }

    public function testRenameItem()
    {
        $this->storage->expects($this->once())
            ->method('renameItem')
            ->with('from', 'to');

        $this->repository->expects($this->once())
            ->method('updateKey')
            ->with('from', 'to');

        $this->service->renameItem('from', 'to');
    }

    /**
     * @expectedException InvalidArgumentException from
     */
    public function testRenameItemException()
    {
        $this->storage->expects($this->once())
            ->method('renameItem')
            ->with('from', 'to')
            ->will($this->throwException(new \InvalidArgumentException));

        $this->service->renameItem('from', 'to');
    }

    public function testDeleteItemNotUsed()
    {
        $this->storage->expects($this->once())
            ->method('isUsed')
            ->with($this->equalTo('key'))
            ->will($this->returnValue(false));

        $this->repository->expects($this->once())
            ->method('isUsed')
            ->with($this->equalTo('key'))
            ->will($this->returnValue(false));

        $this->storage->expects($this->once())
            ->method('deleteItem')
            ->with($this->equalTo('key'));

        $this->repository->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('key'));

        $this->service->deleteItem('key');
    }

    /**
     * @expectedException InvalidArgumentException key
     */
    public function testDeleteItemException()
    {
        $this->storage->expects($this->once())
            ->method('isUsed')
            ->with($this->equalTo('key'))
            ->will($this->returnValue(false));

        $this->repository->expects($this->once())
            ->method('isUsed')
            ->with($this->equalTo('key'))
            ->will($this->returnValue(false));

        $this->storage->expects($this->once())
            ->method('deleteItem')
            ->with($this->equalTo('key'))
            ->will($this->throwException(new \InvalidArgumentException));

        $this->service->deleteItem('key');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeleteItemUsedRepository()
    {
        $this->repository->expects($this->once())
            ->method('isUsed')
            ->with($this->equalTo('key'))
            ->will($this->returnValue(true));

        $this->service->deleteItem('key');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeleteItemUsedStorage()
    {
        $this->storage->expects($this->once())
            ->method('isUsed')
            ->with($this->equalTo('key'))
            ->will($this->returnValue(true));

        $this->service->deleteItem('key');
    }

    public function testCreateFile()
    {
        $this->storage->expects($this->once())
            ->method('createFile')
            ->with($this->equalTo('file'));

        $this->service->createFile('file');
    }

    /**
     * @expectedException InvalidArgumentException file
     */
    public function testCreateFileException()
    {
        $this->storage->expects($this->once())
            ->method('createFile')
            ->with($this->equalTo('file'))
            ->will($this->throwException(new \InvalidArgumentException));

        $this->service->createFile('file');
    }

    public function testCreateFolder()
    {
        $this->storage->expects($this->once())
            ->method('createDir')
            ->with($this->equalTo('dir'));

        $this->service->createFolder('dir');
    }

    /**
     * @expectedException InvalidArgumentException dir
     */
    public function testCreateFolderException()
    {
        $this->storage->expects($this->once())
            ->method('createDir')
            ->with($this->equalTo('dir'))
            ->will($this->throwException(new \InvalidArgumentException));

        $this->service->createFolder('dir');
    }

    public function testIsWritable()
    {
        $this->storage->expects($this->once())
            ->method('isWritable')
            ->with($this->equalTo('path'));

        $this->service->isWritable('path');
    }

    public function testFetchMetadata()
    {
        $item = $this->getStorageItem(false, 'key');
        $template = $this->getMockBuilder('Newscoop\Entity\Template')
            ->disableOriginalConstructor()
            ->getMock();

        $this->storage->expects($this->once())
            ->method('getItem')
            ->with('key')
            ->will($this->returnValue($item));

        $this->repository->expects($this->once())
            ->method('getTemplate')
            ->with('key')
            ->will($this->returnValue($template));

        $metadata = $this->service->fetchMetadata('key');
        $this->assertObjectHasAttribute('ttl', $metadata);
        $this->assertObjectHasAttribute('key', $metadata);
        $this->assertObjectHasAttribute('name', $metadata);
        $this->assertObjectHasAttribute('type', $metadata);
        $this->assertObjectHasAttribute('size', $metadata);
        $this->assertObjectHasAttribute('ctime', $metadata);
        $this->assertObjectHasAttribute('id', $metadata);
        $this->assertObjectHasAttribute('ttl', $metadata);
    }

    public function testFetchMetadataDir()
    {
        $item = $this->getStorageItem(true, 'key');

        $this->storage->expects($this->once())
            ->method('getItem')
            ->with('key')
            ->will($this->returnValue($item));

        $metadata = $this->service->fetchMetadata('key');
        $this->assertObjectHasAttribute('key', $metadata);
        $this->assertObjectHasAttribute('name', $metadata);
    }

    public function testStoreMetadata()
    {
        $template = $this->getMockBuilder('Newscoop\Entity\Template')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository->expects($this->once())
            ->method('getTemplate')
            ->with('key')
            ->will($this->returnValue($template));

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($template), array('cache_lifetime' => 5));

        $this->service->storeMetadata('key', array('cache_lifetime' => 5));
    }

    public function testReplaceItem()
    {
        $this->storage->expects($this->once())
            ->method('getMimeType')
            ->with('key')
            ->will($this->returnValue('text/html'));

        $file = $this->getMockBuilder('Zend_Form_Element_File')
            ->disableOriginalConstructor()
            ->getMock();

        $file->expects($this->once())
            ->method('getMimeType')
            ->will($this->returnValue('text/plain'));

        $filename = sys_get_temp_dir() . '/' . uniqid('phpunit_', TRUE);
        file_put_contents($filename, 'data');

        $file->expects($this->once())
            ->method('getFileName')
            ->will($this->returnValue($filename));

        $this->storage->expects($this->once())
            ->method('storeItem')
            ->with('key', 'data');

        $this->service->replaceItem('key', $file);
        unlink($filename);
    }

    public function testReplaceItemCharset()
    {
        $this->storage->expects($this->once())
            ->method('getMimeType')
            ->with('key')
            ->will($this->returnValue('text/html'));

        $file = $this->getMockBuilder('Zend_Form_Element_File')
            ->disableOriginalConstructor()
            ->getMock();

        $file->expects($this->once())
            ->method('getMimeType')
            ->will($this->returnValue('text/plain; charset=utf-8'));

        $filename = sys_get_temp_dir() . '/' . uniqid('phpunit_', TRUE);
        file_put_contents($filename, 'data');

        $file->expects($this->once())
            ->method('getFileName')
            ->will($this->returnValue($filename));

        $this->storage->expects($this->once())
            ->method('storeItem')
            ->with('key', 'data');

        $this->service->replaceItem('key', $file);
        unlink($filename);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testReplaceItemException()
    {
        $this->storage->expects($this->once())
            ->method('getMimeType')
            ->with('key')
            ->will($this->returnValue('text/html'));

        $file = $this->getMockBuilder('Zend_Form_Element_File')
            ->disableOriginalConstructor()
            ->getMock();

        $file->expects($this->once())
            ->method('getMimeType')
            ->will($this->returnValue('binary/jpeg'));

        $this->service->replaceItem('key', $file);
    }

    /**
     * Get Newscoop\Storage\Item mock
     *
     * @param bool $isDir
     * @param string $key
     * @return Newscoop\Storage\Item
     */
    protected function getStorageItem($isDir = false, $key = null)
    {
        if ($key == null) {
            $key = uniqid();
        }

        $mock = $this->getMockBuilder('Newscoop\Storage\Item')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('getKey')
            ->with()
            ->will($this->returnValue($key));

        $mock->expects($this->once())
            ->method('isDir')
            ->with()
            ->will($this->returnValue($isDir));

        return $mock;
    }
}
