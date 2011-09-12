<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Ingest;

use Newscoop\Entity\Ingest\Feed\Entry;

class PublisherServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Services\Ingest\PublisherService */
    protected $service;

    /** @var array */
    protected $config = array(
        'article_type' => 'news',
        'section_sport' => 101,
        'section_culture' => 102,
        'section_basel' => 103,
        'section_international' => 104,
        'section_other' => 105,
    );

    public function setUp()
    {
        $this->service = new PublisherService($this->config);

        $this->parser = $this->getMockBuilder('Newscoop\Ingest\Parser\NewsMlParser')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\Ingest\PublisherService', $this->service);
    }

    public function testPublish()
    {
        $this->parser->expects($this->once())
            ->method('getLanguage')
            ->will($this->returnValue('de'));

        $entry = Entry::create($this->parser);
        $article = $this->service->publish($entry);
        $this->assertInstanceOf('Article', $article);
        $this->assertEquals('Deutsch', $article->getLanguageName());
        $this->assertGreaterThan(0, $article->getPublicationId());
        $this->assertGreaterThan(0, $article->getIssueNumber());
        $this->assertGreaterThan(0, $article->getSectionNumber());
    }

    public function testPublishSectionSport()
    {
        $this->parser->expects($this->once())
            ->method('getLanguage')
            ->will($this->returnValue('de'));

        $this->parser->expects($this->once())
            ->method('getSubject')
            ->will($this->returnValue(15000000));

        $entry = Entry::create($this->parser);
        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_sport'], $article->getSectionNumber());
    }

    public function testPublishSectionCulture()
    {
        $this->parser->expects($this->once())
            ->method('getLanguage')
            ->will($this->returnValue('de'));

        $this->parser->expects($this->once())
            ->method('getSubject')
            ->will($this->returnValue(1000000));

        $entry = Entry::create($this->parser);
        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_culture'], $article->getSectionNumber());
    }

    public function testPublishSectionInternational()
    {
        $this->parser->expects($this->once())
            ->method('getLanguage')
            ->will($this->returnValue('de'));

        $this->parser->expects($this->once())
            ->method('getSubject')
            ->will($this->returnValue(1));

        $this->parser->expects($this->once())
            ->method('getCountry')
            ->will($this->returnValue('CZ'));

        $entry = Entry::create($this->parser);
        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_international'], $article->getSectionNumber());
    }

    public function testPublishSectionBasel()
    {
        $this->parser->expects($this->once())
            ->method('getLanguage')
            ->will($this->returnValue('de'));

        $this->parser->expects($this->once())
            ->method('getSubject')
            ->will($this->returnValue(1));

        $this->parser->expects($this->once())
            ->method('getCountry')
            ->will($this->returnValue('CH'));

        $this->parser->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue('Regionaldienst Nord'));

        $entry = Entry::create($this->parser);
        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_basel'], $article->getSectionNumber());
    }

    public function testPublishSectionOther()
    {
        $this->parser->expects($this->once())
            ->method('getLanguage')
            ->will($this->returnValue('de'));

        $this->parser->expects($this->once())
            ->method('getSubject')
            ->will($this->returnValue(1));

        $this->parser->expects($this->once())
            ->method('getCountry')
            ->will($this->returnValue('CH'));

        $this->parser->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue('x'));

        $entry = Entry::create($this->parser);
        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_other'], $article->getSectionNumber());
    }

}
