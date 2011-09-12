<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\Ingest\Feed,
    Newscoop\Entity\Ingest\Feed\Entry;

class IngestServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Services\IngestService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var array */
    protected $config;

    public function setUp()
    {
        $this->config = \Zend_Registry::get('container')->getParameter('ingest');

        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->publisher = $this->getMockBuilder('Newscoop\Services\Ingest\PublisherService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new IngestService($this->config, $this->em, $this->publisher);

        foreach (glob(APPLICATION_PATH . '/../tests/ingest/tmp_*.xml') as $file) {
            unlink($file);
        }
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\IngestService', $this->service);
    }

    public function testGetFeeds()
    {
        $repository = $this->getRepository();
        $repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array('feeds')));

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\Ingest\Feed'))
            ->will($this->returnValue($repository));

        $this->assertEquals(array('feeds'), $this->service->getFeeds());
    }

    public function testFindBy()
    {
        $repository = $this->getRepository();

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

    public function testFind()
    {
        $repository = $this->getRepository();
        $repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo(123))
            ->will($this->returnValue('entry'));

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\Ingest\Feed\Entry'))
            ->will($this->returnValue($repository));

        $this->assertEquals('entry', $this->service->find(123));
    }

    public function testPublish()
    {
        $entry = new Entry('title', 'content');
        $this->assertFalse($entry->isPublished());

        $this->publisher->expects($this->once())
            ->method('publish')
            ->with($this->isInstanceOf('Newscoop\Entity\Ingest\Feed\Entry'));

        $this->em->expects($this->once())
            ->method('persist')
            ->id('persist')
            ->with($this->isInstanceOf('Newscoop\Entity\Ingest\Feed\Entry'));

        $this->em->expects($this->once())
            ->method('flush')
            ->after('persist');

        $this->service->publish($entry);
        $this->assertTrue($entry->isPublished());
    }

    /**
     * Get repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testUpdateAll()
    {
        $feed = new Feed('sda');
        $repository = $this->getRepository();

        $repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array($feed)));

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\Ingest\Feed'))
            ->will($this->returnValue($repository));

        $this->assertEquals(0, count($feed->getEntries()));

        $this->service->updateAll();
        $this->assertEquals(2, count($feed->getEntries()));
        $this->assertInstanceOf('DateTime', $feed->getUpdated());

        $this->service->updateAll();
        $this->assertEquals(2, count($feed->getEntries()));

        $tmpFile = APPLICATION_PATH . '/../tests/ingest/' . uniqid('tmp_') . '.xml';
        copy(APPLICATION_PATH . '/../tests/ingest/newsml1.xml', $tmpFile);

        $this->service->updateAll();
        $this->assertEquals(2, count($feed->getEntries()));
    }
}
