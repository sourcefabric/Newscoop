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
    );

    public function setUp()
    {
        $this->service = new PublisherService();
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
}
