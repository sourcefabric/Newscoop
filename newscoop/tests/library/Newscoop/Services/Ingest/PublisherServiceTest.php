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

    public function testPublish()
    {
        foreach (\Article::GetByName(NewsMlParserTest::TITLE) as $oldArticle) {
            $oldArticle->delete();
        }

        $feed = new Feed('feed_title');
        $entry = Entry::create(new NewsMlParser(APPLICATION_PATH . NewsMlParserTest::NEWSML));
        $feed->addEntry($entry);

        $this->em->persist($feed);
        $this->em->persist($entry);
        $this->em->flush();

        $article = $this->service->publish($entry);

        $this->assertInstanceOf('Article', $article);
        $this->assertEquals($this->config['article_type'], $article->getType());
        $this->assertEquals('Deutsch', $article->getLanguageName());
        $this->assertGreaterThan(0, $article->getPublicationId());
        $this->assertGreaterThan(0, $article->getIssueNumber());
        $this->assertGreaterThan(0, $article->getSectionNumber());
        $this->assertEquals($entry->getCreated()->format('Y-m-d H:i:s'), $article->getCreationDate());
        $this->assertEquals($entry->getUpdated()->format('Y-m-d H:i:s'), $article->getLastModified());
        $this->assertEquals('Y', $article->getWorkflowStatus());
        $this->assertNotEmpty($article->getPublishDate());
        $this->assertTrue($article->isPublic());
        $this->assertEquals($entry->getArticleNumber(), $article->getArticleNumber());
        $this->assertEquals(NewsMlParserTest::TITLE, $article->getTitle());
        $this->assertEquals(NewsMlParserTest::CATCH_WORD, $article->getKeywords());

        $this->checkData($article, $entry);
        $this->checkImages(1, $article);
        $this->checkAuthors(2, $article, $feed);

        $article->delete();
    }

    public function testUpdate()
    {
        $updated = new \DateTime(NewsMlParserTest::UPDATED);
        $created = new \DateTime(NewsMlParserTest::CREATED);

        $feed = new Feed('feed_title');

        $entry = $this->getEntry(array(
            'getTitle' => uniqid(),
            'getContent' => 'hello',
            'getLanguage' => 'de',
            'getSubject' => self::SECTION_SPORT,
            'getCreated' => $created,
        ));
        $this->feed->addEntry($entry);

        $feed->addEntry($entry);

        $this->em->persist($feed);
        $this->em->persist($entry);
        $this->em->flush();

        $orig = $this->service->publish($entry, 'N');
        $entry->update(new NewsMlParser(APPLICATION_PATH . NewsMlParserTest::NEWSML));
        $next = $this->service->update($entry);
        $this->assertTrue($entry->isPublished());

        $this->assertGreaterThan(0, $entry->getArticleNumber());
        $this->assertEquals($entry->getArticleNumber(), $orig->getArticleNumber());
        $this->assertEquals($orig->getArticleNumber(), $next->getArticleNumber());
        $this->assertEquals(NewsMlParserTest::TITLE, $next->getTitle());
        $this->assertEquals(NewsMlParserTest::CATCH_WORD, $next->getKeywords());
        $this->assertEquals($updated->format('Y-m-d H:i:s'), $next->getLastModified());
        $this->assertEquals($created->format('Y-m-d H:i:s'), $orig->getCreationDate());
        $this->assertEquals($created->format('Y-m-d H:i:s'), $next->getCreationDate());

        $this->checkData($next, $entry);
        $this->checkImages(1, $next);
        $this->checkAuthors(2, $next, $feed);

        // test replacing images/authors
        $next = $this->service->update($entry);
        $this->checkImages(1, $next);
        $this->checkAuthors(2, $next, $feed);

        $next->delete();
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

    public function testDelete()
    {
        $feed = new Feed('feed_title');

        $entry = $this->getEntry(array(
            'getTitle' => uniqid(),
            'getContent' => 'hello',
            'getLanguage' => 'en',
            'getSubject' => self::SECTION_SPORT,
        ));
        $this->feed->addEntry($entry);

        $feed->addEntry($entry);

        $this->em->persist($feed);
        $this->em->persist($entry);
        $this->em->flush();

        $article = $this->service->publish($entry, 'N');
        $this->service->delete($entry);

        $article->fetch();
        $this->assertFalse($article->exists());
    }

    public function testPublishSectionSport()
    {
        $feed = new Feed('feed_title');

        $entry = $this->getEntry(array(
            'getTitle' => uniqid(),
            'getContent' => 'hello',
            'getLanguage' => 'de',
            'getSubject' => self::SECTION_SPORT,
        ));
        $this->feed->addEntry($entry);

        $feed->addEntry($entry);

        $this->em->persist($feed);
        $this->em->persist($entry);
        $this->em->flush();

        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_sport'], $article->getSectionNumber());
    }

    public function testPublishSectionCulture()
    {
        $feed = new Feed('feed_title');

        $entry = $this->getEntry(array(
            'getTitle' => uniqid(),
            'getContent' => 'hello',
            'getLanguage' => 'de',
            'getSubject' => self::SECTION_CULTURE,
        ));
        $this->feed->addEntry($entry);

        $feed->addEntry($entry);

        $this->em->persist($feed);
        $this->em->persist($entry);
        $this->em->flush();

        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_culture'], $article->getSectionNumber());
    }

    public function testPublishSectionInternational()
    {
        $feed = new Feed('feed_title');

        $entry = $this->getEntry(array(
            'getTitle' => uniqid(),
            'getContent' => 'hello',
            'getLanguage' => 'de',
            'getSubject' => 1,
            'getCountry' => 'CZ',
        ));
        $this->feed->addEntry($entry);

        $feed->addEntry($entry);

        $this->em->persist($feed);
        $this->em->persist($entry);
        $this->em->flush();

        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_international'], $article->getSectionNumber());
    }

    public function testPublishSectionBasel()
    {
        $feed = new Feed('feed_title');

        $entry = $this->getEntry(array(
            'getTitle' => uniqid(),
            'getContent' => 'hello',
            'getLanguage' => 'de',
            'getSubject' => 1,
            'getCountry' => 'CH',
            'getProduct' => 'Regionaldienst Nord',
        ));
        $this->feed->addEntry($entry);

        $feed->addEntry($entry);

        $this->em->persist($feed);
        $this->em->persist($entry);
        $this->em->flush();

        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_basel'], $article->getSectionNumber());
    }

    public function testPublishSectionOther()
    {
        $feed = new Feed('feed_title');

        $entry = $this->getEntry(array(
            'getTitle' => uniqid(),
            'getContent' => 'hello',
            'getLanguage' => 'de',
            'getSubject' => 1,
            'getCountry' => 'CH',
            'getProduct' => '',
        ));
        $this->feed->addEntry($entry);

        $feed->addEntry($entry);

        $this->em->persist($feed);
        $this->em->persist($entry);
        $this->em->flush();

        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_other'], $article->getSectionNumber());
    }

    public function testPublishProgram()
    {
        $entry = Entry::create(new NewsMlParser(APPLICATION_PATH . '/../tests/ingest/wochenprogramm_rdn201.xml'));

        $this->feed->addEntry($entry);
        $this->em->persist($entry);
        $this->em->flush();

        $article = $this->service->publish($entry);
        $this->assertFalse($article->isPublished());
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
