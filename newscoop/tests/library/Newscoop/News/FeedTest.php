<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
class FeedTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\News\Feed */
    protected $feed;

    /** @var Doctrine\Common\Persistance\Objectmanager */
    protected $odm;

    public function setUp()
    {
        global $application;

        $this->odm = $application->getBootstrap()->getResource('odm');
        $this->odm->getConfiguration()->setDefaultDB('phpunit');

        $this->feed = new TestFeed();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Newscoop\News\Feed', $this->feed);
    }

    public function testConfiguration()
    {
        $this->feed->setConfiguration(array('key' => 'value'));
        $this->odm->persist($this->feed);

        $this->odm->flush();
        $this->odm->clear();

        $feed = $this->odm->find('Newscoop\News\TestFeed', $this->feed->getId());
        $this->assertEquals(array('key' => 'value'), $feed->getConfiguration());
    }

    public function testUpdate()
    {
        $this->assertNull($this->feed->getUpdated());
        $this->feed->update();
        $this->assertInstanceOf('DateTime', $this->feed->getUpdated());
    }
}

/**
 * Test feed
 * @Document
 */
class TestFeed extends Feed
{
    public function update()
    {
        $this->updated = new \DateTime();
    }
}
