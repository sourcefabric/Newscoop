<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
class ReutersFeedTest extends \PHPUnit_Framework_TestCase
{
    /** @var Zend_Rest_Client */
    protected $client;

    /** @var Newscoop\News\ReutersFeed */
    protected $feed;

    /** @var Doctrine\Common\Persistance\Objectmanager */
    protected $odm;

    public function setUp()
    {
        global $application;

        $this->odm = $application->getBootstrap()->getResource('odm');

        if ($this->odm === null) {
            $this->markTestSkipped('Mongo extension not available.');
        }

        if ($application->getOption('reuters') === null) {
            $this->markTestSkipped('API settings not available.');
        }

        $this->odm->getConfiguration()->setDefaultDB('phpunit');
        $this->odm->getSchemaManager()->dropDocumentDatabase('Newscoop\News\ReutersFeed');
        $this->odm->clear();

        $this->feed = new ReutersFeed($application->getOption('reuters'));
    }

    public function tearDown()
    {
        $this->odm->getSchemaManager()->dropDocumentDatabase('Newscoop\News\ReutersFeed');
        $this->odm->clear();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Newscoop\News\ReutersFeed', $this->feed);
    }

    public function testGetName()
    {
        global $application;

        $options = $application->getOptions();
        $this->assertContains('Reuters', $this->feed->getName());
        $this->assertContains($options['reuters']['username'], $this->feed->getName());
    }

    public function testGetChannels()
    {
        $channels = $this->feed->getChannels();
        $this->assertNotEmpty($channels);

        $this->assertObjectHasAttribute('alias', $channels[0]);
        $this->assertObjectHasAttribute('description', $channels[0]);
        $this->assertObjectHasAttribute('lastUpdate', $channels[0]);
        $this->assertObjectHasAttribute('category', $channels[0]);
        $this->assertInstanceOf('DateTime', $channels[0]->lastUpdate);

        return $channels;
    }

    public function testUpdate()
    {
        $itemService = new ItemService($this->odm);

        $this->odm->persist($this->feed);
        $this->odm->flush();

        $this->assertNotNull($this->feed->getId());
        $this->assertNull($this->feed->getUpdated());

        $this->feed->update($this->odm, $itemService);
        $this->assertInstanceOf('DateTime', $this->feed->getUpdated());

        $items = $this->odm->getRepository('Newscoop\News\NewsItem')->findBy(array('feed.id' => $this->feed->getId()));
        $this->assertGreaterThan(0, count($items));

        // test with relative date
        $this->feed->update($this->odm, $itemService);
    }

    public function testGetRemoteContentSrc()
    {
        $remoteContent = $this->getMockBuilder('Newscoop\News\RemoteContent')
            ->disableOriginalConstructor()
            ->getMock();

        $remoteContent->expects($this->once())
            ->method('getHref')
            ->will($this->returnValue('href'));

        $this->assertContains("href?token=", $this->feed->getRemoteContentSrc($remoteContent));
    }
}
