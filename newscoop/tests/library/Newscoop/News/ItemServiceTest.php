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

        $this->odm->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\News\NewsItem'))
            ->will($this->returnValue($this->repository));

        $this->service = new ItemService($this->odm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\News\ItemService', $this->service);
    }

    public function testFindBy()
    {
        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('foo' => 'bar')), $this->equalTo(array('id' => 'desc')), $this->equalTo(1), $this->equalTo(2))
            ->will($this->returnValue('res'));

        $this->assertEquals('res', $this->service->findBy(array('foo' => 'bar'), array('id' => 'desc'), 1, 2));
    }

    public function testPersist()
    {
        $item = $this->getMockBuilder('Newscoop\News\NewsItem')
            ->disableOriginalConstructor()
            ->getMock();

        $item->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('itemId'));

        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo('itemId'));

        $this->odm->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($item));

        $this->service->persist($item);
    }

    public function testPersistExisting()
    {
        $previous = $this->getMockBuilder('Newscoop\News\NewsItem')
            ->disableOriginalConstructor()
            ->getMock();

        $item = $this->getMockBuilder('Newscoop\News\NewsItem')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository->expects($this->once())
            ->method('find')
            ->will($this->returnValue($previous));

        $previous->expects($this->once())
            ->method('update')
            ->with($this->equalTo($item));

        $this->service->persist($item);
    }
}
