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
    /** @var Newscoop\News\ItemService */
    protected $service;

    /** @var Doctrine\Common\Persistance\Objectmanager */
    protected $odm;

    public function setUp()
    {
        $this->odm = $this->setUpOdm();
        $this->service = new ItemService($this->odm);
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
}
