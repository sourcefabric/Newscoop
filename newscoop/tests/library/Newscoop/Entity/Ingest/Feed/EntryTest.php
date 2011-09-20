<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Ingest\Feed;

use Newscoop\Ingest\Parser\NewsMlParser,
    Newscoop\Ingest\Parser\NewsMlParserTest;

require_once APPLICATION_PATH . '/../tests/library/Newscoop/Ingest/Parser/NewsMlParserTest.php';

/**
 */
class EntryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Entity\Ingest\Feed\Entry */
    private $entry;

    public function setUp()
    {
        $this->entry = new Entry('title', 'content');
    }

    public function testEntry()
    {
        $this->assertInstanceOf('Newscoop\Entity\Ingest\Feed\Entry', $this->entry);
    }

    public function testPublished()
    {
        $now = new \DateTime();

        $this->assertFalse($this->entry->isPublished());
        $this->assertEquals($this->entry, $this->entry->setPublished($now));

        $this->assertTrue($this->entry->isPublished());
        $this->assertEquals($now, $this->entry->getPublished());
    }

    public function testCreate()
    {
        $parser = new NewsMlParser(APPLICATION_PATH . NewsMlParserTest::NEWSML);
        $entry = Entry::create($parser);

        $this->assertInstanceOf('Newscoop\Entity\Ingest\Feed\Entry', $entry);
        $this->assertEquals(NewsMlParserTest::TITLE, $entry->getTitle());
        $this->assertEquals(NewsMlParserTest::SUBTITLE, $entry->getSubtitle());
        $this->assertEquals(NewsMlParserTest::CONTENT, $entry->getContent());
        $this->assertEquals(new \DateTime(NewsMlParserTest::CREATED), $entry->getCreated());
        $this->assertEquals(new \DateTime(NewsMlParserTest::UPDATED), $entry->getUpdated());
        $this->assertEquals(NewsMlParserTest::PRIORITY, $entry->getPriority());
        $this->assertEquals(NewsMlParserTest::SERVICE, $entry->getService());
        $this->assertEquals(NewsMlParserTest::PRODUCT, $entry->getProduct());
        $this->assertEquals(NewsMlParserTest::SUMMARY, $entry->getSummary());
        $this->assertEquals(NewsMlParserTest::PROVIDER_ID, $entry->getProviderId());
        $this->assertEquals(NewsMlParserTest::DATE_ID, $entry->getDateId());
        $this->assertEquals(NewsMlParserTest::NEWS_ITEM_ID, $entry->getNewsItemId());
        $this->assertEquals(NewsMlParserTest::REVISION_ID, $entry->getRevisionId());
        $this->assertEquals(NewsMlParserTest::LOCATION, $entry->getLocation());
        $this->assertEquals(NewsMlParserTest::LANGUAGE, $entry->getLanguage());
        $this->assertEquals(NewsMlParserTest::COUNTRY, $entry->getCountry());
        $this->assertEquals(NewsMlParserTest::PROVIDER, $entry->getProvider());
        $this->assertEquals(NewsMlParserTest::SOURCE, $entry->getSource());
        $this->assertEquals(NewsMlParserTest::SUBJECT, $entry->getSubject());
        $this->assertEquals(NewsMlParserTest::CATCH_LINE, $entry->getCatchLine());
        $this->assertEquals(NewsMlParserTest::CATCH_WORD, $entry->getCatchWord());
        $this->assertEquals(NewsMlParserTest::AUTHORS, $entry->getAuthors());
        $this->assertEquals(NewsMlParserTest::STATUS, $entry->getStatus());
        $this->assertEquals(new \DateTime(NewsMlParserTest::EMBARGO_LIFT), $entry->getEmbargoed());
        $this->assertEquals(array(NewsMlParserTest::IMAGE_FILE => NewsMlParserTest::IMAGE_CAPTION), $entry->getImages());
    }

    public function testUpdate()
    {
        $entry = Entry::create(new NewsMlParser(APPLICATION_PATH . NewsMlParserTest::NEWSML));
        $updated = new NewsMlParser(APPLICATION_PATH . NewsMlParserTest::NEWSML_UPDATED);

        $entry->update($updated);

        $this->assertEquals($entry->getTitle(), $updated->getTitle());
        $this->assertEquals($entry->getCreated(), $updated->getCreated());
        $this->assertEquals($entry->getUpdated(), $updated->getUpdated());
        $this->assertEquals($entry->getContent(), $updated->getContent());
        $this->assertEquals($entry->getPriority(), $updated->getPriority());
        $this->assertEquals($entry->getSummary(), $updated->getSummary());
        $this->assertEquals($entry->getStatus(), $updated->getStatus());
        $this->assertEquals($entry->getAuthors(), $updated->getAuthors());
    }
}
