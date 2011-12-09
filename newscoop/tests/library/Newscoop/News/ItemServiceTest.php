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
        $this->expectFindBy(array(), array('id' => 'desc'), 25, 50, 'result');
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

    public function testFind()
    {
        $this->expectFindBy(array('id' => 'id'), null, 1, 0, null);
        $this->assertEquals(null, $this->service->find('id'));
    }

    private function expectFindBy(array $criteria, $orderBy, $limit, $offset, $return = 'result')
    {
        $qb = $this->getMockBuilder('Doctrine\ODM\MongoDB\Query\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->odm->expects($this->once())
            ->method('createQueryBuilder')
            ->with($this->equalTo('Newscoop\News\NewsItem'))
            ->will($this->returnValue($qb));

        $criteria['type'] = array('news', 'package');
        $qb->expects($this->exactly(count($criteria)))
            ->method('field')
            ->with($this->logicalOr($this->equalTo('type'), $this->equalTo('id')))
            ->will($this->returnValue($qb));

        $qb->expects($this->any())
            ->method('in')
            ->will($this->returnValue($qb));

        $qb->expects($this->any())
            ->method('equals')
            ->will($this->returnValue($qb));

        if ($orderBy) {
            $qb->expects($this->once())
                ->method('sort')
                ->with($this->equalTo($orderBy))
                ->will($this->returnValue($qb));
        }

        $qb->expects($this->once())
            ->method('limit')
            ->with($this->equalTo($limit))
            ->will($this->returnValue($qb));

        $qb->expects($this->once())
            ->method('skip')
            ->with($this->equalTo($offset))
            ->will($this->returnValue($qb));

        $query = $this->getMockBuilder('Doctrine\ODM\MongoDB\Query\Query')
            ->disableOriginalConstructor()
            ->getMock();

        $qb->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $query->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($return));
    }
}
