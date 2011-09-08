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
    public function setUp()
    {
        parent::setUp('Newscoop\Entity\Ingest\Feed', 'Newscoop\Entity\Ingest\Feed\Entry');
    }

    public function testEntry()
    {
        $entry = new Entry('title', 'content');
        $this->assertInstanceOf('Newscoop\Entity\Ingest\Feed\Entry', $entry);
    }

    public function testTitleContent()
    {
        $entry = new Entry('title', 'content');
        $this->assertEquals('title', $entry->getTitle());
        $this->assertEquals('content', $entry->getContent());
    }

    public function testPublished()
    {
        $entry = new Entry('t', 'c');
        $this->assertFalse($entry->isPublished());

        $now = new \DateTime();
        $entry->setPublished($now);

        $this->assertTrue($entry->isPublished());
        $this->assertEquals($now, $entry->getPublished());
    }
}
