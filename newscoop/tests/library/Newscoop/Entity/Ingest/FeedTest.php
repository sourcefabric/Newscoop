<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Ingest;

/**
 */
class FeedTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Entity\Ingest\Feed */
    private $feed;

    public function setUp()
    {
        $this->feed = new Feed('sda');
    }

    public function testFeed()
    {
        $this->assertInstanceOf('Newscoop\Entity\Ingest\Feed', $this->feed);
    }

    public function testUpdated()
    {
        $now = new \DateTime();
        $this->assertEquals($this->feed, $this->feed->setUpdated($now));
        $this->assertEquals($now, $this->feed->getUpdated());
    }
}
