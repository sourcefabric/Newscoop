<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Package;

use Newscoop\Image\LocalImage;

/**
 */
class PackageServiceTest extends \TestCase
{
    const ARTICLE_NUMBER = 123;

    /** @var Newscoop\Package\PackageService */
    protected $service;

    /** @var Doctrine\ORM\EntityManager */
    protected $orm;

    public function setUp()
    {
        $this->orm = $this->setUpOrm('Newscoop\Package\Package', 'Newscoop\Package\Item', 'Newscoop\Image\LocalImage');
        $this->service = new PackageService($this->orm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Package\PackageService', $this->service);
    }

    public function testSave()
    {
        $this->assertEquals(0, count($this->service->findByArticle(self::ARTICLE_NUMBER)));

        $package = $this->service->save(array(
            'article' => self::ARTICLE_NUMBER,
        ));

        $this->assertInstanceOf('Newscoop\Package\Package', $package);
        $this->assertNotNull($package->getId());
        $this->assertEquals(self::ARTICLE_NUMBER, $package->getArticleNumber());
        $this->assertContains('1', (string) $package);

        $this->assertEquals(1, count($this->service->findByArticle(self::ARTICLE_NUMBER)));
    }

    public function testFind()
    {
        $this->assertNull($this->service->find(1));
        $this->service->save(array());
        $this->assertNotNull($this->service->find(1));
    }

    public function testAddItem()
    {
        $this->orm->persist($image = new LocalImage('test'));
        $this->orm->persist($image2 = new LocalImage('next'));
        $this->orm->flush();


        $slideshow = $this->service->save(array());
        $this->assertEquals(0, count($slideshow->getItems()));

        $this->service->addItem($slideshow, $image);
        $this->assertEquals(1, count($slideshow->getItems()));

        $this->assertEquals($image, $slideshow->getItems()->first()->getImage());
        $this->assertEquals(1, $slideshow->getItems()->first()->getId());
        $this->assertEquals(0, $slideshow->getItems()->first()->getOffset());

        $this->service->addItem($slideshow, $image2);
        $this->assertEquals(2, count($slideshow->getItems()));
    }

    public function testSetOrder()
    {
        $package = $this->service->save(array());
        $this->service->addItem($package, new LocalImage('tic'));
        $this->service->addItem($package, new LocalImage('toc'));
        $this->service->addItem($package, new LocalImage('tac'));

        $this->service->setOrder($package, array('item-3', 'item-1', 'item-2'));

        $this->assertContains('tac', $package->getItems()->first()->getImage()->getPath());
        $this->assertContains('tic', $package->getItems()->get(1)->getImage()->getPath());
        $this->assertContains('toc', $package->getItems()->get(2)->getImage()->getPath());
    }

    public function testRemoveItem()
    {
        $package = $this->service->save(array());
        $this->service->addItem($package, new LocalImage('tic'));
        $this->service->addItem($package, new LocalImage('tac'));
        $this->service->addItem($package, new LocalImage('toc'));

        $this->service->removeItem($package, 1);

        $this->assertEquals(2, count($package->getItems()));
        $this->assertEquals(0, $package->getItems()->get(0)->getOffset());
        $this->assertEquals(1, $package->getItems()->get(1)->getOffset());
    }
}
