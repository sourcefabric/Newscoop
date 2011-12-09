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
        $qb = $this->getMockBuilder('Doctrine\ODM\MongoDB\Query\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->odm->expects($this->once())
            ->method('createQueryBuilder')
            ->with($this->equalTo('Newscoop\News\NewsItem'))
            ->will($this->returnValue($qb));

        $qb->expects($this->once())
            ->method('field')
            ->with($this->equalTo('type'))
            ->will($this->returnValue($qb));

        $qb->expects($this->once())
            ->method('in')
            ->with($this->equalTo(array('news', 'package')))
            ->will($this->returnValue($qb));

        $qb->expects($this->once())
            ->method('sort')
            ->with($this->equalTo(array('id' => 'desc')))
            ->will($this->returnValue($qb));

        $qb->expects($this->once())
            ->method('limit')
            ->with($this->equalTo(25))
            ->will($this->returnValue($qb));

        $qb->expects($this->once())
            ->method('skip')
            ->with($this->equalTo(50))
            ->will($this->returnValue($qb));

        $query = $this->getMockBuilder('Doctrine\ODM\MongoDB\Query\Query')
            ->disableOriginalConstructor()
            ->getMock();

        $qb->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $query->expects($this->once())
            ->method('execute')
            ->will($this->returnValue('result'));

        $this->assertEquals('result', $this->service->findBy(array(), array('id' => 'desc'), 25, 50));
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

        $this->odm->expects($this->atLeastOnce())
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

    public function testSaveCanceledItem()
    {
        $item = $this->getMockBuilder('Newscoop\News\NewsItem')
            ->disableOriginalConstructor()
            ->getMock();

        $item->expects($this->once())
            ->method('isCanceled')
            ->will($this->returnValue(true));

        $this->odm->expects($this->never())
            ->method('persist');

        $this->service->save($item);
    }
}
