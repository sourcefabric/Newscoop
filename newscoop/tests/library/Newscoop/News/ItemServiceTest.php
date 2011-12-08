<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
class ItemServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\News\ItemService */
    protected $service;

    /** @var Doctrine\Common\Persistance\Objectmanager */
    protected $odm;

    public function setUp()
    {
        $this->odm = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new ItemService($this->odm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\News\ItemService', $this->service);
    }

    public function testFindBy()
    {
        $this->odm->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\News\NewsItem'))
            ->will($this->returnValue($this->repository));

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('foo' => 'bar')), $this->equalTo(array('id' => 'desc')), $this->equalTo(1), $this->equalTo(2))
            ->will($this->returnValue('res'));

        $this->assertEquals('res', $this->service->findBy(array('foo' => 'bar'), array('id' => 'desc'), 1, 2));
    }

    public function testSave()
    {
        $item = $this->getMockBuilder('Newscoop\News\NewsItem')
            ->disableOriginalConstructor()
            ->getMock();

        $item->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('itemId'));

        $this->odm->expects($this->once())
            ->method('find')
            ->with($this->equalTo('Newscoop\News\NewsItem'), $this->equalTo('itemId'));

        $this->odm->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($item));

        $this->odm->expects($this->once())
            ->method('flush');

        $this->service->save($item);
    }

    public function testSaveExisting()
    {
        $previous = $this->getMockBuilder('Newscoop\News\NewsItem')
            ->disableOriginalConstructor()
            ->getMock();

        $item = $this->getMockBuilder('Newscoop\News\NewsItem')
            ->disableOriginalConstructor()
            ->getMock();

        $this->odm->expects($this->once())
            ->method('find')
            ->with($this->equalTo('Newscoop\News\NewsItem'))
            ->will($this->returnValue($previous));

        $previous->expects($this->once())
            ->method('getVersion')
            ->will($this->returnValue(1));

        $item->expects($this->once())
            ->method('getVersion')
            ->will($this->returnValue(2));

        $this->odm->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($previous));

        $this->odm->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($item));

        $this->odm->expects($this->once())
            ->method('flush');

        $this->service->save($item);
    }

    public function testSavePackageItem()
    {
        $this->odm->expects($this->once())
            ->method('find')
            ->with($this->equalTo('Newscoop\News\PackageItem'), $this->equalTo('itemId'));

        $item = $this->getMockBuilder('Newscoop\News\PackageItem')
            ->disableOriginalConstructor()
            ->getMock();

        $item->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('itemId'));

        $this->service->save($item);
    }
}
