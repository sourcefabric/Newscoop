<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\Ingest\Feed,
    Newscoop\Entity\Ingest\Feed\Entry,
    Newscoop\Services\Ingest\PublisherService;

class IngestServiceTest extends \RepositoryTestCase
{
    /** @var Newscoop\Services\IngestService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var array */
    protected $config;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\Ingest\Feed', 'Newscoop\Entity\Ingest\Feed\Entry');
        $this->cleanFiles();

        $this->config = \Zend_Registry::get('container')->getParameter('ingest');
        $this->publisher = new PublisherService(\Zend_Registry::get('container')->getParameter('ingest_publisher'));
        $this->service = new IngestService($this->config, $this->em, $this->publisher);

    }

    public function tearDown()
    {
        parent::tearDown();
        $this->cleanFiles();
    }

    private function cleanFiles()
    {
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
        $this->assertEmpty($this->service->getFeeds());
    }

    public function testFindBy()
    {
        $feed = new Feed('title');
        $feed->addEntry(new Entry('title', 'content'));
        $feed->addEntry(new Entry('title2', 'content'));

        $this->em->persist($feed);
        $this->em->flush();
        $this->em->clear();

        $entries = $this->service->findBy(array('feed' => $feed->getId()), array('updated' => 'desc'), 10, 0);
        $this->assertEquals(2, count($entries));
    }

    public function testFind()
    {
        $entry = new Entry('title', 'content');
        $this->em->persist($entry);
        $this->em->flush();

        $this->assertEquals($entry, $this->service->find($entry->getId()));
    }

    public function testPublish()
    {
        $entry = new Entry('title', 'content');

        $this->assertFalse($entry->isPublished());
        $this->service->publish($entry);
        $this->assertTrue($entry->isPublished());
    }

    public function testUpdateAll()
    {
        $feed = new Feed('sda');
        $this->service->addFeed($feed);

        $this->assertEquals(0, count($feed->getEntries()));

        $this->service->updateAll();
        $this->assertEquals(3, count($feed->getEntries()));
        $this->assertInstanceOf('DateTime', $feed->getUpdated());

        $this->service->updateAll();
        $this->assertEquals(3, count($feed->getEntries()));

        $tmpFile = APPLICATION_PATH . '/../tests/ingest/' . uniqid('tmp_') . '.xml';
        copy(APPLICATION_PATH . '/../tests/ingest/newsml1.xml', $tmpFile);

        $this->service->updateAll();
        $this->assertEquals(3, count($feed->getEntries()));
    }
}
