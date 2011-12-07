<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\News\Item */
    protected $item;

    public function setUp()
    {
        $this->item = new TestItem();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Newscoop\News\Item', $this->item);
    }

    public function testGetFeed()
    {
        require_once __DIR__ . '/FeedTest.php';

        $this->assertNull($this->item->getFeed());

        $feed = new TestFeed();
        $this->item->setFeed($feed);

        $this->assertEquals($feed, $this->item->getFeed());
    }
}

/**
 * Test item
 * @Document
 */
class TestItem extends Item
{
    public function __construct()
    {
    }
}
