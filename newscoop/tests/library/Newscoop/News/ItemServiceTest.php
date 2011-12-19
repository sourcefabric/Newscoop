<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

require_once __DIR__ . '/TestCase.php';

/**
 */
class ItemServiceTest extends TestCase
{
    const TEXT_XML = 'textNewsItem.xml';

    /** @var Newscoop\News\ItemService */
    protected $service;

    /** @var Doctrine\ODM\MongoDB\DocumentManager */
    protected $odm;

    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    public function setUp()
    {
        $this->odm = $this->setUpOdm();
        $this->service = new ItemService($this->odm);
    }

    public function tearDown()
    {
        $this->tearDownOdm($this->odm);

        if ($this->orm !== null) {
            $this->tearDownOrm($this->orm);
            $this->orm = null;
        }
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\News\ItemService', $this->service);
    }

    public function testFind()
    {
        $this->assertNull($this->service->find('item'));

        $this->service->save(new NewsItem('item'));
        $this->odm->clear();

        $this->assertNotNull($this->service->find('item'));

    }

    public function testFindPackageItem()
    {
        $this->service->save(new PackageItem('package'));
        $this->odm->clear();
        $this->assertNotNull($this->service->find('package'));
    }

    public function testFindBy()
    {
        $this->assertEquals(0, count($this->service->findBy(array())));

        $item = new NewsItem('item:1');
        $this->service->save($item);

        $item = new PackageItem('item:2');
        $this->service->save($item);

        $this->odm->clear();

        $items = $this->service->findBy(array(), array('id' => 'asc'));
        $this->assertEquals(2, count($items));
        $this->assertInstanceOf('Newscoop\News\NewsItem', $items->getNext());
        $this->assertInstanceOf('Newscoop\News\PackageItem', $items->getNext());
    }

    public function testSave()
    {
        $item = new NewsItem('tag:id');
        $this->service->save($item);
        $this->assertEquals($item, $this->service->find('tag:id'));
    }

    public function testSaveExisting()
    {
        $previous = new NewsItem('tag:id');
        $item = new NewsItem('tag:id', 2);
        $this->service->save($item);
        $this->assertEquals($item, $this->service->find('tag:id'));
    }

    public function testSavePackageItem()
    {
        $item = new PackageItem('tag:id');
        $this->service->save($item);
        $this->assertEquals($item, $this->service->find('tag:id'));
    }

    public function testSaveCanceledItem()
    {
        $meta = new ItemMeta();
        $meta->setPubStatus(ItemMeta::STATUS_CANCELED);

        $item = new NewsItem('tag:id');
        $item->setItemMeta($meta);
        $this->service->save($item);

        $this->assertEquals(0, count($this->service->findBy(array())));
    }

    public function testPublish()
    {
        $this->orm = $this->setUpOrm('Newscoop\Entity\Article', 'Newscoop\Entity\Comment', 'Newscoop\Entity\ArticleDatetime');

        $xml = simplexml_load_file(APPLICATION_PATH . '/../tests/fixtures/' . self::TEXT_XML);
        $item = NewsItem::createFromXml($xml->itemSet->newsItem);

        $articles = \Article::GetByName($item->getContentMeta()->getHeadline(), null, null, null, null, true);
        foreach ($articles as $article) {
            $article->delete();
        }

        $this->assertFalse($item->isPublished());

        $article = $this->service->publish($item);

        $this->assertTrue($item->isPublished());
        $this->assertInstanceOf('DateTime', $item->getPublished());

        $articles = \Article::GetByName($item->getContentMeta()->getHeadline(), null, null, null, null, true);
        $this->assertEquals(1, count($articles));

        $article = $articles[0];
        $this->assertEquals($item->getContentMeta()->getHeadline(), $article->getTitle());
        $this->assertEquals('newsml', $article->getType());
        $this->assertEquals($item->getItemMeta()->getFirstCreated()->format('Y-m-d H:i:s'), $article->getCreationDate());

        $articleData = $article->getArticleData();
        $this->assertEquals($item->getId(), $articleData->getFieldValue('guid'));
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
}
