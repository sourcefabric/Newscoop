<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Ingest\Feed;

/**
 */
class EntryTest extends \RepositoryTestCase
{
    /** @var Newscoop\Entity\Ingest\Feed\Entry */
    private $entry;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\Ingest\Feed', 'Newscoop\Entity\Ingest\Feed\Entry');
        $this->entry = new Entry('title', 'content');
    }

    public function testEntry()
    {
        $this->assertInstanceOf('Newscoop\Entity\Ingest\Feed\Entry', $this->entry);
    }

    public function testTitleContent()
    {
        $this->assertEquals('title', $this->entry->getTitle());
        $this->assertEquals('content', $this->entry->getContent());
    }

    public function testPublished()
    {
        $this->assertFalse($this->entry->isPublished());

        $now = new \DateTime();
        $this->assertEquals($this->entry, $this->entry->setPublished($now));

        $this->assertTrue($this->entry->isPublished());
        $this->assertEquals($now, $this->entry->getPublished());
    }

    public function testCreated()
    {
        $this->assertNotNull($this->entry->getCreated());
        $now = new \DateTime();
        $this->assertEquals($this->entry, $this->entry->setCreated($now));
        $this->assertEquals($now, $this->entry->getCreated());
    }

    public function testPriority()
    {
        $this->assertNull($this->entry->getPriority());
        $this->assertEquals($this->entry, $this->entry->setPriority(3));
        $this->assertEquals(3, $this->entry->getPriority());
    }

    public function testService()
    {
        $this->assertNull($this->entry->getService());
        $this->assertEquals($this->entry, $this->entry->setService('service'));
        $this->assertEquals('service', $this->entry->getService());
    }

    public function testCreate()
    {
        $parser = $this->getMock('Newscoop\Ingest\Parser\NewsMlParser', array(), array(APPLICATION_PATH . '/../tests/ingest/test_phd.xml'));

        $now = new \DateTime();
        $parser->expects($this->once())
            ->method('getCreated')
            ->will($this->returnValue($now));

        $parser->expects($this->once())
            ->method('getUpdated')
            ->will($this->returnValue($now));

        $entry = Entry::create($parser);
        $this->assertInstanceOf('Newscoop\Entity\Ingest\Feed\Entry', $entry);
    }
}
