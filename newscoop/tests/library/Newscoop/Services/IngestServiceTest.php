<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\Ingest\Feed;

class IngestServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Services\IngestService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var array */
    protected $config = array();

    public function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new IngestService($this->config, $this->em);
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\IngestService', $this->service);
    }

    public function testUpdateAll()
    {
        $feed = $this->getMock('Newscoop\Entity\Ingest\Feed', array('update'), array('title'));
        $feed->expects($this->once())
            ->method('update')
            ->with();

        $this->service->addFeed($feed);
        $this->service->updateAll();
    }

    public function testGetFeeds()
    {
        $this->assertEmpty($this->service->getFeeds());

        $feed = $this->getMock('Newscoop\Entity\Ingest\Feed', null, array('title'));
        $this->service->addFeed($feed);

        $feeds = $this->service->getFeeds();
        $this->assertEquals(1, count($feeds));
    }

    public function testFindBy()
    {
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\Ingest\Feed\Entry'))
            ->will($this->returnValue($repository));

        $repository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array('feed' => 1)), $this->equalTo(array('updated' => 'desc')), $this->equalTo(0), $this->equalTo(10))
            ->will($this->returnValue(array('result')));

        $this->assertEquals(array('result'), $this->service->findBy(array('feed' => 1), array('updated' => 'desc'), 0, 10));
    }
}
