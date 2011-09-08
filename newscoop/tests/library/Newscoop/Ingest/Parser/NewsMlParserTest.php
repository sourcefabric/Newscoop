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
    /** @var Newscoop\Ingest\Parser\NewsMlParser */
    private $parser;

    public function setUp()
    {
        $this->parser = new NewsMlParser(APPLICATION_PATH . '/../tests/ingest/test_phd.xml');
    }

    public function testParser()
    {
        $this->assertInstanceOf('Newscoop\Ingest\Parser\NewsMlParser', $this->parser);
    }

    public function testGetTitle()
    {
        $this->assertEquals('title', $this->parser->getTitle());
    }

    public function testGetContent()
    {
        $this->assertStringEqualsFile(APPLICATION_PATH . '/../tests/ingest/test_phd.content.xml', $this->parser->getContent());
    }

    public function testGetCreated()
    {
        $created = new \DateTime('20110825T051533+0200');
        $this->assertEquals($created, $this->parser->getCreated());
    }

    public function testGetUpdated()
    {
        $updated = new \DateTime('20110825T120549+0200');
        $this->assertEquals($updated, $this->parser->getUpdated());
    }

    public function testGetPriority()
    {
        $this->assertEquals(2, $this->parser->getPriority());
    }

    public function testGetService()
    {
        $this->assertEquals('SDA-ATS News Service', $this->parser->getService());
    }

    public function getPublicId()
    {
        $this->assertEquals('urn:newsml:www.sda-ats.ch:20110825:brd004:3N', $this->parser->getPublicId());
    }
}
