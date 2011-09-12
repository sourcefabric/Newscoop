<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Ingest;

/**
 */
class FeedTest extends \RepositoryTestCase
{
    public function setUp()
    {
        parent::setUp('Newscoop\Entity\Ingest\Feed');
    }

    public function testFeed()
    {
        $feed = new Feed('title');
        $this->assertInstanceOf('Newscoop\Entity\Ingest\Feed', $feed);
    }

    public function testUpdateEmpty()
    {
        $feed = new Feed('sda');
        $feed->update();
        $this->assertEquals(0, count($feed->getEntries()));
    }
}
