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
        $this->feed->setConfig(\Zend_Registry::get('container')->getParameter('ingest'));

        foreach (glob(APPLICATION_PATH . '/../tests/ingest/tmp_*.xml') as $file) {
            unlink($file);
        }
    }

    public function testFeed()
    {
        $this->assertInstanceOf('Newscoop\Entity\Ingest\Feed', $this->feed);
    }

    public function testUpdate()
    {
        $this->assertEquals(0, count($this->feed->getEntries()));

        $this->feed->update();
        $this->assertEquals(2, count($this->feed->getEntries()));

        $this->feed->update();
        $this->assertEquals(2, count($this->feed->getEntries()));
    }

    public function testUpdateWithFreshFile()
    {
        $tmpFile = APPLICATION_PATH . '/../tests/ingest/' . uniqid('tmp_') . '.xml';
        copy(APPLICATION_PATH . '/../tests/ingest/newsml1.xml', $tmpFile);

        $this->feed->update();
        $this->assertEquals(2, count($this->feed->getEntries()));

        $this->feed->update();
        $this->assertEquals(2, count($this->feed->getEntries()));
    }
}
