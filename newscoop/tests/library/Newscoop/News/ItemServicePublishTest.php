<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 */
class ItemServicePublishTest extends \TestCase
{
    const TEXT_XML = 'textNewsItem.xml';
    const PICTURE_XML = 'pictureNewsItem.xml';

    /** @var Newscoop\News\ItemService */
    protected $service;

    /** @var Doctrine\ODM\MongoDB\DocumentManager */
    protected $odm;

    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    public function setUp()
    {
        global $g_ado_db;

        $this->odm = $this->setUpOdm();
        $settingsService = new SettingsService($this->odm);
        $this->service = new ItemService($this->odm, $settingsService);

        $this->orm = $this->setUpOrm(
            'Newscoop\Entity\Article',
            'Newscoop\Entity\Comment',
            'Newscoop\Entity\ArticleDatetime',
            'Newscoop\Entity\Picture',
            'Newscoop\Entity\AutoId',
            'Newscoop\Entity\ArticleTypeField',
            'Newscoop\Entity\Publication',
            'Newscoop\Entity\Language',
            'Newscoop\Entity\Log'
        );
        $g_ado_db = new \AdoDbDoctrineAdapter($this->orm);
    }

    public function tearDown()
    {
        $this->tearDownOdm($this->odm);

        if ($this->orm !== null) {
            $this->tearDownOrm($this->orm);
            $this->orm = null;
        }
    }

    public function testPublishText()
    {
        $xml = simplexml_load_file(APPLICATION_PATH . '/../tests/fixtures/' . self::TEXT_XML);
        $item = NewsItem::createFromXml($xml->itemSet->newsItem);

        $articles = \Article::GetByName($item->getContentMeta()->getHeadline(), null, null, null, null, true);
        foreach ($articles as $article) {
            $article->delete();
        }

        $this->assertFalse($item->isPublished());

        $this->service->publish($item);

        $this->assertTrue($item->isPublished());
        $this->assertInstanceOf('DateTime', $item->getPublished());

        $articles = \Article::GetByName($item->getContentMeta()->getHeadline(), null, null, null, null, true);
        $this->assertEquals(1, count($articles));

        $article = $articles[0];
        $this->assertEquals($item->getContentMeta()->getHeadline(), $article->getTitle());
        $this->assertEquals('newsml', $article->getType());
        $this->assertEquals($item->getItemMeta()->getFirstCreated()->format('Y-m-d H:i:s'), $article->getCreationDate());

        $articleData = $article->getArticleData();
        $this->assertEquals($item->getId(), $articleData->getFieldValue('guid', true));
        $this->assertEquals($item->getVersion(), $articleData->getFieldValue('version'));
        $this->assertEquals($item->getContentMeta()->getUrgency(), $articleData->getFieldValue('urgency'));
        $this->assertEquals($item->getRightsInfo()->first()->getCopyrightNotice(), $articleData->getFieldValue('copyright'));
        $this->assertEquals($item->getItemMeta()->getProvider(), $articleData->getFieldValue('provider'));
        $this->assertEquals($item->getContentMeta()->getDescription(), $articleData->getFieldValue('description'));
        $this->assertEquals($item->getContentMeta()->getDateline(), $articleData->getFieldValue('dateline'));
        $this->assertEquals($item->getContentMeta()->getByline(), $articleData->getFieldValue('byline'));
        $this->assertEquals($item->getContentMeta()->getCreditline(), $articleData->getFieldValue('creditline'));
        $this->assertEquals((string) $item->getContentSet()->getInlineContent(), $articleData->getFieldValue('inlinecontent'));
    }

    public function testPublishPicture()
    {
        $xml = simplexml_load_file(APPLICATION_PATH . '/../tests/fixtures/' . self::PICTURE_XML);
        $item = NewsItem::createFromXml($xml->itemSet->newsItem);

        $feed = $this->getMockBuilder('Newscoop\News\Feed')
            ->disableOriginalConstructor()
            ->getMock();

        $feed->expects($this->once())
            ->method('getRemoteContentSrc')
            ->with($this->equalTo($item->getContentSet()->getRemoteContent('rend:baseImage')))
            ->will($this->returnValue(APPLICATION_PATH . '/../tests/fixtures/picture.jpg'));

        $item->setFeed($feed);

        $this->service->publish($item);
        $this->assertTrue($item->isPublished());
        $this->assertInstanceOf('DateTime', $item->getPublished());

        $pictures = $this->orm->getRepository('Newscoop\Entity\Picture')->findAll();
        $this->assertEquals(1, count($pictures));

        $picture = $pictures[0];
        $this->assertEquals('Alex Blackburn-Smith arrives at Croydon Magistrates Court in southeast London', $picture->getHeadline());
        $this->assertEquals('Alex Blackburn-Smith arrives at Croydon Magistrates Court in southeast London December 6, 2011. Blackburn-Smith pleaded guilty to failing to ensure a dog�s welfare, as well as being in possession of a banned pitbull terrier.   example/Luke MacGregor  (BRITAIN - Tags: CRIME LAW)', $picture->getCaption());
        $this->assertEquals('image/jpeg', $picture->getContentType());
        $this->assertEquals('LUKE MACGREGOR', $picture->getPhotographer());
        $this->assertEquals('newsfeed', $picture->getSource());
        $this->assertTrue($picture->isApproved());
        $this->assertEquals(date_create('2011-12-06T13:32:23.000Z')->format('Y-m-d H:i'), $picture->getDate()->format('Y-m-d H:i'));
        $this->assertEquals('LONDON', $picture->getPlace());
    }
}
