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
    protected $config;

    public function setUp()
    {
        $this->config = \Zend_Registry::get('container')->getParameter('ingest_publisher');
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
        $created = new \DateTime('-2 day');
        $published = new \DateTime();

        $this->parser->expects($this->once())
            ->method('getLanguage')
            ->will($this->returnValue('en'));

        $this->parser->expects($this->once())
            ->method('getCreated')
            ->will($this->returnValue($created));

        $entry = Entry::create($this->parser);
        $article = $this->service->publish($entry);
        $this->assertInstanceOf('Article', $article);
        $this->assertEquals($this->config['article_type'], $article->getType());
        $this->assertEquals('English', $article->getLanguageName());
        $this->assertGreaterThan(0, $article->getPublicationId());
        $this->assertGreaterThan(0, $article->getIssueNumber());
        $this->assertGreaterThan(0, $article->getSectionNumber());
        $this->assertEquals($created->format('Y-m-d H:i:s'), $article->getCreationDate());
        $this->assertEquals($entry->getUpdated()->format('Y-m-d H:i:s'), $article->getLastModified());
        $this->assertEquals('Y', $article->getWorkflowStatus());
        $this->assertNotEmpty($article->getPublishDate());
        $this->assertTrue($article->isPublic());
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
