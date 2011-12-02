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
        $this->odm->getConfiguration()->setDefaultDB('phpunit');
        $this->odm->clear();

        $this->feed = new ReutersFeed($application->getOptions());
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Newscoop\News\ReutersFeed', $this->feed);
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
        $this->odm->persist($this->feed);
        $this->odm->flush();

        $this->assertNotNull($this->feed->getId());
        $this->assertNull($this->feed->getUpdated());

        $this->feed->update($this->odm);
        $this->assertInstanceOf('DateTime', $this->feed->getUpdated());

        $items = $this->odm->getRepository('Newscoop\News\NewsItem')->findBy(array('feed.id' => $this->feed->getId()));
        $this->assertGreaterThan(0, count($items));
    }
}
