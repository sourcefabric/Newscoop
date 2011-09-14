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
    const SECTION_SPORT = 15000000;
    const SECTION_CULTURE = 1000000;

    /** @var Newscoop\Services\Ingest\PublisherService */
    protected $service;

    /** @var array */
    protected $config;

    public function setUp()
    {
        $this->config = \Zend_Registry::get('container')->getParameter('ingest_publisher');
        $this->service = new PublisherService($this->config);
    }

    public function testService()
    {
        $this->assertInstanceOf('Newscoop\Services\Ingest\PublisherService', $this->service);
    }

    public function testPublish()
    {
        $created = new \DateTime('-2 day');
        $published = new \DateTime();


        $entry = $this->getEntry(array(
            'getLanguage' => 'en',
            'getCreated' => $created,
            'getTitle' => 'title',
            'getCatchWord' => 'catch',
            'getSummary' => 'summary',
            'getContent' => 'content',
        ));

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
        $this->assertEquals($entry->getArticleNumber(), $article->getArticleNumber());
        $this->assertEquals('title', $article->getTitle());
        $this->assertEquals('catch', $article->getKeywords());

        /**
        $data = $article->getArticleData();
        $this->assertEquals('content', $data->getFieldValue($this->config['field']['content']));
        $this->assertEquals('summary', $data->getFieldValue($this->config['field']['summary']));
         */
    }

    public function testUpdate()
    {
        $entry = $this->getEntry(array(
            'getLanguage' => 'en',
            'getSubject' => self::SECTION_SPORT,
        ));

        $orig = $this->service->publish($entry);

        $updatedTime = new \DateTime('-2d');
        $entry->update($this->getParser(array(
            'getLanguage' => 'en',
            'getSubject' => self::SECTION_SPORT,
            'getTitle' => 'newtitle',
            'getUpdated' => $updatedTime,
        )));

        $updated = $this->service->update($entry);

        $this->assertEquals($orig->getArticleNumber(), $updated->getArticleNumber());
        $this->assertEquals($entry->getTitle(), $updated->getTitle());
        $this->assertEquals($updatedTime->format('Y-m-d H:i:s'), $updated->getLastModified());
    }

    public function testDelete()
    {
        $entry = $this->getEntry(array(
            'getLanguage' => 'en',
            'getSubject' => self::SECTION_SPORT,
        ));

        $article = $this->service->publish($entry);
        $this->service->delete($entry);

        $article->fetch();
        $this->assertFalse($article->exists());
    }

    public function testPublishSectionSport()
    {
        $entry = $this->getEntry(array(
            'getLanguage' => 'de',
            'getSubject' => self::SECTION_SPORT,
        ));

        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_sport'], $article->getSectionNumber());
    }

    public function testPublishSectionCulture()
    {
        $entry = $this->getEntry(array(
            'getLanguage' => 'de',
            'getSubject' => self::SECTION_CULTURE,
        ));

        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_culture'], $article->getSectionNumber());
    }

    public function testPublishSectionInternational()
    {
        $entry = $this->getEntry(array(
            'getLanguage' => 'de',
            'getSubject' => 1,
            'getCountry' => 'CZ',
        ));

        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_international'], $article->getSectionNumber());
    }

    public function testPublishSectionBasel()
    {
        $entry = $this->getEntry(array(
            'getLanguage' => 'de',
            'getSubject' => 1,
            'getCountry' => 'CH',
            'getProduct' => 'Regionaldienst Nord',
        ));

        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_basel'], $article->getSectionNumber());
    }

    public function testPublishSectionOther()
    {
        $entry = $this->getEntry(array(
            'getLanguage' => 'de',
            'getSubject' => 1,
            'getCountry' => 'CH',
            'getProduct' => '',
        ));

        $article = $this->service->publish($entry);
        $this->assertEquals($this->config['section_other'], $article->getSectionNumber());
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
