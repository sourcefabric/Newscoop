<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
class FeedServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\News\FeedService */
    protected $service;

    /** @var Doctrine\Common\Persistance\Objectmanager */
    protected $odm;

    /** @var Newscoop\News\ItemService */
    protected $itemService;

    public function setUp()
    {
        $this->odm = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');

        $this->odm->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\News\ReutersFeed'))
            ->will($this->returnValue($this->repository));

        $this->itemService = $this->getMockBuilder('Newscoop\News\ItemService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new FeedService($this->odm, $this->itemService);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\News\FeedService', $this->service);
    }

    public function testFindBy()
    {
        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('foo' => 'bar')), $this->equalTo(array('id' => 'desc')), $this->equalTo(1), $this->equalTo(2))
            ->will($this->returnValue('res'));

        $this->assertEquals('res', $this->service->findBy(array('foo' => 'bar'), array('id' => 'desc'), 1, 2));
    }

    public function testUpdateAll()
    {
        $feed = $this->getMockBuilder('Newscoop\News\Feed')
            ->disableOriginalConstructor()
            ->getMock();

        $feed->expects($this->once())
            ->method('update')
            ->with($this->equalTo($this->odm), $this->equalTo($this->itemService));

        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array($feed)));

        $this->service->updateAll();
    }

    public function testSave()
    {
        $config = array(
            'username' => 'tic',
            'password' => 'toc',
        );

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array(
                'type' => 'reuters',
                'config' => $config,
            )));

        $this->odm->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf('Newscoop\News\ReutersFeed'));

        $this->odm->expects($this->once())
            ->method('flush');

        $feed = $this->service->save(array(
            'type' => 'reuters',
            'config' => $config,
        ));

        $this->assertInstanceOf('Newscoop\News\ReutersFeed', $feed);
    }
}
