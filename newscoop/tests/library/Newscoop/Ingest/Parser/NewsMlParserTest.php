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
    const NEWSML = '/../tests/ingest/test_phd.xml';
    const TITLE = 'title';
    const CONTENT = '/../tests/ingest/test_phd.content.xml';
    const CREATED = '20110825T051533+0200';
    const UPDATED = '20110825T120549+0200';
    const PRIORITY = 2;
    const SERVICE = 'SDA-ATS News Service';
    const UID = 'urn:newsml:www.sda-ats.ch:20110825:brd004:3N';
    const SUMMARY = 'first';

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
        $this->assertStringEqualsFile(APPLICATION_PATH . self::CONTENT, $this->parser->getContent());
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

    public function testGetService()
    {
        $this->assertEquals(self::SERVICE, $this->parser->getService());
    }

    public function getPublicId()
    {
        $this->assertEquals(self::UID, $this->parser->getPublicId());
    }
}
