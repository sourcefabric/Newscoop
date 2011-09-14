<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Ingest\Parser;

/**
 */
class NewsMlParserTest extends \PHPUnit_Framework_TestCase
{
    const NEWSML = '/../tests/ingest/test.xml';
    const NEWSML_UPDATED = '/../tests/ingest/test_updated.xml';
    const TITLE = 'title';
    const SUBTITLE = '';
    const CONTENT = "<p>next</p>\n<h2>h2</h2>\n<p>last</p>";
    const CREATED = '20110825T051533+0200';
    const UPDATED = '20110825T120549+0200';
    const PRIORITY = 2;
    const SERVICE = 'SDA-ATS News Service';
    const PRODUCT = 'sda-Online D';
    const SUMMARY = 'first';
    const PROVIDER_ID = 'www.sda-ats.ch';
    const DATE_ID = '20110825';
    const NEWS_ITEM_ID = 'brd004';
    const REVISION_ID = 3;
    const INSTRUCTION = 'Update';
    const LOCATION = 'Tripolis';
    const LANGUAGE = 'de';
    const COUNTRY = 'LY';
    const PROVIDER = 'sda';
    const SOURCE = 'sda, dpa, afp, dapd';
    const SUBJECT = '11000000';
    const CATCH_LINE = 'NATO unterstützt Rebellen bei Gaddafi-Jagd';
    const CATCH_WORD = 'Libyen';
    const AUTHORS = 'kr, kad';
    const IMAGE_FILE = '20110825222727235.jpg';
    const IMAGE_CAPTION = 'Sion feiert das 1:0 durch Feindouno';
    const STATUS = 'Embargoed';
    const EMBARGO_LIFT = '20110831T150029';

    /** @var Newscoop\Ingest\Parser\NewsMlParser */
    private $parser;

    public function setUp()
    {
        $this->parser = new NewsMlParser(APPLICATION_PATH . self::NEWSML);
    }

    public function testParser()
    {
        $this->assertInstanceOf('Newscoop\Ingest\Parser\NewsMlParser', $this->parser);
    }

    public function testGetTitle()
    {
        $this->assertEquals(self::TITLE, $this->parser->getTitle());
    }

    public function testGetContent()
    {
        $this->assertEquals(self::CONTENT, $this->parser->getContent());
    }

    public function testGetCreated()
    {
        $created = new \DateTime(self::CREATED);
        $this->assertEquals($created, $this->parser->getCreated());
    }

    public function testGetUpdated()
    {
        $updated = new \DateTime(self::UPDATED);
        $this->assertEquals($updated, $this->parser->getUpdated());
    }

    public function testGetPriority()
    {
        $this->assertEquals(self::PRIORITY, $this->parser->getPriority());
    }

    public function testIsImage()
    {
        $this->assertFalse($this->parser->isImage());
    }

    public function testGetService()
    {
        $this->assertEquals(self::SERVICE, $this->parser->getService());
    }

    public function testGetProviderId()
    {
        $this->assertEquals(self::PROVIDER_ID, $this->parser->getProviderId());
    }

    public function testGetProvider()
    {
        $this->assertEquals(self::PROVIDER, $this->parser->getProvider());
    }

    public function testGetDateId()
    {
        $this->assertEquals(self::DATE_ID, $this->parser->getDateId());
    }

    public function testGetNewsItemId()
    {
        $this->assertEquals(self::NEWS_ITEM_ID, $this->parser->getNewsItemId());
    }

    public function testGetRevisionId()
    {
        $this->assertEquals(self::REVISION_ID, $this->parser->getRevisionId());
    }

    public function testGetInstruction()
    {
        $this->assertEquals(self::INSTRUCTION, $this->parser->getInstruction());
    }

    public function testGetLocation()
    {
        $this->assertEquals(self::LOCATION, $this->parser->getLocation());
    }

    public function testGetProduct()
    {
        $this->assertEquals(self::PRODUCT, $this->parser->getProduct());
    }

    public function testGetLanguage()
    {
        $this->assertEquals(self::LANGUAGE, $this->parser->getLanguage());
    }

    public function testGetSource()
    {
        $this->assertEquals(self::SOURCE, $this->parser->getSource());
    }

    public function testGetCountry()
    {
        $this->assertEquals(self::COUNTRY, $this->parser->getCountry());
    }

    public function testGetSubject()
    {
        $this->assertEquals(self::SUBJECT, $this->parser->getSubject());
    }

    public function testGetCatchLine()
    {
        $this->assertEquals(self::CATCH_LINE, $this->parser->getCatchLine());
    }

    public function testGetCatchWord()
    {
        $this->assertEquals(self::CATCH_WORD, $this->parser->getCatchWord());
    }

    public function testGetSubHeadline()
    {
        $this->assertEquals(self::SUBTITLE, $this->parser->getSubTitle());
    }

    public function testGetAuthors()
    {
        $this->assertEquals(self::AUTHORS, $this->parser->getAuthors());
    }

    public function testGetImages()
    {
        $images = $this->parser->getImages();
        $this->assertEquals(1, count($images));

        $image = array_shift($images);
        $this->assertInstanceOf('Newscoop\Ingest\Parser\NewsMlParser', $image);
        $this->assertTrue($image->isImage());
        $this->assertFileExists($image->getPath());
        $this->assertEquals(self::IMAGE_CAPTION, $image->getTitle());
    }

    public function testGetStatus()
    {
        $this->assertEquals(self::STATUS, $this->parser->getStatus());
    }

    public function testgetLiftEmbargo()
    {
        $this->assertEquals(new \DateTime(self::EMBARGO_LIFT), $this->parser->getLiftEmbargo());
    }
}
