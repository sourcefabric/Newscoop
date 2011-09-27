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

    public function testAutoMode()
    {
        $this->setAutoMode();
        $this->assertTrue($this->service->isAutoMode());
        $this->service->switchAutoMode();
        $this->assertFalse($this->service->isAutoMode());
    }

    public function testPublish()
    {
        $entry = new Entry('title', 'content');
        $this->assertFalse($entry->isPublished());

        $article = $this->service->publish($entry);

        $this->assertInstanceOf('Article', $article);
        $this->assertGreaterThan(0, $article->getArticleNumber());
        $this->assertTrue($article->isPublished());
        $this->assertTrue($entry->isPublished());
    }

    public function testPrepare()
    {
        $entry = new Entry('title', 'content');
        $article = $this->service->publish($entry, 'N');
        $this->assertFalse($article->isPublished());
        $this->assertTrue($entry->isPublished());
    }

    public function testUpdateAllEmpty()
    {
        $this->service->updateAll();
        $this->assertEquals(0, count($this->service->getFeeds()));
    }

    public function testUpdateAll()
    {
        $feed = new Feed('sda');
        $this->service->addFeed($feed);
        $this->assertEquals(0, count($feed->getEntries()));

        $this->service->updateAll();

        $this->assertEquals(6, count($feed->getEntries()));
        $this->assertInstanceOf('DateTime', $feed->getUpdated());
    }

    public function testUpdateAutoModeWithoutPublishedPreviousVersion()
    {
        $feed = new Feed('sda');
        $this->service->addFeed($feed);
    }

    public function testUpdateAllUnique()
    {
        $feed = new Feed('sda');
        $this->service->addFeed($feed);

        $this->service->updateAll();
        $this->service->updateAll();

        $this->assertEquals(6, count($feed->getEntries()));
    }

    public function testUpdateAllTimeout()
    {
        $feed = new Feed('sda');
        $this->service->addFeed($feed);
        $tmpFile = APPLICATION_PATH . '/../tests/ingest/' . uniqid('tmp_') . '.xml';
        copy(APPLICATION_PATH . '/../tests/ingest/newsml1.xml', $tmpFile);

        $this->service->updateAll();
        $this->assertEquals(6, count($feed->getEntries()));
    }

    public function testLiftEmbargoNew()
    {
        $feed = new Feed('sda');
        $this->service->addFeed($feed);

        $entry = $this->getEntry(array(
            'getTitle' => 'test',
            'getContent' => 'test',
            'getStatus' => 'Embargoed',
            'getLiftEmbargo' => new \DateTime('+2 day'),
        ));

        $this->em->persist($entry);
        $this->em->flush();
        $this->em->clear();

        $this->service->updateAll();

        $loaded = $this->em->find('Newscoop\Entity\Ingest\Feed\Entry', $entry->getId());
        $this->assertEquals('Embargoed', $loaded->getStatus());
    }

    public function testLiftEmbargoOld()
    {
        $feed = new Feed('sda');
        $this->service->addFeed($feed);

        $entry = $this->getEntry(array(
            'getTitle' => 'test',
            'getContent' => 'test',
            'getStatus' => 'Embargoed',
            'getLiftEmbargo' => new \DateTime('-2 day'),
        ));

        $this->em->persist($entry);
        $this->em->flush();
        $this->em->clear();

        $this->service->updateAll();

        $loaded = $this->em->find('Newscoop\Entity\Ingest\Feed\Entry', $entry->getId());
        $this->assertEquals('Usable', $loaded->getStatus());
    }

    /**
     * Get entry from parser with given methods
     *
     * @param array $methods
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    private function getEntry(array $methods)
    {
        $parser = $this->getMockBuilder('Newscoop\Ingest\Parser\NewsMlParser')
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($methods as $method => $return) {
            $parser->expects($this->once())
                ->method($method)
                ->will($this->returnValue($return));
        }

        return Entry::create($parser);
    }

    /**
     * Set auto mode
     *
     * @param bool $auto
     */
    private function setAutoMode($auto = true)
    {
        \SystemPref::Set(IngestService::MODE_SETTING, $auto);
    }
}
