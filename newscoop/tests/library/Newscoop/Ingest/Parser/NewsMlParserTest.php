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
}
