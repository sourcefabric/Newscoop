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

        $this->client = new \Zend_Rest_Client();
        $this->feed = new ReutersFeed($application->getOptions(), $this->client);
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

        return $channels;
    }

    /**
     * @depends testGetChannels
     */
    public function testGetChannelItems(array $channels)
    {
        $items = $this->feed->getChannelItems($channels[0]);
        $this->assertNotEmpty($items);

        $this->assertObjectHasAttribute('id', $items[0]);
        $this->assertObjectHasAttribute('guid', $items[0]);
        $this->assertObjectHasAttribute('version', $items[0]);
        $this->assertObjectHasAttribute('dateCreated', $items[0]);
        $this->assertObjectHasAttribute('slug', $items[0]);
        $this->assertObjectHasAttribute('author', $items[0]);
        $this->assertObjectHasAttribute('source', $items[0]);
        $this->assertObjectHasAttribute('language', $items[0]);
        $this->assertObjectHasAttribute('headline', $items[0]);
        $this->assertObjectHasAttribute('mediaType', $items[0]);
        $this->assertObjectHasAttribute('priority', $items[0]);
        $this->assertObjectHasAttribute('geography', $items[0]);
        $this->assertObjectHasAttribute('previewUrl', $items[0]);
        $this->assertObjectHasAttribute('size', $items[0]);
        $this->assertObjectHasAttribute('dimensions', $items[0]);
        $this->assertObjectHasAttribute('channel', $items[0]);

        return $items;
    }

    /**
     * @depends testGetChannelItems
     */
    public function testGetItem(array $items)
    {
        $item = $this->feed->getItem($items[0]->guid);
        $this->assertInstanceOf('Newscoop\News\NewsItem', $item);
        $this->assertEquals($items[0]->guid, $item->getId());
        $this->assertEquals($this->feed, $item->getFeed());
    }
}
