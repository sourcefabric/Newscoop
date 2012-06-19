<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services\Ingest;

use Newscoop\Entity\Ingest\Feed\Entry,
    Newscoop\Entity\Ingest\Feed,
    Newscoop\Ingest\Parser\NewsMlParser,
    Newscoop\Ingest\Parser\NewsMlParserTest;

require_once __DIR__ . '/../../Ingest/Parser/NewsMlParserTest.php';

class PublisherServiceTest extends \RepositoryTestCase
{
    const SECTION_SPORT = 15000000;
    const SECTION_CULTURE = 1000000;

    /** @var Newscoop\Services\Ingest\PublisherService */
    protected $service;

    /** @var array */
    protected $config;

    /** @var Newscoop\Entity\Ingest\Feed */
    private $feed;

    public function setUp()
    {
        parent::setUp('Newscoop\Entity\Comment', 'Newscoop\Entity\Ingest\Feed', 'Newscoop\Entity\Ingest\Feed\Entry', 'Newscoop\Entity\Article');
        $this->config = \Zend_Registry::get('container')->getParameter('ingest_publisher');
        $this->service = new PublisherService($this->config);
        
        $this->feed = new Feed('test');
        $this->em->persist($this->feed);
        $this->em->flush();
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\Ingest\PublisherService', $this->service);
    }

    public function testUpdateNotPublished()
    {
        $entry = $this->getEntry(array(
            'getTitle' => 'new',
            'getContent' => 'hello',
            'getLanguage' => 'de',
            'getSubject' => self::SECTION_SPORT,
            'getCreated' => new \DateTime(),
        ));

        $this->service->update($entry);
        $this->assertFalse($entry->isPublished());
    }

    public function testDeleteNotPublished()
    {
        $this->markTestSkipped('Fails on travis');

        $entry = $this->getEntry(array(
            'getTitle' => 'new',
            'getContent' => 'hello',
            'getLanguage' => 'de',
            'getSubject' => self::SECTION_SPORT,
            'getCreated' => new \DateTime(),
        ));

        $this->service->delete($entry);
        $this->assertFalse($entry->isPublished());
    }

    /**
     * Test article data
     */
    private function checkData($article, $entry)
    {
        $data = $article->getArticleData();
        foreach ($this->config['field'] as $field => $getter) {
            if (method_exists($entry, $getter)) {
                $this->assertEquals($entry->$getter(), $data->getFieldValue($field));
            }
        }
    }

    /**
     * Test article images
     */
    private function checkImages($count, \Article $article)
    {
        $images = \ArticleImage::GetImagesByArticleNumber($article->getArticleNumber());
        $this->assertEquals($count, count($images));
    }

    /**
     * Test article authors
     */
    private function checkAuthors($count, \Article $article, Feed $feed)
    {
        $authors = \ArticleAuthor::GetAuthorsByArticle($article->getArticleNumber(), $article->getLanguageId());
        $this->assertEquals(1, count($authors), 'Authors count fails.');
        $this->assertEquals($feed->getTitle(), $authors[0]->getName());
    }

    /**
     * Get parser
     *
     * @param array $expects
     * @return Newscoop\Ingest\Parser
     */
    private function getParser(array $expects)
    {
        $parser = $this->getMockBuilder('Newscoop\Ingest\Parser\NewsMlParser')
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($expects as $method => $value) {
            $parser->expects($this->once())
                ->method($method)
                ->will($this->returnValue($value));
        }

        return $parser;
    }

    /**
     * Get entry
     *
     * @param array $expects
     * @return Newscoop\Entity\Ingest\Feed\Entry
     */
    private function getEntry(array $expects)
    {
        return Entry::create($this->getParser($expects));
    }
}
